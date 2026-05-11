@php
    $t = fn ($ar, $en) => app()->getLocale() === 'ar' ? $ar : $en;
    $search = $search ?? $defaultSearch;
    $ajaxMode = $ajaxMode ?? false;
    $bookingPropertyQuery = $bookingPropertyQuery ?? [];
@endphp

<form
    method="GET"
    action="{{ route('booking.search', $bookingPropertyQuery) }}"
    class="booking-search"
    data-booking-search
    @if ($ajaxMode)
        data-booking-search-ajax="true"
    @endif
>
    <div class="booking-search__grid">
        @foreach ($bookingPropertyQuery as $name => $value)
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endforeach
        <div class="booking-search__field">
            <label for="check_in" class="booking-search__label">
                <i class="fas fa-calendar-check"></i>
                {{ $t('الوصول', 'Check-in') }}
            </label>
            <input id="check_in" type="date" name="check_in" value="{{ $search['check_in'] }}" min="{{ now()->toDateString() }}" class="booking-search__input" required>
        </div>
        <div class="booking-search__field">
            <label for="check_out" class="booking-search__label">
                <i class="fas fa-calendar-xmark"></i>
                {{ $t('المغادرة', 'Check-out') }}
            </label>
            <input id="check_out" type="date" name="check_out" value="{{ $search['check_out'] }}" min="{{ $search['check_in'] }}" class="booking-search__input" required>
        </div>
        <div class="booking-search__field">
            <label for="adults" class="booking-search__label">
                <i class="fas fa-user"></i>
                {{ $t('البالغون', 'Adults') }}
            </label>
            <select id="adults" name="adults" class="booking-search__input" required>
                @for ($i = 1; $i <= 6; $i++)
                    <option value="{{ $i }}" @selected((int) $search['adults'] === $i)>{{ $i }} {{ $i === 1 ? $t('بالغ', 'Adult') : $t('بالغين', 'Adults') }}</option>
                @endfor
            </select>
        </div>
        <div class="booking-search__field">
            <label for="children" class="booking-search__label">
                <i class="fas fa-child"></i>
                {{ $t('الأطفال', 'Children') }}
            </label>
            <select id="children" name="children" class="booking-search__input">
                @for ($i = 0; $i <= 6; $i++)
                    <option value="{{ $i }}" @selected((int) $search['children'] === $i)>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="booking-search__actions">
            <button type="submit" class="booking-cta booking-cta--primary w-100 booking-search__button">
                <span class="booking-search__button-icon">
                    <i class="fas fa-magnifying-glass"></i>
                </span>
                <span class="booking-search__button-text">{{ $t('بحث', 'Search') }}</span>
            </button>
        </div>
    </div>
</form>
