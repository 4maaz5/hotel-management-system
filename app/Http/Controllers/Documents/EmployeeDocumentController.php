<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class EmployeeDocumentController extends Controller
{
    use ScopesTenantAccess;

    private const DOCUMENT_DISK = 'local';

    private const DOCUMENT_DIRECTORY = 'employee_documents';

    private const IMAGE_DIRECTORY = 'employee_documents/images';

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
            $branchId = $user->branch_id;

            if ($branchId) {
                $employees = Employee::where('branch_id', $branchId)->get();
                $employeeDocs = EmployeeDocument::whereHas('employee', function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                })->get();
                $employeeDocsCard = EmployeeDocument::whereHas('employee', function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                })->paginate(10);
            } else {
                $employees = Employee::where('company_id', $user->company_id)->get();
                $employeeDocs = EmployeeDocument::whereHas('employee', function ($q) use ($user) {
                    $q->where('company_id', $user->company_id);
                })->get();
                $employeeDocsCard = EmployeeDocument::whereHas('employee', function ($q) use ($user) {
                    $q->where('company_id', $user->company_id);
                })->paginate(10);
            }
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
            'employee_id' => [
                'required',
                Rule::exists('employees', 'id')->where(fn ($query) => $this->scopeEmployeesForUser($query, $request->user())),
            ],
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
            $filePath = $request->file('file')->store(self::DOCUMENT_DIRECTORY, self::DOCUMENT_DISK);
        }

        //  Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store(self::IMAGE_DIRECTORY, self::DOCUMENT_DISK);
        }

        // Store in database
        $document = EmployeeDocument::create([
            'employee_id' => $validated['employee_id'],
            'type' => $validated['type'],
            'file_path' => $filePath,
            'issue_date' => $validated['issue_date'],
            'document_number' => $validated['doc_number'],
            'expiration_date' => $validated['expiry_date'],
            'image' => $imagePath,
        ]);

        // Return success response (for AJAX)
        return response()->json([
            'success' => true,
            'message' => __('messages.employee_document_added_successfully'),
            'data' => $this->documentPayload($document->load('employee:id,first_name,last_name,employee_id')),
        ]);

    }

    public function update(Request $request, $id)
    {
        $document = $this->scopeEmployeeDocumentsForUser(EmployeeDocument::query(), $request->user())->findOrFail($id);

        $validated = $request->validate([
            'employee_id' => [
                'required',
                Rule::exists('employees', 'id')->where(fn ($query) => $this->scopeEmployeesForUser($query, $request->user())),
            ],
            'type' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after_or_equal:issue_date',
            'doc_number' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:2048',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('file')) {
            $this->deleteStoredAsset($document->file_path);
            $validated['file_path'] = $request->file('file')->store(self::DOCUMENT_DIRECTORY, self::DOCUMENT_DISK);
        }

        if ($request->hasFile('image')) {
            $this->deleteStoredAsset($document->image);
            $validated['image'] = $request->file('image')->store(self::IMAGE_DIRECTORY, self::DOCUMENT_DISK);
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
            'data' => $this->documentPayload($document),
        ]);
    }

    public function destroy($id)
    {
        $doc = $this->scopeEmployeeDocumentsForUser(EmployeeDocument::query(), Auth::user())->find($id);

        if (! $doc) {
            return response()->json(['success' => false, 'message' => 'Document not found.']);
        }

        $this->deleteStoredAsset($doc->file_path);
        $this->deleteStoredAsset($doc->image);

        $doc->delete();

        return response()->json(['success' => true, 'message' => __('messages.employee_document_deleted_successfully')]);
    }

    public function file(Request $request, EmployeeDocument $document): Response
    {
        return $this->streamDocumentAsset($request, $document, 'file_path');
    }

    public function image(Request $request, EmployeeDocument $document): Response
    {
        return $this->streamDocumentAsset($request, $document, 'image');
    }

    public function filter(Request $request)
    {
        $user = Auth::user();

        $query = $this->scopeEmployeeDocumentsForUser(EmployeeDocument::with('employee'), $user);

        // Filter: employee
        if ($request->filled('employee_id')) {
            if (! $this->scopeEmployeesForUser(Employee::whereKey($request->employee_id), $user)->exists()) {
                return response()->json(['html' => ''], 403);
            }

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

    private function scopeEmployeeDocumentsForUser($query, $user)
    {
        return $query->whereHas('employee', fn ($employeeQuery) => $this->scopeEmployeesForUser($employeeQuery, $user));
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

    private function streamDocumentAsset(Request $request, EmployeeDocument $document, string $column): Response
    {
        $document = $this->scopeEmployeeDocumentsForUser(EmployeeDocument::query(), $request->user())
            ->findOrFail($document->id);

        $path = $document->{$column};

        $disk = $column === 'image'
            ? $document->storedImageDisk()
            : $document->storedFileDisk();

        abort_unless($path && $disk, 404);

        $filename = basename($path);

        if ($request->boolean('download')) {
            return Storage::disk($disk)->download($path, $filename);
        }

        return Storage::disk($disk)->response($path, $filename, [
            'Cache-Control' => 'private, no-store',
        ]);
    }

    private function deleteStoredAsset(?string $path): void
    {
        if (! $path) {
            return;
        }

        foreach ([self::DOCUMENT_DISK, 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        }
    }

    private function documentPayload(EmployeeDocument $document): array
    {
        return [
            'id' => $document->id,
            'employee_id' => $document->employee_id,
            'type' => $document->type,
            'document_number' => $document->document_number,
            'issue_date' => $document->issue_date,
            'expiration_date' => $document->expiration_date,
            'file_path' => $document->file_path,
            'file_url' => $document->file_path
                ? route('dashboard.document.employee.file', $document)
                : null,
            'image' => $document->image,
            'image_url' => $document->image
                ? route('dashboard.document.employee.image', $document)
                : null,
            'employee' => $document->employee ? [
                'id' => $document->employee->id,
                'first_name' => $document->employee->first_name,
                'last_name' => $document->employee->last_name,
                'employee_id' => $document->employee->employee_id,
            ] : null,
        ];
    }
}
