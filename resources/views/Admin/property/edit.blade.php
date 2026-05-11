@extends('layouts.app')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    :root {
        --primary-color: #1a73e8;
        --danger-color: #dc3545;
        --success-color: #28a745;
        --warning-color: #ffc107;
        --light-bg: #f8f9fa;
        --border-color: #dee2e6;
    }

    body {
        background-color: #f5f5f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .page-category {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .page-header__title {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .page-header__subtitle {
        color: #6c757d;
        font-size: 0.95rem;
    }

    /* Form Styles */
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #495057;
    }

    .form__star {
        color: var(--danger-color);
        margin-left: 3px;
    }

    .form-control,
    .form-select {
        border-radius: 0.375rem;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color);
        transition: all 0.3s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(26, 115, 232, 0.25);
    }

    .form__input-msg {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 0.25rem;
        min-height: 1.25rem;
    }

    /* Switch Toggle */
    .form-switch .form-check-input {
        width: 3em;
        height: 1.5em;
        margin-right: 10px;
    }

    .form-switch .form-check-label {
        font-weight: 500;
        color: #495057;
    }

    /* Panel/Collapse Styles */
    .collapse-panel {
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        overflow: hidden;
    }

    .collapse-header {
        background-color: var(--light-bg);
        padding: 1rem 1.25rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid transparent;
    }

    .collapse-header[aria-expanded="true"] {
        border-bottom-color: var(--border-color);
    }

    .collapse-header:hover {
        background-color: #e9ecef;
    }

    .collapse-title {
        font-weight: 600;
        color: #495057;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .collapse-icon {
        color: var(--primary-color);
    }

    .collapse-content {
        padding: 1.5rem;
        background-color: white;
    }

    /* Image Upload */
    .img-upload {
        border: 2px dashed #dee2e6;
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }

    .img-upload:hover {
        border-color: var(--primary-color);
        background-color: rgba(26, 115, 232, 0.05);
    }

    .img-upload__icon {
        font-size: 2rem;
        color: #6c757d;
        margin-bottom: 1rem;
    }

    .img-upload__label {
        color: #495057;
        font-weight: 500;
    }

    .img-upload__size-info {
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 0.5rem;
    }

    /* Phone Number Input */
    .phone-input-group {
        display: flex;
    }

    .dial-code {
        background-color: var(--light-bg);
        border: 1px solid var(--border-color);
        border-right: none;
        padding: 0.75rem 1rem;
        border-radius: 0.375rem 0 0 0.375rem;
        color: #495057;
        min-width: 60px;
        text-align: center;
    }

    .phone-input {
        border-radius: 0 0.375rem 0.375rem 0;
        flex: 1;
    }

    /* Multi-language Input */
    .multi-lang-input {
        position: relative;
    }

    .lang-switch-btn {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        background-color: var(--primary-color);
        color: white;
        border: none;
        border-radius: 0.25rem;
        padding: 0.25rem 0.75rem;
        font-size: 0.85rem;
    }

    .lang-switch-btn:hover {
        background-color: #0d6efd;
    }

    /* Buttons */
    .btn-primary-custom {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        padding: 0.75rem 2rem;
        font-weight: 500;
    }

    .btn-danger-border {
        background-color: transparent;
        border: 2px solid var(--danger-color);
        color: var(--danger-color);
        padding: 0.75rem 2rem;
        font-weight: 500;
    }

    .btn-danger-border:hover {
        background-color: var(--danger-color);
        color: white;
    }

    /* Property Info Display */
    .property-info-display {
        background-color: var(--light-bg);
        border-radius: 0.375rem;
        padding: 0.75rem 1rem;
        margin-bottom: 0.5rem;
    }

    .property-info-label {
        font-weight: 500;
        color: #495057;
    }

    .property-info-value {
        color: #212529;
        font-weight: 600;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .collapse-content {
            padding: 1rem;
        }

        .btn-primary-custom,
        .btn-danger-border {
            padding: 0.75rem 1rem;
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }
</style>
@section('content')
    <div class="container-fluid py-4 bg-white " style="border-radius:10px;">
        <!-- Page Header -->
        <div class="page-category">{{ __('dashboard.company_properties') }}</div>
        <div class="page-header">
            <h2 class="page-header__title">{{ __('dashboard.edit_property') }}</h2>
            <div class="page-header__subtitle">{{ __('dashboard.edit_the_information_of_a_property') }}</div>
        </div>

        <!-- Main Form -->
        <form id="propertyForm" class="needs-validation" novalidate
            action="{{ isset($property) ? route('setup-sidebar.property.update', $property->id) : route('setup-sidebar.property.store') }}"
            method="POST" enctype="multipart/form-data">
            @csrf
            @if (isset($property))
                @method('PUT')
            @endif

            <!-- Active Property Switch -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="activeProperty" name="status" value="ACTIVE"
                            {{ old('status', isset($property) ? $property->status : 'ACTIVE') == 'ACTIVE' ? 'checked' : '' }}>
                        <label class="form-check-label" for="activeProperty">{{ __('dashboard.active_property') }}</label>
                    </div>
                    <input type="hidden" name="status" value="INACTIVE" id="statusHidden">
                </div>
            </div>

            <!-- Main Data Panel -->
            <div class="collapse-panel">
                <div class="collapse-header" data-bs-toggle="collapse" data-bs-target="#mainDataCollapse"
                    aria-expanded="true">
                    <div class="collapse-title">
                        <i class="fas fa-minus collapse-icon"></i>
                        <span>{{ __('dashboard.main_data') }}</span>
                    </div>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="collapse collapse-content show" id="mainDataCollapse">
                    <div class="row g-3">
                        <!-- Property Name English -->
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.property_name') }} (English) <span
                                    class="form__star">*</span></label>
                            <input type="text" class="form-control @error('property_name_en') is-invalid @enderror"
                                name="property_name_en"
                                placeholder="{{ __('dashboard.property_name') }} ({{ __('dashboard.english') }})"
                                value="{{ old('property_name_en', isset($property) ? $property->property_name_en : '') }}"
                                required>
                            @error('property_name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form__input-msg"></div>
                        </div>

                        <!-- Property Name Arabic -->
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.property_name') }} ({{ __('dashboard.arabic') }})
                                <span class="form__star">*</span></label>
                            <input type="text" class="form-control @error('property_name_ar') is-invalid @enderror"
                                name="property_name_ar"
                                placeholder="{{ __('dashboard.property_name') }} ({{ __('dashboard.arabic') }})"
                                value="{{ old('property_name_ar', isset($property) ? $property->property_name_ar : '') }}"
                                required>
                            @error('property_name_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form__input-msg"></div>
                        </div>

                        <!-- Report Name English -->
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.report_name') }} ({{ __('dashboard.english') }})
                                <span class="form__star">*</span></label>
                            <input type="text" class="form-control @error('report_name_en') is-invalid @enderror"
                                name="report_name_en"
                                placeholder="{{ __('dashboard.report_name') }} ({{ __('dashboard.english') }})"
                                value="{{ old('report_name_en', isset($property) ? $property->report_name_en : '') }}"
                                required>
                            @error('report_name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form__input-msg"></div>
                        </div>

                        <!-- Report Name Arabic -->
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.report_name') }} ({{ __('dashboard.arabic') }})
                                <span class="form__star">*</span></label>
                            <input type="text" class="form-control @error('report_name_ar') is-invalid @enderror"
                                name="report_name_ar"
                                placeholder="{{ __('dashboard.report_name') }} ({{ __('dashboard.arabic') }})"
                                value="{{ old('report_name_ar', isset($property) ? $property->report_name_ar : '') }}"
                                required>
                            @error('report_name_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form__input-msg"></div>
                        </div>

                        <!-- Property Type -->
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.property_type') }} <span
                                    class="form__star">*</span></label>
                            <select class="form-select @error('property_type_id') is-invalid @enderror"
                                name="property_type_id" required>
                                <option value="" selected disabled>{{ __('dashboard.select_type') }}</option>
                                @foreach ($propertyTypes as $type)
                                    <option value="{{ $type->id }}"
                                        {{ old('property_type_id', isset($property) ? $property->property_type_id : '') == $type->id ? 'selected' : '' }}>
                                        {{ $type->code }}
                                    </option>
                                @endforeach
                            </select>
                            @error('property_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form__input-msg"></div>
                        </div>

                        <!-- Logo Upload -->
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.logo') }}</label>
                            <div class="img-upload" onclick="document.getElementById('logoUpload').click()">
                                <div class="img-upload__icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div class="img-upload__label">{{ __('dashboard.upload_logo') }}</div>
                                <div class="img-upload__size-info">{{ __('dashboard.minimum_size') }} 400px*300px</div>
                                <input type="file" accept="image/*" class="d-none" id="logoUpload" name="logo">
                                @if (isset($property) && $property->logo_url)
                                    <input type="hidden" name="existing_logo" value="{{ $property->logo_url }}">
                                    <div class="mt-2">
                                        <img src="{{ Storage::url($property->logo_url) }}" alt="Logo" width="50"
                                            height="50">
                                    </div>
                                @endif
                            </div>
                            @error('logo')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                            <div class="form__input-msg"></div>
                        </div>

                        <!-- Account Information -->
                        <div class="col-md-6">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="property-info-display">
                                        <span
                                            class="property-info-label">{{ __('dashboard.nazeel_account_status') }}:</span>
                                        <span class="property-info-value text-success">
                                            @if (isset($property) && $property->account_expiry_date && $property->account_expiry_date->isFuture())
                                                {{ __('dashboard.valid') }}
                                            @elseif(isset($property))
                                                {{ __('dashboard.expired') }}
                                            @else
                                                {{ __('dashboard.pending') }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="property-info-display">
                                        <span
                                            class="property-info-label">{{ __('dashboard.nazeel_account_expiry_date') }}:</span>
                                        <span class="property-info-value">
                                            <input type="date" class="form-control form-control-sm"
                                                name="account_expiry_date"
                                                value="{{ old('account_expiry_date', isset($property) ? $property->account_expiry_date?->format('Y-m-d') : '') }}">
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="property-info-display">
                                        <span class="property-info-label">{{ __('dashboard.property_code') }}:</span>
                                        <span class="property-info-value">
                                            <input type="text" class="form-control form-control-sm"
                                                name="property_code"
                                                value="{{ old('property_code', isset($property) ? $property->property_code : '') }}"
                                                required>
                                            @error('property_code')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Units Count -->
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.active_units') }} <span
                                    class="form__star">*</span></label>
                            <input type="number" class="form-control @error('active_units_count') is-invalid @enderror"
                                name="active_units_count" placeholder="{{ __('dashboard.active_units') }}"
                                value="{{ old('active_units_count', isset($property) ? $property->active_units_count : 0) }}"
                                min="0" required>
                            @error('active_units_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form__input-msg"></div>
                        </div>

                        <!-- Max Units Count -->
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.max_units') }} <span
                                    class="form__star">*</span></label>
                            <input type="number" class="form-control @error('max_units_count') is-invalid @enderror"
                                name="max_units_count" placeholder="{{ __('dashboard.max_units') }}"
                                value="{{ old('max_units_count', isset($property) ? $property->max_units_count : 0) }}"
                                min="0" required>
                            @error('max_units_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form__input-msg"></div>
                        </div>

                        <!-- Account Version -->
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.account_version') }}</label>
                            <select class="form-select @error('account_version') is-invalid @enderror"
                                name="account_version">
                                <option value="" selected disabled>{{ __('dashboard.account_version') }}</option>
                                <option value="BASIC"
                                    {{ old('account_version', isset($property) ? $property->account_version : '') == 'BASIC' ? 'selected' : '' }}>
                                    {{ __('dashboard.basic') }}</option>
                                {{-- <option value="PREMIUM"
                                    {{ old('account_version', isset($property) ? $property->account_version : '') == 'PREMIUM' ? 'selected' : '' }}>
                                    {{ __('dashboard.premium') }}</option>
                                <option value="ENTERPRISE"
                                    {{ old('account_version', isset($property) ? $property->account_version : '') == 'ENTERPRISE' ? 'selected' : '' }}>
                                    {{ __('dashboard.enterprise') }}</option> --}}
                            </select>
                            @error('account_version')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form__input-msg"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Data Panel -->
            <div class="collapse-panel">
                <div class="collapse-header" data-bs-toggle="collapse" data-bs-target="#locationDataCollapse"
                    aria-expanded="true">
                    <div class="collapse-title">
                        <i class="fas fa-minus collapse-icon"></i>
                        <span>{{ __('dashboard.location_data') }}</span>
                    </div>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="collapse collapse-content show" id="locationDataCollapse">
                    <div class="row g-3">
                        <!-- Country, Region, City, District -->
                        <div class="col-md-6">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.country') }} <span
                                            class="form__star">*</span></label>
                                    <select class="form-select @error('country_id') is-invalid @enderror"
                                        name="country_id" required>
                                        <option value="" selected disabled>{{ __('dashboard.country') }}</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}"
                                                {{ old('country_id', isset($property) ? $property->country_id : '') == $country->id ? 'selected' : '' }}>
                                                {{ $country->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('country_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form__input-msg"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.region') }} <span
                                            class="form__star">*</span></label>
                                    <select class="form-select @error('region_id') is-invalid @enderror" name="region_id"
                                        required>
                                        <option value="" selected disabled>{{ __('dashboard.select_region') }}
                                        </option>
                                        @foreach ($regions as $region)
                                            <option value="{{ $region->id }}"
                                                {{ old('region_id', isset($property) ? $property->region_id : '') == $region->id ? 'selected' : '' }}>
                                                {{ $region->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('region_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form__input-msg"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.city') }} <span
                                            class="form__star">*</span></label>
                                    <select class="form-select @error('city_id') is-invalid @enderror" name="city_id"
                                        required>
                                        <option value="" selected disabled>{{ __('dashboard.city') }}</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}"
                                                {{ old('city_id', isset($property) ? $property->city_id : '') == $city->id ? 'selected' : '' }}>
                                                {{ $city->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('city_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form__input-msg"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.district') }} <span
                                            class="form__star">*</span></label>
                                    <select class="form-select @error('district_id') is-invalid @enderror"
                                        name="district_id" required>
                                        <option value="" selected disabled>{{ __('dashboard.district') }}</option>
                                        @foreach ($districts as $district)
                                            <option value="{{ $district->id }}"
                                                {{ old('district_id', isset($property) ? $property->district_id : '') == $district->id ? 'selected' : '' }}>
                                                {{ $district->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('district_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form__input-msg"></div>
                                </div>

                                <!-- Street -->
                                {{-- <div class="col-md-6">
                                    <label class="form-label">Street</label>
                                    <select class="form-select @error('street_id') is-invalid @enderror"
                                        name="street_id">
                                        <option value="" selected disabled>Street</option>
                                        @foreach ($streets as $street)
                                            <option value="{{ $street->id }}"
                                                {{ old('street_id', isset($property) ? $property->street_id : '') == $street->id ? 'selected' : '' }}>
                                                {{ $street->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('street_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form__input-msg"></div>
                                </div> --}}

                                <!-- Time Zone -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.time_zone') }} <span
                                            class="form__star">*</span></label>
                                    <input type="text" class="form-control @error('time_zone') is-invalid @enderror"
                                        name="time_zone" placeholder="{{ __('dashboard.time_zone') }}"
                                        value="{{ old('time_zone', isset($property) ? $property->time_zone : 'Asia/Riyadh') }}"
                                        required>
                                    @error('time_zone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form__input-msg"></div>
                                </div>

                                <!-- Latitude & Longitude -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.latitude') }}</label>
                                    <input type="text" class="form-control @error('latitude') is-invalid @enderror"
                                        name="latitude" id="latitude" placeholder="{{ __('dashboard.latitude') }}"
                                        value="{{ old('latitude', isset($property) ? $property->latitude : '') }}" readonly>
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form__input-msg"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.longitude') }}</label>
                                    <input type="text" class="form-control @error('longitude') is-invalid @enderror"
                                        name="longitude" id="longitude" placeholder="{{ __('dashboard.longitude') }}"
                                        value="{{ old('longitude', isset($property) ? $property->longitude : '') }}" readonly>
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form__input-msg"></div>
                                </div>

                                <!-- Address English -->
                                <div class="col-md-12">
                                    <label class="form-label">{{ __('dashboard.address') }}
                                        ({{ __('dashboard.english') }}) <span class="form__star">*</span></label>
                                    <textarea class="form-control @error('address_en') is-invalid @enderror" name="address_en"
                                        placeholder="{{ __('dashboard.address') }} ({{ __('dashboard.english') }})" rows="2" required>{{ old('address_en', isset($property) ? $property->address_en : '') }}</textarea>
                                    @error('address_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form__input-msg"></div>
                                </div>

                                <!-- Address Arabic -->
                                <div class="col-md-12">
                                    <label class="form-label">{{ __('dashboard.address') }}
                                        ({{ __('dashboard.arabic') }}) <span class="form__star">*</span></label>
                                    <textarea class="form-control @error('address_ar') is-invalid @enderror" name="address_ar"
                                        placeholder="{{ __('dashboard.address') }} ({{ __('dashboard.arabic') }})" rows="2" required>{{ old('address_ar', isset($property) ? $property->address_ar : '') }}</textarea>
                                    @error('address_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form__input-msg"></div>
                                </div>

                                <!-- Building & Secondary Numbers -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.building_no.') }} <span
                                            class="form__star">*</span></label>
                                    <input type="text" class="form-control @error('building_no') is-invalid @enderror"
                                        name="building_no" placeholder="{{ __('dashboard.building_no.') }}"
                                        value="{{ old('building_no', isset($property) ? $property->building_no : '') }}"
                                        required>
                                    @error('building_no')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form__input-msg"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.secondary_no.') }}</label>
                                    <input type="text"
                                        class="form-control @error('secondary_no') is-invalid @enderror"
                                        name="secondary_no" placeholder="{{ __('dashboard.secondary_no.') }}"
                                        value="{{ old('secondary_no', isset($property) ? $property->secondary_no : '') }}">
                                    @error('secondary_no')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form__input-msg"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Map & Additional Info -->
                        <div class="col-md-6">
                            <div class="row g-3">
                                <!-- Leaflet Map -->
                                <div class="col-md-12">
                                    <label class="form-label">{{ __('dashboard.select_your_location_on_maps') }}</label>
                                    <div id="propertyMap" style="height: 350px; border-radius: 8px; border: 2px solid #dee2e6;"></div>
                                    <div id="mapCoords" class="text-muted small mt-1 text-center" style="display: none;">
                                        <span id="displayLat"></span>, <span id="displayLng"></span>
                                    </div>
                                    <div class="form__input-msg"></div>
                                </div>

                                <!-- PO Box & Postal Code -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.po_box') }}</label>
                                    <input type="text" class="form-control @error('po_box') is-invalid @enderror"
                                        name="po_box" placeholder="{{ __('dashboard.po_box') }}"
                                        value="{{ old('po_box', isset($property) ? $property->po_box : '') }}"
                                        maxlength="10">
                                    @error('po_box')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form__input-msg"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.postal_code') }} <span
                                            class="form__star">*</span></label>
                                    <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                                        name="postal_code" placeholder="{{ __('dashboard.postal_code') }}"
                                        value="{{ old('postal_code', isset($property) ? $property->postal_code : '') }}"
                                        required>
                                    @error('postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form__input-msg"></div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information Panel -->
            <div class="collapse-panel">
                <div class="collapse-header" data-bs-toggle="collapse" data-bs-target="#contactInfoCollapse"
                    aria-expanded="true">
                    <div class="collapse-title">
                        <i class="fas fa-minus collapse-icon"></i>
                        <span>{{ __('dashboard.contact_information') }}</span>
                    </div>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="collapse collapse-content show" id="contactInfoCollapse">
                    <div class="row g-3">
                        <!-- Phone Number -->
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.phone_number') }} <span
                                    class="form__star">*</span></label>
                            <div class="phone-input-group">
                                <span class="dial-code">+966</span>
                                <input type="tel"
                                    class="form-control phone-input @error('phone') is-invalid @enderror" name="phone"
                                    placeholder="{{ __('dashboard.phone_number') }}"
                                    value="{{ old('phone', isset($property) ? $property->phone : '') }}" required>
                            </div>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form__input-msg"></div>
                        </div>

                        <!-- Mobile Number -->
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.mobile_no.') }} <span
                                    class="form__star">*</span></label>
                            <div class="phone-input-group">
                                <span class="dial-code">+966</span>
                                <input type="tel"
                                    class="form-control phone-input @error('mobile') is-invalid @enderror" name="mobile"
                                    placeholder="{{ __('dashboard.mobile_no.') }}"
                                    value="{{ old('mobile', isset($property) ? $property->mobile : '') }}" required>
                            </div>
                            @error('mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form__input-msg"></div>
                        </div>

                        <!-- Fax Number -->
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.fax_number') }}</label>
                            <div class="phone-input-group">
                                <span class="dial-code">+966</span>
                                <input type="tel" class="form-control phone-input @error('fax') is-invalid @enderror"
                                    name="fax_number" placeholder="{{ __('dashbaord.fax_number') }}"
                                    value="{{ old('fax', isset($property) ? $property->fax_number : '') }}">
                            </div>
                            @error('fax')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form__input-msg"></div>
                        </div>

                        <!-- Hot Line -->
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.hot_line') }}</label>
                            <div class="phone-input-group">
                                <span class="dial-code">+966</span>
                                <input type="tel"
                                    class="form-control phone-input @error('hotline') is-invalid @enderror"
                                    name="hot_line" placeholder="{{ __('dashboard.hot_line') }}"
                                    value="{{ old('hotline', isset($property) ? $property->hot_line : '') }}">
                            </div>
                            @error('hotline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form__input-msg"></div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.email') }} <span
                                    class="form__star">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                name="email" placeholder="{{ __('dashboard.email') }}"
                                value="{{ old('email', isset($property) ? $property->email : '') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form__input-msg"></div>
                        </div>

                        <!-- Website -->
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.website') }}</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror"
                                name="website" placeholder="{{ __('dashboard.website') }}"
                                value="{{ old('website', isset($property) ? $property->website : '') }}">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form__input-msg"></div>
                        </div>

                        <!-- Admin Mobile No. -->
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.admin_mobile_no.') }}</label>
                            <div class="phone-input-group">
                                <span class="dial-code">+966</span>
                                <input type="tel"
                                    class="form-control phone-input @error('admin_mobile') is-invalid @enderror"
                                    name="admin_number" placeholder="{{ __('dashboard.admin_mobile_no.') }}"
                                    value="{{ old('admin_mobile', isset($property) ? $property->admin_number : '') }}">
                            </div>
                            @error('admin_mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form__input-msg"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end mt-4">
                <button type="button" class="btn btn-secondary me-3"
                    onclick="window.history.back()">{{ __('dashboard.discard') }}</button>
                <button type="submit" class="btn btn-success">
                    {{ isset($property) ? __('dashboard.update_property') : __('dashboard.create_property') }}
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <!-- JavaScript for form handling -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Handle the status switch
        document.getElementById('activeProperty').addEventListener('change', function() {
            document.getElementById('statusHidden').value = this.checked ? 'ACTIVE' : 'INACTIVE';
        });

        // Form validation
        (function() {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()

        // Leaflet Map
        document.addEventListener('DOMContentLoaded', function() {
            var latInput = document.getElementById('latitude');
            var lngInput = document.getElementById('longitude');
            var displayLat = document.getElementById('displayLat');
            var displayLng = document.getElementById('displayLng');
            var mapCoords = document.getElementById('mapCoords');

            var initialLat = parseFloat(latInput.value) || 24.7136;
            var initialLng = parseFloat(lngInput.value) || 46.6753;

            var map = L.map('propertyMap').setView([initialLat, initialLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            var marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

            function updateCoords(lat, lng) {
                lat = lat.toFixed(7);
                lng = lng.toFixed(7);
                latInput.value = lat;
                lngInput.value = lng;
                displayLat.textContent = lat;
                displayLng.textContent = lng;
                mapCoords.style.display = 'block';
            }

            if (latInput.value && lngInput.value) {
                updateCoords(initialLat, initialLng);
            }

            marker.on('dragend', function() {
                var pos = marker.getLatLng();
                updateCoords(pos.lat, pos.lng);
            });

            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                updateCoords(e.latlng.lat, e.latlng.lng);
            });

            // Initialize status hidden field based on checkbox
            var statusCheckbox = document.getElementById('activeProperty');
            var statusHidden = document.getElementById('statusHidden');
            statusHidden.value = statusCheckbox.checked ? 'ACTIVE' : 'INACTIVE';
        });
    </script>
@endpush
