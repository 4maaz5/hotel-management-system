<?php

namespace App\Http\Controllers\Subscriptions;

use App\Http\Controllers\Concerns\ScopesTenantAccess;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\PlatformSubscription;
use App\Models\ThirdPartyPlatform;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{
    use ScopesTenantAccess;

    public function index()
    {
        $user = auth()->user();

        $platforms = ThirdPartyPlatform::all();
        $branches = $this->scopeBranchesForUser(Branch::query(), $user)->get();
        $subscriptions = $this->scopeSubscriptionsForUser(PlatformSubscription::with(['platform', 'branch']), $user)
            ->latest()
            ->get();

        return view('Admin.Backend.Subscriptions.subscription', compact('platforms', 'branches', 'subscriptions'));
    }

    public function store(Request $request)
    {
        //  Validate the request
        $validated = $request->validate([
            'third_party_platform_id' => 'required|exists:third_party_platforms,id',
            'branch_id' => [
                'nullable',
                Rule::exists('branches', 'id')->where(fn ($query) => $this->scopeBranchesForUser($query, $request->user())),
            ],
            'subscription_start_date' => 'required|date',
            'subscription_end_date' => 'required|date|after_or_equal:subscription_start_date',
            'contract_amount' => 'required|numeric|min:0',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,expired,pending',
            'notes' => 'nullable|string',
        ]);

        $companyId = $this->companyIdForSubscription($validated['branch_id'] ?? null, $request->user());

        //  Create subscription
        PlatformSubscription::create([
            'third_party_platform_id' => $validated['third_party_platform_id'],
            'company_id' => $companyId,
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
        $subscription = $this->scopeSubscriptionsForUser(PlatformSubscription::query(), $request->user())
            ->findOrFail($subscription->id);

        //  Validate incoming request
        $validated = $request->validate([
            'third_party_platform_id' => 'required|exists:third_party_platforms,id',
            'branch_id' => [
                'nullable',
                Rule::exists('branches', 'id')->where(fn ($query) => $this->scopeBranchesForUser($query, $request->user())),
            ],
            'subscription_start_date' => 'required|date',
            'subscription_end_date' => 'required|date|after_or_equal:subscription_start_date',
            'contract_amount' => 'required|numeric|min:0',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,expired,pending',
            'notes' => 'nullable|string',
        ]);

        $companyId = $this->companyIdForSubscription($validated['branch_id'] ?? null, $request->user());

        //  Update subscription
        $subscription->update([
            'third_party_platform_id' => $validated['third_party_platform_id'],
            'company_id' => $companyId,
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
        $subscription = $this->scopeSubscriptionsForUser(PlatformSubscription::query(), auth()->user())
            ->findOrFail($subscription->id);

        $subscription->delete();

        return redirect()->back()->with('delete', __('messages.subscription_deleted_successfully'));
    }

    public function filter(Request $request)
    {
        $query = $this->scopeSubscriptionsForUser(
            PlatformSubscription::with(['platform', 'branch']),
            $request->user()
        )->latest();

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

    private function companyIdForSubscription(?int $branchId, $user): ?int
    {
        if ($branchId) {
            return Branch::whereKey($branchId)->value('company_id');
        }

        return $this->isSuperAdmin($user) ? null : $user->company_id;
    }
}
