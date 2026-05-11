<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PenaltySetting extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'early_checkin_detection',
        'late_checkout_detection',
        'skip_cancel_no_show_penalty',
    ];
}
