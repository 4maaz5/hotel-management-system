<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class DateTimeSetting extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'date_format',
        'time_format',
        'timezone',
    ];
}
