<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Controllers\Controller;
use App\Models\PaymentVoucher;
use App\Models\PaymentMethodConfig;
use App\Models\Bank;
use App\Models\Guest;
use App\Models\Corporate;
use App\Models\CostCenter;
use App\Models\Reservation;
use App\Models\Vendor;
use App\Models\TaxFeeCustomization;
use App\Support\PropertyContext;
use App\Support\TenantContext;
use App\Support\UserActivityLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentVoucher::with(['reservation', 'guest', 'paymentMethod.paymentMethod']);

        if ($request->voucher_number) {
            $query->where('voucher_number', 'like', '%' . $request->voucher_number . '%');
        }

        if ($request->vendor_name) {
            $query->where('vendor_name', 'like', '%' . $request->vendor_name . '%');
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->payment_method) {
            $query->where('payment_method_id', $request->payment_method);
        }

        if ($request->date_from) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('date', '<=', $request->date_to);
        }

        $vouchers = $query->orderBy('created_at', 'desc')->paginate(20);
        $paymentMethods = PaymentMethodConfig::with('paymentMethod')->get();
        $banks = Bank::where('is_active', 1)->get();
        $costCenters = CostCenter::where('is_active', 1)->get();
        $vendors = Vendor::where('is_active', 1)->limit(50)->get();
        $taxConfigs = TaxFeeCustomization::where('is_expenses', true)
            ->where('type', 'tax')
            ->where(function($q) {
                $q->where('end_date', '>=', now()->toDateString())->orWhereNull('end_date');
            })
            ->get();
        $printingOption = \App\Models\PrintingOption::where('report_key', 'payment_voucher')->first();

        return view('admin.voucher_payment.index', compact('vouchers', 'paymentMethods', 'banks', 'costCenters', 'vendors', 'taxConfigs', 'printingOption'));
    }

    public function searchVendors(Request $request)
    {
        $search = $request->get('q', '');
        $vendors = Vendor::where('is_active', 1)
            ->where(function($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('vat_registration_number', 'like', '%' . $search . '%');
            })
            ->limit(20)
            ->get();

        return response()->json($vendors);
    }

    public function show($id)
    {
        $voucher = PaymentVoucher::with(['reservation', 'guest', 'paymentMethod.paymentMethod', 'receivingBank'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'voucher' => [
                'id' => $voucher->id,
                'voucher_number' => $voucher->voucher_number,
                'date' => $voucher->date ? $voucher->date->format('Y-m-d') : null,
                'time' => $voucher->time ? $voucher->time->format('H:i:s') : null,
                'cost_center_id' => $voucher->cost_center_id,
                'purpose' => $voucher->purpose,
                'comment' => $voucher->comment,
                'vendor_name' => $voucher->vendor_name,
                'vendor_tax_no' => $voucher->vendor_tax_no,
                'vendor_invoice_no' => $voucher->vendor_invoice_no,
                'amount' => $voucher->amount,
                'vat_amount' => $voucher->vat_amount,
                'amount_before_vat' => $voucher->amount_before_vat,
                'apply_vat' => $voucher->apply_vat,
                'payment_method_id' => $voucher->payment_method_id,
                'receiving_bank_id' => $voucher->receiving_bank_id,
                'transaction_number' => $voucher->transaction_number,
                'sending_bank_name' => $voucher->sending_bank_name,
                'cheque_number' => $voucher->cheque_number,
                'status' => $voucher->status,
                'voucher_type' => $voucher->voucher_type ?? 'payment',
                'guest_id' => $voucher->guest_id,
                'cancel_reason' => $voucher->cancel_reason,
                'guest' => $voucher->guest ? [
                    'id' => $voucher->guest->id,
                    'name' => $voucher->guest->first_name . ' ' . $voucher->guest->last_name,
                ] : null,
                'reservation' => $voucher->reservation ? [
                    'id' => $voucher->reservation->id,
                    'reservation_number' => $voucher->reservation->reservation_number,
                ] : null,
                'payment_method' => $voucher->paymentMethod ? [
                    'id' => $voucher->paymentMethod->id,
                    'name' => $voucher->paymentMethod->paymentMethod->name ?? '',
                ] : null,
            ],
        ]);
    }

    public function store(Request $request)
    {
        [$companyId, $branchId] = $this->currentTenantAndBranch($request);
        $this->validateVoucherRequest($request, $companyId, $branchId);

        $reservationId = $this->resolvedReservationId($request);
        $guestId = $this->resolvedGuestId($request);

        $voucher = PaymentVoucher::create([
            'company_id' => $companyId,
            'branch_id' => $branchId,
            'voucher_number' => PaymentVoucher::generateVoucherNumber($companyId, $branchId),
            'date' => $request->date ?? now()->toDateString(),
            'time' => $request->time ?? now()->format('H:i:s'),
            'cost_center_id' => $request->cost_center_id ?? null,
            'purpose' => $request->purpose,
            'comment' => $request->comment,
            'vendor_name' => $request->vendor_name,
            'vendor_tax_no' => $request->vendor_tax_no ?? null,
            'vendor_invoice_no' => $request->vendor_invoice_no ?? null,
            'amount' => $request->amount,
            'vat_amount' => $request->vat_amount ?? 0,
            'amount_before_vat' => $request->amount_before_vat ?? 0,
            'apply_vat' => $request->apply_vat ?? false,
            'payment_method_id' => $request->payment_method_id,
            'receiving_bank_id' => $request->receiving_bank_id ?? null,
            'transaction_number' => $request->transaction_number ?? null,
            'sending_bank_name' => $request->sending_bank_name ?? null,
            'cheque_number' => $request->cheque_number ?? null,
            'reservation_id' => $reservationId,
            'guest_id' => $guestId,
            'created_by' => auth()->id(),
        ]);

        app(UserActivityLogger::class)->log(
            'payments',
            'created',
            $voucher,
            "Created payment voucher {$voucher->voucher_number}",
            [],
            $this->paymentVoucherActivityData($voucher)
        );

        return response()->json([
            'success' => true,
            'message' => __('dashboard.payment_voucher_created'),
            'voucher' => $voucher
        ]);
    }

    public function update(Request $request, $id)
    {
        $voucher = PaymentVoucher::findOrFail($id);
        $companyId = (int) ($voucher->company_id ?: app(TenantContext::class)->id() ?: $request->user()?->company_id);
        $branchId = (int) ($voucher->branch_id ?: app(PropertyContext::class)->branchId() ?: $request->user()?->branch_id);
        $this->validateVoucherRequest($request, $companyId, $branchId);

        $before = $this->paymentVoucherActivityData($voucher);
        $guestId = $request->filled('guest_id')
            ? $this->resolvedGuestId($request)
            : $voucher->guest_id;

        $voucher->update([
            'date' => $request->date,
            'time' => $request->time,
            'cost_center_id' => $request->cost_center_id ?? null,
            'purpose' => $request->purpose,
            'comment' => $request->comment,
            'vendor_name' => $request->vendor_name,
            'vendor_tax_no' => $request->vendor_tax_no ?? null,
            'vendor_invoice_no' => $request->vendor_invoice_no ?? null,
            'amount' => $request->amount,
            'vat_amount' => $request->vat_amount ?? 0,
            'amount_before_vat' => $request->amount_before_vat ?? 0,
            'apply_vat' => $request->apply_vat ?? false,
            'payment_method_id' => $request->payment_method_id,
            'receiving_bank_id' => $request->receiving_bank_id ?? null,
            'transaction_number' => $request->transaction_number ?? null,
            'sending_bank_name' => $request->sending_bank_name ?? null,
            'cheque_number' => $request->cheque_number ?? null,
            'voucher_type' => $request->voucher_type ?? 'payment',
            'guest_id' => $guestId,
        ]);

        app(UserActivityLogger::class)->log(
            'payments',
            'updated',
            $voucher,
            "Updated payment voucher {$voucher->voucher_number}",
            $before,
            $this->paymentVoucherActivityData($voucher->fresh())
        );

        return response()->json([
            'success' => true,
            'message' => __('dashboard.payment_voucher_updated')
        ]);
    }

    public function cancel(Request $request, $id)
    {
        $voucher = PaymentVoucher::findOrFail($id);
        $before = $this->paymentVoucherActivityData($voucher);

        $voucher->update([
            'status' => 'cancelled',
            'cancel_reason' => $request->cancel_reason,
            'cancelled_at' => now(),
        ]);

        app(UserActivityLogger::class)->log(
            'payments',
            'cancelled',
            $voucher,
            "Cancelled payment voucher {$voucher->voucher_number}",
            $before,
            $this->paymentVoucherActivityData($voucher->fresh())
        );

        return response()->json([
            'success' => true,
            'message' => __('dashboard.payment_voucher_cancelled')
        ]);
    }

    public function print(Request $request, $id)
    {
        $voucher = PaymentVoucher::with(['reservation', 'guest', 'paymentMethod.paymentMethod', 'receivingBank'])->findOrFail($id);

        $printingOption = \App\Models\PrintingOption::where('report_key', 'payment_voucher')->first();
        $globalSetting = \App\Models\PrintingOption::first();
        $property = \App\Models\Property::current(['commercialDetail']);

        return view('admin.voucher_payment.print', compact('voucher', 'printingOption', 'globalSetting', 'property'));
    }

    protected function resolvedReservationId(Request $request): ?int
    {
        if (! $request->filled('reservation_id')) {
            return null;
        }

        return Reservation::query()
            ->findOrFail($request->integer('reservation_id'))
            ->getKey();
    }

    protected function resolvedGuestId(Request $request): ?int
    {
        if (! $request->filled('guest_id')) {
            return null;
        }

        return $this->visibleGuests($request)
            ->findOrFail($request->integer('guest_id'))
            ->getKey();
    }

    protected function visibleGuests(Request $request): Builder
    {
        return Guest::query();
    }

    protected function currentTenantAndBranch(Request $request): array
    {
        $companyId = app(TenantContext::class)->id() ?: $request->user()?->company_id;
        $branchId = app(PropertyContext::class)->branchId() ?: $request->user()?->branch_id;

        if (! $companyId || ! $branchId) {
            throw ValidationException::withMessages([
                'branch_id' => __('Please select or create a branch first.'),
            ]);
        }

        return [(int) $companyId, (int) $branchId];
    }

    protected function validateVoucherRequest(Request $request, int $companyId, int $branchId): void
    {
        $request->validate([
            'date' => ['nullable', 'date'],
            'time' => ['nullable', 'date_format:H:i'],
            'cost_center_id' => [
                'nullable',
                Rule::exists('cost_centers', 'id')->where(fn ($query) => $query->where('company_id', $companyId)),
            ],
            'purpose' => ['nullable', 'string', 'max:255'],
            'comment' => ['nullable', 'string'],
            'vendor_name' => ['nullable', 'string', 'max:255'],
            'vendor_tax_no' => ['nullable', 'string', 'max:255'],
            'vendor_invoice_no' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'vat_amount' => ['nullable', 'numeric', 'min:0'],
            'amount_before_vat' => ['nullable', 'numeric', 'min:0'],
            'apply_vat' => ['nullable', 'boolean'],
            'vat_type' => ['nullable', 'string', 'max:50'],
            'vat_percentage' => ['nullable', 'numeric', 'min:0'],
            'payment_method_id' => [
                'required',
                Rule::exists('payment_method_configs', 'id')->where(fn ($query) => $query->where('company_id', $companyId)),
            ],
            'receiving_bank_id' => [
                'nullable',
                Rule::exists('banks', 'id')->where(fn ($query) => $query->where('company_id', $companyId)),
            ],
            'transaction_number' => ['nullable', 'string', 'max:255'],
            'sending_bank_name' => ['nullable', 'string', 'max:255'],
            'cheque_number' => ['nullable', 'string', 'max:255'],
            'reservation_id' => [
                'nullable',
                Rule::exists('reservations', 'id')
                    ->where(fn ($query) => $query->where('company_id', $companyId)->where('branch_id', $branchId)),
            ],
            'guest_id' => [
                'nullable',
                Rule::exists('guests', 'id')
                    ->where(fn ($query) => $query->where('company_id', $companyId)->where('branch_id', $branchId)),
            ],
            'voucher_type' => ['nullable', Rule::in(['payment', 'refund'])],
        ]);
    }

    protected function paymentVoucherActivityData(PaymentVoucher $voucher): array
    {
        return [
            'voucher_number' => $voucher->voucher_number,
            'voucher_type' => $voucher->voucher_type,
            'status' => $voucher->status,
            'amount' => (float) $voucher->amount,
            'purpose' => $voucher->purpose,
            'payment_method_id' => $voucher->payment_method_id,
            'reservation_id' => $voucher->reservation_id,
            'guest_id' => $voucher->guest_id,
        ];
    }
}
