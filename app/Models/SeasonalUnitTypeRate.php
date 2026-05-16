<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class SeasonalUnitTypeRate extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'company_id',
        'seasonal_rate_id',
        'unit_type_id',
        'low_weekday_rate',
        'high_weekday_rate',
        'daily_min_rate',
    ];

    public function seasonalRate()
    {
        return $this->belongsTo(SeasonalRate::class);
    }

    public function unitType()
    {
        return $this->belongsTo(UnitType::class);
    }

}
