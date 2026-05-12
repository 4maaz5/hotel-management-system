<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyDocumentController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            // Super Admin sees all company documents
            $companyDocs = CompanyDocument::all();
            $companies = Company::all();
            $companyDocsCard = CompanyDocument::paginate(10);
        } else {
            $companyDocs = collect();
            $companyDocsCard = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            $companies = collect();
        }

        return view('Admin.Backend.CompanyDocument.index', compact('companyDocs', 'companyDocsCard', 'companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required',
            'type' => 'required|string|max:100',
            'issued_by' => 'nullable|string|max:255',
            'issue_date' => 'nullable|date',
            'expiration_date' => 'nullable|date',
            'file' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $data = $request->only(['name', 'company_id', 'type', 'issued_by', 'issue_date', 'expiration_date']);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = Str::slug($request->name).'-'.time().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('company_documents', $filename, 'public');
            $data['file_path'] = $path;
        }

        $companyDoc = CompanyDocument::create($data);

        // Return JSON for AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $companyDoc,
            ]);
        }

        return redirect()->back()->with('success', __('messages.company_documents_added_successfully'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'issued_by' => 'nullable|string|max:255',
            'issued_date' => 'nullable|date',
            'expiration_date' => 'nullable|date',
            'file' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $doc = CompanyDocument::findOrFail($id);

        $doc->update($request->only(['name', 'type', 'issued_by', 'issue_date', 'expiration_date']));

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = Str::slug($request->name).'-'.time().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('company_documents', $filename, 'public');
            $doc->file_path = $path;
            $doc->save();
        }

        return response()->json(['data' => $doc]);
    }

    public function destroy($id)
    {
        $doc = CompanyDocument::findOrFail($id);

        // Delete the file from the 'public' disk
        if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
            Storage::disk('public')->delete($doc->file_path);
        }

        $doc->delete();

        return response()->json(['success' => true, 'message' => __('messages.document_deleted_successfully')]);
    }

    public function filter(Request $request)
    {
        $query = CompanyDocument::query();

        // Filter by type
        if ($request->type) {
            $query->where('type', 'LIKE', '%'.$request->type.'%');
        }

        // Filter by issued_by
        if ($request->issued_by) {
            $query->where('issued_by', 'LIKE', '%'.$request->issued_by.'%');
        }

        // Filter by issue_date range
        if ($request->start_date) {
            $query->whereDate('issue_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('issue_date', '<=', $request->end_date);
        }

        // Filter by expiration_date
        if ($request->expiry_date) {
            $query->whereDate('expiration_date', $request->expiry_date);
        }

        $documents = $query->get();

        $html = view('Admin.Backend.partials.company_doce_rows', compact('documents'))->render();

        return response()->json(['html' => $html]);
    }
}
