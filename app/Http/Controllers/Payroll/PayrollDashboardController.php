<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollDashboardController extends Controller
{
    use ScopesTenantAccess;

    // public function PayrollDashboard()
    // {
    //     $user = Auth::user();

    //     // Initialize variables
    //     $employees = collect();
    //     $payrolls = collect();
    //     $monthlySalary = 0;
    //     $pendingSalary = 0;
    //     $averageSalary = 0;
    //     $payrollPercent = 0; //  New field

    //     // Super Admin → all branches
    //     if ($user->hasRole('super_admin')) {
    //         $employees = Employee::with('branch')->get();
    //         $payrolls = Payroll::with('employee')->latest()->get();
    //         $payrollCards = Payroll::with('employee')->latest()->paginate(10);

    //         $monthlySalary = Payroll::whereMonth('created_at', Carbon::now()->month)
    //             ->whereYear('created_at', Carbon::now()->year)
    //             ->sum('net_pay');

    //         $pendingSalary = Payroll::where('status', 'pending')->sum('net_pay');

    //         $averageSalary = Payroll::whereMonth('created_at', Carbon::now()->month)
    //             ->whereYear('created_at', Carbon::now()->year)
    //             ->avg('net_pay');

    //         // Payroll percentage (paid vs total)
    //         $payrollPercent = $monthlySalary > 0
    //             ? (($monthlySalary - $pendingSalary) / $monthlySalary) * 100
    //             : 0;
    //     }

    //     //  Manager → only their branch
    //     elseif ($user->hasRole('manager')) {
    //         $employees = Employee::with('branch')
    //             ->where('branch_id', $user->branch_id)
    //             ->get();

    //         $payrolls = Payroll::with('employee')
    //             ->whereHas('employee', function ($query) use ($user) {
    //                 $query->where('branch_id', $user->branch_id);
    //             })
    //             ->latest()
    //             ->get();
    //         $payrollCards = Payroll::with('employee')
    //             ->whereHas('employee', function ($query) use ($user) {
    //                 $query->where('branch_id', $user->branch_id);
    //             })
    //             ->latest()
    //             ->paginate(10);

    //         $monthlySalary = Payroll::whereHas('employee', function ($query) use ($user) {
    //             $query->where('branch_id', $user->branch_id);
    //         })
    //             ->whereMonth('created_at', Carbon::now()->month)
    //             ->whereYear('created_at', Carbon::now()->year)
    //             ->sum('net_pay');

    //         $pendingSalary = Payroll::whereHas('employee', function ($query) use ($user) {
    //             $query->where('branch_id', $user->branch_id);
    //         })
    //             ->where('status', 'pending')
    //             ->sum('net_pay');

    //         $averageSalary = Payroll::whereHas('employee', function ($query) use ($user) {
    //             $query->where('branch_id', $user->branch_id);
    //         })
    //             ->whereMonth('created_at', Carbon::now()->month)
    //             ->whereYear('created_at', Carbon::now()->year)
    //             ->avg('net_pay');

    //         // Payroll % for manager’s branch
    //         $payrollPercent = $monthlySalary > 0
    //             ? (($monthlySalary - $pendingSalary) / $monthlySalary) * 100
    //             : 0;
    //     }

    //     // Employee → only their own payroll
    //     elseif ($user->hasRole('employee')) {
    //         $employee = Employee::where('user_id', $user->id)->first();

    //         $employees = collect([$employee]);

    //         $payrolls = Payroll::with('employee')
    //             ->where('employee_id', $employee->id)
    //             ->latest()
    //             ->get();

    //         $monthlySalary = Payroll::where('employee_id', $employee->id)
    //             ->whereMonth('created_at', Carbon::now()->month)
    //             ->whereYear('created_at', Carbon::now()->year)
    //             ->sum('net_pay');

    //         $pendingSalary = Payroll::where('employee_id', $employee->id)
    //             ->where('status', 'pending')
    //             ->sum('net_pay');

    //         $averageSalary = Payroll::where('employee_id', $employee->id)
    //             ->whereMonth('created_at', Carbon::now()->month)
    //             ->whereYear('created_at', Carbon::now()->year)
    //             ->avg('net_pay');

    //         // Payroll % for this employee
    //         $payrollPercent = $monthlySalary > 0
    //             ? (($monthlySalary - $pendingSalary) / $monthlySalary) * 100
    //             : 0;
    //     }

    //     return view('Admin.Backend.Payroll.dashboard', compact(
    //         'employees',
    //         'payrolls',
    //         'payrollCards',
    //         'monthlySalary',
    //         'pendingSalary',
    //         'averageSalary',
    //         'payrollPercent'
    //     ));
    // }

    public function PayrollDashboard()
    {
        $user = Auth::user();

        // Initialize variables
        $employees = collect();
        $payrolls = collect();
        $payrollCards = collect();
        $monthlySalary = 0;
        $pendingSalary = 0;
        $averageSalary = 0;
        $payrollPercent = 0;

        if ($user->hasRole('super_admin')) {
            // Super Admin → all branches
            $employees = Employee::with('branch')->get();
            $payrolls = Payroll::with('employee')->latest()->get();
            $payrollCards = Payroll::with('employee')->latest()->paginate(10);

            $monthlySalary = Payroll::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('net_pay');

            $pendingSalary = Payroll::where('status', 'pending')->sum('net_pay');

            $averageSalary = Payroll::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->avg('net_pay');

        } elseif ($user->branch_id) {
            $employees = Employee::with('branch')
                ->where('branch_id', $user->branch_id)->get();
            $payrolls = Payroll::with('employee')
                ->whereHas('employee', fn ($q) => $q->where('branch_id', $user->branch_id))
                ->latest()->get();
            $payrollCards = Payroll::with('employee')
                ->whereHas('employee', fn ($q) => $q->where('branch_id', $user->branch_id))
                ->latest()->paginate(10);
            $monthlySalary = Payroll::whereHas('employee', fn ($q) => $q->where('branch_id', $user->branch_id))
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)->sum('net_pay');
            $pendingSalary = Payroll::whereHas('employee', fn ($q) => $q->where('branch_id', $user->branch_id))
                ->where('status', 'pending')->sum('net_pay');
            $averageSalary = Payroll::whereHas('employee', fn ($q) => $q->where('branch_id', $user->branch_id))
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)->avg('net_pay');
        } else {
            $employees = Employee::where('company_id', $user->company_id)->get();
            $payrolls = Payroll::with('employee')
                ->whereHas('employee', fn ($q) => $q->where('company_id', $user->company_id))
                ->latest()->get();
            $payrollCards = Payroll::with('employee')
                ->whereHas('employee', fn ($q) => $q->where('company_id', $user->company_id))
                ->latest()->paginate(10);
            $monthlySalary = Payroll::whereHas('employee', fn ($q) => $q->where('company_id', $user->company_id))
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)->sum('net_pay');
            $pendingSalary = Payroll::whereHas('employee', fn ($q) => $q->where('company_id', $user->company_id))
                ->where('status', 'pending')->sum('net_pay');
            $averageSalary = Payroll::whereHas('employee', fn ($q) => $q->where('company_id', $user->company_id))
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)->avg('net_pay');
        }

        // Payroll percentage (paid vs total)
        $payrollPercent = $monthlySalary > 0
            ? (($monthlySalary - $pendingSalary) / $monthlySalary) * 100
            : 0;

        return view('Admin.Backend.Payroll.dashboard', compact(
            'employees',
            'payrolls',
            'payrollCards',
            'monthlySalary',
            'pendingSalary',
            'averageSalary',
            'payrollPercent'
        ));
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

        // Role-based filtering
        if ($user->hasRole('manager') && $user->branch_id) {
            $query->whereHas('employee', fn ($q) => $q->where('branch_id', $user->branch_id));
        } elseif ($user->hasRole('employee')) {
            $employee = Employee::where('user_id', $user->id)->first();
            if (! $employee) {
                return redirect()->back()->with('error', __('messages.no_employee_record'));
            }
            $query->where('employee_id', $employee->id);
        }

        // Apply filters
        if ($employeeId) {
            $this->scopeEmployeesForUser(Employee::query(), $user)->findOrFail($employeeId);
            $query->where('employee_id', $employeeId);
        }

        if ($branchId && $user->hasRole('super_admin')) {
            $query->whereHas('employee', fn ($q) => $q->where('branch_id', $branchId));
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($startDate && $endDate) {
            $from = Carbon::parse($startDate)->startOfDay();
            $to = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        } else {
            if ($month) {
                if (str_contains($month, '-')) {
                    $m = intval(explode('-', $month)[1]);
                } else {
                    $m = intval($month);
                }
                $query->whereMonth('created_at', $m);
            }

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

        if ($request->ajax()) {
            $html = view('Admin.Backend.partials.filter-results', compact('payrolls'))->render();

            return response()->json(['html' => $html]);
        }

        // Normal full-page render
        $employees = $this->scopeEmployeesForUser(Employee::select('id', 'first_name', 'last_name'), $user)->get();
        $branches = $this->scopeBranchesForUser(Branch::select('id', 'name'), $user)->get();

        return view('Admin.Backend.partials.filter-results', compact(
            'payrolls', 'employees', 'branches', 'summary'
        ));
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
