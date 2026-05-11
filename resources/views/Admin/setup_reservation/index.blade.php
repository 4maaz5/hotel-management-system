@extends('layouts.app')

@php
    $theme = \App\Models\ThemeCustomization::getTheme();
@endphp

@section('title', 'Setup Reservation')

<style>
    .res-rules {
        background: {{ $theme->card_bg_color }};
        border-radius: 8px;
        padding: 20px;
        border: 1px solid {{ $theme->card_border_color }};
    }

    .res-rules__title {
        font-size: 18px;
        font-weight: 600;
        color: {{ $theme->dashboard_card_title_color }};
        margin-bottom: 15px;
        margin-top: 25px;
    }

    .res-rules__title:first-child {
        margin-top: 0;
    }

    .u-align-center {
        text-align: center;
    }

    .u-d-flex {
        display: flex;
    }

    .u-mt-10 {
        margin-top: 10px;
    }

    .u-mb-3 {
        margin-bottom: 3px;
    }

    .u-m-start-5 {
        margin-left: 5px;
    }

    .u-m-start-10 {
        margin-left: 10px;
    }

    .margin-top {
        margin-top: 20px;
    }

    .u-flex-end {
        justify-content: flex-end;
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }

    .switch-label {
        font-size: 14px;
        margin-left: 10px;
    }

    .switch-label--sm {
        font-size: 13px;
    }

    .switch-label--dark {
        color: {{ $theme->dashboard_card_title_color }};
    }

    .switch-label-info {
        display: block;
        font-size: 12px;
        color: #6c757d;
        margin-top: 4px;
    }

    .form__label-icon {
        width: 14px;
        height: 14px;
        vertical-align: middle;
        cursor: pointer;
    }

    .timepicker-container {
        position: relative;
    }

    .page-category {
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 5px;
    }

    .page-header__title {
        font-size: 24px;
        font-weight: 600;
        color: {{ $theme->dashboard_card_title_color }};
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

    .view-btn {
        padding: 8px 16px;
        border: 1px solid {{ $theme->card_border_color }};
        background: {{ $theme->card_bg_color }};
        color: {{ $theme->dashboard_card_text_color }};
        cursor: pointer;
        transition: all 0.2s;
    }

    .view-btn:first-child {
        border-radius: 4px 0 0 4px;
    }

    .view-btn:last-child {
        border-radius: 0 4px 4px 0;
    }

    .view-btn.active {
        background: {{ $theme->button_primary_color }};
        color: white;
        border-color: {{ $theme->button_primary_color }};
    }

    .view-btn:not(:last-child) {
        border-right: none;
    }

    .view-btn i {
        font-size: 16px;
    }

    .custom-switch {
        padding-left: 2.5rem;
    }

    .custom-switch .form-check-input {
        width: 2.5rem;
        height: 1.25rem;
        cursor: pointer;
    }
</style>

@section('content')
    <main class="bg-white p-3" style="border-radius:15px;">
        <!-- Page Header -->
        <div class="page-category">{{ __('dashboard.rules') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.reservations') }}</h2>
                <div class="page-header__subtitle">{{ __('dashboard.set_reservation_rules_for_properties') }}</div>
            </div>
        </div>

        <form method="POST" action="{{ route('setup-sidebar.setup_reservation.update') }}">
            @csrf
            @method('PUT')

            <div class="res-rules">
                <!-- Reservations View -->
                <div class="res-rules__title">{{ __('dashboard.reservations_view') }}</div>

                <div class="mb-4">
                    <label class="form-label">{{ __('dashboard.default_mode') }}</label>
                    <div class="u-d-flex">
                        <button type="button"
                            class="view-btn {{ ($settings->default_view ?? 'list') === 'calendar' ? '' : 'active' }}"
                            onclick="setView('list', this)">
                            <i class="fas fa-list"></i>
                        </button>
                        <button type="button"
                            class="view-btn {{ ($settings->default_view ?? '') === 'calendar' ? 'active' : '' }}"
                            onclick="setView('calendar', this)">
                            <i class="fas fa-calendar"></i>
                        </button>
                    </div>
                    <input type="hidden" name="default_view" id="default_view"
                        value="{{ ($settings->default_view ?? 'list') === 'calendar' ? 'calendar' : 'list' }}">
                </div>

                <div class="alert alert-info mb-0">
                    This page now controls only the reservation index UI. Saving here updates the default
                    reservation list view without changing create, edit, pricing, timing, or other reservation
                    business rules.
                </div>

                {{--
                    Hidden reservation rule fields kept out of the frontend for now:
                    - check_in_time
                    - check_out_time
                    - grace_period
                    - enable_previous_day_calculation
                    - previous_day_before
                    - auto_extend_daily
                    - auto_extend_monthly
                    - auto_extend_after
                    - restrict_unit_change
                    - unit_change_reason_required
                    - unit_allowance_period
                    - enable_unconfirmed_reservation
                    - enable_monthly_reservation
                    - auto_change_unconfirmed_to_noshow
                    - auto_noshow_time
                    - auto_noshow_reason_id
                    - auto_cancel_ota_reservation
                    - auto_cancel_reason_id
                    - enable_mandatory_checkin
                    - enable_close_reservation_with_balance
                    - reset_number_annually
                    - use_custom_rate_last_night
                --}}

                <!-- Footer Buttons -->
                <div class="u-flex-end">
                    @can('setup_reservation.edit')
                        <button type="submit" class="btn btn-primary"
                            style="background-color: {{ $theme->button_primary_color }}; border-color: {{ $theme->button_primary_color }}">
                            {{ __('dashboard.save_changes') }}
                        </button>
                    @endcan

                </div>
            </div>
        </form>
    </main>

    <script>
        function setView(view, button) {
            document.getElementById('default_view').value = view;
            document.querySelectorAll('.view-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            button.classList.add('active');
        }
    </script>
@endsection
