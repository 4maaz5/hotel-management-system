<?php

namespace Database\Seeders;

use App\Models\UnitType;
use Illuminate\Database\Seeder;

class UnitTypeSeeder extends Seeder
{
    public function run(): void
    {
        UnitType::insert([
            ['name' => 'Single Room', 'is_active' => true],
            ['name' => 'Room with Hall', 'is_active' => true],
            ['name' => 'Two Rooms', 'is_active' => true],
            ['name' => 'Two Rooms with Hall', 'is_active' => true],
            ['name' => '3 Rooms with Hall', 'is_active' => false],
            ['name' => '3 Rooms', 'is_active' => false],
            ['name' => '4 Rooms with Hall', 'is_active' => false],
            ['name' => '4 Rooms', 'is_active' => true],
            ['name' => 'Suite for marriage', 'is_active' => false],
            ['name' => 'Double Room', 'is_active' => true],
            ['name' => 'Twin Room', 'is_active' => false],
            ['name' => 'Triple Room', 'is_active' => true],
            ['name' => 'Quadruple Room', 'is_active' => false],
            ['name' => 'Suite', 'is_active' => false],
            ['name' => 'Vip', 'is_active' => false],
            ['name' => 'Two Connecting Rooms(5 adults)', 'is_active' => false],
            ['name' => 'Two Connecting Rooms(6 adults)', 'is_active' => true],
            ['name' => 'Two Bedroom Suite(6 adults)', 'is_active' => true],
            ['name' => 'Villa', 'is_active' => true],
            ['name' => 'Twin Room with Hall', 'is_active' => false],
            ['name' => 'Double Room with Hall', 'is_active' => true],
            ['name' => 'Tourist Villa', 'is_active' => true],
            ['name' => 'Vip Villa', 'is_active' => true],
            ['name' => 'Royal Villa', 'is_active' => true],
            ['name' => 'Small Pool Villa', 'is_active' => true],
            ['name' => 'Large Pool Villa', 'is_active' => true],
        ]);
    }
}
