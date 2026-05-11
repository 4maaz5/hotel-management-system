<?php

namespace App\Http\Controllers\Financials;

use App\Http\Controllers\Controller;
use App\Models\DiscountType;
use Illuminate\Http\Request;

class DiscountTypeController extends Controller
{
    public function index()
    {
        $discountTypes = DiscountType::latest()->get();

        return view('admin.discount.index', compact('discountTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'report_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        DiscountType::create([
            'type' => $request->type,
            'report_name' => $request->report_name,
            'description' => $request->description,
        ]);

        return redirect()
            ->back()
            ->with('success', __('messages.discount_type_added_successfully'));
    }

    public function update(Request $request, DiscountType $discount)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'report_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $discount->update([
            'type' => $request->type,
            'report_name' => $request->report_name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', __('messages.discount_type_updated_successfully'));
    }

    public function statusToggle(DiscountType $discount)
    {
        $discount->update([
            'is_active' => ! $discount->is_active,
        ]);

        return redirect()->back()
            ->with('success', __('messages.status_updated_successfully'));
    }
}
