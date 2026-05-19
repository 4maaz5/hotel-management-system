<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStaffCurrentProperty;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Scopes\StaffCurrentPropertyScope;
use App\Support\PropertyContext;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DropCashVoucher extends Model
{
    use BelongsToStaffCurrentProperty, BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'company_id',
        'branch_id',
        'voucher_number',
        'user_id',
        'date_from',
        'date_to',
        'amount',
        'drop_method',
        'bank_id',
        'paid_to',
        'purpose',
        'comment',
        'created_by',
    ];

    protected $casts = [
        'date_from' => 'datetime',
        'date_to' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bank()
    {
        return $this->belongsTo(\App\Models\Bank::class, 'bank_id');
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
            ->withTrashed()
            ->when($companyId, fn ($query) => $query->where('company_id', $companyId))
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->orderByDesc('voucher_number')
            ->first();

        $nextNumber = $lastVoucher ? ((int) $lastVoucher->voucher_number) + 1 : 1;

        return str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public static function getDropMethods()
    {
        return [
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'other' => 'Other',
        ];
    }
}
