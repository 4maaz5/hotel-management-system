<?php

namespace App\Http\Controllers\Subscriptions;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\PlatformRevenue;
use App\Models\PlatformSubscription;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RevenueController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $subscriptions = $this->scopeSubscriptionsForUser(PlatformSubscription::query(), auth()->user())->get();
        $revenues = $this->scopeRevenuesForUser(PlatformRevenue::with([
            'subscription.platform',
            'subscription.branch',
        ]), auth()->user())->latest()->get();

        return view('Admin.Backend.Subscriptions.revenue', compact('subscriptions', 'revenues'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subscription_id' => [
                'required',
                Rule::exists('platform_subscriptions', 'id')->where(fn ($query) => $this->scopeSubscriptionsForUser($query, $request->user())),
            ],
            'amount_collected' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
        ]);

        $subscription = $this->scopeSubscriptionsForUser(PlatformSubscription::query(), $request->user())
            ->findOrFail($request->subscription_id);

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
            'subscription_id' => [
                'required',
                Rule::exists('platform_subscriptions', 'id')->where(fn ($query) => $this->scopeSubscriptionsForUser($query, $request->user())),
            ],
            'amount_collected' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
        ]);

        $revenue = $this->scopeRevenuesForUser(PlatformRevenue::query(), $request->user())->findOrFail($revenue);
        $subscription = $this->scopeSubscriptionsForUser(PlatformSubscription::query(), $request->user())
            ->findOrFail($request->subscription_id);

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
        $this->scopeRevenuesForUser(PlatformRevenue::query(), auth()->user())->findOrFail($revenue)->delete();

        return redirect()->back()
            ->with('delete', __('messages.revenue_deleted_successfully'));
    }

    private function scopeSubscriptionsForUser($query, $user)
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        if ($user->branch_id) {
            return $query->where('branch_id', $user->branch_id);
        }

        return $query->where('company_id', $user->company_id);
    }

    private function scopeRevenuesForUser($query, $user)
    {
        return $query->whereHas('subscription', fn ($subscriptionQuery) => $this->scopeSubscriptionsForUser($subscriptionQuery, $user));
    }
}
