<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\Region;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        if (Country::count() > 0) {
            return;
        }

        $sa = Country::create([
            'iso_code' => 'SA',
            'name_en' => 'Saudi Arabia',
            'name_ar' => 'المملكة العربية السعودية',
            'phone_code' => '+966',
            'currency_code' => 'SAR',
            'time_zone' => 'Asia/Riyadh',
        ]);

        $riyadhRegion = Region::create([
            'country_id' => $sa->id,
            'name_en' => 'Riyadh Region',
            'name_ar' => 'منطقة الرياض',
        ]);

        $riyadhCity = City::create([
            'region_id' => $riyadhRegion->id,
            'name_en' => 'Riyadh',
            'name_ar' => 'الرياض',
        ]);

        District::create([
            'city_id' => $riyadhCity->id,
            'name_en' => 'Olaya',
            'name_ar' => 'العليا',
        ]);
    }
}
