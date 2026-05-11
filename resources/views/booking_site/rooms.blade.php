@extends('layouts.booking-site')

@section('content')
    @php
        $t = fn ($ar, $en) => app()->getLocale() === 'ar' ? $ar : $en;
        $selectedNightCount = \Carbon\Carbon::parse($search['check_in'])->diffInDays(\Carbon\Carbon::parse($search['check_out']));
        $roomsTitle = app()->getLocale() === 'ar' ? ($websiteSettings->rooms_page_title_ar ?: $websiteSettings->rooms_page_title_en) : ($websiteSettings->rooms_page_title_en ?: $websiteSettings->rooms_page_title_ar);
        $roomsIntro = app()->getLocale() === 'ar' ? ($websiteSettings->rooms_page_intro_ar ?: $websiteSettings->rooms_page_intro_en) : ($websiteSettings->rooms_page_intro_en ?: $websiteSettings->rooms_page_intro_ar);
    @endphp

    <style>
        .rooms-hero {
            padding: clamp(1.5rem, 4vw, 2.5rem);
            margin-bottom: 1.2rem;
        }

        .rooms-hero__meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .rooms-hero__meta span {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.75rem 0.95rem;
            border-radius: 999px;
            background: rgba(20, 33, 61, 0.06);
            font-weight: 700;
            color: var(--muted);
        }

        @media (max-width: 767px) {
            .rooms-hero {
                padding: 1.2rem;
            }

            .rooms-hero__meta span {
                width: 100%;
                justify-content: center;
            }

            .rooms-hero .booking-cta {
                width: 100%;
            }
        }
    </style>

    <section class="page-card rooms-hero">
        <div class="d-flex justify-content-between gap-3 flex-wrap">
            <div>
                <h1 class="section-title">
                    {{ $searchMode ? $t('نتائج التوفر المباشر', 'Live availability results') : $roomsTitle }}
                </h1>
                <p class="section-copy">
                    {{ $searchMode ? $t('تمت مقارنة الوحدات حسب الجاهزية للحجز والسعر الفعلي ضمن النظام الحالي.', 'Unit options are compared using live availability and current pricing from the reservation system.') : $roomsIntro }}
                </p>
            </div>
            <div class="d-flex gap-2 flex-wrap align-items-start">
                <a href="{{ route('booking.home', $bookingPropertyQuery ?? []) }}" class="booking-cta booking-cta--soft">
                    <i class="fas fa-house"></i>
                    {{ $t('العودة للرئيسية', 'Back home') }}
                </a>
            </div>
        </div>

        <div class="mt-4">
            @include('booking_site.partials.search-form', ['search' => $search])
        </div>

        <div class="rooms-hero__meta">
            <span><i class="fas fa-moon"></i>{{ $selectedNightCount }} {{ $t('ليالٍ', 'nights') }}</span>
            <span><i class="fas fa-user-group"></i>{{ $search['adults'] + $search['children'] }} {{ $t('ضيوف', 'guests') }}</span>
            @if ($searchMode)
                <span><i class="fas fa-check-circle"></i>{{ $availableRoomCount }} {{ $t('خيار متاح', 'available options') }}</span>
            @endif
        </div>
    </section>

    <section class="rooms-stack">
        @include('booking_site.partials.search-results-stack', ['rooms' => $rooms, 'search' => $search])
    </section>
@endsection
