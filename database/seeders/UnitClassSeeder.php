<?php

namespace Database\Seeders;

use App\Models\UnitClass;
use Illuminate\Database\Seeder;

class UnitClassSeeder extends Seeder
{
    public function run(): void
    {
        UnitClass::insert([
            ['name' => 'Chalet', 'is_active' => true],
            ['name' => 'Apartment', 'is_active' => true],
            ['name' => 'Room', 'is_active' => true],
            ['name' => 'Unit', 'is_active' => true],
        ]);
    }
}
