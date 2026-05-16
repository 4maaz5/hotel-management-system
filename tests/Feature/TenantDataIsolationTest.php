<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class TenantDataIsolationTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    public function test_tenant_users_cannot_open_another_tenants_reservation(): void
    {
        $this->withoutMiddleware();

        [$userA, $propertyA, $tenantA] = $this->createTenantUserWithProperty();
        $unitA = $this->createUnitForProperty($propertyA, $tenantA);
        $this->createReservation($userA, $propertyA, $unitA);

        [$userB, $propertyB, $tenantB] = $this->createTenantUserWithProperty();
        $unitB = $this->createUnitForProperty($propertyB, $tenantB);
        $reservationB = $this->createReservation($userB, $propertyB, $unitB);

        $this->setTenantAndPropertyContext($tenantA, $propertyA);

        $this
            ->withSession([
                'current_property_id' => $propertyA->id,
                'branch_id' => $propertyA->branch_id,
            ])
            ->actingAs($userA)
            ->get(route('dashboard.reservation.edit', $reservationB))
            ->assertNotFound();
    }
}
