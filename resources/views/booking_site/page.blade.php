@extends('layouts.booking-site')

@section('content')
    @php
        $t = fn ($ar, $en) => app()->getLocale() === 'ar' ? $ar : $en;
        $pageTitleText = app()->getLocale() === 'ar' ? ($page->title_ar ?: $page->title_en) : ($page->title_en ?: $page->title_ar);
        $heroTitle = app()->getLocale() === 'ar' ? ($page->hero_title_ar ?: $page->hero_title_en ?: $pageTitleText) : ($page->hero_title_en ?: $page->hero_title_ar ?: $pageTitleText);
        $heroIntro = app()->getLocale() === 'ar' ? ($page->hero_intro_ar ?: $page->hero_intro_en) : ($page->hero_intro_en ?: $page->hero_intro_ar);
        $body = app()->getLocale() === 'ar' ? ($page->body_ar ?: $page->body_en) : ($page->body_en ?: $page->body_ar);
        $pageSearchQuery = array_merge($search, $bookingPropertyQuery ?? []);
    @endphp

    <style>
        .content-hero {
            padding: clamp(1.5rem, 4vw, 2.8rem);
            margin-bottom: 1.2rem;
        }

        .content-body {
            padding: clamp(1.5rem, 4vw, 2.5rem);
        }

        .content-rich {
            display: grid;
            gap: 1rem;
        }

        .content-rich p {
            margin: 0;
            color: var(--muted);
            line-height: 1.9;
        }

        .support-grid,
        .faq-grid {
            display: grid;
            gap: 1rem;
        }

        .support-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .support-card,
        .faq-card {
            padding: 1.25rem;
            border-radius: 20px;
            background: rgba(20, 33, 61, 0.04);
            border: 1px solid rgba(20, 33, 61, 0.08);
        }

        .faq-card strong,
        .support-card strong {
            display: block;
            margin-bottom: 0.5rem;
        }

        @media (max-width: 991px) {
            .support-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .content-hero,
            .content-body {
                padding: 1.2rem;
            }

            .content-body .booking-cta {
                width: 100%;
            }
        }
    </style>

    <section class="page-card content-hero">
        <p class="text-uppercase fw-bold mb-2" style="letter-spacing:0.16em; color: var(--muted);">{{ $pageTitleText }}</p>
        <h1 class="section-title">{{ $heroTitle }}</h1>
        @if ($heroIntro)
            <p class="section-copy mt-3">{{ $heroIntro }}</p>
        @endif
    </section>

    <section class="page-card content-body">
        @if ($body)
            <div class="content-rich mb-4">
                @foreach (preg_split("/\r\n|\n|\r/", $body) as $line)
                    @if (trim($line) !== '')
                        <p>{{ $line }}</p>
                    @endif
                @endforeach
            </div>
        @endif

        @if ($pageKey === 'faq')
            <div class="faq-grid">
                @forelse ($faqItems as $item)
                    <div class="faq-card">
                        <strong>{{ app()->getLocale() === 'ar' ? ($item->question_ar ?: $item->question_en) : ($item->question_en ?: $item->question_ar) }}</strong>
                        <div class="section-copy">{{ app()->getLocale() === 'ar' ? ($item->answer_ar ?: $item->answer_en) : ($item->answer_en ?: $item->answer_ar) }}</div>
                    </div>
                @empty
                    <div class="faq-card">
                        <strong>{{ $t('لا توجد أسئلة شائعة بعد', 'No FAQ items yet') }}</strong>
                        <div class="section-copy">{{ $t('يمكن لفريقك إضافة الأسئلة من لوحة التحكم.', 'Your team can add public questions from the dashboard.') }}</div>
                    </div>
                @endforelse
            </div>
        @endif

        @if ($pageKey === 'contact')
            <div class="support-grid">
                <div class="support-card">
                    <strong>{{ $t('الهاتف', 'Phone') }}</strong>
                    <div class="section-copy">{{ $branding['phone'] ?: '-' }}</div>
                </div>
                <div class="support-card">
                    <strong>{{ $t('البريد الإلكتروني', 'Email') }}</strong>
                    <div class="section-copy">{{ $branding['email'] ?: '-' }}</div>
                </div>
                <div class="support-card">
                    <strong>{{ $t('العنوان', 'Address') }}</strong>
                    <div class="section-copy">{{ $branding['address'] }}</div>
                </div>
                <div class="support-card">
                    <strong>{{ $t('الحجز المباشر', 'Direct booking') }}</strong>
                    <div class="section-copy">{{ $branding['tagline'] }}</div>
                </div>
            </div>
        @endif

        <div class="d-flex flex-wrap gap-3 mt-4">
            <a href="{{ route('booking.search', $pageSearchQuery) }}" class="booking-cta booking-cta--primary">
                <i class="fas fa-calendar-check"></i>
                {{ $t('تحقق من التوفر', 'Check availability') }}
            </a>
            <a href="{{ route('booking.rooms.index', $pageSearchQuery) }}" class="booking-cta booking-cta--soft">
                <i class="fas fa-bed"></i>
                {{ $t('استعرض الوحدات', 'Browse units') }}
            </a>
        </div>
    </section>
@endsection
