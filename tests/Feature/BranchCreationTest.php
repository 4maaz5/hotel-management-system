<?php

namespace Tests\Feature;

use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class BranchCreationTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    public function test_tenant_user_can_create_branch_from_dashboard_form_payload(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('manage_branch', 'web');

        [$user, $property, $tenant] = $this->createTenantUserWithProperty();
        $user->givePermissionTo('manage_branch');

        $response = $this
            ->actingAs($user)
            ->withSession([
                'current_property_id' => $property->id,
                'branch_id' => $property->branch_id,
            ])
            ->postJson(route('dashboard.branch.store'), [
                'branch_name' => 'North Test Branch',
                'branch_address' => 'Riyadh North',
                'branch_manager' => 'Test Manager',
                'branch_email' => 'north-branch@example.test',
                'branch_phone' => '0551234500',
                'branch_status' => 'Active',
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('branches', [
            'company_id' => $tenant->id,
            'name' => 'North Test Branch',
            'location' => 'Riyadh North',
            'manager' => 'Test Manager',
            'email' => 'north-branch@example.test',
            'phone' => '0551234500',
            'status' => 'Active',
            'building_type' => 'owned',
        ]);

        $this->assertSame(2, Branch::where('company_id', $tenant->id)->count());
    }

    public function test_branch_create_dropdown_falls_back_to_company_name(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('manage_branch', 'web');

        [$user, $property, $tenant] = $this->createTenantUserWithProperty();
        $tenant->forceFill(['legal_name' => null])->save();
        $user->givePermissionTo('manage_branch');

        $this
            ->actingAs($user)
            ->withSession([
                'current_property_id' => $property->id,
                'branch_id' => $property->branch_id,
            ])
            ->get(route('dashboard.branch.index'))
            ->assertOk()
            ->assertSee($tenant->name, false);
    }

    public function test_branch_manager_with_assigned_branch_can_see_new_company_branches(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('manage_branch', 'web');

        [$user, $property, $tenant, $branch] = $this->createTenantUserWithProperty(userOverrides: [
            'branch_id' => null,
        ]);
        $user->forceFill(['branch_id' => $branch->id])->save();
        $user->givePermissionTo('manage_branch');

        $this
            ->actingAs($user)
            ->withSession([
                'current_property_id' => $property->id,
                'branch_id' => $property->branch_id,
            ])
            ->postJson(route('dashboard.branch.store'), [
                'branch_name' => 'Visible Company Branch',
                'branch_address' => 'Riyadh East',
                'branch_manager' => 'Visible Manager',
                'branch_email' => 'visible-branch@example.test',
                'branch_phone' => '0551234599',
                'branch_status' => 'Active',
            ])
            ->assertCreated();

        $this
            ->actingAs($user)
            ->withSession([
                'current_property_id' => $property->id,
                'branch_id' => $property->branch_id,
            ])
            ->get(route('dashboard.branch.index'))
            ->assertOk()
            ->assertSee('Visible Company Branch', false);
    }

    public function test_branch_uniqueness_is_scoped_to_tenant_company(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('manage_branch', 'web');

        [$userA, $propertyA] = $this->createTenantUserWithProperty();
        $userA->givePermissionTo('manage_branch');

        [$userB, $propertyB, $tenantB] = $this->createTenantUserWithProperty();
        $userB->givePermissionTo('manage_branch');

        $payload = [
            'branch_name' => 'Shared Branch Name',
            'branch_address' => 'Shared Address',
            'branch_manager' => 'Shared Manager',
            'branch_email' => 'shared-branch@example.test',
            'branch_phone' => '0551112233',
            'branch_status' => 'Active',
        ];

        $this
            ->actingAs($userA)
            ->withSession([
                'current_property_id' => $propertyA->id,
                'branch_id' => $propertyA->branch_id,
            ])
            ->postJson(route('dashboard.branch.store'), $payload)
            ->assertCreated();

        $this
            ->actingAs($userB)
            ->withSession([
                'current_property_id' => $propertyB->id,
                'branch_id' => $propertyB->branch_id,
            ])
            ->postJson(route('dashboard.branch.store'), $payload)
            ->assertCreated();

        $this->assertDatabaseHas('branches', [
            'company_id' => $tenantB->id,
            'name' => 'Shared Branch Name',
            'email' => 'shared-branch@example.test',
            'phone' => '0551112233',
        ]);
    }

    public function test_duplicate_branch_identity_is_rejected_inside_same_tenant(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('manage_branch', 'web');

        [$user, $property] = $this->createTenantUserWithProperty();
        $user->givePermissionTo('manage_branch');

        $payload = [
            'branch_name' => 'Tenant Unique Branch',
            'branch_address' => 'Riyadh',
            'branch_manager' => 'Tenant Manager',
            'branch_email' => 'tenant-unique@example.test',
            'branch_phone' => '0552223344',
            'branch_status' => 'Active',
        ];

        $session = [
            'current_property_id' => $property->id,
            'branch_id' => $property->branch_id,
        ];

        $this
            ->actingAs($user)
            ->withSession($session)
            ->postJson(route('dashboard.branch.store'), $payload)
            ->assertCreated();

        $this
            ->actingAs($user)
            ->withSession($session)
            ->postJson(route('dashboard.branch.store'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['branch_name', 'branch_email', 'branch_phone']);
    }
}
