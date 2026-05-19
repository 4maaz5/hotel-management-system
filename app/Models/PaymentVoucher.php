<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStaffCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Scopes\StaffCurrentPropertyScope;
use App\Models\Scopes\TenantScope;
use App\Support\PropertyContext;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Model;

class PaymentVoucher extends Model
{
    use BelongsToStaffCurrentProperty, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'branch_id',
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
        return $this->belongsTo(Property::class, 'branch_id', 'branch_id');
    }

    public static function generateVoucherNumber(?int $companyId = null, ?int $branchId = null): string
    {
        $companyId ??= app(TenantContext::class)->id();
        $branchId ??= app(PropertyContext::class)->branchId();

        $lastVoucher = self::withoutGlobalScope(StaffCurrentPropertyScope::class)
            ->withoutGlobalScope(TenantScope::class)
            ->when($companyId, fn ($query) => $query->where('company_id', $companyId))
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->orderByDesc('voucher_number')
            ->first();

        $nextNumber = $lastVoucher ? ((int) $lastVoucher->voucher_number) + 1 : 1;

        return str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
