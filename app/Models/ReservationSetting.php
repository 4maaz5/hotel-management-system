<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationSetting extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'company_id',
        'default_view',
        'check_in_time',
        'check_out_time',
        'grace_period',
        'enable_previous_day_calculation',
        'previous_day_before',
        'auto_extend_daily',
        'auto_extend_monthly',
        'auto_extend_after',
        'restrict_unit_change',
        'unit_change_reason_required',
        'unit_allowance_period',
        'enable_unconfirmed_reservation',
        'enable_monthly_reservation',
        'auto_change_unconfirmed_to_noshow',
        'auto_noshow_time',
        'auto_noshow_reason_id',
        'auto_cancel_ota_reservation',
        'auto_cancel_reason_id',
        'enable_mandatory_checkin',
        'enable_close_reservation_with_balance',
        'reset_number_annually',
        'use_custom_rate_last_night',
    ];

    protected $casts = [
        'enable_previous_day_calculation' => 'boolean',
        'auto_extend_daily' => 'boolean',
        'auto_extend_monthly' => 'boolean',
        'restrict_unit_change' => 'boolean',
        'unit_change_reason_required' => 'boolean',
        'enable_unconfirmed_reservation' => 'boolean',
        'enable_monthly_reservation' => 'boolean',
        'auto_change_unconfirmed_to_noshow' => 'boolean',
        'auto_cancel_ota_reservation' => 'boolean',
        'enable_mandatory_checkin' => 'boolean',
        'enable_close_reservation_with_balance' => 'boolean',
        'reset_number_annually' => 'boolean',
        'use_custom_rate_last_night' => 'boolean',
    ];

    public static function getSettings()
    {
        return self::first() ?? self::createDefault();
    }

    public static function createDefault()
    {
        return self::create([
            'default_view' => 'list',
            'check_in_time' => '14:00:00',
            'check_out_time' => '12:00:00',
            'grace_period' => 1,
            'enable_previous_day_calculation' => true,
            'auto_extend_daily' => true,
            'auto_extend_monthly' => true,
            'restrict_unit_change' => false,
            'unit_change_reason_required' => false,
            'unit_allowance_period' => 1,
            'enable_unconfirmed_reservation' => true,
            'enable_monthly_reservation' => true,
            'auto_change_unconfirmed_to_noshow' => true,
            'auto_noshow_time' => '23:00:00',
            'reset_number_annually' => false,
            'use_custom_rate_last_night' => false,
        ]);
    }

    public function noshowReason()
    {
        return $this->belongsTo(CancelReason::class, 'auto_noshow_reason_id');
    }

    public function autoCancelReason()
    {
        return $this->belongsTo(CancelReason::class, 'auto_cancel_reason_id');
    }
}
