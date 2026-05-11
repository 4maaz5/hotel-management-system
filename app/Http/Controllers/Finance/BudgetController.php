<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    // public function index()
    // {
    //     $user = auth()->user();

    //     if ($user->hasRole('super_admin')) {
    //         $budgets = Budget::with('branch')->latest()->get();
    //         $budgetCards = Budget::with('branch')->latest()->paginate(10);
    //     } elseif ($user->hasRole('manager')) {
    //         $budgets = Budget::with('branch')
    //             ->where('branch_id', $user->branch_id)
    //             ->latest()
    //             ->get();
    //         $budgetCards = Budget::with('branch')
    //             ->where('branch_id', $user->branch_id)
    //             ->latest()
    //             ->paginate(10);
    //     } else {
    //         $budgets = collect();
    //     }

    //     $branches = Branch::with('company', 'brand')->get();

    //     return view('Admin.Backend.Budget.index', compact('budgets', 'branches', 'budgetCards'));
    // }

    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            // Super admin sees all budgets
            $budgets = Budget::with('branch')->latest()->get();
            $budgetCards = Budget::with('branch')->latest()->paginate(10);
            $branches = Branch::with('company', 'brand')->get();
        } else {
            // Non-super admin → only their branch
            $branchId = $user->branch_id;

            $budgets = Budget::with('branch')
                ->where('branch_id', $branchId)
                ->latest()
                ->get();

            $budgetCards = Budget::with('branch')
                ->where('branch_id', $branchId)
                ->latest()
                ->paginate(10);

            $branches = Branch::with('company', 'brand')
                ->where('id', $branchId)
                ->get();
        }

        return view('Admin.Backend.Budget.index', compact(
            'budgets',
            'branches',
            'budgetCards'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'total_budget' => 'required|numeric|min:0',
            'used_budget' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|string|in:On Track,At Risk,Over Spent',
        ]);

        $validated['remaining_budget'] = $validated['total_budget'] - ($validated['used_budget'] ?? 0);

        $budget = Budget::create($validated);

        return response()->json([
            'success' => true,
            'message' => __('messages.budget_created_successfully'),
            'budget' => $budget->load('branch'),
        ]);
    }

    public function edit($id)
    {
        $budget = Budget::with('branch')->findOrFail($id);

        return response()->json($budget);
    }

    public function update(Request $request, $id)
    {
        $budget = Budget::findOrFail($id);

        $budget->update([
            'branch_id' => $request->branch_id,
            'total_budget' => $request->total_budget,
            'used_budget' => $request->used_budget,
            'remaining_budget' => $request->total_budget - $request->used_budget,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
        ]);

        // Load branch relationship for JS
        $budget->load('branch');

        return response()->json([
            'success' => true,
            'message' => __('messages.budget_updated_successfully'),
            'budget' => $budget,
        ]);
    }

    public function destroy($id)
    {
        $budget = Budget::findOrFail($id);
        $budget->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.budget_deleted_successfully'),
        ]);
    }

    // public function filter(Request $request)
    // {
    //     $user = Auth::user();

    //     $query = Budget::with('branch');

    //     // Manager can only see their branch
    //     if ($user->hasRole('manager')) {
    //         $query->where('branch_id', $user->branch_id);
    //     }

    //     // Super admin branch filter
    //     if ($user->hasRole('super_admin') && $request->filled('branch_id')) {
    //         $query->where('branch_id', $request->branch_id);
    //     }

    //     // Status filter
    //     if ($request->filled('status')) {
    //         $query->where('status', $request->status);
    //     }

    //     // Date range filter
    //     if ($request->filled('start_date')) {
    //         $query->whereDate('start_date', '>=', $request->start_date);
    //     }

    //     if ($request->filled('end_date')) {
    //         $query->whereDate('end_date', '<=', $request->end_date);
    //     }

    //     $budgets = $query->orderBy('start_date', 'desc')->get();

    //     $html = view('Admin.Backend.partials.budgets_rows', compact('budgets'))->render();

    //     return response()->json([
    //         'html' => $html,
    //     ]);
    // }
    public function filter(Request $request)
    {
        $user = Auth::user();

        $query = Budget::with('branch');

        if ($user->hasRole('super_admin')) {
            // Super admin → can filter by branch if provided
            if ($request->filled('branch_id')) {
                $query->where('branch_id', $request->branch_id);
            }
        } else {
            // Other roles → only their branch
            if ($user->branch_id) {
                $query->where('branch_id', $user->branch_id);
            } else {
                // No branch assigned → return empty
                return response()->json(['html' => '']);
            }
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        $budgets = $query->orderBy('start_date', 'desc')->get();

        $html = view('Admin.Backend.partials.budgets_rows', compact('budgets'))->render();

        return response()->json([
            'html' => $html,
        ]);
    }
}
