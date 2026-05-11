<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatePlanUnitType extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'company_id',
        'rate_plan_id',
        'unit_type_id',
        'daily_rate',
        'monthly_rate',
    ];

    public function ratePlan()
    {
        return $this->belongsTo(RatePlan::class);
    }

    public function unitType()
    {
        return $this->belongsTo(UnitType::class);
    }
}
