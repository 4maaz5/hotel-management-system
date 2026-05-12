<?php

namespace App\Http\Controllers\CompanyPartners;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyPartner;
use App\Models\PartnerDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PartnerController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $companies = $user->isSuperAdmin() ? Company::all() : Company::whereKey($user->company_id)->get();
        $partners = CompanyPartner::with(['company', 'documents'])->get();

        return view('Admin.Backend.Partners.index', compact('companies', 'partners'));
    }

    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'nationality' => 'required|string|max:50',
            'company_id' => 'required|exists:companies,id',
            'partner_type' => 'required|in:owner,investor',
            'id_type' => 'required|in:national_id,iqama,passport',
            'id_number' => 'required|string|max:50',
            'investment_amount' => 'nullable|numeric|min:0',
            'share_percentage' => 'nullable|numeric|min:0|max:100',
            'share_quantity' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'documents' => 'nullable|array',
            'documents.*.type' => 'required_with:documents|string',
            'documents.*.file' => 'required_with:documents|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        DB::beginTransaction();

        try {
            //  Create partner
            $partner = CompanyPartner::create([
                'company_id' => $request->company_id,
                'partner_type' => $request->partner_type,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'nationality' => $request->nationality,
                'id_type' => $request->id_type,
                'id_number' => $request->id_number,
                'investment_amount' => $request->investment_amount,
                'share_percentage' => $request->share_percentage,
                'share_quantity' => $request->share_quantity,
                'notes' => $request->notes,
            ]);

            //  Handle documents
            if ($request->has('documents')) {
                foreach ($request->documents as $doc) {

                    if (! isset($doc['file'])) {
                        continue;
                    }

                    $file = $doc['file'];
                    $path = $file->store('partner_documents', 'public');

                    $partner->documents()->create([
                        'document_type' => $doc['type'],
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            DB::commit();

            // return response()->json([
            //     'status' => 'success',
            //     'message' => 'Partner created successfully',
            //     'partner_id' => $partner->id,
            // ]);
            return redirect()->back()->with(['success' => __('messages.partner_added_successfully')]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        //  Validate input
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'nationality' => 'required|string|max:100',
            'company_id' => 'required|exists:companies,id',
            'partner_type' => 'required|in:owner,investor',
            'id_type' => 'required|in:national_id,iqama,passport',
            'id_number' => 'required|string|max:100',
            'investment_amount' => 'nullable|numeric',
            'share_percentage' => 'nullable|numeric',
            'share_quantity' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'documents.*.file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'documents.*.type' => 'nullable|string|max:50',
        ]);

        //  Find partner
        $partner = CompanyPartner::findOrFail($id);

        //  Update partner info
        $partner->update([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'nationality' => $request->nationality,
            'company_id' => $request->company_id,
            'partner_type' => $request->partner_type,
            'id_type' => $request->id_type,
            'id_number' => $request->id_number,
            'investment_amount' => $request->investment_amount,
            'share_percentage' => $request->share_percentage,
            'share_quantity' => $request->share_quantity,
            'notes' => $request->notes,
        ]);

        //  Add new documents (if any)
        if ($request->has('documents')) {
            foreach ($request->documents as $doc) {
                if (isset($doc['file'])) {
                    $file = $doc['file'];
                    $filePath = $file->store('partner_documents', 'public');

                    PartnerDocument::create([
                        'company_partner_id' => $partner->id,
                        'document_type' => $doc['type'] ?? 'other',
                        'file_path' => $filePath,
                        'original_name' => $file->getClientOriginalName(),
                    ]);
                }
            }
        }

        //  Return response
        return redirect()->back()
            ->with(['success' => __('messages.partner_updated_successfully')]);
    }

    public function destroy(Request $request)
    {
        $partner = CompanyPartner::findOrFail($request->id);

        // Delete related documents
        foreach ($partner->documents as $doc) {
            Storage::disk('public')->delete($doc->file_path);
            $doc->delete();
        }

        // Delete partner
        $partner->delete();

        return redirect()->back()
            ->with('delete', __('messages.partner_deleted_successfully'));
    }
}
