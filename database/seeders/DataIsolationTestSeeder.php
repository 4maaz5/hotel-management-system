<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Block;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Floor;
use App\Models\Guest;
use App\Models\HallType;
use App\Models\Payroll;
use App\Models\Property;
use App\Models\PropertyCommercialDetail;
use App\Models\PropertyType;
use App\Models\Reservation;
use App\Models\SupportTicket;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\UnitClass;
use App\Models\UnitType;
use App\Models\UnitTypeCustomization;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use App\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DataIsolationTestSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Preparing data isolation demo tenants...');

        $this->call([
            SubscriptionPlanSeeder::class,
            PermissionsSeeder::class,
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $tenants = [
            [
                'name' => 'Isolation Acme Hotels',
                'legal_name' => 'Isolation Acme Hotels Ltd.',
                'subdomain' => 'isolation-acme',
                'owner_name' => 'Acme Owner',
                'owner_email' => 'owner@isolation-acme.test',
                'prefix' => 'ACME',
                'city' => 'Riyadh',
                'brands' => ['Acme Stay', 'Acme Suites'],
                'branches' => [
                    ['name' => 'Acme Riyadh Downtown', 'location' => 'Olaya, Riyadh'],
                    ['name' => 'Acme Jeddah Corniche', 'location' => 'Corniche, Jeddah'],
                ],
            ],
            [
                'name' => 'Isolation Globex Resorts',
                'legal_name' => 'Isolation Globex Resorts LLC',
                'subdomain' => 'isolation-globex',
                'owner_name' => 'Globex Owner',
                'owner_email' => 'owner@isolation-globex.test',
                'prefix' => 'GLOBEX',
                'city' => 'Dammam',
                'brands' => ['Globex Resort', 'Globex Business'],
                'branches' => [
                    ['name' => 'Globex Dammam Marina', 'location' => 'Marina, Dammam'],
                    ['name' => 'Globex Makkah Central', 'location' => 'Central, Makkah'],
                ],
            ],
        ];

        foreach ($tenants as $tenantConfig) {
            $this->seedTenant($tenantConfig);
        }

        $this->command->info('');
        $this->command->info('========== DATA ISOLATION TEST CREDENTIALS ==========');
        $this->command->info('  SaaS Super Admin: admin@gmail.com / Admin@123');
        $this->command->info('  Acme Owner:       owner@isolation-acme.test / Password1');
        $this->command->info('  Globex Owner:     owner@isolation-globex.test / Password1');
        $this->command->info('  Employee users:   first.last@isolation-*.test / Password1');
        $this->command->info('=====================================================');
        $this->command->info('Use these tenants to verify HR, reservation, support tickets, properties, guests, units, and reservations stay isolated.');
    }

    private function seedTenant(array $config): void
    {
        $plan = \App\Models\SubscriptionPlan::where('name', 'Pro')->first()
            ?? \App\Models\SubscriptionPlan::where('is_active', true)->first();

        $tenant = Tenant::updateOrCreate(
            ['email' => $config['owner_email']],
            [
                'name' => $config['name'],
                'subdomain' => $config['subdomain'],
                'phone' => '+96650000'.random_int(1000, 9999),
                'start_date' => now()->subMonth(),
                'end_date' => now()->addMonths(6),
                'subscription_status' => 'active',
                'subscription_plan_id' => $plan?->id,
            ]
        );

        $tenant->forceFill([
            'legal_name' => $config['legal_name'],
            'city' => $config['city'],
            'country' => 'Saudi Arabia',
            'industry_type' => 'Hospitality',
            'is_active' => true,
        ])->save();

        $owner = $this->upsertUser(
            $tenant,
            null,
            $config['owner_name'],
            $config['owner_email'],
            'owner'
        );
        $this->giveFullAccess($owner, 'owner');

        $brandIds = [];
        foreach ($config['brands'] as $brandName) {
            $brand = Brand::updateOrCreate(
                ['company_id' => $tenant->id, 'name' => $brandName],
                ['company_id' => $tenant->id, 'name' => $brandName]
            );
            $brandIds[] = $brand->id;
        }

        foreach ($config['branches'] as $branchIndex => $branchData) {
            $branch = Branch::updateOrCreate(
                ['company_id' => $tenant->id, 'name' => $branchData['name']],
                [
                    'brand_id' => $brandIds[$branchIndex % count($brandIds)],
                    'location' => $branchData['location'],
                    'manager' => $config['prefix'].' Manager '.($branchIndex + 1),
                    'email' => 'branch'.($branchIndex + 1).'@'.$config['subdomain'].'.test',
                    'phone' => '+96651111'.str_pad((string) ($branchIndex + 1), 4, '0', STR_PAD_LEFT),
                    'status' => 'Active',
                ]
            );

            if ($branchIndex === 0) {
                $owner->forceFill(['branch_id' => $branch->id])->save();
            }

            $departments = $this->seedDepartments($branch);
            $employees = $this->seedEmployees($tenant, $branch, $brandIds[$branchIndex % count($brandIds)], $departments, $config, $branchIndex);
            $property = $this->seedReservationDashboardData($tenant, $branch, $owner, $config, $branchIndex);

            $property->users()->syncWithoutDetaching([$owner->id]);
            foreach ($employees['users'] as $employeeUser) {
                $property->users()->syncWithoutDetaching([$employeeUser->id]);
            }
        }

        $this->seedSupportTickets($tenant, $owner, $config);

        $this->command->info("  Seeded {$config['name']} ({$config['owner_email']})");
    }

    private function seedDepartments(Branch $branch): array
    {
        $departmentIds = [];
        foreach (['HR', 'Front Office', 'Housekeeping', 'Finance'] as $name) {
            $department = Department::updateOrCreate(
                ['branch_id' => $branch->id, 'name' => $name],
                ['branch_id' => $branch->id, 'name' => $name]
            );
            $departmentIds[$name] = $department->id;
        }

        return $departmentIds;
    }

    private function seedEmployees(Tenant $tenant, Branch $branch, int $brandId, array $departments, array $config, int $branchIndex): array
    {
        $employeeRows = [
            ['first' => 'Sara', 'last' => 'Frontdesk', 'designation' => 'Receptionist', 'department' => 'Front Office', 'salary' => 6500],
            ['first' => 'Omar', 'last' => 'Payroll', 'designation' => 'Payroll Officer', 'department' => 'HR', 'salary' => 8500],
            ['first' => 'Mona', 'last' => 'Rooms', 'designation' => 'Housekeeping Supervisor', 'department' => 'Housekeeping', 'salary' => 7000],
        ];

        $users = [];

        foreach ($employeeRows as $index => $row) {
            $email = Str::lower($row['first'].'.'.$row['last'].($branchIndex + 1).'@'.$config['subdomain'].'.test');
            $employeeCode = $config['prefix'].'-EMP-'.str_pad((string) (($branchIndex + 1) * 10 + $index + 1), 3, '0', STR_PAD_LEFT);

            $employee = Employee::updateOrCreate(
                ['employee_id' => $employeeCode],
                [
                    'company_id' => $tenant->id,
                    'brand_id' => $brandId,
                    'branch_id' => $branch->id,
                    'department_id' => $departments[$row['department']],
                    'first_name' => $row['first'],
                    'last_name' => $row['last'],
                    'email' => $email,
                    'phone' => '+96652222'.str_pad((string) (($branchIndex + 1) * 10 + $index), 4, '0', STR_PAD_LEFT),
                    'designation' => $row['designation'],
                    'join_date' => now()->subMonths(8 + $index),
                    'base_salary' => $row['salary'],
                    'residence_expiry_date' => now()->addMonths(10 + $index),
                ]
            );

            $user = $this->upsertUser(
                $tenant,
                $branch,
                $row['first'].' '.$row['last'],
                $email,
                'employee'
            );
            $this->giveFullAccess($user, 'employee');

            $employee->forceFill(['user_id' => $user->id])->save();
            $users[] = $user;

            Attendance::updateOrCreate(
                ['employee_id' => $employee->id, 'date' => now()->subDays($index)->toDateString()],
                [
                    'check_in' => '09:00:00',
                    'check_out' => '17:30:00',
                    'status' => $index === 2 ? 'Leave' : 'Present',
                    'overtime_hours' => $index === 0 ? 1.5 : 0,
                ]
            );

            Payroll::updateOrCreate(
                ['employee_id' => $employee->id, 'month' => now()->format('Y-m')],
                [
                    'basic_salary' => $row['salary'],
                    'allowance' => 500,
                    'deductions' => $index === 2 ? 100 : 0,
                    'commission_amount' => 0,
                    'total_amount' => $row['salary'] + 500,
                    'net_pay' => $row['salary'] + 500 - ($index === 2 ? 100 : 0),
                    'status' => $index === 0 ? 'Paid' : 'Pending',
                ]
            );
        }

        return ['users' => $users];
    }

    private function seedReservationDashboardData(Tenant $tenant, Branch $branch, User $owner, array $config, int $branchIndex): Property
    {
        $propertyType = PropertyType::firstOrCreate(
            ['code' => 'HOTEL'],
            ['name_en' => 'Hotel', 'name_ar' => 'Hotel', 'is_active' => true]
        );
        $unitType = UnitType::firstOrCreate(['name' => 'Standard Room'], ['is_active' => true]);
        $unitClass = UnitClass::firstOrCreate(['name' => 'Standard'], ['is_active' => true]);
        $hallType = HallType::firstOrCreate(['name' => 'Hotel Room'], ['is_active' => true]);

        $property = Property::updateOrCreate(
            ['company_id' => $tenant->id, 'branch_id' => $branch->id],
            [
                'property_name_en' => $branch->name.' Property',
                'property_name_ar' => $branch->name.' Property',
                'report_name_en' => $branch->name.' Report',
                'report_name_ar' => $branch->name.' Report',
                'property_code' => $config['prefix'].'-P'.($branchIndex + 1),
                'property_type_id' => $propertyType->id,
                'owner_user_id' => $owner->id,
                'status' => 'ACTIVE',
                'account_expiry_date' => now()->addMonths(6),
                'time_zone' => 'Asia/Riyadh',
                'phone' => $branch->phone,
                'email' => $branch->email,
                'active_units_count' => 4,
                'max_units_count' => 25,
            ]
        );

        PropertyCommercialDetail::updateOrCreate(
            ['company_id' => $tenant->id, 'branch_id' => $branch->id],
            [
                'registration_number' => $config['prefix'].'-CR-'.($branchIndex + 1),
                'activity_license_number' => $config['prefix'].'-LIC-'.($branchIndex + 1),
                'vat_registration_number' => '3'.str_pad((string) $tenant->id, 14, '0', STR_PAD_LEFT),
            ]
        );

        $customization = UnitTypeCustomization::updateOrCreate(
            ['company_id' => $tenant->id, 'name' => $config['prefix'].' Standard Room'],
            [
                'unit_type_id' => $unitType->id,
                'unit_area' => 32,
                'single_beds' => 1,
                'double_beds' => 1,
                'base_occupancy' => 2,
                'is_published_online' => true,
            ]
        );

        $block = Block::updateOrCreate(
            ['company_id' => $tenant->id, 'branch_id' => $branch->id, 'name' => 'Isolation Block A'],
            ['is_active' => true]
        );

        $floor = Floor::updateOrCreate(
            ['company_id' => $tenant->id, 'branch_id' => $branch->id, 'name' => 'Isolation Floor 1'],
            ['block_id' => $block->id, 'order' => 1, 'is_active' => true]
        );

        $units = [];
        foreach ([101, 102, 103, 104] as $roomNumber) {
            $unitNumber = $config['prefix'].'-'.($branchIndex + 1).'-'.$roomNumber;
            $units[] = Unit::updateOrCreate(
                ['unit_number' => $unitNumber],
                [
                    'company_id' => $tenant->id,
                    'branch_id' => $branch->id,
                    'unit_type_id' => $customization->id,
                    'unit_class_id' => $unitClass->id,
                    'block_id' => $block->id,
                    'floor_id' => $floor->id,
                    'hall_type_id' => $hallType->id,
                    'base_occupancy' => 2,
                    'number_of_single_beds' => 1,
                    'number_of_double_beds' => 1,
                    'unit_area' => 32,
                    'is_active' => true,
                    'housekeeping_status' => $roomNumber === 104 ? 'dirty' : 'clean',
                ]
            );
        }

        $guest = Guest::updateOrCreate(
            ['company_id' => $tenant->id, 'branch_id' => $branch->id, 'email' => 'guest'.($branchIndex + 1).'@'.$config['subdomain'].'.test'],
            [
                'first_name' => $config['prefix'],
                'last_name' => 'Guest '.($branchIndex + 1),
                'gender' => 'male',
                'guest_type' => 'individual',
                'id_type' => 'national_id',
                'id_number' => $config['prefix'].'-ID-'.($branchIndex + 1),
                'mobile_number' => '555000'.str_pad((string) ($branchIndex + 1), 4, '0', STR_PAD_LEFT),
                'is_active' => true,
            ]
        );

        $checkIn = Carbon::today()->addDays($branchIndex);
        $checkOut = $checkIn->copy()->addDays(2);
        $rent = 320 + ($branchIndex * 60);

        Reservation::updateOrCreate(
            [
                'company_id' => $tenant->id,
                'reservation_number' => $config['prefix'].'-RES-'.($branchIndex + 1),
            ],
            [
                'branch_id' => $branch->id,
                'guest_id' => $guest->id,
                'unit_id' => $units[0]->id,
                'check_in_date' => $checkIn->toDateString(),
                'check_out_date' => $checkOut->toDateString(),
                'nights' => 2,
                'adults' => 2,
                'children' => 0,
                'reservation_type' => 'daily',
                'daily_rate' => $rent,
                'monthly_rate' => 0,
                'total_rent' => $rent * 2,
                'discount' => 0,
                'total_taxes_fees' => 0,
                'security_deposit' => 0,
                'paid_amount' => $rent,
                'balance' => $rent,
                'subtotal' => $rent * 2,
                'grand_total' => $rent * 2,
                'status' => $branchIndex === 0 ? 'confirmed' : 'checked_in',
                'is_confirmed' => true,
                'booking_date' => now()->subDays(3)->toDateString(),
                'created_by' => $owner->id,
            ]
        );

        return $property;
    }

    private function seedSupportTickets(Tenant $tenant, User $owner, array $config): void
    {
        foreach (['reservation', 'hr'] as $area) {
            $ticket = SupportTicket::updateOrCreate(
                ['company_id' => $tenant->id, 'subject' => $config['prefix'].' '.ucfirst($area).' isolation ticket'],
                [
                    'created_by' => $owner->id,
                    'category' => $area === 'hr' ? 'HR' : 'Reservation',
                    'support_area' => $area,
                    'priority' => $area === 'hr' ? 'normal' : 'high',
                    'status' => $area === 'hr' ? 'pending' : 'open',
                    'last_message_at' => now(),
                    'last_sender_role' => $area === 'hr' ? 'super_admin' : 'tenant',
                    'tenant_last_read_at' => $area === 'hr' ? null : now(),
                    'super_admin_last_read_at' => $area === 'hr' ? now() : null,
                ]
            );

            $body = '<p>'.$config['name'].' '.$area.' support conversation for tenant isolation testing.</p>';

            if (! $ticket->messages()->where('body', $body)->exists()) {
                $ticket->messages()->create([
                    'user_id' => $owner->id,
                    'sender_role' => $area === 'hr' ? 'super_admin' : 'tenant',
                    'body' => $body,
                ]);
            }
        }
    }

    private function upsertUser(Tenant $tenant, ?Branch $branch, string $name, string $email, string $role): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            [
                'company_id' => $tenant->id,
                'branch_id' => $branch?->id,
                'name' => $name,
                'password' => Hash::make('Password1'),
                'role' => $role,
                'status' => 'active',
                'user_type' => $role,
            ]
        );
    }

    private function giveFullAccess(User $user, string $roleName): void
    {
        $role = Role::firstOrCreate(['company_id' => null, 'name' => $roleName, 'guard_name' => 'web']);

        if ($roleName === 'owner') {
            $role->syncPermissions(Permission::all());
        }

        if (! $user->hasRole($roleName)) {
            $user->assignRole($role);
        }
    }
}
