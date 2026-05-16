<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Models\Block;
use App\Models\CancelReason;
use App\Models\CreditNote;
use App\Models\DiscountType;
use App\Models\Floor;
use App\Models\GuestClass;
use App\Models\HighWeekday;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentMethodConfig;
use App\Models\PaymentVoucher;
use App\Models\Penalty;
use App\Models\Property;
use App\Models\RatePlan;
use App\Models\RatePlanUnitType;
use App\Models\ReceiptVoucher;
use App\Models\Reservation;
use App\Models\ReservationGuest;
use App\Models\ReservationSetting;
use App\Models\ReservationSourceSetting;
use App\Models\SeasonalRate;
use App\Models\SpecialRate;
use App\Models\Scopes\CurrentPropertyScope;
use App\Models\Scopes\TenantScope;
use App\Models\TaxFeeCustomization;
use App\Models\Unit;
use App\Models\UnitCustomRate;
use App\Models\UnitReason;
use App\Models\UnitTypeCustomization;
use App\Models\UnitTypeRate;
use App\Services\Ntmp\NtmpService;
use App\Services\Shomoos\ShomoosService;
use App\Support\PropertyContext;
use App\Support\UserActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $settings = ReservationSetting::getSettings();

        $query = Reservation::with(['guest', 'unit.unitType', 'ratePlan', 'corporate']);

        if ($request->guest_name) {
            $query->where(function ($reservationQuery) use ($request) {
                $reservationQuery->whereHas('guest', function ($q) use ($request) {
                    $q->where('first_name', 'like', '%'.$request->guest_name.'%')
                        ->orWhere('last_name', 'like', '%'.$request->guest_name.'%');
                })->orWhereHas('corporate', function ($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->guest_name.'%');
                });
            });
        }

        if ($request->unit_number) {
            $query->whereHas('unit', function ($q) use ($request) {
                $q->where('unit_number', 'like', '%'.$request->unit_number.'%');
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->check_in_from) {
            $query->where('check_in_date', '>=', $request->check_in_from);
        }

        if ($request->check_out_from) {
            $query->where('check_out_date', '>=', $request->check_out_from);
        }

        $reservations = $query->orderBy('id', 'desc')->paginate(50);
        $cancelReasons = CancelReason::all();
        $penalties = Penalty::all();

        return view('admin.reservation.index', compact('settings', 'reservations', 'cancelReasons', 'penalties'));
    }

    public function create()
    {
        $propertyId = app(PropertyContext::class)->id();
        $settings = ReservationSetting::getSettings();
        $units = Unit::where('is_active', 1)->with(['amenities', 'unitType', 'floor', 'block'])->get();
        $unitTypes = UnitTypeCustomization::all();
        $ratePlans = RatePlan::where('is_active', 1)->with(['unitTypeRates', 'meals'])->get();
        $reservationSources = ReservationSourceSetting::all();
        $paymentMethods = PaymentMethodConfig::all();
        $guestClasses = GuestClass::where('is_active', 1)->get();
        $cancelReasons = CancelReason::where('is_active', 1)->get();
        $unitReasons = UnitReason::where('is_active', 1)->get();
        $floors = Floor::all();
        $blocks = Block::all();
        $discountTypes = DiscountType::where('is_active', 1)->get();

        // Rate related data
        $highWeekdays = HighWeekday::pluck('day_name')->toArray();
        $seasonalRates = SeasonalRate::where('is_active', 1)->with('unitRates')->get();
        $specialRates = SpecialRate::where('is_active', 1)->with('unitRates')->get();
        $unitTypeRates = UnitTypeRate::where('is_active', 1)->get();
        $unitCustomRates = UnitCustomRate::all();
        $ratePlanUnitTypes = RatePlanUnitType::all();

        // Tax/Fee related data - get active taxes/fees for reservations
        $taxFees = TaxFeeCustomization::where('is_expenses', false)
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now()->toDateString());
            })
            ->where('start_date', '<=', now()->toDateString())
            ->get();

        return view('admin.reservation.create', compact(
            'settings',
            'units',
            'unitTypes',
            'ratePlans',
            'reservationSources',
            'paymentMethods',
            'guestClasses',
            'cancelReasons',
            'unitReasons',
            'floors',
            'blocks',
            'discountTypes',
            'highWeekdays',
            'seasonalRates',
            'specialRates',
            'unitTypeRates',
            'unitCustomRates',
            'taxFees',
            'ratePlanUnitTypes',
            'propertyId'
        ));
    }

    public function edit(Request $request, $reservation)
    {
        $reservation = $this->resolveAccessibleReservation($request, $reservation);
        $propertyId = app(PropertyContext::class)->id();
        $settings = ReservationSetting::getSettings();
        $reservation->load([
            'guest',
            'unit',
            'unit.unitType',
            'ratePlan',
            'corporate',
            'reservationGuests.guest',
            'occupants',
        ]);

        $units = Unit::where('is_active', 1)->with(['amenities', 'unitType', 'floor', 'block'])->get();
        $unitTypes = UnitTypeCustomization::all();
        $ratePlans = RatePlan::where('is_active', 1)->with(['unitTypeRates', 'meals'])->get();
        $reservationSources = ReservationSourceSetting::all();
        $paymentMethods = PaymentMethodConfig::all();
        $guestClasses = GuestClass::where('is_active', 1)->get();
        $cancelReasons = CancelReason::where('is_active', 1)->get();
        $unitReasons = UnitReason::where('is_active', 1)->get();
        $floors = Floor::all();
        $blocks = Block::all();
        $discountTypes = DiscountType::where('is_active', 1)->get();
        $penalties = Penalty::where('is_active', 1)->get();

        // Rate related data
        $highWeekdays = HighWeekday::pluck('day_name')->toArray();
        $seasonalRates = SeasonalRate::where('is_active', 1)->with('unitRates')->get();
        $specialRates = SpecialRate::where('is_active', 1)->with('unitRates')->get();
        $unitTypeRates = UnitTypeRate::where('is_active', 1)->get();
        $unitCustomRates = UnitCustomRate::all();
        $ratePlanUnitTypes = RatePlanUnitType::all();

        // Tax/Fee related data
        $taxFees = TaxFeeCustomization::where('is_expenses', false)
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now()->toDateString());
            })
            ->where('start_date', '<=', now()->toDateString())
            ->get();

        return view('admin.reservation.edit', compact(
            'settings',
            'reservation',
            'units',
            'unitTypes',
            'ratePlans',
            'reservationSources',
            'paymentMethods',
            'guestClasses',
            'cancelReasons',
            'unitReasons',
            'floors',
            'blocks',
            'discountTypes',
            'penalties',
            'highWeekdays',
            'seasonalRates',
            'specialRates',
            'unitTypeRates',
            'unitCustomRates',
            'taxFees',
            'ratePlanUnitTypes',
            'propertyId'
        ));
    }

    public function update(Request $request, $reservation)
    {
        $reservation = $this->resolveAccessibleReservation($request, $reservation);
        $beforeReservation = $this->reservationActivityData($reservation);
        $propertyId = app(PropertyContext::class)->id();
        $branchId = app(PropertyContext::class)->branchId();

        $validated = $request->validate([
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'unit_id' => [
                'required',
                Rule::exists('units', 'id')->where(fn ($query) => $query->where('branch_id', $branchId)),
            ],
            'nights' => 'required|integer|min:1',
            'adults' => 'nullable|integer|min:0',
            'children' => 'nullable|integer|min:0',
            'reservation_type' => 'required|in:daily,monthly',
            'daily_rate' => 'nullable|numeric|min:0',
            'monthly_rate' => 'nullable|numeric|min:0',
            'total_rent' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'total_taxes_fees' => 'nullable|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'balance' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:pending,confirmed,checked_in,checked_out,cancelled,no_show',
        ] + $this->guestSelectionRules($branchId));

        $settings = ReservationSetting::getSettings();

        $checkInTime = $request->check_in_time ?? $settings->check_in_time;
        $checkOutTime = $request->check_out_time ?? $settings->check_out_time;

        // Determine status - if reservation_action is 'update', keep existing status
        if ($request->reservation_action === 'update') {
            $status = $reservation->status ?? 'pending';
        } elseif ($request->reservation_action === 'check_in') {
            $status = 'checked_in';
        } elseif ($request->reservation_action === 'check_out') {
            $status = 'checked_out';
        } elseif ($request->is_confirmed) {
            $status = 'confirmed';
        } else {
            $status = $request->status ?? 'pending';
        }

        // Prevent double booking when editing by matching the create flow
        // while excluding the current reservation from the overlap check.
        $alreadyBooked = Reservation::where('id', '!=', $reservation->id)
            ->where('unit_id', $request->unit_id)
            ->whereNotIn('status', ['cancelled', 'checked_out'])
            ->where('check_in_date', '<', $request->check_out_date)
            ->where('check_out_date', '>', $request->check_in_date)
            ->exists();

        if ($alreadyBooked) {
            return back()->withErrors([
                'unit_id' => 'This unit is already booked for the selected dates.',
            ])->withInput();
        }

        $stayEventAttributes = $this->resolveStayEventAttributes($reservation, $status, $request->reservation_action);

        $reservation->update([
            'penalty_id' => $request->penalty_id ?? null,
            'penalty_amount' => $request->penalty_amount ?? 0,
            'guest_id' => $request->guest_id ?? null,
            'corporate_id' => $request->corporate_id ?? null,
            'unit_id' => $request->unit_id,
            'source_id' => $request->source_id ?? null,
            'guest_class_id' => $request->guest_class_id ?? null,
            'rate_plan_id' => $request->rate_plan_id ?? null,
            'payment_method_id' => $request->payment_method_id ?? null,
            'check_in_date' => $request->check_in_date,
            'check_in_time' => $checkInTime,
            'check_out_date' => $request->check_out_date,
            'check_out_time' => $checkOutTime,
            'nights' => $request->nights,
            'adults' => $request->adults ?? 1,
            'children' => $request->children ?? 0,
            'reservation_type' => $request->reservation_type,
            'daily_rate' => $request->daily_rate ?? 0,
            'monthly_rate' => $request->monthly_rate ?? 0,
            'total_rent' => $request->total_rent ?? 0,
            'discount_type' => $request->discount_type ?? null,
            'discount' => $request->discount ?? 0,
            'total_taxes_fees' => $request->total_taxes_fees ?? 0,
            'security_deposit' => $request->security_deposit ?? 0,
            'paid_amount' => $request->paid_amount ?? 0,
            'balance' => $request->balance ?? 0,
            'subtotal' => $request->total_rent ?? 0,
            'grand_total' => ($request->total_rent ?? 0) - ($request->discount ?? 0) + ($request->total_taxes_fees ?? 0),
            'status' => $status,
            'is_confirmed' => $request->is_confirmed ?? false,
            'booking_date' => $request->booking_date ?? now()->toDateString(),
            'notes' => $request->notes,
            'updated_by' => auth()->id(),
            ...$stayEventAttributes,
        ]);

        $this->syncReservationOccupants($reservation, $request, $request->reservation_action, $status);
        $this->syncShomoosForReservation($reservation->fresh([
            'property',
            'property.commercialDetail',
            'guest',
            'unit',
            'reservationGuests.guest',
        ]), $request->reservation_action, $status);
        $this->syncNtmpForReservation($reservation->fresh([
            'property',
            'property.commercialDetail',
            'property.tourismLicense',
            'guest',
            'unit',
            'reservationGuests.guest',
        ]), $request->reservation_action, $status);

        $afterReservation = $this->reservationActivityData($reservation->fresh([
            'guest',
            'corporate',
            'unit',
            'ratePlan',
            'reservationGuests.guest',
        ]));
        $assignmentFields = ['guest_id', 'corporate_id', 'unit_id', 'rate_plan_id'];
        $wasReassigned = collect($assignmentFields)->contains(
            fn (string $field) => ($beforeReservation[$field] ?? null) != ($afterReservation[$field] ?? null)
        );

        app(UserActivityLogger::class)->log(
            'reservations',
            $wasReassigned ? 'assigned' : 'updated',
            $reservation,
            $wasReassigned
                ? "Updated reservation assignment for {$reservation->reservation_number}"
                : "Updated reservation {$reservation->reservation_number}",
            $beforeReservation,
            $afterReservation
        );

        $this->syncUnitHousekeepingStatus($reservation, $request->reservation_action);

        // Update housekeeping status to dirty when guest checks out
        if ($request->reservation_action === 'check_out') {
            NotificationHelper::notifyCheckOut(auth()->id(), $reservation);
        }

        // Send check-in notification
        if ($request->reservation_action === 'check_in') {
            NotificationHelper::notifyCheckIn(auth()->id(), $reservation);
        }

        return redirect()->route('dashboard.reservation.index')
            ->with('success', __('messages.reservation_updated_successfully'));
    }

    public function getRates(Request $request)
    {
        $unitId = $request->unit_id;
        $reservationType = $request->reservation_type;
        $checkIn = $request->check_in ? \Carbon\Carbon::parse($request->check_in) : null;
        $checkOut = $request->check_out ? \Carbon\Carbon::parse($request->check_out) : null;

        $unit = Unit::find($unitId);
        if (! $unit) {
            return response()->json(['error' => 'Unit not found'], 404);
        }

        $unitTypeId = $unit->unit_type_id;
        $pricingUnitTypeId = UnitTypeCustomization::find($unitTypeId)?->unit_type_id ?? $unitTypeId;

        // Check unit custom rates first
        $unitCustomRate = UnitCustomRate::where('unit_id', $unitId)->first();

        // Check unit type rates (base rates)
        $unitTypeRate = UnitTypeRate::where('unit_type_id', $pricingUnitTypeId)->where('is_active', true)->first();

        // Check for seasonal rates active during booking period
        $seasonalRates = [];
        if ($checkIn && $checkOut) {
            $activeSeasonals = SeasonalRate::where('is_active', true)
                ->where('start_date', '<=', $checkOut->format('Y-m-d'))
                ->where('end_date', '>=', $checkIn->format('Y-m-d'))
                ->with(['unitRates' => function ($q) use ($pricingUnitTypeId) {
                    $q->where('unit_type_id', $pricingUnitTypeId);
                }])
                ->get();

            foreach ($activeSeasonals as $seasonal) {
                $seasonalRates[] = [
                    'id' => $seasonal->id,
                    'name' => $seasonal->name,
                    'start_date' => $seasonal->start_date->format('Y-m-d'),
                    'end_date' => $seasonal->end_date->format('Y-m-d'),
                    'rate' => $seasonal->unitRates->first()?->toArray() ?? null,
                ];
            }
        }

        // Check for special rates active during booking period
        $specialRates = [];
        if ($checkIn && $checkOut) {
            $activeSpecials = SpecialRate::where('is_active', true)
                ->where('start_date', '<=', $checkOut->format('Y-m-d'))
                ->where('end_date', '>=', $checkIn->format('Y-m-d'))
                ->with(['unitRates' => function ($q) use ($pricingUnitTypeId) {
                    $q->where('unit_type_id', $pricingUnitTypeId);
                }])
                ->get();

            foreach ($activeSpecials as $special) {
                $specialRates[] = [
                    'id' => $special->id,
                    'name' => $special->name,
                    'start_date' => $special->start_date->format('Y-m-d'),
                    'end_date' => $special->end_date->format('Y-m-d'),
                    'rate' => $special->unitRates->first()?->toArray() ?? null,
                ];
            }
        }

        $dailyRate = 0;
        $monthlyRate = 0;

        // Always get both rates (for switching between daily/monthly)
        if ($unitCustomRate) {
            $dailyRate = [
                'low' => $unitCustomRate->low_weekday_rate ?? 0,
                'high' => $unitCustomRate->high_weekday_rate ?? 0,
            ];
            $monthlyRate = $unitCustomRate->monthly_rate ?? 0;
        } elseif ($unitTypeRate) {
            $dailyRate = [
                'low' => $unitTypeRate->low_weekday_rate ?? 0,
                'high' => $unitTypeRate->high_weekday_rate ?? 0,
            ];
            $monthlyRate = $unitTypeRate->monthly_rate ?? 0;
        }

        return response()->json([
            'unit_id' => $unitId,
            'unit_type_id' => $unitTypeId,
            'pricing_unit_type_id' => $pricingUnitTypeId,
            'daily_rate' => $dailyRate,
            'monthly_rate' => $monthlyRate,
            'has_custom_rate' => ! is_null($unitCustomRate),
            'unit_custom_rate' => $unitCustomRate ? [
                'low_weekday_rate' => $unitCustomRate->low_weekday_rate,
                'high_weekday_rate' => $unitCustomRate->high_weekday_rate,
                'monthly_rate' => $unitCustomRate->monthly_rate,
            ] : null,
            'unit_type_rate' => $unitTypeRate ? [
                'low_weekday_rate' => $unitTypeRate->low_weekday_rate,
                'high_weekday_rate' => $unitTypeRate->high_weekday_rate,
                'monthly_rate' => $unitTypeRate->monthly_rate,
            ] : null,
            'seasonal_rates' => $seasonalRates,
            'special_rates' => $specialRates,
            'security_deposit' => \App\Models\SecurityDeposit::where('unit_type_id', $pricingUnitTypeId)->value('deposit_amount') ?? 0,
        ]);
    }

    public function store(Request $request)
    {
        $propertyId = app(PropertyContext::class)->id();
        $branchId = app(PropertyContext::class)->branchId();
        $companyId = app(PropertyContext::class)->property()?->company_id;

        abort_unless($propertyId && $branchId && $companyId, 422, 'Please select or create a branch first.');

        $validated = $request->validate([
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'unit_id' => [
                'required',
                Rule::exists('units', 'id')->where(fn ($query) => $query->where('branch_id', $branchId)),
            ],
            'nights' => 'required|integer|min:1',
            'adults' => 'nullable|integer|min:0',
            'children' => 'nullable|integer|min:0',
            'reservation_type' => 'required|in:daily,monthly',
            'daily_rate' => 'nullable|numeric|min:0',
            'monthly_rate' => 'nullable|numeric|min:0',
            'total_rent' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'total_taxes_fees' => 'nullable|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'balance' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:pending,confirmed,checked_in,checked_out,cancelled,no_show',
        ] + $this->guestSelectionRules($branchId));

        $settings = ReservationSetting::getSettings();

        $checkInTime = $request->check_in_time ?? $settings->check_in_time;
        $checkOutTime = $request->check_out_time ?? $settings->check_out_time;

        // Determine status based on reservation_action
        if ($request->reservation_action === 'check_in') {
            $status = 'checked_in';
        } elseif ($request->reservation_action === 'check_out') {
            $status = 'checked_out';
        } elseif ($request->is_confirmed) {
            $status = 'confirmed';
        } else {
            $status = $request->status ?? 'pending';
        }

        // Check for double booking
        $unitId = $request->unit_id;
        $checkIn = $request->check_in_date;
        $checkOut = $request->check_out_date;

        $alreadyBooked = Reservation::where('unit_id', $unitId)
            ->whereNotIn('status', ['cancelled', 'checked_out'])
            ->where('check_in_date', '<', $checkOut)
            ->where('check_out_date', '>', $checkIn)
            ->exists();

        if ($alreadyBooked) {
            return back()->withErrors([
                'unit_id' => 'This unit is already booked for the selected dates.',
            ])->withInput();
        }

        $stayEventAttributes = $this->resolveStayEventAttributes(null, $status, $request->reservation_action);

        $reservation = Reservation::create([
            'company_id' => $companyId,
            'reservation_number' => Reservation::generateReservationNumber(),
            'branch_id' => $branchId,
            'guest_id' => $request->guest_id ?? null,
            'corporate_id' => $request->corporate_id ?? null,
            'unit_id' => $request->unit_id,
            'source_id' => $request->source_id ?? null,
            'guest_class_id' => $request->guest_class_id ?? null,
            'rate_plan_id' => $request->rate_plan_id ?? null,
            'payment_method_id' => $request->payment_method_id ?? null,
            'check_in_date' => $request->check_in_date,
            'check_in_time' => $checkInTime,
            'check_out_date' => $request->check_out_date,
            'check_out_time' => $checkOutTime,
            'nights' => $request->nights,
            'adults' => $request->adults ?? 1,
            'children' => $request->children ?? 0,
            'reservation_type' => $request->reservation_type,
            'daily_rate' => $request->daily_rate ?? 0,
            'monthly_rate' => $request->monthly_rate ?? 0,
            'total_rent' => $request->total_rent ?? 0,
            'discount_type' => $request->discount_type ?? null,
            'discount' => $request->discount ?? 0,
            'total_taxes_fees' => $request->total_taxes_fees ?? 0,
            'security_deposit' => $request->security_deposit ?? 0,
            'paid_amount' => $request->paid_amount ?? 0,
            'balance' => $request->balance ?? 0,
            'subtotal' => $request->total_rent ?? 0,
            'grand_total' => ($request->total_rent ?? 0) - ($request->discount ?? 0) + ($request->total_taxes_fees ?? 0) + ($request->penalty_amount ?? 0),
            'status' => $status,
            'is_confirmed' => $request->is_confirmed ?? false,
            'booking_date' => $request->booking_date ?? now()->toDateString(),
            'notes' => $request->notes ?? null,
            'created_by' => auth()->id(),
            ...$stayEventAttributes,
        ]);

        $this->syncReservationOccupants($reservation, $request, $request->reservation_action, $status);
        $this->syncShomoosForReservation($reservation->fresh([
            'property',
            'property.commercialDetail',
            'guest',
            'unit',
            'reservationGuests.guest',
        ]), $request->reservation_action, $status);
        $this->syncNtmpForReservation($reservation->fresh([
            'property',
            'property.commercialDetail',
            'property.tourismLicense',
            'guest',
            'unit',
            'reservationGuests.guest',
        ]), $request->reservation_action, $status);

        $activityLogger = app(UserActivityLogger::class);

        // Create invoice for the reservation
        $invoice = Invoice::create([
            'company_id' => $companyId,
            'branch_id' => $branchId,
            'reservation_id' => $reservation->id,
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'issue_date' => now()->toDateString(),
            'due_date' => $request->check_in_date,
            'subtotal' => $request->total_rent ?? 0,
            'discount' => 0,
            'discount_amount' => $request->discount ?? 0,
            'tax_amount' => $request->total_taxes_fees ?? 0,
            'security_deposit' => $request->security_deposit ?? 0,
            'total' => ($request->total_rent ?? 0) - ($request->discount ?? 0) + ($request->total_taxes_fees ?? 0) + ($request->security_deposit ?? 0),
            'paid_amount' => $request->paid_amount ?? 0,
            'balance' => $request->balance ?? 0,
            'status' => ($request->paid_amount ?? 0) >= (($request->total_rent ?? 0) - ($request->discount ?? 0) + ($request->total_taxes_fees ?? 0)) ? 'paid' : 'pending',
            'payment_method' => $request->payment_method_id ? \App\Models\PaymentMethodConfig::find($request->payment_method_id)?->name : null,
        ]);

        // Generate QR code
        $invoice->qr_code = $invoice->generateQrCode();
        $invoice->save();

        // Create invoice items
        $nights = $request->nights ?? 1;
        $dailyRate = $request->daily_rate ?? 0;

        // Room charges
        InvoiceItem::create([
            'company_id' => $companyId,
            'invoice_id' => $invoice->id,
            'description' => 'Room Charges ('.$nights.' night'.($nights > 1 ? 's' : '').')',
            'quantity' => $nights,
            'unit_price' => $dailyRate,
            'total' => $dailyRate * $nights,
        ]);

        // Add discount if any
        if (($request->discount ?? 0) > 0) {
            InvoiceItem::create([
                'company_id' => $companyId,
                'invoice_id' => $invoice->id,
                'description' => 'Discount',
                'quantity' => 1,
                'unit_price' => -($request->discount ?? 0),
                'total' => -($request->discount ?? 0),
            ]);
        }

        // Add taxes/fees
        if (($request->total_taxes_fees ?? 0) > 0) {
            InvoiceItem::create([
                'company_id' => $companyId,
                'invoice_id' => $invoice->id,
                'description' => 'Taxes & Fees',
                'quantity' => 1,
                'unit_price' => $request->total_taxes_fees ?? 0,
                'total' => $request->total_taxes_fees ?? 0,
            ]);
        }

        // Add security deposit if any
        if (($request->security_deposit ?? 0) > 0) {
            InvoiceItem::create([
                'company_id' => $companyId,
                'invoice_id' => $invoice->id,
                'description' => 'Security Deposit (Refundable)',
                'quantity' => 1,
                'unit_price' => $request->security_deposit ?? 0,
                'total' => $request->security_deposit ?? 0,
            ]);
        }

        // Create receipt voucher if there's any payment
        if (($request->paid_amount ?? 0) > 0) {
            $receiptVoucher = ReceiptVoucher::create([
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'reservation_id' => $reservation->id,
                'guest_id' => $request->guest_id ?? null,
                'corporate_id' => $request->corporate_id ?? null,
                'payment_method_id' => $request->payment_method_id ?? null,
                'voucher_number' => ReceiptVoucher::generateVoucherNumber(),
                'amount' => $request->paid_amount ?? 0,
                'received_from_name' => $reservation->guest ? ($reservation->guest->first_name.' '.$reservation->guest->last_name) : ($reservation->corporate->name ?? null),
                'purpose' => 'Reservation Payment',
                'date' => now()->toDateString(),
                'time' => now()->format('H:i:s'),
                'created_by' => auth()->id(),
            ]);

            $activityLogger->log(
                'receipts',
                'created',
                $receiptVoucher,
                "Created receipt voucher {$receiptVoucher->voucher_number} for reservation {$reservation->reservation_number}",
                [],
                $this->receiptVoucherActivityData($receiptVoucher)
            );
        }

        $activityLogger->log(
            'reservations',
            'created',
            $reservation,
            "Created reservation {$reservation->reservation_number}",
            [],
            $this->reservationActivityData($reservation->fresh([
                'guest',
                'corporate',
                'unit',
                'ratePlan',
                'reservationGuests.guest',
            ]))
        );

        $activityLogger->log(
            'invoices',
            'created',
            $invoice,
            "Generated invoice {$invoice->invoice_number} for reservation {$reservation->reservation_number}",
            [],
            $this->invoiceActivityData($invoice)
        );

        NotificationHelper::notifyNewReservation(auth()->id(), $reservation);

        // Send check-in notification
        if ($request->reservation_action === 'check_in') {
            NotificationHelper::notifyCheckIn(auth()->id(), $reservation);
        }

        // Send check-out notification
        if ($request->reservation_action === 'check_out') {
            NotificationHelper::notifyCheckOut(auth()->id(), $reservation);
        }

        return redirect()->route('dashboard.reservation.index')
            ->with('success', __('messages.reservation_created_successfully'));
    }

    public function getNotifications()
    {
        $today = now()->toDateString();

        // Get arrivals count
        $arrivalsCount = \App\Models\Reservation::whereDate('check_in_date', $today)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->count();

        // Get departures count
        $departuresCount = \App\Models\Reservation::whereDate('check_out_date', $today)
            ->where('status', 'checked_in')
            ->count();

        // Create arrival notification if exists
        if ($arrivalsCount > 0) {
            NotificationHelper::notifyArrivals(auth()->id(), $arrivalsCount);
        }

        // Create departure notification if exists
        if ($departuresCount > 0) {
            NotificationHelper::notifyDepartures(auth()->id(), $departuresCount);
        }

        return response()->json([
            'arrivals' => $arrivalsCount,
            'departures' => $departuresCount,
        ]);
    }

    public function markNotificationRead(Request $request)
    {
        $notification = \App\Models\Notification::findOrFail($request->id);
        $notification->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function markAllNotificationsRead()
    {
        \App\Models\Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function allNotifications()
    {
        $notifications = \App\Models\Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function cancelReservation(Request $request)
    {

        $reservation = Reservation::findOrFail($request->reservation_id);
        $beforeReservation = $this->reservationActivityData($reservation);

        if ($reservation->status == 'checked_in') {
            return redirect()->back()->with('danger', __('messages.checked_in_reaservation_cannot_be_cancelled'));
        }

        $penaltyAmount = 0;

        if ($request->penalty_id) {

            $penalty = Penalty::find($request->penalty_id);

            if ($penalty->penalty_type == 'percentage') {

                $penaltyAmount = ($reservation->total_rent * $penalty->value) / 100;

            } else {

                $penaltyAmount = $penalty->value;
            }

        }

        $reservation->update([
            'status' => 'cancelled',
            'cancel_reason_id' => $request->cancel_reason_id,
            'penalty_id' => $request->penalty_id,
            'penalty_amount' => $penaltyAmount,
            'cancelled_at' => now(),
        ]);

        app(UserActivityLogger::class)->log(
            'reservations',
            'cancelled',
            $reservation,
            "Cancelled reservation {$reservation->reservation_number}",
            $beforeReservation,
            $this->reservationActivityData($reservation->fresh(['guest', 'corporate', 'unit', 'ratePlan'])),
            [
                'refund_amount' => (float) ($request->refund_amount ?? 0),
                'cancel_reason_id' => $request->cancel_reason_id,
            ]
        );

        $refundAmount = floatval($request->refund_amount ?? 0);
        if ($refundAmount > 0) {
            $refundVoucher = PaymentVoucher::create([
                'reservation_id' => $reservation->id,
                'guest_id' => $reservation->guest_id,
                'voucher_number' => PaymentVoucher::generateVoucherNumber(),
                'voucher_type' => 'refund',
                'amount' => $refundAmount,
                'date' => now()->toDateString(),
                'time' => now()->format('H:i:s'),
                'purpose' => 'Reservation Refund - '.($reservation->guest ? $reservation->guest->first_name.' '.$reservation->guest->last_name : $reservation->corporate->name ?? 'Guest'),
                'created_by' => auth()->id(),
                'status' => 'pending',
                'payment_method_id' => $reservation->payment_method_id,
            ]);

            app(UserActivityLogger::class)->log(
                'payments',
                'created',
                $refundVoucher,
                "Created refund payment voucher {$refundVoucher->voucher_number} for cancelled reservation {$reservation->reservation_number}",
                [],
                $this->paymentVoucherActivityData($refundVoucher)
            );
        }

        // Auto-create Credit Note when reservation is cancelled with refund
        if ($refundAmount > 0) {
            $invoice = $reservation->invoice()->latest()->first();

            CreditNote::create([
                'credit_note_number' => CreditNote::generateCreditNoteNumber(),
                'invoice_type' => 'B2C',
                'reservation_id' => $reservation->id,
                'outlet_id' => $reservation->outlet_id,
                'invoice_id' => $invoice?->id,
                'invoice_number' => $invoice?->invoice_number,
                'guest_id' => $reservation->guest_id,
                'cn_date' => now()->toDateString(),
                'period_from' => $reservation->check_in_date,
                'period_to' => $reservation->check_out_date,
                'amount' => $refundAmount,
                'qr_code' => $invoice?->qr_code,
                'created_by' => auth()->id(),
            ]);
        }

        return redirect()->back()->with('success', __('messages.reservation_cancelled_successfully'));
    }

    public function getPenalties($id)
    {
        $reason = CancelReason::with('penalties')->findOrFail($id);

        return response()->json($reason->penalties);
    }

    public function downloadContractTemplate()
    {
        $property = Property::current(['commercialDetail']);
        $hotelTerms = \App\Models\HotelTerm::where('is_active', true)
            ->orderBy('order_no')
            ->get();

        return view('admin.reservation.contract-template', [
            'property' => $property,
            'reservation' => null,
            'hotelTerms' => $hotelTerms,
        ]);
    }

    public function downloadContract(Request $request, $reservation)
    {
        $reservation = $this->resolveAccessibleReservation($request, $reservation);
        $reservation->load(['guest', 'unit', 'corporate']);
        $property = Property::current(['commercialDetail']);
        $hotelTerms = \App\Models\HotelTerm::where('is_active', true)
            ->orderBy('order_no')
            ->get();

        return view('admin.reservation.contract-template', [
            'property' => $property,
            'reservation' => $reservation,
            'hotelTerms' => $hotelTerms,
        ]);
    }

    public function contractModal(Request $request, $reservation)
    {
        $reservation = $this->resolveAccessibleReservation($request, $reservation);
        $reservation->load(['guest', 'unit', 'corporate']);
        $property = Property::current(['commercialDetail']);
        $hotelTerms = \App\Models\HotelTerm::where('is_active', true)
            ->orderBy('order_no')
            ->get();

        $printingOption = \App\Models\PrintingOption::where('report_key', 'reservation_contract')->first();
        $globalSetting = \App\Models\PrintingOption::first();

        return view('admin.reservation.contract-modal', [
            'property' => $property,
            'reservation' => $reservation,
            'hotelTerms' => $hotelTerms,
            'printingOption' => $printingOption,
            'contractType' => $globalSetting?->contract_template_type ?? 'double',
        ]);
    }

    public function getAvailableUnits(Request $request)
    {
        $checkIn = $request->check_in_date;
        $checkOut = $request->check_out_date;
        $unitTypeId = $request->unit_type_id;
        $search = $request->search;

        $unitTypes = \App\Models\UnitTypeCustomization::all();

        // If no dates provided, return all active units (not filtered by availability)
        if (! $checkIn || ! $checkOut) {
            $query = Unit::with(['unitType', 'floor', 'block'])
                ->where('is_active', true);

            if ($unitTypeId) {
                $query->where('unit_type_id', $unitTypeId);
            }

            if ($search) {
                $query->where('unit_number', 'like', '%'.$search.'%');
            }

            $units = $query->get();

            return response()->json([
                'units' => $units,
                'unit_types' => $unitTypes,
            ]);
        }

        // Filter by availability when dates are provided
        $bookedUnitIds = Reservation::whereNotIn('status', ['cancelled', 'checked_out'])
            // Match the same strict overlap rule used when saving a reservation.
            ->where('check_in_date', '<', $checkOut)
            ->where('check_out_date', '>', $checkIn)
            ->pluck('unit_id')
            ->toArray();

        $query = Unit::with(['unitType', 'floor', 'block'])
            ->where('is_active', true)
            ->whereNotIn('id', $bookedUnitIds);

        if ($unitTypeId) {
            $query->where('unit_type_id', $unitTypeId);
        }

        if ($search) {
            $query->where('unit_number', 'like', '%'.$search.'%');
        }

        $units = $query->get();

        return response()->json([
            'units' => $units,
            'unit_types' => $unitTypes,
        ]);
    }

    public function getUnavailableUnits(Request $request)
    {
        $checkIn = $request->check_in_date;
        $checkOut = $request->check_out_date;

        if (! $checkIn || ! $checkOut) {
            return response()->json([
                'unavailable_unit_ids' => [],
            ]);
        }

        $unavailableUnitIds = Reservation::whereNotIn('status', ['cancelled', 'checked_out'])
            ->where('check_in_date', '<', $checkOut)
            ->where('check_out_date', '>', $checkIn)
            ->pluck('unit_id')
            ->toArray();

        return response()->json([
            'unavailable_unit_ids' => $unavailableUnitIds,
        ]);
    }

    public function calendarEvents(Request $request)
    {
        $start = $request->start ?? date('Y-m-01');
        $end = $request->end ?? date('Y-m-t');

        $reservations = Reservation::with(['guest', 'unit', 'unit.unitType'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('check_in_date', [$start, $end])
                    ->orWhereBetween('check_out_date', [$start, $end])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('check_in_date', '<=', $start)
                            ->where('check_out_date', '>=', $end);
                    });
            })
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $events = [];
        $colors = [
            'pending' => '#ffc107',
            'confirmed' => '#0d6efd',
            'checked_in' => '#198754',
            'checked_out' => '#6c757d',
            'no_show' => '#dc3545',
            'cancelled' => '#dc3545',
        ];

        foreach ($reservations as $reservation) {
            $guestName = $reservation->guest
                ? $reservation->guest->first_name.' '.$reservation->guest->last_name
                : 'Walk-in';
            $unitNumber = $reservation->unit ? $reservation->unit->unit_number : 'N/A';
            $color = $colors[$reservation->status] ?? '#6c757d';

            $events[] = [
                'id' => $reservation->id,
                'title' => "{$unitNumber} - {$guestName}",
                'start' => $reservation->check_in_date,
                'end' => date('Y-m-d', strtotime($reservation->check_out_date.'+1 day')),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'status' => $reservation->status,
                'guest_name' => $guestName,
                'unit_number' => $unitNumber,
                'nights' => $reservation->nights,
                'reservation_number' => $reservation->reservation_number,
            ];
        }

        return response()->json($events);
    }

    protected function resolveAccessibleReservation(Request $request, int|string $reservationId): Reservation
    {
        $reservation = Reservation::withoutGlobalScopes([
            CurrentPropertyScope::class,
            TenantScope::class,
        ])
            ->findOrFail($reservationId);

        $user = $request->user();

        $property = Property::query()
            ->where('branch_id', $reservation->branch_id)
            ->first();

        if ($user && (! $property || ! $user->canAccessProperty((int) $property->id))) {
            abort(404);
        }

        if ($property && app(PropertyContext::class)->id() !== $property->id) {
            $request->session()->put('current_property_id', $property->id);
            $request->session()->forget('property_id');
            app(PropertyContext::class)->setProperty($property);
        }

        return $reservation;
    }

    protected function syncUnitHousekeepingStatus(Reservation $reservation, ?string $reservationAction): void
    {
        if (! $reservationAction) {
            return;
        }

        $unit = Unit::withoutGlobalScopes()->find($reservation->unit_id);

        if (! $unit) {
            return;
        }

        if ($reservationAction === 'check_in') {
            $unit->update([
                'housekeeping_status' => 'clean',
            ]);

            return;
        }

        if ($reservationAction === 'check_out') {
            $unit->update([
                'housekeeping_status' => 'dirty',
            ]);
        }
    }

    protected function guestSelectionRules(int $branchId): array
    {
        return [
            'guest_id' => [
                'nullable',
                Rule::exists('guests', 'id')->where(fn ($query) => $query->where('branch_id', $branchId)),
            ],
            'corporate_id' => [
                'nullable',
                Rule::exists('corporates', 'id')->where(fn ($query) => $query->where('branch_id', $branchId)),
            ],
            'occupants' => ['nullable', 'array'],
            'occupants.*.guest_id' => [
                'nullable',
                'distinct',
                Rule::exists('guests', 'id')->where(fn ($query) => $query->where('branch_id', $branchId)),
            ],
            'occupants.*.relationship' => 'nullable|string|max:50',
        ];
    }

    protected function resolveStayEventAttributes(?Reservation $reservation, string $status, ?string $reservationAction): array
    {
        $attributes = [
            'checked_in_at' => $reservation?->checked_in_at,
            'checked_out_at' => $reservation?->checked_out_at,
            'no_show_at' => $reservation?->no_show_at,
            'shomoos_reported_at' => $reservation?->shomoos_reported_at,
        ];

        if ($reservationAction === 'check_in') {
            $attributes['checked_in_at'] = $reservation?->checked_in_at ?? now();
            $attributes['checked_out_at'] = null;
            $attributes['no_show_at'] = null;

            return $attributes;
        }

        if ($reservationAction === 'check_out') {
            $attributes['checked_in_at'] = $reservation?->checked_in_at ?? now();
            $attributes['checked_out_at'] = now();
            $attributes['no_show_at'] = null;

            return $attributes;
        }

        if ($status === 'no_show') {
            $attributes['checked_in_at'] = null;
            $attributes['checked_out_at'] = null;
            $attributes['no_show_at'] = $reservation?->no_show_at ?? now();

            return $attributes;
        }

        if ($status === 'checked_out') {
            $attributes['checked_in_at'] = $reservation?->checked_in_at ?? now();
            $attributes['checked_out_at'] = $reservation?->checked_out_at ?? now();
            $attributes['no_show_at'] = null;

            return $attributes;
        }

        if ($status === 'checked_in') {
            $attributes['checked_in_at'] = $reservation?->checked_in_at ?? now();
            $attributes['checked_out_at'] = null;
            $attributes['no_show_at'] = null;

            return $attributes;
        }

        if ($status !== 'checked_out' && $reservation?->status === 'checked_out' && $status === 'checked_in') {
            $attributes['checked_out_at'] = null;
        }

        if ($status !== 'no_show' && $reservation?->status === 'no_show') {
            $attributes['no_show_at'] = null;
        }

        return $attributes;
    }

    protected function syncShomoosForReservation(
        Reservation $reservation,
        ?string $reservationAction,
        string $status
    ): void {
        $eventType = $this->determineShomoosEventType($reservationAction, $status, $reservation);

        if (! $eventType) {
            return;
        }

        app(ShomoosService::class)->syncReservationEvent($reservation, $eventType);
    }

    protected function determineShomoosEventType(
        ?string $reservationAction,
        string $status,
        Reservation $reservation
    ): ?string {
        if ($reservationAction === 'check_in' || $status === 'checked_in') {
            return $reservation->shomoos_reported_at ? 'stay_update' : 'check_in';
        }

        if ($reservationAction === 'check_out' || $status === 'checked_out') {
            return 'check_out';
        }

        if ($status === 'no_show') {
            return 'no_show';
        }

        if ($reservationAction === 'update' && $reservation->checked_in_at) {
            return 'stay_update';
        }

        return null;
    }

    protected function syncNtmpForReservation(
        Reservation $reservation,
        ?string $reservationAction,
        string $status
    ): void {
        $eventType = $this->determineNtmpEventType($reservationAction, $status, $reservation);

        if (! $eventType) {
            return;
        }

        app(NtmpService::class)->syncReservationEvent($reservation, $eventType);
    }

    protected function determineNtmpEventType(
        ?string $reservationAction,
        string $status,
        Reservation $reservation
    ): ?string {
        if ($reservationAction === 'check_in' || $status === 'checked_in') {
            return $reservation->ntmp_reported_at ? 'stay_update' : 'check_in';
        }

        if ($reservationAction === 'check_out' || $status === 'checked_out') {
            return 'check_out';
        }

        if ($status === 'no_show') {
            return 'no_show';
        }

        if ($reservationAction === 'update' && $reservation->checked_in_at) {
            return 'stay_update';
        }

        return null;
    }

    protected function syncReservationOccupants(
        Reservation $reservation,
        Request $request,
        ?string $reservationAction,
        string $status
    ): void {
        $existingRecords = $reservation->reservationGuests()
            ->withoutGlobalScopes()
            ->get()
            ->keyBy('guest_id');

        $syncPayload = [];
        $primaryGuestId = $request->filled('guest_id') ? (int) $request->guest_id : null;

        if ($primaryGuestId) {
            $syncPayload[$primaryGuestId] = [
                'company_id' => $reservation->company_id,
                'branch_id' => $reservation->branch_id,
                'is_primary' => true,
                'relationship' => 'primary',
                ...$this->resolveOccupantStatuses(
                    $existingRecords->get($primaryGuestId),
                    $reservationAction,
                    $status
                ),
            ];
        }

        foreach ((array) $request->input('occupants', []) as $occupant) {
            $guestId = isset($occupant['guest_id']) ? (int) $occupant['guest_id'] : null;

            if (! $guestId || $guestId === $primaryGuestId) {
                continue;
            }

            $existingRecord = $existingRecords->get($guestId);

            $syncPayload[$guestId] = [
                'company_id' => $reservation->company_id,
                'branch_id' => $reservation->branch_id,
                'is_primary' => false,
                'relationship' => $occupant['relationship']
                    ?? $existingRecord?->relationship
                    ?? null,
                ...$this->resolveOccupantStatuses(
                    $existingRecord,
                    $reservationAction,
                    $status
                ),
            ];
        }

        if ($syncPayload === []) {
            $reservation->reservationGuests()->delete();

            return;
        }

        $reservation->occupants()->sync($syncPayload);
    }

    protected function resolveOccupantStatuses(
        ?ReservationGuest $existingRecord,
        ?string $reservationAction,
        string $status
    ): array {
        $checkInStatus = $existingRecord?->check_in_status ?? 'pending';
        $checkOutStatus = $existingRecord?->check_out_status ?? 'pending';

        if ($reservationAction === 'check_in') {
            return [
                'check_in_status' => 'checked_in',
                'check_out_status' => 'pending',
            ];
        }

        if ($reservationAction === 'check_out') {
            return [
                'check_in_status' => 'checked_in',
                'check_out_status' => 'checked_out',
            ];
        }

        if ($status === 'no_show') {
            return [
                'check_in_status' => 'no_show',
                'check_out_status' => 'pending',
            ];
        }

        if (! $existingRecord) {
            if ($status === 'checked_out') {
                return [
                    'check_in_status' => 'checked_in',
                    'check_out_status' => 'checked_out',
                ];
            }

            if ($status === 'checked_in') {
                return [
                    'check_in_status' => 'checked_in',
                    'check_out_status' => 'pending',
                ];
            }
        }

        if ($existingRecord?->check_in_status === 'no_show' && $status !== 'no_show') {
            $checkInStatus = 'pending';
        }

        return [
            'check_in_status' => $checkInStatus,
            'check_out_status' => $checkOutStatus,
        ];
    }

    protected function reservationActivityData(Reservation $reservation): array
    {
        $reservation->loadMissing(['guest', 'corporate', 'unit', 'ratePlan', 'reservationGuests.guest']);

        return [
            'reservation_number' => $reservation->reservation_number,
            'status' => $reservation->status,
            'guest_id' => $reservation->guest_id,
            'guest_name' => $reservation->guest ? trim($reservation->guest->first_name.' '.$reservation->guest->last_name) : null,
            'corporate_id' => $reservation->corporate_id,
            'corporate_name' => $reservation->corporate?->name,
            'unit_id' => $reservation->unit_id,
            'unit_number' => $reservation->unit?->unit_number,
            'rate_plan_id' => $reservation->rate_plan_id,
            'rate_plan_name' => $reservation->ratePlan?->name,
            'check_in_date' => $reservation->check_in_date?->format('Y-m-d'),
            'check_out_date' => $reservation->check_out_date?->format('Y-m-d'),
            'checked_in_at' => $reservation->checked_in_at?->toDateTimeString(),
            'checked_out_at' => $reservation->checked_out_at?->toDateTimeString(),
            'no_show_at' => $reservation->no_show_at?->toDateTimeString(),
            'occupant_count' => $reservation->reservationGuests->count(),
            'daily_rate' => (float) $reservation->daily_rate,
            'monthly_rate' => (float) $reservation->monthly_rate,
            'total_rent' => (float) $reservation->total_rent,
            'paid_amount' => (float) $reservation->paid_amount,
            'balance' => (float) $reservation->balance,
        ];
    }

    protected function invoiceActivityData(Invoice $invoice): array
    {
        return [
            'invoice_number' => $invoice->invoice_number,
            'status' => $invoice->status,
            'total' => (float) $invoice->total,
            'paid_amount' => (float) $invoice->paid_amount,
            'balance' => (float) $invoice->balance,
            'reservation_id' => $invoice->reservation_id,
        ];
    }

    protected function receiptVoucherActivityData(ReceiptVoucher $voucher): array
    {
        return [
            'voucher_number' => $voucher->voucher_number,
            'amount' => (float) $voucher->amount,
            'status' => $voucher->status,
            'payment_method_id' => $voucher->payment_method_id,
            'reservation_id' => $voucher->reservation_id,
            'purpose' => $voucher->purpose,
        ];
    }

    protected function paymentVoucherActivityData(PaymentVoucher $voucher): array
    {
        return [
            'voucher_number' => $voucher->voucher_number,
            'voucher_type' => $voucher->voucher_type,
            'amount' => (float) $voucher->amount,
            'status' => $voucher->status,
            'payment_method_id' => $voucher->payment_method_id,
            'reservation_id' => $voucher->reservation_id,
            'purpose' => $voucher->purpose,
        ];
    }
}
