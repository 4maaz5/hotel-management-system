<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStaffCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Corporate extends Model
{
    use BelongsToStaffCurrentProperty, BelongsToTenant, HasFactory;

    protected $fillable = [
        'branch_id',
        'name',
        'postal_code',
        'vat_registration_number',
        'commercial_registration_number',
        'discount_type',
        'discount_value',
        'country',
        'city',
        'district',
        'street',
        'building_number',
        'secondary_number',
        'address',
        'email',
        'phone',
        'contact_person_name',
        'contact_person_dial_code',
        'contact_person_phone',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'branch_id', 'branch_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
