@extends('layouts.booking-site')

@section('content')
    @php
        $t = fn ($ar, $en) => app()->getLocale() === 'ar' ? $ar : $en;
        $featuredTitle = app()->getLocale() === 'ar' ? ($websiteSettings->featured_rooms_title_ar ?: $websiteSettings->featured_rooms_title_en) : ($websiteSettings->featured_rooms_title_en ?: $websiteSettings->featured_rooms_title_ar);
        $featuredIntro = app()->getLocale() === 'ar' ? ($websiteSettings->featured_rooms_intro_ar ?: $websiteSettings->featured_rooms_intro_en) : ($websiteSettings->featured_rooms_intro_en ?: $websiteSettings->featured_rooms_intro_ar);
        $facilitiesTitle = app()->getLocale() === 'ar' ? ($websiteSettings->facilities_section_title_ar ?: $websiteSettings->facilities_section_title_en) : ($websiteSettings->facilities_section_title_en ?: $websiteSettings->facilities_section_title_ar);
        $facilitiesIntro = app()->getLocale() === 'ar' ? ($websiteSettings->facilities_section_intro_ar ?: $websiteSettings->facilities_section_intro_en) : ($websiteSettings->facilities_section_intro_en ?: $websiteSettings->facilities_section_intro_ar);
    @endphp

    <style>
        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(28px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes floatGlow {
            0%,
            100% {
                transform: translate3d(0, 0, 0) scale(1);
            }

            50% {
                transform: translate3d(10px, -16px, 0) scale(1.08);
            }
        }

        @keyframes driftOrb {
            0%,
            100% {
                transform: translate3d(0, 0, 0);
            }

            50% {
                transform: translate3d(-18px, 14px, 0);
            }
        }

        @keyframes shimmerSweep {
            0% {
                transform: translateX(-120%) rotate(16deg);
            }

            100% {
                transform: translateX(300%) rotate(16deg);
            }
        }

        .hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(320px, 0.85fr);
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .hero-panel {
            position: relative;
            overflow: hidden;
            padding: clamp(1.5rem, 4vw, 3rem);
            min-height: 520px;
            color: white;
            background:
                linear-gradient(135deg, rgba(24, 49, 83, 0.92), rgba(179, 138, 61, 0.82)),
                url('{{ $branding['hero_image_url'] }}') center/cover no-repeat;
        }

        .hero-panel::before,
        .hero-panel::after {
            content: '';
            position: absolute;
            border-radius: 999px;
            pointer-events: none;
        }

        .hero-panel::before {
            width: 18rem;
            height: 18rem;
            inset-block-start: -5rem;
            inset-inline-end: -4rem;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.26) 0%, rgba(255, 255, 255, 0) 72%);
            animation: floatGlow 11s ease-in-out infinite;
        }

        .hero-panel::after {
            width: 12rem;
            height: 12rem;
            inset-block-end: 4rem;
            inset-inline-start: -3rem;
            background: radial-gradient(circle, rgba(255, 214, 126, 0.28) 0%, rgba(255, 214, 126, 0) 74%);
            animation: driftOrb 13s ease-in-out infinite;
        }

        .hero-panel__inner {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            height: 100%;
        }

        .hero-kicker,
        .hero-title {
            margin: 0;
            animation: fadeSlideUp 0.7s ease both;
        }

        .hero-kicker {
            color: rgba(255, 255, 255, 0.72);
            letter-spacing: 0.18em;
        }

        .hero-title {
            font-size: clamp(2.6rem, 6vw, 5.2rem);
            line-height: 0.92;
            max-width: 12ch;
            text-shadow: 0 10px 40px rgba(8, 15, 31, 0.28);
            animation-delay: 0.08s;
        }

        .hero-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.65rem;
            margin-top: 1rem;
            animation: fadeSlideUp 0.7s ease both;
            animation-delay: 0.16s;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.7rem 0.9rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.16);
            color: rgba(255, 255, 255, 0.94);
            font-size: 0.88rem;
            font-weight: 700;
            backdrop-filter: blur(12px);
        }

        .hero-copy {
            margin: 0;
            max-width: 42rem;
            color: rgba(255, 255, 255, 0.86);
            font-size: 1.05rem;
            line-height: 1.8;
            animation: fadeSlideUp 0.7s ease both;
            animation-delay: 0.24s;
        }

        .hero-search-shell {
            position: relative;
            animation: fadeSlideUp 0.72s ease both;
            animation-delay: 0.32s;
        }

        .hero-search-shell::before {
            content: '';
            position: absolute;
            inset: 1rem;
            border-radius: 28px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.16), rgba(255, 255, 255, 0.02));
            filter: blur(30px);
            pointer-events: none;
        }

        .hero-search-shell .booking-search {
            position: relative;
            z-index: 1;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(248, 245, 237, 0.97));
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.85rem;
            margin-top: auto;
            animation: fadeSlideUp 0.76s ease both;
            animation-delay: 0.4s;
        }

        .hero-stat {
            padding: 1rem;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            transition: transform 0.25s ease, background 0.25s ease, border-color 0.25s ease;
        }

        .hero-stat:hover {
            transform: translateY(-4px);
            background: rgba(255, 255, 255, 0.18);
            border-color: rgba(255, 255, 255, 0.22);
        }

        .hero-stat strong {
            display: block;
            font-size: 2rem;
        }

        .hero-stat small {
            display: block;
            color: rgba(255, 255, 255, 0.74);
        }

        .hero-side,
        .featured-room-grid,
        .facility-grid {
            display: grid;
            gap: 1rem;
        }

        .hero-side {
            perspective: 1200px;
        }

        .hero-side__card,
        .facility-chip,
        .cta-banner {
            padding: 1.35rem;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.74);
            border: 1px solid rgba(20, 33, 61, 0.08);
            box-shadow: var(--shadow-md);
        }

        .hero-side__card {
            position: relative;
            overflow: hidden;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(247, 243, 234, 0.84));
            transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
            animation: fadeSlideUp 0.7s ease both;
        }

        .hero-side__card::after {
            content: '';
            position: absolute;
            inset-inline: 1.2rem;
            inset-block-start: 0;
            height: 4px;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--brand-accent), rgba(24, 49, 83, 0.75));
        }

        .hero-side__card:nth-child(1) {
            animation-delay: 0.18s;
        }

        .hero-side__card:nth-child(2) {
            animation-delay: 0.28s;
        }

        .hero-side__card:nth-child(3) {
            animation-delay: 0.38s;
        }

        .hero-side__card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(20, 33, 61, 0.14);
        }

        .facility-grid {
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            align-items: stretch;
        }

        .facility-chip {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            min-width: 0;
            height: 100%;
            overflow-wrap: anywhere;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(247, 243, 234, 0.82));
            transition: transform 0.26s ease, box-shadow 0.26s ease, border-color 0.26s ease;
            animation: fadeSlideUp 0.7s ease both;
        }

        .facility-chip:nth-child(1) {
            animation-delay: 0.08s;
        }

        .facility-chip:nth-child(2) {
            animation-delay: 0.14s;
        }

        .facility-chip:nth-child(3) {
            animation-delay: 0.2s;
        }

        .facility-chip:nth-child(4) {
            animation-delay: 0.26s;
        }

        .facility-chip::before {
            content: '';
            width: 3.25rem;
            height: 0.28rem;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--brand-accent), var(--brand-primary));
        }

        .facility-chip:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(20, 33, 61, 0.14);
        }

        .facility-chip__heading {
            display: flex;
            align-items: flex-start;
            gap: 0.8rem;
        }

        .facility-chip__icon {
            width: 2.5rem;
            height: 2.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: rgba(24, 49, 83, 0.08);
            color: var(--brand-primary);
            flex-shrink: 0;
        }

        .facility-chip strong {
            display: block;
            margin-bottom: 0.4rem;
            font-size: 1rem;
        }

        .facility-chip .section-copy {
            overflow-wrap: anywhere;
        }

        .section-head,
        .cta-banner__inner {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .section-head {
            animation: fadeSlideUp 0.72s ease both;
        }

        .featured-room-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            align-items: stretch;
        }

        .featured-room-card {
            position: relative;
            display: grid;
            grid-template-rows: 190px minmax(0, 1fr);
            overflow: hidden;
            min-height: 100%;
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid rgba(20, 33, 61, 0.08);
            box-shadow: var(--shadow-md);
            transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
        }

        .featured-room-card:hover {
            transform: translateY(-4px);
            border-color: rgba(179, 138, 61, 0.24);
            box-shadow: 0 18px 42px rgba(20, 33, 61, 0.12);
        }

        .featured-room-card__media {
            position: relative;
            overflow: hidden;
            background: rgba(20, 33, 61, 0.06);
        }

        .featured-room-card__media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .featured-room-card:hover .featured-room-card__media img {
            transform: scale(1.04);
        }

        .featured-room-card__status {
            position: absolute;
            inset-block-start: 0.9rem;
            inset-inline-start: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.55rem 0.75rem;
            border-radius: 999px;
            color: white;
            background: rgba(15, 118, 110, 0.92);
            font-size: 0.78rem;
            font-weight: 800;
            box-shadow: 0 12px 26px rgba(20, 33, 61, 0.16);
        }

        .featured-room-card__status.is-soldout {
            background: rgba(180, 35, 24, 0.92);
        }

        .featured-room-card__body {
            display: grid;
            gap: 0.85rem;
            padding: 1.15rem;
        }

        .featured-room-card__title-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.9rem;
        }

        .featured-room-card__title {
            margin: 0;
            font-size: 1.45rem;
            line-height: 1.08;
        }

        .featured-room-card__unit {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 0.65rem;
            border-radius: 999px;
            background: rgba(20, 33, 61, 0.06);
            color: var(--muted);
            font-size: 0.78rem;
            font-weight: 800;
        }

        .featured-room-card__summary {
            margin: 0;
            color: var(--muted);
            line-height: 1.55;
        }

        .featured-room-card__facts {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.55rem;
        }

        .featured-room-card__facts span {
            display: grid;
            gap: 0.2rem;
            min-width: 0;
            padding: 0.65rem;
            border-radius: 14px;
            background: rgba(20, 33, 61, 0.04);
            color: var(--muted);
            font-size: 0.78rem;
            font-weight: 700;
        }

        .featured-room-card__facts i {
            color: var(--brand-accent);
        }

        .featured-room-card__footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.8rem;
            margin-top: auto;
        }

        .featured-room-card__price {
            display: grid;
            gap: 0.15rem;
            min-width: 0;
        }

        .featured-room-card__price small {
            color: var(--muted);
            font-size: 0.78rem;
            font-weight: 700;
        }

        .featured-room-card__price strong {
            font-size: 1.25rem;
            line-height: 1;
        }

        .featured-room-card__actions {
            display: inline-flex;
            gap: 0.5rem;
            flex: 0 0 auto;
        }

        .featured-room-card__actions .booking-cta {
            min-height: 42px;
            padding: 0.7rem 0.85rem;
            font-size: 0.88rem;
        }

        .featured-room-grid > * {
            animation: fadeSlideUp 0.76s ease both;
        }

        .featured-room-grid > *:nth-child(1) {
            animation-delay: 0.08s;
        }

        .featured-room-grid > *:nth-child(2) {
            animation-delay: 0.18s;
        }

        .featured-room-grid > *:nth-child(3) {
            animation-delay: 0.28s;
        }

        .cta-banner {
            position: relative;
            overflow: hidden;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(247, 243, 234, 0.78)),
                radial-gradient(circle at top right, rgba(179, 138, 61, 0.2), transparent 40%);
            animation: fadeSlideUp 0.8s ease both;
        }

        .cta-banner__inner {
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .cta-banner::after {
            content: '';
            position: absolute;
            inset-block: -20%;
            inset-inline-start: -30%;
            width: 28%;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.44), rgba(255, 255, 255, 0));
            transform: rotate(16deg);
            animation: shimmerSweep 7s linear infinite;
            pointer-events: none;
        }

        .home-section {
            position: relative;
        }

        .live-search-feedback {
            display: none;
            margin-top: 0.85rem;
            padding: 0.9rem 1rem;
            border-radius: 18px;
            background: rgba(180, 35, 24, 0.12);
            border: 1px solid rgba(180, 35, 24, 0.18);
            color: white;
            font-weight: 700;
        }

        .live-search-feedback.is-visible {
            display: block;
            animation: fadeSlideUp 0.35s ease both;
        }

        .booking-search.is-loading {
            pointer-events: none;
            opacity: 0.72;
        }

        .live-results-shell {
            display: grid;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .live-results-shell[hidden] {
            display: none !important;
        }

        .live-results-head {
            padding: clamp(1.25rem, 3vw, 1.8rem);
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.94), rgba(247, 243, 234, 0.84)),
                radial-gradient(circle at top right, rgba(179, 138, 61, 0.16), transparent 42%);
        }

        .live-results-head__top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .live-results-kicker {
            letter-spacing: 0.16em;
            text-transform: uppercase;
            font-size: 0.78rem;
            font-weight: 800;
            color: var(--muted);
        }

        .live-results-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .live-results-meta span {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 0.95rem;
            border-radius: 999px;
            background: rgba(20, 33, 61, 0.06);
            color: var(--muted);
            font-weight: 700;
        }

        .live-results-stack {
            display: grid;
            gap: 1rem;
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation: none !important;
                transition: none !important;
                scroll-behavior: auto !important;
            }
        }

        @media (max-width: 1199px) {
            .hero-grid {
                grid-template-columns: 1fr;
            }

            .hero-panel {
                min-height: 460px;
            }

            .hero-side {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .featured-room-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767px) {
            .hero-panel {
                min-height: auto;
                padding: 1.3rem;
            }

            .hero-panel__inner {
                gap: 1.25rem;
            }

            .hero-copy {
                font-size: 0.96rem;
                line-height: 1.75;
            }

            .hero-badges {
                gap: 0.55rem;
            }

            .hero-badge {
                width: 100%;
                justify-content: center;
            }

            .hero-side,
            .hero-stats,
            .featured-room-grid,
            .facility-grid {
                grid-template-columns: 1fr;
            }

            .featured-room-card {
                grid-template-rows: 220px minmax(0, 1fr);
            }

            .featured-room-card__footer,
            .featured-room-card__actions {
                align-items: stretch;
                flex-direction: column;
            }

            .featured-room-card__actions .booking-cta {
                width: 100%;
            }

            .hero-side__card,
            .facility-chip,
            .cta-banner {
                padding: 1.1rem;
                border-radius: 20px;
            }

            .live-results-head {
                padding: 1.1rem;
            }

            .facility-chip__icon {
                width: 2.2rem;
                height: 2.2rem;
                border-radius: 14px;
            }

            .section-head,
            .cta-banner__inner,
            .live-results-head__top {
                align-items: stretch;
            }

            .section-head .booking-cta,
            .cta-banner__inner .booking-cta,
            .live-results-head__top .booking-cta {
                width: 100%;
            }

            .live-results-meta span {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 575px) {
            .hero-title {
                font-size: clamp(2.1rem, 11vw, 3rem);
                max-width: none;
            }

            .hero-stat strong {
                font-size: 1.6rem;
            }
        }
    </style>

    <section class="hero-grid">
        <article class="page-card hero-panel">
            <div class="hero-panel__inner">
                <div>
                    <p class="text-uppercase fw-bold mb-2 hero-kicker">
                        {{ app()->getLocale() === 'ar' ? ($websiteSettings->home_hero_kicker_ar ?: $websiteSettings->home_hero_kicker_en) : ($websiteSettings->home_hero_kicker_en ?: $websiteSettings->home_hero_kicker_ar) }}
                    </p>
                    <h1 class="hero-title">
                        {{ app()->getLocale() === 'ar' ? ($websiteSettings->home_hero_title_ar ?: $websiteSettings->home_hero_title_en) : ($websiteSettings->home_hero_title_en ?: $websiteSettings->home_hero_title_ar) }}
                    </h1>
                    <div class="hero-badges">
                        <span class="hero-badge">
                            <i class="fas fa-bolt"></i>
                            {{ $t('أسعار مباشرة', 'Direct rates') }}
                        </span>
                        <span class="hero-badge">
                            <i class="fas fa-mobile-screen-button"></i>
                            {{ $t('حجز أسرع بالجوال', 'Faster mobile booking') }}
                        </span>
                        <span class="hero-badge">
                            <i class="fas fa-shield-halved"></i>
                            {{ $t('رحلة حجز أوضح', 'Clearer booking journey') }}
                        </span>
                    </div>
                </div>

                <p class="hero-copy">
                    {{ app()->getLocale() === 'ar' ? ($websiteSettings->home_hero_text_ar ?: $websiteSettings->home_hero_text_en) : ($websiteSettings->home_hero_text_en ?: $websiteSettings->home_hero_text_ar) }}
                </p>

                <div class="hero-search-shell">
                    @include('booking_site.partials.search-form', ['search' => $search, 'ajaxMode' => true])
                </div>
                <div class="live-search-feedback" id="booking-live-search-feedback" role="status" aria-live="polite"></div>

                <div class="hero-stats">
                    <div class="hero-stat">
                        <small>{{ $t('قوالب العرض المنشورة', 'Published layouts') }}</small>
                        <strong>{{ $roomCount }}</strong>
                    </div>
                    <div class="hero-stat">
                        <small>{{ $t('الوحدات النشطة', 'Active units') }}</small>
                        <strong>{{ $unitCount }}</strong>
                    </div>
                    <div class="hero-stat">
                        <small>{{ $t('المرافق المنشورة', 'Published facilities') }}</small>
                        <strong>{{ $facilityCount }}</strong>
                    </div>
                </div>
            </div>
        </article>

        <aside class="hero-side">
            <div class="hero-side__card">
                <strong class="d-block mb-2">{{ $t('موقع مباشر أكثر وضوحاً', 'A clearer direct website') }}</strong>
                <p class="section-copy">{{ $t('يعرض الموقع الوحدات الفعلية المتاحة للضيف بشكل أوضح بينما تبقى الأسعار والسياسات داخل نظام الإدارة الحالي.', 'Guests browse the actual available units more clearly while pricing and policies remain controlled by the live reservation system.') }}</p>
            </div>
            <div class="hero-side__card">
                <strong class="d-block mb-2">{{ $t('جاهز للجوال', 'Mobile-first experience') }}</strong>
                <p class="section-copy">{{ $t('حقول أقل وخطوات أقصر وبطاقات وحدات أوضح حتى يصل المستخدم إلى الحجز بسرعة أكبر.', 'A shorter journey with fewer fields and clearer unit cards helps guests complete the booking faster.') }}</p>
            </div>
            <div class="hero-side__card">
                <strong class="d-block mb-2">{{ $t('صفحات قابلة للأرشفة', 'Search-friendly pages') }}</strong>
                <p class="section-copy">{{ $t('صفحات الوحدات والمحتوى الثابت قابلة للفهرسة مع وصف وبيانات منظمة، بينما البحث والدفع يبقيان خارج الأرشفة.', 'Unit pages and static content stay indexable with structured metadata while search and checkout remain out of the index.') }}</p>
            </div>
        </aside>
    </section>

    <section
        class="live-results-shell home-section"
        id="booking-live-results"
        @if (!$liveSearchResults)
            hidden
        @endif
    >
        @if ($liveSearchResults)
            @include('booking_site.partials.live-search-results', $liveSearchResults)
        @endif
    </section>

    <section class="mb-4 home-section">
        <div class="section-head mb-3">
            <div>
                <h2 class="section-title">{{ $featuredTitle }}</h2>
                <p class="section-copy">{{ $featuredIntro }}</p>
            </div>
            <a href="{{ route('booking.rooms.index', $bookingPropertyQuery ?? []) }}" class="booking-cta booking-cta--soft">
                <i class="fas fa-grid-2"></i>
                {{ $t('عرض جميع الوحدات', 'Browse all units') }}
            </a>
        </div>

        <div class="featured-room-grid">
            @forelse ($featuredRooms as $room)
                @php
                    $roomTitle = trim(preg_replace('/\s+-\s+Unit\s+'.preg_quote((string) $room['unit_number'], '/').'$/i', '', $room['name']));
                    $featuredCheckoutQuery = array_merge([
                        'unit_id' => $room['id'],
                        'check_in' => $search['check_in'],
                        'check_out' => $search['check_out'],
                        'adults' => $search['adults'],
                        'children' => $search['children'],
                    ], $bookingPropertyQuery ?? []);
                    $featuredDetailsQuery = array_merge([
                        'roomType' => $room['slug'],
                        'check_in' => $search['check_in'],
                        'check_out' => $search['check_out'],
                        'adults' => $search['adults'],
                        'children' => $search['children'],
                    ], $bookingPropertyQuery ?? []);
                @endphp
                <article class="page-card featured-room-card">
                    <div class="featured-room-card__media">
                        <img src="{{ $room['image'] }}" alt="{{ $room['name'] }}">
                        <div class="featured-room-card__status {{ $room['available'] ? '' : 'is-soldout' }}">
                            <i class="fas {{ $room['available'] ? 'fa-check-circle' : 'fa-circle-exclamation' }}"></i>
                            {{ $room['available'] ? $t('متاح', 'Available') : $t('غير متاح', 'Unavailable') }}
                        </div>
                    </div>
                    <div class="featured-room-card__body">
                        <div class="featured-room-card__title-row">
                            <h3 class="featured-room-card__title">{{ $roomTitle ?: $room['name'] }}</h3>
                            <span class="featured-room-card__unit">
                                <i class="fas fa-door-open"></i>
                                {{ $room['unit_number'] }}
                            </span>
                        </div>
                        <p class="featured-room-card__summary">{{ $room['summary'] }}</p>
                        <div class="featured-room-card__facts">
                            @if ($room['base_occupancy'])
                                <span><i class="fas fa-user-group"></i>{{ $room['base_occupancy'] }} {{ $t('ضيوف', 'guests') }}</span>
                            @endif
                            @if ($room['area'])
                                <span><i class="fas fa-ruler-combined"></i>{{ number_format((float) $room['area'], 0) }} sqm</span>
                            @endif
                            <span><i class="fas fa-bed"></i>{{ $room['bed_summary'] }}</span>
                        </div>
                        <div class="featured-room-card__footer">
                            <div class="featured-room-card__price">
                                @if ($room['display_rate'])
                                    <small>{{ $t('ابتداء من', 'From') }}</small>
                                    <strong>SAR {{ number_format((float) $room['display_rate'], 2) }}</strong>
                                @else
                                    <small>{{ $t('السعر حسب التوفر', 'Rate on availability') }}</small>
                                @endif
                            </div>
                            <div class="featured-room-card__actions">
                                <a href="{{ route('booking.rooms.show', $featuredDetailsQuery) }}" class="booking-cta booking-cta--soft">
                                    <i class="fas fa-eye"></i>
                                    {{ $t('التفاصيل', 'Details') }}
                                </a>
                                @if ($room['available'])
                                    <a href="{{ route('booking.checkout', $featuredCheckoutQuery) }}" class="booking-cta booking-cta--primary">
                                        <i class="fas fa-arrow-right"></i>
                                        {{ $t('احجز', 'Reserve') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="page-card p-4">
                    <p class="section-copy mb-0">{{ $t('لا توجد وحدات نشطة متاحة حتى الآن.', 'No active units are available yet.') }}</p>
                </div>
            @endforelse
        </div>
    </section>

    <section class="mb-4 home-section">
        <div class="mb-3">
            <h2 class="section-title">{{ $facilitiesTitle }}</h2>
            <p class="section-copy">{{ $facilitiesIntro }}</p>
        </div>

        <div class="facility-grid">
            @forelse ($facilities as $facility)
                <div class="facility-chip">
                    <div class="facility-chip__heading">
                        <span class="facility-chip__icon">
                            <i class="fas fa-star"></i>
                        </span>
                        <strong>{{ $facility->facility->name }}</strong>
                    </div>
                    <span class="section-copy">{{ $facility->description ?: $t('مرفق منشور من إعدادات العقار الحالية.', 'Published from the current property setup.') }}</span>
                </div>
            @empty
                <div class="facility-chip">
                    <strong>{{ $t('المرافق', 'Facilities') }}</strong>
                    <span class="section-copy">{{ $t('أضف مرافق العقار من لوحة الإعدادات لإظهارها هنا.', 'Add property facilities in setup to show them here.') }}</span>
                </div>
            @endforelse
        </div>
    </section>

    <section class="cta-banner home-section">
        <div class="cta-banner__inner">
            <div>
                <h2 class="h2 fw-bold mb-2">{{ $t('جاهز لبدء الحجز؟', 'Ready to start a booking?') }}</h2>
                <p class="section-copy mb-0">{{ $t('ابدأ بالبحث عن التواريخ المناسبة ثم قارن الوحدات والأسعار المباشرة قبل إكمال الطلب.', 'Start with your stay dates, compare units and direct rates, and continue with a cleaner booking journey.') }}</p>
            </div>
            <a href="{{ route('booking.search', array_merge($search, $bookingPropertyQuery ?? [])) }}" class="booking-cta booking-cta--primary">
                <i class="fas fa-calendar-check"></i>
                {{ $t('ابدأ الآن', 'Start now') }}
            </a>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (() => {
            const form = document.querySelector('form[data-booking-search-ajax="true"]');
            const resultsSection = document.getElementById('booking-live-results');
            const feedback = document.getElementById('booking-live-search-feedback');

            if (!form || !resultsSection || !feedback) {
                return;
            }

            let activeRequest = null;
            const submitButton = form.querySelector('button[type="submit"]');
            const submitLabel = submitButton ? submitButton.innerHTML : '';

            const showFeedback = (message) => {
                feedback.textContent = message;
                feedback.classList.add('is-visible');
            };

            const clearFeedback = () => {
                feedback.textContent = '';
                feedback.classList.remove('is-visible');
            };

            const setLoading = (isLoading) => {
                form.classList.toggle('is-loading', isLoading);

                if (!submitButton) {
                    return;
                }

                submitButton.disabled = isLoading;
                submitButton.innerHTML = isLoading
                    ? '<i class="fas fa-spinner fa-spin"></i> {{ $t('جاري التحقق...', 'Checking...') }}'
                    : submitLabel;
            };

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                clearFeedback();

                if (activeRequest) {
                    activeRequest.abort();
                }

                activeRequest = new AbortController();
                const params = new URLSearchParams(new FormData(form));

                setLoading(true);

                try {
                    const response = await fetch(`${form.action}?${params.toString()}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        signal: activeRequest.signal,
                    });

                    const payload = await response.json();

                    if (!response.ok) {
                        const message = payload.message || Object.values(payload.errors || {}).flat()[0];
                        throw new Error(message || '{{ $t('تعذر تحديث النتائج الآن.', 'Unable to refresh the results right now.') }}');
                    }

                    resultsSection.innerHTML = payload.html;
                    resultsSection.hidden = false;

                    if (payload.historyUrl) {
                        window.history.replaceState({}, '', payload.historyUrl);
                    }

                    resultsSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start',
                    });
                } catch (error) {
                    if (error.name === 'AbortError') {
                        return;
                    }

                    showFeedback(error.message || '{{ $t('حدث خطأ غير متوقع أثناء البحث.', 'An unexpected error happened while searching.') }}');
                } finally {
                    setLoading(false);
                }
            });
        })();
    </script>
@endpush
