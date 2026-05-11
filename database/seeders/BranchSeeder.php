<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [];

        for ($i = 1; $i <= 30; $i++) {

            // Random company ID (1–5)
            $companyId = rand(1, 5);

            // Random brand ID inside all 10 brands (1–10)
            // (Assuming you seeded 10 brands earlier)
            $brandId = rand(1, 10);

            $name = 'Branch '.$i;
            $manager = 'Manager '.$i;

            $branches[] = [
                'company_id' => $companyId,
                'brand_id' => $brandId,
                'name' => $name,
                'location' => 'Location '.rand(1, 100),
                'manager' => $manager,
                'email' => 'branch'.$i.'@example.com',
                'phone' => '+9665'.rand(10000000, 99999999),
                'status' => rand(0, 1) ? 'Active' : 'Inactive',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('branches')->insert($branches);
    }
}
