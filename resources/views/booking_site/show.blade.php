@extends('layouts.booking-site')

@section('content')
    @php
        $t = fn ($ar, $en) => app()->getLocale() === 'ar' ? $ar : $en;
        $quote = $room['quote'];
        $checkoutQuery = [
            'unit_id' => $room['id'],
            'check_in' => $search['check_in'],
            'check_out' => $search['check_out'],
            'adults' => $search['adults'],
            'children' => $search['children'],
        ];
        $bookingPropertyQuery = $bookingPropertyQuery ?? [];
        $searchQuery = array_merge($search, $bookingPropertyQuery);
        $checkoutQuery = array_merge($checkoutQuery, $bookingPropertyQuery);
    @endphp

    <style>
        .detail-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(320px, 380px);
            gap: 1.35rem;
            align-items: start;
        }

        .detail-column {
            display: grid;
            gap: 1rem;
            align-content: start;
            min-width: 0;
        }

        .detail-gallery {
            display: grid;
            gap: 0.9rem;
        }

        .detail-gallery__main img,
        .detail-gallery__thumbs img {
            width: 100%;
            object-fit: cover;
            border-radius: 24px;
        }

        .detail-gallery__main img {
            height: min(56vw, 460px);
        }

        .detail-gallery__thumbs {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.75rem;
        }

        .detail-gallery__thumbs img {
            height: 140px;
        }

        .detail-panel,
        .detail-section {
            padding: 1.4rem;
        }

        .detail-panel--hero {
            padding: 1.55rem;
        }

        .detail-sticky {
            min-width: 0;
        }

        .detail-summary-card {
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(250, 246, 237, 0.98)),
                rgba(255, 255, 255, 0.98);
            border: 1px solid rgba(179, 138, 61, 0.24);
            box-shadow: 0 22px 48px rgba(24, 49, 83, 0.12);
            position: sticky;
            top: 6.5rem;
            align-self: start;
        }

        .detail-section {
            background: rgba(255, 255, 255, 0.9);
        }

        .detail-meta,
        .detail-amenities,
        .detail-rateplans {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .detail-meta span,
        .detail-amenities span,
        .detail-rateplans span {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.7rem 0.9rem;
            border-radius: 999px;
            background: rgba(20, 33, 61, 0.05);
            font-weight: 700;
            color: var(--muted);
        }

        .detail-breakdown {
            display: grid;
            gap: 0.8rem;
        }

        .detail-breakdown__row {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            color: var(--muted);
            padding: 0.8rem 0;
            border-bottom: 1px solid rgba(20, 33, 61, 0.08);
        }

        .detail-breakdown__row:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        @media (max-width: 1320px) {
            .detail-grid {
                grid-template-columns: 1fr;
            }

            .detail-summary-card {
                position: static;
            }
        }

        @media (max-width: 767px) {
            .detail-panel,
            .detail-section {
                padding: 1.1rem;
            }

            .detail-panel--hero {
                padding: 1.2rem;
            }

            .detail-gallery__main img {
                height: 260px;
            }

            .detail-gallery__thumbs {
                grid-template-columns: 1fr 1fr;
            }

            .detail-breakdown__row {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.3rem;
            }

            .detail-meta span,
            .detail-amenities span,
            .detail-rateplans span {
                width: 100%;
                justify-content: center;
            }

            .detail-sticky .booking-cta,
            .detail-panel .booking-cta {
                width: 100%;
            }
        }

        @media (max-width: 575px) {
            .detail-gallery__thumbs {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <section class="detail-grid">
        <div class="detail-column">
            <article class="page-card detail-panel detail-panel--hero">
                <div class="d-flex justify-content-between gap-3 flex-wrap align-items-start mb-4">
                    <div>
                        <p class="text-uppercase fw-bold mb-2" style="letter-spacing:0.16em; color: var(--muted);">{{ $t('تفاصيل الوحدة', 'Unit detail') }}</p>
                        <h1 class="section-title">{{ $room['name'] }}</h1>
                        <p class="section-copy">{{ $room['summary'] }}</p>
                    </div>
                    <a href="{{ route('booking.rooms.index', $searchQuery) }}" class="booking-cta booking-cta--soft">
                        <i class="fas fa-arrow-left"></i>
                        {{ $t('كل الوحدات', 'All units') }}
                    </a>
                </div>

                <div class="detail-meta mb-4">
                    @if ($room['base_occupancy'])
                        <span><i class="fas fa-user-group"></i>{{ $room['base_occupancy'] }} {{ $t('ضيوف', 'guests') }}</span>
                    @endif
                    @if ($room['area'])
                        <span><i class="fas fa-ruler-combined"></i>{{ number_format((float) $room['area'], 0) }} sqm</span>
                    @endif
                    <span><i class="fas fa-bed"></i>{{ $room['bed_summary'] }}</span>
                    <span><i class="fas fa-door-open"></i>{{ $t('رقم الوحدة', 'Unit number') }} {{ $room['unit_number'] }}</span>
                </div>

                <div class="detail-gallery mb-4">
                    <div class="detail-gallery__main">
                        <img src="{{ $room['gallery']->first() }}" alt="{{ $room['name'] }}">
                    </div>
                    @if ($room['gallery']->count() > 1)
                        <div class="detail-gallery__thumbs">
                            @foreach ($room['gallery']->slice(1, 3) as $image)
                                <img src="{{ $image }}" alt="{{ $room['name'] }}">
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="section-copy">{!! nl2br(e($room['description'])) !!}</div>
            </article>

            <article class="page-card detail-section">
                <h2 class="h4 fw-bold mb-3">{{ $t('المزايا الأساسية', 'Guest-facing highlights') }}</h2>
                <div class="detail-amenities">
                    @forelse ($room['amenities'] as $amenity)
                        <span>{{ $amenity }}</span>
                    @empty
                        <span>{{ $t('ستظهر وسائل الراحة هنا بعد ربطها بالوحدات في الإعدادات.', 'Amenities will appear here once they are linked to units in setup.') }}</span>
                    @endforelse
                </div>
            </article>

            @if (count($room['rate_plans']) > 0)
                <article class="page-card detail-section">
                    <h2 class="h4 fw-bold mb-3">{{ $t('خطط الأسعار المرتبطة', 'Mapped rate plans') }}</h2>
                    <div class="detail-rateplans">
                        @foreach ($room['rate_plans'] as $plan)
                            <span>
                                <i class="fas fa-tags"></i>
                                {{ $plan['name'] }}
                                @if ($plan['daily_rate'])
                                    <strong>SAR {{ number_format((float) $plan['daily_rate'], 2) }}</strong>
                                @endif
                            </span>
                        @endforeach
                    </div>
                </article>
            @endif

            <article class="page-card detail-section">
                <h2 class="h4 fw-bold mb-3">{{ $t('سياسات مهمة قبل الإكمال', 'Policies to review before checkout') }}</h2>
                <div class="d-grid gap-2">
                    @forelse ($policies as $policy)
                        <div class="section-copy">{{ $policy->description }}</div>
                    @empty
                        <div class="section-copy">{{ $t('يمكن ربط هذه المنطقة بسياسات الفندق الحالية داخل النظام.', 'This area reflects the hotel policies maintained inside the current system.') }}</div>
                    @endforelse
                </div>
            </article>
        </div>

        <aside class="detail-column detail-sticky">
            <article class="page-card detail-panel detail-summary-card">
                <p class="text-uppercase fw-bold mb-2" style="letter-spacing:0.14em; color: var(--muted);">{{ $t('ملخص الحجز', 'Booking summary') }}</p>
                <h2 class="h3 fw-bold mb-3">{{ $room['available'] ? $t('جاهز للإكمال', 'Ready to reserve') : $t('جرّب تواريخ أخرى', 'Try other dates') }}</h2>

                <div class="detail-breakdown mb-4">
                    <div class="detail-breakdown__row">
                        <span>{{ $t('تسجيل الوصول', 'Check-in') }}</span>
                        <strong>{{ $search['check_in'] }}</strong>
                    </div>
                    <div class="detail-breakdown__row">
                        <span>{{ $t('تسجيل المغادرة', 'Check-out') }}</span>
                        <strong>{{ $search['check_out'] }}</strong>
                    </div>
                    <div class="detail-breakdown__row">
                        <span>{{ $t('الضيوف', 'Guests') }}</span>
                        <strong>{{ $search['adults'] + $search['children'] }}</strong>
                    </div>
                    <div class="detail-breakdown__row">
                        <span>{{ $t('رقم الوحدة', 'Unit number') }}</span>
                        <strong>{{ $room['unit_number'] }}</strong>
                    </div>
                </div>

                @if ($quote)
                    <div class="detail-breakdown mb-4">
                        <div class="detail-breakdown__row">
                            <span>{{ $t('متوسط سعر الليلة', 'Average nightly rate') }}</span>
                            <strong>SAR {{ number_format((float) $quote['daily_rate'], 2) }}</strong>
                        </div>
                        <div class="detail-breakdown__row">
                            <span>{{ $t('الإقامة', 'Stay subtotal') }}</span>
                            <strong>SAR {{ number_format((float) $quote['subtotal'], 2) }}</strong>
                        </div>
                        @foreach ($quote['tax_breakdown'] as $tax)
                            <div class="detail-breakdown__row">
                                <span>{{ $tax['name'] }}</span>
                                <strong>SAR {{ number_format((float) $tax['amount'], 2) }}</strong>
                            </div>
                        @endforeach
                        <div class="detail-breakdown__row" style="color: var(--ink); font-size: 1.08rem;">
                            <span>{{ $t('الإجمالي', 'Grand total') }}</span>
                            <strong>SAR {{ number_format((float) $quote['grand_total'], 2) }}</strong>
                        </div>
                    </div>
                @endif

                @if ($room['available'])
                    <a href="{{ route('booking.checkout', $checkoutQuery) }}" class="booking-cta booking-cta--primary w-100">
                        <i class="fas fa-credit-card"></i>
                        {{ $t('انتقل إلى الدفع', 'Continue to checkout') }}
                    </a>
                @else
                    <a href="{{ route('booking.search', $searchQuery) }}" class="booking-cta booking-cta--soft w-100">
                        <i class="fas fa-repeat"></i>
                        {{ $t('اعرض خيارات أخرى', 'See other options') }}
                    </a>
                @endif
            </article>
        </aside>
    </section>
@endsection
