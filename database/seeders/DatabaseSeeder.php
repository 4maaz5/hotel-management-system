<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            SubscriptionPlanSeeder::class,
            PermissionsSeeder::class,
        ]);

        $this->call(LocationSeeder::class);
        $this->call(PropertyTypeSeeder::class);
        $this->call(UnitTypeSeeder::class);
        $this->call(UnitClassSeeder::class);
        $this->call(HallTypeSeeder::class);
        $this->call(FacilityCategorySeeder::class);
        $this->call(FacilitySeeder::class);
        $this->call(ReportSettingSeeder::class);
        $this->call(PaymentMethodSeeder::class);
        $this->call(ReservationSourceSeeder::class);
        $this->call(CostCenterCategorySeeder::class);
        $this->call(PrintingOptionSeeder::class);
        // $this->call(DataIsolationTestSeeder::class);
        // $this->call(ReservationTestSeeder::class);
    }
}
