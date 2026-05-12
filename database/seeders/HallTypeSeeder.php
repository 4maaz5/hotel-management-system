<?php

namespace Database\Seeders;

use App\Models\HallType;
use Illuminate\Database\Seeder;

class HallTypeSeeder extends Seeder
{
    public function run(): void
    {
        HallType::insert([
            ['name' => 'Without HAll', 'is_active' => true],
            ['name' => 'Small Hall', 'is_active' => true],
            ['name' => 'Medium Hall', 'is_active' => true],
            ['name' => 'Large Hall', 'is_active' => true],
            ['name' => 'Deluxe Hall', 'is_active' => true],
        ]);
    }
}
