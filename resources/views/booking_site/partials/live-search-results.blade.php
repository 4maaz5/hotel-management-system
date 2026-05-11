@php
    $t = fn ($ar, $en) => app()->getLocale() === 'ar' ? $ar : $en;
@endphp

<div class="page-card live-results-head">
    <div class="live-results-head__top">
        <div>
            <p class="live-results-kicker mb-2">{{ $t('التوفر المباشر', 'Live availability') }}</p>
            <h2 class="section-title">{{ $t('نتائج الحجز المباشر', 'Direct booking results') }}</h2>
            <p class="section-copy mb-0">{{ $t('تم تحديث النتائج دون إعادة تحميل الصفحة باستخدام التوفر الفعلي داخل النظام.', 'Results were refreshed without reloading the page using live availability from the reservation system.') }}</p>
        </div>

        <a href="{{ $resultsUrl }}" class="booking-cta booking-cta--soft">
            <i class="fas fa-up-right-from-square"></i>
            {{ $t('فتح صفحة النتائج', 'Open results page') }}
        </a>
    </div>

    <div class="live-results-meta">
        <span><i class="fas fa-moon"></i>{{ $selectedNightCount }} {{ $t('ليالٍ', 'nights') }}</span>
        <span><i class="fas fa-user-group"></i>{{ $search['adults'] + $search['children'] }} {{ $t('ضيوف', 'guests') }}</span>
        <span><i class="fas fa-check-circle"></i>{{ $availableRoomCount }} {{ $t('خيار متاح', 'available options') }}</span>
    </div>
</div>

@include('booking_site.partials.search-results-stack', [
    'rooms' => $rooms,
    'search' => $search,
    'bookingPropertyQuery' => $bookingPropertyQuery ?? collect($search)->only(['property_id', 'property_code'])->filter()->all(),
])
