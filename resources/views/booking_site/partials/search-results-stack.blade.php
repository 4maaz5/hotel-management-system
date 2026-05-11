@php
    $t = fn ($ar, $en) => app()->getLocale() === 'ar' ? $ar : $en;
@endphp

<div class="live-results-stack">
    @forelse ($rooms as $room)
        @include('booking_site.partials.room-card', ['room' => $room, 'search' => $search])
    @empty
        <div class="page-card booking-empty-state">
            <h3 class="mb-2">{{ $t('لا توجد نتائج مطابقة حالياً', 'No matching rooms right now') }}</h3>
            <p class="section-copy mb-0">{{ $t('جرّب تغيير التواريخ أو عدد الضيوف لإظهار خيارات أخرى.', 'Try different dates or guest counts to reveal more options.') }}</p>
        </div>
    @endforelse
</div>
