<?php

namespace App\Http\Controllers\CompanyPartners;

use App\Http\Controllers\Controller;

class InvestmentController extends Controller
{
    public function index()
    {
        return view('Admin.Backend.Investments.index');
    }
}
