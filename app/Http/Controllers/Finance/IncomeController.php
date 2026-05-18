<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Income;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class IncomeController extends Controller
{
    use ScopesTenantAccess;

    // public function index()
    // {
    //     $user = Auth::user();

    //     // Send branches only for super admin
    //     $branches = collect();
    //     if ($user->hasRole('super_admin')) {
    //         $branches = Branch::all();
    //         $employees = Employee::all();
    //     }

    //     if ($user->hasRole('super_admin')) {

    //         // Super Admin sees all income records
    //         $incomes = Income::with(['branch', 'user'])->latest()->get();

    //         $incomeCards = Income::with(['branch', 'user'])->latest()->paginate(10);

    //     } elseif ($user->role!=='super_admin') {

    //         // Manager sees only their branch
    //         $branches = Branch::where('id', $user->branch_id)->get();

    //         // Employees in manager's branch
    //         $employees = Employee::where('branch_id', $user->branch_id)->get();

    //         // Incomes in manager's branch
    //         $incomes = Income::where('branch_id', $user->branch_id)
    //             ->with(['branch', 'user'])
    //             ->latest()
    //             ->get();

    //         $incomeCards = Income::where('branch_id', $user->branch_id)
    //             ->with(['branch', 'user'])
    //             ->latest()
    //             ->paginate(10);

    //     } elseif ($user->hasRole('employee')) {

    //         // Employee sees only the income he added
    //         $incomes = Income::where('user_id', $user->id)
    //             ->with(['branch', 'user'])
    //             ->latest()
    //             ->get();

    //     } else {
    //         $incomes = collect();
    //     }

    //     return view('Admin.Backend.Income.index', compact('incomes', 'branches', 'incomeCards', 'employees'));
    // }

    public function index()
    {
        $user = auth()->user();

        $branches = $this->scopeBranchesForIncome(Branch::with(['company', 'brand']), $user)->get();
        $employees = $this->scopeEmployeesForUser(Employee::query(), $user)->get();
        $incomes = $this->scopeIncomesForUser(Income::with(['branch', 'employee']), $user)->latest()->get();
        $incomeCards = $this->scopeIncomesForUser(Income::with(['branch', 'employee']), $user)->latest()->paginate(10);

        return view('Admin.Backend.Income.index', compact(
            'incomes',
            'branches',
            'incomeCards',
            'employees'
        ));
    }

    public function store(Request $request)
    {
        $request->merge([
            'branch_id' => $this->branchIdFromRequest($request),
        ]);

        $validated = $request->validate([
            'branch_id' => [
                'required',
                Rule::exists('branches', 'id')->where(fn ($query) => $this->scopeBranchesForIncome($query, $request->user())),
            ],
            'type' => 'required|string',
            'employee_id' => [
                'nullable',
                Rule::exists('employees', 'id')->where(fn ($query) => $this->scopeEmployeesForUser($query, $request->user())),
            ],
            'amount' => 'required|numeric|min:0',
            'payment_type' => 'nullable|string',
            'income_date' => 'required|date',
        ]);

        try {
            // Create income
            $income = Income::create([
                'branch_id' => $validated['branch_id'],
                'type' => $validated['type'],
                'amount' => $validated['amount'],
                'payment_type' => $validated['payment_type'] ?? null,
                'income_date' => $validated['income_date'],
                'employee_id' => $validated['employee_id'] ?? null,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.income_added_successfully'),
                'data' => $income,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->merge([
            'branch_id' => $this->branchIdFromRequest($request),
        ]);

        $validated = $request->validate([
            'branch_id' => [
                'required',
                Rule::exists('branches', 'id')->where(fn ($query) => $this->scopeBranchesForIncome($query, $request->user())),
            ],
            'type' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'payment_type' => 'nullable|string',
            'income_date' => 'required|date',
        ]);

        $income = $this->scopeIncomesForUser(Income::query(), $request->user())->findOrFail($id);

        $income->update([
            'branch_id' => $validated['branch_id'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'payment_type' => $validated['payment_type'] ?? null,
            'income_date' => $validated['income_date'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => __('messages.income_updated_successfully'),
        ]);
    }

    public function destroy($id)
    {
        $income = $this->scopeIncomesForUser(Income::query(), Auth::user())->findOrFail($id);
        $income->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('messages.income_deleted_successfully'),
        ]);
    }

    // public function filter(Request $request)
    // {
    //     $user = Auth::user();

    //     $query = Income::with('branch');

    //     // Manager can see only their branch
    //     if ($user->hasRole('manager')) {
    //         $query->where('branch_id', $user->branch_id);
    //     }

    //     // Super admin filter by branch
    //     if ($user->hasRole('super_admin') && $request->filled('branch_id')) {
    //         $query->where('branch_id', $request->branch_id);
    //     }

    //     // Type filter
    //     if ($request->filled('type')) {
    //         $query->where('type', 'like', "%{$request->type}%");
    //     }

    //     // Date filters
    //     if ($request->filled('start_date')) {
    //         $query->whereDate('income_date', '>=', $request->start_date);
    //     }

    //     if ($request->filled('end_date')) {
    //         $query->whereDate('income_date', '<=', $request->end_date);
    //     }
    //     if ($request->filled('payment_type')) {
    //         $query->where('payment_type', 'like', "%{$request->payment_type}%");
    //     }

    //     $incomes = $query->orderBy('income_date', 'desc')->get();

    //     $html = view('Admin.Backend.partials.incomes_rows', compact('incomes'))->render();

    //     return response()->json([
    //         'html' => $html,
    //     ]);
    // }
    public function filter(Request $request)
    {
        $user = Auth::user();

        $query = $this->scopeIncomesForUser(Income::with('branch'), $user);

        if (! $this->tenantIdForUser($user) && $user->hasRole('super_admin')) {
            // Super admin → can filter by branch if provided
            if ($request->filled('branch_id')) {
                $query->where('branch_id', $request->branch_id);
            }
        } elseif ($request->filled('branch_id')) {
            if (! $this->userCanAccessBranch((int) $request->branch_id, $user)) {
                return response()->json(['html' => ''], 403);
            }

            $query->where('branch_id', $request->branch_id);
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', 'like', "%{$request->type}%");
        }

        // Date filters
        if ($request->filled('start_date')) {
            $query->whereDate('income_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('income_date', '<=', $request->end_date);
        }

        // Payment type filter
        if ($request->filled('payment_type')) {
            $query->where('payment_type', 'like', "%{$request->payment_type}%");
        }

        $incomes = $query->orderBy('income_date', 'desc')->get();

        $html = view('Admin.Backend.partials.incomes_rows', compact('incomes'))->render();

        return response()->json([
            'html' => $html,
        ]);
    }

    private function scopeIncomesForUser($query, $user)
    {
        if (! $this->tenantIdForUser($user) && $this->isSuperAdmin($user)) {
            return $query;
        }

        if ($user->branch_id) {
            return $query->where('branch_id', $user->branch_id);
        }

        $branchIds = Branch::where('company_id', $this->tenantIdForUser($user))->pluck('id');

        return $query->whereIn('branch_id', $branchIds);
    }

    private function scopeEmployeesForUser($query, $user)
    {
        if (! $this->tenantIdForUser($user) && $this->isSuperAdmin($user)) {
            return $query;
        }

        if ($user->branch_id) {
            return $query->where('branch_id', $user->branch_id);
        }

        return $query->where('company_id', $this->tenantIdForUser($user));
    }

    private function scopeBranchesForIncome($query, $user)
    {
        if (! $this->tenantIdForUser($user) && $this->isSuperAdmin($user)) {
            return $query;
        }

        if ($user->branch_id) {
            return $query->where('id', $user->branch_id);
        }

        return $query->where('company_id', $this->tenantIdForUser($user));
    }

    private function branchIdFromRequest(Request $request): ?int
    {
        if ($request->filled('branch_id')) {
            return $request->integer('branch_id');
        }

        $branches = $this->scopeBranchesForIncome(Branch::query(), $request->user())->pluck('id');

        return $branches->count() === 1 ? (int) $branches->first() : null;
    }

    private function tenantIdForUser($user): ?int
    {
        return app(TenantContext::class)->id() ?: $user?->company_id;
    }
}
