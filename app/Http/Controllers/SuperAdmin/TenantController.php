<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::query()
            ->with('plan')
            ->withCount(['users', 'properties'])
            ->latest()
            ->paginate(15);

        return view('super_admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        $plans = SubscriptionPlan::active()->get();
        return view('super_admin.tenants.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateTenant($request);

        $tenant = DB::transaction(function () use ($validated) {
            $tenant = Tenant::create([
                'name' => $validated['name'],
                'subdomain' => $validated['subdomain'],
                'email' => $validated['email'] ?? $validated['owner_email'],
                'phone' => $validated['phone'] ?? null,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'subscription_status' => $validated['status'],
                'subscription_plan_id' => $validated['subscription_plan_id'],
            ]);

            $owner = User::create([
                'company_id' => $tenant->id,
                'name' => $validated['owner_name'],
                'email' => $validated['owner_email'],
                'password' => Hash::make($validated['owner_password']),
                'status' => 'active',
                'user_type' => 'owner',
                'default_language' => 'en',
                'profile_data' => [
                    'first_name_en' => $validated['owner_name'],
                ],
                'contact_info' => [
                    'email' => $validated['owner_email'],
                    'mobile_number' => $validated['phone'] ?? null,
                ],
                'properties' => [],
            ]);

            $owner->assignRole($this->ensureTenantRoles()['owner']);

            return $tenant;
        });

        return redirect()
            ->route('super-admin.tenants.show', $tenant)
            ->with('success', 'Tenant and owner user created successfully.');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['plan', 'users', 'properties']);
        $owner = $this->ownerForTenant($tenant);

        return view('super_admin.tenants.show', compact('tenant', 'owner'));
    }

    public function edit(Tenant $tenant)
    {
        $plans = SubscriptionPlan::active()->get();
        $owner = $this->ownerForTenant($tenant);

        return view('super_admin.tenants.edit', compact('tenant', 'owner', 'plans'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $owner = $this->ownerForTenant($tenant);
        $validated = $this->validateTenant($request, $tenant, $owner?->id);

        DB::transaction(function () use ($tenant, $owner, $validated) {
            $tenant->update([
                'name' => $validated['name'],
                'subdomain' => $validated['subdomain'],
                'email' => $validated['email'] ?? $validated['owner_email'],
                'phone' => $validated['phone'] ?? null,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'subscription_status' => $validated['status'],
                'subscription_plan_id' => $validated['subscription_plan_id'],
            ]);

            $ownerUser = $owner ?: new User([
                'company_id' => $tenant->id,
                'status' => 'active',
                'user_type' => 'owner',
                'default_language' => 'en',
                'properties' => [],
            ]);

            $ownerUser->fill([
                'company_id' => $tenant->id,
                'name' => $validated['owner_name'],
                'email' => $validated['owner_email'],
                'status' => 'active',
                'user_type' => 'owner',
                'default_language' => 'en',
                'profile_data' => array_merge($ownerUser->profile_data ?? [], [
                    'first_name_en' => $validated['owner_name'],
                ]),
                'contact_info' => array_merge($ownerUser->contact_info ?? [], [
                    'email' => $validated['owner_email'],
                    'mobile_number' => $validated['phone'] ?? null,
                ]),
            ]);

            if (! empty($validated['owner_password'])) {
                $ownerUser->password = Hash::make($validated['owner_password']);
            }

            $ownerUser->save();
            $ownerUser->syncRoles([$this->ensureTenantRoles()['owner']]);
        });

        return redirect()
            ->route('super-admin.tenants.show', $tenant)
            ->with('success', 'Tenant updated successfully.');
    }

    protected function validateTenant(Request $request, ?Tenant $tenant = null, ?int $ownerUserId = null): array
    {
        $tenantId = $tenant?->id ?? 'NULL';

        $passwordRules = $ownerUserId
            ? ['nullable', 'string', 'min:8', 'confirmed']
            : ['required', 'string', 'min:8', 'confirmed'];

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subdomain' => ['required', 'string', 'max:63', 'alpha_dash', "unique:companies,subdomain,{$tenantId},id"],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:active,inactive,suspended'],
            'subscription_plan_id' => ['nullable', 'integer', 'exists:subscription_plans,id'],
            'owner_name' => ['required', 'string', 'max:255'],
            'owner_email' => ['required', 'email', 'max:255', 'unique:users,email,'.($ownerUserId ?? 'NULL').',id'],
            'owner_password' => $passwordRules,
        ]);
    }

    protected function ownerForTenant(Tenant $tenant): ?User
    {
        return $tenant->users()
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['owner']);
            })
            ->first();
    }

    protected function ensureTenantRoles(): array
    {
        $owner = Role::firstOrCreate(
            ['company_id' => null, 'name' => 'owner', 'guard_name' => 'web'],
            ['description' => 'Tenant owner', 'status' => 'ACTIVE']
        );
        $manager = Role::firstOrCreate(
            ['company_id' => null, 'name' => 'manager', 'guard_name' => 'web'],
            ['description' => 'Property manager', 'status' => 'ACTIVE']
        );
        $receptionist = Role::firstOrCreate(
            ['company_id' => null, 'name' => 'receptionist', 'guard_name' => 'web'],
            ['description' => 'Front desk operator', 'status' => 'ACTIVE']
        );
        $housekeeping = Role::firstOrCreate(
            ['company_id' => null, 'name' => 'housekeeping', 'guard_name' => 'web'],
            ['description' => 'Housekeeping operator', 'status' => 'ACTIVE']
        );

        $allPermissions = Permission::all();
        $owner->syncPermissions($allPermissions);
        $manager->syncPermissions($allPermissions);
        $receptionist->syncPermissions(Permission::query()->whereIn('name', [
            'dashboard.view',
            'guest.add',
            'guest.edit',
            'guest.view',
            'corporate.add',
            'corporate.edit',
            'corporate.view',
            'reservation.add',
            'reservation.edit',
            'reservation.cancel',
            'reservation.contract',
            'invoice.view',
            'invoice.print',
            'receipt.add',
            'receipt.edit',
            'receipt.print',
            'payment.add',
            'payment.edit',
            'payment.print',
        ])->get());
        $housekeeping->syncPermissions(Permission::query()->whereIn('name', [
            'dashboard.view',
            'housekeeper_list.add',
            'housekeeper_list.edit',
            'housekeeper_list.view',
            'housekeeping_task.add',
            'housekeeping_task.edit',
            'housekeeping_task.delete',
        ])->get());

        return compact('owner', 'manager', 'receptionist', 'housekeeping');
    }
}
