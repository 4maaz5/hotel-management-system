<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class ClientUpdateTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    public function test_company_level_tenant_user_can_update_client_inside_same_tenant(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('manage_branch', 'web');

        [$user, $propertyA, $tenant] = $this->createTenantUserWithProperty();
        $propertyB = $this->createSecondPropertyForTenant($tenant, $user->id);

        $user->forceFill(['branch_id' => null])->save();
        $user->givePermissionTo('manage_branch');

        $this->setTenantAndPropertyContext($tenant, $propertyB);
        $client = Client::create([
            'company_id' => $tenant->id,
            'branch_id' => $propertyB->branch_id,
            'company_name' => 'Original Company',
            'client_name' => 'Original Client',
            'email' => 'original-client@example.com',
        ]);

        $this->setTenantAndPropertyContext($tenant, $propertyA);

        $this
            ->actingAs($user)
            ->withSession([
                'current_property_id' => $propertyA->id,
                'branch_id' => $propertyA->branch_id,
            ])
            ->put(route('dashboard.company.client.update', $client->id), [
                'company_name' => 'Updated Company',
                'client_name' => 'Updated Client',
                'cr_number' => 'CR-1',
                'vat_number' => 'VAT-1',
                'email' => 'updated-client@example.com',
                'phone' => '555-0199',
                'person_name' => 'Responsible',
                'contact' => 'Contact',
                'address' => 'Riyadh',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'company_id' => $tenant->id,
            'branch_id' => $propertyB->branch_id,
            'company_name' => 'Updated Company',
            'client_name' => 'Updated Client',
        ]);
    }

    private function createSecondPropertyForTenant($tenant, int $ownerUserId): Property
    {
        $brand = Brand::create([
            'company_id' => $tenant->id,
            'name' => 'Client Second Brand',
        ]);

        $branch = Branch::create([
            'company_id' => $tenant->id,
            'brand_id' => $brand->id,
            'name' => 'Client Second Branch',
            'location' => 'Riyadh',
            'manager' => 'Manager',
            'email' => 'client-second-branch@example.com',
            'phone' => '+966500000002',
            'status' => 'Active',
        ]);

        $propertyType = PropertyType::create([
            'code' => 'CLIENT-SECOND-HOTEL',
            'name_en' => 'Client Second Hotel',
            'name_ar' => 'Client Second Hotel',
            'is_active' => true,
        ]);

        return Property::create([
            'company_id' => $tenant->id,
            'branch_id' => $branch->id,
            'owner_user_id' => $ownerUserId,
            'property_type_id' => $propertyType->id,
            'property_name_en' => 'Client Second Property',
            'property_name_ar' => 'Client Second Property',
            'report_name_en' => 'Client Second Property',
            'report_name_ar' => 'Client Second Property',
            'property_code' => 'CLIENT-SECOND-PROP',
            'status' => 'ACTIVE',
            'account_expiry_date' => $tenant->end_date,
            'time_zone' => 'Asia/Riyadh',
        ]);
    }
}
