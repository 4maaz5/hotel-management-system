<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PropertyAdditionalDetail extends Model
{
    use BelongsToCurrentProperty, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'property_id',
        'distance_from_haram_km',
        'description_en',
        'description_ar',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
