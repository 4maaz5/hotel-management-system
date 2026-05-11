<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();
        $users = User::all();

        if ($branches->count() === 0 || $users->count() === 0) {
            $this->command->error('No branches or users found. Seed them first.');

            return;
        }

        $types = ['Payroll', 'Expense', 'Bonus', 'Other'];

        for ($i = 0; $i < 200; $i++) {
            $branch = $branches->random();
            $user = $users->random();

            $type = $types[array_rand($types)];
            $amount = rand(100, 20000); // amount between 100 and 20,000
            $date = Carbon::now()->subDays(rand(1, 365))->toDateString();
            $description = "Auto-generated transaction of type {$type}";

            Transaction::create([
                'type' => $type,
                'amount' => $amount,
                'date' => $date,
                'description' => $description,
                'branch_id' => $branch->id,
                'created_by' => $user->id,
            ]);
        }

        $this->command->info('Seeded 200 random transactions successfully!');
    }
}
