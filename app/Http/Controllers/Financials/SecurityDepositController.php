<?php

namespace App\Http\Controllers\Financials;

use App\Http\Controllers\Controller;
use App\Models\SecurityDeposit;
use App\Models\UnitTypeCustomization;
use Illuminate\Http\Request;

class SecurityDepositController extends Controller
{
    public function index(Request $request)
    {
        $query = UnitTypeCustomization::query();

        if ($request->filled('unit_type')) {
            $query->where('name', 'like', '%'.$request->unit_type.'%');
        }

        $unitTypes = $query->get();

        $deposits = SecurityDeposit::whereIn('unit_type_id', $unitTypes->pluck('id'))
            ->pluck('deposit_amount', 'unit_type_id')
            ->toArray();

        return view('admin.security_deposits.index', compact('unitTypes', 'deposits'));
    }

    public function updateDeposits(Request $request)
    {
        $deposits = $request->input('deposits', []);

        foreach ($deposits as $unitTypeId => $amount) {
            SecurityDeposit::updateOrCreate(
                ['unit_type_id' => $unitTypeId],
                ['deposit_amount' => $amount]
            );
        }

        return redirect()->back()->with('success', __('messages.security_deposits_updated_successfully'));
    }
}
