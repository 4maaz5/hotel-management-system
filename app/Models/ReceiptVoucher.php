<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStaffCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Scopes\StaffCurrentPropertyScope;
use Illuminate\Database\Eloquent\Model;

class ReceiptVoucher extends Model
{
    use BelongsToStaffCurrentProperty, BelongsToTenant;

    protected $fillable = [
        'property_id',
        'reservation_id',
        'guest_id',
        'corporate_id',
        'payment_method_id',
        'voucher_number',
        'amount',
        'received_from_name',
        'purpose',
        'comment',
        'date',
        'time',
        'status',
        'cancel_reason',
        'cancelled_at',
        'created_by',
        'receiving_bank_id',
        'transaction_number',
        'sending_bank_name',
        'cheque_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
        'time' => 'datetime',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function corporate()
    {
        return $this->belongsTo(Corporate::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethodConfig::class, 'payment_method_id');
    }

    public function receivingBank()
    {
        return $this->belongsTo(Bank::class, 'receiving_bank_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public static function generateVoucherNumber()
    {
        $lastVoucher = self::withoutGlobalScope(StaffCurrentPropertyScope::class)
            ->orderBy('id', 'desc')
            ->first();
        $nextNumber = $lastVoucher ? $lastVoucher->id + 1 : 1;
        return str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
