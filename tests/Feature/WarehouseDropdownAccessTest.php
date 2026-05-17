<?php

namespace Tests\Feature;

use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class WarehouseDropdownAccessTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    public function test_manage_warehouse_permission_shows_add_warehouse_button(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('manage_warehouse', 'web');

        [$user, $property, $tenant] = $this->createTenantUserWithProperty();
        $user->givePermissionTo('manage_warehouse');
        $this->setTenantAndPropertyContext($tenant, $property);

        $this
            ->actingAs($user)
            ->withSession([
                'current_property_id' => $property->id,
                'branch_id' => $property->branch_id,
            ])
            ->get(route('dashboard.warehouse.index'))
            ->assertOk()
            ->assertSee('data-target="#warehouseModal"', false);
    }

    public function test_company_user_sees_company_main_warehouse_in_inventory_dropdown(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('manage_warehouse', 'web');

        [$user, $property, $tenant] = $this->createTenantUserWithProperty();
        $user->forceFill(['branch_id' => null])->save();
        $user->givePermissionTo('manage_warehouse');

        $mainWarehouse = Warehouse::create([
            'company_id' => $tenant->id,
            'branch_id' => null,
            'name' => 'Company Main Warehouse',
            'type' => 'main',
        ]);

        Warehouse::create([
            'company_id' => $tenant->id,
            'branch_id' => $property->branch_id,
            'name' => 'Branch Warehouse',
            'type' => 'branch',
        ]);

        $this->setTenantAndPropertyContext($tenant, $property);

        $this
            ->actingAs($user)
            ->withSession([
                'current_property_id' => $property->id,
                'branch_id' => $property->branch_id,
            ])
            ->get(route('dashboard.inventory.index'))
            ->assertOk()
            ->assertSee($mainWarehouse->name);
    }
}
