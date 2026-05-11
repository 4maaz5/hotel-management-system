<?php

namespace App\Services\Ntmp;

use App\Models\PropertyCommercialDetail;
use App\Models\PropertyTourismLicense;
use App\Models\Reservation;

class NtmpPayloadBuilder
{
    public const CATEGORY_TYPES = [
        1 => 'Single Bedroom',
        2 => 'Double Bedroom / King Bedroom',
        3 => 'Regular Suite',
        4 => 'Studio Apartment / 1 Bedroom Apartment',
        5 => '2 Bedroom Furnished Apartment',
        6 => '3 Bedroom Furnished Apartment',
        7 => 'Villa',
        8 => 'Room with 3 Beds',
        9 => 'Room with 4 Beds',
        10 => 'Room with 5 Beds',
        11 => 'Room with 6 Beds',
        12 => 'Room with 7+ Beds',
        13 => 'Other',
        14 => 'Executive / Luxury Suite',
    ];

    public const GUEST_TYPES = [
        1 => 'Citizen',
        2 => 'Gulf Citizen',
        3 => 'Visitor',
        4 => 'Resident',
    ];

    public const VISIT_PURPOSES = [
        1 => 'Tourism',
        2 => 'Family or Friends',
        3 => 'Religious',
        4 => 'Business or Work',
        5 => 'Sports',
        6 => 'Entertainment',
        7 => 'Other',
        8 => 'Work (Royal Court)',
        9 => 'Quarantined Guests',
        10 => 'Ministry of Health Staff',
    ];

    public const CANCELLATION_REASONS = [
        0 => 'No Reason / Not Applicable',
        1 => 'Cancelled by Guest',
        2 => 'Cancelled by Hotel',
        3 => 'Found Better Deal',
        4 => 'Unsatisfied',
        5 => 'No Show',
        6 => 'Changed Travel Dates',
        7 => 'Rebooked Alternate Travel Dates or Travelers',
        8 => 'Other',
    ];

    public function build(Reservation $reservation, string $eventType): array
    {
        $property = $reservation->property()
            ->withoutGlobalScopes()
            ->with(['commercialDetail', 'tourismLicense'])
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

        /** @var PropertyTourismLicense|null $tourismLicense */
        $tourismLicense = $property?->tourismLicense;

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
                    'second_name' => $guest?->second_name,
                    'middle_name' => $guest?->middle_name,
                    'last_name' => $guest?->last_name,
                    'mobile_dial_code' => $guest?->mobile_dial_code,
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
            'platform' => 'saudi_ntmp',
            'submission_type' => 'reservation_event',
            'event_type' => $eventType,
            'prepared_for' => 'official_ntmp_api_mapping',
            'ntmp_setup_reference' => [
                'category_types' => self::CATEGORY_TYPES,
                'guest_types' => self::GUEST_TYPES,
                'visit_purposes' => self::VISIT_PURPOSES,
                'cancellation_reasons' => self::CANCELLATION_REASONS,
            ],
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
                'guest_count' => (int) $reservation->adults + (int) $reservation->children,
                'visit_purpose_id' => null,
                'cancellation_reason_id' => $eventType === 'no_show' ? 5 : 0,
                'notes' => $reservation->notes,
            ],
            'financials' => [
                'daily_rate' => $reservation->daily_rate,
                'monthly_rate' => $reservation->monthly_rate,
                'total_rent' => $reservation->total_rent,
                'discount' => $reservation->discount,
                'total_taxes_fees' => $reservation->total_taxes_fees,
                'security_deposit' => $reservation->security_deposit,
                'paid_amount' => $reservation->paid_amount,
                'balance' => $reservation->balance,
                'subtotal' => $reservation->subtotal,
                'grand_total' => $reservation->grand_total,
            ],
            'property' => [
                'id' => $property?->id,
                'name_en' => $property?->property_name_en,
                'name_ar' => $property?->property_name_ar,
                'property_code' => $property?->property_code,
                'registration_number' => $commercialDetail?->registration_number,
                'activity_license_number' => $commercialDetail?->activity_license_number,
                'vat_registration_number' => $commercialDetail?->vat_registration_number,
                'tourism_license_number' => $tourismLicense?->license_number,
                'tourism_license_expiry_date' => optional($tourismLicense?->license_expiry_date)->toDateString(),
                'tourism_activity_type' => $tourismLicense?->tourism_activity_type,
            ],
            'unit' => [
                'id' => $unit?->id,
                'unit_number' => $unit?->unit_number,
                'category_type_id' => null,
            ],
            'primary_guest' => $occupants[0] ?? null,
            'occupants' => $occupants,
        ];
    }
}
