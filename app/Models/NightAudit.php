<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\BelongsToStaffCurrentProperty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NightAudit extends Model
{
    use BelongsToStaffCurrentProperty, BelongsToTenant, HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'start_date_time',
        'end_date_time',
        'status',
        'user_id',
        'period_date_from',
        'period_date_to',
        'night_count',
        'notes',
        'financial_summary',
    ];

    protected $casts = [
        'start_date_time' => 'datetime',
        'end_date_time' => 'datetime',
        'period_date_from' => 'date',
        'period_date_to' => 'date',
        'night_count' => 'integer',
        'financial_summary' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilter($query, $filters)
    {
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (! empty($filters['date_from'])) {
            $query->whereDate('start_date_time', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $query->whereDate('start_date_time', '<=', $filters['date_to']);
        }

        return $query;
    }

    public static function calculateFinancialSummary($periodDateFrom, $periodDateTo, ?int $companyId = null, ?int $branchId = null)
    {
        $cashMethodIds = \App\Models\PaymentMethodConfig::idsForMethodNameLike('%cash%');
        $cardMethodIds = \App\Models\PaymentMethodConfig::idsForMethodNames(['Credit Cards', 'Mada', 'GCCNET', 'Digital Payments']);
        $knownMethodIds = array_values(array_unique(array_merge($cashMethodIds, $cardMethodIds)));

        $receipts = \App\Models\ReceiptVoucher::whereBetween('date', [$periodDateFrom, $periodDateTo])
            ->when($companyId, fn ($query) => $query->where('company_id', $companyId))
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $payments = \App\Models\PaymentVoucher::whereBetween('date', [$periodDateFrom, $periodDateTo])
            ->when($companyId, fn ($query) => $query->where('company_id', $companyId))
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $dropCash = \App\Models\DropCashVoucher::whereBetween('date_from', [$periodDateFrom, $periodDateTo])
            ->when($companyId, fn ($query) => $query->where('company_id', $companyId))
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->get();

        $cashReceived = $receipts->whereIn('payment_method_id', $cashMethodIds)->sum('amount');
        $cardReceived = $receipts->whereIn('payment_method_id', $cardMethodIds)->sum('amount');
        $otherReceived = $receipts->whereNotIn('payment_method_id', $knownMethodIds)->sum('amount');
        $totalReceipts = $receipts->sum('amount');

        $cashPayments = $payments->whereIn('payment_method_id', $cashMethodIds)->where('voucher_type', '!=', 'refund')->sum('amount');
        $cardPayments = $payments->whereIn('payment_method_id', $cardMethodIds)->where('voucher_type', '!=', 'refund')->sum('amount');
        $otherPayments = $payments->whereNotIn('payment_method_id', $knownMethodIds)->where('voucher_type', '!=', 'refund')->sum('amount');
        $totalPayments = $payments->where('voucher_type', '!=', 'refund')->sum('amount');

        $securityDepositsRefunded = $payments->where('voucher_type', 'refund')->sum('amount');
        $securityDepositsReceived = $receipts->filter(function ($r) {
            if ($r->reservation_id) {
                $res = \App\Models\Reservation::find($r->reservation_id);

                return $res && $res->security_deposit > 0;
            }

            return false;
        })->sum('amount');

        $dropCashTotal = $dropCash->where('drop_method', 'cash')->sum('amount');
        $bankTransfers = $dropCash->where('drop_method', 'bank_transfer')->sum('amount');

        $receiptCount = $receipts->count();
        $paymentCount = $payments->where('voucher_type', '!=', 'refund')->count();
        $refundCount = $payments->where('voucher_type', 'refund')->count();

        return [
            'period_date_from' => $periodDateFrom,
            'period_date_to' => $periodDateTo,
            'generated_at' => now()->toDateTimeString(),
            'receipts' => [
                'cash_received' => round($cashReceived, 2),
                'card_received' => round($cardReceived, 2),
                'other_received' => round($otherReceived, 2),
                'total_received' => round($totalReceipts, 2),
                'count' => $receiptCount,
            ],
            'payments' => [
                'cash_paid' => round($cashPayments, 2),
                'card_paid' => round($cardPayments, 2),
                'other_paid' => round($otherPayments, 2),
                'total_paid' => round($totalPayments, 2),
                'count' => $paymentCount,
            ],
            'security_deposits' => [
                'received' => round($securityDepositsReceived, 2),
                'refunded' => round($securityDepositsRefunded, 2),
                'refund_count' => $refundCount,
            ],
            'drop_cash' => [
                'cash_drops' => round($dropCashTotal, 2),
                'bank_transfers' => round($bankTransfers, 2),
                'total_drops' => round($dropCashTotal + $bankTransfers, 2),
            ],
            'net_change' => round($totalReceipts - $totalPayments - $dropCashTotal, 2),
        ];
    }
}
