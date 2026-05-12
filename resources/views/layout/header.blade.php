<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>@yield('title', 'Human Resource Management')</title>
    @if (app()->getLocale() == 'ar')
        <link rel="stylesheet" href="{{ asset('css/app.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('css/app.min.css') }}">
    @endif

    <link rel="stylesheet" href="{{ asset('css/app.min.css') }}">
    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">

    <!-- Custom style CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel='shortcut icon' type='image/x-icon' href='{{ asset('favicon.png') }}' />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

@php
    $setting = \App\Models\GeneralSetting::first();
    $background = $setting?->dashboard_background ?? null;
@endphp

<body
    style="
    background: {{ $background ? "url('" . asset($background) . "') no-repeat center center fixed" : '#ffffff' }};
    background-size: {{ $background ? 'cover' : 'auto' }};
">
    <div class="loader"></div>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
            <nav class="navbar navbar-expand-lg main-navbar sticky">
                <div class="form-inline mr-auto">
                    <ul class="navbar-nav mr-3">
                        <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg
									collapse-btn">
                                <i data-feather="align-justify"></i></a></li>


                    </ul>
                </div>
                <ul class="navbar-nav navbar-right">
                    <li style="display: none;"><a href="#" class="nav-link nav-link-lg fullscreen-btn">
                            <i data-feather="maximize"></i>
                        </a></li>

                    <li>
                        <a href="{{ route('program') }}"
                           style="display:inline-flex;align-items:center;gap:6px;color:#6c757d;padding:8px 12px;text-decoration:none;white-space:nowrap;border-radius:6px;transition:all 0.2s;"
                           onmouseover="this.style.background='#f1f5f9'"
                           onmouseout="this.style.background='transparent'">
                            <i data-feather="grid" style="width:16px;height:16px;"></i>
                            <span>{{ __('dashboard.reservation_management') }}</span>
                        </a>
                    </li>

                    <!-- Globe Icon -->

                    @php
                        $current = App::getLocale(); // Get current locale
                        $switchLocale = $current === 'en' ? 'ar' : 'en'; // If English, switch to Arabic, else English
                        $switchLabel = $current === 'en' ? 'Arabic' : 'English';
                    @endphp

                    <a href="{{ route('locale.switch', ['locale' => $switchLocale]) }}" class="language-switch mt-2">
                        <i class="fas fa-globe"></i> {{ $switchLabel }}
                    </a>


                    <li class="dropdown dropdown-list-toggle">
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" id="notifDropdown" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false"
                            style="display: flex; align-items: center; gap: 2px;">
                            <i class="fas fa-bell" style="font-size: 16px; color: #555; line-height: 1;"></i>
                            <span id="notifCount" class="badge badge-danger"
                                style="display:none; font-size: 10px; position: relative; top: -6px; right: 4px;"></span>
                        </a>

                        <div class="dropdown-menu dropdown-list dropdown-menu-right pullDown"
                            aria-labelledby="notifDropdown" style="width:360px;">
                            <div class="dropdown-header">
                                {{ __('dashboard.notifications') }}
                                <div class="float-right">
                                    <a href="#" id="markAllRead">{{ __('dashboard.mark_all_as_read') }}</a>

                                </div>
                            </div>
                            <div id="notifList" class="dropdown-list-content dropdown-list-icons"></div>
                            <div id="notifEmpty" class="py-3 text-center text-muted" style="display:none;">
                                {{ __('dashboard.no_new_notification') }}</div>

                            <div class="dropdown-footer text-center">
                                <a href="{{ route('notification.viewAll') }}">{{ __('dashboard.view_all') }} <i
                                        class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </li>

                    </li>
                    <li class="dropdown"><a href="#" data-toggle="dropdown"
                            class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                            <svg class="user-img-radious-style" width="40" height="40" viewBox="0 0 24 24"
                                fill="#000" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                            </svg>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right pullDown">
                            <div class="dropdown-title">Hello {{ Auth::user()->name ?? '-' }} !</div>
                            <a href="{{ route('dashboard.setting.user.password') }}" class="dropdown-item has-icon">
                                <i class="far
										fa-user"></i>
                                {{ __('dashboard.change_password') }}
                            </a>
                            {{-- @if (Auth::user()->hasRole('super_admin'))
                                <a href="{{ route('dashboard.setting.general.index') }}"
                                    class="dropdown-item has-icon">
                                    <i class="fas fa-cog"></i>
                                    {{ __('dashboard.settings') }}
                                </a>
                            @endif --}}
                            @can('manage_setting')
                                <a href="{{ route('dashboard.setting.general.index') }}"
                                    class="dropdown-item has-icon">
                                    <i class="fas fa-cog"></i>
                                    {{ __('dashboard.settings') }}
                                </a>
                            @endcan
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <div class="dropdown-divider"></div>
                                <button type="submit" class="dropdown-item has-icon text-danger"> <i
                                        class="fas fa-sign-out-alt mt-2"></i>
                                    {{ __('dashboard.logout') }}
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </nav>
            @include('layout.messages')
