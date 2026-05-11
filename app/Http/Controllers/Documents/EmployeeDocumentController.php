<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeDocumentController extends Controller
{
    // public function index()
    // {
    //     $user = auth()->user();

    //     // Super Admin sees all employees and all employee documents
    //     if ($user->hasRole('super_admin')) {
    //         $employees = Employee::all();
    //         $employeeDocs = EmployeeDocument::all();
    //         $employeeDocsCard = EmployeeDocument::paginate(10);

    //     } elseif ($user->hasRole('manager')) {
    //         // Manager sees employees and documents only in their branch
    //         $employees = Employee::where('branch_id', $user->branch_id)->get();
    //         $employeeDocs = EmployeeDocument::whereHas('employee', function ($q) use ($user) {
    //             $q->where('branch_id', $user->branch_id);
    //         })->get();
    //         $employeeDocsCard = EmployeeDocument::whereHas('employee', function ($q) use ($user) {
    //             $q->where('branch_id', $user->branch_id);
    //         })->paginate(10);

    //     } elseif ($user->hasRole('employee')) {
    //         // Employee sees only their own record and documents
    //         $employees = Employee::where('user_id', $user->id)->get();
    //         $employeeDocs = EmployeeDocument::where('employee_id', $user->employee->id ?? 0)->get();

    //     } else {
    //         $employees = collect();
    //         $employeeDocs = collect();
    //     }

    //     return view('Admin.Backend.EmployeeDocument.index', compact('employees', 'employeeDocs', 'employeeDocsCard'));
    // }

    public function index()
    {
        $user = auth()->user();

        // Initialize variables
        $employees = collect();
        $employeeDocs = collect();
        $employeeDocsCard = collect();

        if ($user->hasRole('super_admin')) {
            // Super admin sees all employees and documents
            $employees = Employee::all();
            $employeeDocs = EmployeeDocument::all();
            $employeeDocsCard = EmployeeDocument::paginate(10);
        } else {
            // Non-super-admin → only their branch
            $branchId = $user->branch_id;

            $employees = Employee::where('branch_id', $branchId)->get();

            $employeeDocs = EmployeeDocument::whereHas('employee', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })->get();

            $employeeDocsCard = EmployeeDocument::whereHas('employee', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })->paginate(10);
        }

        return view('Admin.Backend.EmployeeDocument.index', compact(
            'employees',
            'employeeDocs',
            'employeeDocsCard'
        ));
    }

    public function store(Request $request)
    {
        //  Validate the incoming data
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date',
            'doc_number' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:2048',
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        //  Handle file upload (document)
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('employee_documents', 'public');
        }

        //  Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('employee_documents/images', 'public');
        }

        // Store in database
        $document = EmployeeDocument::create([
            'employee_id' => $request->employee_id,
            'type' => $request->type,
            'file_path' => $filePath,
            'issue_date' => $request->issue_date,
            'document_number' => $request->doc_number,
            'expiration_date' => $request->expiry_date,
            'image' => $imagePath,
        ]);

        // Return success response (for AJAX)
        return response()->json([
            'success' => true,
            'message' => __('messages.employee_document_added_successfully'),
            'data' => $document->load('employee:id,first_name,last_name'),
        ]);

    }

    public function update(Request $request, $id)
    {
        $document = EmployeeDocument::findOrFail($id);

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after_or_equal:issue_date',
            'doc_number' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:2048',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('employee_documents', 'public');
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('employee_documents/images', 'public');
        }

        // update document
        $document->update([
            'employee_id' => $validated['employee_id'],
            'type' => $validated['type'],
            'document_number' => $validated['doc_number'],
            'issue_date' => $validated['issue_date'],
            'expiration_date' => $validated['expiry_date'],
            'file_path' => $validated['file_path'] ?? $document->file_path,
            'image' => $validated['image'] ?? $document->image,
        ]);

        $document->load('employee');

        return response()->json([
            'success' => true,
            'message' => __('messages.employee_document_updated_successfully'),
            'data' => [
                'id' => $document->id,
                'employee_id' => $document->employee_id,
                'type' => $document->type,
                'document_number' => $document->document_number,
                'issue_date' => $document->issue_date,
                'expiration_date' => $document->expiration_date,
                'employee' => [
                    'id' => $document->employee->id,
                    'first_name' => $document->employee->first_name,
                    'employee_id' => $document->employee->employee_id,
                ],
            ],
        ]);
    }

    public function destroy($id)
    {
        $doc = EmployeeDocument::find($id);

        if (! $doc) {
            return response()->json(['success' => false, 'message' => 'Document not found.']);
        }

        // Delete files if exist
        if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
            Storage::disk('public')->delete($doc->file_path);
        }

        if ($doc->image && Storage::disk('public')->exists($doc->image)) {
            Storage::disk('public')->delete($doc->image);
        }

        $doc->delete();

        return response()->json(['success' => true, 'message' => __('messages.employee_document_deleted_successfully')]);
    }

    public function filter(Request $request)
    {
        $user = Auth::user();

        $query = EmployeeDocument::with('employee');

        // Manager sees ONLY employees of their branch
        if ($user->hasRole('manager')) {
            $query->whereHas('employee', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }

        // Filter: employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter: document type
        if ($request->filled('type')) {
            $query->where('type', 'like', '%'.$request->type.'%');
        }

        // Filter: document number
        if ($request->filled('document_number')) {
            $query->where('document_number', 'like', '%'.$request->document_number.'%');
        }

        // Filter: issue date range
        if ($request->filled('start_date')) {
            $query->whereDate('issue_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('issue_date', '<=', $request->end_date);
        }

        $employeeDocs = $query->orderBy('issue_date', 'desc')->get();

        $html = view('Admin.Backend.partials.employee_docs_rows', compact('employeeDocs'))->render();

        return response()->json(['html' => $html]);
    }
}
