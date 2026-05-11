@include('admin.online_reservation.live')
{{--
@extends('layouts.app')

@php
    $theme = \App\Models\ThemeCustomization::getTheme();
    $pendingCount = 2;
    $acceptedCount = 1;
    $declinedCount = 1;
    $queueValue = 2000;
    $channelCount = 3;
@endphp

@section('title', 'Online Reservation')
<style>
    .parent-Contact {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .contact-number.style-number {
        color: #333;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .contact-number.background-icon,
    .contact-number.u-cursor-pointer {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    /* Page Header */
    .page-category {
        font-size: 0.875rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .page-header__title {
        font-size: 1.75rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .page-header__subtitle {
        font-size: 1rem;
        color: #6c757d;
    }

    /* Table Top Buttons */
    .n-table__top-btns {
        display: flex;
        gap: 0.75rem;
    }

    .n-button {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
    }

    .n-button--primary {
        background-color: white;
        color: #333;
        border-color: #dee2e6;
    }

    .n-button--primary:hover {
        background-color: #f8f9fa;
        border-color: #4a90e2;
    }

    .n-button--green {
        background-color: #2335da;
        color: white;
        border-color: #190cd8;
    }

    .n-button--green:hover {
        background-color: #3759f1;
        border-color: #292ce9;
    }

    /* Filter Form */
    .filter-form__container {
        background-color: #343a40;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .filter-form {
        padding: 1.5rem;
    }

    .filter-form--dark label {
        color: #e9ecef;
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.875rem;
    }

    .filter-form--dark .form-control {
        background-color: #495057;
        border: 1px solid #6c757d;
        color: white;
        width: 100%;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }

    .filter-form--dark .form-control::placeholder {
        color: #adb5bd;
    }

    .form__input-msg {
        font-size: 0.75rem;
        margin-top: 0.25rem;
        min-height: 1rem;
        color: #6c757d;
    }

    /* Overlay hidden by default */
    .unit-card .card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        /* semi-transparent overlay */
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
        z-index: 10;
    }

    /* Show overlay on hover */
    .unit-card:hover .card-overlay {
        opacity: 1;
    }

    /* Style buttons */
    .unit-card .card-overlay .btn {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .unit-card .card-overlay .btn i {
        font-size: 16px;
    }

    .online-shell {
        padding: 1.5rem;
        border-radius: 24px;
        background:
            radial-gradient(circle at top left, rgba(255, 255, 255, 0.95), rgba(246, 248, 252, 0.92)),
            linear-gradient(180deg, rgba(255, 255, 255, 0.95), rgba(242, 245, 250, 0.9));
    }

    .online-hero {
        display: grid;
        grid-template-columns: minmax(0, 1.6fr) minmax(280px, 1fr);
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .online-hero__panel {
        padding: 1.6rem;
        border-radius: 24px;
        color: #fff;
        background: linear-gradient(135deg, {{ $theme->button_primary_color }} 0%, {{ $theme->topbar_bg_color }} 100%);
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.14);
    }

    .online-hero__panel .page-category {
        color: rgba(255, 255, 255, 0.72);
        margin-bottom: 0.4rem;
    }

    .online-hero__title {
        margin: 0;
        font-size: clamp(1.8rem, 2vw, 2.4rem);
        font-weight: 700;
    }

    .online-hero__copy {
        margin: 0.8rem 0 0;
        max-width: 42rem;
        color: rgba(255, 255, 255, 0.86);
        line-height: 1.65;
    }

    .online-hero__pills {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
        margin-top: 1.1rem;
    }

    .online-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.5rem 0.85rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.16);
        color: #fff;
        font-size: 0.88rem;
        font-weight: 500;
    }

    .online-sidecard {
        padding: 1.2rem;
        border-radius: 24px;
        background: {{ $theme->card_bg_color }};
        border: 1px solid {{ $theme->card_border_color }};
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    }

    .online-sidecard h3 {
        color: {{ $theme->dashboard_card_title_color }};
        font-size: 1.05rem;
        font-weight: 700;
        margin-bottom: 0.3rem;
    }

    .online-sidecard p {
        color: {{ $theme->dashboard_card_text_color }};
        margin-bottom: 1rem;
    }

    .channel-health {
        display: grid;
        gap: 0.7rem;
    }

    .channel-health__item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.8rem 0.95rem;
        border-radius: 16px;
        background: #f7f8fb;
        border: 1px solid rgba(15, 23, 42, 0.05);
    }

    .channel-health__name {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        color: {{ $theme->dashboard_card_title_color }};
        font-weight: 600;
    }

    .channel-health__dot {
        width: 11px;
        height: 11px;
        border-radius: 999px;
        box-shadow: 0 0 0 5px rgba(15, 23, 42, 0.05);
    }

    .channel-health__meta {
        color: #64748b;
        font-size: 0.85rem;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .summary-card {
        padding: 1rem;
        border-radius: 20px;
        background: {{ $theme->card_bg_color }};
        border: 1px solid {{ $theme->card_border_color }};
        box-shadow: 0 10px 26px rgba(15, 23, 42, 0.05);
    }

    .summary-card__top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.7rem;
    }

    .summary-card__label {
        color: #64748b;
        font-size: 0.88rem;
    }

    .summary-card__value {
        color: {{ $theme->dashboard_card_title_color }};
        font-size: 1.45rem;
        font-weight: 700;
        line-height: 1.1;
    }

    .summary-card__meta {
        color: {{ $theme->dashboard_card_text_color }};
        font-size: 0.85rem;
    }

    .summary-card__icon {
        width: 46px;
        height: 46px;
        border-radius: 15px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.15rem;
    }

    .summary-card__icon--pending {
        background: linear-gradient(135deg, #d89114, #f3b64d);
    }

    .summary-card__icon--accepted {
        background: linear-gradient(135deg, #1f9d62, #4ac28a);
    }

    .summary-card__icon--value {
        background: linear-gradient(135deg, #1f5bd8, #5c8cf5);
    }

    .summary-card__icon--channel {
        background: linear-gradient(135deg, #247ba0, #61acc9);
    }

    .action-surface {
        padding: 1rem;
        border-radius: 22px;
        background: {{ $theme->card_bg_color }};
        border: 1px solid {{ $theme->card_border_color }};
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.05);
        margin-bottom: 1.25rem;
    }

    .action-surface__head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .action-surface__title {
        color: {{ $theme->dashboard_card_title_color }};
        font-size: 1.05rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .action-surface__copy {
        color: {{ $theme->dashboard_card_text_color }};
        margin: 0;
    }

    .channel-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
        margin-bottom: 1rem;
    }

    .channel-tag {
        padding: 0.42rem 0.78rem;
        border-radius: 999px;
        background: #f4f7fb;
        border: 1px solid rgba(15, 23, 42, 0.06);
        color: #475569;
        font-size: 0.84rem;
        font-weight: 600;
    }

    .filter-form__container {
        background: linear-gradient(180deg, #f7f9fc 0%, #edf2f8 100%);
        border: 1px solid rgba(15, 23, 42, 0.07);
        box-shadow: none;
        display: none;
    }

    .filter-form__container.is-open {
        display: block;
    }

    .request-board {
        border-radius: 24px;
        overflow: hidden;
        background: {{ $theme->card_bg_color }};
        border: 1px solid {{ $theme->card_border_color }};
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.06);
    }

    .request-board .nav-tabs {
        gap: 0.75rem;
        padding: 1rem 1rem 0;
        border-bottom: 1px solid rgba(15, 23, 42, 0.06);
    }

    .request-board .nav-link {
        border: none;
        border-radius: 18px;
        padding: 0.85rem 1rem;
        color: #64748b;
        background: transparent;
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }

    .request-board .nav-link.active {
        color: {{ $theme->dashboard_card_title_color }};
        background: #f4f7fb;
        box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.05);
    }

    .tab-count {
        min-width: 34px;
        height: 34px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(15, 23, 42, 0.08);
        font-weight: 700;
    }

    .tab-copy {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        line-height: 1.15;
    }

    .tab-copy small {
        margin-top: 0.15rem;
        color: #94a3b8;
        font-size: 0.76rem;
    }

    .request-board .card-body {
        padding: 1rem;
    }

    .request-table thead th {
        background: #f7f8fb;
        color: #64748b;
        border: none;
        font-size: 0.82rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        white-space: nowrap;
    }

    .request-table tbody td {
        padding-top: 0.95rem;
        padding-bottom: 0.95rem;
        border-color: rgba(15, 23, 42, 0.06);
    }

    .request-guest {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .request-guest__avatar {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.16), rgba(14, 116, 144, 0.08));
        color: #1d4ed8;
        font-weight: 700;
    }

    .request-guest__name {
        color: {{ $theme->dashboard_card_title_color }};
        font-weight: 700;
    }

    .request-guest__sub {
        color: #94a3b8;
        font-size: 0.84rem;
    }

    .ota-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.42rem 0.72rem;
        border-radius: 999px;
        background: #f8fafc;
        border: 1px solid rgba(15, 23, 42, 0.06);
        color: #334155;
        font-weight: 600;
    }

    .ota-chip::before {
        content: '';
        width: 9px;
        height: 9px;
        border-radius: 999px;
        background: var(--ota-color, #94a3b8);
    }

    .status-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.45rem 0.7rem;
        border-radius: 999px;
        font-size: 0.84rem;
        font-weight: 700;
    }

    .status-chip::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: currentColor;
    }

    .status-chip--pending {
        color: #d89000;
        background: rgba(216, 144, 0, 0.12);
    }

    .status-chip--accepted {
        color: #1f9d62;
        background: rgba(31, 157, 98, 0.12);
    }

    .status-chip--declined {
        color: #d64545;
        background: rgba(214, 69, 69, 0.12);
    }

    .table-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.45rem;
        flex-wrap: wrap;
    }

    .table-actions .btn {
        border-radius: 12px;
        font-weight: 600;
    }

    @media (max-width: 1199px) {
        .online-hero,
        .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 991px) {
        .online-hero,
        .summary-grid {
            grid-template-columns: 1fr;
        }

        .action-surface__head {
            flex-direction: column;
        }
    }

    @media (max-width: 767px) {
        .online-shell {
            padding: 1rem;
        }

        .request-board .nav-link {
            width: 100%;
        }

        .table-actions {
            justify-content: flex-start;
        }
    }
</style>
@section('content')
    <main class="online-shell">
        <section class="online-hero">
            <div class="online-hero__panel">
                <div class="page-category">{{ __('dashboard.online_reservation') }}</div>
                <h2 class="online-hero__title">{{ __('dashboard.manage_external_reservation') }}</h2>
                <p class="online-hero__copy">
                    Review OTA demand, keep channel traffic visible, and move online requests through approval with a
                    cleaner day-to-day workspace.
                </p>

                <div class="online-hero__pills">
                    <span class="online-pill">
                        <i class="bi bi-lightning-charge"></i>
                        {{ $pendingCount }} requests need attention
                    </span>
                    <span class="online-pill">
                        <i class="bi bi-arrow-repeat"></i>
                        Last sync 5 min ago
                    </span>
                    <span class="online-pill">
                        <i class="bi bi-cash-coin"></i>
                        Queue value SAR {{ number_format($queueValue) }}
                    </span>
                </div>
            </div>

            <aside class="online-sidecard">
                <h3>Channel snapshot</h3>
                <p>High-traffic sources currently feeding online reservations.</p>

                <div class="channel-health">
                    <div class="channel-health__item">
                        <div class="channel-health__name">
                            <span class="channel-health__dot" style="background:#1e88e5;"></span>
                            Booking.com
                        </div>
                        <span class="channel-health__meta">2 requests</span>
                    </div>
                    <div class="channel-health__item">
                        <div class="channel-health__name">
                            <span class="channel-health__dot" style="background:#d81b60;"></span>
                            Agoda
                        </div>
                        <span class="channel-health__meta">1 request</span>
                    </div>
                    <div class="channel-health__item">
                        <div class="channel-health__name">
                            <span class="channel-health__dot" style="background:#5e35b1;"></span>
                            Expedia
                        </div>
                        <span class="channel-health__meta">1 request</span>
                    </div>
                </div>
            </aside>
        </section>

        <section class="summary-grid">
            <article class="summary-card">
                <div class="summary-card__top">
                    <div>
                        <div class="summary-card__label">Pending queue</div>
                        <div class="summary-card__value">{{ $pendingCount }}</div>
                    </div>
                    <span class="summary-card__icon summary-card__icon--pending">
                        <i class="bi bi-hourglass-split"></i>
                    </span>
                </div>
                <div class="summary-card__meta">Requests waiting for quick review.</div>
            </article>

            <article class="summary-card">
                <div class="summary-card__top">
                    <div>
                        <div class="summary-card__label">Accepted today</div>
                        <div class="summary-card__value">{{ $acceptedCount }}</div>
                    </div>
                    <span class="summary-card__icon summary-card__icon--accepted">
                        <i class="bi bi-check2-circle"></i>
                    </span>
                </div>
                <div class="summary-card__meta">Approved and ready for follow-up.</div>
            </article>

            <article class="summary-card">
                <div class="summary-card__top">
                    <div>
                        <div class="summary-card__label">Queue revenue</div>
                        <div class="summary-card__value">SAR {{ number_format($queueValue) }}</div>
                    </div>
                    <span class="summary-card__icon summary-card__icon--value">
                        <i class="bi bi-cash-stack"></i>
                    </span>
                </div>
                <div class="summary-card__meta">Pending value across active requests.</div>
            </article>

            <article class="summary-card">
                <div class="summary-card__top">
                    <div>
                        <div class="summary-card__label">Active channels</div>
                        <div class="summary-card__value">{{ $channelCount }}</div>
                    </div>
                    <span class="summary-card__icon summary-card__icon--channel">
                        <i class="bi bi-broadcast"></i>
                    </span>
                </div>
                <div class="summary-card__meta">Connected sources pushing reservations.</div>
            </article>
        </section>

        <section class="action-surface">
            <div class="action-surface__head">
                <div>
                    <div class="action-surface__title">Request workspace</div>
                    <p class="action-surface__copy">
                        Filter by guest, channel, or stay window, then process new, accepted, and declined requests
                        from one place.
                    </p>
                </div>

                <button class="n-button n-button--primary" type="button" id="filterToggleBtn">
                    <i class="bi bi-funnel"></i>
                    {{ __('dashboard.filter') }}
                </button>
            </div>

            <div class="channel-tags">
                <span class="channel-tag">Booking.com</span>
                <span class="channel-tag">Agoda</span>
                <span class="channel-tag">Expedia</span>
                <span class="channel-tag">Direct sync preview</span>
            </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('dashboard.online_reservation.index') }}">
            <div class="filter-form__container mb-0 {{ request()->hasAny(['guest', 'channel', 'arrival_from', 'arrival_to']) ? 'is-open' : '' }}">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">{{ __('dashboard.guest') }}</label>
                                <input type="text" name="guest" value="{{ request('guest') }}" class="form-control"
                                    placeholder="Search guest name">
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">Channel</label>
                                <select name="channel" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    <option value="Booking.com" {{ request('channel') === 'Booking.com' ? 'selected' : '' }}>Booking.com</option>
                                    <option value="Agoda" {{ request('channel') === 'Agoda' ? 'selected' : '' }}>Agoda</option>
                                    <option value="Expedia" {{ request('channel') === 'Expedia' ? 'selected' : '' }}>Expedia</option>
                                </select>
                            </div>

                            <div class="col-lg-2 col-md-6">
                                <label class="form-label">{{ __('dashboard.from') }}</label>
                                <input type="date" name="arrival_from" value="{{ request('arrival_from') }}" class="form-control">
                            </div>

                            <div class="col-lg-2 col-md-6">
                                <label class="form-label">{{ __('dashboard.to') }}</label>
                                <input type="date" name="arrival_to" value="{{ request('arrival_to') }}" class="form-control">
                            </div>

                            <div class="col-lg-2 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary w-100">{{ __('dashboard.search') }}</button>
                                <a href="{{ route('dashboard.online_reservation.index') }}" class="btn btn-outline-secondary w-100">
                                    {{ __('dashboard.reset') }}
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
        </section>
        <section class="request-board">
        <div class="card-body">

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-0" id="reservationTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab"
                        data-bs-target="#newRequests" type="button">
                        <span class="tab-count">{{ $pendingCount }}</span>
                        <span class="tab-copy">
                            <span>{{ __('dashboard.new_online_reservation_request') }}</span>
                            <small>Fresh requests waiting in queue</small>
                        </span>
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab"
                        data-bs-target="#acceptedRequests" type="button">
                        <span class="tab-count">{{ $acceptedCount }}</span>
                        <span class="tab-copy">
                            <span>{{ __('dashboard.new_online_accepted_requests') }}</span>
                            <small>Approved and ready to convert</small>
                        </span>
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#declinedRequests"
                        type="button">
                        <span class="tab-count">{{ $declinedCount }}</span>
                        <span class="tab-copy">
                            <span>{{ __('dashboard.new_declined_request') }}</span>
                            <small>Requests the team could not accept</small>
                        </span>
                    </button>
                </li>
            </ul>

            <div class="tab-content">

                <!-- NEW REQUESTS -->
                <div class="tab-pane fade show active" id="newRequests">

                    <div class="table-responsive">
                        <table class="table table-hover align-middle request-table">
                            <thead>
                                <tr>
                                    <th>{{ __('dashboard.res_no') }}</th>
                                    <th>{{ __('dashboard.guest') }}</th>
                                    <th>{{ __('dashboard.from') }} / {{ __('dashboard.to') }}</th>
                                    <th>{{ __('dashboard.nights') }}</th>
                                    <th>OTA</th>
                                    <th>{{ __('dashboard.amount') }}</th>
                                    <th>Status</th>
                                    <th class="text-end">{{ __('dashboard.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr>
                                    <td class="fw-bold text-primary">#OTA-2001</td>
                                    <td>
                                        <div class="request-guest">
                                            <span class="request-guest__avatar">AA</span>
                                            <div>
                                                <div class="request-guest__name">Ahmed Ali</div>
                                                <div class="request-guest__sub">External booking request</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="request-guest__name">12 Feb 2026</div>
                                        <div class="request-guest__sub">15 Feb 2026</div>
                                    </td>
                                    <td>3</td>
                                    <td><span class="ota-chip" style="--ota-color:#1e88e5;">Booking.com</span></td>
                                    <td>
                                        <div class="request-guest__name">SAR 1,200</div>
                                        <div class="request-guest__sub">Estimated booking value</div>
                                    </td>
                                    <td><span class="status-chip status-chip--pending">Priority review</span></td>
                                    <td>
                                        <div class="table-actions">
                                            <button type="button" class="btn btn-success btn-sm">Accept</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm">Review</button>
                                            <button type="button" class="btn btn-danger btn-sm">Decline</button>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="fw-bold text-primary">#OTA-2002</td>
                                    <td>
                                        <div class="request-guest">
                                            <span class="request-guest__avatar">SK</span>
                                            <div>
                                                <div class="request-guest__name">Sarah Khan</div>
                                                <div class="request-guest__sub">Awaiting approval</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="request-guest__name">18 Feb 2026</div>
                                        <div class="request-guest__sub">20 Feb 2026</div>
                                    </td>
                                    <td>2</td>
                                    <td><span class="ota-chip" style="--ota-color:#d81b60;">Agoda</span></td>
                                    <td>
                                        <div class="request-guest__name">SAR 800</div>
                                        <div class="request-guest__sub">Estimated booking value</div>
                                    </td>
                                    <td><span class="status-chip status-chip--pending">Awaiting approval</span></td>
                                    <td>
                                        <div class="table-actions">
                                            <button type="button" class="btn btn-success btn-sm">Accept</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm">Review</button>
                                            <button type="button" class="btn btn-danger btn-sm">Decline</button>
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>


                <!-- ACCEPTED REQUESTS -->
                <div class="tab-pane fade" id="acceptedRequests">

                    <div class="table-responsive">
                        <table class="table table-hover align-middle request-table">
                            <thead>
                                <tr>
                                    <th>{{ __('dashboard.res_no') }}</th>
                                    <th>{{ __('dashboard.guest') }}</th>
                                    <th>{{ __('dashboard.from') }} / {{ __('dashboard.to') }}</th>
                                    <th>{{ __('dashboard.nights') }}</th>
                                    <th>OTA</th>
                                    <th>{{ __('dashboard.amount') }}</th>
                                    <th>Status</th>
                                    <th class="text-end">{{ __('dashboard.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr>
                                    <td class="fw-bold text-primary">#OTA-1998</td>
                                    <td>
                                        <div class="request-guest">
                                            <span class="request-guest__avatar">MF</span>
                                            <div>
                                                <div class="request-guest__name">Mohammed Faisal</div>
                                                <div class="request-guest__sub">Approved by reservation team</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="request-guest__name">05 Feb 2026</div>
                                        <div class="request-guest__sub">07 Feb 2026</div>
                                    </td>
                                    <td>2</td>
                                    <td><span class="ota-chip" style="--ota-color:#5e35b1;">Expedia</span></td>
                                    <td>
                                        <div class="request-guest__name">SAR 950</div>
                                        <div class="request-guest__sub">Accepted channel revenue</div>
                                    </td>
                                    <td><span class="status-chip status-chip--accepted">Accepted</span></td>
                                    <td>
                                        <div class="table-actions">
                                            <button type="button" class="btn btn-outline-secondary btn-sm">View</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm">Open stay</button>
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>


                <!-- DECLINED REQUESTS -->
                <div class="tab-pane fade" id="declinedRequests">

                    <div class="table-responsive">
                        <table class="table table-hover align-middle request-table">
                            <thead>
                                <tr>
                                    <th>{{ __('dashboard.res_no') }}</th>
                                    <th>{{ __('dashboard.guest') }}</th>
                                    <th>{{ __('dashboard.from') }} / {{ __('dashboard.to') }}</th>
                                    <th>{{ __('dashboard.nights') }}</th>
                                    <th>OTA</th>
                                    <th>{{ __('dashboard.amount') }}</th>
                                    <th>Status</th>
                                    <th class="text-end">{{ __('dashboard.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr>
                                    <td class="fw-bold text-primary">#OTA-1995</td>
                                    <td>
                                        <div class="request-guest">
                                            <span class="request-guest__avatar">AH</span>
                                            <div>
                                                <div class="request-guest__name">Ali Hassan</div>
                                                <div class="request-guest__sub">Unable to match inventory</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="request-guest__name">01 Feb 2026</div>
                                        <div class="request-guest__sub">03 Feb 2026</div>
                                    </td>
                                    <td>2</td>
                                    <td><span class="ota-chip" style="--ota-color:#1e88e5;">Booking.com</span></td>
                                    <td>
                                        <div class="request-guest__name">SAR 700</div>
                                        <div class="request-guest__sub">Lost opportunity value</div>
                                    </td>
                                    <td><span class="status-chip status-chip--declined">Declined</span></td>
                                    <td>
                                        <div class="table-actions">
                                            <button type="button" class="btn btn-outline-secondary btn-sm">View</button>
                                            <button type="button" class="btn btn-outline-dark btn-sm">Retry</button>
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
        </section>






    </main>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('filterToggleBtn');
            const filterContainer = document.querySelector('.filter-form__container');

            if (!toggleBtn || !filterContainer) {
                return;
            }

            toggleBtn.addEventListener('click', function() {
                filterContainer.classList.toggle('is-open');
            });
        });
    </script>
@endpush
--}}
