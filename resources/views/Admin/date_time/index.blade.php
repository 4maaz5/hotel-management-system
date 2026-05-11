@extends('layouts.app')

@section('title', 'Date/Time Settings')

@section('content')
    <div class="container mt-4 bg-white p-3" style="border-radius:15px;">

        <!-- Page Category -->
        <div class="text-muted fw-semibold mb-2">
            {{ __('dashboard.general_settings') }}
        </div>

        <!-- Page Header -->
        <div class="mb-4">
            <h2 class="fw-bold">
                {{ __('dashboard.date_time_settings') }}
            </h2>
            <p class="mb-0 text-white-50">
                {{ __('dashboard.set_date_and_time_settings_for_the_system') }}
            </p>
        </div>

        <!-- Info Message -->
        <div class="alert alert-info d-flex align-items-center mb-4">
            <i class="bi bi-info-circle me-2"></i>
            <span>
                {{ __('dashboard.time_zone') }}:
                @php
                    $tz = new DateTimeZone(system_timezone());
                    $offset = $tz->getOffset(new DateTime('now', $tz)) / 3600;
                @endphp

                (UTC{{ $offset >= 0 ? '+' : '' }}{{ sprintf('%02d:00', $offset) }})
                {{ str_replace('_', ' ', explode('/', system_timezone())[1]) }}
            </span>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('setup-sidebar.date.update') }}">
            @csrf
            <div class="row mb-4">

                <!-- Date Format -->
                <div class="col-lg-3 col-md-4">
                    <label class="form-label fw-semibold">
                        {{ __('dashboard.date_format') }}
                        <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" required name="date_format">
                        <option value="">
                            {{ __('dashboard.select_date_format') }}
                        </option>

                        <option value="d-m-Y"
                            {{ old('date_format', $dateTimeSetting->date_format ?? '') == 'd-m-Y' ? 'selected' : '' }}>
                            {{ __('dashboard.dd_mm_yyyy') }}
                        </option>

                        <option value="m-d-Y"
                            {{ old('date_format', $dateTimeSetting->date_format ?? '') == 'm-d-Y' ? 'selected' : '' }}>
                            {{ __('dashboard.mm_dd_yyyy') }}
                        </option>

                        <option value="Y-m-d"
                            {{ old('date_format', $dateTimeSetting->date_format ?? '') == 'Y-m-d' ? 'selected' : '' }}>
                            {{ __('dashboard.yyyy_mm_dd') }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Time Format -->
            <div class="row mb-4">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="time_format" id="am_pm" value="12"
                        {{ old('time_format', $dateTimeSetting->time_format ?? '') == '12' ? 'checked' : '' }}>
                    <label class="form-check-label" for="am_pm">
                        {{ __('dashboard.am_pm') }}
                    </label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="radio" name="time_format" id="24_hours" value="24"
                        {{ old('time_format', $dateTimeSetting->time_format ?? '') == '24' ? 'checked' : '' }}>
                    <label class="form-check-label" for="24_hours">
                        {{ __('dashboard.24_hours') }}
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-end gap-3">
                @can('date_setting.edit')
                    <button type="submit" class="btn btn-primary">
                        {{ __('dashboard.save_changes') }}
                    </button>
                @endcan

            </div>
        </form>
    </div>

@endsection
