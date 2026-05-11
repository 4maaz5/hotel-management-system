<?php

namespace App\Services\Chatbot;

use App\Helpers\NotificationHelper;
use App\Models\ChatSession;
use App\Models\Guest;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Reservation;
use App\Models\ReservationSourceSetting;
use App\Models\ReservationSetting;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReservationBookingService
{
    public function __construct(
        private readonly AvailabilityToolService $availabilityService,
        private readonly ReservationPricingService $pricingService,
    ) {
    }

    public function prepareBooking(ChatSession $session, array $parameters): array
    {
        $missing = [];

        foreach (['check_in_date', 'check_out_date', 'guest_name', 'phone'] as $field) {
            if (blank($parameters[$field] ?? '')) {
                $missing[] = $field;
            }
        }

        if ($missing !== []) {
            return [
                'status' => 'needs_more_info',
                'missing_fields' => $missing,
                'message' => 'Guest details and stay dates are required before booking.',
            ];
        }

        try {
            $checkIn = Carbon::parse($parameters['check_in_date'])->startOfDay();
            $checkOut = Carbon::parse($parameters['check_out_date'])->startOfDay();
        } catch (\Throwable) {
            return [
                'status' => 'failed',
                'message' => 'The provided dates are invalid.',
            ];
        }

        if ($checkOut->lessThanOrEqualTo($checkIn)) {
            return [
                'status' => 'failed',
                'message' => 'Check-out date must be after check-in date.',
            ];
        }

        $unit = $this->availabilityService->firstMatchingUnit($parameters);

        if (! $unit) {
            return [
                'status' => 'failed',
                'message' => 'No available unit matches the requested stay.',
            ];
        }

        $quote = $this->pricingService->quote(
            $unit,
            $checkIn,
            $checkOut,
            ($parameters['reservation_type'] ?? '') === 'monthly' ? 'monthly' : 'daily'
        );

        $proposal = [
            'guest_name' => trim((string) $parameters['guest_name']),
            'phone' => trim((string) $parameters['phone']),
            'email' => trim((string) ($parameters['email'] ?? '')),
            'notes' => trim((string) ($parameters['notes'] ?? '')),
            'check_in_date' => $checkIn->toDateString(),
            'check_out_date' => $checkOut->toDateString(),
            'reservation_type' => $quote['reservation_type'],
            'adults' => max(1, (int) ($parameters['adults'] ?? 1)),
            'children' => max(0, (int) ($parameters['children'] ?? 0)),
            'room_type' => $unit->unitType?->name ?? '',
            'unit_id' => $unit->id,
            'unit_number' => $unit->unit_number,
            'quote' => $quote,
        ];

        $session->update([
            'status' => 'pending_confirmation',
            'context' => array_merge($session->context ?? [], [
                'pending_action' => 'create_booking',
                'pending_summary' => 'Waiting for booking confirmation.',
                'booking_proposal' => $proposal,
            ]),
        ]);

        return [
            'status' => 'requires_confirmation',
            'message' => 'Booking proposal prepared and waiting for confirmation.',
            'data' => $proposal,
        ];
    }

    public function confirmBooking(ChatSession $session): array
    {
        $proposal = data_get($session->context ?? [], 'booking_proposal');

        if (! is_array($proposal)) {
            return [
                'status' => 'failed',
                'message' => 'No pending booking proposal was found.',
            ];
        }

        $unit = Unit::query()->with('unitType')->find($proposal['unit_id'] ?? null);

        if (! $unit) {
            return [
                'status' => 'failed',
                'message' => 'The selected unit is no longer available.',
            ];
        }

        $stillAvailable = Reservation::query()
            ->where('unit_id', $unit->id)
            ->whereNotIn('status', ['cancelled', 'checked_out', 'no_show'])
            ->where('check_in_date', '<', $proposal['check_out_date'])
            ->where('check_out_date', '>', $proposal['check_in_date'])
            ->doesntExist();

        if (! $stillAvailable) {
            $this->clearPendingState($session);

            return [
                'status' => 'failed',
                'message' => 'The unit became unavailable before confirmation. Please search again.',
            ];
        }

        $guest = $this->findOrCreateGuest($proposal);
        $settings = ReservationSetting::getSettings();
        $quote = $proposal['quote'];
        $isWebsiteGuest = $this->isWebsiteGuestSession($session);
        $reservation = $isWebsiteGuest
            ? $this->createWebsiteGuestReservation($session, $guest, $settings, $proposal, $quote)
            : $this->createStaffReservation($session, $guest, $settings, $proposal, $quote);

        if ($session->user_id) {
            NotificationHelper::notifyNewReservation($session->user_id, $reservation->load('guest'));
        }

        $this->clearPendingState($session);

        if ($isWebsiteGuest) {
            return [
                'status' => 'completed',
                'message' => 'Your booking request has been submitted successfully. Please save your reference number: '.$reservation->reservation_number.'.',
                'data' => [
                    'reservation_id' => $reservation->id,
                    'booking_reference' => $reservation->reservation_number,
                    'guest_name' => $proposal['guest_name'],
                    'unit_number' => $proposal['unit_number'],
                    'check_in_date' => $proposal['check_in_date'],
                    'check_out_date' => $proposal['check_out_date'],
                    'grand_total' => $quote['grand_total'],
                ],
            ];
        }

        return [
            'status' => 'completed',
            'message' => 'Reservation created successfully.',
            'data' => [
                'reservation_id' => $reservation->id,
                'reservation_number' => $reservation->reservation_number,
                'guest_name' => $proposal['guest_name'],
                'unit_number' => $proposal['unit_number'],
                'check_in_date' => $proposal['check_in_date'],
                'check_out_date' => $proposal['check_out_date'],
                'grand_total' => $quote['grand_total'],
            ],
        ];
    }

    private function createStaffReservation(
        ChatSession $session,
        Guest $guest,
        ReservationSetting $settings,
        array $proposal,
        array $quote
    ): Reservation {
        return DB::transaction(function () use ($session, $guest, $settings, $proposal, $quote) {
            $reservation = Reservation::create([
                'reservation_number' => Reservation::generateReservationNumber(),
                'property_id' => $session->property_id,
                'guest_id' => $guest->id,
                'unit_id' => $proposal['unit_id'],
                'check_in_date' => $proposal['check_in_date'],
                'check_in_time' => $settings->check_in_time,
                'check_out_date' => $proposal['check_out_date'],
                'check_out_time' => $settings->check_out_time,
                'nights' => $quote['nights'],
                'adults' => $proposal['adults'],
                'children' => $proposal['children'],
                'reservation_type' => $proposal['reservation_type'],
                'daily_rate' => $quote['daily_rate'],
                'monthly_rate' => $quote['monthly_rate'],
                'total_rent' => $quote['total_rent'],
                'discount' => $quote['discount'],
                'total_taxes_fees' => $quote['total_taxes_fees'],
                'security_deposit' => $quote['security_deposit'],
                'paid_amount' => $quote['paid_amount'],
                'balance' => $quote['balance'],
                'subtotal' => $quote['subtotal'],
                'grand_total' => $quote['grand_total'],
                'status' => 'confirmed',
                'is_confirmed' => true,
                'booking_date' => now()->toDateString(),
                'notes' => $proposal['notes'] ?: null,
                'created_by' => $session->user_id,
            ]);

            $this->createInvoice($reservation, $proposal, $quote);

            return $reservation;
        });
    }

    private function createWebsiteGuestReservation(
        ChatSession $session,
        Guest $guest,
        ReservationSetting $settings,
        array $proposal,
        array $quote
    ): Reservation {
        return DB::transaction(function () use ($session, $guest, $settings, $proposal, $quote) {
            $reservation = Reservation::create([
                'reservation_number' => Reservation::generateReservationNumber(),
                'property_id' => $session->property_id,
                'guest_id' => $guest->id,
                'unit_id' => $proposal['unit_id'],
                'source_id' => $this->websiteSourceId(),
                'check_in_date' => $proposal['check_in_date'],
                'check_in_time' => $settings->check_in_time,
                'check_out_date' => $proposal['check_out_date'],
                'check_out_time' => $settings->check_out_time,
                'nights' => $quote['nights'],
                'adults' => $proposal['adults'],
                'children' => $proposal['children'],
                'reservation_type' => $proposal['reservation_type'],
                'daily_rate' => $quote['daily_rate'],
                'monthly_rate' => $quote['monthly_rate'],
                'total_rent' => $quote['total_rent'],
                'discount' => $quote['discount'],
                'total_taxes_fees' => $quote['total_taxes_fees'],
                'security_deposit' => $quote['security_deposit'],
                'paid_amount' => $quote['paid_amount'],
                'balance' => $quote['balance'],
                'subtotal' => $quote['subtotal'],
                'grand_total' => $quote['grand_total'],
                'status' => 'pending',
                'is_confirmed' => false,
                'booking_date' => now()->toDateString(),
                'notes' => $proposal['notes'] ?: null,
            ]);

            $this->createInvoice($reservation, $proposal, $quote);

            return $reservation;
        });
    }

    private function createInvoice(Reservation $reservation, array $proposal, array $quote): void
    {
        $invoice = Invoice::create([
            'reservation_id' => $reservation->id,
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'issue_date' => now()->toDateString(),
            'due_date' => $proposal['check_in_date'],
            'subtotal' => $quote['subtotal'],
            'discount' => 0,
            'discount_amount' => $quote['discount'],
            'tax_amount' => $quote['total_taxes_fees'],
            'security_deposit' => $quote['security_deposit'],
            'total' => $quote['grand_total'] + $quote['security_deposit'],
            'paid_amount' => $quote['paid_amount'],
            'balance' => $quote['balance'],
            'status' => $quote['paid_amount'] >= $quote['grand_total'] ? 'paid' : 'pending',
            'payment_method' => null,
            'qr_code' => $this->safeQrCode(),
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Room Charges ('.$quote['nights'].' night'.($quote['nights'] > 1 ? 's' : '').')',
            'quantity' => $quote['nights'],
            'unit_price' => $quote['daily_rate'],
            'total' => $quote['total_rent'],
        ]);

        if ((float) $quote['total_taxes_fees'] > 0) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => 'Taxes & Fees',
                'quantity' => 1,
                'unit_price' => $quote['total_taxes_fees'],
                'total' => $quote['total_taxes_fees'],
            ]);
        }
    }

    private function websiteSourceId(): ?int
    {
        $source = ReservationSourceSetting::query()
            ->where(function ($query) {
                $query
                    ->where('report_name', 'like', '%website%')
                    ->orWhereHas('masterSource', function ($masterSourceQuery) {
                        $masterSourceQuery->where('name', 'like', '%Website%');
                    });
            })
            ->first();

        return $source?->id;
    }

    private function isWebsiteGuestSession(ChatSession $session): bool
    {
        return $session->user_id === null
            && data_get($session->context ?? [], 'channel') === 'website';
    }

    private function findOrCreateGuest(array $proposal): Guest
    {
        [$dialCode, $mobileNumber] = $this->splitPhone($proposal['phone']);
        [$firstName, $secondName, $lastName] = $this->splitName($proposal['guest_name']);

        $guest = Guest::query()
            ->where('mobile_number', $mobileNumber)
            ->when($dialCode, fn ($query) => $query->where('mobile_dial_code', $dialCode))
            ->first();

        if ($guest) {
            $guest->update(array_filter([
                'email' => $proposal['email'] ?: null,
                'first_name' => $firstName,
                'second_name' => $secondName ?: null,
                'last_name' => $lastName,
                'is_active' => true,
            ], fn ($value) => $value !== null));

            return $guest;
        }

        return Guest::create([
            'first_name' => $firstName,
            'second_name' => $secondName ?: null,
            'last_name' => $lastName,
            'mobile_dial_code' => $dialCode,
            'mobile_number' => $mobileNumber,
            'email' => $proposal['email'] ?: null,
            'guest_type' => 'individual',
            'is_active' => true,
        ]);
    }

    private function splitName(string $fullName): array
    {
        $parts = collect(explode(' ', preg_replace('/\s+/', ' ', trim($fullName)) ?? ''))
            ->filter()
            ->values();

        if ($parts->count() < 2) {
            return [$parts->first() ?: 'Guest', null, 'Guest'];
        }

        $firstName = (string) $parts->shift();
        $lastName = (string) $parts->pop();
        $secondName = $parts->implode(' ') ?: null;

        return [$firstName, $secondName, $lastName];
    }

    private function splitPhone(string $phone): array
    {
        $normalized = preg_replace('/[^\d+]/', '', $phone) ?? '';
        $knownCodes = ['+974', '+973', '+971', '+968', '+966', '+965', '+92', '+91', '+20'];

        foreach ($knownCodes as $code) {
            if (Str::startsWith($normalized, $code)) {
                return [$code, ltrim(Str::after($normalized, $code), '0')];
            }
        }

        foreach ($knownCodes as $code) {
            $withoutPlus = ltrim($code, '+');

            if (Str::startsWith($normalized, $withoutPlus)) {
                return [$code, ltrim(Str::after($normalized, $withoutPlus), '0')];
            }
        }

        return [null, ltrim($normalized, '+')];
    }

    private function safeQrCode(): ?string
    {
        try {
            $invoice = new Invoice([
                'total' => 0,
                'tax_amount' => 0,
            ]);

            return $invoice->generateQrCode();
        } catch (\Throwable) {
            return null;
        }
    }

    private function clearPendingState(ChatSession $session): void
    {
        $context = $session->context ?? [];
        unset($context['pending_action'], $context['pending_summary'], $context['booking_proposal'], $context['cancel_proposal']);

        $session->update([
            'status' => 'open',
            'context' => $context,
        ]);
    }
}
