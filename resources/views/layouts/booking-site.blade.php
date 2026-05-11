<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="{{ $robots ?? 'index,follow' }}">
    <meta name="description" content="{{ $metaDescription ?? 'Direct hotel booking website' }}">
    @if (!empty($metaKeywords))
        <meta name="keywords" content="{{ $metaKeywords }}">
    @endif
    <meta property="og:title" content="{{ $pageTitle ?? ($branding['name'] ?? 'Booking') }}">
    <meta property="og:description" content="{{ $metaDescription ?? 'Direct hotel booking website' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonicalUrl ?? url()->current() }}">
    <meta property="og:image" content="{{ $branding['hero_image_url'] ?? asset('logo.webp') }}">
    <link rel="canonical" href="{{ $canonicalUrl ?? url()->current() }}">
    <title>{{ $pageTitle ?? ($branding['name'] ?? 'Booking') }}</title>
    <link rel="icon" href="{{ $branding['logo_url'] ?? asset('logo.webp') }}" type="image/x-icon">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-primary: {{ $theme->button_primary_color ?? '#183153' }};
            --brand-accent: {{ $theme->topbar_bg_color ?? '#b38a3d' }};
            --ink: {{ $theme->text_color ?? '#14213d' }};
            --muted: #6d7487;
            --line: rgba(20, 33, 61, 0.1);
            --success: #0f766e;
            --danger: #b42318;
            --shadow-lg: 0 24px 60px rgba(20, 33, 61, 0.12);
            --shadow-md: 0 12px 28px rgba(20, 33, 61, 0.08);
            --radius-xl: 28px;
            --radius-lg: 20px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--ink);
            overflow-x: hidden;
            background:
                radial-gradient(circle at top right, rgba(179, 138, 61, 0.18), transparent 28rem),
                radial-gradient(circle at top left, rgba(24, 49, 83, 0.14), transparent 24rem),
                linear-gradient(180deg, #fffdf8 0%, #f8f4ec 45%, #f3efe7 100%);
            font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Manrope', sans-serif" }};
        }

        h1,
        h2,
        h3,
        h4 {
            font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Cormorant Garamond', serif" }};
            letter-spacing: 0.02em;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .booking-shell {
            width: min(1180px, calc(100% - 2rem));
            margin: 0 auto;
        }

        .booking-header {
            position: sticky;
            top: 0;
            z-index: 30;
            backdrop-filter: blur(14px);
            background: rgba(255, 253, 248, 0.88);
            border-bottom: 1px solid rgba(20, 33, 61, 0.08);
        }

        .booking-nav {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            align-items: center;
            gap: 1rem;
            padding: 0.85rem 0;
        }

        .booking-brand {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            min-width: 0;
        }

        .booking-brand img {
            width: 54px;
            height: 54px;
            object-fit: cover;
            border-radius: 18px;
            box-shadow: var(--shadow-md);
            background: white;
        }

        .booking-brand__name {
            font-size: 1.05rem;
            font-weight: 800;
            margin: 0;
        }

        .booking-brand__meta {
            color: var(--muted);
            font-size: 0.9rem;
            margin: 0.2rem 0 0;
        }

        .booking-nav__toggle {
            display: none;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            min-width: 48px;
            min-height: 48px;
            padding: 0.8rem 1rem;
            border: 1px solid rgba(20, 33, 61, 0.12);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.9);
            color: var(--brand-primary);
            font-weight: 800;
            box-shadow: var(--shadow-md);
        }

        .booking-nav__toggle i {
            font-size: 1rem;
        }

        .booking-nav__group {
            display: flex;
            flex-direction: column;
            min-width: 0;
            align-items: flex-end;
            gap: 0.7rem;
        }

        .booking-nav__topbar,
        .booking-nav__links,
        .booking-nav__actions,
        .booking-nav__tools {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .booking-nav__topbar,
        .booking-nav__tools {
            width: 100%;
        }

        .booking-nav__topbar {
            flex-wrap: nowrap;
            justify-content: space-between;
        }

        .booking-nav__links {
            min-width: 0;
            flex: 1 1 auto;
            width: auto;
        }

        .booking-nav__actions {
            flex: 0 0 auto;
            flex-wrap: nowrap;
        }

        .booking-link {
            padding: 0.6rem 0.95rem;
            border-radius: 999px;
            color: var(--muted);
            font-weight: 700;
        }

        .booking-link:hover,
        .booking-link.is-active {
            color: var(--ink);
            background: rgba(24, 49, 83, 0.06);
        }

        .booking-cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            padding: 0.95rem 1.25rem;
            border: 0;
            border-radius: 999px;
            font-weight: 800;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .booking-cta:hover {
            transform: translateY(-1px);
        }

        .booking-cta--primary {
            color: white;
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-accent));
            box-shadow: var(--shadow-md);
            flex-shrink: 0;
        }

        .booking-cta--soft {
            color: var(--brand-primary);
            background: white;
            border: 1px solid rgba(24, 49, 83, 0.08);
        }

        .locale-switch {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.3rem;
            border-radius: 999px;
            background: rgba(20, 33, 61, 0.05);
            border: 1px solid rgba(20, 33, 61, 0.08);
            flex-shrink: 0;
        }

        .locale-switch a {
            padding: 0.45rem 0.75rem;
            border-radius: 999px;
            font-weight: 800;
            color: var(--muted);
        }

        .locale-switch a.is-active {
            color: white;
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-accent));
        }

        .booking-alert {
            padding: 1rem 1.1rem;
            border-radius: 18px;
            border: 1px solid rgba(180, 35, 24, 0.14);
            background: rgba(180, 35, 24, 0.06);
            color: var(--danger);
            margin: 1.25rem 0 0;
        }

        .booking-search {
            padding: 1.5rem;
            border-radius: 28px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(247, 243, 234, 0.96));
            border: 1.5px solid rgba(20, 33, 61, 0.08);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08), inset 0 1px 1px rgba(255, 255, 255, 0.6);
            transition: all 0.3s ease;
            width: 100%;
            min-width: 100%;
        }

        .booking-search:hover,
        .booking-search:focus-within {
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.12), inset 0 1px 1px rgba(255, 255, 255, 0.8);
            border-color: rgba(20, 33, 61, 0.12);
        }

        .booking-search__grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 1.2rem;
            align-items: flex-end;
        }

        .booking-search__field {
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .booking-search__label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--muted);
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            transition: color 0.2s ease;
        }

        .booking-search__label i {
            font-size: 0.9rem;
            opacity: 0.7;
            transition: all 0.2s ease;
        }

        .booking-search__input,
        .booking-search select {
            width: 100%;
            min-height: 56px;
            padding: 0.95rem 1.15rem;
            border-radius: 18px;
            border: 2px solid rgba(20, 33, 61, 0.08);
            background: transparent;
            font-size: 1.005rem;
            font-weight: 500;
            color: #1a202c;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .booking-search__input::placeholder {
            color: #718096;
        }

        .booking-search__input:hover,
        .booking-search select:hover {
            border-color: rgba(20, 33, 61, 0.14);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        .booking-search__input:focus,
        .booking-search select:focus {
            outline: none;
            border-color: var(--brand-accent);
            box-shadow: 0 0 0 3px rgba(179, 138, 61, 0.1), 0 6px 16px rgba(179, 138, 61, 0.12);
            background: transparent;
        }

        .booking-search__actions {
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .booking-search__button {
            min-height: 56px;
            border-radius: 18px;
            font-weight: 600;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 24px rgba(179, 138, 61, 0.2);
            position: relative;
            overflow: hidden;
            width: 100%;
        }

        .booking-search__button::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .booking-search__button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(179, 138, 61, 0.28);
        }

        .booking-search__button:hover::before {
            opacity: 1;
        }

        .booking-search__button:active {
            transform: translateY(0);
            box-shadow: 0 4px 16px rgba(179, 138, 61, 0.2);
        }

        .booking-search__button-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            animation: fadeIn 0.4s ease;
        }

        .booking-search__button-text {
            animation: fadeIn 0.4s ease 0.1s both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(4px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Select dropdown styling */
        .booking-search select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23402c1c' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            padding-right: 2.5rem;
        }

        .booking-search select:disabled {
            background-color: rgba(20, 33, 61, 0.04);
            color: rgba(20, 33, 61, 0.5);
            cursor: not-allowed;
        }

        /* Date input styling */
        .booking-search input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(0.5);
            cursor: pointer;
        }

        .booking-search input[type="date"]:hover::-webkit-calendar-picker-indicator {
            filter: invert(0.8);
        }

        /* Focus visible for accessibility */
        .booking-search__input:focus-visible,
        .booking-search select:focus-visible {
            outline: 2px solid var(--brand-accent);
            outline-offset: 2px;
        }

        /* Loading state */
        .booking-search.is-loading .booking-search__button-icon {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .room-card {
            display: grid;
            overflow: hidden;
            grid-template-rows: 190px minmax(0, 1fr);
            min-height: 100%;
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid rgba(20, 33, 61, 0.08);
            box-shadow: var(--shadow-md);
            transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
        }

        .room-card:hover {
            transform: translateY(-4px);
            border-color: rgba(179, 138, 61, 0.24);
            box-shadow: 0 18px 42px rgba(20, 33, 61, 0.12);
        }

        .room-card__media {
            position: relative;
            overflow: hidden;
            background: rgba(20, 33, 61, 0.06);
        }

        .room-card__media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .room-card:hover .room-card__media img {
            transform: scale(1.04);
        }

        .room-card__badge {
            position: absolute;
            inset-block-start: 0.9rem;
            inset-inline-start: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.55rem 0.75rem;
            border-radius: 999px;
            font-weight: 800;
            font-size: 0.78rem;
            color: white;
            background: rgba(15, 118, 110, 0.92);
            box-shadow: 0 12px 26px rgba(20, 33, 61, 0.16);
        }

        .room-card__badge.is-soldout {
            background: rgba(180, 35, 24, 0.92);
        }

        .room-card__body {
            padding: 1.15rem;
            display: grid;
            gap: 0.85rem;
        }

        .room-card__title-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.9rem;
        }

        .room-card__title {
            margin: 0;
            font-size: 1.45rem;
            line-height: 1.08;
        }

        .room-card__unit {
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

        .room-card__summary {
            margin: 0;
            color: var(--muted);
            line-height: 1.55;
        }

        .room-card__facts {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.55rem;
        }

        .room-card__facts span {
            display: grid;
            gap: 0.2rem;
            min-width: 0;
            padding: 0.65rem;
            border-radius: 14px;
            background: rgba(20, 33, 61, 0.04);
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--muted);
        }

        .room-card__facts i {
            color: var(--brand-accent);
        }

        .room-card__amenities {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .room-card__amenities span {
            display: inline-flex;
            padding: 0.45rem 0.65rem;
            border-radius: 999px;
            background: rgba(20, 33, 61, 0.05);
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--muted);
        }

        .room-card__footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.8rem;
            margin-top: auto;
        }

        .room-card__price strong {
            font-size: 1.25rem;
            line-height: 1;
        }

        .room-card__price small {
            display: block;
            color: var(--muted);
            font-size: 0.78rem;
            font-weight: 700;
        }

        .room-card__actions {
            display: flex;
            gap: 0.5rem;
            flex: 0 0 auto;
        }

        .room-card__actions .booking-cta {
            min-height: 42px;
            padding: 0.7rem 0.85rem;
            font-size: 0.88rem;
        }

        .live-results-stack {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }

        .booking-main {
            padding: 2rem 0 4rem;
        }

        .booking-footer {
            padding: 0 0 3rem;
            color: var(--muted);
        }

        .booking-footer__panel {
            border-radius: var(--radius-xl);
            background:
                linear-gradient(135deg, rgba(24, 49, 83, 0.96), rgba(179, 138, 61, 0.9)),
                linear-gradient(180deg, rgba(255, 255, 255, 0.06), rgba(255, 255, 255, 0));
            color: rgba(255, 255, 255, 0.9);
            padding: 1.5rem;
            box-shadow: var(--shadow-lg);
        }

        .booking-footer__grid {
            display: grid;
            grid-template-columns: 1.3fr 0.9fr 1fr 0.95fr;
            gap: 1rem;
        }

        .booking-footer__card {
            padding: 1.25rem;
            border-radius: var(--radius-lg);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            min-width: 0;
            height: 100%;
        }

        .booking-footer__brand {
            background: rgba(255, 255, 255, 0.12);
        }

        .booking-footer__brand-row {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            margin-bottom: 1rem;
        }

        .booking-footer__brand-logo {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            object-fit: cover;
            background: white;
            box-shadow: 0 12px 24px rgba(9, 15, 29, 0.22);
        }

        .booking-footer__eyebrow {
            margin: 0 0 0.25rem;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.72);
        }

        .booking-footer__brand-name {
            margin: 0;
            font-size: 1.6rem;
            line-height: 1;
            color: white;
        }

        .booking-footer__text {
            margin: 0;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.86);
        }

        .booking-footer__title {
            color: white;
            font-size: 0.9rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.6rem;
        }

        .booking-footer__links,
        .booking-footer__contact-list,
        .booking-footer__actions {
            display: grid;
            gap: 0.7rem;
        }

        .booking-footer__link,
        .booking-footer__contact-item {
            display: flex;
            align-items: flex-start;
            gap: 0.7rem;
            padding: 0.8rem 0.9rem;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            min-width: 0;
            color: rgba(255, 255, 255, 0.88);
        }

        .booking-footer__link {
            justify-content: space-between;
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .booking-footer__link:hover {
            transform: translateY(-1px);
            background: rgba(255, 255, 255, 0.14);
        }

        .booking-footer__contact-item i,
        .booking-footer__link i {
            color: rgba(255, 255, 255, 0.78);
            line-height: 1.4;
            flex-shrink: 0;
        }

        .booking-footer__link span:last-child,
        .booking-footer__contact-copy {
            min-width: 0;
            overflow-wrap: anywhere;
        }

        .booking-footer__actions .booking-cta {
            width: 100%;
        }

        .booking-footer__bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1.25rem;
            padding-top: 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.14);
            color: rgba(255, 255, 255, 0.74);
            font-size: 0.92rem;
        }

        .page-card {
            border-radius: var(--radius-xl);
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(20, 33, 61, 0.07);
            box-shadow: var(--shadow-lg);
        }

        .section-title {
            margin: 0 0 0.35rem;
            font-size: clamp(2rem, 4vw, 3.5rem);
            line-height: 0.95;
        }

        .section-copy {
            margin: 0;
            color: var(--muted);
            font-size: 1rem;
            line-height: 1.7;
        }

        .booking-empty-state {
            padding: 1.6rem;
            text-align: center;
        }

        @media (min-width: 1200px) {
            .booking-link {
                padding: 0.55rem 0.8rem;
                font-size: 0.95rem;
            }

            .booking-cta {
                padding: 0.82rem 1rem;
                font-size: 0.95rem;
            }

            .locale-switch a {
                padding: 0.4rem 0.65rem;
            }
        }

        @media (max-width: 1199px) {
            .booking-nav {
                grid-template-columns: minmax(0, 1fr) auto;
                align-items: center;
            }

            .booking-brand {
                justify-content: flex-start;
            }

            .booking-nav__toggle {
                display: inline-flex;
            }

            .booking-nav__group {
                display: none;
                grid-column: 1 / -1;
                align-items: stretch;
            }

            .booking-nav.is-open .booking-nav__group {
                display: flex;
            }

            .booking-nav__topbar {
                flex-direction: column;
                align-items: stretch;
                gap: 0.9rem;
            }

            .booking-nav__links {
                justify-content: flex-start;
            }

            .booking-nav__links,
            .booking-nav__actions,
            .booking-nav__tools {
                justify-content: flex-start;
            }

            .booking-nav__actions {
                width: 100%;
            }

            .booking-search {
                padding: 1.4rem;
            }

            .booking-search__grid {
                grid-template-columns: repeat(3, minmax(120px, 1fr));
                gap: 1rem;
            }

            .booking-search__actions {
                grid-column: 1 / -1;
            }

            .room-card {
                grid-template-rows: 220px minmax(0, 1fr);
            }

            .live-results-stack {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .room-card__footer {
                flex-direction: column;
                align-items: stretch;
            }

            .room-card__actions {
                justify-content: flex-start;
            }

            .booking-main {
                padding: 1.5rem 0 3rem;
            }

            .booking-footer__grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767px) {
            .booking-shell {
                width: min(100% - 1rem, 1180px);
            }

            .booking-brand {
                gap: 0.75rem;
            }

            .booking-brand img {
                width: 46px;
                height: 46px;
                border-radius: 16px;
            }

            .booking-brand__name {
                font-size: 0.98rem;
            }

            .booking-brand__meta {
                font-size: 0.82rem;
            }

            .booking-nav__toggle {
                min-width: 44px;
                min-height: 44px;
                padding: 0.7rem 0.9rem;
            }

            .booking-link,
            .booking-cta,
            .locale-switch {
                width: 100%;
            }

            .booking-search {
                padding: 1.25rem;
                border-radius: 22px;
            }

            .booking-search__grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 0.85rem;
            }

            .booking-search__field {
                gap: 0.5rem;
            }

            .booking-search__label {
                font-size: 0.78rem;
            }

            .booking-search__input,
            .booking-search select {
                min-height: 52px;
                padding: 0.85rem 0.95rem;
            }

            .booking-search__actions {
                grid-column: 1 / -1;
            }

            .booking-search__button {
                min-height: 52px;
            }

            .booking-nav__actions {
                width: 100%;
                flex-direction: column;
            }

            .locale-switch {
                justify-content: center;
            }

            .locale-switch a {
                flex: 1;
                text-align: center;
            }

            .page-card {
                border-radius: 22px;
            }

            .booking-footer__card {
                padding: 1rem;
            }

            .room-card__body {
                padding: 1.1rem;
            }

            .booking-footer__panel {
                padding: 1rem;
            }

            .booking-footer__grid {
                grid-template-columns: 1fr;
            }

            .booking-footer__brand-row {
                align-items: flex-start;
            }

            .booking-footer__bottom {
                font-size: 0.88rem;
            }
        }

        @media (max-width: 575px) {
            .booking-main {
                padding: 1.25rem 0 2.5rem;
            }

            .booking-search {
                padding: 1.1rem;
                border-radius: 20px;
            }

            .booking-search__grid {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }

            .booking-search__label {
                font-size: 0.75rem;
            }

            .booking-search__input,
            .booking-search select {
                min-height: 48px;
                padding: 0.8rem 0.9rem;
                font-size: 0.95rem;
            }

            .booking-search__actions {
                grid-column: 1;
            }

            .booking-search__button {
                min-height: 48px;
                font-size: 0.9rem;
                gap: 0.6rem;
            }

            .booking-search__button-text {
                display: inline;
            }

            .booking-search__button-icon {
                font-size: 1rem;
            }

            .section-title {
                font-size: clamp(1.8rem, 10vw, 2.4rem);
                line-height: 1;
            }

            .section-copy {
                font-size: 0.95rem;
            }

            .live-results-stack {
                grid-template-columns: 1fr;
            }

            .room-card__title {
                font-size: 1.45rem;
            }

            .room-card__price strong {
                font-size: 1.25rem;
            }

            .room-card__actions .booking-cta {
                width: 100%;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    @php
        $t = fn ($ar, $en) => app()->getLocale() === 'ar' ? $ar : $en;
        $bookingPropertyQuery = $bookingPropertyQuery ?? [];
        $footerNote = app()->getLocale() === 'ar'
            ? ($websiteSettings->footer_note_ar ?: $websiteSettings->footer_note_en)
            : ($websiteSettings->footer_note_en ?: $websiteSettings->footer_note_ar);
    @endphp

    <header class="booking-header">
        <div class="booking-shell">
            <nav class="booking-nav">
                <a href="{{ route('booking.home', $bookingPropertyQuery) }}" class="booking-brand">
                    <img src="{{ $branding['logo_url'] }}" alt="{{ $branding['name'] }}">
                    <div>
                        <p class="booking-brand__name">{{ $branding['name'] }}</p>
                    </div>
                </a>
                <button type="button" class="booking-nav__toggle" aria-expanded="false" aria-controls="booking-nav-menu">
                    <i class="fas fa-bars" aria-hidden="true"></i>
                    <span class="d-none d-sm-inline">{{ $t('القائمة', 'Menu') }}</span>
                </button>

                <div class="booking-nav__group" id="booking-nav-menu">
                    <div class="booking-nav__topbar">
                        <div class="booking-nav__links">
                            <a class="booking-link {{ request()->routeIs('booking.home') ? 'is-active' : '' }}" href="{{ route('booking.home', $bookingPropertyQuery) }}">
                                {{ $t('الرئيسية', 'Home') }}
                            </a>
                            <a class="booking-link {{ request()->routeIs('booking.rooms.*') || request()->routeIs('booking.search') ? 'is-active' : '' }}" href="{{ route('booking.rooms.index', $bookingPropertyQuery) }}">
                                {{ $t('الغرف', 'Rooms') }}
                            </a>
                            @foreach ($navigationPages as $navPage)
                                @php
                                    $routeName = 'booking.'.$navPage->page_key;
                                @endphp
                                <a class="booking-link {{ request()->routeIs($routeName) ? 'is-active' : '' }}" href="{{ route($routeName, $bookingPropertyQuery) }}">
                                    {{ app()->getLocale() === 'ar' ? ($navPage->nav_label_ar ?: $navPage->nav_label_en) : ($navPage->nav_label_en ?: $navPage->nav_label_ar) }}
                                </a>
                            @endforeach
                        </div>
                        <div class="booking-nav__actions">
                            <div class="locale-switch">
                                <a href="{{ url('/locale/en') }}" class="{{ app()->getLocale() === 'en' ? 'is-active' : '' }}">EN</a>
                                <a href="{{ url('/locale/ar') }}" class="{{ app()->getLocale() === 'ar' ? 'is-active' : '' }}">AR</a>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            @if ($errors->any())
                <div class="booking-alert">
                    {{ $errors->first() }}
                </div>
            @endif
        </div>
    </header>

    <main class="booking-main">
        <div class="booking-shell">
            @yield('content')
        </div>
    </main>

    <footer class="booking-footer">
        <div class="booking-shell">
            <div class="booking-footer__panel">
                <div class="booking-footer__grid">
                    <div class="booking-footer__card booking-footer__brand">
                        <div class="booking-footer__brand-row">
                            <img class="booking-footer__brand-logo" src="{{ $branding['logo_url'] }}" alt="{{ $branding['name'] }}">
                            <div>
                                <p class="booking-footer__eyebrow">{{ $t('الحجز المباشر', 'Direct Booking') }}</p>
                                <h2 class="booking-footer__brand-name">{{ $branding['name'] }}</h2>
                            </div>
                        </div>
                        @if (!empty($footerNote))
                            <p class="booking-footer__text mt-3">{{ $footerNote }}</p>
                        @endif
                    </div>

                    <div class="booking-footer__card">
                        <div class="booking-footer__title">{{ $t('روابط سريعة', 'Quick Links') }}</div>
                        <div class="booking-footer__links">
                            <a class="booking-footer__link" href="{{ route('booking.home', $bookingPropertyQuery) }}">
                                <span>{{ $t('الرئيسية', 'Home') }}</span>
                                <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
                            </a>
                            <a class="booking-footer__link" href="{{ route('booking.rooms.index', $bookingPropertyQuery) }}">
                                <span>{{ $t('الغرف', 'Rooms') }}</span>
                                <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
                            </a>
                            @foreach ($navigationPages as $navPage)
                                @php
                                    $routeName = 'booking.'.$navPage->page_key;
                                @endphp
                                <a class="booking-footer__link" href="{{ route($routeName, $bookingPropertyQuery) }}">
                                    <span>{{ app()->getLocale() === 'ar' ? ($navPage->nav_label_ar ?: $navPage->nav_label_en) : ($navPage->nav_label_en ?: $navPage->nav_label_ar) }}</span>
                                    <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="booking-footer__card">
                        <div class="booking-footer__title">{{ $t('الدعم والموقع', 'Support & Location') }}</div>
                        <div class="booking-footer__contact-list">
                            <div class="booking-footer__contact-item">
                                <i class="fas fa-location-dot"></i>
                                <div class="booking-footer__contact-copy">{{ $branding['address'] }}</div>
                            </div>
                            @if (!empty($branding['phone']))
                                <a class="booking-footer__contact-item" href="tel:{{ $branding['phone'] }}">
                                    <i class="fas fa-phone"></i>
                                    <div class="booking-footer__contact-copy">{{ $branding['phone'] }}</div>
                                </a>
                            @endif
                            @if (!empty($branding['email']))
                                <a class="booking-footer__contact-item" href="mailto:{{ $branding['email'] }}">
                                    <i class="fas fa-envelope"></i>
                                    <div class="booking-footer__contact-copy">{{ $branding['email'] }}</div>
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="booking-footer__card">
                        <div class="booking-footer__title">{{ $t('ابدأ الحجز', 'Start Booking') }}</div>
                        <div class="booking-footer__actions">
                            @if (!empty($branding['phone']))
                                <a class="booking-cta booking-cta--soft" href="tel:{{ $branding['phone'] }}">
                                    <i class="fas fa-headset"></i>
                                    {{ $t('تواصل معنا', 'Contact Us') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="booking-footer__bottom">
                    <div>{{ $t('جميع الأسعار والتوفرات مرتبطة مباشرة بنظام الحجز.', 'Rates and availability stay synced with the reservation system.') }}</div>
                    <div>&copy; {{ now()->year }} {{ $branding['name'] }}</div>
                </div>
            </div>
        </div>
    </footer>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        const bookingNav = document.querySelector('.booking-nav');
        const bookingNavToggle = document.querySelector('.booking-nav__toggle');
        const bookingNavMenu = document.getElementById('booking-nav-menu');
        const bookingNavBreakpoint = window.matchMedia('(max-width: 1199px)');

        const syncBookingNavState = () => {
            if (!bookingNav || !bookingNavToggle || !bookingNavMenu) {
                return;
            }

            if (!bookingNavBreakpoint.matches) {
                bookingNav.classList.remove('is-open');
                bookingNavToggle.setAttribute('aria-expanded', 'false');
                return;
            }

            if (!bookingNav.classList.contains('is-open')) {
                bookingNavToggle.setAttribute('aria-expanded', 'false');
            }
        };

        if (bookingNav && bookingNavToggle && bookingNavMenu) {
            bookingNavToggle.addEventListener('click', () => {
                const isOpen = bookingNav.classList.toggle('is-open');
                bookingNavToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });

            bookingNavMenu.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => {
                    if (!bookingNavBreakpoint.matches) {
                        return;
                    }

                    bookingNav.classList.remove('is-open');
                    bookingNavToggle.setAttribute('aria-expanded', 'false');
                });
            });

            if (typeof bookingNavBreakpoint.addEventListener === 'function') {
                bookingNavBreakpoint.addEventListener('change', syncBookingNavState);
            } else if (typeof bookingNavBreakpoint.addListener === 'function') {
                bookingNavBreakpoint.addListener(syncBookingNavState);
            }

            syncBookingNavState();
        }

        document.querySelectorAll('form[data-booking-search]').forEach((form) => {
            const checkIn = form.querySelector('input[name="check_in"]');
            const checkOut = form.querySelector('input[name="check_out"]');

            if (!checkIn || !checkOut) {
                return;
            }

            const syncCheckout = () => {
                checkOut.min = checkIn.value;

                if (checkOut.value && checkIn.value && checkOut.value <= checkIn.value) {
                    const nextDate = new Date(checkIn.value);
                    nextDate.setDate(nextDate.getDate() + 1);
                    checkOut.value = nextDate.toISOString().split('T')[0];
                }
            };

            checkIn.addEventListener('change', syncCheckout);
            syncCheckout();
        });
    </script>

    @if (!empty($structuredData))
        @foreach ($structuredData as $schema)
            <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @endforeach
    @endif

    @include('booking_site.partials.chatbot')

    @stack('scripts')
</body>

</html>
