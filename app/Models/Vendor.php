<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStaffCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use BelongsToStaffCurrentProperty, BelongsToTenant, HasFactory;

    protected $fillable = [
        'property_id',
        'name',
        'dial_code',
        'phone',
        'email',
        'vat_registration_number',
        'commercial_registration_number',
        'postal_code',
        'description',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
