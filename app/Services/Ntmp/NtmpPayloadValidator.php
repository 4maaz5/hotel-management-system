<?php

namespace App\Services\Ntmp;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class NtmpPayloadValidator
{
    /**
     * @throws ValidationException
     */
    public function validate(array $payload): array
    {
        return Validator::make($payload, [
            'platform' => 'required|string|in:saudi_ntmp',
            'submission_type' => 'required|string|in:reservation_event',
            'event_type' => 'required|string|in:check_in,check_out,stay_update,no_show',
            'prepared_for' => 'required|string|in:official_ntmp_api_mapping',
            'ntmp_setup_reference' => 'required|array',

            'reservation' => 'required|array',
            'reservation.id' => 'required|integer',
            'reservation.reservation_number' => 'required|string|max:255',
            'reservation.status' => 'required|string|max:50',
            'reservation.check_in_date' => 'required|date',
            'reservation.check_out_date' => 'required|date|after_or_equal:reservation.check_in_date',
            'reservation.adults' => 'required|integer|min:0',
            'reservation.children' => 'required|integer|min:0',
            'reservation.guest_count' => 'required|integer|min:0',
            'reservation.visit_purpose_id' => 'nullable|integer|min:1|max:10',
            'reservation.cancellation_reason_id' => 'nullable|integer|min:0|max:8',

            'financials' => 'required|array',
            'financials.daily_rate' => 'nullable|numeric|min:0',
            'financials.monthly_rate' => 'nullable|numeric|min:0',
            'financials.total_rent' => 'nullable|numeric|min:0',
            'financials.discount' => 'nullable|numeric|min:0',
            'financials.total_taxes_fees' => 'nullable|numeric|min:0',
            'financials.security_deposit' => 'nullable|numeric|min:0',
            'financials.paid_amount' => 'nullable|numeric|min:0',
            'financials.balance' => 'nullable|numeric',
            'financials.subtotal' => 'nullable|numeric|min:0',
            'financials.grand_total' => 'nullable|numeric|min:0',

            'property' => 'required|array',
            'property.id' => 'required|integer',
            'property.name_en' => 'required|string|max:255',
            'property.name_ar' => 'nullable|string|max:255',
            'property.property_code' => 'required|string|max:255',
            'property.registration_number' => 'nullable|string|max:255',
            'property.activity_license_number' => 'nullable|string|max:255',
            'property.vat_registration_number' => 'nullable|string|max:255',
            'property.tourism_license_number' => 'nullable|string|max:255',
            'property.tourism_license_expiry_date' => 'nullable|date',
            'property.tourism_activity_type' => 'nullable|string|max:255',

            'unit' => 'required|array',
            'unit.id' => 'required|integer',
            'unit.unit_number' => 'required|string|max:255',
            'unit.category_type_id' => 'nullable|integer|min:1|max:14',

            'primary_guest' => 'required|array',
            'occupants' => 'required|array|min:1',
            'occupants.*.guest_id' => 'required|integer',
            'occupants.*.is_primary' => 'required|boolean',
            'occupants.*.relationship' => 'nullable|string|max:50',
            'occupants.*.check_in_status' => 'nullable|string|max:50',
            'occupants.*.check_out_status' => 'nullable|string|max:50',
            'occupants.*.first_name' => 'required|string|max:255',
            'occupants.*.second_name' => 'nullable|string|max:255',
            'occupants.*.middle_name' => 'nullable|string|max:255',
            'occupants.*.last_name' => 'required|string|max:255',
            'occupants.*.mobile_dial_code' => 'nullable|string|max:10',
            'occupants.*.mobile_number' => 'required|string|max:30',
            'occupants.*.email' => 'nullable|email|max:255',
            'occupants.*.gender' => 'required|string|in:male,female',
            'occupants.*.dob' => 'required|date|before_or_equal:today',
            'occupants.*.nationality' => 'nullable|string|max:255',
            'occupants.*.nationality_code' => 'required|string|size:3',
            'occupants.*.id_type' => 'required|string|max:50',
            'occupants.*.id_number' => 'required|string|max:50',
            'occupants.*.id_issue_country' => 'required|string|max:255',
            'occupants.*.id_expiry_date' => 'required|date|after_or_equal:today',
            'occupants.*.visa_number' => 'nullable|string|max:255',
            'occupants.*.arrival_from' => 'nullable|string|max:255',
            'occupants.*.vehicle_plate' => 'nullable|string|max:255',
        ])->validate();
    }
}
