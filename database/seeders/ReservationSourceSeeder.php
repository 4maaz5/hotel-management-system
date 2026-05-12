<?php

namespace Database\Seeders;

use App\Models\ReservationSourceMaster;
use Illuminate\Database\Seeder;

class ReservationSourceSeeder extends Seeder
{
    public function run(): void
    {
        $channels = [
            ['name' => 'Social Media', 'channel_type' => 'Primary'],
            ['name' => 'Airbnb', 'channel_type' => 'OTA'],
            ['name' => 'Nirvana', 'channel_type' => 'OTA'],
            ['name' => 'RezGain', 'channel_type' => 'OTA'],
            ['name' => 'Holdinn', 'channel_type' => 'OTA'],
            ['name' => 'OYO', 'channel_type' => 'OTA'],
            ['name' => 'Haraj', 'channel_type' => 'OTA'],
            ['name' => 'Agoda Web', 'channel_type' => 'OTA'],
            ['name' => 'Expedia', 'channel_type' => 'OTA'],
            ['name' => 'Almosafer Web', 'channel_type' => 'OTA'],
            ['name' => 'Flyin Web', 'channel_type' => 'OTA'],
            ['name' => 'Instagram', 'channel_type' => 'OTA'],
            ['name' => 'Booking Web', 'channel_type' => 'OTA'],
            ['name' => 'Contracted Corporate', 'channel_type' => null],
            ['name' => 'Reception', 'channel_type' => 'Primary'],
            ['name' => 'Yamsafer', 'channel_type' => 'OTA'],
            ['name' => 'Aqar', 'channel_type' => 'OTA'],
            ['name' => 'Website', 'channel_type' => 'Primary'],
        ];

        foreach ($channels as $channel) {
            ReservationSourceMaster::create([
                'name' => $channel['name'],
                'channel_type' => $channel['channel_type'],
                'icon' => null,
                'is_active' => true,
            ]);
        }
    }
}
