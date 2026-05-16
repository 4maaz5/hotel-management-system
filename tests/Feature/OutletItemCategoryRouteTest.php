<?php

namespace Tests\Feature;

use App\Models\ItemCategory;
use App\Models\OutletSetup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class OutletItemCategoryRouteTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('outlet_item.add', 'web');
    }

    public function test_item_create_page_and_legacy_url_load_outlet_categories(): void
    {
        [$user, $property, $tenant, $branch] = $this->createTenantUserWithProperty();
        $user->givePermissionTo('outlet_item.add');

        $outlet = OutletSetup::create([
            'company_id' => $tenant->id,
            'branch_id' => $branch->id,
            'status' => true,
            'operating_status' => 'open',
            'outlet_code' => 'RST',
            'name' => 'Restaurant',
        ]);

        ItemCategory::create([
            'company_id' => $tenant->id,
            'outlet_id' => $outlet->id,
            'status' => true,
            'name' => 'Hot Drinks',
            'ntmp_category' => 'food_and_beverage',
        ]);

        $session = [
            'current_property_id' => $property->id,
            'branch_id' => $property->branch_id,
        ];

        $this
            ->actingAs($user)
            ->withSession($session)
            ->get(route('setup-sidebar.items.create'))
            ->assertOk()
            ->assertSee(str_replace('/', '\/', route('setup-sidebar.items.categories', ['outlet' => '__OUTLET__'])), false)
            ->assertDontSee('/app/outlet', false);

        $this
            ->actingAs($user)
            ->withSession($session)
            ->getJson(route('setup-sidebar.items.categories', ['outlet' => $outlet->id]))
            ->assertOk()
            ->assertJsonFragment(['name' => 'Hot Drinks']);

        $this
            ->actingAs($user)
            ->withSession($session)
            ->getJson('/app/outlet/'.$outlet->id.'/categories')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Hot Drinks']);
    }
}
