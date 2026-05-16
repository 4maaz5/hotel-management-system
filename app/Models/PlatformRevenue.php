<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformRevenue extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'amount_collected',
        'commission_amount',
        'payment_date',
    ];

    protected $casts = [
        'amount_collected' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    /* ================= Relationships ================= */

    public function subscription()
    {
        return $this->belongsTo(PlatformSubscription::class, 'subscription_id');
    }
}
