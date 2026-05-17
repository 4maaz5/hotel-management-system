<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Branch;
use App\Models\MarketingQuotation;
use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class MarketingQuotationUpdateTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    public function test_company_level_tenant_user_can_update_quotation_inside_same_tenant(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('manage_branch', 'web');

        [$user, $propertyA, $tenant] = $this->createTenantUserWithProperty();
        $propertyB = $this->createSecondPropertyForTenant($tenant, $user->id);

        $user->forceFill(['branch_id' => null])->save();
        $user->givePermissionTo('manage_branch');

        $this->setTenantAndPropertyContext($tenant, $propertyB);
        $quotation = MarketingQuotation::create([
            'company_id' => $tenant->id,
            'branch_id' => $propertyB->branch_id,
            'quotation_number' => 'Q-TEST-001',
            'client_name' => 'Original Client',
            'quotation_amount' => 250,
            'status' => 'pending',
        ]);

        $this->setTenantAndPropertyContext($tenant, $propertyA);

        $this
            ->actingAs($user)
            ->withSession([
                'current_property_id' => $propertyA->id,
                'branch_id' => $propertyA->branch_id,
            ])
            ->put(route('marketing-quotations.update', $quotation->id), [
                'marketing_agent_id' => null,
                'manual_agent_name' => 'Manual Agent',
                'branch_id' => $propertyB->branch_id,
                'client_name' => 'Updated Client',
                'client_contact' => '555-0100',
                'description' => 'Updated description',
                'quotation_amount' => 500,
                'status' => 'approved',
                'account_number' => 'ACC-1',
                'bank_name' => 'Bank',
                'cr_no' => 'CR-1',
                'vat_no' => 'VAT-1',
                'email' => 'quote@example.com',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('marketing_quotations', [
            'id' => $quotation->id,
            'company_id' => $tenant->id,
            'branch_id' => $propertyB->branch_id,
            'client_name' => 'Updated Client',
            'status' => 'approved',
        ]);
    }

    private function createSecondPropertyForTenant($tenant, int $ownerUserId): Property
    {
        $brand = Brand::create([
            'company_id' => $tenant->id,
            'name' => 'Second Brand',
        ]);

        $branch = Branch::create([
            'company_id' => $tenant->id,
            'brand_id' => $brand->id,
            'name' => 'Second Branch',
            'location' => 'Riyadh',
            'manager' => 'Manager',
            'email' => 'second-branch@example.com',
            'phone' => '+966500000001',
            'status' => 'Active',
        ]);

        $propertyType = PropertyType::create([
            'code' => 'SECOND-HOTEL',
            'name_en' => 'Second Hotel',
            'name_ar' => 'Second Hotel',
            'is_active' => true,
        ]);

        return Property::create([
            'company_id' => $tenant->id,
            'branch_id' => $branch->id,
            'owner_user_id' => $ownerUserId,
            'property_type_id' => $propertyType->id,
            'property_name_en' => 'Second Property',
            'property_name_ar' => 'Second Property',
            'report_name_en' => 'Second Property',
            'report_name_ar' => 'Second Property',
            'property_code' => 'SECOND-PROP',
            'status' => 'ACTIVE',
            'account_expiry_date' => $tenant->end_date,
            'time_zone' => 'Asia/Riyadh',
        ]);
    }
}
