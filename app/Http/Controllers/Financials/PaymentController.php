<?php

namespace App\Http\Controllers\Financials;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodConfig;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::all();
        $payments = PaymentMethodConfig::all();

        return view('admin.payments.index', compact('paymentMethods', 'payments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'description' => 'nullable|string|max:1000',
        ]);

        PaymentMethodConfig::updateOrCreate(
            [
                'payment_method_id' => $validated['payment_method_id'],
            ],
            [
                'description' => $validated['description'] ?? null,
            ]
        );

        return redirect()
            ->back()
            ->with('success', __('messages.payment_method_added_successfully'));
    }

    public function update(Request $request, $id)
    {
        $paymentConfig = PaymentMethodConfig::findOrFail($id);

        $validated = $request->validate([
            'description' => 'nullable|string|max:1000',
        ]);

        $isActive = $request->has('is_active') ? 1 : 0;

        $paymentConfig->update([
            'description' => $validated['description'] ?? null,
            'is_active' => $isActive,
        ]);

        return redirect()
            ->back()
            ->with('success', __('messages.payment_method_updated_successfully'));
    }
}
