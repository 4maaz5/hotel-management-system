<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Leave;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class LeavesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Get all employees
        $employees = Employee::pluck('id')->toArray();

        if (empty($employees)) {
            $this->command->info('⚠ No employees found. Please seed employees first.');

            return;
        }

        for ($i = 0; $i < 100; $i++) {

            $startDate = $faker->dateTimeBetween('-1 year', 'now');
            $endDate = (clone $startDate)->modify('+'.rand(1, 10).' days');

            $diff = (new \DateTime($startDate->format('Y-m-d')))->diff(new \DateTime($endDate->format('Y-m-d')));

            Leave::create([
                'employee_id' => $faker->randomElement($employees),
                'leave_type' => $faker->randomElement([
                    'sick', 'annual', 'maternity', 'emergency',
                    'unpaid', 'paternity', 'compensatory', 'bereavement',
                ]),
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'total_days' => $diff->days + 1,
                'payment_type' => 'paid',
                'reason' => $faker->sentence(8),
                'status' => $faker->randomElement(['pending', 'approved', 'rejected', 'in_progress']),
            ]);
        }

        $this->command->info(' 100 fake leave records inserted successfully!');
    }
}
