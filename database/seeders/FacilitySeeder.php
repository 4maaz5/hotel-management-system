<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\FacilityCategory;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $recreation = FacilityCategory::where('name', 'Recreation')->first();
        $fitness = FacilityCategory::where('name', 'Fitness')->first();

        Facility::insert([
            [
                'facility_category_id' => $recreation->id,
                'name' => 'Swimming Pool',
                'status' => true,
            ],
            [
                'facility_category_id' => $recreation->id,
                'name' => 'Kids Play Area',
                'status' => true,
            ],
            [
                'facility_category_id' => $fitness->id,
                'name' => 'Gym',
                'status' => true,
            ],
        ]);
    }
}
