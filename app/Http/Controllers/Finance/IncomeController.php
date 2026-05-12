<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
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

        if ($user->hasRole('super_admin')) {
            // Super admin sees everything
            $branches = Branch::all();
            $employees = Employee::all();
            $incomes = Income::with(['branch', 'user'])->latest()->get();
            $incomeCards = Income::with(['branch', 'user'])->latest()->paginate(10);
        } else {
            $branchId = $user->branch_id;

            if ($branchId) {
                $branches = Branch::where('id', $branchId)->get();
                $employees = Employee::where('branch_id', $branchId)->get();
                $incomes = Income::with(['branch', 'user'])
                    ->where('branch_id', $branchId)
                    ->latest()
                    ->get();
                $incomeCards = Income::with(['branch', 'user'])
                    ->where('branch_id', $branchId)
                    ->latest()
                    ->paginate(10);
            } else {
                $branches = Branch::all();
                $employees = Employee::all();
                $incomes = Income::with(['branch', 'user'])->latest()->get();
                $incomeCards = Income::with(['branch', 'user'])->latest()->paginate(10);
            }
        }

        return view('Admin.Backend.Income.index', compact(
            'incomes',
            'branches',
            'incomeCards',
            'employees'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'type' => 'required|string',
            'employee_id' => 'nullable|exists:employees,id',
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
                'payment_type' => $validated['payment_type'],
                'income_date' => $validated['income_date'],
                'employee_id' => $validated['employee_id'],
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
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'type' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'payment_type' => 'nullable|string',
            'income_date' => 'required|date',
        ]);

        $income = Income::findOrFail($id);

        $income->update([
            'branch_id' => $validated['branch_id'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'payment_type' => $validated['payment_type'],
            'income_date' => $validated['income_date'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => __('messages.income_updated_successfully'),
        ]);
    }

    public function destroy($id)
    {
        $income = Income::findOrFail($id);
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

        $query = Income::with('branch');

        if ($user->hasRole('super_admin')) {
            // Super admin → can filter by branch if provided
            if ($request->filled('branch_id')) {
                $query->where('branch_id', $request->branch_id);
            }
        } else {
            if ($user->branch_id) {
                $query->where('branch_id', $user->branch_id);
            }
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
}
