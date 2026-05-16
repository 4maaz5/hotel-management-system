<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::query()->latest()->paginate(15);
        return view('super_admin.plans.index', compact('plans'));
    }

    public function create()
    {
        $plan = new SubscriptionPlan;
        return view('super_admin.plans.create', compact('plan'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePlan($request);

        SubscriptionPlan::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'billing_period' => $validated['billing_period'],
            'features' => $validated['features'] ?? [],
            'limits' => [
                'max_users' => $validated['max_users'] ?? 0,
                'max_properties' => $validated['max_properties'] ?? 0,
            ],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('super-admin.plans.index')
            ->with('success', 'Plan created successfully.');
    }

    public function edit(SubscriptionPlan $plan)
    {
        return view('super_admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $validated = $this->validatePlan($request, $plan);

        $plan->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'billing_period' => $validated['billing_period'],
            'features' => $validated['features'] ?? [],
            'limits' => [
                'max_users' => $validated['max_users'] ?? 0,
                'max_properties' => $validated['max_properties'] ?? 0,
            ],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('super-admin.plans.index')
            ->with('success', 'Plan updated successfully.');
    }

    public function destroy(SubscriptionPlan $plan)
    {
        $plan->delete();
        return redirect()->route('super-admin.plans.index')
            ->with('success', 'Plan deleted successfully.');
    }

    protected function validatePlan(Request $request, ?SubscriptionPlan $plan = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'billing_period' => ['required', 'in:monthly,yearly'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'in:custom_branding'],
            'max_users' => ['nullable', 'integer', 'min:0'],
            'max_properties' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);
    }
}
