<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationGuest extends Model
{
    use BelongsToCurrentProperty, BelongsToTenant, HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'reservation_id',
        'guest_id',
        'is_primary',
        'relationship',
        'check_in_status',
        'check_out_status',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}
