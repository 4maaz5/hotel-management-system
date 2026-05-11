@extends('layouts.app')
@php
    $theme = \App\Models\ThemeCustomization::getTheme();
@endphp
<style>
    .program-page,
    .program-page * {
        box-sizing: border-box;
    }


    .program-page .settings-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 30px;
        margin-bottom: 40px;
    }

    .program-page .setting-card {
        background: {{ $theme->dashboard_card_bg }};
        border: 2px solid {{ $theme->dashboard_card_border }};
        border-radius: 15px;
        padding: 40px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        min-height: 200px;
    }

    .program-page .setting-card:hover {
        background: {{ $theme->dashboard_card_bg }};
        opacity: 0.9;
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .program-page .setting-card .icon {
        width: 70px;
        height: 70px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .program-page .setting-card .icon svg {
        width: 100%;
        height: 100%;
        stroke: {{ $theme->dashboard_icon_color }};
        fill: none;
        stroke-width: 1.5;
    }

    .program-page .setting-card .title {
        color: {{ $theme->dashboard_card_title_color }};
        font-size: 18px;
        text-align: center;
        font-weight: 400;
    }

    .program-page .setting-card .title small {
        color: {{ $theme->dashboard_card_text_color }};
    }

    @media (max-width: 1200px) {
        .program-page .settings-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .program-page .settings-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .program-page .setting-card {
            padding: 30px 15px;
            min-height: 170px;
        }

        .program-page .setting-card .icon {
            width: 50px;
            height: 50px;
        }
    }

    @media (max-width: 480px) {
        .program-page .settings-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@section('content')

    <div class="container-fluid program-page">
        <div class="settings-grid">
            <!-- Row 1 -->
            <a href="{{ route('dashboard.reservation.index') }}" style="text-decoration: none;">
            <div class="setting-card">
                <div class="icon">
                    <svg viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                        <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01" />
                    </svg>
                </div>
                <div class="title bold">{{ __('dashboard.reservation') }}</div>
            </div>
            </a>

            <a href="{{ route('dashboard.unit_status.index') }}" style="text-decoration: none;">
            <div class="setting-card">
                <div class="icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                    </svg>
                </div>
                <div class="title bold">{{ __('dashboard.unit_status') }}</div>
            </div>
        </a>

        <a href="{{ route('dashboard.housekeeping_status.index') }}" style="text-decoration: none;">
            <div class="setting-card">
                <div class="icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M20 7h-3a2 2 0 01-2-2V2" />
                        <path d="M9 22v-4h6v4" />
                        <path d="M8 6h.01" />
                        <path d="M16 6h.01" />
                        <path d="M12 6h.01" />
                        <path d="M12 10h.01" />
                        <path d="M12 14h.01" />
                        <path d="M16 10h.01" />
                        <path d="M16 14h.01" />
                        <path d="M8 10h.01" />
                        <path d="M8 14h.01" />
                        <rect width="16" height="20" x="4" y="2" rx="2" />
                    </svg>
                </div>
                <div class="title bold">{{ __('dashboard.housekeeping') }}</div>
            </div>
        </a>

        <a href="{{ route('dashboard.receipt.index') }}" style="text-decoration: none;">
            <div class="setting-card">
                <div class="icon">
                    <svg viewBox="0 0 24 24">
                        <rect x="2" y="5" width="20" height="14" rx="2" />
                        <line x1="2" y1="10" x2="22" y2="10" />
                    </svg>
                </div>
                <div class="title bold">{{ __('dashboard.vouchers') }}</div>
            </div>
        </a>

        <a href="{{ route('dashboard.outlet_property.index') }}" style="text-decoration: none;">
            <!-- Row 2 -->
            <div class="setting-card">
                <div class="icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        <circle cx="12" cy="13" r="2" />
                    </svg>
                </div>
                <div class="title bold">{{ __('dashboard.outlets') }}</div>
            </div>
        </a>

        <a href="{{ route('dashboard.guest.index') }}" style="text-decoration: none;">
            <div class="setting-card">
                <div class="icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 00-3-3.87" />
                        <path d="M16 3.13a4 4 0 010 7.75" />
                    </svg>
                </div>
                <div class="title bold">{{ __('dashboard.customers') }}</div>
            </div>
        </a>

        <a href="#" style="text-decoration: none;">
            <div class="setting-card">
                <div class="icon">
                    <svg viewBox="0 0 24 24">
                        <rect x="2" y="2" width="20" height="8" rx="2" ry="2" />
                        <rect x="2" y="14" width="20" height="8" rx="2" ry="2" />
                        <line x1="6" y1="6" x2="6.01" y2="6" />
                        <line x1="6" y1="18" x2="6.01" y2="18" />
                    </svg>
                </div>
                <div class="title bold">{{ __('dashboard.sms') }}</div>
            </div>
        </a>

        <a href="{{ route('dashboard.cash_drawer.index') }}" style="text-decoration: none;">
            <div class="setting-card">
                <div class="icon">
                    <svg viewBox="0 0 24 24">
                        <line x1="12" y1="1" x2="12" y2="23" />
                        <path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" />
                    </svg>
                </div>
                <div class="title bold">{{ __('dashboard.cash_drawer_balance') }}</div>
            </div>
        </a>

        <a href="{{ route('dashboard.reports.index') }}" style="text-decoration: none;">
            <!-- Row 3 -->
            <div class="setting-card">
                <div class="icon">
                    <svg viewBox="0 0 24 24">
                        <line x1="18" y1="20" x2="18" y2="10" />
                        <line x1="12" y1="20" x2="12" y2="4" />
                        <line x1="6" y1="20" x2="6" y2="14" />
                    </svg>
                </div>
                <div class="title bold">{{ __('dashboard.reports') }}</div>
            </div>
        </a>

        <a href="#" style="text-decoration: none;">
            <div class="setting-card">
                <div class="icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                        <polyline points="10 9 9 9 8 9" />
                    </svg>
                </div>
                <div class="title bold">{{ __('dashboard.logs') }}</div>
            </div>
        </a>

        <a href="{{ route('dashboard.night_audit.index') }}" style="text-decoration: none;">
            <div class="setting-card">
                <div class="icon">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                </div>
                <div class="title bold">{{ __('dashboard.night_audit') }}</div>
            </div>
        </a>

        <a href="{{ route('dashboard.online_reservation.index') }}" style="text-decoration: none;">
            <div class="setting-card">
                <div class="icon">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="2" y1="12" x2="22" y2="12" />
                        <path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z" />
                    </svg>
                </div>
                <div class="title bold">{{ __('dashboard.online_reservation') }}</div>
            </div>
        </a>
        </div>
    </div>
@endsection