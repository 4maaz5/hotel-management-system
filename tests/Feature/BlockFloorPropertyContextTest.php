<?php

namespace Tests\Feature;

use App\Models\Block;
use App\Models\Floor;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class BlockFloorPropertyContextTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('block.add', 'web');
        Permission::findOrCreate('floor.add', 'web');
    }

    public function test_block_and_floor_creation_repair_property_without_branch(): void
    {
        $tenant = $this->createTenant();
        $user = User::factory()->create([
            'company_id' => $tenant->id,
            'role' => 'owner',
            'status' => 'active',
        ]);
        $user->givePermissionTo(['block.add', 'floor.add']);

        $property = Property::create([
            'company_id' => $tenant->id,
            'owner_user_id' => $user->id,
            'property_name_en' => 'Legacy Hotel',
            'property_name_ar' => 'Legacy Hotel AR',
            'report_name_en' => 'Legacy Hotel Report',
            'report_name_ar' => 'Legacy Hotel Report AR',
            'property_code' => 'LEGACY-HOTEL',
            'status' => 'ACTIVE',
            'email' => 'legacy-hotel@example.com',
            'phone' => '+966500000000',
        ]);

        $this
            ->actingAs($user)
            ->withSession(['current_property_id' => $property->id])
            ->post(route('setup-sidebar.blocks.store'), [
                'name' => 'Main Block',
                'description' => 'Main building',
                'is_active' => '1',
            ])
            ->assertRedirect();

        $property->refresh();
        $block = Block::withoutGlobalScopes()->firstOrFail();

        $this->assertNotNull($property->branch_id);
        $this->assertSame($property->branch_id, $block->branch_id);

        $this
            ->actingAs($user)
            ->withSession([
                'current_property_id' => $property->id,
                'branch_id' => $property->branch_id,
            ])
            ->post(route('setup-sidebar.floors.store'), [
                'block_id' => $block->id,
                'name' => 'Ground Floor',
                'order' => 1,
                'description' => 'Ground level',
                'is_active' => '1',
            ])
            ->assertRedirect();

        $floor = Floor::withoutGlobalScopes()->firstOrFail();

        $this->assertSame($property->branch_id, $floor->branch_id);
        $this->assertSame($block->id, $floor->block_id);
    }
}
