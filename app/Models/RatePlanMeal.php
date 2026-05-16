<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class RatePlanMeal extends Model
{
    use BelongsToTenant;

    protected $table = 'rate_plan_meals';

    protected $fillable = ['company_id', 'rate_plan_id', 'meal_name', 'adult_price', 'child_price'];

    public function ratePlan()
    {
        return $this->belongsTo(RatePlan::class);
    }
}
