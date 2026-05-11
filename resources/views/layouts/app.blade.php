<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Reservation System'))</title>
    <link rel="icon" href="{{ asset('logo.webp') }}" type="image/x-icon">

    <!-- Local Bootstrap 5 CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Bootstrap RTL for Arabic -->
    @if (app()->getLocale() == 'ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-rtl@5.3.0/css/bootstrap-rtl.min.css">
    @endif

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Google Fonts -->
    @if (app()->getLocale() == 'ar')
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    @endif

    @php
        $theme = \App\Models\ThemeCustomization::getTheme();
    @endphp

    <style>
        :root {
            --sidebar-bg: {{ $theme->sidebar_bg_color }};
            --sidebar-text: {{ $theme->sidebar_text_color }};
            --sidebar-active: {{ $theme->sidebar_active_color }};
            --sidebar-hover: {{ $theme->sidebar_hover_color }};
            --primary-blue: #1a73e8;
            --light-bg: #f8f9fa;
            --border-color: #dee2e6;
            --text-dark: {{ $theme->text_color }};
            --text-light: #6c757d;
        }

        * {
            font-family: @if (app()->getLocale() == 'ar')
                'Cairo',
            @endif
            {{ $theme->font_family }};
        }

        body {
            background-color: #f5f5f5;
            color: var(--text-dark);
            overflow-x: hidden;

            {{-- RTL/LTR specific --}} @if (app()->getLocale() == 'ar')
                direction: rtl;
                text-align: right;
            @else
                direction: ltr;
                text-align: left;
            @endif
        }

        /* Sidebar - RTL/LTR aware */
        #sidebar {
            position: fixed;
            top: 0;

            @if (app()->getLocale() == 'ar')
                right: 0;
            @else
                left: 0;
            @endif
            width: 280px;
            height: 100vh;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            transition: all 0.3s;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 20px 15px;
            background-color: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .logo-title {
            color: white;
            font-weight: 700;
            font-size: 1.8rem;
            margin: 0;
        }

        .sidebar-menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            margin-bottom: 2px;
        }

        .sidebar-menu a,
        .sidebar-menu button {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 12px 20px;
            color: var(--sidebar-text);
            text-decoration: none;
            background: none;
            border: none;

            @if (app()->getLocale() == 'ar')
                text-align: right;
                border-right: 4px solid transparent;
            @else
                text-align: left;
                border-left: 4px solid transparent;
            @endif
            transition: all 0.3s;
        }

        .sidebar-menu a:hover,
        .sidebar-menu button:hover {
            background-color: var(--sidebar-hover);

            @if (app()->getLocale() == 'ar')
                border-right-color: var(--primary-blue);
            @else
                border-left-color: var(--primary-blue);
            @endif
        }

        .sidebar-menu a.active {
            background-color: var(--sidebar-active);

            @if (app()->getLocale() == 'ar')
                border-right-color: var(--primary-blue);
            @else
                border-left-color: var(--primary-blue);
            @endif
            font-weight: 600;
        }

        .sidebar-menu i {
            width: 25px;

            @if (app()->getLocale() == 'ar')
                margin-left: 10px;
            @else
                margin-right: 10px;
            @endif
        }

        /* Main Content Area */
        #content {
            @if (app()->getLocale() == 'ar')
                margin-right: 280px;
            @else
                margin-left: 280px;
            @endif
            min-height: 100vh;
            transition: all 0.3s;
        }

        /* Search Box */
        .search-container {
            position: relative;
        }

        .search-input {
            border-radius: 0.5rem;

            @if (app()->getLocale() == 'ar')
                padding-right: 2.5rem;
            @else
                padding-left: 2.5rem;
            @endif
            border: 1px solid var(--border-color);
            width: 100%;
        }

        .search-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);

            @if (app()->getLocale() == 'ar')
                left: 0.75rem;
            @else
                right: 0.75rem;
            @endif
        }

        /* Stat Box Border */
        .stat-box {
            background-color: var(--light-bg);
            border-radius: 0.375rem;
            padding: 1rem;

            @if (app()->getLocale() == 'ar')
                border-right: 4px solid var(--primary-blue);
            @else
                border-left: 4px solid var(--primary-blue);
            @endif
        }

        /* Mobile Toggle */
        @media (max-width: 992px) {
            #sidebar {
                @if (app()->getLocale() == 'ar')
                    margin-right: -280px;
                @else
                    margin-left: -280px;
                @endif
            }

            #sidebar.active {
                margin-right: 0;
                margin-left: 0;
            }

            #content {
                margin-right: 0 !important;
                margin-left: 0 !important;
            }
        }

        /* Custom Button Colors */
        .btn-primary {
            background-color: {{ $theme->button_primary_color }};
            border-color: {{ $theme->button_primary_color }};
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: {{ $theme->button_primary_color }};
            border-color: {{ $theme->button_primary_color }};
            opacity: 0.9;
        }

        .btn-secondary {
            background-color: {{ $theme->button_secondary_color }};
            border-color: {{ $theme->button_secondary_color }};
        }

        .btn-secondary:hover,
        .btn-secondary:focus {
            background-color: {{ $theme->button_secondary_color }};
            border-color: {{ $theme->button_secondary_color }};
            opacity: 0.9;
        }

        /* Custom Card Styles */
        .card {
            background-color: {{ $theme->card_bg_color }};
            border-color: {{ $theme->card_border_color }};
        }

        .card-header {
            background-color: {{ $theme->table_header_bg }};
            color: {{ $theme->table_header_text }};
            border-bottom: 1px solid {{ $theme->card_border_color }};
        }

        /* Custom Table Styles */
        .table {
            color: {{ $theme->text_color }};
        }

        .table thead th {
            background-color: {{ $theme->table_header_bg }};
            color: {{ $theme->table_header_text }};
            border-color: {{ $theme->card_border_color }};
        }

        .table tbody tr:nth-child(even) {
            background-color: {{ $theme->table_row_even }};
        }

        .table tbody tr:nth-child(odd) {
            background-color: {{ $theme->table_row_odd }};
        }

        .table tbody td {
            border-color: {{ $theme->card_border_color }};
        }

        /* Custom Form Input Styles */
        .form-control {
            background-color: {{ $theme->input_bg_color }};
            border-color: {{ $theme->input_border_color }};
            color: {{ $theme->input_text_color }};
        }

        .form-control:focus {
            background-color: {{ $theme->input_bg_color }};
            border-color: {{ $theme->button_primary_color }};
            color: {{ $theme->input_text_color }};
        }

        .form-control::placeholder {
            color: #999;
        }

        .form-select {
            background-color: {{ $theme->input_bg_color }};
            border-color: {{ $theme->input_border_color }};
            color: {{ $theme->input_text_color }};
        }

        .form-select:focus {
            border-color: {{ $theme->button_primary_color }};
        }

        .top-navbar.app-header {
            min-height: 72px;
        }

        .app-header__bar,
        .app-header__actions,
        .app-header__tabs,
        .property-switcher {
            min-height: 38px;
        }

        .app-header__bar {
            gap: 1rem;
        }

        .app-header__title {
            min-width: 0;
        }

        .app-header__actions,
        .app-header__tabs,
        .property-switcher {
            flex-shrink: 0;
        }

        .property-switcher {
            margin: 0;
        }

        .property-switcher__select.form-select.form-select-sm {
            width: 220px;
            min-width: 220px;
            height: 31px;
            min-height: 31px;
            margin: 0;
            padding-top: .25rem;
            padding-bottom: .25rem;
            line-height: 1.5;
            vertical-align: middle;
        }

        @media (max-width: 992px) {
            .app-header__bar,
            .app-header__actions {
                flex-wrap: wrap;
            }

            .app-header__actions {
                row-gap: .5rem;
            }
        }

        /* Custom Badge Colors */
        .badge.bg-primary {
            background-color: {{ $theme->button_primary_color }} !important;
        }

        .page-item.active .page-link {
            background-color: {{ $theme->button_primary_color }};
            border-color: {{ $theme->button_primary_color }};
        }

        /* Dashboard Card Styles */
        .setting-card,
        .dashboard-card {
            background-color: {{ $theme->dashboard_card_bg }};
            border-color: {{ $theme->dashboard_card_border }};
        }

        .setting-card .icon svg,
        .dashboard-card .icon svg {
            stroke: {{ $theme->dashboard_icon_color }};
        }

        .setting-card .title,
        .dashboard-card .title {
            color: {{ $theme->dashboard_card_title_color }};
        }

        .setting-card .title small,
        .dashboard-card .title small {
            color: {{ $theme->dashboard_card_text_color }};
        }
    </style>

</head>

<body>
    <div class="wrapper"
        style="{{ $theme->background_image ? "background-image: url('".asset($theme->background_image)."');" : "background-image: linear-gradient(135deg, #eef2ff 0%, #dbeafe 50%, #e0f2fe 100%);" }}">
        <!-- Sidebar Conditional by Route Name -->
        @if (request()->routeIs('setup-sidebar.*'))
            @include('layouts.setup-sidebar')
        @else
            {{-- @include('admin.partials.flash') --}}
            @include('layouts.sidebar')
        @endIf

        <!-- Main Content -->
        <div id="content">
            <!-- Top Header -->
            @include('layouts.header')

            <!-- Page Content -->
            <main class="container-fluid py-4">
                @yield('content')
            </main>
        </div>
    </div>

    @auth
        @include('dashboard.partials.chatbot')
    @endauth

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

    <script>
        // Sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');

            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                });
            }

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth < 992) {
                    if (sidebar && !sidebar.contains(event.target) &&
                        sidebarToggle && !sidebarToggle.contains(event.target) &&
                        sidebar.classList.contains('active')) {
                        sidebar.classList.remove('active');
                    }
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
