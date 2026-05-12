<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Company;
use App\Models\MarketingAgent;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $companies = $user->isSuperAdmin() ? Company::all() : Company::whereKey($user->company_id)->get();
        $brands = Brand::all();
        $branches = Branch::all();
        $agents = MarketingAgent::all();

        return view('Admin.Backend.Marketing.index', compact('companies', 'brands', 'branches', 'agents'));
    }

    public function store(Request $request)
    {
        //  Validate request data
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'brand_id' => 'nullable|exists:brands,id',
            'branch_id' => 'nullable|exists:branches,id',
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            'type' => 'required|in:agent,company',
            'commission_percent' => 'required|numeric|min:0|max:100',
        ]);

        //  Create new Marketing Agent
        MarketingAgent::create($validated);

        //  Redirect back with success message
        return redirect()->back()->with('success', __('messages.marketing_agent_added_successfully'));
    }

    public function update(Request $request, MarketingAgent $marketingAgent)
    {
        //  Validate input
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'brand_id' => 'nullable|exists:brands,id',
            'branch_id' => 'nullable|exists:branches,id',
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            'type' => 'required|in:agent,company',
            'commission_percent' => 'required|numeric|min:0|max:100',
        ]);

        //  Update agent
        $marketingAgent->update($validated);

        //  Redirect back with success message
        return redirect()->back()->with('success', __('messages.marketing_agent_updated_successfully'));
    }

    public function destroy(MarketingAgent $marketingAgent)
    {
        $marketingAgent->delete();

        return redirect()->back()->with('delete', __('messages.marketing_agent_deleted_successfully'));
    }
}
