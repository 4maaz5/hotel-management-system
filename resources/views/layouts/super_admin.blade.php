@php
    $theme = \App\Models\ThemeCustomization::getTheme();
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin')</title>
    <link rel="icon" href="{{ asset('logo.webp') }}" type="image/x-icon">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --sidebar-bg: {{ $theme->sidebar_bg_color }};
            --sidebar-text: {{ $theme->sidebar_text_color }};
            --sidebar-active: {{ $theme->sidebar_active_color }};
            --sidebar-hover: {{ $theme->sidebar_hover_color }};
            --body-bg: #f4f6fb;
            --content-text: {{ $theme->text_color }};
            --card-bg: {{ $theme->card_bg_color }};
            --card-border: {{ $theme->card_border_color }};
            --primary: {{ $theme->button_primary_color }};
        }

        * {
            font-family: {{ $theme->font_family }};
        }

        body {
            margin: 0;
            background: var(--body-bg);
            color: var(--content-text);
            overflow-x: hidden;
        }

        .super-admin-shell {
            display: flex;
            min-height: 100vh;
        }

        .super-admin-sidebar {
            width: 280px;
            background: linear-gradient(180deg, var(--sidebar-bg), #111a52);
            color: var(--sidebar-text);
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 1030;
            padding: 24px 16px;
            box-shadow: 18px 0 45px rgba(15, 23, 42, 0.15);
        }

        html[dir="rtl"] .super-admin-sidebar {
            inset: 0 0 0 auto;
            box-shadow: -18px 0 45px rgba(15, 23, 42, 0.15);
        }

        .super-admin-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 8px 10px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 24px;
        }

        .super-admin-brand__logo {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.14);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .super-admin-brand__title {
            font-size: 1rem;
            font-weight: 700;
            margin: 0;
        }

        .super-admin-brand__subtitle {
            margin: 2px 0 0;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.85rem;
        }

        .super-admin-nav {
            display: grid;
            gap: 8px;
        }

        .super-admin-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 14px;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: background 0.18s ease, transform 0.18s ease;
        }

        .super-admin-nav a:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateX(2px);
        }

        .super-admin-nav a.is-active {
            background: var(--sidebar-active);
            color: #fff;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.08);
        }

        .super-admin-content {
            flex: 1;
            margin-left: 280px;
            min-width: 0;
        }

        html[dir="rtl"] .super-admin-content {
            margin-left: 0;
            margin-right: 280px;
        }

        .super-admin-header {
            position: sticky;
            top: 0;
            z-index: 1020;
            background: rgba(244, 246, 251, 0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(15, 23, 42, 0.08);
            padding: 18px 28px;
        }

        .super-admin-header__card {
            background: #fff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 18px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            box-shadow: 0 14px 35px rgba(15, 23, 42, 0.06);
        }

        .super-admin-header__eyebrow {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            margin-bottom: 2px;
        }

        .super-admin-header__title {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .super-admin-main {
            padding: 28px;
        }

        .super-admin-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: var(--primary);
            border-color: var(--primary);
            opacity: 0.92;
        }

        .super-admin-mobile-toggle {
            display: none;
            position: fixed;
            top: 18px;
            left: 18px;
            z-index: 1040;
        }

        html[dir="rtl"] .super-admin-mobile-toggle {
            left: auto;
            right: 18px;
        }

        @media (max-width: 991px) {
            .super-admin-mobile-toggle {
                display: inline-flex;
            }

            .super-admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.22s ease;
            }

            html[dir="rtl"] .super-admin-sidebar {
                transform: translateX(100%);
            }

            .super-admin-sidebar.is-open {
                transform: translateX(0);
            }

            .super-admin-content,
            html[dir="rtl"] .super-admin-content {
                margin-left: 0;
                margin-right: 0;
            }

            .super-admin-header,
            .super-admin-main {
                padding-left: 18px;
                padding-right: 18px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <button type="button" class="btn btn-primary super-admin-mobile-toggle" id="superAdminSidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <div class="super-admin-shell">
        @include('layouts.super_admin_sidebar')

        <div class="super-admin-content">
            @include('layouts.super_admin_header')

            <main class="super-admin-main">
                @foreach (['success', 'danger', 'warning', 'info'] as $messageType)
                    @if (session($messageType))
                        <div class="alert alert-{{ $messageType }} alert-dismissible fade show" role="alert">
                            {{ session($messageType) }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                @endforeach

                @yield('content')
            </main>
        </div>
    </div>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('superAdminSidebarToggle');
            const sidebar = document.getElementById('superAdminSidebar');

            toggle?.addEventListener('click', function () {
                sidebar?.classList.toggle('is-open');
            });

            document.addEventListener('click', function (event) {
                if (window.innerWidth >= 992 || !sidebar || !sidebar.classList.contains('is-open')) {
                    return;
                }

                if (!sidebar.contains(event.target) && !toggle?.contains(event.target)) {
                    sidebar.classList.remove('is-open');
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
