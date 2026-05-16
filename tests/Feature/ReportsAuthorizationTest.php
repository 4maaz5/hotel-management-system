<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class ReportsAuthorizationTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('reports.view', 'web');
        Permission::findOrCreate('reports.print', 'web');
    }

    public function test_reports_require_report_view_permission(): void
    {
        [$user, $property, $tenant] = $this->createTenantUserWithProperty();
        $this->setTenantAndPropertyContext($tenant, $property);

        $this
            ->withSession([
                'current_property_id' => $property->id,
                'branch_id' => $property->branch_id,
            ])
            ->actingAs($user)
            ->get(route('dashboard.reports.index'))
            ->assertForbidden();

        $user->givePermissionTo('reports.view');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this
            ->withSession([
                'current_property_id' => $property->id,
                'branch_id' => $property->branch_id,
            ])
            ->actingAs($user)
            ->get(route('dashboard.reports.index'))
            ->assertOk();
    }

    public function test_report_print_requires_print_permission(): void
    {
        [$user, $property, $tenant] = $this->createTenantUserWithProperty();
        $this->setTenantAndPropertyContext($tenant, $property);

        $user->givePermissionTo('reports.view');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this
            ->withSession([
                'current_property_id' => $property->id,
                'branch_id' => $property->branch_id,
            ])
            ->actingAs($user)
            ->get(route('dashboard.reports.print', ['reportType' => 'occupancy']))
            ->assertForbidden();

        $user->givePermissionTo('reports.print');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this
            ->withSession([
                'current_property_id' => $property->id,
                'branch_id' => $property->branch_id,
            ])
            ->actingAs($user)
            ->get(route('dashboard.reports.print', ['reportType' => 'occupancy']))
            ->assertOk();
    }
}
