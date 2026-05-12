<?php

namespace Database\Seeders;

use App\Models\GuestClass;
use Illuminate\Database\Seeder;

class GuestClassSeeder extends Seeder
{
    public function run(): void
    {
        GuestClass::insert([
            [
                'class_name' => json_encode(['en' => 'Regular', 'ar' => 'عادي']),
                'is_active' => true,
            ],
            [
                'class_name' => json_encode(['en' => 'VIP', 'ar' => 'VIP']),
                'is_active' => true,
            ],
        ]);
    }
}
