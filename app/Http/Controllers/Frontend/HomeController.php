<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;

class HomeController extends Controller
{
    public function index()
    {
        $company = Company::where('vat_number', '310459947700003')->first();

        if ($company) {
            $branches = Branch::where('company_id', $company->id)->get();
        } else {
            $branches = collect();
        }

        return view('User.Frontend.layout.home', compact('branches'));
    }
}
