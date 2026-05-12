<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DataIsolationTestSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating test tenants for data isolation verification...');

        $this->createTenant([
            'company_name' => 'Acme Corporation',
            'legal_name'   => 'Acme Corporation Ltd.',
            'subdomain'    => 'acme',
            'owner_name'   => 'John Acme',
            'owner_email'  => 'owner@acme.com',
            'password'     => 'Password1',
            'brands'       => ['Acme Electronics', 'Acme Furniture'],
            'branches'     => [
                ['name' => 'Acme HQ - Riyadh',  'location' => 'Olaya St, Riyadh'],
                ['name' => 'Acme Branch - Jeddah', 'location' => 'Al-Madinah Rd, Jeddah'],
            ],
        ]);

        $this->createTenant([
            'company_name' => 'Globex Industries',
            'legal_name'   => 'Globex Industries LLC',
            'subdomain'    => 'globex',
            'owner_name'   => 'Jane Globex',
            'owner_email'  => 'owner@globex.com',
            'password'     => 'Password1',
            'brands'       => ['Globex Automotive', 'Globex Healthcare'],
            'branches'     => [
                ['name' => 'Globex HQ - Dammam',   'location' => 'King Fahd St, Dammam'],
                ['name' => 'Globex Branch - Mecca',  'location' => 'Ibrahim Al-Khalil St, Mecca'],
                ['name' => 'Globex Branch - Madinah', 'location' => 'Prince Mohammed St, Madinah'],
            ],
        ]);

        $this->command->info('');
        $this->command->info('========== DATA ISOLATION TEST CREDENTIALS ==========');
        $this->command->info('  Tenant Owners:');
        $this->command->info('    owner@acme.com   / Password1  (Acme)');
        $this->command->info('    owner@globex.com / Password1  (Globex)');
        $this->command->info('  Super Admin:');
        $this->command->info('    admin@gmail.com  / Admin@123');
        $this->command->info('====================================================');
        $this->command->info('');
        $this->command->info('Log in as each owner and verify you ONLY see their data.');
    }

    protected function createTenant(array $config): void
    {
        if (User::where('email', $config['owner_email'])->exists()) {
            $ownerRole = Role::where('name', 'owner')->first();
            if ($ownerRole) {
                $ownerRole->syncPermissions(Permission::all());
            }
            $this->command->info("  Synced permissions for {$config['company_name']}");
            return;
        }

        $company = Company::create([
            'name'               => $config['company_name'],
            'legal_name'         => $config['legal_name'],
            'email'              => $config['owner_email'],
            'phone'              => '+966500000000',
            'city'               => 'Riyadh',
            'country'            => 'Saudi Arabia',
            'is_active'          => true,
            'subscription_status' => 'active',
        ]);

        $owner = User::create([
            'company_id' => $company->id,
            'name'       => $config['owner_name'],
            'email'      => $config['owner_email'],
            'password'   => Hash::make($config['password']),
            'status'     => 'active',
            'user_type'  => 'owner',
        ]);
        $ownerRole = Role::firstOrCreate(
            ['name' => 'owner', 'guard_name' => 'web'],
            ['description' => 'Tenant owner', 'status' => 'ACTIVE']
        );
        $ownerRole->syncPermissions(Permission::all());
        $owner->assignRole($ownerRole);

        $brandIds = [];
        foreach ($config['brands'] as $brandName) {
            $brand = Brand::create([
                'company_id' => $company->id,
                'name'       => $brandName,
            ]);
            $brandIds[] = $brand->id;
        }

        $branchIndex = 0;
        foreach ($config['branches'] as $i => $branchData) {
            $branch = Branch::create([
                'company_id' => $company->id,
                'brand_id'   => $brandIds[$branchIndex % count($brandIds)],
                'name'       => $branchData['name'],
                'location'   => $branchData['location'],
                'manager'    => 'Manager ' . ($i + 1),
                'email'      => 'branch' . ($i + 1) . '@' . $config['subdomain'] . '.com',
                'phone'      => '+9665111111' . str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT),
                'status'     => 'Active',
            ]);
            $branchIndex++;

            $departments = ['HR', 'Finance', 'IT', 'Sales'];
            foreach ($departments as $deptName) {
                Department::create([
                    'branch_id' => $branch->id,
                    'name'      => $deptName,
                ]);
            }

            $employees = [
                ['first' => 'Ahmed',  'last' => 'Al-Saud',  'designation' => 'Branch Manager'],
                ['first' => 'Sara',   'last' => 'Al-Otaibi', 'designation' => 'Accountant'],
                ['first' => 'Mohammed', 'last' => 'Khan',    'designation' => 'Sales Rep'],
            ];
            foreach ($employees as $j => $emp) {
                $email = strtolower($emp['first'] . '.' . $emp['last'] . ($i + 1)) . '@' . $config['subdomain'] . '.com';
                if (User::where('email', $email)->exists()) {
                    continue;
                }
                $employee = Employee::create([
                    'company_id'   => $company->id,
                    'brand_id'     => $brandIds[$branchIndex % count($brandIds)],
                    'branch_id'    => $branch->id,
                    'department_id' => Department::where('branch_id', $branch->id)->first()->id,
                    'first_name'   => $emp['first'],
                    'last_name'    => $emp['last'],
                    'employee_id'  => strtoupper($config['subdomain']) . '-EMP-' . str_pad((string)($branch->id * 10 + $j), 3, '0', STR_PAD_LEFT),
                    'email'        => $email,
                    'phone'        => '+9665222222' . str_pad((string)($branchIndex), 2, '0', STR_PAD_LEFT),
                    'designation'  => $emp['designation'],
                    'join_date'    => Carbon::now()->subMonths(rand(3, 24)),
                    'base_salary'  => rand(4000, 15000),
                ]);

                $user = User::create([
                    'company_id' => $company->id,
                    'branch_id'  => $branch->id,
                    'name'       => $emp['first'] . ' ' . $emp['last'],
                    'email'      => $email,
                    'password'   => Hash::make('Password1'),
                    'status'     => 'active',
                    'user_type'  => 'employee',
                ]);
                $employee->user_id = $user->id;
                $employee->save();

                $empRole = Role::firstOrCreate(
                    ['name' => 'employee', 'guard_name' => 'web'],
                    ['description' => 'Employee', 'status' => 'ACTIVE']
                );
                $user->assignRole($empRole);
            }
        }

        $this->command->info("  Created tenant: {$config['company_name']} (owner: {$config['owner_email']})");
    }
}
