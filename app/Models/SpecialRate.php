<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class SpecialRate extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function unitRates()
    {
        return $this->hasMany(SpecialUnitTypeRate::class, 'special_rate_id');
    }

    public function isActiveOn($date)
    {
        return $this->is_active &&
               $date >= $this->start_date &&
               $date <= $this->end_date;
    }
}
