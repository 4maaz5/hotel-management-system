<?php

namespace Database\Seeders;

use App\Models\CostCenterCategory;
use Illuminate\Database\Seeder;

class CostCenterCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'General',
            'Communications and Internet Expenses',
            'Petty Cash',
            'Fines and Violations Expenses',
            'Electricity',
            'Hospitality Expenses',
            'Employees Expenses',
            'Periodic Subscription Expenses',
            'Operating Expenses',
            'Consultation and Review Expenses',
            'Comissions for online Reservations sites',
            'Banking Expenses',
            'Maintenance and Cleaning Expenses',
            'Travel and Transportation Expenses',
            'Salaries and Wages Expenses',
            'Government Expenses',
            'Advertising Expenses',
            'Equipment and decoration Expenses',
            'Renting Expenses',
        ];

        foreach ($categories as $name) {
            CostCenterCategory::create(['name' => $name]);
        }
    }
}
