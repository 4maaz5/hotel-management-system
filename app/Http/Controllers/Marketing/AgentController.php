<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Company;
use App\Models\MarketingAgent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AgentController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $user = auth()->user();
        $companies = $this->isSuperAdmin($user) ? Company::all() : Company::whereKey($this->companyIdForUser($user))->get();
        $brands = $this->scopeBrandsForUser(Brand::query(), $user)->get();
        $branches = $this->scopeBranchesForUser(Branch::query(), $user)->get();
        $agents = $this->scopeMarketingAgentsForUser(MarketingAgent::query(), $user)->get();

        return view('Admin.Backend.Marketing.index', compact('companies', 'brands', 'branches', 'agents'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $companyId = $this->inputCompanyId($request, $user);

        //  Validate request data
        $validated = $request->validate([
            'company_id' => [$this->isSuperAdmin($user) ? 'required' : 'nullable', Rule::exists('companies', 'id')],
            'brand_id' => ['nullable', Rule::exists('brands', 'id')->where(fn ($query) => $query->where('company_id', $companyId))],
            'branch_id' => ['nullable', Rule::exists('branches', 'id')->where(fn ($query) => $this->scopeBranchesForUser($query, $user))],
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            'type' => 'required|in:agent,company',
            'commission_percent' => 'required|numeric|min:0|max:100',
        ]);
        $validated['company_id'] = $companyId;

        //  Create new Marketing Agent
        MarketingAgent::create($validated);

        //  Redirect back with success message
        return redirect()->back()->with('success', __('messages.marketing_agent_added_successfully'));
    }

    public function update(Request $request, MarketingAgent $marketingAgent)
    {
        $user = auth()->user();
        $marketingAgent = $this->scopeMarketingAgentsForUser(MarketingAgent::query(), $user)->findOrFail($marketingAgent->id);
        $companyId = $this->inputCompanyId($request, $user);

        //  Validate input
        $validated = $request->validate([
            'company_id' => [$this->isSuperAdmin($user) ? 'required' : 'nullable', Rule::exists('companies', 'id')],
            'brand_id' => ['nullable', Rule::exists('brands', 'id')->where(fn ($query) => $query->where('company_id', $companyId))],
            'branch_id' => ['nullable', Rule::exists('branches', 'id')->where(fn ($query) => $this->scopeBranchesForUser($query, $user))],
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            'type' => 'required|in:agent,company',
            'commission_percent' => 'required|numeric|min:0|max:100',
        ]);
        $validated['company_id'] = $companyId;

        //  Update agent
        $marketingAgent->update($validated);

        //  Redirect back with success message
        return redirect()->back()->with('success', __('messages.marketing_agent_updated_successfully'));
    }

    public function destroy(MarketingAgent $marketingAgent)
    {
        $marketingAgent = $this->scopeMarketingAgentsForUser(MarketingAgent::query(), auth()->user())->findOrFail($marketingAgent->id);
        $marketingAgent->delete();

        return redirect()->back()->with('delete', __('messages.marketing_agent_deleted_successfully'));
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

    protected function scopeBrandsForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        return $query->where('company_id', $this->companyIdForUser($user));
    }

    protected function inputCompanyId(Request $request, $user): ?int
    {
        return $this->isSuperAdmin($user)
            ? $request->integer('company_id')
            : $this->companyIdForUser($user);
    }

    protected function companyIdForUser($user): ?int
    {
        return $user?->company_id ?: $user?->branch?->company_id;
    }
}
