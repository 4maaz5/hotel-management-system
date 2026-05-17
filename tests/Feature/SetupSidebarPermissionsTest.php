<?php

namespace Tests\Feature;

use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class SetupSidebarPermissionsTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_owner_role_can_see_item_categories_tab_in_setup_sidebar(): void
    {
        $this->seed(PermissionsSeeder::class);

        [$user, $property] = $this->createTenantUserWithProperty(userOverrides: [
            'role' => 'owner',
            'user_type' => 'owner',
        ]);
        $user->assignRole('owner');

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this
            ->actingAs($user)
            ->withSession([
                'current_property_id' => $property->id,
                'branch_id' => $property->branch_id,
            ])
            ->get(route('setup-sidebar'))
            ->assertOk()
            ->assertSee(route('setup-sidebar.item_category.index'), false)
            ->assertSee(__('dashboard.items_categories'), false);
    }

    public function test_reservation_role_create_page_handles_all_permission_name_formats(): void
    {
        $this->seed(PermissionsSeeder::class);

        [$user, $property] = $this->createTenantUserWithProperty(userOverrides: [
            'role' => 'owner',
            'user_type' => 'owner',
        ]);
        $user->assignRole('owner');

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this
            ->actingAs($user)
            ->withSession([
                'current_property_id' => $property->id,
                'branch_id' => $property->branch_id,
            ])
            ->get(route('setup-sidebar.property-role.create'))
            ->assertOk()
            ->assertSee('General', false)
            ->assertSee('Manage employee', false)
            ->assertSee('View', false);
    }

    public function test_limited_reservation_user_cannot_open_setup_pages_by_direct_url(): void
    {
        $this->seed(PermissionsSeeder::class);

        [$user, $property] = $this->createTenantUserWithProperty();
        $user->givePermissionTo(Permission::findByName('reservation.view', 'web'));

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $session = [
            'current_property_id' => $property->id,
            'branch_id' => $property->branch_id,
        ];

        $this
            ->actingAs($user)
            ->withSession($session)
            ->get(route('dashboard.reservation.index'))
            ->assertOk();

        $this
            ->actingAs($user)
            ->withSession($session)
            ->get(route('setup-sidebar.property-role.index'))
            ->assertForbidden();

        $this
            ->actingAs($user)
            ->withSession($session)
            ->get(route('setup-sidebar.blocks.index'))
            ->assertForbidden();
    }

    public function test_reservation_program_cards_follow_user_permissions(): void
    {
        $this->seed(PermissionsSeeder::class);

        [$user, $property] = $this->createTenantUserWithProperty();
        $user->givePermissionTo(Permission::findByName('reservation.view', 'web'));

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this
            ->actingAs($user)
            ->withSession([
                'current_property_id' => $property->id,
                'branch_id' => $property->branch_id,
            ])
            ->get(route('program'))
            ->assertOk()
            ->assertSee(route('dashboard.reservation.index'), false)
            ->assertDontSee(route('dashboard.unit_status.index'), false)
            ->assertDontSee(route('dashboard.receipt.index'), false);
    }
}
