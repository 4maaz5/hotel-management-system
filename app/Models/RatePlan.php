<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class RatePlan extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'name', 'description', 'is_active'];

    public function unitTypeRates()
    {
        return $this->belongsToMany(UnitType::class, 'rate_plan_unit_types')
            ->withPivot('daily_rate', 'monthly_rate')
            ->withTimestamps();
    }

    public function meals()
    {
        return $this->hasMany(RatePlanMeal::class);
    }
}
