<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    // public function index()
    // {
    //     $user = Auth::user();

    //     $branches = collect();
    //     $transactions = collect();

    //     if ($user->hasRole('super_admin')) {
    //         $branches = Branch::all();
    //         $transactions = Transaction::with('branch')->get();
    //         $transactionCards = Transaction::with('branch')->paginate(10);
    //     } elseif ($user->hasRole('manager')) {
    //         // Manager → only their branch
    //         $branches = Branch::where('id', $user->branch_id)->get();

    //         // Filter transactions for manager's branch
    //         $transactions = Transaction::where('branch_id', $user->branch_id)->get();
    //         $transactionCards = Transaction::where('branch_id', $user->branch_id)->paginate(10);
    //     }

    //     return view('Admin.Backend.Transaction.index', compact('branches', 'transactions', 'transactionCards'));
    // }

    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            // Super admin sees all branches and transactions
            $branches = Branch::all();
            $transactions = Transaction::with('branch')->get();
            $transactionCards = Transaction::with('branch')->paginate(10);
        } else {
            // Non-super admin → only their branch
            $branchId = $user->branch_id;

            $branches = Branch::where('id', $branchId)->get();
            $transactions = Transaction::with('branch')->where('branch_id', $branchId)->get();
            $transactionCards = Transaction::with('branch')->where('branch_id', $branchId)->paginate(10);
        }

        return view('Admin.Backend.Transaction.index', compact(
            'branches',
            'transactions',
            'transactionCards'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $transaction = Transaction::create([
            'type' => $request->type,
            'branch_id' => $request->branch_id,
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description,
        ]);

        // Load branch name for JS display
        $transaction->branch_name = $transaction->branch->name ?? '';

        return response()->json([
            'success' => true,
            'message' => __('messages.transaction_added_successfully'),
            'data' => $transaction,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $transaction = Transaction::findOrFail($id);
        $transaction->update([
            'type' => $request->type,
            'branch_id' => $request->branch_id,
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description,
        ]);

        $transaction->branch_name = $transaction->branch->name ?? '';

        return response()->json([
            'success' => true,
            'message' => __('messages.transaction_updated_successfully'),
            'data' => $transaction,
        ]);
    }

    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.transaction_deleted_successfully'),
        ]);
    }

    // public function filter(Request $request)
    // {
    //     $user = Auth::user();

    //     $query = Transaction::with('branch');

    //     // Manager can only see his branch
    //     if ($user->hasRole('manager')) {
    //         $query->where('branch_id', $user->branch_id);
    //     }

    //     // Super admin branch filter
    //     if ($user->hasRole('super_admin') && $request->filled('branch_id')) {
    //         $query->where('branch_id', $request->branch_id);
    //     }

    //     if ($request->filled('type')) {
    //         $query->where('type', $request->type);
    //     }

    //     if ($request->filled('start_date')) {
    //         $query->whereDate('date', '>=', $request->start_date);
    //     }

    //     if ($request->filled('end_date')) {
    //         $query->whereDate('date', '<=', $request->end_date);
    //     }

    //     if ($request->filled('q')) {
    //         $q = $request->q;
    //         $query->where(function ($qb) use ($q) {
    //             $qb->where('description', 'like', "%{$q}%")
    //                 ->orWhere('amount', 'like', "%{$q}%");
    //         });
    //     }

    //     // $perPage = $request->input('per_page', 15);
    //     $transactions = $query->orderBy('date', 'desc')->get();

    //     $html = view('Admin.Backend.partials.transactions_rows', compact('transactions'))->render();

    //     return response()->json([
    //         'html' => $html,
    //     ]);
    // }

    public function filter(Request $request)
    {
        $user = Auth::user();

        $query = Transaction::with('branch');

        if ($user->hasRole('super_admin')) {
            // Super admin → optional branch filter
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

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Date filters
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        // Search query filter
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qb) use ($q) {
                $qb->where('description', 'like', "%{$q}%")
                    ->orWhere('amount', 'like', "%{$q}%");
            });
        }

        $transactions = $query->orderBy('date', 'desc')->get();

        $html = view('Admin.Backend.partials.transactions_rows', compact('transactions'))->render();

        return response()->json([
            'html' => $html,
        ]);
    }
}
