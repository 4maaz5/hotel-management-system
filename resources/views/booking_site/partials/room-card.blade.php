@php
    $t = fn ($ar, $en) => app()->getLocale() === 'ar' ? $ar : $en;
    $roomTitle = trim(preg_replace('/\s+-\s+Unit\s+'.preg_quote((string) $room['unit_number'], '/').'$/i', '', $room['name']));
    $checkoutQuery = [
        'unit_id' => $room['id'],
        'check_in' => $search['check_in'],
        'check_out' => $search['check_out'],
        'adults' => $search['adults'],
        'children' => $search['children'],
    ];
    $bookingPropertyQuery = $bookingPropertyQuery ?? [];
    $checkoutQuery = array_merge($checkoutQuery, $bookingPropertyQuery);
    $roomDetailsQuery = array_merge([
        'roomType' => $room['slug'],
        'check_in' => $search['check_in'],
        'check_out' => $search['check_out'],
        'adults' => $search['adults'],
        'children' => $search['children'],
    ], $bookingPropertyQuery);
@endphp

<article class="page-card room-card">
    <div class="room-card__media">
        <img src="{{ $room['image'] }}" alt="{{ $room['name'] }}">
        <div class="room-card__badge {{ $room['available'] ? '' : 'is-soldout' }}">
            <i class="fas {{ $room['available'] ? 'fa-check-circle' : 'fa-circle-exclamation' }}"></i>
            {{ $room['available'] ? $t('متاح', 'Available') : $t('غير متاح', 'Unavailable') }}
        </div>
    </div>

    <div class="room-card__body">
        <div class="room-card__title-row">
            <h3 class="room-card__title">{{ $roomTitle ?: $room['name'] }}</h3>
            <span class="room-card__unit">
                <i class="fas fa-door-open"></i>
                {{ $room['unit_number'] }}
            </span>
        </div>

        <p class="room-card__summary">{{ $room['summary'] }}</p>

        <div class="room-card__facts">
            @if ($room['base_occupancy'])
                <span><i class="fas fa-user-group"></i>{{ $room['base_occupancy'] }} {{ $t('ضيوف', 'guests') }}</span>
            @endif
            @if ($room['area'])
                <span><i class="fas fa-ruler-combined"></i>{{ number_format((float) $room['area'], 0) }} sqm</span>
            @endif
            <span><i class="fas fa-bed"></i>{{ $room['bed_summary'] }}</span>
        </div>

        @if (count($room['amenities']) > 0)
            <div class="room-card__amenities">
                @foreach ($room['amenities'] as $amenity)
                    <span>{{ $amenity }}</span>
                @endforeach
            </div>
        @endif

        <div class="room-card__footer">
            <div class="room-card__price">
                @if ($room['display_rate'])
                    <small>{{ $t('ابتداء من', 'From') }}</small>
                    <strong>SAR {{ number_format((float) $room['display_rate'], 2) }}</strong>
                @else
                    <small>{{ $t('السعر حسب التوفر', 'Rate on availability') }}</small>
                @endif
            </div>

            <div class="room-card__actions">
                <a href="{{ route('booking.rooms.show', $roomDetailsQuery) }}" class="booking-cta booking-cta--soft">
                    <i class="fas fa-eye"></i>
                    {{ $t('التفاصيل', 'Details') }}
                </a>
                @if ($room['available'])
                    <a href="{{ route('booking.checkout', $checkoutQuery) }}" class="booking-cta booking-cta--primary">
                        <i class="fas fa-arrow-right"></i>
                        {{ $t('احجز', 'Reserve') }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</article>
