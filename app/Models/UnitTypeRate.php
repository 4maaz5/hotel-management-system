<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class UnitTypeRate extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'unit_type_id',
        'low_weekday_rate',
        'high_weekday_rate',
        'daily_min_rate',
        'monthly_rate',
        'monthly_min_rate',
        'is_active',
    ];

    public function unitType()
    {
        return $this->belongsTo(UnitType::class);
    }
}
