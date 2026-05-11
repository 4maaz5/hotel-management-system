<?php

namespace App\Http\Controllers\Subscriptions;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\PlatformSubscription;
use App\Models\ThirdPartyPlatform;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $platforms = ThirdPartyPlatform::all();
        $branches = Branch::all();
        $subscriptions = PlatformSubscription::with(['platform', 'branch'])->latest()->get();

        return view('Admin.Backend.Subscriptions.subscription', compact('platforms', 'branches', 'subscriptions'));
    }

    public function store(Request $request)
    {
        //  Validate the request
        $validated = $request->validate([
            'third_party_platform_id' => 'required|exists:third_party_platforms,id',
            'branch_id' => 'nullable|exists:branches,id',
            'subscription_start_date' => 'required|date',
            'subscription_end_date' => 'required|date|after_or_equal:subscription_start_date',
            'contract_amount' => 'required|numeric|min:0',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,expired,pending',
            'notes' => 'nullable|string',
        ]);

        //  Create subscription
        PlatformSubscription::create([
            'third_party_platform_id' => $validated['third_party_platform_id'],
            'branch_id' => $validated['branch_id'] ?? null,
            'subscription_start_date' => $validated['subscription_start_date'],
            'subscription_end_date' => $validated['subscription_end_date'],
            'contract_amount' => $validated['contract_amount'],
            'commission_percentage' => $validated['commission_percentage'] ?? 0,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        //  Redirect back with success message
        return redirect()
            ->back()
            ->with('success', __('messages.subscription_added_successfully'));
    }

    public function update(Request $request, PlatformSubscription $subscription)
    {
        //  Validate incoming request
        $validated = $request->validate([
            'third_party_platform_id' => 'required|exists:third_party_platforms,id',
            'branch_id' => 'nullable|exists:branches,id',
            'subscription_start_date' => 'required|date',
            'subscription_end_date' => 'required|date|after_or_equal:subscription_start_date',
            'contract_amount' => 'required|numeric|min:0',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,expired,pending',
            'notes' => 'nullable|string',
        ]);

        //  Update subscription
        $subscription->update([
            'third_party_platform_id' => $validated['third_party_platform_id'],
            'branch_id' => $validated['branch_id'] ?? null,
            'subscription_start_date' => $validated['subscription_start_date'],
            'subscription_end_date' => $validated['subscription_end_date'],
            'contract_amount' => $validated['contract_amount'],
            'commission_percentage' => $validated['commission_percentage'] ?? 0,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        //  Redirect back with success message
        return redirect()->back()->with('success', __('messages.subscription_updated_successfully'));
    }

    public function destroy(PlatformSubscription $subscription)
    {
        $subscription->delete();

        return redirect()->back()->with('delete', __('messages.subscription_deleted_successfully'));
    }

    public function filter(Request $request)
    {
        $query = PlatformSubscription::with(['platform', 'branch'])->latest();

        if ($request->has('status')) {
            $status = $request->get('status');

            if ($status === 'expired') {
                $query->where('subscription_end_date', '<', now());
            } elseif ($status === 'active') {
                $query->where('subscription_end_date', '>=', now());
            }
        }

        $subscriptions = $query->get();

        // Return JSON data
        return response()->json($subscriptions);
    }
}
