@extends('layouts.app')

@section('title', 'Reservation Sources')
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
</style>
@section('content')
    <div class="page-wrapper bg-white p-3" style="border-radius:15px;">

        <div class="page-category">{{ __('dashboard.general_settings') }}</div>

        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.reservation_sources') }}</h2>
                <div class="page-header__subtitle">
                    {{ __('dashboard.set_the_reservation_sources_you_will_be_use_on_your_properties') }}</div>
            </div>
            <div class="n-table__top-btns">

                <div>
                    @can('reservation_source.add')
                        <a href="#" class="n-button n-button--green" data-bs-toggle="modal"
                            data-bs-target="#addChannelSettingModal" style="text-decoration:none;" tabindex="0">
                            {{ __('dashboard.new_reservation_source') }}
                        </a>
                    @endcan

                </div>
            </div>
        </div>

        <div class="container mt-5">
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th style="width:70px;"></th>
                                <th>{{ __('dashboard.source_name') }}</th>
                                <th style="width:120px;">{{ __('dashboard.status') }}</th>
                                <th>{{ __('dashboard.name') }}</th>
                                <th style="width:320px;">{{ __('dashboard.description') }}</th>
                                <th style="width:130px;">{{ __('dashboard.actions') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($settings as $setting)
                                <tr>
                                    <td>
                                        <i class="bi bi-arrows-move text-muted"></i>
                                    </td>

                                    <td>
                                        {{ $setting->masterSource->name ?? '-' }}
                                    </td>

                                    <td>
                                        @if ($setting->status)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $setting->report_name ?? '-' }}
                                    </td>

                                    <td style="max-width:320px; white-space:normal;">
                                        {{ $setting->description ?? '-' }}
                                    </td>

                                    <td>
                                        @can('reservation_source.edit')
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#editModal{{ $setting->id }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                        @endcan

                                        @can('reservation_source.view')
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#viewModal{{ $setting->id }}">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        @endcan

                                        @can('reservation_source.delete')
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteCustomRateModal{{ $setting->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endcan

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>

            </div>

        </div>

    </div>

    <!-- Add Channel Setting Modal -->
    <div class="modal fade" id="addChannelSettingModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <form method="POST" action="{{ route('setup-sidebar.reservation_source.store') }}">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('dashboard.add_reservation_source_setting') }}
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="row">

                            <!-- Channel Dropdown -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    {{ __('dashboard.reservation_source') }}
                                    <span class="text-danger">*</span>
                                </label>

                                <select name="master_channel_id" class="form-select" required>
                                    <option value="">{{ __('dashboard.select_source') }}</option>

                                    @foreach ($sources as $source)
                                        <option value="{{ $source->id }}">
                                            {{ $source->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <!-- Report Name -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    {{ __('dashboard.report_name') }}
                                </label>

                                <input type="text" name="report_name" class="form-control"
                                    placeholder="{{ __('dashboard.enter_report_name') }}">
                            </div>

                            <!-- URL -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">URL</label>
                                <input type="text" name="url" class="form-control"
                                    placeholder="{{ __('dashboard.enter_source_url') }}">
                            </div>

                            <!-- Commission Rate -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    {{ __('dashboard.commission_rate') }} (%)
                                </label>

                                <input type="number" step="0.01" name="commission_rate" class="form-control"
                                    placeholder="{{ __('dashboard.enter_commission_rate') }}">
                            </div>

                            <!-- Tax Mode -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    {{ __('dashboard.tax_mode') }}
                                </label>

                                <select name="tax_mode" class="form-select" id="taxModeSelect">
                                    <option value="auto">{{ __('dashboard.auto') }}</option>
                                    <option value="manual">{{ __('dashboard.manual') }}</option>
                                </select>
                            </div>

                            <!-- Tax Calculation Type -->
                            <div class="col-md-6 mb-3" id="taxCalculationBox">
                                <label class="form-label fw-semibold">
                                    {{ __('dashboard.consider_taxes_as') }}
                                </label>

                                <select name="tax_calculation_type" class="form-select">
                                    <option value="inclusive">{{ __('dashboard.inclusive') }}</option>
                                    <option value="exclusive">{{ __('dashboard.exclusive') }}</option>
                                </select>
                            </div>

                            <!-- Description -->
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">
                                    {{ __('dashboard.description') }}
                                </label>

                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.discard') }}
                        </button>

                        <button type="submit" class="btn btn-primary">
                            {{ __('dashboard.save') }}
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    @foreach ($settings as $setting)
        <!-- Edit Modal -->
        <div class="modal fade" id="editModal{{ $setting->id }}" tabindex="-1">

            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <form method="POST" action="{{ route('setup-sidebar.reservation_source.update', $setting->id) }}">

                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ __('dashboard.edit_setting') }}
                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <div class="row">

                                <!-- Report Name -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        {{ __('dashboard.report_name') }}
                                    </label>

                                    <input type="text" name="report_name" class="form-control"
                                        value="{{ $setting->report_name }}">
                                </div>

                                <!-- URL -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        URL
                                    </label>

                                    <input type="text" name="url" class="form-control"
                                        value="{{ $setting->url }}">
                                </div>

                                <!-- Commission Rate -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        {{ __('dashboard.commission_rate') }}
                                    </label>

                                    <input type="number" step="0.01" name="commission_rate" class="form-control"
                                        value="{{ $setting->commission_rate }}">
                                </div>

                                <!-- Tax Mode -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        {{ __('dashboard.tax_mode') }}
                                    </label>

                                    <select name="tax_mode" class="form-select">

                                        <option value="auto" {{ $setting->tax_mode == 'auto' ? 'selected' : '' }}>
                                            {{ __('dashboard.auto') }}
                                        </option>

                                        <option value="manual" {{ $setting->tax_mode == 'manual' ? 'selected' : '' }}>
                                            {{ __('dashboard.manual') }}
                                        </option>

                                    </select>
                                </div>

                                <!-- Consider Taxes As -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        {{ __('dashboard.consider_taxes_as') }}
                                    </label>

                                    <select name="tax_calculation_type" class="form-select">
                                        <option value="">{{ __('dashboard.select_tax_mode') }}</option>
                                        <option value="inclusive"
                                            {{ $setting->tax_calculation_type == 'inclusive' ? 'selected' : '' }}>
                                            {{ __('dashboard.inclusive') }}
                                        </option>

                                        <option value="exclusive"
                                            {{ $setting->tax_calculation_type == 'exclusive' ? 'selected' : '' }}>
                                            {{ __('dashboard.exclusive') }}
                                        </option>

                                    </select>
                                </div>

                                <!-- Status Toggle -->
                                <div class="col-md-6 mb-3">

                                    <label class="form-label d-block">
                                        {{ __('dashboard.status') }}
                                    </label>

                                    <div class="form-check form-switch">

                                        <input class="form-check-input" type="checkbox" name="status" value="1"
                                            {{ $setting->status ? 'checked' : '' }}>

                                        <label class="form-check-label">
                                            {{ $setting->status ? __('dashboard.active') : __('dashboard.inactive') }}
                                        </label>

                                    </div>

                                </div>

                                <!-- Description -->
                                <div class="col-12 mb-3">
                                    <label class="form-label">
                                        {{ __('dashboard.description') }}
                                    </label>

                                    <textarea name="description" class="form-control">
                                {{ $setting->description }}
                            </textarea>
                                </div>

                            </div>

                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ __('dashboard.discard') }}
                            </button>

                            <button class="btn btn-primary">
                                {{ __('dashboard.save_changes') }}
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        <!-- View Modal -->
        <div class="modal fade" id="viewModal{{ $setting->id }}" tabindex="-1">

            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <form method="POST" action="#">

                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ __('dashboard.view_setting') }}
                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <div class="row">

                                <!-- Report Name -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        {{ __('dashboard.report_name') }}
                                    </label>

                                    <input type="text" name="report_name" class="form-control"
                                        value="{{ $setting->report_name }}" disabled>
                                </div>

                                <!-- URL -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        URL
                                    </label>

                                    <input type="text" name="url" class="form-control"
                                        value="{{ $setting->url }}" disabled>
                                </div>

                                <!-- Commission Rate -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        {{ __('dashboard.commission_rate') }}
                                    </label>

                                    <input type="number" step="0.01" name="commission_rate" class="form-control"
                                        value="{{ $setting->commission_rate }}" disabled>
                                </div>

                                <!-- Tax Mode -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        {{ __('dashboard.tax_mode') }}
                                    </label>

                                    <select name="tax_mode" class="form-select" disabled>

                                        <option value="auto" {{ $setting->tax_mode == 'auto' ? 'selected' : '' }}>
                                            {{ __('dashboard.auto') }}
                                        </option>

                                        <option value="manual" {{ $setting->tax_mode == 'manual' ? 'selected' : '' }}>
                                            {{ __('dashboard.manual') }}
                                        </option>

                                    </select>
                                </div>

                                <!-- Consider Taxes As -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        {{ __('dashboard.consider_taxes_as') }}
                                    </label>

                                    <select name="tax_calculation_type" class="form-select" disabled>
                                        <option>{{ __('dashboard.select_tax_mode') }}</option>
                                        <option value="inclusive"
                                            {{ $setting->tax_calculation_type == 'inclusive' ? 'selected' : '' }}>
                                            {{ __('dashboard.inclusive') }}
                                        </option>

                                        <option value="exclusive"
                                            {{ $setting->tax_calculation_type == 'exclusive' ? 'selected' : '' }}>
                                            {{ __('dashboard.exclusive') }}
                                        </option>

                                    </select>
                                </div>

                                <!-- Status Toggle -->
                                <div class="col-md-6 mb-3">

                                    <label class="form-label d-block">
                                        {{ __('dashboard.status') }}
                                    </label>

                                    <div class="form-check form-switch">

                                        <input class="form-check-input" type="checkbox" name="status" value="1"
                                            {{ $setting->status ? 'checked' : '' }} disabled>

                                        <label class="form-check-label">
                                            {{ $setting->status ? __('dashboard.active') : __('dashboard.inactive') }}
                                        </label>

                                    </div>

                                </div>

                                <!-- Description -->
                                <div class="col-12 mb-3">
                                    <label class="form-label">
                                        {{ __('dashboard.description') }}
                                    </label>

                                    <textarea name="description" class="form-control" disabled>
                                {{ $setting->description }}
                            </textarea>
                                </div>

                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ __('dashboard.discard') }}
                            </button>


                        </div>

                    </form>

                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteCustomRateModal{{ $setting->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('dashboard.delete_reservation_source') }} – {{ $setting->masterSource->name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p>{{ __('dashboard.delete_reservation_source_confirmation') }}</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.cancel') }}
                        </button>
                        <form action="{{ route('setup-sidebar.reservation_source.delete', $setting->id) }}"
                            method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                {{ __('dashboard.delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
@push('scripts')
    <script>
        function handleTaxBoxVisibility() {
            let taxMode = document.getElementById('taxModeSelect').value;
            let box = document.getElementById('taxCalculationBox');

            if (taxMode === 'manual') {
                box.style.display = 'block';
            } else {
                box.style.display = 'none';
            }
        }

        // When dropdown changes
        document.getElementById('taxModeSelect').addEventListener('change', function() {
            handleTaxBoxVisibility();
        });

        // When modal opens → reset state
        document.getElementById('addChannelSettingModal')
            .addEventListener('shown.bs.modal', function() {

                document.getElementById('taxModeSelect').value = 'auto';
                handleTaxBoxVisibility();

            });
    </script>
@endpush
