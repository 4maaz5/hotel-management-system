<?php

namespace Tests\Feature;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class TenantScopedRolesTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_hr_role_index_only_shows_current_tenant_roles(): void
    {
        Permission::findOrCreate('manage_setting', 'web');

        [$userA, $propertyA, $tenantA] = $this->createTenantUserWithProperty();
        $userA->givePermissionTo('manage_setting');

        [$userB, $propertyB, $tenantB] = $this->createTenantUserWithProperty();

        Role::create(['company_id' => $tenantA->id, 'name' => 'Tenant A HR Role', 'guard_name' => 'web']);
        Role::create(['company_id' => $tenantB->id, 'name' => 'Tenant B HR Role', 'guard_name' => 'web']);

        $this->setTenantAndPropertyContext($tenantA, $propertyA);

        $this
            ->actingAs($userA)
            ->withSession([
                'current_property_id' => $propertyA->id,
                'branch_id' => $propertyA->branch_id,
            ])
            ->get(route('dashboard.setting.role.index'))
            ->assertOk()
            ->assertSee('Tenant A HR Role')
            ->assertDontSee('Tenant B HR Role');
    }

    public function test_reservation_role_index_only_shows_current_tenant_roles(): void
    {
        Permission::findOrCreate('role.view', 'web');

        [$userA, $propertyA, $tenantA] = $this->createTenantUserWithProperty();
        $userA->givePermissionTo('role.view');

        [$userB, $propertyB, $tenantB] = $this->createTenantUserWithProperty();

        Role::create(['company_id' => $tenantA->id, 'name' => 'Tenant A Reservation Role', 'guard_name' => 'web']);
        Role::create(['company_id' => $tenantB->id, 'name' => 'Tenant B Reservation Role', 'guard_name' => 'web']);

        $this->setTenantAndPropertyContext($tenantA, $propertyA);

        $this
            ->actingAs($userA)
            ->withSession([
                'current_property_id' => $propertyA->id,
                'branch_id' => $propertyA->branch_id,
            ])
            ->get(route('setup-sidebar.property-role.index'))
            ->assertOk()
            ->assertSee('Tenant A Reservation Role')
            ->assertDontSee('Tenant B Reservation Role');
    }

    public function test_same_role_name_can_exist_in_different_tenants(): void
    {
        [$userA, $propertyA, $tenantA] = $this->createTenantUserWithProperty();
        [$userB, $propertyB, $tenantB] = $this->createTenantUserWithProperty();

        Role::create(['company_id' => $tenantA->id, 'name' => 'Branch Supervisor', 'guard_name' => 'web']);
        Role::create(['company_id' => $tenantB->id, 'name' => 'Branch Supervisor', 'guard_name' => 'web']);

        $this->assertSame(1, Role::where('company_id', $tenantA->id)->where('name', 'Branch Supervisor')->count());
        $this->assertSame(1, Role::where('company_id', $tenantB->id)->where('name', 'Branch Supervisor')->count());
    }
}
