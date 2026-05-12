<?php

namespace Database\Seeders;

use App\Models\PropertyType;
use Illuminate\Database\Seeder;

class PropertyTypeSeeder extends Seeder
{
    public function run(): void
    {
        PropertyType::insert([
            ['code' => 'HOTEL', 'name_en' => 'Hotel', 'name_ar' => 'فندق'],
            ['code' => 'APARTMENT', 'name_en' => 'Apartment', 'name_ar' => 'شقق'],
            ['code' => 'SERVICED_APARTMENT', 'name_en' => 'Serviced Apartment', 'name_ar' => 'شقق فندقية'],
            ['code' => 'HOSTEL', 'name_en' => 'Hostel', 'name_ar' => 'نُزل'],
        ]);
    }
}
