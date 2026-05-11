<?php

namespace Database\Seeders;

use App\Models\AdministrativeExpense;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AdministrativeExpensesSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();
        $users = User::all();

        if ($branches->count() === 0 || $users->count() === 0) {
            $this->command->error('No branches or users found. Seed them first.');

            return;
        }

        $expenseItems = [
            'Stationery',
            'Office Supplies',
            'Internet Charges',
            'Electricity Bill',
            'Water Bill',
            'Maintenance',
            'Cleaning Services',
            'Software Subscription',
            'Printing Costs',
            'Travel Expenses',
        ];

        foreach ($branches as $branch) {

            for ($i = 0; $i < 10; $i++) {

                $item = $expenseItems[array_rand($expenseItems)];

                $amount = rand(100, 5000);
                $quantity = rand(1, 10);

                $invoiceNumber = 'INV-'.strtoupper(uniqid());

                $description = "Expense for {$item} in branch {$branch->name}";
                $date = Carbon::now()->subDays(rand(1, 365))->toDateString();

                $createdBy = $users->random()->id;

                AdministrativeExpense::create([
                    'branch_id' => $branch->id,
                    'item_name' => $item,
                    'invoice_number' => $invoiceNumber,
                    'quantity' => $quantity,
                    'file' => null, // seeder leaves file empty
                    'amount' => $amount,
                    'description' => $description,
                    'expense_date' => $date,
                    'created_by' => $createdBy,
                ]);
            }
        }

        $this->command->info('Administrative expenses (with invoice + quantity) seeded successfully!');
    }
}
