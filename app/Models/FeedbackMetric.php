<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class FeedbackMetric extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function getMetricOptions()
    {
        return [
            'Staff',
            'Cleanliness',
            'Comfort',
            'Value for money',
            'Location',
            'Free Wi-Fi',
            'Property Facilities',
            'Unit Facilities',
        ];
    }
}
