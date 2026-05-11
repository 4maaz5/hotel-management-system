<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $employeeRole = 'employee';

        $employees = Employee::whereNotNull('email')->get();

        foreach ($employees as $employee) {

            // Skip if user already exists
            if ($employee->user_id) {
                continue;
            }

            $password = ucfirst($employee->first_name).'@123';

            $user = User::firstOrCreate(
                ['email' => $employee->email],
                [
                    'name' => $employee->first_name.' '.$employee->last_name,
                    'branch_id' => $employee->branch_id,
                    'password' => Hash::make($password),
                ]
            );

            // Assign employee role
            if (! $user->hasRole($employeeRole)) {
                $user->assignRole($employeeRole);
            }

            // Update employee with user_id
            $employee->user_id = $user->id;
            $employee->save();
        }

        $this->command->info('Users created for all employees successfully!');
    }
}
