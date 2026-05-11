@extends('layouts.app')

@section('title', __('dashboard.edit_reservation'))

<style>
    :root {
        --primary-color: #6366f1;
        --secondary-color: #8b5cf6;
    }

    .page-category {
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 5px;
    }

    .page-header__title {
        font-size: 24px;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .page-header__subtitle {
        font-size: 14px;
        color: #6c757d;
        margin-top: 5px;
    }

    .page-header {
        margin-bottom: 20px;
    }

    .res-header {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .res-card {
        background: #ffffff;
        border-radius: 8px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        margin-bottom: 20px;
    }

    .res-card__title {
        font-size: 18px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 15px;
        margin-top: 0;
        padding-bottom: 10px;
        border-bottom: 1px solid #e2e8f0;
    }

    .stepper-wrapper {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
        padding: 0 20px;
    }

    .stepper-item {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
    }

    .stepper-item::before {
        content: '';
        position: absolute;
        top: 20px;
        left: -50%;
        width: 100%;
        height: 2px;
        background: #e2e8f0;
        z-index: 0;
    }

    .stepper-item:first-child::before {
        display: none;
    }

    .stepper-counter {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #ffffff;
        border: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        z-index: 1;
        margin-bottom: 8px;
    }

    .stepper-item.active .stepper-counter {
        background: #6366f1;
        color: white;
        border-color: #6366f1;
    }

    .stepper-item.completed .stepper-counter {
        background: #28a745;
        color: white;
        border-color: #28a745;
    }

    .stepper-title {
        font-size: 12px;
        color: #6c757d;
    }

    .stepper-item.active .stepper-title {
        color: #6366f1;
        font-weight: bold;
    }

    .form-label-custom {
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        margin-bottom: 5px;
    }

    .section-title {
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 1px solid #e2e8f0;
    }

    .unit-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        gap: 10px;
    }

    .unit-item {
        padding: 10px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .unit-item:hover {
        border-color: var(--primary-color);
        background: #f8f9fa;
    }

    .unit-item.selected {
        border-color: var(--primary-color);
        background: var(--primary-color);
        color: white;
    }

    .unit-item.occupied {
        background: #fee2e2;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .guest-list-item {
        padding: 10px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        margin-bottom: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .guest-list-item:hover {
        border-color: var(--primary-color);
    }

    .guest-list-item.selected {
        border-color: var(--primary-color);
        background: #f8f9ff;
    }

    .financial-summary {
        background: #ffffff;
        border-radius: 8px;
        padding: 15px;
        border: 1px solid #e2e8f0;
    }

    .financial-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .financial-row:last-child {
        border-bottom: none;
        font-weight: bold;
        font-size: 16px;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        padding: 20px;
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        border-radius: 0 0 8px 8px;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 500;
    }

    .status-available {
        background: #d4edda;
        color: #155724;
    }

    .status-occupied {
        background: #f8d7da;
        color: #721c24;
    }

    .status-reserved {
        background: #fff3cd;
        color: #856404;
    }

    .marquee-container {
        background: #1a73e8;
        color: white;
        padding: 8px 0;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .marquee-text {
        white-space: nowrap;
        overflow: hidden;
    }

    .marquee-text span {
        display: inline-block;
        padding-left: 100%;
        animation: marquee 20s linear infinite;
    }

    @keyframes marquee {
        0% {
            transform: translate(0, 0);
        }

        100% {
            transform: translate(-100%, 0);
        }
    }

    .info-icon {
        color: #6c757d;
        cursor: pointer;
        font-size: 12px;
    }

    .tab-buttons {
        display: flex;
        gap: 5px;
        margin-bottom: 15px;
    }

    .tab-btn {
        padding: 8px 16px;
        border: 1px solid #dee2e6;
        background: white;
        border-radius: 4px;
        cursor: pointer;
    }

    .tab-btn.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    .guest-hub {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .guest-hub__top {
        display: grid;
        grid-template-columns: minmax(0, 1.8fr) minmax(240px, 0.9fr);
        gap: 16px;
        align-items: start;
    }

    .guest-hub__bottom {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .lookup-panel {
        position: relative;
        padding: 18px;
        border-radius: 18px;
        border: 1px solid #dbe4f0;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
    }

    .lookup-panel--primary {
        background: linear-gradient(180deg, rgba(99, 102, 241, 0.09) 0%, #ffffff 42%);
        border-color: rgba(99, 102, 241, 0.2);
    }

    .lookup-panel__header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }

    .lookup-panel__label {
        color: #0f172a;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 4px;
    }

    .lookup-panel__hint {
        color: #64748b;
        font-size: 13px;
        margin: 0;
    }

    .lookup-panel__search-wrap {
        position: relative;
    }

    .lookup-panel__input-group .form-control {
        border-radius: 14px 0 0 14px;
        border-color: #cbd5e1;
        min-height: 48px;
        box-shadow: none;
    }

    .lookup-panel__input-group .form-control:focus {
        border-color: rgba(99, 102, 241, 0.45);
    }

    .lookup-panel__input-group .btn {
        min-width: 52px;
        border-radius: 0 14px 14px 0;
        border-color: transparent;
    }

    .lookup-results {
        position: absolute;
        top: calc(100% + 10px);
        left: 0;
        right: 0;
        max-height: 260px;
        overflow-y: auto;
        padding: 8px;
        border-radius: 16px;
        border: 1px solid #dbe4f0;
        background: #ffffff;
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.14);
        z-index: 1000;
    }

    .lookup-results__state {
        padding: 14px 12px;
        text-align: center;
        font-size: 13px;
        color: #64748b;
    }

    .lookup-result-item {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border: 0;
        border-radius: 12px;
        background: #ffffff;
        text-align: left;
        transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .lookup-result-item:hover {
        background: #eef4ff;
        transform: translateY(-1px);
    }

    .lookup-result-item + .lookup-result-item {
        margin-top: 4px;
    }

    .lookup-result-item__icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: rgba(99, 102, 241, 0.12);
        color: var(--primary-color);
        flex-shrink: 0;
    }

    .lookup-result-item__icon--corporate {
        background: rgba(14, 165, 233, 0.14);
        color: #0284c7;
    }

    .lookup-result-item__icon--occupant {
        background: rgba(16, 185, 129, 0.14);
        color: #059669;
    }

    .lookup-result-item__content {
        min-width: 0;
        flex: 1;
    }

    .lookup-result-item__title {
        display: block;
        color: #0f172a;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.25;
    }

    .lookup-result-item__meta {
        display: block;
        color: #64748b;
        font-size: 12px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-top: 2px;
    }

    .lookup-result-item__cta {
        color: #94a3b8;
        flex-shrink: 0;
    }

    .quick-actions {
        display: grid;
        gap: 12px;
    }

    .quick-action-btn {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        border: 0;
        border-radius: 18px;
        color: #ffffff;
        font-size: 14px;
        font-weight: 600;
        text-align: left;
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.12);
    }

    .quick-action-btn i {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.2);
        font-size: 18px;
        flex-shrink: 0;
    }

    .quick-action-btn--guest {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .quick-action-btn--corporate {
        background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%);
    }

    .selection-card {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-top: 14px;
        padding: 16px;
        border-radius: 16px;
        border: 1px solid #dbe4f0;
        background: #ffffff;
    }

    .selection-card--guest {
        background: linear-gradient(180deg, rgba(99, 102, 241, 0.08) 0%, #ffffff 100%);
    }

    .selection-card--corporate {
        background: linear-gradient(180deg, rgba(16, 185, 129, 0.08) 0%, #ffffff 100%);
    }

    .selection-card__icon {
        width: 46px;
        height: 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        background: rgba(99, 102, 241, 0.14);
        color: var(--primary-color);
        font-size: 20px;
        flex-shrink: 0;
    }

    .selection-card--corporate .selection-card__icon {
        background: rgba(16, 185, 129, 0.14);
        color: #059669;
    }

    .selection-card__body {
        min-width: 0;
        flex: 1;
    }

    .selection-card__title {
        color: #0f172a;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.3;
        margin-bottom: 6px;
    }

    .selection-card__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px 14px;
        color: #475569;
        font-size: 12px;
    }

    .selection-card__meta span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .selection-card__remove {
        flex-shrink: 0;
    }

    .occupant-count-badge {
        min-width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 10px;
        border-radius: 999px;
        background: rgba(99, 102, 241, 0.12);
        color: var(--primary-color);
        font-size: 13px;
        font-weight: 700;
    }

    .occupant-stack {
        display: grid;
        gap: 12px;
    }

    .occupant-card {
        padding: 14px;
        border-radius: 16px;
        border: 1px solid #dbe4f0;
        background: #ffffff;
    }

    .occupant-card__top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
    }

    .occupant-card__identity {
        min-width: 0;
        display: flex;
        gap: 12px;
    }

    .occupant-card__icon {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: rgba(16, 185, 129, 0.14);
        color: #059669;
        font-size: 18px;
        flex-shrink: 0;
    }

    .occupant-card__name {
        color: #0f172a;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.3;
    }

    .occupant-card__meta {
        color: #64748b;
        font-size: 12px;
        margin-top: 3px;
    }

    .occupant-card__field {
        margin-top: 12px;
    }

    .empty-state {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 14px;
        padding: 16px;
        border-radius: 16px;
        border: 1px dashed #cbd5e1;
        background: #f8fafc;
        color: #64748b;
        font-size: 13px;
    }

    .empty-state__icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #ffffff;
        color: #94a3b8;
        flex-shrink: 0;
    }

    @media (max-width: 991.98px) {
        .guest-hub__top,
        .guest-hub__bottom {
            grid-template-columns: 1fr;
        }

        .quick-actions {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575.98px) {
        .quick-actions {
            grid-template-columns: 1fr;
        }

        .selection-card {
            flex-wrap: wrap;
        }
    }
</style>

@section('content')
    <main class="u-white-bg bg-white p-2" style="border-radius:10px;">
        <div class="container-fluid mt-4">

            <!-- Marquee -->
            <div class="marquee-container">
                <div class="marquee-text">
                    <span>{{ __('dashboard.welcome_message') }} | {{ __('dashboard.reservation_system') }} |
                        {{ now()->format('Y-m-d') }}</span>
                </div>
            </div>

            <!-- Header -->
            <div class="res-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">{{ __('dashboard.edit_reservation') }} - {{ $reservation->reservation_number }}</h4>
                    <small>{{ __('dashboard.reservation_no') }}: {{ $reservation->reservation_number }}</small>
                </div>
                {{-- <div class="text-end">
                    <small>{{ __('dashboard.reservation_no') }}:
                        <strong>{{ str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT) }}</strong></small>
                </div> --}}
            </div>

            <!-- Stepper -->
            <div class="stepper-wrapper">
                <div class="stepper-item active">
                    <div class="stepper-counter">1</div>
                    <div class="stepper-title">{{ __('dashboard.main_data') }}</div>
                </div>
                <div class="stepper-item">
                    <div class="stepper-counter">2</div>
                    <div class="stepper-title">{{ __('dashboard.select_unit') }}</div>
                </div>
                <div class="stepper-item">
                    <div class="stepper-counter">3</div>
                    <div class="stepper-title">{{ __('dashboard.guest_info') }}</div>
                </div>
                <div class="stepper-item">
                    <div class="stepper-counter">4</div>
                    <div class="stepper-title">{{ __('dashboard.financial') }}</div>
                </div>
                <div class="stepper-item">
                    <div class="stepper-counter">5</div>
                    <div class="stepper-title">{{ __('dashboard.confirmation') }}</div>
                </div>
            </div>

            <form id="reservationForm" method="POST"
                action="{{ route('dashboard.reservation.update', $reservation->id) }}">
                @csrf
                @method('PUT')

                <!-- Main Data Card -->
                <div class="res-card">
                    <h3 class="res-card__title">
                        <i class="bi bi-calendar-check me-2"></i>{{ __('dashboard.main_data') }}
                    </h3>
                    <div class="row g-3">

                        <!-- Check-in Date -->
                        <div class="col-md-3">
                            <label class="form-label-custom">
                                {{ __('dashboard.check_in') }} <span class="text-danger">*</span>
                                <i class="bi bi-info-circle info-icon"
                                    title="{{ __('dashboard.check_in_time') }}: {{ $settings->check_in_time }}"></i>
                            </label>
                            <input type="date" class="form-control" name="check_in_date" id="checkInDate"
                                value="{{ old('check_in_date', isset($reservation->check_in_date) ? $reservation->check_in_date->format('Y-m-d') : '') }}"
                                required>
                        </div>

                        <!-- Check-out Date -->
                        <div class="col-md-3">
                            <label class="form-label-custom">
                                {{ __('dashboard.check_out') }} <span class="text-danger">*</span>
                                <i class="bi bi-info-circle info-icon"
                                    title="{{ __('dashboard.check_out_time') }}: {{ $settings->check_out_time }}"></i>
                            </label>
                            <input type="date" class="form-control" name="check_out_date" id="checkOutDate"
                                value="{{ old('check_out_date', isset($reservation->check_out_date) ? $reservation->check_out_date->format('Y-m-d') : '') }}"
                                required>
                        </div>

                        <!-- Nights -->
                        <div class="col-md-2">
                            <label class="form-label-custom">{{ __('dashboard.nights') }}</label>
                            <input type="number" class="form-control" name="nights" id="nights"
                                value="{{ old('nights', $reservation->nights ?? 1) }}" readonly>
                        </div>

                        <!-- Adults -->
                        <div class="col-md-2">
                            <label class="form-label-custom">{{ __('dashboard.adults') }}</label>
                            <input type="number" class="form-control" name="adults" id="adults"
                                value="{{ old('adults', isset($reservation) ? $reservation->adults : 1) }}" min="1"
                                max="10">
                        </div>

                        <!-- Children -->
                        <div class="col-md-2">
                            <label class="form-label-custom">{{ __('dashboard.children') }}</label>
                            <input type="number" class="form-control" name="children" id="children"
                                value="{{ old('children', isset($reservation) ? $reservation->children : 0) }}"
                                min="0" max="10">
                        </div>


                        <!-- Reservation Source -->
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.reservation_source') }}</label>
                            <select class="form-select" name="source_id" id="reservationSource">
                                <option value="">{{ __('dashboard.select_source') }}</option>
                                @foreach ($reservationSources as $source)
                                    <option value="{{ $source->id }}"
                                        {{ old('source_id', $reservation->source_id ?? '') == $source->id ? 'selected' : '' }}>
                                        {{ $source->masterSource->name ?? $source->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Guest Class -->
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.guest_class') }}</label>
                            <select class="form-select" name="guest_class_id" id="guestClass">
                                <option value="">{{ __('dashboard.select_guest_class') }}</option>
                                @foreach ($guestClasses as $class)
                                    <option value="{{ $class->id }}"
                                        {{ old('guest_class_id', $reservation->guest_class_id ?? '') == $class->id ? 'selected' : '' }}>
                                        {{ $class->class_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Confirmation Status -->
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.status') }}</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_confirmed" value="1"
                                    id="isConfirmed"
                                    {{ old('is_confirmed', $reservation->is_confirmed ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="isConfirmed">
                                    {{ __('dashboard.confirmed') }}
                                </label>
                            </div>
                        </div>

                        <!-- Booking Date -->
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.booking_date') }}</label>
                            <input type="date" class="form-control" name="booking_date" id="bookingDate"
                                value="{{ old('booking_date', isset($reservation->booking_date) ? $reservation->booking_date : date('Y-m-d')) }}">
                        </div>

                        <!-- Notes -->
                        <div class="col-md-12">
                            <label class="form-label-custom">{{ __('dashboard.notes') }}</label>
                            <textarea class="form-control" name="notes" id="notes" rows="2"
                                placeholder="{{ __('dashboard.enter_notes') }}">{{ $reservation->notes }}</textarea>
                        </div>
                    </div>

                    <!-- Unit Selection Card -->
                    <div class="res-card mt-2">
                        <h3 class="res-card__title">
                            <i class="bi bi-door-open me-2"></i>{{ __('dashboard.unit_selection') }}
                        </h3>

                        <!-- Add Unit Button -->
                        <div class="mb-3">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#unitModal">
                                <i class="bi bi-plus-circle me-1"></i> {{ __('dashboard.add_unit') }}
                            </button>
                        </div>

                        <input type="hidden" name="unit_id" id="selectedUnitId"
                            value="{{ old('unit_id', $reservation->unit_id ?? '') }}">
                        <input type="hidden" name="unit_type_id" id="selectedUnitTypeId"
                            value="{{ old('unit_type_id', $reservation->unit->unit_type_id ?? '') }}">

                        <!-- Selected Unit Info -->
                        @php
                            $selectedUnitType = $unitTypes->firstWhere('id', $reservation->unit->unit_type_id ?? null);
                        @endphp
                        <div class="mt-3 p-3 bg-light rounded" id="selectedUnitInfo"
                            style="display: {{ $reservation->unit_id ? 'block' : 'none' }};">
                            <div class="row">
                                <div class="col-md-3">
                                    <small class="text-muted">{{ __('dashboard.unit') }}</small>
                                    <div class="fw-bold" id="displayUnitNumber">
                                        {{ $reservation->unit->unit_number ?? '' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">{{ __('dashboard.unit_type') }}</small>
                                    <div class="fw-bold" id="displayUnitType">
                                        {{ $selectedUnitType?->name ?? ($reservation->unit->unitType->name ?? '') }}</div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">{{ __('dashboard.floor') }}</small>
                                    <div class="fw-bold" id="displayFloor">{{ $reservation->unit->floor->name ?? '' }}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">{{ __('dashboard.block') }}</small>
                                    <div class="fw-bold" id="displayBlock">{{ $reservation->unit->block->name ?? '' }}
                                    </div>
                                </div>
                                {{-- <div class="col-md-3">
                                    <small class="text-muted">{{ __('dashboard.daily_rate') }}</small>
                                    <div class="fw-bold" id="displayDailyRate">0.00</div>
                                </div> --}}
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="clearUnitSelection()">
                                        <i class="bi bi-x-lg"></i> {{ __('dashboard.remove') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guest Selection Card -->
                <div class="res-card">
                    <h3 class="res-card__title">
                        <i class="bi bi-person-fill me-2"></i>{{ __('dashboard.guest_details') }}
                    </h3>

                    <div class="guest-hub">
                        <div class="guest-hub__top">
                            <div class="lookup-panel lookup-panel--primary">
                                <div class="lookup-panel__header">
                                    <div>
                                        <div class="lookup-panel__label">{{ __('dashboard.search_guest') }}</div>
                                        <p class="lookup-panel__hint">{{ __('dashboard.search_by_name_phone') }}</p>
                                    </div>
                                </div>
                                <div class="lookup-panel__search-wrap">
                                    <div class="input-group lookup-panel__input-group">
                                        <input type="text" class="form-control" id="guestSearch"
                                            placeholder="{{ __('dashboard.search_by_name_phone') }}">
                                        <button class="btn btn-primary" type="button" id="searchGuestBtn">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </div>
                                    <div id="guestSearchResults" class="lookup-results d-none"></div>
                                </div>

                                <div id="guestInfoDisplay"
                                    class="selection-card selection-card--guest {{ $reservation->guest_id ? '' : 'd-none' }}">
                                    <div class="selection-card__icon">
                                        <i class="bi bi-person-check"></i>
                                    </div>
                                    <div class="selection-card__body">
                                        <div class="selection-card__title" id="displayGuestName">
                                            {{ $reservation->guest?->full_name ?? '' }}
                                        </div>
                                        <div class="selection-card__meta">
                                            <span><i class="bi bi-phone"></i> <span
                                                    id="displayGuestMobile">{{ $reservation->guest?->mobile ?? '' }}</span></span>
                                            <span><i class="bi bi-envelope"></i> <span
                                                    id="displayGuestEmail">{{ $reservation->guest?->email ?? '' }}</span></span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger selection-card__remove"
                                        onclick="clearGuestInfo()">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="quick-actions">
                                <button class="quick-action-btn quick-action-btn--guest" type="button"
                                    data-bs-toggle="modal" data-bs-target="#newGuestModal">
                                    <i class="bi bi-person-plus"></i>
                                    <span>{{ __('dashboard.new_guest') }}</span>
                                </button>
                                <button class="quick-action-btn quick-action-btn--corporate" type="button"
                                    data-bs-toggle="modal" data-bs-target="#newCorporateModal">
                                    <i class="bi bi-building"></i>
                                    <span>{{ __('dashboard.new_corporate') }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Hidden Guest Form Fields -->
                        <input type="hidden" name="guest_first_name" id="guestFirstName" value="">
                        <input type="hidden" name="guest_last_name" id="guestLastName" value="">
                        <input type="hidden" name="guest_mobile" id="guestMobile" value="">
                        <input type="hidden" name="guest_email" id="guestEmail" value="">
                        <input type="hidden" name="guest_nationality" id="guestNationality" value="">
                        <input type="hidden" name="guest_id_type" id="guestIdType" value="">
                        <input type="hidden" name="guest_id_number" id="guestIdNumber" value="">
                        <input type="hidden" name="guest_gender" id="guestGender" value="">
                        <input type="hidden" name="guest_dob" id="guestDob" value="">
                        <input type="hidden" name="guest_address" id="guestAddress" value="">
                        <input type="hidden" name="guest_id" id="guestId"
                            value="{{ old('guest_id', $reservation->guest_id ?? '') }}">
                        <input type="hidden" name="corporate_id" id="corporateId"
                            value="{{ old('corporate_id', $reservation->corporate_id ?? '') }}">

                        <div class="guest-hub__bottom">
                            <div class="lookup-panel">
                                <div class="lookup-panel__header">
                                    <div>
                                        <div class="lookup-panel__label">{{ __('dashboard.additional_occupants') }}</div>
                                        <p class="lookup-panel__hint">{{ __('dashboard.search_additional_occupant') }}</p>
                                    </div>
                                    <span class="occupant-count-badge" id="occupantsCountBadge">0</span>
                                </div>
                                <div class="lookup-panel__search-wrap">
                                    <div class="input-group lookup-panel__input-group">
                                        <input type="text" class="form-control" id="occupantSearch"
                                            placeholder="{{ __('dashboard.search_additional_occupant') }}">
                                        <button class="btn btn-outline-primary" type="button" id="searchOccupantBtn">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </div>
                                    <div id="occupantSearchResults" class="lookup-results d-none"></div>
                                </div>
                                <div class="occupant-stack mt-3" id="occupantsList"></div>
                                <div id="occupantsEmptyState" class="empty-state">
                                    <div class="empty-state__icon">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div>{{ __('dashboard.no_additional_occupants') }}</div>
                                </div>
                                <div id="occupantsFields">
                                    @foreach (old('occupants', $reservation->reservationGuests
                                        ->where('is_primary', false)
                                        ->values()
                                        ->map(fn ($reservationGuest) => [
                                            'guest_id' => $reservationGuest->guest_id,
                                            'name' => $reservationGuest->guest?->full_name,
                                            'mobile' => $reservationGuest->guest?->mobile,
                                            'relationship' => $reservationGuest->relationship,
                                        ])
                                        ->all()) as $index => $occupant)
                                        <input type="hidden" name="occupants[{{ $index }}][guest_id]"
                                            value="{{ $occupant['guest_id'] ?? '' }}">
                                        <input type="hidden" name="occupants[{{ $index }}][relationship]"
                                            id="occupantRelationshipField_{{ $occupant['guest_id'] ?? $index }}"
                                            value="{{ $occupant['relationship'] ?? '' }}">
                                        <input type="hidden" name="occupants[{{ $index }}][name]"
                                            value="{{ $occupant['name'] ?? '' }}">
                                        <input type="hidden" name="occupants[{{ $index }}][mobile]"
                                            value="{{ $occupant['mobile'] ?? '' }}">
                                    @endforeach
                                </div>
                            </div>

                            <div class="lookup-panel">
                                <div class="lookup-panel__header">
                                    <div>
                                        <div class="lookup-panel__label">{{ __('dashboard.search_corporate') }}</div>
                                        <p class="lookup-panel__hint">{{ __('dashboard.search_by_corporate_name') }}</p>
                                    </div>
                                </div>
                                <div class="lookup-panel__search-wrap">
                                    <div class="input-group lookup-panel__input-group">
                                        <input type="text" class="form-control" id="corporateSearch"
                                            placeholder="{{ __('dashboard.search_by_corporate_name') }}">
                                        <button class="btn btn-info text-white" type="button" id="searchCorporateBtn">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </div>
                                    <div id="corporateSearchResults" class="lookup-results d-none"></div>
                                </div>

                                <div id="corporateInfoDisplay"
                                    class="selection-card selection-card--corporate {{ $reservation->corporate_id ? '' : 'd-none' }}">
                                    <div class="selection-card__icon">
                                        <i class="bi bi-buildings"></i>
                                    </div>
                                    <div class="selection-card__body">
                                        <div class="selection-card__title" id="displayCorporateName">
                                            {{ $reservation->corporate->name ?? '' }}
                                        </div>
                                        <div class="selection-card__meta">
                                            <span><i class="bi bi-percent"></i> <span id="displayCorporateDiscount">
                                                    @if ($reservation->corporate)
                                                        {{ $reservation->corporate->discount_value ?? 0 }}{{ $reservation->corporate->discount_type === 'percentage' ? '%' : '' }}
                                                        @lang('dashboard.discount')
                                                    @endif
                                                </span></span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger selection-card__remove"
                                        onclick="clearCorporateInfo()">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Card -->
                <div class="financial-card bg-white p-2">
                    <div class="card-header-custom">
                        <i class="bi bi-cash-stack me-2"></i>{{ __('dashboard.financial_summary') }}
                    </div>
                    <div class="card-body-custom">
                        <div class="row g-3">
                            <!-- Reservation Type -->
                            <div class="col-md-12">
                                <div class="section-title">{{ __('dashboard.reservation_type') }}</div>
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <label class="form-label-custom">{{ __('dashboard.reservation_type') }}</label>
                                        <select class="form-select" name="reservation_type" id="reservationType">
                                            <option value="daily"
                                                {{ old('reservation_type', $reservation->reservation_type ?? 'daily') === 'daily' ? 'selected' : '' }}>
                                                {{ __('dashboard.daily') }}
                                            </option>
                                            <option value="monthly"
                                                {{ old('reservation_type', $reservation->reservation_type ?? 'daily') === 'monthly' ? 'selected' : '' }}>
                                                {{ __('dashboard.monthly') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Rate Options (Common for both Daily and Monthly) -->
                            <div class="col-md-12" id="rateOptionsSection">
                                <div class="section-title">{{ __('dashboard.rate_options') }}</div>
                                <div class="row g-2">
                                    <div class="col-md-12">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="rateOption"
                                                id="rateOptionDaily" value="daily_rate" checked>
                                            <label class="form-check-label"
                                                for="rateOptionDaily">{{ __('dashboard.daily_rate') }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="rateOption"
                                                id="rateOptionPlan" value="rate_plan">
                                            <label class="form-check-label"
                                                for="rateOptionPlan">{{ __('dashboard.rate_plan') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Daily Rate Section -->
                            <div class="col-md-12" id="dailyRateSection">
                                <!-- Daily Rate Input -->
                                <div class="row g-2 mt-2" id="dailyRateInput">
                                    <div class="col-md-4">
                                        <label class="form-label-custom">{{ __('dashboard.daily_rate') }}</label>
                                        <input type="number" class="form-control" name="daily_rate" id="dailyRate"
                                            value="{{ old('daily_rate', $reservation->daily_rate ?? 0) }}"
                                            step="0.01" min="0">
                                    </div>
                                </div>

                                <!-- Rate Plan Dropdown -->
                                <div class="row g-2 mt-2 d-none" id="ratePlanSelect">
                                    <div class="col-md-4">
                                        <label class="form-label-custom">{{ __('dashboard.rate_plan') }}</label>
                                        <select class="form-select" name="rate_plan_id" id="ratePlanSelectInput">
                                            <option value="">{{ __('dashboard.select_rate_plan') }}</option>
                                            @foreach ($ratePlans as $plan)
                                                <option value="{{ $plan->id }}"
                                                    {{ old('rate_plan_id', $reservation->rate_plan_id ?? '') == $plan->id ? 'selected' : '' }}
                                                    data-daily-rate="{{ $plan->unitTypeRates->first()?->pivot->daily_rate ?? 0 }}"
                                                    data-monthly-rate="{{ $plan->unitTypeRates->first()?->pivot->monthly_rate ?? 0 }}">
                                                    {{ $plan->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Monthly Rate Section -->
                            <div class="col-md-12 d-none" id="monthlyRateSection">
                                <div class="row g-2 mt-2">
                                    <div class="col-md-4">
                                        <label class="form-label-custom">{{ __('dashboard.monthly_rate') }}</label>
                                        <input type="number" class="form-control" name="monthly_rate"
                                            id="monthlyRateInput"
                                            value="{{ old('monthly_rate', $reservation->monthly_rate ?? 0) }}"
                                            step="0.01" min="0">
                                    </div>
                                </div>
                            </div>

                            <!-- Rate & Total -->
                            <div class="col-md-6">
                                <div class="section-title">{{ __('dashboard.rate_details') }}</div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label-custom">{{ __('dashboard.total_rent') }}</label>
                                        <input type="number" class="form-control" name="total_rent" id="totalRent"
                                            value="{{ old('total_rent', $reservation->total_rent ?? 0) }}"
                                            step="0.01" readonly>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label-custom">{{ __('dashboard.discount_type') }}</label>
                                        <select class="form-select" name="discount_type" id="discountTypeSelect">
                                            <option value="">{{ __('dashboard.select_discount_type') }}</option>
                                            @foreach ($discountTypes as $type)
                                                <option value="{{ $type->type }}"
                                                    {{ old('discount_type', $reservation->discount_type ?? '') == $type->type ? 'selected' : '' }}>
                                                    {{ $type->type }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label-custom">{{ __('dashboard.discount') }}</label>
                                        <input type="number" class="form-control" name="discount" id="discount"
                                            value="{{ old('discount', $reservation->discount ?? 0) }}" step="0.01"
                                            min="0">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label-custom">{{ __('dashboard.penalty') }}</label>
                                        <select class="form-select" name="penalty_id" id="penaltySelect">
                                            <option value="">{{ __('dashboard.select_penalty') }}</option>
                                            @foreach ($penalties as $penalty)
                                                <option value="{{ $penalty->id }}"
                                                    data-value="{{ $penalty->value ?? 0 }}"
                                                    data-type="{{ $penalty->penalty_type ?? 'fixed' }}"
                                                    {{ $reservation->penalty_id == $penalty->id ? 'selected' : '' }}>
                                                    {{ $penalty->name }} ({{ $penalty->value ?? 0 }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label-custom">{{ __('dashboard.penalty_amount') }}</label>
                                        <input type="number" class="form-control" name="penalty_amount"
                                            id="penaltyAmount" value="{{ $reservation->penalty_amount ?? 0 }}"
                                            step="0.01" min="0">
                                    </div>
                                </div>
                                <!-- Rate Breakdown Display -->
                                <div class="mt-2">
                                    <small class="text-muted">{{ __('dashboard.rate_breakdown') }}:</small>
                                    <div id="rateBreakdown"></div>
                                </div>
                            </div>

                            <!-- Taxes & Fees -->
                            <div class="col-md-6">
                                <div class="section-title">{{ __('dashboard.taxes_fees') }}</div>
                                <div id="taxFieldsContainer">
                                    @foreach ($taxFees as $tax)
                                        <div class="row g-2 mb-2">
                                            <div class="col-8">
                                                <label class="form-label-custom">{{ $tax->custom_name }}</label>
                                                <input type="number" class="form-control tax-fee-input"
                                                    name="tax_fees[{{ $tax->id }}]" id="tax_{{ $tax->id }}"
                                                    value="0" step="0.01" min="0" readonly
                                                    data-method="{{ $tax->method }}"
                                                    data-amount="{{ $tax->amount }}">
                                            </div>
                                            <div class="col-4 d-flex align-items-end">
                                                <span class="badge bg-secondary">
                                                    @if ($tax->method == 'percentage')
                                                        {{ $tax->amount }}%
                                                    @else
                                                        {{ number_format($tax->amount, 2) }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label-custom">{{ __('dashboard.total_taxes_fees') }}</label>
                                        <input type="number" class="form-control" name="total_taxes_fees"
                                            id="totalTaxesFees"
                                            value="{{ old('total_taxes_fees', $reservation->total_taxes_fees ?? 0) }}"
                                            step="0.01" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Summary Display -->
                            <div class="col-md-12 mt-3">
                                <div class="financial-summary">
                                    <div class="financial-row">
                                        <span>{{ __('dashboard.subtotal') }}</span>
                                        <span id="displaySubtotal">0.00</span>
                                    </div>
                                    <div class="financial-row">
                                        <span>{{ __('dashboard.discount') }}</span>
                                        <span id="displayDiscount">-0.00</span>
                                    </div>
                                    <div class="financial-row">
                                        <span>{{ __('dashboard.taxes_fees') }}</span>
                                        <span id="displayTaxes">0.00</span>
                                    </div>
                                    <div class="financial-row">
                                        <span>{{ __('dashboard.security_deposit') }}</span>
                                        <span id="displayDeposit">0.00</span>
                                    </div>
                                    <div class="financial-row">
                                        <span>{{ __('dashboard.total') }}</span>
                                        <span id="displayTotal">0.00</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Section -->
                            <div class="col-md-12 mt-3">
                                <div class="section-title">{{ __('dashboard.payment_details') }}</div>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label-custom">{{ __('dashboard.payment_method') }}</label>
                                        <select class="form-select" name="payment_method_id" id="paymentMethod">
                                            <option value="">{{ __('dashboard.select_payment_method') }}</option>
                                            @foreach ($paymentMethods as $method)
                                                <option value="{{ $method->id }}"
                                                    {{ old('payment_method_id', $reservation->payment_method_id ?? '') == $method->id ? 'selected' : '' }}>
                                                    {{ $method->paymentMethod->name ?? $method->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label-custom">{{ __('dashboard.paid_amount') }}</label>
                                        <input type="number" class="form-control" name="paid_amount" id="paidAmount"
                                            value="{{ old('paid_amount', $reservation->paid_amount ?? 0) }}"
                                            step="0.01" min="0">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label-custom">{{ __('dashboard.security_deposit') }}</label>
                                        <input type="number" class="form-control" name="security_deposit"
                                            id="securityDeposit"
                                            value="{{ old('security_deposit', $reservation->security_deposit ?? 0) }}"
                                            step="0.01" min="0">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label-custom">{{ __('dashboard.balance') }}</label>
                                        <input type="number" class="form-control" name="balance" id="balance"
                                            value="{{ old('balance', $reservation->balance ?? 0) }}" step="0.01"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                        <i class="bi bi-x-circle me-1"></i>{{ __('dashboard.cancel') }}
                    </button>
                    <button type="button" class="btn btn-primary" onclick="saveReservation('check_out')">
                        <i class="bi bi-box-arrow-left me-1"></i>{{ __('dashboard.check_out') }}
                    </button>
                    <button type="button" class="btn btn-success" onclick="saveReservation('update')">
                        <i class="bi bi-check-circle me-1"></i>{{ __('dashboard.update_reservation') }}
                    </button>
                </div>
            </form>

        </div>
    </main>

    <!-- New Guest Modal -->
    <div class="modal fade" id="newGuestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('dashboard.add_new_guest') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Guest Information -->
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="section-title">{{ __('dashboard.guest_information') }}</h6>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.first_name') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="newGuestFirstName"
                                placeholder="{{ __('dashboard.type_first_name') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.second_name') }}</label>
                            <input type="text" class="form-control" id="newGuestSecondName"
                                placeholder="{{ __('dashboard.type_second_name') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.middle_name') }}</label>
                            <input type="text" class="form-control" id="newGuestMiddleName"
                                placeholder="{{ __('dashboard.type_middle_name') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.last_name') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="newGuestLastName"
                                placeholder="{{ __('dashboard.type_last_name') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.gender') }} <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="newGuestGender">
                                <option value="">{{ __('dashboard.select_gender') }}</option>
                                <option value="male">{{ __('dashboard.male') }}</option>
                                <option value="female">{{ __('dashboard.female') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.date_of_birth') }}</label>
                            <input type="date" class="form-control" id="newGuestDob">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.guest_class') }}</label>
                            <select class="form-select" id="newGuestClass">
                                <option value="">{{ __('dashboard.select_guest_class') }}</option>
                                @foreach ($guestClasses as $class)
                                    <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.car_license_plate') }}</label>
                            <input type="text" class="form-control" id="newGuestCarPlate"
                                placeholder="{{ __('dashboard.type_car_plate') }}">
                        </div>
                    </div>

                    <!-- Verification Information -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h6 class="section-title">{{ __('dashboard.verification_information') }}</h6>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.nationality') }} <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="newGuestNationality">
                                <option value="">{{ __('dashboard.select_nationality') }}</option>
                                <option value="SA">Saudi Arabia</option>
                                <option value="KW">Kuwait</option>
                                <option value="AE">United Arab Emirates</option>
                                <option value="BH">Bahrain</option>
                                <option value="OM">Oman</option>
                                <option value="QA">Qatar</option>
                                <option value="EG">Egypt</option>
                                <option value="IN">India</option>
                                <option value="PK">Pakistan</option>
                                <option value="OTHER">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.nationality_code') }}</label>
                            <input type="text" class="form-control" id="newGuestNationalityCode"
                                placeholder="{{ __('dashboard.type_nationality_code') }}" maxlength="3">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.guest_type') }}</label>
                            <select class="form-select" id="newGuestType">
                                <option value="">{{ __('dashboard.select_guest_type') }}</option>
                                <option value="individual">{{ __('dashboard.individual') }}</option>
                                <option value="family">{{ __('dashboard.family') }}</option>
                                <option value="corporate">{{ __('dashboard.corporate') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.id_type') }} <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="newGuestIdType">
                                <option value="">{{ __('dashboard.select_id_type') }}</option>
                                <option value="national_id">{{ __('dashboard.national_id') }}</option>
                                <option value="passport">{{ __('dashboard.passport') }}</option>
                                <option value="iqama">{{ __('dashboard.iqama') }}</option>
                                <option value="driver_license">{{ __('dashboard.driver_license') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.id_number') }}</label>
                            <input type="text" class="form-control" id="newGuestIdNumber"
                                placeholder="{{ __('dashboard.type_id_number') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.id_serial') }}</label>
                            <select class="form-select" id="newGuestIdSerial">
                                <option value="">{{ __('dashboard.select_id_serial') }}</option>
                                <option value="first">{{ __('dashboard.first') }}</option>
                                <option value="second">{{ __('dashboard.second') }}</option>
                                <option value="third">{{ __('dashboard.third') }}</option>
                                <option value="last">{{ __('dashboard.last') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.id_issue_country') }}</label>
                            <input type="text" class="form-control" id="newGuestIdIssueCountry"
                                placeholder="{{ __('dashboard.type_id_issue_country') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.id_expiry_date') }}</label>
                            <input type="date" class="form-control" id="newGuestIdExpiryDate">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.visa_number') }}</label>
                            <input type="text" class="form-control" id="newGuestVisaNumber"
                                placeholder="{{ __('dashboard.type_visa_number') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.arrival_from') }}</label>
                            <input type="text" class="form-control" id="newGuestArrivalFrom"
                                placeholder="{{ __('dashboard.type_arrival_from') }}">
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h6 class="section-title">{{ __('dashboard.contact_information') }}</h6>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.mobile_number') }} <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <select class="form-select" id="newGuestDialCode" style="max-width: 100px;">
                                    <option value="+966">+966</option>
                                    <option value="+965">+965</option>
                                    <option value="+971">+971</option>
                                    <option value="+973">+973</option>
                                    <option value="+968">+968</option>
                                    <option value="+974">+974</option>
                                    <option value="+20">+20</option>
                                    <option value="+91">+91</option>
                                    <option value="+92">+92</option>
                                </select>
                                <input type="tel" class="form-control" id="newGuestMobile"
                                    placeholder="{{ __('dashboard.type_mobile_number') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.email') }}</label>
                            <input type="email" class="form-control" id="newGuestEmail"
                                placeholder="{{ __('dashboard.type_email') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.work_place') }}</label>
                            <input type="text" class="form-control" id="newGuestWorkPlace"
                                placeholder="{{ __('dashboard.type_work_place') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.work_phone') }}</label>
                            <input type="tel" class="form-control" id="newGuestWorkPhone"
                                placeholder="{{ __('dashboard.type_work_phone') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">{{ __('dashboard.address') }}</label>
                            <input type="text" class="form-control" id="newGuestAddress"
                                placeholder="{{ __('dashboard.type_address') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('dashboard.discard') }}</button>
                    <button type="button" class="btn btn-primary"
                        onclick="createNewGuest()">{{ __('dashboard.create_guest_profile') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Unit Modal -->
    <div class="modal fade" id="unitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('dashboard.select_unit') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Filter Tabs -->
                    <div class="tab-buttons mb-3">
                        <button type="button" class="tab-btn active"
                            data-filter="all">{{ __('dashboard.all') }}</button>
                        @foreach ($unitTypes as $type)
                            <button type="button" class="tab-btn"
                                data-filter="{{ $type->id }}">{{ $type->name }}</button>
                        @endforeach
                    </div>

                    <!-- Floor/Block Filter -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label-custom">{{ __('dashboard.floor') }}</label>
                            <select class="form-select" id="modalFloorFilter">
                                <option value="">{{ __('dashboard.all_floors') }}</option>
                                @foreach ($floors as $floor)
                                    <option value="{{ $floor->id }}">{{ $floor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-custom">{{ __('dashboard.block') }}</label>
                            <select class="form-select" id="modalBlockFilter">
                                <option value="">{{ __('dashboard.all_blocks') }}</option>
                                @foreach ($blocks as $block)
                                    <option value="{{ $block->id }}">{{ $block->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-custom">{{ __('dashboard.unit_type') }}</label>
                            <select class="form-select" id="modalUnitTypeFilter">
                                <option value="">{{ __('dashboard.all_types') }}</option>
                                @foreach ($unitTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Unit Grid -->
                    <div class="unit-grid" id="modalUnitGrid">
                        @foreach ($units as $unit)
                            @php
                                $customizedUnitType = $unitTypes->firstWhere('id', $unit->unit_type_id);
                                $unitTypeName = $customizedUnitType?->name ?? ($unit->unitType->name ?? '');
                            @endphp
                            <div class="unit-item modal-unit-item" data-unit-id="{{ $unit->id }}"
                                data-unit-number="{{ $unit->unit_number }}" data-unit-type="{{ $unit->unit_type_id }}"
                                data-unit-type-name="{{ $unitTypeName }}"
                                data-floor="{{ $unit->floor_id }}" data-floor-name="{{ $unit->floor->name ?? '' }}"
                                data-block="{{ $unit->block_id }}" data-block-name="{{ $unit->block->name ?? '' }}">
                                <div class="fw-bold">{{ $unit->unit_number }}</div>
                                <small>{{ $unitTypeName }}</small>
                                <div class="status-badge status-available mt-1">{{ __('dashboard.available') }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Corporate Modal -->
    <div class="modal fade" id="newCorporateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('dashboard.new_corporate') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.corporate_name') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="corporateName" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.postal_code') }}</label>
                            <input type="text" class="form-control" id="corporatePostalCode">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.vat_registration_number') }}</label>
                            <input type="text" class="form-control" id="corporateVatNumber">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.commercial_registration_number') }}</label>
                            <input type="text" class="form-control" id="corporateCommercialRegNumber">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.discount_method') }}</label>
                            <select class="form-select" id="corporateDiscountType">
                                <option value="">{{ __('dashboard.select_discount_method') }}</option>
                                <option value="percentage">{{ __('dashboard.percentage') }}</option>
                                <option value="fixed">{{ __('dashboard.fixed_amount') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.discount') }}</label>
                            <input type="number" class="form-control" id="corporateDiscountValue" value="0"
                                min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.country') }}</label>
                            <select class="form-select" id="corporateCountry">
                                <option value="">{{ __('dashboard.select_country') }}</option>
                                <option value="SA">Saudi Arabia</option>
                                <option value="KW">Kuwait</option>
                                <option value="AE">United Arab Emirates</option>
                                <option value="BH">Bahrain</option>
                                <option value="OM">Oman</option>
                                <option value="QA">Qatar</option>
                                <option value="EG">Egypt</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.city') }}</label>
                            <input type="text" class="form-control" id="corporateCity">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.district') }}</label>
                            <input type="text" class="form-control" id="corporateDistrict">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.street') }}</label>
                            <input type="text" class="form-control" id="corporateStreet">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.building_number') }}</label>
                            <input type="text" class="form-control" id="corporateBuildingNumber">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.secondary_number') }}</label>
                            <input type="text" class="form-control" id="corporateSecondaryNumber">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label-custom">{{ __('dashboard.address') }}</label>
                            <textarea class="form-control" id="corporateAddress" rows="2"></textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.email') }}</label>
                            <input type="email" class="form-control" id="corporateEmail">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.phone') }}</label>
                            <input type="tel" class="form-control" id="corporatePhone">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.contact_person_name') }}</label>
                            <input type="text" class="form-control" id="corporateContactPerson">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">{{ __('dashboard.contact_person_phone') }}</label>
                            <input type="tel" class="form-control" id="corporateContactPersonPhone">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                    <button type="button" class="btn btn-primary"
                        onclick="createNewCorporate()">{{ __('dashboard.save_corporate') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        // Unit data for JS
        const unitsData = {!! json_encode(
            $units->map(function ($u) use ($unitTypes) {
                $customizedUnitType = $unitTypes->firstWhere('id', $u->unit_type_id);

                return [
                    'id' => $u->id,
                    'number' => $u->unit_number,
                    'type' => $customizedUnitType?->name ?? ($u->unitType->name ?? ''),
                    'type_id' => $u->unit_type_id,
                    'base_type_id' => $customizedUnitType?->unit_type_id,
                    'floor' => $u->floor_id,
                    'block' => $u->block_id,
                    'floor_name' => $u->floor->name ?? '',
                    'block_name' => $u->block->name ?? '',
                ];
            }),
        ) !!};

        // Unit Modal - Filter by tab
        document.querySelectorAll('#unitModal .tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('#unitModal .tab-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                filterModalUnits();
            });
        });

        // Unit Modal - Filter by dropdowns
        document.getElementById('modalFloorFilter').addEventListener('change', filterModalUnits);
        document.getElementById('modalBlockFilter').addEventListener('change', filterModalUnits);
        document.getElementById('modalUnitTypeFilter').addEventListener('change', filterModalUnits);

        function filterModalUnits() {
            const activeTab = document.querySelector('#unitModal .tab-btn.active');
            const filterType = activeTab ? activeTab.dataset.filter : 'all';
            const floorFilter = document.getElementById('modalFloorFilter').value;
            const blockFilter = document.getElementById('modalBlockFilter').value;
            const typeFilter = document.getElementById('modalUnitTypeFilter').value;

            document.querySelectorAll('.modal-unit-item').forEach(item => {
                const unitType = item.dataset.unitType;
                const floor = item.dataset.floor;
                const block = item.dataset.block;

                let show = true;

                if (filterType !== 'all' && filterType != unitType) show = false;
                if (floorFilter && floorFilter != floor) show = false;
                if (blockFilter && blockFilter != block) show = false;
                if (typeFilter && typeFilter != unitType) show = false;

                item.style.display = show ? '' : 'none';
            });
        }

        // Unit Modal - Select unit
        document.querySelectorAll('.modal-unit-item').forEach(item => {
            item.addEventListener('click', function() {
                const unitId = this.dataset.unitId;
                const unitNumber = this.dataset.unitNumber;
                const unitTypeId = this.dataset.unitType;
                const unitTypeName = this.dataset.unitTypeName;
                const floorName = this.dataset.floorName;
                const blockName = this.dataset.blockName;

                document.getElementById('selectedUnitId').value = unitId;
                document.getElementById('selectedUnitTypeId').value = unitTypeId;
                document.getElementById('displayUnitNumber').textContent = unitNumber;
                document.getElementById('displayUnitType').textContent = unitTypeName;
                document.getElementById('displayFloor').textContent = floorName;
                document.getElementById('displayBlock').textContent = blockName;

                // Get rate for selected unit from API
                const reservationType = document.getElementById('reservationType').value;
                const checkInDate = document.getElementById('checkInDate').value;
                const checkOutDate = document.getElementById('checkOutDate').value;
                console.log('Fetching rates for unit:', unitId);
                let ratesUrl = '{{ route('dashboard.reservation.get_rates') }}?unit_id=' + unitId +
                    '&reservation_type=' + reservationType;
                if (checkInDate) ratesUrl += '&check_in=' + checkInDate;
                if (checkOutDate) ratesUrl += '&check_out=' + checkOutDate;
                fetch(ratesUrl)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Rates fetched:', data);
                        // Store unit rates globally for use in rate calculations
                        window.selectedUnitRates = data;
                        console.log('window.selectedUnitRates stored:', window.selectedUnitRates);
                        // Update rate display
                        updateRateDisplay();
                    })
                    .catch(error => {
                        console.error('Error fetching rates:', error);
                        updateRateDisplay();
                    });

                document.getElementById('selectedUnitInfo').style.display = 'block';

                const modal = bootstrap.Modal.getInstance(document.getElementById('unitModal'));
                modal.hide();
            });
        });

        // Reset modal filters when opening
        document.getElementById('unitModal').addEventListener('show.bs.modal', function() {
            document.getElementById('modalFloorFilter').value = '';
            document.getElementById('modalBlockFilter').value = '';
            document.getElementById('modalUnitTypeFilter').value = '';
            document.querySelectorAll('#unitModal .tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('#unitModal .tab-btn[data-filter="all"]').forEach(b => b.classList.add(
                'active'));
            document.querySelectorAll('.modal-unit-item').forEach(item => {
                item.style.display = '';
            });
        });

        // Clear unit selection
        function clearUnitSelection() {
            document.getElementById('selectedUnitId').value = '';
            document.getElementById('selectedUnitTypeId').value = '';
            document.getElementById('displayUnitNumber').textContent = '';
            document.getElementById('displayUnitType').textContent = '';
            document.getElementById('displayFloor').textContent = '';
            document.getElementById('displayBlock').textContent = '';
            document.getElementById('selectedUnitInfo').style.display = 'none';

            // Reset rates
            document.getElementById('dailyRate').value = '0';
            document.getElementById('monthlyRateInput').value = '0';
            document.getElementById('totalRent').value = '0';

            calculateTaxes();
        }

        // Rate data from backend
        const highWeekdays = {!! json_encode($highWeekdays) !!};
        console.log('HighWeekdays from DB:', highWeekdays);
        const seasonalRates = {!! json_encode(
            $seasonalRates->map(function ($s) {
                    return [
                        'id' => $s->id,
                        'name' => $s->name,
                        'start_date' => $s->start_date,
                        'end_date' => $s->end_date,
                        'rates' => $s->unitRates->map(function ($r) {
                                return [
                                    'unit_type_id' => $r->unit_type_id,
                                    'low_weekday_rate' => $r->low_weekday_rate,
                                    'high_weekday_rate' => $r->high_weekday_rate,
                                    'daily_min_rate' => $r->daily_min_rate,
                                ];
                            })->toArray(),
                    ];
                })->toArray(),
        ) !!};

        const specialRates = {!! json_encode(
            $specialRates->map(function ($s) {
                    return [
                        'id' => $s->id,
                        'name' => $s->name,
                        'start_date' => $s->start_date,
                        'end_date' => $s->end_date,
                        'rates' => $s->unitRates->map(function ($r) {
                                return [
                                    'unit_type_id' => $r->unit_type_id,
                                    'daily_rate' => $r->rate,
                                    'min_rate' => $r->min_rate,
                                ];
                            })->toArray(),
                    ];
                })->toArray(),
        ) !!};

        const unitTypeRates = {!! json_encode(
            $unitTypeRates->map(function ($r) {
                    return [
                        'unit_type_id' => $r->unit_type_id,
                        'low_weekday_rate' => $r->low_weekday_rate,
                        'high_weekday_rate' => $r->high_weekday_rate,
                        'daily_min_rate' => $r->daily_min_rate,
                        'monthly_rate' => $r->monthly_rate,
                        'monthly_min_rate' => $r->monthly_min_rate,
                    ];
                })->toArray(),
        ) !!};

        const unitCustomRates = {!! json_encode(
            $unitCustomRates->map(function ($r) {
                    return [
                        'unit_id' => $r->unit_id,
                        'unit_type_id' => $r->unit_type_id,
                        'low_weekday_rate' => $r->low_weekday_rate,
                        'high_weekday_rate' => $r->high_weekday_rate,
                        'daily_min_rate' => $r->daily_min_rate,
                        'monthly_rate' => $r->monthly_rate,
                        'monthly_min_rate' => $r->monthly_min_rate,
                    ];
                })->toArray(),
        ) !!};

        const ratePlans = {!! json_encode(
            $ratePlans->map(function ($rp) {
                    return [
                        'id' => $rp->id,
                        'name' => $rp->name,
                        'daily_rate' => $rp->daily_rate ?? 0,
                        'monthly_rate' => $rp->monthly_rate ?? 0,
                        'meals' => $rp->meals->map(function ($m) {
                                return [
                                    'name' => $m->meal_name,
                                    'adults' => $m->adults,
                                    'children' => $m->children,
                                ];
                            })->toArray(),
                    ];
                })->toArray(),
        ) !!};

        const ratePlanUnitTypes = {!! json_encode(
            $ratePlans->flatMap(function ($ratePlan) {
                    return $ratePlan->unitTypeRates->map(function ($unitType) use ($ratePlan) {
                        return [
                            'rate_plan_id' => $ratePlan->id,
                            'unit_type_id' => $unitType->id,
                            'daily_rate' => (float) ($unitType->pivot->daily_rate ?? 0),
                            'monthly_rate' => (float) ($unitType->pivot->monthly_rate ?? 0),
                        ];
                    });
                })->values()->toArray(),
        ) !!};

        function getPricingUnitTypeIds(unitTypeId = null) {
            const unitId = document.getElementById('selectedUnitId')?.value;
            const selectedUnit = unitsData.find(unit => unit.id == unitId);
            const unitTypeIds = [
                window.selectedUnitRates?.pricing_unit_type_id,
                selectedUnit?.base_type_id,
                unitTypeId,
                window.selectedUnitRates?.unit_type_id,
                selectedUnit?.type_id,
                document.getElementById('selectedUnitTypeId')?.value,
            ];

            return [...new Set(unitTypeIds.filter(value => value !== undefined && value !== null && value !== '').map(String))];
        }

        function findRateForUnitType(rates, unitTypeId = null) {
            const unitTypeIds = getPricingUnitTypeIds(unitTypeId);

            return (rates || []).find(rate => unitTypeIds.includes(String(rate.unit_type_id))) || null;
        }

        function findSelectedRatePlanUnitType(selectedPlanId, unitTypeId = null) {
            if (!selectedPlanId) {
                return null;
            }

            return findRateForUnitType(
                ratePlanUnitTypes.filter(rate => rate.rate_plan_id == selectedPlanId),
                unitTypeId
            );
        }

        // Global variable to store selected unit's rates from API
        window.selectedUnitRates = null;

        // Get day name
        function getDayName(date) {
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            return days[date.getDay()];
        }

        function parseDateInput(value) {
            if (!value) {
                return null;
            }

            const [year, month, day] = value.split('-').map(Number);

            if (!year || !month || !day) {
                return null;
            }

            return new Date(year, month - 1, day);
        }

        function formatDateInput(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        }

        // Check if day is high weekday
        function isHighWeekday(date) {
            const dayName = getDayName(date).toLowerCase();
            const highDays = highWeekdays.map(d => d.toLowerCase());
            console.log('Day:', dayName, 'HighWeekdays:', highDays, 'Is High:', highDays.includes(dayName));
            return highDays.includes(dayName);
        }

        // Get rate for a specific unit and date
        function getRateForDate(unitId, unitTypeId, date) {
            console.log('getRateForDate called', unitId, unitTypeId, window.selectedUnitRates);

            const dateStr = formatDateInput(date);

            // Priority 0: Check API response - Unit Custom Rate
            if (window.selectedUnitRates && window.selectedUnitRates.unit_custom_rate) {
                const rate = isHighWeekday(date) ?
                    window.selectedUnitRates.unit_custom_rate.high_weekday_rate :
                    window.selectedUnitRates.unit_custom_rate.low_weekday_rate;
                console.log('Using API custom rate:', rate);
                return rate;
            }

            // Priority 1: Check API response - Special Rate
            if (window.selectedUnitRates && window.selectedUnitRates.special_rates) {
                for (const special of window.selectedUnitRates.special_rates) {
                    if (dateStr >= special.start_date && dateStr <= special.end_date && special.rate) {
                        console.log('Using API special rate:', special.rate.rate);
                        return special.rate.rate || 0;
                    }
                }
            }

            // Priority 2: Check API response - Seasonal Rate
            if (window.selectedUnitRates && window.selectedUnitRates.seasonal_rates) {
                for (const seasonal of window.selectedUnitRates.seasonal_rates) {
                    if (dateStr >= seasonal.start_date && dateStr <= seasonal.end_date && seasonal.rate) {
                        const rate = isHighWeekday(date) ?
                            seasonal.rate.high_weekday_rate :
                            seasonal.rate.low_weekday_rate;
                        console.log('Using API seasonal rate:', rate);
                        return rate;
                    }
                }
            }

            // Priority 3: Check API response - Unit Type Rate (base rate)
            if (window.selectedUnitRates && window.selectedUnitRates.unit_type_rate) {
                const rate = isHighWeekday(date) ?
                    window.selectedUnitRates.unit_type_rate.high_weekday_rate :
                    window.selectedUnitRates.unit_type_rate.low_weekday_rate;
                console.log('Using API unit_type_rate:', rate);
                return rate;
            }

            // Priority 4: Check PHP data - Unit Custom Rate
            const customRate = unitCustomRates.find(r => r.unit_id == unitId);
            if (customRate) {
                return isHighWeekday(date) ? customRate.high_weekday_rate : customRate.low_weekday_rate;
            }

            // Priority 5: Check PHP data - Special Rate
            for (const special of specialRates) {
                if (dateStr >= special.start_date && dateStr <= special.end_date) {
                    const rate = findRateForUnitType(special.rates, unitTypeId);
                    if (rate) {
                        return rate.daily_rate;
                    }
                }
            }

            // Priority 6: Check PHP data - Seasonal Rate
            for (const seasonal of seasonalRates) {
                if (dateStr >= seasonal.start_date && dateStr <= seasonal.end_date) {
                    const rate = findRateForUnitType(seasonal.rates, unitTypeId);
                    if (rate) {
                        return isHighWeekday(date) ? rate.high_weekday_rate : rate.low_weekday_rate;
                    }
                }
            }

            // Priority 7: PHP data - Base rate from unit_type_rates
            const baseRate = findRateForUnitType(unitTypeRates, unitTypeId);
            if (baseRate) {
                return isHighWeekday(date) ? baseRate.high_weekday_rate : baseRate.low_weekday_rate;
            }

            return 0;
        }

        // Get monthly rate
        function getMonthlyRate(unitId, unitTypeId) {
            // Priority 0: Check API response first (most recent data for selected unit)
            if (window.selectedUnitRates && parseFloat(window.selectedUnitRates.monthly_rate) > 0) {
                return parseFloat(window.selectedUnitRates.monthly_rate);
            }
            if (window.selectedUnitRates && window.selectedUnitRates.unit_custom_rate && parseFloat(window.selectedUnitRates
                    .unit_custom_rate.monthly_rate) > 0) {
                return parseFloat(window.selectedUnitRates.unit_custom_rate.monthly_rate);
            }

            // Priority 1: Unit Custom Rate
            const customRate = unitCustomRates.find(r => r.unit_id == unitId);
            if (customRate && customRate.monthly_rate) {
                return parseFloat(customRate.monthly_rate);
            }

            // Priority 2: Special Rate (check first day)
            const checkInDate = parseDateInput(document.getElementById('checkInDate').value);
            const dateStr = checkInDate ? formatDateInput(checkInDate) : '';
            for (const special of specialRates) {
                if (dateStr >= special.start_date && dateStr <= special.end_date) {
                    const rate = findRateForUnitType(special.rates, unitTypeId);
                    if (rate && rate.monthly_rate) {
                        return parseFloat(rate.monthly_rate);
                    }
                }
            }

            // Priority 3: Seasonal Rate
            for (const seasonal of seasonalRates) {
                if (dateStr >= seasonal.start_date && dateStr <= seasonal.end_date) {
                    const rate = findRateForUnitType(seasonal.rates, unitTypeId);
                    if (rate && rate.monthly_rate) {
                        return parseFloat(rate.monthly_rate);
                    }
                }
            }

            // Priority 4: Base rate (also check API response)
            if (window.selectedUnitRates && window.selectedUnitRates.unit_type_rate && parseFloat(window.selectedUnitRates
                    .unit_type_rate.monthly_rate) > 0) {
                return parseFloat(window.selectedUnitRates.unit_type_rate.monthly_rate);
            }

            const baseRate = findRateForUnitType(unitTypeRates, unitTypeId);
            if (baseRate) {
                return parseFloat(baseRate.monthly_rate) || 0;
            }

            return 0;
        }

        // Calculate total rate based on dates and unit
        function calculateRate() {
            const checkIn = document.getElementById('checkInDate').value;
            const checkOut = document.getElementById('checkOutDate').value;
            const unitId = document.getElementById('selectedUnitId').value;
            const reservationType = document.getElementById('reservationType').value;

            if (!checkIn || !checkOut || !unitId) {
                return {
                    dailyRate: 0,
                    monthlyRate: 0,
                    breakdown: []
                };
            }

            const unit = unitsData.find(u => u.id == unitId);
            if (!unit) {
                return {
                    dailyRate: 0,
                    monthlyRate: 0,
                    breakdown: []
                };
            }

            const startDate = parseDateInput(checkIn);
            const endDate = parseDateInput(checkOut);
            if (!startDate || !endDate || endDate <= startDate) {
                return {
                    dailyRate: 0,
                    monthlyRate: 0,
                    breakdown: []
                };
            }
            const dayDiff = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));

            let totalDailyRate = 0;
            let breakdown = [];

            // Calculate daily rate for each night
            for (let i = 0; i < dayDiff; i++) {
                const currentDate = new Date(startDate);
                currentDate.setDate(currentDate.getDate() + i);

                const rate = parseFloat(getRateForDate(unitId, unit.base_type_id || unit.type_id, currentDate)) || 0;
                totalDailyRate += rate;

                const isHigh = isHighWeekday(currentDate);
                breakdown.push({
                    date: formatDateInput(currentDate),
                    day: getDayName(currentDate),
                    rate: rate,
                    isHighWeekday: isHigh
                });
            }

            // Calculate monthly rate
            const monthlyRate = getMonthlyRate(unitId, unit.base_type_id || unit.type_id);

            // If monthly reservation, use monthly rate
            if (reservationType === 'monthly' && parseFloat(monthlyRate) > 0) {
                return {
                    dailyRate: 0,
                    monthlyRate: parseFloat(monthlyRate),
                    totalRate: parseFloat(monthlyRate),
                    breakdown: breakdown,
                    isMonthly: true
                };
            }

            return {
                dailyRate: parseFloat(totalDailyRate),
                monthlyRate: parseFloat(monthlyRate),
                totalRate: parseFloat(totalDailyRate),
                breakdown: breakdown,
                isMonthly: false
            };
        }

        // Update rate display
        function updateRateDisplay() {
            console.log('updateRateDisplay called');
            const reservationType = document.getElementById('reservationType').value;
            const rateOption = document.querySelector('input[name="rateOption"]:checked').value;
            const unitId = document.getElementById('selectedUnitId').value;

            console.log('reservationType:', reservationType, 'rateOption:', rateOption, 'unitId:', unitId);
            console.log('window.selectedUnitRates in updateRateDisplay:', window.selectedUnitRates);

            let dailyRate = 0;
            let monthlyRate = 0;

            if (reservationType === 'daily') {
                if (rateOption === 'rate_plan') {
                    console.log('Rate plan selected - checking rates', window.selectedUnitRates, ratePlanUnitTypes);
                    const selectedPlanId = document.getElementById('ratePlanSelectInput').value;
                    const ratePlanUnitType = findSelectedRatePlanUnitType(selectedPlanId);
                    console.log('Selected plan ID:', selectedPlanId, 'Unit type matches:', getPricingUnitTypeIds());
                    console.log('Found rate plan unit type:', ratePlanUnitType);

                    if (ratePlanUnitType) {
                        dailyRate = parseFloat(ratePlanUnitType.daily_rate) || 0;
                        monthlyRate = parseFloat(ratePlanUnitType.monthly_rate) || 0;
                    }
                } else if (rateOption === 'monthly_rate') {
                    // Monthly rate selected for daily reservation
                    const rateInfo = calculateRate();
                    monthlyRate = parseFloat(rateInfo.monthlyRate) || 0;
                    dailyRate = 0;
                } else {
                    // Get calculated rate (daily_rate)
                    const rateInfo = calculateRate();
                    dailyRate = parseFloat(rateInfo.dailyRate) || 0;
                    monthlyRate = parseFloat(rateInfo.monthlyRate) || 0;

                    // Update breakdown display
                    const breakdownDiv = document.getElementById('rateBreakdown');
                    if (breakdownDiv && rateInfo.breakdown && rateInfo.breakdown.length > 0) {
                        let breakdownHtml =
                            '<table class="table table-sm table-bordered mt-2"><thead><tr><th>Date</th><th>Day</th><th>Type</th><th>Rate</th></tr></thead><tbody>';
                        rateInfo.breakdown.forEach(item => {
                            breakdownHtml += `<tr>
                                <td>${item.date}</td>
                                <td>${item.day}</td>
                                <td><span class="badge ${item.isHighWeekday ? 'bg-danger' : 'bg-success'}">${item.isHighWeekday ? 'High' : 'Low'}</span></td>
                                <td>${item.rate.toFixed(2)}</td>
                            </tr>`;
                        });
                        breakdownHtml += '</tbody></table>';
                        breakdownDiv.innerHTML = breakdownHtml;
                    }
                }

                document.getElementById('dailyRate').value = parseFloat(dailyRate).toFixed(2);
                document.getElementById('monthlyRateInput').value = parseFloat(monthlyRate).toFixed(2);
                console.log('Daily rate set to:', dailyRate, 'Monthly rate set to:', monthlyRate);
            } else {
                // Monthly reservation
                if (rateOption === 'rate_plan') {
                    const selectedPlanId = document.getElementById('ratePlanSelectInput').value;
                    const ratePlanUnitType = findSelectedRatePlanUnitType(selectedPlanId);

                    if (ratePlanUnitType) {
                        monthlyRate = parseFloat(ratePlanUnitType.monthly_rate) || 0;
                    }
                } else {
                    // Get calculated monthly rate
                    const rateInfo = calculateRate();
                    monthlyRate = parseFloat(rateInfo.monthlyRate) || 0;
                }
                document.getElementById('monthlyRateInput').value = parseFloat(monthlyRate).toFixed(2);
            }

            // Trigger financial calculation and tax calculation
            calculateTaxes(); // This will also call calculateFinancials via calculateTotalTaxes
        }

        @php
            $initialOccupants = old('occupants', $reservation->reservationGuests
            ->where('is_primary', false)
            ->values()
            ->map(fn ($reservationGuest) => [
                'guest_id' => $reservationGuest->guest_id,
                'name' => $reservationGuest->guest?->full_name,
                'mobile' => $reservationGuest->guest?->mobile,
                'relationship' => $reservationGuest->relationship,
            ])
            ->all());
        @endphp

        let selectedUnit = null;
        let selectedGuest = null;
        const initialOccupants = @json($initialOccupants);
        let occupantEntries = [];

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function escapeAttribute(value) {
            return escapeHtml(value).replace(/`/g, '&#096;');
        }

        function selectGuestFromResult(button) {
            selectGuest(
                parseInt(button.dataset.guestId, 10),
                button.dataset.name || '',
                button.dataset.mobile || '',
                button.dataset.email || ''
            );
        }

        function addOccupantFromResult(button) {
            addOccupant(
                parseInt(button.dataset.guestId, 10),
                button.dataset.name || '',
                button.dataset.mobile || ''
            );
        }

        function selectCorporateFromResult(button) {
            selectCorporate(
                parseInt(button.dataset.corporateId, 10),
                button.dataset.name || '',
                button.dataset.discount || ''
            );
        }

        function bootstrapOccupants(entries) {
            occupantEntries = [];

            (entries || []).forEach(entry => {
                const guestId = parseInt(entry.guest_id, 10);

                if (!guestId || occupantEntries.some(existing => existing.guest_id === guestId)) {
                    return;
                }

                occupantEntries.push({
                    guest_id: guestId,
                    name: entry.name || '',
                    mobile: entry.mobile || '',
                    relationship: entry.relationship || '',
                });
            });

            renderOccupants();
        }

        function renderOccupants() {
            const list = document.getElementById('occupantsList');
            const fields = document.getElementById('occupantsFields');
            const emptyState = document.getElementById('occupantsEmptyState');
            const countBadge = document.getElementById('occupantsCountBadge');

            if (!list || !fields || !emptyState || !countBadge) {
                return;
            }

            countBadge.textContent = occupantEntries.length;

            if (occupantEntries.length === 0) {
                list.innerHTML = '';
                fields.innerHTML = '';
                emptyState.classList.remove('d-none');

                return;
            }

            emptyState.classList.add('d-none');
            list.innerHTML = occupantEntries.map(entry => `
                <div class="occupant-card">
                    <div class="occupant-card__top">
                        <div class="occupant-card__identity">
                            <div class="occupant-card__icon">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div>
                                <div class="occupant-card__name">${escapeHtml(entry.name || '{{ __('dashboard.occupant') }}')}</div>
                                <div class="occupant-card__meta">${escapeHtml(entry.mobile || '-')}</div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeOccupant(${entry.guest_id})">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="occupant-card__field">
                        <label class="form-label-custom mb-1">{{ __('dashboard.relationship') }}</label>
                        <input type="text" class="form-control" value="${escapeHtml(entry.relationship || '')}"
                            placeholder="{{ __('dashboard.type_relationship') }}"
                            onchange="updateOccupantRelationship(${entry.guest_id}, this.value)">
                    </div>
                </div>
            `).join('');

            fields.innerHTML = occupantEntries.map((entry, index) => `
                <input type="hidden" name="occupants[${index}][guest_id]" value="${entry.guest_id}">
                <input type="hidden" name="occupants[${index}][relationship]" id="occupantRelationshipField_${entry.guest_id}" value="${escapeHtml(entry.relationship || '')}">
                <input type="hidden" name="occupants[${index}][name]" value="${escapeHtml(entry.name || '')}">
                <input type="hidden" name="occupants[${index}][mobile]" value="${escapeHtml(entry.mobile || '')}">
            `).join('');
        }

        function updateOccupantRelationship(guestId, value) {
            occupantEntries = occupantEntries.map(entry => entry.guest_id === guestId ? {
                ...entry,
                relationship: value,
            } : entry);

            const hiddenField = document.getElementById(`occupantRelationshipField_${guestId}`);
            if (hiddenField) {
                hiddenField.value = value;
            }
        }

        function removeOccupant(guestId) {
            occupantEntries = occupantEntries.filter(entry => entry.guest_id !== guestId);
            renderOccupants();
        }

        function removeOccupantByGuestId(guestId) {
            if (occupantEntries.some(entry => entry.guest_id === guestId)) {
                removeOccupant(guestId);
            }
        }

        function searchOccupant() {
            const query = document.getElementById('occupantSearch').value;

            if (!query || query.length < 2) {
                alert('Please enter at least 2 characters to search');
                return;
            }

            const resultsDiv = document.getElementById('occupantSearchResults');
            resultsDiv.innerHTML = '<div class="lookup-results__state"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';
            resultsDiv.classList.remove('d-none');

            fetch('{{ route("dashboard.guest.search") }}?q=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(guests => {
                    if (guests.length === 0) {
                        resultsDiv.innerHTML = '<div class="lookup-results__state">No guests found</div>';
                        return;
                    }

                    let html = '';
                    guests.forEach(guest => {
                        const fullName = [guest.first_name, guest.second_name, guest.middle_name, guest.last_name].filter(Boolean).join(' ');
                        const mobile = guest.mobile_dial_code ? guest.mobile_dial_code + guest.mobile_number : guest.mobile_number;
                        html += `
                            <button type="button" class="lookup-result-item"
                                data-guest-id="${guest.id}"
                                data-name="${escapeAttribute(fullName)}"
                                data-mobile="${escapeAttribute(mobile || '')}"
                                onclick="addOccupantFromResult(this)">
                                <span class="lookup-result-item__icon lookup-result-item__icon--occupant">
                                    <i class="bi bi-person-plus"></i>
                                </span>
                                <span class="lookup-result-item__content">
                                    <span class="lookup-result-item__title">${escapeHtml(fullName)}</span>
                                    <span class="lookup-result-item__meta">${escapeHtml(mobile || '-')} | ${escapeHtml(guest.id_number || '-')}</span>
                                </span>
                                <span class="lookup-result-item__cta">
                                    <i class="bi bi-arrow-right"></i>
                                </span>
                            </button>
                        `;
                    });
                    resultsDiv.innerHTML = html;
                })
                .catch(error => {
                    resultsDiv.innerHTML = '<div class="lookup-results__state text-danger">Error searching guests</div>';
                    console.error(error);
                });
        }

        function addOccupant(guestId, name, mobile) {
            const primaryGuestId = parseInt(document.getElementById('guestId').value, 10);

            if (primaryGuestId && primaryGuestId === guestId) {
                alert('{{ __('dashboard.primary_guest_already_selected') }}');
                return;
            }

            if (occupantEntries.some(entry => entry.guest_id === guestId)) {
                alert('{{ __('dashboard.occupant_already_added') }}');
                return;
            }

            occupantEntries.push({
                guest_id: guestId,
                name: name,
                mobile: mobile,
                relationship: '',
            });

            renderOccupants();
            document.getElementById('occupantSearch').value = '';
            document.getElementById('occupantSearchResults').classList.add('d-none');
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            const hasRatePlan = Boolean(document.getElementById('ratePlanSelectInput')?.value);
            const reservationType = document.getElementById('reservationType').value;
            const dailyRateSection = document.getElementById('dailyRateSection');
            const monthlyRateSection = document.getElementById('monthlyRateSection');
            const dailyRateInput = document.getElementById('dailyRateInput');
            const ratePlanSelect = document.getElementById('ratePlanSelect');

            if (reservationType === 'monthly') {
                monthlyRateSection.classList.remove('d-none');
                dailyRateSection.classList.add('d-none');
            } else {
                dailyRateSection.classList.remove('d-none');
                monthlyRateSection.classList.add('d-none');
            }

            if (hasRatePlan) {
                document.getElementById('rateOptionPlan').checked = true;
                dailyRateSection.classList.remove('d-none');
                dailyRateInput.classList.add('d-none');
                ratePlanSelect.classList.remove('d-none');
            }

            calculateNights();
            calculateTaxes(); // This will also call calculateFinancials via calculateTotalTaxes

            // Date change handlers
            document.getElementById('checkInDate').addEventListener('change', function() {
                calculateNights();
                updateRateDisplay();
                calculateTaxes();
            });

            document.getElementById('checkOutDate').addEventListener('change', function() {
                calculateNights();
                updateRateDisplay();
                calculateTaxes();
            });

            // Reservation type change
            document.getElementById('reservationType').addEventListener('change', function() {
                const reservationType = this.value;
                const dailyRateSection = document.getElementById('dailyRateSection');
                const monthlyRateSection = document.getElementById('monthlyRateSection');
                const rateOptionDaily = document.getElementById('rateOptionDaily');
                const rateOptionPlan = document.getElementById('ratePlanSelect');
                const dailyRateLabel = document.querySelector('label[for="rateOptionDaily"]');
                const dailyInputLabel = document.getElementById('dailyRateInput').querySelector('label');

                const unitId = document.getElementById('selectedUnitId').value;

                if (reservationType === 'daily') {
                    dailyRateSection.classList.remove('d-none');
                    monthlyRateSection.classList.add('d-none');
                    // Show daily rate input by default, hide rate plan
                    document.getElementById('dailyRateInput').classList.remove('d-none');
                    document.getElementById('ratePlanSelect').classList.add('d-none');
                    // Reset to daily rate
                    rateOptionDaily.value = 'daily_rate';
                    rateOptionDaily.checked = true;
                    dailyRateLabel.textContent = 'Daily Rate';
                    dailyInputLabel.textContent = 'Daily Rate';

                    // Fetch fresh rates for daily
                    if (unitId) {
                        fetch('{{ route('dashboard.reservation.get_rates') }}?unit_id=' + unitId +
                                '&reservation_type=daily')
                            .then(response => response.json())
                            .then(data => {
                                window.selectedUnitRates = data;
                                updateRateDisplay();
                            })
                            .catch(error => {
                                console.error('Error fetching rates:', error);
                                updateRateDisplay();
                            });
                    } else {
                        updateRateDisplay();
                    }
                } else {
                    dailyRateSection.classList.add('d-none');
                    monthlyRateSection.classList.remove('d-none');
                    // For monthly: show monthly rate input, hide rate plan by default
                    document.getElementById('dailyRateInput').classList.add('d-none');
                    document.getElementById('ratePlanSelect').classList.add('d-none');
                    // Change to monthly rate
                    rateOptionDaily.value = 'monthly_rate';
                    rateOptionDaily.checked = true;
                    dailyRateLabel.textContent = 'Monthly Rate';
                    dailyInputLabel.textContent = 'Monthly Rate';

                    // Fetch fresh rates for monthly
                    if (unitId) {
                        fetch('{{ route('dashboard.reservation.get_rates') }}?unit_id=' + unitId +
                                '&reservation_type=monthly')
                            .then(response => response.json())
                            .then(data => {
                                window.selectedUnitRates = data;
                                updateRateDisplay();
                            })
                            .catch(error => {
                                console.error('Error fetching rates:', error);
                                updateRateDisplay();
                            });
                    } else {
                        updateRateDisplay();
                    }
                }
                calculateTaxes();
            });

            // Rate option change (daily rate vs rate plan)
            document.querySelectorAll('input[name="rateOption"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    console.log('Rate option changed to:', this.value);
                    const reservationType = document.getElementById('reservationType').value;
                    const dailyRateInput = document.getElementById('dailyRateInput');
                    const ratePlanSelect = document.getElementById('ratePlanSelect');
                    const dailyRateLabel = document.getElementById('dailyRateInput').querySelector(
                        'label');

                    if (this.value === 'daily_rate' || this.value === 'monthly_rate') {
                        // Show rate input (daily or monthly based on value)
                        dailyRateInput.classList.remove('d-none');
                        ratePlanSelect.classList.add('d-none');

                        // Update label based on type
                        if (this.value === 'daily_rate') {
                            dailyRateLabel.textContent = 'Daily Rate';
                        } else {
                            dailyRateLabel.textContent = 'Monthly Rate';
                        }
                    } else {
                        // Rate plan selected - show dropdown
                        dailyRateInput.classList.add('d-none');
                        ratePlanSelect.classList.remove('d-none');
                    }
                    console.log('Calling updateRateDisplay from rate option change');
                    updateRateDisplay();
                    calculateTaxes();
                });
            });

            // Rate plan selection
            document.getElementById('ratePlanSelectInput').addEventListener('change', function() {
                console.log('Rate plan changed to:', this.value);
                console.log('window.selectedUnitRates at rate plan change:', window.selectedUnitRates);
                console.log('ratePlanUnitTypes:', ratePlanUnitTypes);
                updateRateDisplay();
                calculateTaxes();
            });

            // Rate changes (manual override)
            document.getElementById('dailyRate').addEventListener('input', function() {
                calculateFinancials();
                calculateTaxes();
            });
            document.getElementById('monthlyRateInput').addEventListener('input', function() {
                calculateFinancials();
                calculateTaxes();
            });
            document.getElementById('discount').addEventListener('input', calculateFinancials);
            document.getElementById('paidAmount').addEventListener('input', calculateFinancials);
            document.getElementById('securityDeposit').addEventListener('input', calculateFinancials);

            // Guest search
            document.getElementById('searchGuestBtn').addEventListener('click', searchGuest);
            document.getElementById('searchOccupantBtn').addEventListener('click', searchOccupant);

            // Corporate search
            document.getElementById('searchCorporateBtn').addEventListener('click', searchCorporate);

            bootstrapOccupants(initialOccupants);
        });

        function calculateNights() {
            const checkIn = parseDateInput(document.getElementById('checkInDate').value);
            const checkOut = parseDateInput(document.getElementById('checkOutDate').value);

            if (checkIn && checkOut && checkOut > checkIn) {
                const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
                document.getElementById('nights').value = nights;
            }
        }

        function getCurrentSubtotal() {
            const nights = parseInt(document.getElementById('nights').value) || 0;
            const reservationType = document.getElementById('reservationType').value;

            if (reservationType === 'daily') {
                const dailyRate = parseFloat(document.getElementById('dailyRate').value) || 0;

                return nights * dailyRate;
            }

            const monthlyRate = parseFloat(document.getElementById('monthlyRateInput').value) || 0;
            const checkIn = parseDateInput(document.getElementById('checkInDate').value);
            const checkOut = parseDateInput(document.getElementById('checkOutDate').value);

            if (!checkIn || !checkOut) {
                return monthlyRate;
            }

            const months = (checkOut.getFullYear() - checkIn.getFullYear()) * 12 + (checkOut.getMonth() - checkIn
                .getMonth());

            return months > 0 ? monthlyRate : monthlyRate;
        }

        function calculateTaxes() {
            const nights = parseInt(document.getElementById('nights').value) || 0;
            const subtotal = getCurrentSubtotal();

            // Get all tax inputs
            document.querySelectorAll('.tax-fee-input').forEach(input => {
                const method = input.dataset.method;
                const amount = parseFloat(input.dataset.amount) || 0;
                let taxValue = 0;

                if (method === 'percentage') {
                    taxValue = (subtotal * amount) / 100;
                } else if (method === 'fixed_amount_reservation') {
                    taxValue = amount;
                } else if (method === 'fixed_amount_per_night') {
                    taxValue = amount * nights;
                }

                input.value = taxValue.toFixed(2);
            });

            // Calculate total taxes
            calculateTotalTaxes();
        }

        function calculateTotalTaxes() {
            let totalTaxes = 0;
            document.querySelectorAll('.tax-fee-input').forEach(input => {
                totalTaxes += parseFloat(input.value) || 0;
            });
            document.getElementById('totalTaxesFees').value = totalTaxes.toFixed(2);
            calculateFinancials();
        }

        function calculateFinancials() {
            const nights = parseInt(document.getElementById('nights').value) || 0;

            // Calculate subtotal
            const subtotal = getCurrentSubtotal();

            // Calculate taxes from the tax inputs
            let totalTaxes = 0;
            document.querySelectorAll('.tax-fee-input').forEach(input => {
                totalTaxes += parseFloat(input.value) || 0;
            });

            // Get discount and penalty
            const discount = parseFloat(document.getElementById('discount').value) || 0;
            const penaltyAmount = parseFloat(document.getElementById('penaltyAmount').value) || 0;

            // Total
            const total = subtotal - discount + totalTaxes + penaltyAmount;

            // Payment
            const paidAmount = parseFloat(document.getElementById('paidAmount').value) || 0;
            const securityDeposit = parseFloat(document.getElementById('securityDeposit').value) || 0;
            const balance = total - paidAmount - securityDeposit;

            // Update displays
            document.getElementById('totalRent').value = subtotal.toFixed(2);
            document.getElementById('displaySubtotal').textContent = subtotal.toFixed(2);
            document.getElementById('displayDiscount').textContent = '-' + discount.toFixed(2);
            document.getElementById('displayTaxes').textContent = totalTaxes.toFixed(2);
            document.getElementById('displayDeposit').textContent = securityDeposit.toFixed(2);
            document.getElementById('displayTotal').textContent = total.toFixed(2);
            document.getElementById('balance').value = balance.toFixed(2);
        }

        function searchGuest() {
            const query = document.getElementById('guestSearch').value;
            if (!query || query.length < 2) {
                alert('Please enter at least 2 characters to search');
                return;
            }

            const resultsDiv = document.getElementById('guestSearchResults');
            resultsDiv.innerHTML = '<div class="lookup-results__state"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';
            resultsDiv.classList.remove('d-none');

            fetch('{{ route("dashboard.guest.search") }}?q=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(guests => {
                    if (guests.length === 0) {
                        resultsDiv.innerHTML = '<div class="lookup-results__state">No guests found</div>';
                        return;
                    }

                    let html = '';
                    guests.forEach(guest => {
                        const fullName = [guest.first_name, guest.second_name, guest.middle_name, guest.last_name].filter(Boolean).join(' ');
                        const mobile = guest.mobile_dial_code ? guest.mobile_dial_code + guest.mobile_number : guest.mobile_number;
                        html += `
                            <button type="button" class="lookup-result-item"
                                data-guest-id="${guest.id}"
                                data-name="${escapeAttribute(fullName)}"
                                data-mobile="${escapeAttribute(mobile || '')}"
                                data-email="${escapeAttribute(guest.email || '')}"
                                onclick="selectGuestFromResult(this)">
                                <span class="lookup-result-item__icon">
                                    <i class="bi bi-person"></i>
                                </span>
                                <span class="lookup-result-item__content">
                                    <span class="lookup-result-item__title">${escapeHtml(fullName)}</span>
                                    <span class="lookup-result-item__meta">${escapeHtml(mobile || '-')} | ${escapeHtml(guest.email || '-')}</span>
                                </span>
                                <span class="lookup-result-item__cta">
                                    <i class="bi bi-arrow-right"></i>
                                </span>
                            </button>
                        `;
                    });
                    resultsDiv.innerHTML = html;
                })
                .catch(error => {
                    resultsDiv.innerHTML = '<div class="lookup-results__state text-danger">Error searching guests</div>';
                    console.error(error);
                });
        }

        function selectGuest(id, name, mobile, email) {
            removeOccupantByGuestId(id);
            document.getElementById('guestId').value = id;
            document.getElementById('guestFirstName').value = name.split(' ')[0] || '';
            document.getElementById('guestLastName').value = name.split(' ').slice(1).join(' ') || '';
            document.getElementById('guestMobile').value = mobile;
            document.getElementById('guestEmail').value = email;

            document.getElementById('displayGuestName').textContent = name;
            document.getElementById('displayGuestMobile').textContent = mobile;
            const displayGuestEmail = document.getElementById('displayGuestEmail');
            if (displayGuestEmail) {
                displayGuestEmail.textContent = email;
            }
            document.getElementById('guestInfoDisplay').classList.remove('d-none');
            document.getElementById('guestSearch').value = '';
            document.getElementById('guestSearchResults').classList.add('d-none');
        }

        function clearGuestInfo() {
            document.getElementById('guestId').value = '';
            document.getElementById('guestFirstName').value = '';
            document.getElementById('guestLastName').value = '';
            document.getElementById('guestMobile').value = '';
            document.getElementById('guestEmail').value = '';
            document.getElementById('displayGuestName').textContent = '';
            document.getElementById('displayGuestMobile').textContent = '';
            const displayGuestEmail = document.getElementById('displayGuestEmail');
            if (displayGuestEmail) {
                displayGuestEmail.textContent = '';
            }
            document.getElementById('guestInfoDisplay').classList.add('d-none');
        }

        function searchCorporate() {
            const query = document.getElementById('corporateSearch').value;
            if (!query || query.length < 2) {
                alert('Please enter at least 2 characters to search');
                return;
            }

            const resultsDiv = document.getElementById('corporateSearchResults');
            resultsDiv.innerHTML = '<div class="lookup-results__state"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';
            resultsDiv.classList.remove('d-none');

            fetch('{{ route("dashboard.corporate.search") }}?q=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(corporates => {
                    if (corporates.length === 0) {
                        resultsDiv.innerHTML = '<div class="lookup-results__state">No corporates found</div>';
                        return;
                    }

                    let html = '';
                    corporates.forEach(corporate => {
                        const discount = corporate.discount_value ? (corporate.discount_type === 'percentage' ? corporate.discount_value + '%' : corporate.discount_value) : '0';
                        html += `
                            <button type="button" class="lookup-result-item"
                                data-corporate-id="${corporate.id}"
                                data-name="${escapeAttribute(corporate.name)}"
                                data-discount="${escapeAttribute(discount)}"
                                onclick="selectCorporateFromResult(this)">
                                <span class="lookup-result-item__icon lookup-result-item__icon--corporate">
                                    <i class="bi bi-building"></i>
                                </span>
                                <span class="lookup-result-item__content">
                                    <span class="lookup-result-item__title">${escapeHtml(corporate.name)}</span>
                                    <span class="lookup-result-item__meta">Discount: ${escapeHtml(discount)}</span>
                                </span>
                                <span class="lookup-result-item__cta">
                                    <i class="bi bi-arrow-right"></i>
                                </span>
                            </button>
                        `;
                    });
                    resultsDiv.innerHTML = html;
                })
                .catch(error => {
                    resultsDiv.innerHTML = '<div class="lookup-results__state text-danger">Error searching corporates</div>';
                    console.error(error);
                });
        }

        function selectCorporate(id, name, discount) {
            document.getElementById('corporateId').value = id;
            document.getElementById('displayCorporateName').textContent = name;
            document.getElementById('displayCorporateDiscount').textContent = 'Discount: ' + discount;
            document.getElementById('corporateInfoDisplay').classList.remove('d-none');
            document.getElementById('corporateSearch').value = '';
            document.getElementById('corporateSearchResults').classList.add('d-none');
        }

        function clearCorporateInfo() {
            document.getElementById('corporateId').value = '';
            document.getElementById('displayCorporateName').textContent = '';
            document.getElementById('displayCorporateDiscount').textContent = '';
            document.getElementById('corporateInfoDisplay').classList.add('d-none');
        }

        function saveReservation(status) {
            const form = document.getElementById('reservationForm');

            // Validation
            if (!document.getElementById('checkInDate').value) {
                alert('{{ __('dashboard.please_select_check_in_date') }}');
                return;
            }
            if (!document.getElementById('checkOutDate').value) {
                alert('{{ __('dashboard.please_select_check_out_date') }}');
                return;
            }
            if (!document.getElementById('selectedUnitId').value) {
                alert('{{ __('dashboard.please_select_unit') }}');
                return;
            }

            // Convert button action to correct status value.
            // Plain updates keep the existing reservation status on the backend,
            // so do not submit "update" as a reservation status.
            let statusValue = null;
            if (status === 'check_in') {
                statusValue = 'checked_in';
            } else if (status === 'check_out') {
                statusValue = 'checked_out';
            }

            // Remove existing inputs if any
            const existingAction = document.getElementById('reservationAction');
            if (existingAction) {
                existingAction.remove();
            }
            const existingStatus = document.getElementById('reservationStatus');
            if (existingStatus) {
                existingStatus.remove();
            }

            // Add action to form
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.id = 'reservationAction';
            actionInput.name = 'reservation_action';
            actionInput.value = status;
            form.appendChild(actionInput);

            if (statusValue) {
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.id = 'reservationStatus';
                statusInput.name = 'status';
                statusInput.value = statusValue;
                form.appendChild(statusInput);
            }

            // Submit
            form.submit();
        }

        function createNewGuest() {
            // Get values
            const firstName = document.getElementById('newGuestFirstName').value;
            const lastName = document.getElementById('newGuestLastName').value;
            const secondName = document.getElementById('newGuestSecondName').value;
            const middleName = document.getElementById('newGuestMiddleName').value;
            const mobile = document.getElementById('newGuestMobile').value;
            const dialCode = document.getElementById('newGuestDialCode').value;

            if (!firstName || !lastName || !mobile) {
                alert('{{ __('dashboard.please_fill_required_fields') }}');
                return;
            }

            // Prepare guest data
            const guestData = {
                first_name: firstName,
                last_name: lastName,
                second_name: secondName,
                middle_name: middleName,
                gender: document.getElementById('newGuestGender').value,
                date_of_birth: document.getElementById('newGuestDob').value,
                guest_class_id: document.getElementById('newGuestClass').value,
                nationality: document.getElementById('newGuestNationality').value,
                nationality_code: document.getElementById('newGuestNationalityCode').value,
                guest_type: document.getElementById('newGuestType').value,
                id_type: document.getElementById('newGuestIdType').value,
                id_number: document.getElementById('newGuestIdNumber').value,
                id_issue_country: document.getElementById('newGuestIdIssueCountry').value,
                id_expiry_date: document.getElementById('newGuestIdExpiryDate').value,
                visa_number: document.getElementById('newGuestVisaNumber').value,
                arrival_from: document.getElementById('newGuestArrivalFrom').value,
                id_serial: document.getElementById('newGuestIdSerial').value,
                mobile_dial_code: dialCode,
                mobile_number: mobile,
                email: document.getElementById('newGuestEmail').value,
                work_place: document.getElementById('newGuestWorkPlace').value,
                work_phone: document.getElementById('newGuestWorkPhone').value,
                address: document.getElementById('newGuestAddress').value,
                car_license_plate: document.getElementById('newGuestCarPlate').value,
                _token: '{{ csrf_token() }}'
            };

            // Send AJAX request to store guest
            fetch('{{ route('dashboard.guest.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(guestData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Fill hidden form fields with guest data
                        document.getElementById('guestFirstName').value = firstName;
                        document.getElementById('guestLastName').value = lastName;
                        document.getElementById('guestMobile').value = dialCode + ' ' + mobile;
                        document.getElementById('guestEmail').value = document.getElementById('newGuestEmail').value;
                        document.getElementById('guestNationality').value = document.getElementById(
                            'newGuestNationality').value;
                        document.getElementById('guestIdType').value = document.getElementById('newGuestIdType').value;
                        document.getElementById('guestIdNumber').value = document.getElementById('newGuestIdNumber')
                            .value;
                        document.getElementById('guestGender').value = document.getElementById('newGuestGender').value;
                        document.getElementById('guestDob').value = document.getElementById('newGuestDob').value;
                        document.getElementById('guestAddress').value = document.getElementById('newGuestAddress')
                            .value;

                        selectGuest(data.guest.id, data.guest.name, data.guest.mobile, data.guest.email || '');

                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('newGuestModal'));
                        modal.hide();

                        // Reset modal form
                        document.getElementById('newGuestFirstName').value = '';
                        document.getElementById('newGuestLastName').value = '';
                        document.getElementById('newGuestSecondName').value = '';
                        document.getElementById('newGuestMiddleName').value = '';
                        document.getElementById('newGuestMobile').value = '';
                        document.getElementById('newGuestEmail').value = '';
                        document.getElementById('newGuestCarPlate').value = '';
                        document.getElementById('newGuestNationalityCode').value = '';
                        document.getElementById('newGuestIdIssueCountry').value = '';
                        document.getElementById('newGuestIdExpiryDate').value = '';
                        document.getElementById('newGuestVisaNumber').value = '';
                        document.getElementById('newGuestArrivalFrom').value = '';
                    } else {
                        alert('Error: ' + (data.message || 'Failed to create guest'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('{{ __('messages.error_occurred') }}');
                });
        }

        function createNewCorporate() {
            const name = document.getElementById('corporateName').value;

            if (!name) {
                alert('{{ __('dashboard.please_fill_required_fields') }}');
                return;
            }

            const corporateData = {
                name: name,
                postal_code: document.getElementById('corporatePostalCode').value,
                vat_registration_number: document.getElementById('corporateVatNumber').value,
                commercial_registration_number: document.getElementById('corporateCommercialRegNumber').value,
                discount_type: document.getElementById('corporateDiscountType').value,
                discount_value: document.getElementById('corporateDiscountValue').value,
                country: document.getElementById('corporateCountry').value,
                city: document.getElementById('corporateCity').value,
                district: document.getElementById('corporateDistrict').value,
                street: document.getElementById('corporateStreet').value,
                building_number: document.getElementById('corporateBuildingNumber').value,
                secondary_number: document.getElementById('corporateSecondaryNumber').value,
                address: document.getElementById('corporateAddress').value,
                email: document.getElementById('corporateEmail').value,
                phone: document.getElementById('corporatePhone').value,
                contact_person_name: document.getElementById('corporateContactPerson').value,
                contact_person_phone: document.getElementById('corporateContactPersonPhone').value,
            };

            fetch('{{ route('dashboard.corporate.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(corporateData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let corporateIdField = document.getElementById('corporateId');
                        if (!corporateIdField) {
                            corporateIdField = document.createElement('input');
                            corporateIdField.type = 'hidden';
                            corporateIdField.id = 'corporateId';
                            corporateIdField.name = 'corporate_id';
                            document.getElementById('reservationForm').appendChild(corporateIdField);
                        }
                        corporateIdField.value = data.corporate.id;

                        const corporateSelect = document.getElementById('corporateSelect');
                        if (corporateSelect) {
                            const option = document.createElement('option');
                            option.value = data.corporate.id;
                            option.text = data.corporate.name;
                            option.selected = true;
                            corporateSelect.appendChild(option);
                        }

                        const corporateInfoDisplay = document.getElementById('corporateInfoDisplay');
                        if (corporateInfoDisplay) {
                            document.getElementById('displayCorporateName').textContent = data.corporate.name;
                            const discountType = corporateData.discount_type === 'percentage' ? '%' : '';
                            const discountValue = corporateData.discount_value || 0;
                            const discountLabel = '{{ __('dashboard.discount') }}';
                            document.getElementById('displayCorporateDiscount').textContent =
                                discountValue + discountType + ' ' + discountLabel;
                            corporateInfoDisplay.classList.remove('d-none');
                        }

                        const modal = bootstrap.Modal.getInstance(document.getElementById('newCorporateModal'));
                        modal.hide();

                        document.getElementById('corporateName').value = '';
                        document.getElementById('corporatePostalCode').value = '';
                        document.getElementById('corporateVatNumber').value = '';
                        document.getElementById('corporateCommercialRegNumber').value = '';
                        document.getElementById('corporateDiscountType').value = '';
                        document.getElementById('corporateDiscountValue').value = '0';
                        document.getElementById('corporateCountry').value = '';
                        document.getElementById('corporateCity').value = '';
                        document.getElementById('corporateDistrict').value = '';
                        document.getElementById('corporateStreet').value = '';
                        document.getElementById('corporateBuildingNumber').value = '';
                        document.getElementById('corporateSecondaryNumber').value = '';
                        document.getElementById('corporateAddress').value = '';
                        document.getElementById('corporateEmail').value = '';
                        document.getElementById('corporatePhone').value = '';
                        document.getElementById('corporateContactPerson').value = '';
                        document.getElementById('corporateContactPersonPhone').value = '';
                    } else {
                        alert('Error: ' + (data.message || 'Failed to create corporate'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('{{ __('messages.error_occurred') }}');
                });
        }

        function clearGuestInfo() {
            document.getElementById('guestFirstName').value = '';
            document.getElementById('guestLastName').value = '';
            document.getElementById('guestMobile').value = '';
            document.getElementById('guestEmail').value = '';
            document.getElementById('guestNationality').value = '';
            document.getElementById('guestIdType').value = '';
            document.getElementById('guestIdNumber').value = '';
            document.getElementById('guestGender').value = '';
            document.getElementById('guestDob').value = '';
            document.getElementById('guestAddress').value = '';

            const guestIdField = document.getElementById('guestId');
            if (guestIdField) {
                guestIdField.value = '';
            }

            const guestInfoDisplay = document.getElementById('guestInfoDisplay');
            if (guestInfoDisplay) {
                guestInfoDisplay.classList.add('d-none');
            }
        }

        function clearCorporateInfo() {
            const corporateIdField = document.getElementById('corporateId');
            if (corporateIdField) {
                corporateIdField.value = '';
            }

            const corporateInfoDisplay = document.getElementById('corporateInfoDisplay');
            if (corporateInfoDisplay) {
                corporateInfoDisplay.classList.add('d-none');
            }

            const corporateSelect = document.getElementById('corporateSelect');
            if (corporateSelect) {
                corporateSelect.value = '';
            }
        }

        // Initialize for edit mode
        document.addEventListener('DOMContentLoaded', function() {
            const unitId = document.getElementById('selectedUnitId').value;
            if (unitId) {
                // Show unit info section
                document.getElementById('selectedUnitInfo').style.display = 'block';

                // Fetch rates for existing unit
                const reservationType = document.getElementById('reservationType').value;
                const checkInDate = document.getElementById('checkInDate').value;
                const checkOutDate = document.getElementById('checkOutDate').value;

                let ratesUrl = '{{ route('dashboard.reservation.get_rates') }}?unit_id=' + unitId +
                    '&reservation_type=' + reservationType;
                if (checkInDate) ratesUrl += '&check_in=' + checkInDate;
                if (checkOutDate) ratesUrl += '&check_out=' + checkOutDate;

                fetch(ratesUrl)
                    .then(response => response.json())
                    .then(data => {
                        window.selectedUnitRates = data;
                    })
                    .catch(error => console.error('Error fetching rates:', error));

                // Set guest info if exists
                const guestId = document.getElementById('guestId').value;
                if (guestId) {
                    const guestInfoDisplay = document.getElementById('guestInfoDisplay');
                    if (guestInfoDisplay) {
                        guestInfoDisplay.classList.remove('d-none');
                    }
                }

                // Set corporate info if exists
                const corporateId = document.getElementById('corporateId').value;
                if (corporateId) {
                    const corporateInfoDisplay = document.getElementById('corporateInfoDisplay');
                    if (corporateInfoDisplay) {
                        corporateInfoDisplay.classList.remove('d-none');
                    }
                }

                // Trigger penalty calculation if penalty is selected
                const penaltySelect = document.getElementById('penaltySelect');
                const penaltyAmountInput = document.getElementById('penaltyAmount');

                // If penalty is selected but amount is 0, auto-fill from dropdown
                if (penaltySelect && penaltySelect.value && (!penaltyAmountInput.value || parseFloat(
                        penaltyAmountInput.value) === 0)) {
                    const selectedOption = penaltySelect.options[penaltySelect.selectedIndex];
                    if (selectedOption && selectedOption.dataset.value) {
                        penaltyAmountInput.value = selectedOption.dataset.value;
                    }
                }

                // Initial calculation uses the saved edit values. Rate recalculation runs only
                // after the user changes dates, reservation type, rate option, or unit.
                calculateFinancials();
            }

            // Penalty selection handler
            const penaltySelect = document.getElementById('penaltySelect');
            if (penaltySelect) {
                penaltySelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const penaltyAmountInput = document.getElementById('penaltyAmount');
                    if (selectedOption && selectedOption.dataset.value) {
                        penaltyAmountInput.value = selectedOption.dataset.value;
                    } else {
                        penaltyAmountInput.value = 0;
                    }
                    calculateFinancials();
                });
            }
        });
    </script>
@endpush
