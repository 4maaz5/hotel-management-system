<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\FacilityCategory;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $categories = FacilityCategory::whereIn('name', [
            'Recreation',
            'Fitness',
            'Food & Beverage',
            'Business Services',
            'Parking',
        ])->get()->keyBy('name');

        foreach ([
            'Recreation' => [
                'Swimming Pool',
                'Kids Play Area',
                'Spa',
                'Sauna',
            ],
            'Fitness' => [
                'Gym',
                'Yoga Room',
            ],
            'Food & Beverage' => [
                'Restaurant',
                'Coffee Shop',
                'Room Service',
            ],
            'Business Services' => [
                'Meeting Room',
                'Business Center',
            ],
            'Parking' => [
                'Valet Parking',
                'Self Parking',
            ],
        ] as $categoryName => $facilityNames) {
            $category = $categories->get($categoryName);

            if (! $category) {
                continue;
            }

            foreach ($facilityNames as $facilityName) {
                Facility::updateOrCreate(
                    [
                        'facility_category_id' => $category->id,
                        'name' => $facilityName,
                    ],
                    ['status' => true]
                );
            }
        }
    }
}
