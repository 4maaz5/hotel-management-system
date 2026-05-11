<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class NightAuditSetting extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'is_active',
        'allowance_period',
        'cancellation_threshold',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function getSettings()
    {
        return self::first() ?? new self();
    }
}
