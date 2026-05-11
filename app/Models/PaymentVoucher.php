<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStaffCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Scopes\StaffCurrentPropertyScope;
use Illuminate\Database\Eloquent\Model;

class PaymentVoucher extends Model
{
    use BelongsToStaffCurrentProperty, BelongsToTenant;

    protected $fillable = [
        'property_id',
        'voucher_number',
        'voucher_type',
        'date',
        'time',
        'cost_center_id',
        'purpose',
        'comment',
        'vendor_name',
        'vendor_tax_no',
        'vendor_invoice_no',
        'amount',
        'vat_amount',
        'amount_before_vat',
        'apply_vat',
        'payment_method_id',
        'receiving_bank_id',
        'transaction_number',
        'sending_bank_name',
        'cheque_number',
        'reservation_id',
        'guest_id',
        'status',
        'cancel_reason',
        'cancelled_at',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'amount_before_vat' => 'decimal:2',
        'apply_vat' => 'boolean',
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

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethodConfig::class, 'payment_method_id');
    }

    public function receivingBank()
    {
        return $this->belongsTo(Bank::class, 'receiving_bank_id');
    }

    public function costCenter()
    {
        return $this->belongsTo(\App\Models\CostCenter::class, 'cost_center_id');
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
