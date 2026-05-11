<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('brands')->insert([
            [
                'company_id' => 1,
                'name' => 'Alpha Electronics',
                'logo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'name' => 'Alpha Home Goods',
                'logo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'company_id' => 2,
                'name' => 'Beta Cleaning Solutions',
                'logo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 2,
                'name' => 'Beta Maintenance',
                'logo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'company_id' => 3,
                'name' => 'Gamma Industrial Parts',
                'logo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 3,
                'name' => 'Gamma Engineering',
                'logo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'company_id' => 4,
                'name' => 'Delta Builders',
                'logo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 4,
                'name' => 'Delta Contracting',
                'logo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'company_id' => 5,
                'name' => 'Epsilon Software',
                'logo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 5,
                'name' => 'Epsilon Hardware',
                'logo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
