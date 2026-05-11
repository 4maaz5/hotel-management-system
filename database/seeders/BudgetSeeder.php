<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Budget;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BudgetSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();

        if ($branches->count() === 0) {
            $this->command->error('No branches found. Seed branches first.');

            return;
        }

        $statuses = ['On Track', 'At Risk', 'Over Spent'];

        foreach ($branches as $branch) {

            $total = rand(50000, 500000); // total budget
            $used = rand(0, $total);      // used budget
            $remaining = $total - $used;

            $start = Carbon::now()->subMonths(rand(0, 6))->toDateString();
            $end = Carbon::parse($start)->addMonths(rand(1, 12))->toDateString();

            // Optional: auto-set status based on used %
            $usedPercentage = ($used / $total) * 100;
            if ($usedPercentage < 70) {
                $status = 'On Track';
            } elseif ($usedPercentage < 100) {
                $status = 'At Risk';
            } else {
                $status = 'Over Spent';
            }

            Budget::create([
                'branch_id' => $branch->id,
                'total_budget' => $total,
                'used_budget' => $used,
                'remaining_budget' => $remaining,
                'start_date' => $start,
                'end_date' => $end,
                'status' => $status,
            ]);
        }

        $this->command->info('Budgets seeded successfully for all branches!');
    }
}
