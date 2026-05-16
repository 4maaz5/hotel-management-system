<?php

namespace Tests\Feature;

use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
