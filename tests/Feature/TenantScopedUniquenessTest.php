<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Accident;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\Concerns\BuildsTenantHotelContext;
use Tests\TestCase;

class TenantScopedUniquenessTest extends TestCase
{
    use BuildsTenantHotelContext;
    use RefreshDatabase;

    public function test_employee_ids_can_repeat_across_tenants(): void
    {
        [$userA, $propertyA, $tenantA] = $this->createTenantUserWithProperty();
        [$userB, $propertyB, $tenantB] = $this->createTenantUserWithProperty();

        Employee::create([
            'company_id' => $tenantA->id,
            'branch_id' => $propertyA->branch_id,
            'first_name' => 'Tenant',
            'last_name' => 'A',
            'employee_id' => 'EMP0001',
        ]);

        Employee::create([
            'company_id' => $tenantB->id,
            'branch_id' => $propertyB->branch_id,
            'first_name' => 'Tenant',
            'last_name' => 'B',
            'employee_id' => 'EMP0001',
        ]);

        $this->assertSame(1, Employee::withoutGlobalScopes()->where('company_id', $tenantA->id)->where('employee_id', 'EMP0001')->count());
        $this->assertSame(1, Employee::withoutGlobalScopes()->where('company_id', $tenantB->id)->where('employee_id', 'EMP0001')->count());
    }

    public function test_employee_create_generates_ids_per_tenant(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('manage_employee', 'web');
        Role::findOrCreate('employee', 'web');

        [$userA, $propertyA, $tenantA] = $this->createTenantUserWithProperty();
        [$userB, $propertyB, $tenantB] = $this->createTenantUserWithProperty();
        $userB->givePermissionTo('manage_employee');

        Employee::create([
            'company_id' => $tenantA->id,
            'branch_id' => $propertyA->branch_id,
            'first_name' => 'Tenant',
            'last_name' => 'A',
            'employee_id' => 'EMP0001',
        ]);

        $this->setTenantAndPropertyContext($tenantB, $propertyB);

        $this
            ->actingAs($userB)
            ->withSession([
                'current_property_id' => $propertyB->id,
                'branch_id' => $propertyB->branch_id,
            ])
            ->post(route('dashboard.employee.profile.store'), [
                'first_name' => 'Tenant',
                'last_name' => 'B',
                'email' => 'tenant-b-employee@example.com',
                'branch_id' => $propertyB->branch_id,
                'company_id' => $tenantB->id,
            ])
            ->assertCreated()
            ->assertJsonPath('data.employee_id', 'EMP0001');

        $this->assertSame(1, Employee::withoutGlobalScopes()->where('company_id', $tenantA->id)->where('employee_id', 'EMP0001')->count());
        $this->assertSame(1, Employee::withoutGlobalScopes()->where('company_id', $tenantB->id)->where('employee_id', 'EMP0001')->count());
    }


    public function test_vehicle_plate_numbers_can_repeat_across_tenants(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('manage_branch', 'web');

        [$userA, $propertyA, $tenantA] = $this->createTenantUserWithProperty();
        $userA->givePermissionTo('manage_branch');

        [$userB, $propertyB, $tenantB] = $this->createTenantUserWithProperty();
        $userB->givePermissionTo('manage_branch');

        $payload = [
            'name' => 'Service Van',
            'model' => '2026',
            'plate_number' => 'KSA-1234',
            'owner_name' => 'Operations',
        ];

        $this->setTenantAndPropertyContext($tenantA, $propertyA);

        $this
            ->actingAs($userA)
            ->withSession([
                'current_property_id' => $propertyA->id,
                'branch_id' => $propertyA->branch_id,
            ])
            ->post(route('dashboard.company.vehicle.store'), array_merge($payload, [
                'branch_id' => $propertyA->branch_id,
            ]))
            ->assertRedirect();

        $this->setTenantAndPropertyContext($tenantB, $propertyB);

        $this
            ->actingAs($userB)
            ->withSession([
                'current_property_id' => $propertyB->id,
                'branch_id' => $propertyB->branch_id,
            ])
            ->post(route('dashboard.company.vehicle.store'), array_merge($payload, [
                'branch_id' => $propertyB->branch_id,
            ]))
            ->assertRedirect();

        $this->assertSame(2, Vehicle::withoutGlobalScopes()->where('plate_number', 'KSA-1234')->count());
    }

