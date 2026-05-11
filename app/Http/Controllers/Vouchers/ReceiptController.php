<?php

namespace App\Http\Controllers\Vouchers;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Corporate;
use App\Models\Guest;
use App\Models\PaymentMethodConfig;
use App\Models\Reservation;
use App\Models\ReceiptVoucher;
use App\Support\UserActivityLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReceiptController extends Controller
{
    public function index(Request $request)
    {
        $query = ReceiptVoucher::with(['reservation', 'guest', 'corporate', 'paymentMethod.paymentMethod']);

        if ($request->voucher_number) {
            $query->where('voucher_number', 'like', '%'.$request->voucher_number.'%');
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->guest_name) {
            $query->where('received_from_name', 'like', '%'.$request->guest_name.'%');
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

        $vouchers = $query->orderBy('created_at', 'desc')->where('status', 'active')->paginate(20);
        $paymentMethods = PaymentMethodConfig::with('paymentMethod')->get();
        $printingOption = \App\Models\PrintingOption::where('report_key', 'receipt_voucher')->first();
        $guests = $this->visibleGuests($request)
            ->select('id', 'first_name', 'last_name', 'mobile_number', 'email')
            ->limit(50)
            ->get();
        $corporates = $this->visibleCorporates($request)
            ->select('id', 'name', 'phone', 'email')
            ->limit(50)
            ->get();
        $banks = Bank::where('is_active', 1)->get();

        return view('admin.receipt.index', compact('vouchers', 'paymentMethods', 'printingOption', 'guests', 'corporates', 'banks'));
    }

    public function searchGuests(Request $request)
    {
        $search = $request->get('q', '');
        $guests = $this->visibleGuests($request)
            ->select('id', 'first_name', 'last_name', 'mobile_number', 'email')
            ->where(function ($query) use ($search) {
                $query->where('first_name', 'like', '%'.$search.'%')
                    ->orWhere('last_name', 'like', '%'.$search.'%')
                    ->orWhere('mobile_number', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            })
            ->limit(20)
            ->get();

        return response()->json($guests);
    }

    public function searchCorporates(Request $request)
    {
        $search = $request->get('q', '');
        $corporates = $this->visibleCorporates($request)
            ->select('id', 'name', 'phone', 'email')
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            })
            ->limit(20)
            ->get();

        return response()->json($corporates);
    }

    public function show($id)
    {
        $voucher = ReceiptVoucher::with(['reservation', 'guest', 'corporate', 'paymentMethod.paymentMethod', 'receivingBank'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'voucher' => [
                'id' => $voucher->id,
                'voucher_number' => $voucher->voucher_number,
                'date' => $voucher->date ? $voucher->date->format('Y-m-d') : null,
                'time' => $voucher->time ? $voucher->time->format('H:i:s') : null,
                'amount' => $voucher->amount,
                'received_from_name' => $voucher->received_from_name,
                'purpose' => $voucher->purpose,
                'comment' => $voucher->comment,
                'status' => $voucher->status,
                'guest_id' => $voucher->guest_id,
                'corporate_id' => $voucher->corporate_id,
                'payment_method_id' => $voucher->payment_method_id,
                'receiving_bank_id' => $voucher->receiving_bank_id,
                'transaction_number' => $voucher->transaction_number,
                'sending_bank_name' => $voucher->sending_bank_name,
                'cheque_number' => $voucher->cheque_number,
                'cancel_reason' => $voucher->cancel_reason,
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

    public function cancel(Request $request, $id)
    {
        $voucher = ReceiptVoucher::findOrFail($id);
        $before = $this->receiptVoucherActivityData($voucher);

        $voucher->update([
            'status' => 'cancelled',
            'cancel_reason' => $request->cancel_reason,
            'cancelled_at' => now(),
        ]);

        app(UserActivityLogger::class)->log(
            'receipts',
            'cancelled',
            $voucher,
            "Cancelled receipt voucher {$voucher->voucher_number}",
            $before,
            $this->receiptVoucherActivityData($voucher->fresh())
        );

        return response()->json([
            'success' => true,
            'message' => __('dashboard.receipt_voucher_cancelled'),
        ]);
    }

    public function print(Request $request, $id)
    {
        $voucher = ReceiptVoucher::with(['reservation', 'guest', 'corporate', 'paymentMethod.paymentMethod'])->findOrFail($id);

        $printingOption = \App\Models\PrintingOption::where('report_key', 'receipt_voucher')->first();
        $globalSetting = \App\Models\PrintingOption::first();
        $property = \App\Models\Property::current(['commercialDetail']);

        return view('admin.receipt.print', compact('voucher', 'printingOption', 'globalSetting', 'property'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatedVoucherData($request);
        $reservationId = $this->resolvedReservationId($request);
        $guestId = $this->resolvedGuestId($request);
        $corporateId = $this->resolvedCorporateId($request);

        $voucher = ReceiptVoucher::create([
            'reservation_id' => $reservationId,
            'guest_id' => $guestId,
            'corporate_id' => $corporateId,
            'payment_method_id' => $validated['payment_method_id'],
            'voucher_number' => ReceiptVoucher::generateVoucherNumber(),
            'amount' => $validated['amount'],
            'received_from_name' => $validated['received_from_name'],
            'purpose' => $validated['purpose'],
            'comment' => $validated['comment'] ?? null,
            'date' => $validated['date'],
            'time' => $validated['time'],
            'created_by' => auth()->id(),
            'receiving_bank_id' => $validated['receiving_bank_id'] ?? null,
            'transaction_number' => $validated['transaction_number'] ?? null,
            'sending_bank_name' => $validated['sending_bank_name'] ?? null,
            'cheque_number' => $validated['cheque_number'] ?? null,
        ]);

        app(UserActivityLogger::class)->log(
            'receipts',
            'created',
            $voucher,
            "Created receipt voucher {$voucher->voucher_number}",
            [],
            $this->receiptVoucherActivityData($voucher)
        );

        if ($voucher->reservation_id) {
            NotificationHelper::notifyPayment(auth()->id(), $voucher->amount, $voucher->reservation->reservation_number ?? null);
        }

        return response()->json([
            'success' => true,
            'message' => __('dashboard.receipt_voucher_created'),
            'voucher' => $voucher,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $this->validatedVoucherData($request);
        $voucher = ReceiptVoucher::findOrFail($id);
        $before = $this->receiptVoucherActivityData($voucher);
        $guestId = $request->filled('guest_id')
            ? $this->resolvedGuestId($request)
            : null;
        $corporateId = $request->filled('corporate_id')
            ? $this->resolvedCorporateId($request)
            : null;

        $voucher->update([
            'date' => $validated['date'],
            'time' => $validated['time'],
            'received_from_name' => $validated['received_from_name'],
            'guest_id' => $guestId,
            'corporate_id' => $corporateId,
            'amount' => $validated['amount'],
            'payment_method_id' => $validated['payment_method_id'],
            'purpose' => $validated['purpose'],
            'comment' => $validated['comment'] ?? null,
            'receiving_bank_id' => $validated['receiving_bank_id'] ?? null,
            'transaction_number' => $validated['transaction_number'] ?? null,
            'sending_bank_name' => $validated['sending_bank_name'] ?? null,
            'cheque_number' => $validated['cheque_number'] ?? null,
        ]);

        app(UserActivityLogger::class)->log(
            'receipts',
            'updated',
            $voucher,
            "Updated receipt voucher {$voucher->voucher_number}",
            $before,
            $this->receiptVoucherActivityData($voucher->fresh())
        );

        return response()->json([
            'success' => true,
            'message' => __('dashboard.receipt_voucher_updated'),
        ]);
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

    protected function resolvedCorporateId(Request $request): ?int
    {
        if (! $request->filled('corporate_id')) {
            return null;
        }

        return $this->visibleCorporates($request)
            ->findOrFail($request->integer('corporate_id'))
            ->getKey();
    }

    protected function visibleGuests(Request $request): Builder
    {
        return Guest::query();
    }

    protected function visibleCorporates(Request $request): Builder
    {
        return Corporate::query();
    }

    protected function validatedVoucherData(Request $request): array
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'received_from_name' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'max:255'],
            'payment_method_id' => ['required', 'integer', 'exists:payment_method_configs,id'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'comment' => ['nullable', 'string'],
            'reservation_id' => ['nullable', 'integer'],
            'guest_id' => ['nullable', 'integer'],
            'corporate_id' => ['nullable', 'integer'],
            'receiving_bank_id' => ['nullable', 'integer', 'exists:banks,id'],
            'transaction_number' => ['nullable', 'string', 'max:255'],
            'sending_bank_name' => ['nullable', 'string', 'max:255'],
            'cheque_number' => ['nullable', 'string', 'max:255'],
        ]);

        $paymentMethod = PaymentMethodConfig::with('paymentMethod')->findOrFail($validated['payment_method_id']);
        $paymentMethodName = strtolower($paymentMethod->paymentMethod?->name ?? $paymentMethod->name ?? '');

        $errors = [];

        if (str_contains($paymentMethodName, 'mada')) {
            $this->collectRequiredFieldError($errors, $validated, 'receiving_bank_id', 'A receiving bank is required for Mada payments.');
            $this->collectRequiredFieldError($errors, $validated, 'transaction_number', 'A transaction number is required for Mada payments.');
        }

        if (str_contains($paymentMethodName, 'cheque')) {
            $this->collectRequiredFieldError($errors, $validated, 'receiving_bank_id', 'A receiving bank is required for cheque payments.');
            $this->collectRequiredFieldError($errors, $validated, 'sending_bank_name', 'A sending bank name is required for cheque payments.');
            $this->collectRequiredFieldError($errors, $validated, 'cheque_number', 'A cheque number is required for cheque payments.');
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        return $validated;
    }

    protected function collectRequiredFieldError(array &$errors, array $validated, string $field, string $message): void
    {
        if (! filled($validated[$field] ?? null)) {
            $errors[$field] = $message;
        }
    }

    protected function receiptVoucherActivityData(ReceiptVoucher $voucher): array
    {
        return [
            'voucher_number' => $voucher->voucher_number,
            'status' => $voucher->status,
            'amount' => (float) $voucher->amount,
            'received_from_name' => $voucher->received_from_name,
            'purpose' => $voucher->purpose,
            'payment_method_id' => $voucher->payment_method_id,
            'reservation_id' => $voucher->reservation_id,
        ];
    }
}
