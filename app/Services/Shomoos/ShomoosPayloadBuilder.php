<?php

namespace App\Services\Shomoos;

use App\Models\PropertyCommercialDetail;
use App\Models\Reservation;

class ShomoosPayloadBuilder
{
    public function build(Reservation $reservation, string $eventType): array
    {
        $property = $reservation->property()
            ->withoutGlobalScopes()
            ->with('commercialDetail')
            ->first();

        $unit = $reservation->unit()
            ->withoutGlobalScopes()
            ->first();

        $reservationGuests = $reservation->reservationGuests()
            ->withoutGlobalScopes()
            ->with(['guest' => fn ($query) => $query->withoutGlobalScopes()])
            ->get();

        /** @var PropertyCommercialDetail|null $commercialDetail */
        $commercialDetail = $property?->commercialDetail;

        $occupants = $reservationGuests
            ->sortByDesc('is_primary')
            ->map(function ($reservationGuest) {
                $guest = $reservationGuest->guest;

                return [
                    'guest_id' => $guest?->id,
                    'is_primary' => (bool) $reservationGuest->is_primary,
                    'relationship' => $reservationGuest->relationship,
                    'check_in_status' => $reservationGuest->check_in_status,
                    'check_out_status' => $reservationGuest->check_out_status,
                    'first_name' => $guest?->first_name,
                    'last_name' => $guest?->last_name,
                    'mobile_number' => $guest?->mobile_number,
                    'email' => $guest?->email,
                    'gender' => $guest?->gender,
                    'dob' => optional($guest?->date_of_birth)->toDateString(),
                    'nationality' => $guest?->nationality,
                    'nationality_code' => $guest?->nationality_code,
                    'id_type' => $guest?->id_type,
                    'id_number' => $guest?->id_number,
                    'id_issue_country' => $guest?->id_issue_country,
                    'id_expiry_date' => optional($guest?->id_expiry_date)->toDateString(),
                    'visa_number' => $guest?->visa_number,
                    'arrival_from' => $guest?->arrival_from,
                    'vehicle_plate' => $guest?->car_license_plate,
                ];
            })
            ->values()
            ->all();

        return [
            'event_type' => $eventType,
            'reservation' => [
                'id' => $reservation->id,
                'reservation_number' => $reservation->reservation_number,
                'status' => $reservation->status,
                'check_in_date' => optional($reservation->check_in_date)->toDateString(),
                'check_in_time' => optional($reservation->check_in_time)->format('H:i:s'),
                'check_out_date' => optional($reservation->check_out_date)->toDateString(),
                'check_out_time' => optional($reservation->check_out_time)->format('H:i:s'),
                'checked_in_at' => optional($reservation->checked_in_at)->toIso8601String(),
                'checked_out_at' => optional($reservation->checked_out_at)->toIso8601String(),
                'no_show_at' => optional($reservation->no_show_at)->toIso8601String(),
                'adults' => $reservation->adults,
                'children' => $reservation->children,
                'notes' => $reservation->notes,
            ],
            'property' => [
                'id' => $property?->id,
                'name_en' => $property?->property_name_en,
                'name_ar' => $property?->property_name_ar,
                'property_code' => $property?->property_code,
                'registration_number' => $commercialDetail?->registration_number,
                'activity_license_number' => $commercialDetail?->activity_license_number,
                'vat_registration_number' => $commercialDetail?->vat_registration_number,
            ],
            'unit' => [
                'id' => $unit?->id,
                'unit_number' => $unit?->unit_number,
            ],
            'primary_guest' => $occupants[0] ?? null,
            'occupants' => $occupants,
        ];
    }
}
