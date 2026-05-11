<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\Income;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class IncomeSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();
        $employees = Employee::all();

        if ($branches->isEmpty()) {
            $this->command->error('No branches found. Seed branches first.');

            return;
        }

        $types = ['Product Sale', 'Service', 'Consultancy', 'Subscription', 'Maintenance', 'Other'];
        $paymentTypes = ['Cash', 'Bank Transfer', 'Cheque', 'Credit Card'];

        for ($i = 0; $i < 100; $i++) {

            $branch = $branches->random();

            // Filter employees who belong to this branch
            $branchEmployees = $employees->where('branch_id', $branch->id);

            // Random employee or null (to allow income without assigned employee)
            $employeeId = $branchEmployees->isNotEmpty()
                ? $branchEmployees->random()->id
                : null;

            Income::create([
                'branch_id' => $branch->id,
                'employee_id' => $employeeId, // may be null
                'type' => $types[array_rand($types)],
                'amount' => rand(500, 10000),
                'payment_type' => $paymentTypes[array_rand($paymentTypes)],
                'income_date' => Carbon::now()->subDays(rand(1, 365))->toDateString(),
            ]);
        }

        $this->command->info('100 income records seeded successfully!');
    }
}
