<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class SpecialUnitTypeRate extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'special_rate_id',
        'unit_type_id',
        'rate',
        'min_rate',
    ];

    public function seasonalRate()
    {
        return $this->belongsTo(SpecialRate::class);
    }

    public function unitType()
    {
        return $this->belongsTo(UnitType::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
