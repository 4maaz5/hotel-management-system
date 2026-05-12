<?php

namespace App\Http\Controllers\CompanyPartners;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyPartner;

class ReportController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $companies = $user->isSuperAdmin() ? Company::all() : Company::whereKey($user->company_id)->get();
        $partners = CompanyPartner::with(['company', 'documents'])->get();

        return view('Admin.Backend.Partners.report-view', compact('companies', 'partners'));
    }

    public function report($id)
    {
        $partner = CompanyPartner::with('company', 'documents')->findOrFail($id);

        return view('Admin.Backend.Partners.report', compact('partner'));
    }

    public function reportPdf($id)
    {
        $partner = CompanyPartner::with('company', 'documents')->findOrFail($id);

        // Return the view normally
        return view('Admin.Backend.Partners.download', compact('partner'));
    }
}
