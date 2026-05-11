<?php

namespace App\Http\Controllers\Subscriptions;

use App\Http\Controllers\Controller;
use App\Models\PlatformRevenue;
use App\Models\PlatformSubscription;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    public function index()
    {
        $subscriptions = PlatformSubscription::all();
        $revenues = PlatformRevenue::with([
            'subscription.platform',
            'subscription.branch',
        ])->latest()->get();

        return view('Admin.Backend.Subscriptions.revenue', compact('subscriptions', 'revenues'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:platform_subscriptions,id',
            'amount_collected' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
        ]);

        $subscription = PlatformSubscription::findOrFail($request->subscription_id);

        $commissionAmount =
            ($request->amount_collected * $subscription->commission_percentage) / 100;

        PlatformRevenue::create([
            'subscription_id' => $subscription->id,
            'amount_collected' => $request->amount_collected,
            'commission_amount' => $commissionAmount,
            'payment_date' => $request->payment_date,
        ]);

        return back()->with('success', __('messages.revenue_added_successfully'));
    }

    public function update(Request $request, $revenue)
    {
        $request->validate([
            'subscription_id' => 'required|exists:platform_subscriptions,id',
            'amount_collected' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
        ]);

        $revenue = PlatformRevenue::findOrFail($revenue);
        $subscription = PlatformSubscription::findOrFail($request->subscription_id);

        $commissionAmount =
            ($request->amount_collected * $subscription->commission_percentage) / 100;

        $revenue->update([
            'subscription_id' => $request->subscription_id,
            'amount_collected' => $request->amount_collected,
            'commission_amount' => $commissionAmount,
            'payment_date' => $request->payment_date,
        ]);

        return redirect()->back()->with('success', __('messages.revenue_updated_successfully'));
    }

    public function destroy($revenue)
    {
        PlatformRevenue::findOrFail($revenue)->delete();

        return redirect()->back()
            ->with('delete', __('messages.revenue_deleted_successfully'));
    }
}
