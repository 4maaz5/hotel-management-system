<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->index();
            $table->string('default_view')->default('list');
            $table->time('check_in_time')->default('14:00:00');
            $table->time('check_out_time')->default('12:00:00');
            $table->integer('grace_period')->default(1);
            $table->boolean('enable_previous_day_calculation')->default(true);
            $table->time('previous_day_before')->nullable();
            $table->boolean('auto_extend_daily')->default(true);
            $table->boolean('auto_extend_monthly')->default(true);
            $table->time('auto_extend_after')->nullable();
            $table->boolean('restrict_unit_change')->default(false);
            $table->boolean('unit_change_reason_required')->default(false);
            $table->integer('unit_allowance_period')->default(1);
            $table->boolean('enable_unconfirmed_reservation')->default(true);
            $table->boolean('enable_monthly_reservation')->default(true);
            $table->boolean('auto_change_unconfirmed_to_noshow')->default(true);
            $table->time('auto_noshow_time')->nullable();
            $table->unsignedBigInteger('auto_noshow_reason_id')->nullable();
            $table->boolean('auto_cancel_ota_reservation')->default(false);
            $table->unsignedBigInteger('auto_cancel_reason_id')->nullable();
            $table->boolean('enable_mandatory_checkin')->default(false);
            $table->boolean('enable_close_reservation_with_balance')->default(false);
            $table->boolean('reset_number_annually')->default(false);
            $table->boolean('use_custom_rate_last_night')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_settings');
    }
};

