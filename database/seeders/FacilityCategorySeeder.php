<?php

namespace Database\Seeders;

use App\Models\FacilityCategory;
use Illuminate\Database\Seeder;

class FacilityCategorySeeder extends Seeder
{
    public function run(): void
    {
        FacilityCategory::insert([
            ['name' => 'Recreation', 'status' => true],
            ['name' => 'Fitness', 'status' => true],
            ['name' => 'Food & Beverage', 'status' => true],
            ['name' => 'Business Services', 'status' => true],
            ['name' => 'Parking', 'status' => true],
        ]);
    }
}
