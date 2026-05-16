<?php

namespace Database\Seeders;

use App\Models\FacilityCategory;
use Illuminate\Database\Seeder;

class FacilityCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Recreation',
            'Fitness',
            'Food & Beverage',
            'Business Services',
            'Parking',
        ] as $name) {
            FacilityCategory::updateOrCreate(
                ['name' => $name],
                ['status' => true]
            );
        }
    }
}
