<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\FacilityCategory;
use Database\Seeders\FacilityCategorySeeder;
use Database\Seeders\FacilitySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class PropertyFacilityRouteTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    public function test_facility_ajax_route_returns_seeded_facilities(): void
    {
        $this->seed(FacilityCategorySeeder::class);
        $this->seed(FacilitySeeder::class);
        $this->seed(FacilityCategorySeeder::class);
        $this->seed(FacilitySeeder::class);

        [$user, $property] = $this->createTenantUserWithProperty();
        $category = FacilityCategory::where('name', 'Recreation')->firstOrFail();

        $this
            ->actingAs($user)
            ->withSession([
                'current_property_id' => $property->id,
                'branch_id' => $property->branch_id,
            ])
            ->getJson(route('setup-sidebar.property_facility.facilities', [
                'category_id' => $category->id,
            ]))
            ->assertOk()
            ->assertJsonFragment(['name' => 'Swimming Pool']);

        $this
            ->actingAs($user)
            ->withSession([
                'current_property_id' => $property->id,
                'branch_id' => $property->branch_id,
            ])
            ->getJson('/app/admin/get-facilities?category_id='.$category->id)
            ->assertOk()
            ->assertJsonFragment(['name' => 'Swimming Pool']);

        $this->assertSame(5, FacilityCategory::count());
        $this->assertSame(13, Facility::count());
    }
}
