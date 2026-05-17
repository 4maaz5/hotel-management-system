<?php

namespace Tests\Feature;

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class EmployeeCardNullRelationTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    public function test_employee_card_page_handles_employee_without_direct_brand_or_department(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('manage_employee', 'web');

        [$user, $property, $tenant] = $this->createTenantUserWithProperty();
        $user->givePermissionTo('manage_employee');

        Employee::create([
            'company_id' => $tenant->id,
            'branch_id' => $property->branch_id,
            'brand_id' => null,
            'department_id' => null,
            'first_name' => 'No',
            'last_name' => 'Brand',
            'employee_id' => 'EMP-CARD-001',
            'qr_code' => 'EMP-CARD-001',
        ]);

        $this->setTenantAndPropertyContext($tenant, $property);

        $this
            ->actingAs($user)
            ->withSession([
                'current_property_id' => $property->id,
                'branch_id' => $property->branch_id,
            ])
            ->get(route('dashboard.employee.card'))
            ->assertOk()
            ->assertSee('EMP-CARD-001');
    }
}
