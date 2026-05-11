<?php

namespace App\Http\Controllers;

use App\Mail\BookingConfirmationMail;
use App\Models\Guest;
use App\Models\HotelTerm;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentMethodConfig;
use App\Models\Property;
use App\Models\PropertyFacility;
use App\Models\RatePlanUnitType;
use App\Models\Reservation;
use App\Models\ReservationSetting;
use App\Models\ReservationSourceSetting;
use App\Models\SeasonalRate;
use App\Models\SpecialRate;
use App\Models\TaxFeeCustomization;
use App\Models\ThemeCustomization;
use App\Models\Unit;
use App\Models\UnitCustomRate;
use App\Models\UnitTypeCustomization;
use App\Models\UnitTypeRate;
use App\Models\WebsiteFaqItem;
use App\Models\WebsitePage;
use App\Models\WebsiteSetting;
use App\Services\Chatbot\ReservationPricingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookingEngineController extends Controller
{
    private ?Property $propertyCache = null;

    private ?Collection $roomTypeCache = null;

    private ?Collection $unitCache = null;

    private ?Collection $facilityCache = null;

    private ?Collection $policyCache = null;

    private ?Collection $paymentMethodCache = null;

    private ?Collection $ratePlanMapCache = null;

    private ?int $websiteSourceIdCache = null;

    private ?WebsiteSetting $websiteSettingsCache = null;

    private ?Collection $websitePageCache = null;

    private ?Collection $faqCache = null;

    private array $bookedUnitIdsCache = [];

    private array $activeTaxCache = [];

    public function __construct(
        private readonly ReservationPricingService $pricingService,
    ) {
    }

    public function home(Request $request)
    {
        $settings = $this->websiteSettings();
        $search = $this->defaultSearchPayload($request);
        $rooms = $this->buildRoomCatalog(
            Carbon::parse($search['check_in']),
            Carbon::parse($search['check_out']),
            $search['adults'],
            $search['children']
        );
        $showLiveSearchResults = $request->hasAny(['check_in', 'check_out', 'adults', 'children']);

        return view('booking_site.home', $this->sharedPageData([
            'search' => $search,
            'featuredRooms' => $rooms->take(3),
            'facilities' => $this->facilities()->take(8),
            'liveSearchResults' => $showLiveSearchResults ? $this->searchResultsPayload($search, $rooms) : null,
            'roomCount' => $this->roomTypes()->count(),
            'unitCount' => $this->units()->count(),
            'facilityCount' => $this->facilities()->count(),
            'pageTitle' => $this->localizedValue(
                $settings->default_seo_title_en,
                $settings->default_seo_title_ar,
                $this->propertyName()
            ),
            'metaDescription' => $this->localizedValue(
                $settings->default_seo_description_en,
                $settings->default_seo_description_ar,
                'Book direct with live availability, mobile-friendly checkout, and room details powered by your reservation system.'
            ),
            'structuredData' => [$this->hotelStructuredData()],
        ]));
    }

    public function rooms(Request $request)
    {
        $settings = $this->websiteSettings();
        $search = $this->defaultSearchPayload($request);
        $rooms = $this->buildRoomCatalog(
            Carbon::parse($search['check_in']),
            Carbon::parse($search['check_out']),
            $search['adults'],
            $search['children']
        );

        return view('booking_site.rooms', $this->sharedPageData([
            'search' => $search,
            'rooms' => $rooms,
            'searchMode' => false,
            'pageTitle' => $this->localizedValue(
                $settings->rooms_page_title_en,
                $settings->rooms_page_title_ar,
                'Rooms & Suites'
            ).' | '.$this->propertyName(),
            'metaDescription' => $this->localizedValue(
                $settings->rooms_page_intro_en,
                $settings->rooms_page_intro_ar,
                'Explore room types, amenities, occupancy, and direct booking availability.'
            ),
            'structuredData' => [$this->hotelStructuredData()],
        ]));
    }

    public function show(Request $request, string $roomType)
    {
        $search = $this->defaultSearchPayload($request);
        $unitModel = $this->resolvePublicUnit($roomType);
        $room = $this->transformUnit(
            $unitModel,
            Carbon::parse($search['check_in']),
            Carbon::parse($search['check_out']),
            $search['adults'],
            $search['children']
        );

        abort_unless($room, 404);

        return view('booking_site.show', $this->sharedPageData([
            'search' => $search,
            'room' => $room,
            'policies' => $this->policies()->take(6),
            'pageTitle' => ($room['seo_title'] ?: $room['name']).' | '.$this->propertyName(),
            'metaDescription' => $room['seo_description'] ?: ($room['summary'] ?: 'Explore unit details, amenities, and live direct-booking availability.'),
            'structuredData' => [
                $this->hotelStructuredData(),
                $this->roomStructuredData($room),
            ],
        ]));
    }

    public function search(Request $request)
    {
        if (! $request->filled('check_in') || ! $request->filled('check_out')) {
            return redirect()
                ->route('booking.rooms.index', $this->bookingPropertyQuery())
                ->withErrors([
                    'check_in' => 'Please select stay dates before searching availability.',
                ]);
        }

        $validated = $request->validate([
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'adults' => ['nullable', 'integer', 'min:1', 'max:10'],
            'children' => ['nullable', 'integer', 'min:0', 'max:10'],
        ]);

        $search = [
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'adults' => max(1, (int) ($validated['adults'] ?? 1)),
            'children' => max(0, (int) ($validated['children'] ?? 0)),
        ];
        $search = array_merge($search, $this->bookingPropertyQuery());
        $search = array_merge($search, $this->bookingPropertyQuery());

        $rooms = $this->buildRoomCatalog(
            Carbon::parse($search['check_in']),
            Carbon::parse($search['check_out']),
            $search['adults'],
            $search['children']
        );

        $resultsPayload = $this->searchResultsPayload($search, $rooms);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'html' => view('booking_site.partials.live-search-results', $resultsPayload)->render(),
                'historyUrl' => route('booking.home', $search),
                'resultsUrl' => route('booking.search', $search),
            ]);
        }

        return view('booking_site.rooms', $this->sharedPageData([
            ...$resultsPayload,
            'searchMode' => true,
            'pageTitle' => $this->localizedValue('Availability', 'التوفر').' | '.$this->propertyName(),
            'metaDescription' => $this->localizedValue(
                'Check live room availability and compare direct-booking options for your selected stay.',
                'تحقق من التوفر الفعلي وقارن خيارات الحجز المباشر لتواريخ إقامتك المختارة.'
            ),
            'robots' => 'noindex,nofollow',
        ]));
    }

    public function checkout(Request $request)
    {
        if (! $request->filled('unit_id') || ! $request->filled('check_in') || ! $request->filled('check_out')) {
            return redirect()
                ->route('booking.rooms.index', $this->bookingPropertyQuery())
                ->withErrors([
                    'unit_id' => 'Please choose a room before continuing to checkout.',
                ]);
        }

        $validated = $request->validate([
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'adults' => ['nullable', 'integer', 'min:1', 'max:10'],
            'children' => ['nullable', 'integer', 'min:0', 'max:10'],
        ]);

        $search = [
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'adults' => max(1, (int) ($validated['adults'] ?? 1)),
            'children' => max(0, (int) ($validated['children'] ?? 0)),
        ];

        $unitModel = $this->units()->firstWhere('id', (int) $validated['unit_id']);
        abort_unless($unitModel, 404);

        $room = $this->transformUnit(
            $unitModel,
            Carbon::parse($search['check_in']),
            Carbon::parse($search['check_out']),
            $search['adults'],
            $search['children']
        );

        if (! $room || ! $room['available']) {
            return redirect()
                ->route('booking.search', $search)
                ->withErrors([
                    'unit_id' => 'This unit is no longer available for the selected dates. Please choose another option.',
                ]);
        }

        return view('booking_site.checkout', $this->sharedPageData([
            'search' => $search,
            'room' => $room,
            'paymentMethods' => $this->paymentMethods(),
            'policies' => $this->policies()->take(3),
            'pageTitle' => 'Checkout | '.$room['name'].' | '.$this->propertyName(),
            'metaDescription' => 'Complete your direct booking with a mobile-friendly checkout experience.',
            'robots' => 'noindex,nofollow',
        ]));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'adults' => ['required', 'integer', 'min:1', 'max:10'],
            'children' => ['nullable', 'integer', 'min:0', 'max:10'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'payment_method_id' => ['nullable', 'integer', 'exists:payment_method_configs,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'agree_policies' => ['accepted'],
        ]);

        $unitModel = $this->units()->firstWhere('id', (int) $validated['unit_id']);
        abort_unless($unitModel, 404);

        $search = [
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'adults' => (int) $validated['adults'],
            'children' => (int) ($validated['children'] ?? 0),
        ];
        $search = array_merge($search, $this->bookingPropertyQuery());

        $room = $this->transformUnit(
            $unitModel,
            Carbon::parse($search['check_in']),
            Carbon::parse($search['check_out']),
            $search['adults'],
            $search['children']
        );

        if (! $room || ! $room['available'] || ! data_get($room, 'quote.selected_unit_id')) {
            return redirect()
                ->route('booking.search', $search)
                ->withErrors([
                    'unit_id' => 'Availability changed while you were filling the form. Please review the latest options.',
                ]);
        }

        $settings = ReservationSetting::getSettings();
        $property = $this->property();
        $paymentMethod = $validated['payment_method_id'] ? $this->paymentMethods()->firstWhere('id', (int) $validated['payment_method_id']) : null;
        $quote = $room['quote'];
        $unitId = (int) $quote['selected_unit_id'];
        $availabilityChangedMessage = 'booking_unit_unavailable';

        try {
            $reservation = DB::transaction(function () use ($validated, $search, $settings, $property, $paymentMethod, $quote, $unitId, $availabilityChangedMessage) {
                $lockedUnit = Unit::query()
                    ->whereKey($unitId)
                    ->lockForUpdate()
                    ->first();

                $unitStillAvailable = $lockedUnit
                    && (bool) $lockedUnit->is_active
                    && $this->unitAllowsGuestCount($lockedUnit, $search['adults'] + $search['children'], $this->contentCustomizationForUnit($lockedUnit))
                    && ! Reservation::query()
                        ->where('unit_id', $lockedUnit->id)
                        ->whereNotIn('status', ['cancelled', 'checked_out', 'no_show'])
                        ->where('check_in_date', '<', $search['check_out'])
                        ->where('check_out_date', '>', $search['check_in'])
                        ->lockForUpdate()
                        ->exists();

                if (! $unitStillAvailable) {
                    throw new \RuntimeException($availabilityChangedMessage);
                }

                $guest = $this->findOrCreateGuest($validated['full_name'], $validated['phone'], $validated['email'] ?? null);

                $reservation = Reservation::create([
                    'reservation_number' => Reservation::generateReservationNumber(),
                    'property_id' => $property?->id,
                    'guest_id' => $guest->id,
                    'unit_id' => $lockedUnit->id,
                    'source_id' => $this->websiteSourceId(),
                    'payment_method_id' => $paymentMethod?->id,
                    'check_in_date' => $search['check_in'],
                    'check_in_time' => $settings->check_in_time,
                    'check_out_date' => $search['check_out'],
                    'check_out_time' => $settings->check_out_time,
                    'nights' => $quote['nights'],
                    'adults' => $search['adults'],
                    'children' => $search['children'],
                    'reservation_type' => 'daily',
                    'daily_rate' => $quote['daily_rate'],
                    'monthly_rate' => 0,
                    'total_rent' => $quote['total_rent'],
                    'discount_type' => null,
                    'discount' => 0,
                    'total_taxes_fees' => $quote['total_taxes_fees'],
                    'security_deposit' => 0,
                    'paid_amount' => 0,
                    'balance' => $quote['grand_total'],
                    'subtotal' => $quote['subtotal'],
                    'grand_total' => $quote['grand_total'],
                    'status' => 'pending',
                    'is_confirmed' => false,
                    'booking_date' => now()->toDateString(),
                    'notes' => $validated['notes'] ?? null,
                ]);

                $invoice = Invoice::create([
                    'reservation_id' => $reservation->id,
                    'invoice_number' => Invoice::generateInvoiceNumber(),
                    'issue_date' => now()->toDateString(),
                    'due_date' => $search['check_in'],
                    'subtotal' => $quote['subtotal'],
                    'discount' => 0,
                    'discount_amount' => 0,
                    'tax_amount' => $quote['total_taxes_fees'],
                    'security_deposit' => 0,
                    'total' => $quote['grand_total'],
                    'paid_amount' => 0,
                    'balance' => $quote['grand_total'],
                    'status' => 'pending',
                    'payment_method' => $paymentMethod?->name,
                    'qr_code' => $this->safeQrCode(),
                ]);

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => 'Unit Charges ('.$quote['nights'].' night'.($quote['nights'] > 1 ? 's' : '').')',
                    'quantity' => $quote['nights'],
                    'unit_price' => $quote['daily_rate'],
                    'total' => $quote['total_rent'],
                ]);

                foreach ($quote['tax_breakdown'] as $taxItem) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'description' => $taxItem['name'],
                        'quantity' => 1,
                        'unit_price' => $taxItem['amount'],
                        'total' => $taxItem['amount'],
                    ]);
                }

                return $reservation;
            });
        } catch (\RuntimeException $e) {
            if ($e->getMessage() !== $availabilityChangedMessage) {
                throw $e;
            }

            return redirect()
                ->route('booking.search', $search)
                ->withErrors([
                    'unit_id' => 'Availability changed while you were filling the form. Please review the latest options.',
                ]);
        }

        if ($reservation->guest?->email) {
            try {
                Mail::to($reservation->guest->email)->send(
                    new BookingConfirmationMail(
                        $reservation->loadMissing(['guest', 'unit.unitType', 'property', 'invoice'])
                    )
                );
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()->route('booking.confirmation', array_merge([
            'reservationNumber' => $reservation->reservation_number,
        ], $this->bookingPropertyQuery()));
    }

    public function confirmation(string $reservationNumber)
    {
        $reservation = Reservation::query()
            ->with(['guest', 'unit.unitType', 'invoice'])
            ->where('reservation_number', $reservationNumber)
            ->firstOrFail();

        return view('booking_site.confirmation', $this->sharedPageData([
            'reservation' => $reservation,
            'pageTitle' => $this->localizedValue('Booking Confirmation', 'تأكيد الحجز').' | '.$reservation->reservation_number,
            'metaDescription' => $this->localizedValue(
                'Review your reservation details and confirmation reference.',
                'راجع تفاصيل الحجز والرقم المرجعي الخاص بك.'
            ),
            'robots' => 'noindex,nofollow',
        ]));
    }

    public function page(Request $request, string $pageKey)
    {
        abort_unless(in_array($pageKey, WebsitePage::supportedPageKeys(), true), 404);

        $page = $this->websitePage($pageKey);

        abort_unless($page && $page->is_published, 404);

        $search = $this->defaultSearchPayload($request);
        $structuredData = [$this->hotelStructuredData()];

        if ($pageKey === 'faq' && $this->faqItems()->isNotEmpty()) {
            $structuredData[] = $this->faqStructuredData();
        }

        return view('booking_site.page', $this->sharedPageData([
            'search' => $search,
            'page' => $page,
            'pageKey' => $pageKey,
            'faqItems' => $pageKey === 'faq' ? $this->faqItems() : collect(),
            'pageTitle' => ($this->pageTitle($page) ?: $this->propertyName()).' | '.$this->propertyName(),
            'metaDescription' => $this->pageMetaDescription($page),
            'metaKeywords' => $this->pageMetaKeywords($page),
            'structuredData' => $structuredData,
        ]));
    }

    private function sharedPageData(array $overrides = []): array
    {
        $settings = $this->websiteSettings();
        $property = $this->property();
        $phone = $settings->contact_phone_override ?: ($property?->phone ?: $property?->mobile ?: $property?->hot_line);
        $email = $settings->contact_email_override ?: $property?->email;
        $bookingPropertyQuery = $this->bookingPropertyQuery($property);

        return array_merge([
            'theme' => ThemeCustomization::getTheme(),
            'websiteSettings' => $settings,
            'navigationPages' => $this->navigationPages(),
            'branding' => [
                'name' => $this->propertyName(),
                'address' => $this->propertyAddress(),
                'phone' => $phone,
                'email' => $email,
                'logo_url' => $this->logoUrl(),
                'hero_image_url' => $this->heroImageUrl(),
                'tagline' => $this->localizedValue(
                    $settings->site_tagline_en,
                    $settings->site_tagline_ar,
                    'Direct booking powered by your reservation management system.'
                ),
            ],
            'bookingPropertyQuery' => $bookingPropertyQuery,
            'defaultSearch' => array_merge($this->defaultSearchPayload(), $bookingPropertyQuery),
            'canonicalUrl' => url()->full(),
            'robots' => 'index,follow',
            'metaKeywords' => null,
            'structuredData' => [],
        ], $overrides);
    }

    private function bookingPropertyQuery(?Property $property = null): array
    {
        $property ??= $this->property();

        if (! $property) {
            return [];
        }

        if (filled($property->property_code)) {
            return ['property_code' => $property->property_code];
        }

        return ['property_id' => $property->id];
    }

    private function defaultSearchPayload(?Request $request = null): array
    {
        return [
            'check_in' => $request?->query('check_in', now()->addDay()->toDateString()) ?? now()->addDay()->toDateString(),
            'check_out' => $request?->query('check_out', now()->addDays(2)->toDateString()) ?? now()->addDays(2)->toDateString(),
            'adults' => max(1, (int) ($request?->query('adults', 1) ?? 1)),
            'children' => max(0, (int) ($request?->query('children', 0) ?? 0)),
        ];
    }

    private function searchResultsPayload(array $search, Collection $rooms): array
    {
        return [
            'search' => $search,
            'rooms' => $rooms,
            'availableRoomCount' => $rooms->where('available', true)->count(),
            'selectedNightCount' => Carbon::parse($search['check_in'])->diffInDays(Carbon::parse($search['check_out'])),
            'resultsUrl' => route('booking.search', $search),
        ];
    }

    private function property(): ?Property
    {
        if ($this->propertyCache === null) {
            $this->propertyCache = Property::current([
                'mainPhoto',
                'photos' => fn ($query) => $query->orderBy('photo_order'),
            ]);
        }

        return $this->propertyCache;
    }

    private function roomTypes(): Collection
    {
        if ($this->roomTypeCache === null) {
            $this->roomTypeCache = UnitTypeCustomization::query()
                ->where('is_published_online', true)
                ->with([
                    'primaryImage',
                    'images' => fn ($query) => $query->orderBy('sort_order'),
                ])
                ->orderBy('website_sort_order')
                ->orderBy('name')
                ->get();
        }

        return $this->roomTypeCache;
    }

    private function units(): Collection
    {
        if ($this->unitCache === null) {
            $property = $this->property();

            $this->unitCache = Unit::query()
                ->when($property, fn ($query) => $query->forProperty($property))
                ->where('is_active', true)
                ->with(['amenities', 'unitTypeCustomization.unitType'])
                ->whereHas('unitTypeCustomization', function ($query) {
                    $query->where('is_published_online', true);
                })
                ->get();
        }

        return $this->unitCache;
    }

    private function facilities(): Collection
    {
        if ($this->facilityCache === null) {
            $this->facilityCache = PropertyFacility::query()
                ->with(['facility', 'category'])
                ->get()
                ->filter(fn (PropertyFacility $item) => $item->facility !== null)
                ->values();
        }

        return $this->facilityCache;
    }

    private function policies(): Collection
    {
        if ($this->policyCache === null) {
            $this->policyCache = HotelTerm::query()
                ->where('is_active', true)
                ->orderBy('order_no')
                ->get();
        }

        return $this->policyCache;
    }

    private function paymentMethods(): Collection
    {
        if ($this->paymentMethodCache === null) {
            $this->paymentMethodCache = PaymentMethodConfig::query()
                ->with('paymentMethod')
                ->where('is_active', true)
                ->get();
        }

        return $this->paymentMethodCache;
    }

    private function ratePlanMap(): Collection
    {
        if ($this->ratePlanMapCache === null) {
            $this->ratePlanMapCache = RatePlanUnitType::query()
                ->with('ratePlan')
                ->get();
        }

        return $this->ratePlanMapCache;
    }

    private function buildRoomCatalog(Carbon $checkIn, Carbon $checkOut, int $adults, int $children): Collection
    {
        return $this->units()
            ->map(fn (Unit $unit) => $this->transformUnit($unit, $checkIn, $checkOut, $adults, $children))
            ->filter()
            ->sortBy(function (array $room) {
                $availabilityRank = $room['available'] ? 0 : 1;
                $priceRank = $room['display_rate'] ?? 999999;

                return sprintf('%s-%015.2f-%s-%s', $availabilityRank, $priceRank, Str::lower($room['name']), Str::lower((string) $room['unit_number']));
            })
            ->values();
    }

    private function transformUnit(Unit $unit, Carbon $checkIn, Carbon $checkOut, int $adults, int $children): ?array
    {
        $content = $this->contentCustomizationForUnit($unit);
        $quote = $this->quoteForUnit($unit, $checkIn, $checkOut, $adults, $children, $content);
        $amenities = $unit->amenities
            ->take(6)
            ->map(fn ($amenity) => $amenity->name)
            ->values();

        $gallery = collect($content?->images ?? [])
            ->sortBy('sort_order')
            ->map(fn ($image) => $this->mediaUrl($image->image_path))
            ->filter()
            ->values();

        $unitLabel = $this->localizedValue(
            'Unit '.($unit->unit_number ?: $unit->id),
            'الوحدة '.($unit->unit_number ?: $unit->id),
            'Unit'
        );
        $name = $this->localizedValue(
            $content?->website_name_en ?: $content?->name ?: $content?->unitType?->name,
            $content?->website_name_ar,
            $content?->unitType?->name ?: $unitLabel
        );
        $name = trim($name) !== '' ? trim($name).' - '.$unitLabel : $unitLabel;
        $summary = $this->localizedValue(
            $content?->website_summary_en,
            $content?->website_summary_ar,
            Str::limit(trim((string) ($unit->description ?: $content?->description ?: $unitLabel)), 140)
        );
        $description = $this->localizedValue(
            $content?->website_description_en,
            $content?->website_description_ar,
            trim((string) ($unit->description ?: $content?->description ?: $summary))
        );
        $seoTitle = $this->localizedValue($content?->seo_title_en, $content?->seo_title_ar, $name);
        $seoTitle = Str::contains($seoTitle, (string) ($unit->unit_number ?: $unit->id))
            ? $seoTitle
            : trim($seoTitle.' - '.$unitLabel);
        $seoDescription = $this->localizedValue(
            $content?->seo_description_en,
            $content?->seo_description_ar,
            $summary
        );

        return [
            'id' => $unit->id,
            'slug' => $this->unitSlug($unit, $content),
            'name' => $name,
            'summary' => $summary,
            'description' => $description,
            'area' => $unit->unit_area ?: $content?->unit_area,
            'base_occupancy' => $unit->base_occupancy ?: $content?->base_occupancy,
            'single_beds' => (int) ($unit->number_of_single_beds ?: ($content?->single_beds ?? 0)),
            'double_beds' => (int) ($unit->number_of_double_beds ?: ($content?->double_beds ?? 0)),
            'bed_summary' => $this->bedSummaryForUnit($unit, $content),
            'image' => $gallery->first() ?: $this->heroImageUrl(),
            'gallery' => $gallery->isNotEmpty() ? $gallery : collect([$this->heroImageUrl()]),
            'amenities' => $amenities,
            'unit_number' => $unit->unit_number ?: (string) $unit->id,
            'unit_count' => 1,
            'available' => $quote !== null,
            'available_units_count' => $quote ? 1 : 0,
            'display_rate' => $quote ? (float) ($quote['daily_rate'] ?? 0) > 0 ? $quote['daily_rate'] : null : null,
            'quote' => $quote,
            'seo_title' => $seoTitle,
            'seo_description' => $seoDescription,
            'rate_plans' => $this->ratePlanMap()
                ->where('unit_type_id', $unit->unit_type_id)
                ->filter(fn ($item) => $item->ratePlan)
                ->map(fn ($item) => [
                    'name' => $item->ratePlan->name,
                    'daily_rate' => $item->daily_rate,
                ])
                ->values(),
        ];
    }

    private function quoteForUnit(
        Unit $unit,
        Carbon $checkIn,
        Carbon $checkOut,
        int $adults,
        int $children,
        ?UnitTypeCustomization $content = null
    ): ?array {
        if (! $this->unitAllowsGuestCount($unit, $adults + $children, $content)) {
            return null;
        }

        $bookedUnitIds = array_flip($this->bookedUnitIdsForStayRange($checkIn, $checkOut));

        if (isset($bookedUnitIds[$unit->id])) {
            return null;
        }

        return array_merge($this->pricingQuoteForUnit($unit, $checkIn, $checkOut), [
            'selected_unit_id' => $unit->id,
            'selected_unit_number' => $unit->unit_number,
            'available_units_count' => 1,
        ]);
    }

    private function pricingQuoteForUnit(Unit $unit, Carbon $checkIn, Carbon $checkOut): array
    {
        $quote = $this->pricingService->quote($unit, $checkIn, $checkOut, 'daily');
        $quote['tax_breakdown'] = $this->taxBreakdown((float) $quote['subtotal'], (int) $quote['nights'], $checkIn);

        return $quote;
    }

    private function getRatesForUnit(Unit $unit, Carbon $checkIn, Carbon $checkOut): array
    {
        $unitId = $unit->id;
        $unitTypeId = $unit->unit_type_id;

        // Check unit custom rates first
        $unitCustomRate = UnitCustomRate::where('unit_id', $unitId)->first();

        // Check unit type rates (base rates)
        $unitTypeRate = UnitTypeRate::where('unit_type_id', $unitTypeId)->where('is_active', true)->first();

        // Check for seasonal rates active during booking period
        $seasonalRates = [];
        $activeSeasonals = SeasonalRate::where('is_active', true)
            ->where('start_date', '<=', $checkOut->format('Y-m-d'))
            ->where('end_date', '>=', $checkIn->format('Y-m-d'))
            ->with(['unitRates' => function ($q) use ($unitTypeId) {
                $q->where('unit_type_id', $unitTypeId);
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

        // Check for special rates active during booking period
        $specialRates = [];
        $activeSpecials = SpecialRate::where('is_active', true)
            ->where('start_date', '<=', $checkOut->format('Y-m-d'))
            ->where('end_date', '>=', $checkIn->format('Y-m-d'))
            ->with(['unitRates' => function ($q) use ($unitTypeId) {
                $q->where('unit_type_id', $unitTypeId);
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

        return [
            'unit_id' => $unitId,
            'unit_type_id' => $unitTypeId,
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
        ];
    }

    private function contentCustomizationForUnit(Unit $unit): ?UnitTypeCustomization
    {
        return $this->roomTypes()->firstWhere('id', $unit->unit_type_id);
    }

    private function unitAllowsGuestCount(Unit $unit, int $guestCount, ?UnitTypeCustomization $content = null): bool
    {
        $capacity = (int) ($unit->base_occupancy ?: ($content?->base_occupancy ?? 0));

        return $capacity === 0 || $capacity >= $guestCount;
    }

    private function taxBreakdown(float $subtotal, int $nights, Carbon $checkIn): array
    {
        return $this->activeTaxesForDate($checkIn)
            ->map(function (TaxFeeCustomization $tax) use ($subtotal, $nights) {
                if ($tax->has_max_length && $tax->max_length && $nights > $tax->max_length) {
                    return null;
                }

                $amount = (float) $tax->amount;
                $value = match ($tax->method) {
                    'percentage' => ($subtotal * $amount) / 100,
                    'fixed_amount_per_night' => $amount * $nights,
                    default => $amount,
                };

                if ($value <= 0) {
                    return null;
                }

                return [
                    'name' => $tax->custom_name ?: 'Taxes & Fees',
                    'amount' => round($value, 2),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function bookedUnitIdsForStayRange(Carbon $checkIn, Carbon $checkOut): array
    {
        $cacheKey = $checkIn->toDateString().'|'.$checkOut->toDateString();

        if (! array_key_exists($cacheKey, $this->bookedUnitIdsCache)) {
            $property = $this->property();

            $this->bookedUnitIdsCache[$cacheKey] = Reservation::query()
                ->when($property, fn ($query) => $query->where('property_id', $property->id))
                ->whereNotIn('status', ['cancelled', 'checked_out', 'no_show'])
                ->where('check_in_date', '<', $checkOut->toDateString())
                ->where('check_out_date', '>', $checkIn->toDateString())
                ->pluck('unit_id')
                ->filter()
                ->map(fn ($unitId) => (int) $unitId)
                ->unique()
                ->values()
                ->all();
        }

        return $this->bookedUnitIdsCache[$cacheKey];
    }

    private function activeTaxesForDate(Carbon $checkIn): Collection
    {
        $cacheKey = $checkIn->toDateString();

        if (! array_key_exists($cacheKey, $this->activeTaxCache)) {
            $this->activeTaxCache[$cacheKey] = TaxFeeCustomization::query()
                ->where('is_expenses', false)
                ->whereDate('start_date', '<=', $cacheKey)
                ->where(function ($query) use ($cacheKey) {
                    $query->whereNull('end_date')
                        ->orWhereDate('end_date', '>=', $cacheKey);
                })
                ->get();
        }

        return $this->activeTaxCache[$cacheKey];
    }

    private function resolvePublicUnit(string $value): Unit
    {
        $id = (int) Str::before($value, '-');
        abort_if($id < 1, 404);

        $unit = $this->units()->firstWhere('id', $id);

        abort_unless($unit, 404);

        return $unit;
    }

    private function unitSlug(Unit $unit, ?UnitTypeCustomization $content = null): string
    {
        $base = trim(($content?->website_slug ?: $content?->name ?: $content?->unitType?->name ?: 'unit').' '.($unit->unit_number ?: $unit->id));

        return $unit->id.'-'.Str::slug($base);
    }

    private function propertyName(): string
    {
        $property = $this->property();

        if (! $property) {
            return 'Hotel Booking';
        }

        return app()->getLocale() === 'ar'
            ? ($property->property_name_ar ?: $property->property_name_en ?: 'Hotel Booking')
            : ($property->property_name_en ?: $property->property_name_ar ?: 'Hotel Booking');
    }

    private function propertyAddress(): string
    {
        $property = $this->property();

        if (! $property) {
            return 'Book direct for the best stay experience.';
        }

        $address = app()->getLocale() === 'ar'
            ? ($property->address_ar ?: $property->address_en)
            : ($property->address_en ?: $property->address_ar);

        return trim((string) $address) !== '' ? (string) $address : 'Direct booking powered by your reservation management system.';
    }

    private function heroImageUrl(): string
    {
        $property = $this->property();

        if ($property?->mainPhoto?->photo_path) {
            return $this->mediaUrl($property->mainPhoto->photo_path);
        }

        if ($property?->photos?->first()?->photo_path) {
            return $this->mediaUrl($property->photos->first()->photo_path);
        }

        $roomImage = $this->roomTypes()
            ->flatMap(fn (UnitTypeCustomization $roomType) => $roomType->images)
            ->sortBy('sort_order')
            ->first();

        if ($roomImage?->image_path) {
            return $this->mediaUrl($roomImage->image_path);
        }

        return asset('logo.webp');
    }

    private function logoUrl(): string
    {
        $property = $this->property();

        if ($property?->logo_url) {
            return $this->mediaUrl($property->logo_url);
        }

        return asset('logo.webp');
    }

    private function mediaUrl(?string $path): string
    {
        if (blank($path)) {
            return asset('logo.webp');
        }

        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }

        return Storage::url($path);
    }

    private function bedSummaryForUnit(Unit $unit, ?UnitTypeCustomization $content = null): string
    {
        $parts = [];
        $isArabic = app()->getLocale() === 'ar';
        $singleBeds = (int) ($unit->number_of_single_beds ?: ($content?->single_beds ?? 0));
        $doubleBeds = (int) ($unit->number_of_double_beds ?: ($content?->double_beds ?? 0));

        if ($singleBeds > 0) {
            $count = $singleBeds;
            $parts[] = $isArabic
                ? $count.' سرير مفرد'
                : $count.' single bed'.($count > 1 ? 's' : '');
        }

        if ($doubleBeds > 0) {
            $count = $doubleBeds;
            $parts[] = $isArabic
                ? $count.' سرير مزدوج'
                : $count.' double bed'.($count > 1 ? 's' : '');
        }

        return $parts !== [] ? implode($isArabic ? ' + ' : ' + ', $parts) : $this->localizedValue('Flexible bedding', 'خيارات نوم مرنة');
    }

    private function hotelStructuredData(): array
    {
        $property = $this->property();
        $websiteSettings = $this->websiteSettings();
        $phone = $websiteSettings->contact_phone_override ?: ($property?->phone ?: $property?->mobile ?: null);
        $email = $websiteSettings->contact_email_override ?: ($property?->email ?: null);
        $settings = ReservationSetting::getSettings();

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Hotel',
            'name' => $this->propertyName(),
            'description' => $this->localizedValue(
                $websiteSettings->default_seo_description_en,
                $websiteSettings->default_seo_description_ar,
                'Direct booking website with live room availability and reservation-ready checkout.'
            ),
            'url' => route('booking.home'),
            'telephone' => $phone,
            'email' => $email,
            'image' => [$this->heroImageUrl()],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $this->propertyAddress(),
            ],
            'checkinTime' => $settings->check_in_time,
            'checkoutTime' => $settings->check_out_time,
        ], fn ($value) => $value !== null && $value !== []);
    }

    private function roomStructuredData(array $room): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'HotelRoom',
            'name' => $room['name'],
            'description' => $room['seo_description'] ?: ($room['summary'] ?: $room['description']),
            'image' => $room['gallery']->all(),
            'occupancy' => $room['base_occupancy'] ?: null,
            'offers' => $room['display_rate'] ? [
                '@type' => 'Offer',
                'priceCurrency' => 'SAR',
                'price' => $room['display_rate'],
                'availability' => $room['available']
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/SoldOut',
                'url' => route('booking.rooms.show', ['roomType' => $room['slug']]),
            ] : null,
        ], fn ($value) => $value !== null && $value !== []);
    }

    private function websiteSettings(): WebsiteSetting
    {
        return $this->websiteSettingsCache ??= WebsiteSetting::getSettings();
    }

    private function websitePages(): Collection
    {
        if ($this->websitePageCache === null) {
            WebsitePage::ensureDefaults();

            $this->websitePageCache = WebsitePage::query()
                ->whereIn('page_key', WebsitePage::supportedPageKeys())
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        }

        return $this->websitePageCache;
    }

    private function navigationPages(): Collection
    {
        return $this->websitePages()
            ->where('is_published', true)
            ->where('show_in_navigation', true)
            ->values();
    }

    private function websitePage(string $pageKey): ?WebsitePage
    {
        return $this->websitePages()->firstWhere('page_key', $pageKey);
    }

    private function faqItems(): Collection
    {
        if ($this->faqCache === null) {
            WebsiteFaqItem::ensureDefaults();

            $this->faqCache = WebsiteFaqItem::query()
                ->where('is_published', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        }

        return $this->faqCache;
    }

    private function localizedValue(?string $en, ?string $ar, string $default = ''): string
    {
        return app()->getLocale() === 'ar'
            ? (filled($ar) ? trim($ar) : (filled($en) ? trim($en) : $default))
            : (filled($en) ? trim($en) : (filled($ar) ? trim($ar) : $default));
    }

    private function pageTitle(WebsitePage $page): string
    {
        return $this->localizedValue(
            $page->seo_title_en ?: $page->title_en,
            $page->seo_title_ar ?: $page->title_ar,
            ucfirst($page->page_key)
        );
    }

    private function pageMetaDescription(WebsitePage $page): string
    {
        return $this->localizedValue(
            $page->seo_description_en ?: $page->hero_intro_en,
            $page->seo_description_ar ?: $page->hero_intro_ar,
            $this->localizedValue(
                $this->websiteSettings()->default_seo_description_en,
                $this->websiteSettings()->default_seo_description_ar,
                'Direct booking website with live availability.'
            )
        );
    }

    private function pageMetaKeywords(WebsitePage $page): ?string
    {
        $keywords = $this->localizedValue(
            $page->seo_keywords_en,
            $page->seo_keywords_ar
        );

        return $keywords !== '' ? $keywords : null;
    }

    private function faqStructuredData(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $this->faqItems()->map(function (WebsiteFaqItem $item) {
                return [
                    '@type' => 'Question',
                    'name' => $this->localizedValue($item->question_en, $item->question_ar, $item->question_en),
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $this->localizedValue($item->answer_en, $item->answer_ar, $item->answer_en),
                    ],
                ];
            })->values()->all(),
        ];
    }

    private function findOrCreateGuest(string $fullName, string $phone, ?string $email = null): Guest
    {
        [$dialCode, $mobileNumber] = $this->splitPhone($phone);
        [$firstName, $secondName, $lastName] = $this->splitName($fullName);

        $guest = Guest::query()
            ->where('mobile_number', $mobileNumber)
            ->when($dialCode, fn ($query) => $query->where('mobile_dial_code', $dialCode))
            ->first();

        if ($guest) {
            $guest->update(array_filter([
                'first_name' => $firstName,
                'second_name' => $secondName ?: null,
                'last_name' => $lastName,
                'email' => $email ?: null,
                'guest_type' => 'individual',
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
            'email' => $email ?: null,
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

    private function websiteSourceId(): ?int
    {
        if ($this->websiteSourceIdCache !== null) {
            return $this->websiteSourceIdCache;
        }

        $source = ReservationSourceSetting::query()
            ->whereHas('masterSource', function ($query) {
                $query->where('name', 'like', '%Website%');
            })
            ->first();

        $this->websiteSourceIdCache = $source?->id;

        return $this->websiteSourceIdCache;
    }
}
