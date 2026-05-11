<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        $expenses = ProjectExpense::with('project')->get();

        return view('Admin.Backend.Projects.expense', compact('projects', 'expenses'));
    }

    public function store(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,xlsx,doc,docx|max:2048', // max 2MB per file
        ]);

        $expense = new ProjectExpense;
        $expense->project_id = $validated['project_id'];
        $expense->expense_date = $validated['expense_date'];
        $expense->amount = $validated['amount'];
        $expense->category = $validated['category'];

        // Handle multiple documents
        if ($request->hasFile('documents')) {
            $files = [];
            foreach ($request->file('documents') as $file) {
                $path = $file->store('project_expenses', 'public');
                $files[] = $path;
            }
            $expense->documents = json_encode($files);
        }

        $expense->save();

        return redirect()->back()->with('success', __('messages.project_expense_added_successfully'));
    }

    public function update(Request $request, $id)
    {
        $expense = ProjectExpense::findOrFail($id);

        // Validate request
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:5120',
        ]);

        // Handle new document uploads
        $existingDocs = $expense->documents ? json_decode($expense->documents, true) : [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('project_expenses', 'public');
                $existingDocs[] = $path;
            }
        }

        // Update expense
        $expense->update([
            'project_id' => $request->project_id,
            'expense_date' => $request->expense_date,
            'amount' => $request->amount,
            'category' => $request->category,
            'documents' => json_encode($existingDocs),
        ]);

        return redirect()->back()->with('success', __('messages.project_expense_updated_successfully'));
    }

    public function destroy($id)
    {
        $expense = ProjectExpense::findOrFail($id);

        // Delete associated documents from storage
        if ($expense->documents) {
            $docs = json_decode($expense->documents, true);
            foreach ($docs as $doc) {
                if (Storage::disk('public')->exists($doc)) {
                    Storage::disk('public')->delete($doc);
                }
            }
        }

        // Delete the expense
        $expense->delete();

        return redirect()->back()->with('delete', __('messages.project_expense_deleted_successfully'));
    }
}
