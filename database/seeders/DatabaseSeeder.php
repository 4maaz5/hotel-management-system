<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SubscriptionPlanSeeder::class,
            PermissionsSeeder::class,
            CompanySeeder::class,
            BrandSeeder::class,
            BranchSeeder::class,
            DepartmentSeeder::class,
            EmployeeSeeder::class,
            AttendanceSeeder::class,
            LeavesSeeder::class,
            PayrollSeeder::class,
            IncomeSeeder::class,
            TransactionSeeder::class,
            BudgetSeeder::class,
            AdministrativeExpensesSeeder::class,
            EmployeeDocumentsSeeder::class,
            CompanyDocumentsSeeder::class,
            UserSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
