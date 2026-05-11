<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStaffCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use BelongsToStaffCurrentProperty, BelongsToTenant, HasFactory;

    protected $fillable = [
        'property_id',
        'first_name',
        'second_name',
        'middle_name',
        'last_name',
        'gender',
        'date_of_birth',
        'guest_class_id',
        'nationality',
        'nationality_code',
        'guest_type',
        'id_type',
        'id_number',
        'id_issue_country',
        'id_expiry_date',
        'visa_number',
        'arrival_from',
        'id_serial',
        'mobile_dial_code',
        'mobile_number',
        'email',
        'work_place',
        'work_phone',
        'address',
        'car_license_plate',
        'profile_image',
        'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'id_expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function guestClass()
    {
        return $this->belongsTo(GuestClass::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function reservationGuestRecords()
    {
        return $this->hasMany(ReservationGuest::class);
    }

    public function stays()
    {
        return $this->belongsToMany(Reservation::class, 'reservation_guests')
            ->withPivot([
                'tenant_id',
                'property_id',
                'is_primary',
                'relationship',
                'check_in_status',
                'check_out_status',
            ])
            ->withTimestamps();
    }

    public function getFullNameAttribute()
    {
        $parts = array_filter([
            $this->first_name,
            $this->second_name,
            $this->middle_name,
            $this->last_name
        ]);
        return implode(' ', $parts);
    }

    public function getMobileAttribute()
    {
        if ($this->mobile_dial_code && $this->mobile_number) {
            return $this->mobile_dial_code . ' ' . $this->mobile_number;
        }
        return $this->mobile_number;
    }
}
