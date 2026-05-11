<?php

namespace App\Jobs;

use App\Models\Guest;
use App\Models\LoyaltyAutoSetting;
use App\Models\LoyaltySetting;
use App\Models\Reservation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessLoyaltyUpgrade implements ShouldQueue
{
    // will be implemented later..............
    // use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // protected $guest;

    // public function __construct(Guest $guest)
    // {
    //     $this->guest = $guest;
    // }

    // public function handle()
    // {
    //     $general = LoyaltyAutoSetting::first();

    //     if (! $general || ! $general->auto_loyalty_upgrade) {
    //         return;
    //     }

    //     $guest = $this->guest;

    //     if (! $guest) {
    //         return;
    //     }

    //     $settings = LoyaltySetting::where('is_active', 1)
    //         ->orderByDesc('threshold_value')
    //         ->get();

    //     $reservationStats = [
    //         'total_reservations' => Reservation::where('guest_id', $guest->id)->count(),
    //         'total_spent' => Reservation::where('guest_id', $guest->id)->sum('total_amount'),
    //         'total_nights' => Reservation::where('guest_id', $guest->id)->sum('total_nights'),
    //     ];

    //     foreach ($settings as $setting) {

    //         $currentValue = $reservationStats[$setting->criteria] ?? 0;

    //         if ($currentValue >= $setting->threshold_value) {

    //             if ($guest->guest_class_id != $setting->upgrade_to_class_id) {

    //                 $guest->update([
    //                     'guest_class_id' => $setting->upgrade_to_class_id,
    //                 ]);
    //             }

    //             break;
    //         }
    //     }
    // }
}
