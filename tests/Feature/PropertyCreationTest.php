<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Region;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class PropertyCreationTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    public function test_property_store_generates_code_sets_company_and_uses_tenant_expiry_date(): void
    {
        $this->withoutMiddleware();

        $tenant = $this->createTenant([
            'email' => 'tenant-property@example.com',
            'end_date' => now()->addMonths(2)->toDateString(),
        ]);

        $user = User::factory()->create([
            'company_id' => $tenant->id,
            'role' => 'employee',
            'status' => 'active',
        ]);

        app(TenantContext::class)->setTenant($tenant);

        $propertyType = PropertyType::create([
            'code' => 'HOTEL',
            'name_en' => 'Hotel',
            'name_ar' => 'Hotel AR',
            'is_active' => true,
        ]);
        $country = Country::create([
            'iso_code' => 'SA',
            'name_en' => 'Saudi Arabia',
            'name_ar' => 'Saudi Arabia AR',
        ]);
        $region = Region::create([
            'country_id' => $country->id,
            'name_en' => 'Riyadh',
            'name_ar' => 'Riyadh AR',
        ]);
        $city = City::create([
            'region_id' => $region->id,
            'name_en' => 'Riyadh',
            'name_ar' => 'Riyadh AR',
        ]);
        $district = District::create([
            'city_id' => $city->id,
            'name_en' => 'Olaya',
            'name_ar' => 'Olaya AR',
        ]);

        $this
            ->actingAs($user)
            ->post(route('setup-sidebar.property.store'), [
                'property_name_en' => 'Riyadh Central',
                'property_name_ar' => 'Riyadh Central AR',
                'report_name_en' => 'Riyadh Central Report',
                'report_name_ar' => 'Riyadh Central Report AR',
                'property_type_id' => $propertyType->id,
                'status' => 'ACTIVE',
                'account_version' => 'BASIC',
                'country_id' => $country->id,
                'region_id' => $region->id,
                'city_id' => $city->id,
                'district_id' => $district->id,
                'address_en' => 'Olaya Street',
                'address_ar' => 'Olaya Street AR',
                'building_no' => '123',
                'postal_code' => '12345',
                'time_zone' => 'Asia/Riyadh',
                'phone' => '+966500000000',
                'mobile' => '+966500000001',
                'email' => 'hotel@example.com',
                'active_units_count' => 0,
                'max_units_count' => 10,
            ])
            ->assertRedirect(route('setup-sidebar.property.index'));

        $property = Property::withoutGlobalScopes()->firstOrFail();

        $this->assertSame($tenant->id, $property->company_id);
        $this->assertNotEmpty($property->property_code);
        $this->assertStringStartsWith('RIYADH-CENTR', $property->property_code);
        $this->assertSame($tenant->end_date->format('Y-m-d'), $property->account_expiry_date->format('Y-m-d'));
        $this->assertNotNull($property->branch_id);
        $this->assertDatabaseHas('branches', [
            'id' => $property->branch_id,
            'company_id' => $tenant->id,
            'name' => 'Riyadh Central',
        ]);
    }
}
