<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PayrollSeeder extends Seeder
{
    public function run(): void
    {
        // Select up to 100 random employees
        $employees = Employee::inRandomOrder()->take(100)->get();

        if ($employees->isEmpty()) {
            $this->command->error('No employees found. Please seed employees first.');

            return;
        }

        $monthsBack = 12; // Last 12 months

        foreach ($employees as $employee) {

            for ($i = 0; $i < $monthsBack; $i++) {

                $month = Carbon::now()->subMonths($i)->format('Y-m');

                // Avoid duplicate payroll per employee per month
                if (Payroll::where('employee_id', $employee->id)->where('month', $month)->exists()) {
                    continue;
                }

                // GET employee salary (fallback if null)
                $basicSalary = $employee->base_salary ?? rand(3000, 7000);

                // Random allowance
                $allowance = rand(300, 1500);

                // Random deductions
                $deductions = rand(0, 500);

                // Commission calculation if employee has commission enabled
                $commissionAmount = 0;
                if ($employee->is_commission && $employee->commission_percentage) {
                    // Fake sales amount for payroll month
                    $salesAmount = rand(1000, 10000);

                    // Calculate commission: sales × percentage
                    $commissionAmount = ($salesAmount * $employee->commission_percentage) / 100;
                }

                // Net pay = basic + allowance - deductions
                $netPay = ($basicSalary + $allowance) - $deductions;

                // Total amount (including commission)
                $totalAmount = $netPay + $commissionAmount;

                // Random payroll status
                $status = ['Pending', 'Paid', 'Cancelled'][rand(0, 2)];

                Payroll::create([
                    'employee_id' => $employee->id,
                    'month' => $month,
                    'basic_salary' => $basicSalary,
                    'allowance' => $allowance,
                    'deductions' => $deductions,
                    'commission_amount' => $commissionAmount,
                    'net_pay' => $netPay,
                    'total_amount' => $totalAmount,
                    'status' => $status,
                ]);
            }
        }

        $this->command->info('Payroll seeded successfully for 100 employees (last 12 months).');
    }
}
