<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $clients = $this->scopeClientsForUser(Client::withoutGlobalScopes()->with('documents'), Auth::user())->get();

        return view('Admin.Backend.Client.index', compact('clients'));
    }

    public function store(Request $request)
    {
        // Validate basic client info
        $request->validate([
            'company_name' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'cr_number' => 'nullable|string|max:100',
            'vat_number' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'person_name' => 'nullable|string|max:255',
            'contact' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            // documents
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'start_date.*' => 'nullable|date',
            'end_date.*' => 'nullable|date|after_or_equal:start_date.*',
        ]);

        //  Create client
        $user = Auth::user();
        $client = Client::create([
            'company_id' => $this->companyIdForUser($user),
            'branch_id' => $this->branchIdForUser($user),
            'company_name' => $request->company_name,
            'client_name' => $request->client_name,
            'cr_number' => $request->cr_number,
            'vat_number' => $request->vat_number,
            'email' => $request->email,
            'phone' => $request->phone,
            'person_name' => $request->person_name,
            'contact' => $request->contact,
            'address' => $request->address,
        ]);

        //  Store documents if any
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $index => $file) {

                $filePath = $file->store('client_documents', 'public');

                ClientDocument::create([
                    'client_id' => $client->id,
                    'file_path' => $filePath,
                    'start_date' => $request->start_date[$index] ?? null,
                    'end_date' => $request->end_date[$index] ?? null,
                ]);
            }
        }

        return redirect()->back()->with('success', __('messages.client_added_successfully'));
    }

    public function update(Request $request, $client)
    {
        $client = $this->scopeClientsForUser(Client::withoutGlobalScopes(), Auth::user())->findOrFail($client);

        // Validate basic client info
        $request->validate([
            'company_name' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'cr_number' => 'nullable|string|max:100',
            'vat_number' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'person_name' => 'nullable|string|max:255',
            'contact' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            // documents
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'start_date.*' => 'nullable|date',
            'end_date.*' => 'nullable|date',
            'document_ids.*' => 'nullable|integer', // optional array for existing document IDs
        ]);

        // Update client info
        $client->update([
            'company_name' => $request->company_name,
            'client_name' => $request->client_name,
            'cr_number' => $request->cr_number,
            'vat_number' => $request->vat_number,
            'email' => $request->email,
            'phone' => $request->phone,
            'person_name' => $request->person_name,
            'contact' => $request->contact,
            'address' => $request->address,
        ]);

        // Update existing documents (if you sent document IDs)
        if ($request->filled('document_ids')) {
            foreach ($request->document_ids as $index => $docId) {
                $document = $client->documents()->whereKey($docId)->first();
                if ($document) {
                    // If a new file is uploaded, replace it
                    if ($request->hasFile('files') && isset($request->file('files')[$index])) {
                        // Delete old file
                        if (\Storage::disk('public')->exists($document->file_path)) {
                            \Storage::disk('public')->delete($document->file_path);
                        }
                        $document->file_path = $request->file('files')[$index]->store('client_documents', 'public');
                    }

                    $document->start_date = $request->start_date[$index] ?? $document->start_date;
                    $document->end_date = $request->end_date[$index] ?? $document->end_date;
                    $document->save();
                }
            }
        }

        // Add new documents if any (files without document_ids)
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $index => $file) {
                // skip files that belong to existing documents
                if (isset($request->document_ids[$index])) {
                    continue;
                }

                $filePath = $file->store('client_documents', 'public');

                ClientDocument::create([
                    'client_id' => $client->id,
                    'file_path' => $filePath,
                    'start_date' => $request->start_date[$index] ?? null,
                    'end_date' => $request->end_date[$index] ?? null,
                ]);
            }
        }

        return redirect()->back()->with('success', __('messages.client_updated_successfully'));
    }

    public function destroy($client)
    {
        $client = $this->scopeClientsForUser(Client::withoutGlobalScopes()->with('documents'), Auth::user())->findOrFail($client);

        // Delete all related documents from storage
        foreach ($client->documents as $doc) {
            if (\Storage::disk('public')->exists($doc->file_path)) {
                \Storage::disk('public')->delete($doc->file_path);
            }
        }

        // Delete related documents from database
        $client->documents()->delete();

        // Delete the client
        $client->delete();

        return redirect()->back()->with('delete', __('messages.client_deleted_successfully'));
    }

    protected function scopeClientsForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        $query->where('company_id', $this->companyIdForUser($user));

        if ($user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        }

        return $query;
    }

    protected function companyIdForUser($user): ?int
    {
        return $user?->company_id ?: $user?->branch?->company_id;
    }

    protected function branchIdForUser($user): ?int
    {
        return $user?->branch_id;
    }
}