    public function test_duplicate_vehicle_plate_is_rejected_inside_same_tenant(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('manage_branch', 'web');

        [$user, $property, $tenant] = $this->createTenantUserWithProperty();
        $user->givePermissionTo('manage_branch');

        $payload = [
            'branch_id' => $property->branch_id,
            'name' => 'Service Van',
            'model' => '2026',
            'plate_number' => 'DUP-1234',
            'owner_name' => 'Operations',
        ];

        $session = [
            'current_property_id' => $property->id,
            'branch_id' => $property->branch_id,
        ];

        $this->setTenantAndPropertyContext($tenant, $property);

        $this->actingAs($user)->withSession($session)
            ->post(route('dashboard.company.vehicle.store'), $payload)
            ->assertRedirect();

        $this->setTenantAndPropertyContext($tenant, $property);

        $this->actingAs($user)->withSession($session)
            ->from(route('dashboard.company.vehicle.index'))
            ->post(route('dashboard.company.vehicle.store'), $payload)
            ->assertRedirect(route('dashboard.company.vehicle.index'))
            ->assertSessionHasErrors('plate_number');
    }

    public function test_vehicle_driver_and_accident_tabs_do_not_expose_other_tenants_records(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::findOrCreate('manage_branch', 'web');

        [$userA, $propertyA, $tenantA] = $this->createTenantUserWithProperty();
        $userA->givePermissionTo('manage_branch');

        [$userB, $propertyB, $tenantB] = $this->createTenantUserWithProperty();

        $this->setTenantAndPropertyContext($tenantB, $propertyB);
        $vehicleB = Vehicle::create([
            'company_id' => $tenantB->id,
            'branch_id' => $propertyB->branch_id,
            'name' => 'Hidden Van',
            'plate_number' => 'TENANT-B-DRIVER',
        ]);
        $driverB = Driver::create([
            'vehicle_id' => $vehicleB->id,
            'name' => 'Hidden Driver',
        ]);
        $accidentB = Accident::create([
            'vehicle_id' => $vehicleB->id,
            'driver_id' => $driverB->id,
            'accident_date' => now()->toDateString(),
            'insurance_coverage' => 'no',
            'repair_status' => 'pending',
            'description' => 'Hidden accident',
        ]);

        $this->setTenantAndPropertyContext($tenantA, $propertyA);
        $session = [
            'current_property_id' => $propertyA->id,
            'branch_id' => $propertyA->branch_id,
        ];

        $this->actingAs($userA)->withSession($session)
            ->get(route('dashboard.company.driver.index'))
            ->assertOk()
            ->assertDontSee('Hidden Driver')
            ->assertDontSee('TENANT-B-DRIVER');

        $this->actingAs($userA)->withSession($session)
            ->get(route('dashboard.company.accident.index'))
            ->assertOk()
            ->assertDontSee('Hidden accident')
            ->assertDontSee('TENANT-B-DRIVER');

        $this->actingAs($userA)->withSession($session)
            ->from(route('dashboard.company.driver.index'))
            ->post(route('dashboard.company.driver.store'), [
                'vehicle_id' => $vehicleB->id,
                'name' => 'Cross Tenant Driver',
            ])
            ->assertRedirect(route('dashboard.company.driver.index'))
            ->assertSessionHasErrors('vehicle_id');

        $this->actingAs($userA)->withSession($session)
            ->put(route('dashboard.company.driver.update', $driverB), [
                'vehicle_id' => $vehicleB->id,
                'name' => 'Changed Driver',
            ])
            ->assertNotFound();

        $this->actingAs($userA)->withSession($session)
            ->delete(route('dashboard.company.accident.destroy', $accidentB))
            ->assertNotFound();
    }
}
