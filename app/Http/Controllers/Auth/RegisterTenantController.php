<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RegisterTenantController extends Controller
{
    public function showRegistrationForm()
    {
        $plans = SubscriptionPlan::active()->get();
        return view('auth.register-tenant', compact('plans'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'subdomain' => ['required', 'string', 'max:63', 'alpha_dash', 'unique:companies,subdomain'],
            'plan_id' => ['required', 'integer', 'exists:subscription_plans,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);

        $tenant = DB::transaction(function () use ($validated, $plan) {
            $tenant = Tenant::create([
                'name' => $validated['company_name'],
                'subdomain' => $validated['subdomain'],
                'email' => $validated['email'],
                'start_date' => now(),
                'end_date' => now()->addDays(14),
                'subscription_status' => 'active',
                'subscription_plan_id' => $plan->id,
            ]);

            $owner = User::create([
                'company_id' => $tenant->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'status' => 'active',
                'user_type' => 'owner',
                'default_language' => 'en',
                'profile_data' => ['first_name_en' => $validated['name']],
                'contact_info' => ['email' => $validated['email']],
                'properties' => [],
            ]);

            $ownerRole = Role::firstOrCreate(
                ['name' => 'owner', 'guard_name' => 'web'],
                ['description' => 'Tenant owner', 'status' => 'ACTIVE']
            );
            $owner->assignRole($ownerRole);

            return $tenant;
        });

        $tenantUrl = $this->tenantUrl($tenant);

        return redirect($tenantUrl)->with('success', 'Account created! Please log in to your dashboard.');
    }

    protected function tenantUrl(Tenant $tenant): string
    {
        $url = config('app.url');
        $parsed = parse_url($url);
        $scheme = $parsed['scheme'] ?? 'http';
        $host = $parsed['host'] ?? 'localhost';
        $port = isset($parsed['port']) ? ':'.$parsed['port'] : '';

        return "{$scheme}://{$tenant->subdomain}.{$host}{$port}/login";
    }
}
