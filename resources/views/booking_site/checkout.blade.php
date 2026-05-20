@extends('layouts.booking-site')

@section('content')
    @php
        $t = fn ($ar, $en) => app()->getLocale() === 'ar' ? $ar : $en;
        $quote = $room['quote'];
        $bookingPropertyQuery = $bookingPropertyQuery ?? [];
    @endphp

    <style>
        .checkout-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr);
            gap: 1.2rem;
        }

        .checkout-card {
            padding: 1.5rem;
        }

        .checkout-form__grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .checkout-form label {
            display: block;
            margin-bottom: 0.45rem;
            color: var(--muted);
            font-size: 0.88rem;
            font-weight: 700;
        }

        .checkout-form input:not([type="checkbox"]):not([type="radio"]),
        .checkout-form select,
        .checkout-form textarea {
            width: 100%;
            min-height: 52px;
            padding: 0.9rem 1rem;
            border-radius: 16px;
            border: 1px solid rgba(20, 33, 61, 0.12);
            background: white;
        }

        .checkout-form textarea {
            min-height: 140px;
            resize: vertical;
        }

        .checkout-summary {
            position: sticky;
            top: 6.5rem;
        }

        .checkout-line {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(20, 33, 61, 0.08);
        }

        .checkout-line:last-child {
            border-bottom: 0;
        }

        @media (max-width: 1199px) {
            .checkout-grid,
            .checkout-form__grid {
                grid-template-columns: 1fr;
            }

            .checkout-summary {
                position: static;
            }
        }

        @media (max-width: 767px) {
            .checkout-card {
                padding: 1.15rem;
            }

            .checkout-line {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.35rem;
            }

            .checkout-form button,
            .checkout-summary .booking-cta {
                width: 100%;
            }
        }
    </style>

    <section class="checkout-grid">
        <article class="page-card checkout-card">
            <div class="mb-4">
                <p class="text-uppercase fw-bold mb-2" style="letter-spacing:0.14em; color: var(--muted);">{{ $t('الخطوة الأخيرة', 'Final step') }}</p>
                <h1 class="section-title">{{ $t('بيانات النزيل والدفع', 'Guest details and checkout') }}</h1>
                <p class="section-copy">{{ $t('سيتم حفظ هذا الطلب داخل نظام إدارة الحجوزات الحالي كحجز مباشر بحالة معلقة مع الحفاظ على السعر والتوفر الفعلي.', 'This form creates a direct booking inside the live reservation system while preserving real availability and pricing.') }}</p>
            </div>

            <form method="POST" action="{{ route('booking.store', $bookingPropertyQuery) }}" class="checkout-form">
                @csrf
                @foreach ($bookingPropertyQuery as $name => $value)
                    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                @endforeach
                <input type="hidden" name="unit_id" value="{{ $room['id'] }}">
                <input type="hidden" name="check_in" value="{{ $search['check_in'] }}">
                <input type="hidden" name="check_out" value="{{ $search['check_out'] }}">
                <input type="hidden" name="adults" value="{{ $search['adults'] }}">
                <input type="hidden" name="children" value="{{ $search['children'] }}">

                <div class="checkout-form__grid">
                    <div style="grid-column: 1 / -1;">
                        <label for="full_name">{{ $t('الاسم الكامل', 'Full name') }}</label>
                        <input id="full_name" type="text" name="full_name" value="{{ old('full_name') }}" placeholder="{{ $t('اسم النزيل', 'Guest full name') }}" required>
                    </div>
                    <div>
                        <label for="phone">{{ $t('رقم الجوال', 'Mobile number') }}</label>
                        <input id="phone" type="text" name="phone" value="{{ old('phone') }}" placeholder="+966..." required>
                    </div>
                    <div>
                        <label for="email">{{ $t('البريد الإلكتروني', 'Email address') }}</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="name@example.com">
                    </div>
                    <div>
                        <label for="payment_method_id">{{ $t('طريقة الدفع المفضلة', 'Preferred payment method') }}</label>
                        <select id="payment_method_id" name="payment_method_id">
                            <option value="">{{ $t('اختر إذا لزم الأمر', 'Select if applicable') }}</option>
                            @foreach ($paymentMethods as $method)
                                <option value="{{ $method->id }}" @selected((int) old('payment_method_id') === (int) $method->id)>{{ $method->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>{{ $t('نوع الحجز', 'Booking type') }}</label>
                        <input type="text" value="{{ $t('حجز مباشر عبر الموقع', 'Direct booking via website') }}" disabled>
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <label for="notes">{{ $t('طلبات أو ملاحظات', 'Special requests or notes') }}</label>
                        <textarea id="notes" name="notes" placeholder="{{ $t('مثال: وصول متأخر، غرفة هادئة، أو أي ملاحظات مهمة للفريق.', 'Example: late arrival, quiet room, or anything important for the team.') }}">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="form-check mt-4 mb-4">
                    <input class="form-check-input" type="checkbox" value="1" id="agree_policies" name="agree_policies" required>
                    <label class="form-check-label ms-2" for="agree_policies">
                        {{ $t('أوافق على السياسات المعروضة وأفهم أن الحجز سيحفظ بحالة معلقة حتى تتم مراجعته أو تأكيده.', 'I agree to the displayed policies and understand the booking will be saved as pending until reviewed or confirmed.') }}
                    </label>
                </div>

                <button type="submit" class="booking-cta booking-cta--primary">
                    <i class="fas fa-lock"></i>
                    {{ $t('تأكيد الحجز المباشر', 'Confirm direct booking') }}
                </button>
            </form>
        </article>

        <aside class="checkout-summary d-grid gap-3">
            <article class="page-card checkout-card">
                <h2 class="h3 fw-bold mb-3">{{ $room['name'] }}</h2>
                <div class="checkout-line">
                    <span>{{ $t('رقم الوحدة', 'Unit number') }}</span>
                    <strong>{{ $room['unit_number'] }}</strong>
                </div>
                <div class="checkout-line">
                    <span>{{ $t('الوصول', 'Check-in') }}</span>
                    <strong>{{ $search['check_in'] }}</strong>
                </div>
                <div class="checkout-line">
                    <span>{{ $t('المغادرة', 'Check-out') }}</span>
                    <strong>{{ $search['check_out'] }}</strong>
                </div>
                <div class="checkout-line">
                    <span>{{ $t('الضيوف', 'Guests') }}</span>
                    <strong>{{ $search['adults'] + $search['children'] }}</strong>
                </div>
                <div class="checkout-line">
                    <span>{{ $t('متوسط سعر الليلة', 'Average nightly rate') }}</span>
                    <strong>SAR {{ number_format((float) $quote['daily_rate'], 2) }}</strong>
                </div>
                <div class="checkout-line">
                    <span>{{ $t('الإقامة', 'Stay subtotal') }}</span>
                    <strong>SAR {{ number_format((float) $quote['subtotal'], 2) }}</strong>
                </div>
                @foreach ($quote['tax_breakdown'] as $tax)
                    <div class="checkout-line">
                        <span>{{ $tax['name'] }}</span>
                        <strong>SAR {{ number_format((float) $tax['amount'], 2) }}</strong>
                    </div>
                @endforeach
                <div class="checkout-line" style="font-size: 1.08rem;">
                    <span>{{ $t('الإجمالي النهائي', 'Grand total') }}</span>
                    <strong>SAR {{ number_format((float) $quote['grand_total'], 2) }}</strong>
                </div>
            </article>

            <article class="page-card checkout-card">
                <h3 class="h5 fw-bold mb-3">{{ $t('قبل الإرسال', 'Before you submit') }}</h3>
                <div class="section-copy d-grid gap-2">
                    <div>{{ $t('سيتم إنشاء مرجع للحجز فور حفظ الطلب داخل النظام.', 'A reservation reference will be created as soon as the request is saved.') }}</div>
                    <div>{{ $t('يبقى السعر متوافقاً مع قواعد التسعير والضرائب الحالية.', 'Pricing remains aligned with the current rate and tax setup.') }}</div>
                    <div>{{ $t('يتم التحقق من التداخلات قبل الحفظ لحماية التوفر الفعلي.', 'Overlapping reservations are checked before saving to protect live availability.') }}</div>
                </div>
            </article>
        </aside>
    </section>
@endsection
