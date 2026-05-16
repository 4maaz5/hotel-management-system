<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\MarketingAgent;
use App\Models\MarketingQuotation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuotationController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $user = auth()->user();
        $marketingAgents = $this->scopeMarketingAgentsForUser(MarketingAgent::query(), $user)->get();
        $branches = $this->scopeBranchesForUser(Branch::query(), $user)->get();
        $quotations = $this->scopeMarketingQuotationsForUser(MarketingQuotation::with(['agent', 'branch']), $user)->get();
        $nextQuotationNumber = 'Q'.str_pad(
            (MarketingQuotation::max('id') ?? 0) + 1,
            6,
            '0',
            STR_PAD_LEFT
        );

        return view('Admin.Backend.Marketing.quotation', compact('branches', 'marketingAgents', 'quotations', 'nextQuotationNumber'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // Validate input
        $validated = $request->validate([
            'marketing_agent_id' => ['nullable', Rule::exists('marketing_agents', 'id')->where(fn ($query) => $this->scopeMarketingAgentsForUser($query, $user))],
            'manual_agent_name' => 'nullable|string|max:255',
            'branch_id' => ['required', Rule::exists('branches', 'id')->where(fn ($query) => $this->scopeBranchesForUser($query, $user))],
            'client_name' => 'required|string|max:255',
            'client_contact' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'quotation_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,approved,rejected',
            'account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'cr_no' => 'nullable|string|max:255',
            'vat_no' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $validated['company_id'] = $this->companyIdForBranch((int) $validated['branch_id']);

        // Auto-generate quotation number
        $validated['quotation_number'] = 'Q'.str_pad(\App\Models\MarketingQuotation::max('id') + 1, 6, '0', STR_PAD_LEFT);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('quotation_logos', 'public');
        }

        // Set approved_at if approved
        $validated['approved_at'] = $validated['status'] === 'approved' ? now() : null;

        // Create the quotation
        $quotation = \App\Models\MarketingQuotation::create($validated);

        // Calculate commission if approved and agent exists
        if ($quotation->status === 'approved' && $quotation->marketing_agent_id) {
            $agent = $quotation->agent;
            if ($agent) {
                \App\Models\MarketingCommission::create([
                    'marketing_agent_id' => $agent->id,
                    'branch_id' => $quotation->branch_id,
                    'quotation_id' => $quotation->id,
                    'commission_percentage' => $agent->commission_percent,
                    'commission_amount' => ($quotation->quotation_amount * $agent->commission_percent / 100),
                    'paid_status' => 'pending',
                ]);
            }
        }

        return redirect()->back()->with('success', __('messages.marketing_quotation_added_successfully'));
    }

    public function update(Request $request, MarketingQuotation $marketingQuotation)
    {
        $user = auth()->user();
        $marketingQuotation = $this->scopeMarketingQuotationsForUser(MarketingQuotation::query(), $user)->findOrFail($marketingQuotation->id);

        $validated = $request->validate([
            'marketing_agent_id' => ['nullable', Rule::exists('marketing_agents', 'id')->where(fn ($query) => $this->scopeMarketingAgentsForUser($query, $user))],
            'manual_agent_name' => 'nullable|string|max:255',
            'branch_id' => ['required', Rule::exists('branches', 'id')->where(fn ($query) => $this->scopeBranchesForUser($query, $user))],
            'client_name' => 'required|string|max:255',
            'client_contact' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'quotation_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,approved,rejected',
            'account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'cr_no' => 'nullable|string|max:255',
            'vat_no' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $validated['company_id'] = $this->companyIdForBranch((int) $validated['branch_id']);

        // Handle logo upload if present
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('marketing_logos', 'public');
        }

        // Handle approved_at logic
        if ($validated['status'] === 'approved' && $marketingQuotation->approved_at === null) {
            $validated['approved_at'] = now();
        }

        if ($validated['status'] !== 'approved') {
            $validated['approved_at'] = null;
        }

        // Update the quotation
        $marketingQuotation->update($validated);

        // Handle commission if status is approved and agent exists
        if ($marketingQuotation->status === 'approved' && $marketingQuotation->marketing_agent_id) {
            $agent = $marketingQuotation->agent;

            // Check if commission already exists
            $commission = \App\Models\MarketingCommission::firstOrNew([
                'quotation_id' => $marketingQuotation->id,
            ]);

            $commission->marketing_agent_id = $agent->id;
            $commission->branch_id = $marketingQuotation->branch_id;
            $commission->commission_percentage = $agent->commission_percent;
            $commission->commission_amount = ($marketingQuotation->quotation_amount * $agent->commission_percent / 100);
            $commission->paid_status = $commission->paid_status ?? 'pending';
            $commission->save();
        }

        // Redirect back with success message
        return redirect()->back()->with('success', __('messages.marketing_quotation_updated_successfully'));
    }

    public function destroy(MarketingQuotation $marketingQuotation)
    {
        $marketingQuotation = $this->scopeMarketingQuotationsForUser(MarketingQuotation::query(), auth()->user())->findOrFail($marketingQuotation->id);

        // Delete logo from storage
        if ($marketingQuotation->logo && \Storage::disk('public')->exists($marketingQuotation->logo)) {
            \Storage::disk('public')->delete($marketingQuotation->logo);
        }

        // Delete the quotation record
        $marketingQuotation->delete();

        return redirect()->back()->with('delete', __('messages.marketing_quotation_deleted_successfully'));
    }

    public function print(MarketingQuotation $quotation)
    {
        $quotation = $this->scopeMarketingQuotationsForUser(MarketingQuotation::query(), auth()->user())->findOrFail($quotation->id);

        return view('Admin.Backend.Marketing.quotation_print', compact('quotation'));
    }

    protected function scopeMarketingAgentsForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        $query->where('company_id', $this->companyIdForUser($user));

        if ($user->branch_id) {
            $query->where(function ($branchQuery) use ($user) {
                $branchQuery->whereNull('branch_id')
                    ->orWhere('branch_id', $user->branch_id);
            });
        }

        return $query;
    }

    protected function scopeMarketingQuotationsForUser($query, $user)
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

    protected function companyIdForBranch(int $branchId): ?int
    {
        return Branch::whereKey($branchId)->value('company_id');
    }
}
