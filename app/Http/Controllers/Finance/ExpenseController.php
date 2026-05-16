<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\AdministrativeExpense;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            $branches = Branch::all();
            $expenses = AdministrativeExpense::all();
            $expenseCards = AdministrativeExpense::paginate(10);
        } elseif ($user->branch_id) {
            $branches = Branch::where('id', $user->branch_id)->get();
            $expenses = AdministrativeExpense::where('branch_id', $user->branch_id)->get();
            $expenseCards = AdministrativeExpense::where('branch_id', $user->branch_id)->paginate(10);
        } else {
            $companyBranchIds = Branch::where('company_id', $user->company_id)->pluck('id');
            $branches = Branch::whereIn('id', $companyBranchIds)->get();
            $expenses = AdministrativeExpense::whereIn('branch_id', $companyBranchIds)->get();
            $expenseCards = AdministrativeExpense::whereIn('branch_id', $companyBranchIds)->paginate(10);
        }

        return view('Admin.Backend.AdministrativeExpenses.index', compact(
            'branches',
            'expenses',
            'expenseCards'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_number.*' => 'required|numeric',
            'name.*' => 'required|string|max:255',
            'item_quantity.*' => 'required|numeric',
            'branch_id.*' => [
                'required',
                Rule::exists('branches', 'id')->where(fn ($query) => $this->scopeBranchesForUser($query, $request->user())),
            ],
            'amount.*' => 'required|numeric',
            'purchase_date.*' => 'required|date',
            'file.*' => 'nullable|file|mimes:jpg,png,pdf,docx',
        ]);

        $user = Auth::id();

        foreach ($request->invoice_number as $index => $inv) {

            // Handle file
            $fileName = null;
            if ($request->hasFile("file.$index")) {
                $file = $request->file("file.$index");
                $fileName = time().'_'.$file->getClientOriginalName();
                $file->storeAs('administrative_expenses', $fileName, 'public');
            }

            AdministrativeExpense::create([
                'branch_id' => $request->branch_id[$index],
                'invoice_number' => $inv,
                'quantity' => $request->item_quantity[$index],
                'item_name' => $request->name[$index],
                'amount' => $request->amount[$index],
                'expense_date' => $request->purchase_date[$index],
                'description' => $request->description[$index] ?? null,
                'file' => $fileName,
                'created_by' => $user,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => __('messages.expense_added_successfully'),
        ]);
    }

    public function update(Request $request)
    {
        // Validate input
        $request->validate([
            'id' => 'required|exists:administrative_expenses,id',
            'item_name' => 'required|string|max:255',
            'branch_id' => [
                'required',
                Rule::exists('branches', 'id')->where(fn ($query) => $this->scopeBranchesForUser($query, $request->user())),
            ],
            'amount' => 'required|numeric',
            'expense_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        // Find the expense
        $expense = $this->scopeExpensesForUser(AdministrativeExpense::query(), $request->user())
            ->findOrFail($request->id);

        // Update fields
        $expense->item_name = $request->item_name;
        $expense->invoice_number = $request->invoice_number;
        $expense->quantity = $request->item_quantity;
        $expense->branch_id = $request->branch_id;
        $expense->amount = $request->amount;
        $expense->expense_date = $request->expense_date;
        $expense->description = $request->description;
        $expense->save();

        // Load branch relationship for response
        $expense->load('branch');

        // Return JSON response for AJAX
        return response()->json([
            'success' => true,
            'message' => __('messages.expense_updated_successfully'),
            'data' => [
                'id' => $expense->id,
                'item_name' => $expense->item_name,
                'branch_id' => $expense->branch_id,
                'branch_name' => $expense->branch->name,
                'amount' => $expense->amount,
                'expense_date' => $expense->expense_date,
                'description' => $expense->description,
            ],
        ]);
    }

    public function destroy($id)
    {
        $expense = $this->scopeExpensesForUser(AdministrativeExpense::query(), Auth::user())
            ->find($id);

        if (! $expense) {
            return response()->json([
                'success' => false,
                'message' => 'Expense not found.',
            ], 404);
        }

        // Delete the file if it exists
        if ($expense->file && Storage::disk('public')->exists('administrative_expenses/'.$expense->file)) {
            Storage::disk('public')->delete('administrative_expenses/'.$expense->file);
        }

        // Delete the database record
        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.expense_deleted_successfully'),
            'id' => $id,
        ]);
    }

    // public function filter(Request $request)
    // {
    //     $user = Auth::user();

    //     $query = AdministrativeExpense::with('branch');

    //     // Role-based: manager sees only their branch
    //     if ($user->hasRole('manager')) {
    //         $query->where('branch_id', $user->branch_id);
    //     }

    //     // Super admin filter by branch
    //     if ($user->hasRole('super_admin') && $request->filled('branch_id')) {
    //         $query->where('branch_id', $request->branch_id);
    //     }

    //     // Item name search
    //     if ($request->filled('item_name')) {
    //         $query->where('item_name', 'like', '%'.$request->item_name.'%');
    //     }

    //     // Date range on expense_date
    //     if ($request->filled('start_date')) {
    //         $query->whereDate('expense_date', '>=', $request->start_date);
    //     }
    //     if ($request->filled('end_date')) {
    //         $query->whereDate('expense_date', '<=', $request->end_date);
    //     }

    //     $expenses = $query->orderBy('expense_date', 'desc')->get();

    //     // Render partial
    //     $html = view('Admin.Backend.partials.expenses_rows', compact('expenses'))->render();

    //     return response()->json([
    //         'html' => $html,
    //     ]);
    // }

    public function filter(Request $request)
    {
        $user = Auth::user();

        $query = $this->scopeExpensesForUser(AdministrativeExpense::with('branch'), $user);

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
                if ($request->filled('branch_id')) {
                    if (! $this->userCanAccessBranch((int) $request->branch_id, $user)) {
                        return response()->json(['html' => ''], 403);
                    }

                    $query->where('branch_id', $request->branch_id);
                }
            }
        }

        // Item name search
        if ($request->filled('item_name')) {
            $query->where('item_name', 'like', '%'.$request->item_name.'%');
        }

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('expense_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('expense_date', '<=', $request->end_date);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();

        // Render partial
        $html = view('Admin.Backend.partials.expenses_rows', compact('expenses'))->render();

        return response()->json([
            'html' => $html,
        ]);
    }

    private function scopeExpensesForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        if ($user->branch_id) {
            return $query->where('branch_id', $user->branch_id);
        }

        $branchIds = Branch::where('company_id', $user->company_id)->pluck('id');

        return $query->whereIn('branch_id', $branchIds);
    }
}
