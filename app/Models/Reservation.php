<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use BelongsToCurrentProperty, BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'reservation_number',
        'property_id',
        'guest_id',
        'corporate_id',
        'unit_id',
        'source_id',
        'guest_class_id',
        'rate_plan_id',
        'payment_method_id',
        'check_in_date',
        'check_in_time',
        'check_out_date',
        'check_out_time',
        'nights',
        'adults',
        'children',
        'reservation_type',
        'daily_rate',
        'monthly_rate',
        'total_rent',
        'discount_type',
        'discount',
        'total_taxes_fees',
        'security_deposit',
        'paid_amount',
        'balance',
        'subtotal',
        'grand_total',
        'status',
        'is_confirmed',
        'booking_date',
        'notes',
        'penalty_id',
        'penalty_amount',
        'cancel_reason_id',
        'cancelled_at',
        'checked_in_at',
        'checked_out_at',
        'no_show_at',
        'shomoos_reported_at',
        'ntmp_reported_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'check_in_time' => 'datetime:H:i',
        'check_out_time' => 'datetime:H:i',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'no_show_at' => 'datetime',
        'shomoos_reported_at' => 'datetime',
        'ntmp_reported_at' => 'datetime',
        'is_confirmed' => 'boolean',
        'daily_rate' => 'decimal:2',
        'monthly_rate' => 'decimal:2',
        'total_rent' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_taxes_fees' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public static function generateReservationNumber()
    {
        $prefix = 'RES';
        $date = now()->format('ymd');
        $lastReservation = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastReservation) {
            $lastNumber = (int) substr($lastReservation->reservation_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix.$date.$newNumber;
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function corporate()
    {
        return $this->belongsTo(Corporate::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function ratePlan()
    {
        return $this->belongsTo(RatePlan::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethodConfig::class, 'payment_method_id');
    }

    public function source()
    {
        return $this->belongsTo(ReservationSourceSetting::class, 'source_id');
    }

    public function guestClass()
    {
        return $this->belongsTo(GuestClass::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function penalty()
    {
        return $this->belongsTo(Penalty::class);
    }

    public function penalties()
    {
        return $this->belongsToMany(Penalty::class, 'reservation_penalties')
            ->withPivot('amount', 'tax_amount', 'notes')
            ->withTimestamps();
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function reservationGuests()
    {
        return $this->hasMany(ReservationGuest::class);
    }

    public function occupants()
    {
        return $this->belongsToMany(Guest::class, 'reservation_guests')
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

    public function primaryOccupantRecord()
    {
        return $this->hasOne(ReservationGuest::class)->where('is_primary', true);
    }

    public function getTotalPenaltiesAttribute()
    {
        return $this->penalties->sum('pivot.amount');
    }

    public function getTotalPenaltyTaxAttribute()
    {
        return $this->penalties->sum('pivot.tax_amount');
    }
}
