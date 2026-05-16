<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Company;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ContractController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $user = auth()->user();
        $clients = $this->scopeClientsForUser(Client::query(), $user)->get();
        $companies = $user->isSuperAdmin() ? Company::all() : Company::whereKey($user->company_id)->get();
        $contracts = $this->scopeContractsForUser(Contract::with('client', 'company'), $user)->get();

        return view('Admin.Backend.Client.contract', compact('clients', 'contracts', 'companies'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'client_id' => [
                'required',
                Rule::exists('clients', 'id')->where(fn ($query) => $this->scopeClientsForUser($query, $request->user())),
            ],
            'company_id' => [
                'required',
                $this->isSuperAdmin($request->user())
                    ? Rule::exists('companies', 'id')
                    : Rule::exists('companies', 'id')->where(fn ($query) => $query->where('id', $request->user()->company_id)),
            ],
            'title' => 'required|string|max:255',
            'contract_number' => 'required|string|unique:contracts,contract_number',
            'status' => 'required|in:active,near_expiry,expired,ended',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'remarks' => 'nullable|string',
            'files.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:10240', // max 10MB per file
        ]);

        $filePaths = [];

        // Handle multiple file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('contracts', 'public');
                $filePaths[] = $path;
            }
        }

        // Save contract
        $contract = new Contract;
        $contract->client_id = $validated['client_id'];
        $contract->company_id = $this->isSuperAdmin($request->user()) ? $validated['company_id'] : $request->user()->company_id;
        $contract->title = $validated['title'];
        $contract->contract_number = $validated['contract_number'];
        $contract->status = $validated['status'];
        $contract->start_date = $validated['start_date'];
        $contract->end_date = $validated['end_date'];
        $contract->remarks = $validated['remarks'] ?? null;
        $contract->file = json_encode($filePaths); // Save as JSON
        $contract->save();

        return redirect()->back()->with('success', __('messages.contract_added_successfully'));
    }

    public function update(Request $request, $id)
    {
        $contract = $this->scopeContractsForUser(Contract::query(), $request->user())->findOrFail($id);
        $request->validate([
            'client_id' => [
                'required',
                Rule::exists('clients', 'id')->where(fn ($query) => $this->scopeClientsForUser($query, $request->user())),
            ],
            'company_id' => [
                'required',
                $this->isSuperAdmin($request->user())
                    ? Rule::exists('companies', 'id')
                    : Rule::exists('companies', 'id')->where(fn ($query) => $query->where('id', $request->user()->company_id)),
            ],
            'title' => 'required|string|max:255',
            'contract_number' => 'required|string|max:255|unique:contracts,contract_number,'.$contract->id,
            'status' => 'required|in:active,near_expiry,expired,ended',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'remarks' => 'nullable|string',
            'files.*' => 'nullable|file|max:5120',
        ]);

        $data = $request->only([
            'client_id',
            'company_id',
            'title',
            'contract_number',
            'status',
            'start_date',
            'end_date',
            'remarks',
        ]);
        if (! $this->isSuperAdmin($request->user())) {
            $data['company_id'] = $request->user()->company_id;
        }
        $contract->update($data);

        // Append files
        if ($request->hasFile('files')) {
            $existing = json_decode($contract->file ?? '[]', true);

            foreach ($request->file('files') as $file) {
                $existing[] = $file->store('contracts', 'public');
            }

            $contract->update([
                'file' => json_encode($existing),
            ]);
        }

        return back()->with('success', __('messages.contract_updated_successfully'));
    }

    public function destroy($id)
    {
        $contract = $this->scopeContractsForUser(Contract::query(), auth()->user())->findOrfail($id);
        // Delete stored files
        if ($contract->file) {
            $files = json_decode($contract->file, true);

            if (is_array($files)) {
                foreach ($files as $file) {
                    if (Storage::disk('public')->exists($file)) {
                        Storage::disk('public')->delete($file);
                    }
                }
            }
        }

        // Delete contract record
        $contract->delete();

        return back()->with('delete', __('messages.contract_deleted_successfully'));
    }

    public function print(Contract $contract)
    {
        $contract = $this->scopeContractsForUser(Contract::query(), auth()->user())->findOrFail($contract->id);

        return view('Admin.Backend.Client.contract_print', compact('contract'));
    }

    private function scopeClientsForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        if ($user->branch_id) {
            return $query->where('branch_id', $user->branch_id);
        }

        return $query->where('company_id', $user->company_id);
    }

    private function scopeContractsForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        return $query->where('company_id', $user->company_id);
    }
}
