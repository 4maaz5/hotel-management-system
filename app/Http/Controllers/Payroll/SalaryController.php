<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Company;
use App\Models\Employee;
use App\Models\GeneralSetting;
use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalaryController extends Controller
{
    use ScopesTenantAccess;

    // public function index()
    // {
    //     $user = auth()->user();

    //     if ($user->hasRole('super_admin')) {
    //         $salaryHistory = Payroll::with('employee')
    //             ->where('status', 'Paid')
    //             ->latest()
    //             ->get();
    //         $salaryHistoryCards = Payroll::with('employee')
    //             ->where('status', 'Paid')
    //             ->latest()
    //             ->paginate(10);
    //     } elseif ($user->hasRole('manager')) {
    //         $salaryHistory = Payroll::with('employee')
    //             ->where('status', 'Paid')
    //             ->whereHas('employee', function ($query) use ($user) {
    //                 $query->where('branch_id', $user->branch_id);
    //             })
    //             ->latest()
    //             ->get();
    //         $salaryHistoryCards = Payroll::with('employee')
    //             ->where('status', 'Paid')
    //             ->whereHas('employee', function ($query) use ($user) {
    //                 $query->where('branch_id', $user->branch_id);
    //             })
    //             ->latest()
    //             ->paginate(10);
    //     } else { // employee
    //         $salaryHistory = Payroll::with('employee')
    //             ->where('status', 'Paid')
    //             ->where('employee_id', $user->employee->id)
    //             ->latest()
    //             ->get();
    //     }

    //     return view('Admin.Backend.SalaryHistory.index', compact('salaryHistory', 'salaryHistoryCards'));
    // }

    public function index()
    {
        $user = auth()->user();

        // Initialize variables
        $salaryHistory = collect();
        $salaryHistoryCards = collect();

        if ($user->hasRole('super_admin')) {
            // Super Admin → all paid payrolls
            $salaryHistory = Payroll::with('employee')
                ->where('status', 'Paid')
                ->latest()
                ->paginate(10);

            $salaryHistoryCards = Payroll::with('employee')
                ->where('status', 'Paid')
                ->latest()
                ->paginate(10);

        } elseif ($user->branch_id) {
            // User has a branch → scope by that branch
            $branchId = $user->branch_id;

            $salaryHistory = Payroll::with('employee')
                ->where('status', 'Paid')
                ->whereHas('employee', function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->latest()
                ->paginate(10);

            $salaryHistoryCards = Payroll::with('employee')
                ->where('status', 'Paid')
                ->whereHas('employee', function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->latest()
                ->paginate(10);
        } else {
            // Owner (no branch_id): TenantScope on Employee handles company scoping
            $salaryHistory = Payroll::with('employee')
                ->where('status', 'Paid')
                ->whereHas('employee', function ($query) {
                    // TenantScope auto-scopes Employee by company_id
                })
                ->latest()
                ->paginate(10);

            $salaryHistoryCards = Payroll::with('employee')
                ->where('status', 'Paid')
                ->whereHas('employee', function ($query) {
                    // TenantScope auto-scopes Employee by company_id
                })
                ->latest()
                ->paginate(10);
        }

        return view('Admin.Backend.SalaryHistory.index', compact('salaryHistory', 'salaryHistoryCards'));
    }

    public function downloadSlip($id)
    {
        $payslip = $this->scopePayrollsForUser(Payroll::with('employee'), auth()->user())->findOrFail($id);
        $setting = GeneralSetting::first();

        // Load the Blade view and pass data
        $pdf = Pdf::loadView('Admin.Backend.SalaryHistory.pdf', compact('payslip', 'setting'));

        // To preview before download
        return $pdf->stream('payslip_preview.pdf');

    }

    public function filterPayrolls(Request $request)
    {
        $user = Auth::user();

        // Read inputs
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $month = $request->input('month');
        $year = $request->input('year');
        $status = $request->input('status');
        $employeeId = $request->input('employee_id');
        $branchId = $request->input('branch_id');
        $perPage = $request->input('per_page', 15);

        // Base query
        $query = $this->scopePayrollsForUser(Payroll::with(['employee', 'employee.branch']), $user);

        // ROLE: MANAGER → only own branch
        if ($user->hasRole('manager') && $user->branch_id) {
            $query->whereHas('employee', fn ($q) => $q->where('branch_id', $user->branch_id)
            );
        }
        // ROLE: EMPLOYEE → only their own payroll
        elseif ($user->hasRole('employee')) {
            $employee = Employee::where('user_id', $user->id)->first();
            if (! $employee) {
                return redirect()->back()->with('error', __('No employee record found for your account.'));
            }
            $query->where('employee_id', $employee->id);
        }

        // Filter by Employee
        if ($employeeId) {
            $this->scopeEmployeesForUser(Employee::query(), $user)->findOrFail($employeeId);
            $query->where('employee_id', $employeeId);
        }

        // Filter by Branch (only super admin)
        if ($branchId && $user->hasRole('super_admin')) {
            $query->whereHas('employee', fn ($q) => $q->where('branch_id', $branchId)
            );
        }

        // Status filter → default = Paid
        if ($status) {
            $query->where('status', $status);
        } else {
            // Default show only PAID payrolls
            $query->where('status', 'Paid');
        }

        // Date Range
        if ($startDate && $endDate) {
            $from = Carbon::parse($startDate)->startOfDay();
            $to = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        } else {
            // Month filter
            if ($month) {
                $m = str_contains($month, '-')
                    ? intval(explode('-', $month)[1])
                    : intval($month);

                $query->whereMonth('created_at', $m);
            }

            // Year filter
            if ($year) {
                $query->whereYear('created_at', intval($year));
            }
        }

        // Fetch payrolls
        $payrolls = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->query());

        // Summary
        $summary = [
            'total_net_pay' => $payrolls->sum('net_pay'),
            'pending_net_pay' => $payrolls->where('status', 'Pending')->sum('net_pay'),
            'count' => $payrolls->total(),
        ];

        // AJAX response (partial update only)
        if ($request->ajax()) {
            $html = view('Admin.Backend.partials.salary', compact('payrolls'))->render();
            $pagination = $payrolls->links('pagination::bootstrap-5')->render();

            return response()->json(['html' => $html, 'pagination' => $pagination]);
        }

        // Full page render
        $employees = $this->scopeEmployeesForUser(Employee::select('id', 'first_name', 'last_name'), $user)->get();
        $branches = $this->scopeBranchesForUser(Branch::select('id', 'name'), $user)->get();

        return view('Admin.Backend.partials.salary', compact(
            'payrolls', 'employees', 'branches', 'summary'
        ));
    }

    public function salaryView()
    {
        $user = auth()->user();

        // Base queries
        $companies = Company::query();
        $brands = Brand::query();
        $branches = Branch::query();

        // Role-based filtering
        if ($this->isSuperAdmin($user)) {
            // Super admin sees all salary dimensions.
        } elseif ($user->branch_id) {
            // Manager sees only their branch
            $branches->where('id', $user->branch_id);

            // Optional: filter brands/companies to match manager's branch
            $brands->whereHas('branches', function ($q) use ($user) {
                $q->where('id', $user->branch_id);
            });

            $companies->whereHas('brands.branches', function ($q) use ($user) {
                $q->where('id', $user->branch_id);
            });
        } else {
            $companies->whereKey($user->company_id);
            $brands->where('company_id', $user->company_id);
            $branches->where('company_id', $user->company_id);
        }

        $companies = $companies->get();
        $brands = $brands->get();
        $branches = $branches->get();

        // Get unpaid payrolls grouped by branch
        $unpaidBranches = Branch::withCount(['employees as pending_salaries_count' => function ($q) {
            $q->join('payrolls', 'payrolls.employee_id', '=', 'employees.id')
                ->where('payrolls.status', 'Pending')
                ->where('payrolls.month', date('Y-m'));
        }]);

        // Apply role restriction to unpaidBranches for manager
        if (! $this->isSuperAdmin($user)) {
            if ($user->branch_id) {
                $unpaidBranches->where('id', $user->branch_id);
            } else {
                $unpaidBranches->where('company_id', $user->company_id);
            }
        }

        if ($user->hasRole('manager') && $user->branch_id) {
            $unpaidBranches->where('id', $user->branch_id);
        }

        $unpaidBranches = $unpaidBranches->paginate(10)->appends(request()->query());

        return view('Admin.Backend.Salaries.index', compact(
            'companies',
            'brands',
            'branches',
            'unpaidBranches'
        ));
    }

    public function branchSalaries(Request $request)
    {
        $request->validate([
            // 'company_id' => 'required',
            // 'brand_id' => 'required',
            'branch_id' => 'required',
            'month' => 'required',
        ]);

        // $companyId = $request->company_id;
        // $brandId = $request->brand_id;
        $branchId = $request->branch_id;
        $month = $request->month;
        abort_unless($this->userCanAccessBranch((int) $branchId, $request->user()), 403);

        $allPayrolls = $this->scopePayrollsForUser(Payroll::query(), $request->user())
            ->whereHas('employee', function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        })
            ->where('month', $month)
            ->get();

        // Summary logic (from full dataset)
        $summary = [
            'total_employees' => $allPayrolls->count(),
            'total_salary' => $allPayrolls->sum('basic_salary'),
            'paid_salary' => $allPayrolls->where('status', 'Paid')->sum('net_pay'),
            'pending_salary' => $allPayrolls->where('status', 'Pending')->sum('net_pay'),
        ];

        // Paginated payrolls for table display
        $payrolls = $this->scopePayrollsForUser(Payroll::query(), $request->user())
            ->whereHas('employee', function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        })
            ->where('month', $month)
            ->paginate(10)
            ->appends($request->query());

        return view('Admin.Backend.Salaries.branch_salaries', compact('payrolls', 'summary'));
    }

    public function payBranchSalaries(Request $request)
    {
        $payrollIds = $request->payroll_ids; // array of payroll ids
        if (! $payrollIds) {
            return redirect()->back()->with('error', __('messages.no_payroll_selected'));
        }

        $this->scopePayrollsForUser(Payroll::query(), $request->user())
            ->whereIn('id', $payrollIds)
            ->update(['status' => 'Paid']);

        return redirect()->back()->with('success', __('messages.salaries_marked_as_paid_successfully'));
    }

    private function scopeEmployeesForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        if ($user->branch_id) {
            return $query->where('branch_id', $user->branch_id);
        }

        return $query->where('company_id', $user->company_id);
    }

    private function scopePayrollsForUser($query, $user)
    {
        return $query->whereHas('employee', fn ($employeeQuery) => $this->scopeEmployeesForUser($employeeQuery, $user));
    }
}
