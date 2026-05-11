<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\MarketingCommission;

class CommissionController extends Controller
{
    public function index()
    {
        $commissions = MarketingCommission::with([
            'agent',
            'quotation',
            'branch',
        ])->latest()->get();

        return view('Admin.Backend.Marketing.commission', compact('commissions'));
    }
}
