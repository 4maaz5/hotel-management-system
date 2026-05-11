@extends('layouts.booking-site')

@section('content')
    @php
        $t = fn ($ar, $en) => app()->getLocale() === 'ar' ? $ar : $en;
    @endphp

    <style>
        .confirmation-card {
            padding: clamp(1.5rem, 4vw, 2.5rem);
            max-width: 860px;
            margin: 0 auto;
        }

        .confirmation-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.8rem 1rem;
            border-radius: 999px;
            color: white;
            font-weight: 800;
            background: linear-gradient(135deg, var(--success), var(--brand-primary));
        }

        .confirmation-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .confirmation-cell {
            padding: 1.15rem;
            border-radius: 18px;
            background: rgba(20, 33, 61, 0.05);
        }

        .confirmation-cell small {
            display: block;
            color: var(--muted);
            font-weight: 700;
            margin-bottom: 0.3rem;
        }

        @media (max-width: 767px) {
            .confirmation-card {
                padding: 1.2rem;
            }

            .confirmation-grid {
                grid-template-columns: 1fr;
            }

            .confirmation-card .booking-cta {
                width: 100%;
            }
        }
    </style>

    <section class="page-card confirmation-card">
        <div class="confirmation-badge">
            <i class="fas fa-circle-check"></i>
            {{ $t('تم إنشاء الحجز', 'Reservation created') }}
        </div>

        <div class="mt-4">
            <h1 class="section-title">{{ $t('تم حفظ طلب الحجز المباشر بنجاح', 'Your direct booking request has been saved') }}</h1>
            <p class="section-copy">{{ $t('تم تسجيل الحجز داخل النظام بالحالة المناسبة ويمكن الآن استخدام الرقم المرجعي للمتابعة أو التواصل مع الضيف.', 'The reservation has been written into the live system and the reference below can now be used for follow-up or guest communication.') }}</p>
        </div>

        <div class="confirmation-grid">
            <div class="confirmation-cell">
                <small>{{ $t('مرجع الحجز', 'Booking reference') }}</small>
                <strong>{{ $reservation->reservation_number }}</strong>
            </div>
            <div class="confirmation-cell">
                <small>{{ $t('الحالة', 'Status') }}</small>
                <strong>{{ strtoupper($reservation->status) }}</strong>
            </div>
            <div class="confirmation-cell">
                <small>{{ $t('النزيل', 'Guest') }}</small>
                <strong>{{ $reservation->guest?->full_name ?? $t('نزيل مباشر', 'Direct guest') }}</strong>
            </div>
            <div class="confirmation-cell">
                <small>{{ $t('الوحدة', 'Assigned unit') }}</small>
                <strong>{{ $reservation->unit?->unit_number ?? '-' }}</strong>
            </div>
            <div class="confirmation-cell">
                <small>{{ $t('الوصول', 'Check-in') }}</small>
                <strong>{{ optional($reservation->check_in_date)->format('Y-m-d') }}</strong>
            </div>
            <div class="confirmation-cell">
                <small>{{ $t('المغادرة', 'Check-out') }}</small>
                <strong>{{ optional($reservation->check_out_date)->format('Y-m-d') }}</strong>
            </div>
            <div class="confirmation-cell">
                <small>{{ $t('الإجمالي', 'Grand total') }}</small>
                <strong>SAR {{ number_format((float) $reservation->grand_total, 2) }}</strong>
            </div>
            <div class="confirmation-cell">
                <small>{{ $t('الرصيد', 'Balance') }}</small>
                <strong>SAR {{ number_format((float) $reservation->balance, 2) }}</strong>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-3 mt-4">
            <a href="{{ route('booking.home', $bookingPropertyQuery ?? []) }}" class="booking-cta booking-cta--primary">
                <i class="fas fa-house"></i>
                {{ $t('العودة للرئيسية', 'Return home') }}
            </a>
            <a href="{{ route('booking.rooms.index', $bookingPropertyQuery ?? []) }}" class="booking-cta booking-cta--soft">
                <i class="fas fa-bed"></i>
                {{ $t('استعراض الوحدات', 'Browse units') }}
            </a>
        </div>
    </section>
@endsection
