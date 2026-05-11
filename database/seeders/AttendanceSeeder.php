<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();

        if ($employees->count() === 0) {
            $this->command->error('No employees found! Add employees before seeding attendance.');

            return;
        }

        $recordsToInsert = 1000;
        $inserted = 0;

        while ($inserted < $recordsToInsert) {

            foreach ($employees as $employee) {

                if ($inserted >= $recordsToInsert) {
                    break;
                }

                // Random date within last 12 months
                $date = Carbon::now()
                    ->subDays(rand(1, 365))
                    ->toDateString();

                // Prevent duplicate employee/date
                if (Attendance::where('employee_id', $employee->id)->where('date', $date)->exists()) {
                    continue;
                }

                // Random attendance status
                $status = collect(['Present', 'Absent', 'Leave'])->random();

                // Times only when Present
                $checkIn = $status === 'Present' ? Carbon::createFromTime(rand(8, 10), rand(0, 59)) : null;
                $checkOut = $status === 'Present' ? Carbon::createFromTime(rand(16, 19), rand(0, 59)) : null;

                // Overtime for Present only
                $overtime = $status === 'Present' ? rand(0, 4) : 0;

                Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $date,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'status' => $status,
                    'overtime_hours' => $overtime,
                ]);

                $inserted++;
            }
        }

        $this->command->info("Seeded $inserted attendance records successfully!");
    }
}
