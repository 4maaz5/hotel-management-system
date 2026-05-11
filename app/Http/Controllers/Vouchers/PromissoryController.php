<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Controllers\Controller;
use App\Models\PromissoryNote;
use App\Models\PaymentMethodConfig;
use App\Models\Bank;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\CostCenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PromissoryController extends Controller
{
    public function index(Request $request)
    {
        $query = PromissoryNote::with(['reservation', 'guest', 'paymentMethod.paymentMethod']);

        if ($request->voucher_number) {
            $query->where('voucher_number', 'like', '%' . $request->voucher_number . '%');
        }

        if ($request->guest_name) {
            $query->whereHas('guest', function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->guest_name . '%')
                  ->orWhere('last_name', 'like', '%' . $request->guest_name . '%');
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('date', '<=', $request->date_to);
        }

        $vouchers = $query->orderByDesc('id')->paginate(20);
        $paymentMethods = PaymentMethodConfig::with('paymentMethod')->where('is_active', 1)->get();
        $banks = Bank::where('is_active', 1)->get();
        $guests = $this->visibleGuests($request)
            ->where('is_active', 1)
            ->limit(50)
            ->get();
        $reservations = Reservation::whereIn('status', ['confirmed', 'checked_in'])->orderByDesc('id')->get();
        $allReservations = Reservation::orderByDesc('id')->get();
        $printingOption = \App\Models\PrintingOption::where('report_key', 'promissory_note')->first();
        $property = \App\Models\Property::current();

        return view('admin.voucher_promissory.index', compact('vouchers', 'paymentMethods', 'banks', 'guests', 'reservations', 'allReservations', 'printingOption', 'property'));
    }

    public function show($id)
    {
        $voucher = PromissoryNote::with(['reservation', 'guest', 'paymentMethod.paymentMethod', 'receivingBank'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'voucher' => [
                'id' => $voucher->id,
                'voucher_number' => $voucher->voucher_number,
                'voucher_type' => $voucher->voucher_type ?? 'manual',
                'date' => $voucher->date ? $voucher->date->format('Y-m-d') : null,
                'time' => $voucher->time ? $voucher->time->format('H:i:s') : null,
                'maturity_date' => $voucher->maturity_date ? $voucher->maturity_date->format('Y-m-d') : null,
                'reserved_to' => $voucher->reserved_to,
                'purpose' => $voucher->purpose,
                'maturity_place' => $voucher->maturity_place,
                'amount' => $voucher->amount,
                'collected_amount' => $voucher->collected_amount,
                'comment' => $voucher->comment,
                'payment_method_id' => $voucher->payment_method_id,
                'receiving_bank_id' => $voucher->receiving_bank_id,
                'transaction_number' => $voucher->transaction_number,
                'sending_bank_name' => $voucher->sending_bank_name,
                'cheque_number' => $voucher->cheque_number,
                'status' => $voucher->status,
                'guest_id' => $voucher->guest_id,
                'reservation_id' => $voucher->reservation_id,
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
        try {
            $userId = null;
            if (auth()->check()) {
                $userId = auth()->id();
            }

            $reservationId = $this->resolvedReservationId($request);
            $guestId = $this->resolvedGuestId($request);

            $voucher = PromissoryNote::create([
                'voucher_number' => PromissoryNote::generateVoucherNumber(),
                'voucher_type' => $reservationId ? 'reservation' : 'manual',
                'date' => $request->date ?? now()->toDateString(),
                'time' => $request->time ?? now()->format('H:i:s'),
                'maturity_date' => $request->maturity_date,
                'reserved_to' => $request->reserved_to,
                'purpose' => $request->purpose,
                'maturity_place' => $request->maturity_place,
                'amount' => $request->amount ?? 0,
                'collected_amount' => 0,
                'comment' => $request->comment,
                'reservation_id' => $reservationId,
                'guest_id' => $guestId,
                'created_by' => $userId,
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => __('dashboard.promissory_note_created'),
                'voucher' => $voucher
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $voucher = PromissoryNote::findOrFail($id);

        $voucher->update([
            'maturity_date' => $request->maturity_date,
            'reserved_to' => $request->reserved_to,
            'purpose' => $request->purpose,
            'maturity_place' => $request->maturity_place,
            'amount' => $request->amount,
            'comment' => $request->comment,
            'payment_method_id' => $request->payment_method_id ?? null,
            'receiving_bank_id' => $request->receiving_bank_id ?? null,
            'transaction_number' => $request->transaction_number ?? null,
            'sending_bank_name' => $request->sending_bank_name ?? null,
            'cheque_number' => $request->cheque_number ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('dashboard.promissory_note_updated')
        ]);
    }

    public function collect(Request $request, $id)
    {
        $voucher = PromissoryNote::findOrFail($id);
        
        $collectAmount = floatval($request->amount);
        $remainingAmount = $voucher->amount - $voucher->collected_amount;
        
        if ($collectAmount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Collect amount must be greater than 0'
            ]);
        }

        if ($collectAmount > $remainingAmount) {
            return response()->json([
                'success' => false,
                'message' => 'Collect amount cannot exceed remaining amount'
            ]);
        }

        $voucher->update([
            'collected_amount' => $voucher->collected_amount + $collectAmount,
            'payment_method_id' => $request->payment_method_id ?? $voucher->payment_method_id,
        ]);

        if ($voucher->comment) {
            $voucher->update(['comment' => $voucher->comment . "\n" . ($request->comment ?? '')]);
        } else {
            $voucher->update(['comment' => $request->comment ?? '']);
        }

        $newStatus = $voucher->collected_amount >= $voucher->amount ? 'collected' : 'partial';
        $voucher->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => __('dashboard.promissory_note_collected')
        ]);
    }

    public function cancel(Request $request, $id)
    {
        $voucher = PromissoryNote::findOrFail($id);

        $voucher->update([
            'status' => 'cancelled',
            'cancel_reason' => $request->cancel_reason,
            'cancelled_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('dashboard.promissory_note_cancelled')
        ]);
    }

    public function print(Request $request, $id)
    {
        $voucher = PromissoryNote::with(['reservation', 'guest', 'paymentMethod.paymentMethod', 'receivingBank'])->findOrFail($id);

        $printingOption = \App\Models\PrintingOption::where('report_key', 'promissory_note')->first();
        $globalSetting = \App\Models\PrintingOption::first();
        $property = \App\Models\Property::current(['commercialDetail']);

        return view('admin.voucher_promissory.print', compact('voucher', 'printingOption', 'globalSetting', 'property'));
    }

    public function linkReservation(Request $request, $id)
    {
        $voucher = PromissoryNote::findOrFail($id);
        $reservationId = $this->resolvedReservationId($request);
        $guestId = $request->filled('guest_id')
            ? $this->resolvedGuestId($request)
            : $voucher->guest_id;

        $voucher->update([
            'reservation_id' => $reservationId,
            'guest_id' => $guestId,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('dashboard.promissory_note_updated')
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

    protected function visibleGuests(Request $request): Builder
    {
        return Guest::query();
    }
}
