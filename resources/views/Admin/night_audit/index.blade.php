@extends('layouts.app')

@php
    $theme = \App\Models\ThemeCustomization::getTheme();
@endphp

@section('title', __('dashboard.night_audit_settings'))

<style>
    .page-category {
        font-size: 0.875rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .page-header__title {
        font-size: 1.75rem;
        font-weight: 600;
        color: {{ $theme->dashboard_card_title_color }};
        margin-bottom: 0.5rem;
    }

    .night-audit-card {
        background: {{ $theme->card_bg_color }};
        border-radius: 8px;
        border: 1px solid {{ $theme->card_border_color }};
        overflow: hidden;
    }

    .night-audit-card .card-body {
        padding: 1.5rem;
    }

    .night-audit-footer {
        background: {{ $theme->card_bg_color }};
        border-top: 1px solid {{ $theme->card_border_color }};
        padding: 1rem 1.5rem;
    }
</style>

@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">
        <div class="container mt-4">

            <div class="page-category">
                {{ __('dashboard.rules') }}
            </div>

            <div class="mb-4">
                <h2 class="page-header__title">{{ __('dashboard.night_audit_settings') }}</h2>
            </div>

            <div class="night-audit-card shadow-sm">
                <form action="{{ route('setup-sidebar.night_audit.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="nightAuditSwitch" name="is_active"
                                    {{ $settings->is_active ? 'checked' : '' }}>
                            </div>
                            <label class="ms-2 fw-semibold" for="nightAuditSwitch">
                                {{ __('dashboard.switch_on_night_audit') }}
                            </label>
                        </div>

                        <div class="text-muted mb-4" style="max-width: 750px;">
                            {{ __('dashboard.night_audit_is_a_system_that_relies_on_the_responsible_employee_reviewing_all_activities_of_the_property_after_the_end_of_the_day_during_the_early_hours_of_the_new_day_After_reviewing_the_entire_day_the_transactions_will_be_locked_to_prevent_the_addition_of_vouchers_or_rentals_for_any_reviewed_day') }}
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">
                                    {{ __('dashboard.allowance_period') }} ({{ __('dashboard.hour') }})
                                </label>
                                <select class="form-select" name="allowance_period">
                                    @for ($i = 0; $i <= 24; $i++)
                                        <option value="{{ $i }}"
                                            {{ $settings->allowance_period == $i ? 'selected' : '' }}>{{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">
                                    {{ __('dashboard.cancelation_threshold') }} ({{ __('dashboard.hour') }})
                                </label>
                                <select class="form-select" name="cancellation_threshold">
                                    <option value="">{{ __('dashboard.select_hour') }}</option>
                                    @for ($i = 1; $i <= 24; $i++)
                                        <option value="{{ $i }}"
                                            {{ $settings->cancellation_threshold == $i ? 'selected' : '' }}>
                                            {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="night-audit-footer d-flex justify-content-end gap-2">

                            <button type="submit" class="btn btn-primary px-4">
                                {{ __('dashboard.save') }}
                            </button>


                    </div>
                </form>
            </div>

        </div>
    </main>
@endsection
