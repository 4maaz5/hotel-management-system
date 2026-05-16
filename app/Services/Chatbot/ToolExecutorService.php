<?php

namespace App\Services\Chatbot;

use App\Models\ChatSession;
use App\Models\Reservation;

class ToolExecutorService
{
    public function __construct(
        private readonly AvailabilityToolService $availabilityService,
        private readonly ReservationBookingService $bookingService,
        private readonly KnowledgeBaseService $knowledgeBaseService,
    ) {
    }

    public function execute(ChatSession $session, array $plan, string $userMessage): array
    {
        $toolName = $plan['tool_name'] ?? 'none';
        $parameters = $plan['tool_parameters'] ?? [];
        $isWebsiteGuest = $this->isWebsiteGuestSession($session);

        if ($isWebsiteGuest) {
            $parameters['public_website_only'] = true;
        }

        if ($session->branch_id) {
            $parameters['branch_id'] = $session->branch_id;
        }

        return match ($toolName) {
            'checkAvailability' => $this->availabilityService->checkAvailability($parameters),
            'createBooking' => ($parameters['confirmation'] ?? false)
                ? $this->bookingService->confirmBooking($session)
                : $this->bookingService->prepareBooking($session, $parameters),
            'cancelBooking' => $isWebsiteGuest
                ? $this->publicCancellationUnavailable()
                : (($parameters['confirmation'] ?? false)
                ? $this->confirmCancellation($session)
                : $this->prepareCancellation($session, $parameters)),
            'getHotelPolicies' => $this->hotelPolicies($parameters, $userMessage),
            default => $this->defaultResponse($session, $parameters),
        };
    }

    private function prepareCancellation(ChatSession $session, array $parameters): array
    {
        $reservationNumber = trim((string) ($parameters['reservation_number'] ?? ''));

        if ($reservationNumber === '') {
            return [
                'status' => 'needs_more_info',
                'missing_fields' => ['reservation_number'],
                'message' => 'A reservation number is required to cancel a booking.',
            ];
        }

        $reservation = Reservation::query()
            ->where('reservation_number', $reservationNumber)
            ->first();

        if (! $reservation) {
            return [
                'status' => 'failed',
                'message' => 'Reservation not found.',
            ];
        }

        if (in_array($reservation->status, ['cancelled', 'checked_out'], true)) {
            return [
                'status' => 'failed',
                'message' => 'This reservation cannot be cancelled anymore.',
            ];
        }

        $proposal = [
            'reservation_id' => $reservation->id,
            'reservation_number' => $reservation->reservation_number,
            'guest_name' => $reservation->guest?->full_name,
            'check_in_date' => optional($reservation->check_in_date)->toDateString(),
            'check_out_date' => optional($reservation->check_out_date)->toDateString(),
        ];

        $session->update([
            'status' => 'pending_confirmation',
            'context' => array_merge($session->context ?? [], [
                'pending_action' => 'cancel_booking',
                'pending_summary' => 'Waiting for cancellation confirmation.',
                'cancel_proposal' => $proposal,
            ]),
        ]);

        return [
            'status' => 'requires_confirmation',
            'message' => 'Cancellation confirmation required.',
            'data' => $proposal,
        ];
    }

    private function confirmCancellation(ChatSession $session): array
    {
        $proposal = data_get($session->context ?? [], 'cancel_proposal');

        if (! is_array($proposal)) {
            return [
                'status' => 'failed',
                'message' => 'No pending cancellation request was found.',
            ];
        }

        $reservation = Reservation::query()->find($proposal['reservation_id'] ?? null);

        if (! $reservation) {
            return [
                'status' => 'failed',
                'message' => 'Reservation not found.',
            ];
        }

        if (in_array($reservation->status, ['cancelled', 'checked_out'], true)) {
            $this->clearPendingState($session);

            return [
                'status' => 'failed',
                'message' => 'This reservation cannot be cancelled anymore.',
            ];
        }

        $reservation->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'updated_by' => $session->user_id,
        ]);

        $this->clearPendingState($session);

        return [
            'status' => 'completed',
            'message' => 'Reservation cancelled successfully.',
            'data' => [
                'reservation_number' => $reservation->reservation_number,
            ],
        ];
    }

    private function hotelPolicies(array $parameters, string $userMessage): array
    {
        $topic = trim((string) ($parameters['policy_topic'] ?? '')) ?: $userMessage;
        $language = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $articles = $this->knowledgeBaseService->articlesForPolicy($topic, $language);

        return [
            'status' => 'completed',
            'message' => $articles === []
                ? 'No policy articles matched the request.'
                : 'Policy information found.',
            'data' => [
                'articles' => $articles,
            ],
        ];
    }

    private function defaultResponse(ChatSession $session, array $parameters): array
    {
        if (($parameters['handover'] ?? false) === true) {
            $session->update(['status' => 'handover_requested']);

            return [
                'status' => 'handover',
                'message' => 'The session has been flagged for a human agent.',
            ];
        }

        return [
            'status' => 'completed',
            'message' => 'The assistant can help with availability, bookings, cancellations, and hotel policies.',
        ];
    }

    private function publicCancellationUnavailable(): array
    {
        return [
            'status' => 'failed',
            'message' => 'For security, cancellation is not available in the public website chat. Please contact the hotel team and share your reservation number.',
        ];
    }

    private function isWebsiteGuestSession(ChatSession $session): bool
    {
        return $session->user_id === null
            && data_get($session->context ?? [], 'channel') === 'website';
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
