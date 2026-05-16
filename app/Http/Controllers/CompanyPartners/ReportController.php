<?php

namespace App\Http\Controllers\CompanyPartners;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyPartner;

class ReportController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $user = auth()->user();
        $companies = $user->isSuperAdmin() ? Company::all() : Company::whereKey($user->company_id)->get();
        $partners = $this->scopePartnersForUser(CompanyPartner::with(['company', 'documents']), $user)->get();

        return view('Admin.Backend.Partners.report-view', compact('companies', 'partners'));
    }

    public function report($id)
    {
        $partner = $this->scopePartnersForUser(CompanyPartner::with('company', 'documents'), auth()->user())->findOrFail($id);

        return view('Admin.Backend.Partners.report', compact('partner'));
    }

    public function reportPdf($id)
    {
        $partner = $this->scopePartnersForUser(CompanyPartner::with('company', 'documents'), auth()->user())->findOrFail($id);

        // Return the view normally
        return view('Admin.Backend.Partners.download', compact('partner'));
    }

    private function scopePartnersForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        return $query->where('company_id', $user->company_id);
    }
}
