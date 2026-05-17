<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class HousekeepingStatusRouteTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    public function test_housekeeping_status_page_uses_named_update_route_and_returns_json(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('unit_status.view', 'web');

        [$user, $property, $tenant] = $this->createTenantUserWithProperty();
        $user->givePermissionTo('unit_status.view');

        $unit = $this->createUnitForProperty($property, $tenant, [
            'housekeeping_status' => 'dirty',
        ]);
        $this->clearTenantAndPropertyContext();

        $session = [
            'current_property_id' => $property->id,
            'branch_id' => $property->branch_id,
        ];

        $this
            ->actingAs($user)
            ->withSession($session)
            ->get(route('dashboard.housekeeping_status.index'))
            ->assertOk()
            ->assertSee(str_replace('/', '\/', route('housekeeping.updateStatus', ['unit' => '__UNIT__'])), false)
            ->assertDontSee('housekeepingStatusBaseUrl', false);

        $this
            ->actingAs($user)
            ->withSession($session)
            ->putJson(route('housekeeping.updateStatus', ['unit' => $unit->id]), [
                'status' => 'clean',
            ])
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('units', [
            'id' => $unit->id,
            'housekeeping_status' => 'clean',
        ]);
    }
}
