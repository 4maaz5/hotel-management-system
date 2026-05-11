<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $companies = Company::all();
        $brands = Brand::all();
        $branches = Branch::all();
        $departments = Department::all();

        if ($companies->isEmpty() || $brands->isEmpty() || $branches->isEmpty() || $departments->isEmpty()) {
            $this->command->info('Missing required relations. Seed companies, brands, branches & departments first.');

            return;
        }

        for ($i = 1; $i <= 200; $i++) {

            // Select random branch
            $branch = $branches->random();

            // Company through branch
            $companyId = $branch->company_id;

            // Brand through branch
            $brandId = $branch->brand_id;

            // Departments belonging to this branch
            $branchDepartments = $departments->where('branch_id', $branch->id);

            if ($branchDepartments->isEmpty()) {
                continue;
            }

            $department = $branchDepartments->random();

            // Commission randomize
            $isCommission = $faker->boolean(20); // 20% employees have commission

            $commissionPercentage = $isCommission ? $faker->randomFloat(2, 1, 10) : null;
            $commissionType = $isCommission ? $faker->randomElement(['sales', 'profit', 'revenue']) : null;

            Employee::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'employee_id' => 'EMP'.str_pad($i, 4, '0', STR_PAD_LEFT),

                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,

                'designation' => $faker->jobTitle,
                'qr_code' => Str::uuid(),

                'company_id' => $companyId,
                'brand_id' => $brandId,
                'branch_id' => $branch->id,
                'department_id' => $department->id,

                'join_date' => $faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
                'residence_expiry_date' => $faker->dateTimeBetween('now', '+5 years')->format('Y-m-d'),

                'bank_name' => $faker->company.' Bank',
                'account_number' => $faker->bankAccountNumber,
                'image' => null,

                'base_salary' => $faker->numberBetween(2500, 15000),
                'salary_type' => $faker->randomElement(['monthly', 'weekly', 'daily', 'hourly']),

                'is_commission' => $isCommission,
                'commission_percentage' => $commissionPercentage,
                'commission_type' => $commissionType,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('200 employees seeded successfully with company, brand, branch, department, salary & commission!');
    }
}
