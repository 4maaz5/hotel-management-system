<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckExpiryNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $today = Carbon::today();

        app(\App\Http\Controllers\DashboardController::class)
            ->runDocumentChecks($today);

        app(\App\Http\Controllers\DashboardController::class)
            ->runInsuranceChecks($today);

        // Driver Documents
        app(\App\Http\Controllers\DashboardController::class)
            ->runDriverDocChecks($today);

        // Vehicle Documents
        app(\App\Http\Controllers\DashboardController::class)
            ->runVehicleDocChecks($today);
    }
}
