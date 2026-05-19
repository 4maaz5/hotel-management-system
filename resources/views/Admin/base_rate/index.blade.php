@extends('layouts.app')

@section('title', 'Base Rates')
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
</style>
@section('content')
<div  class="bg-white p-3" style="border-radius:15px;">
    <!-- Page Header -->
    <div class="page-category">{{ __('dashboard.company') }}</div>
    <div class="page-header">
        <div>
            <h2 class="page-header__title">{{ __('dashboard.base_rate') }}</h2>
            <div class="page-header__subtitle">{{ __('dashboard.view_and_manage_rates') }}</div>
        </div>
        <div class="n-table__top-btns">
            <div>
                @can('unit.add')
                    <a href="{{ route('setup-sidebar.unit.create') }}" data-bs-toggle="modal" data-bs-target="#highWeekdaysModal"
                        class="n-button n-button--green" style="text-decoration:none;" tabindex="0">
                        {{ __('dashboard.high_week_days') }}
                    </a>
                @endcan

            </div>
            <button class="n-button n-button--primary">
                {{ __('dashboard.filter') }}
            </button>

        </div>
    </div>
    <main class="u-white-bg bg-white">



        <!-- Filter Form -->
        <form method="GET" action="{{ route('setup-sidebar.base_rate.index') }}">
            <div class="filter-form__container mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">{{ __('dashboard.unit_type') }}</label>
                                <input type="text" name="unit_type_name" value="{{ request('unit_type_name') }}"
                                    class="form-control" placeholder="{{ __('dashboard.enter_unit_type') }}">
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">{{ __('dashboard.unit_number') }}</label>
                                <input type="text" name="unit_number" value="{{ request('unit_number') }}"
                                    class="form-control" placeholder="{{ __('dashboard.enter_unit_number') }}">
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <label class="form-label">{{ __('dashboard.status') }}</label>
                                <select name="is_active" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>
                                        {{ __('dashboard.active') }}</option>
                                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>
                                        {{ __('dashboard.inactive') }}</option>
                                </select>
                            </div>

                            <div class="col-lg-2 col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">{{ __('dashboard.search') }}</button>
                                <a href="{{ route('setup-sidebar.base_rate.index') }}"
                                    class="btn btn-outline-secondary">{{ __('dashboard.reset') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>


        <form method="POST" action="{{ route('setup-sidebar.base_rate.store') }}">
            @csrf

            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>{{ __('dashboard.unit_type') }}</th>
                        <th>{{ __('dashboard.low_weekdays_rate') }}</th>
                        <th>{{ __('dashboard.high_weekdays_rate') }}</th>
                        <th>{{ __('dashboard.daily_min') }}</th>
                        <th>{{ __('dashboard.monthly_rate') }}</th>
                        <th>{{ __('dashboard.monthly_min') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($unitTypes as $type)
                        <tr>
                            <td>{{ $type->name }}
                                <!-- Add Unit Button -->
                                @can('custom_rate.add')
                                    <a type="button" class="btn btn-sm btn-outline-primary ms-2 assign-unit-btn float-end"
                                        data-bs-toggle="modal" data-bs-target="#addUnitModal"
                                        data-unit-type-id="{{ $type->unit_type_id }}"
                                        data-unit-type-name="{{ $type->name }}">
                                        +
                                    </a>
                                @endcan

                            </td>

                            <td>
                                <input type="number" step="0.01" name="rates[{{ $type->unit_type_id }}][low_weekday_rate]"
                                    value="{{ $type->rate->low_weekday_rate ?? '' }}" class="form-control">

                            </td>

                            <td>
                                <input type="number" step="0.01" name="rates[{{ $type->unit_type_id }}][high_weekday_rate]"
                                    value="{{ $type->rate->high_weekday_rate ?? '' }}" class="form-control">
                            </td>

                            <td>
                                <input type="number" step="0.01" name="rates[{{ $type->unit_type_id }}][daily_min_rate]"
                                    value="{{ $type->rate->daily_min_rate ?? '' }}" class="form-control">
                            </td>

                            <td>
                                <input type="number" step="0.01" name="rates[{{ $type->unit_type_id }}][monthly_rate]"
                                    value="{{ $type->rate->monthly_rate ?? '' }}" class="form-control">
                            </td>

                            <td>
                                <input type="number" step="0.01" name="rates[{{ $type->unit_type_id }}][monthly_min_rate]"
                                    value="{{ $type->rate->monthly_min_rate ?? '' }}" class="form-control">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @can('base_rate.edit')
                <button type="submit" class="btn btn-primary m-3">
                    {{ __('dashboard.save_rates') }}
                </button>
            @endcan

        </form>


        <!-- Modal -->
        <div class="modal fade" id="highWeekdaysModal" tabindex="-1" aria-labelledby="highWeekdaysLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title fw-bold" id="highWeekdaysLabel">
                                {{ __('dashboard.high_week_days') }}
                            </h5>
                            <small class="text-muted">
                                {{ __('dashboard.select_high_week_days') }}
                            </small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Body -->
                    <form method="POST" action="{{ route('setup-sidebar.high_weekdays.store') }}">
                        @csrf

                        <div class="modal-body">
                            @php
                                $selectedDays = \App\Models\HighWeekday::pluck('day_name')->toArray();
                                $days = ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
                            @endphp

                            <div class="row">
                                <!-- Select All -->
                                <div class="col-md-3 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                        <label class="form-check-label fw-semibold" for="selectAll">
                                            {{ __('dashboard.all') }}
                                        </label>
                                    </div>
                                </div>

                                @foreach ($days as $day)
                                    <div class="col-md-3 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input day-checkbox" type="checkbox" name="days[]"
                                                value="{{ $day }}" id="{{ $day }}"
                                                {{ in_array($day, $selectedDays) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="{{ $day }}">
                                                {{ __('dashboard.' . $day) }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                                {{ __('dashboard.discard') }}
                            </button>
                            <button type="submit" class="btn btn-primary">
                                {{ __('dashboard.save_changes') }}
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>


        <!-- Assign Unit Modal -->
        <div class="modal fade" id="addUnitModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <form method="POST" action="{{ route('setup-sidebar.custom_rate.store') }}">
                        @csrf

                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ __('dashboard.assign_unit_custom_rate') }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <!-- Hidden Unit Type -->
                            <input type="hidden" name="unit_type_id" id="modal_unit_type_id">
                            <div class="row">
                                <!-- Selected Unit Type Name -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.unit_Type') }}</label>
                                    <input type="text" id="modal_unit_type_name" class="form-control" disabled>
                                </div>

                                <!-- Select Unit -->
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.select_unit') }}</label>
                                    <select name="unit_id" class="form-select form-select-sm" required>
                                        <option value="">{{ __('dashboard.select_unit') }}</option>
                                        @foreach ($availableUnits as $unit)
                                            <option value="{{ $unit->id }}"
                                                data-unit-type-id="{{ $unit->unitTypeCustomization?->unit_type_id }}">
                                                {{ $unit->unit_number }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-md-4">
                                    <label>{{ __('dashboard.low_weekday_rate') }}</label>
                                    <input type="number" step="0.01" name="low_weekday_rate" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label>{{ __('dashboard.high_weekday_rate') }}</label>
                                    <input type="number" step="0.01" name="high_weekday_rate" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label>{{ __('dashboard.daily_min_rate') }}</label>
                                    <input type="number" step="0.01" name="daily_min_rate" class="form-control">
                                </div>

                                <div class="col-md-6 mt-3">
                                    <label>{{ __('dashboard.monthly_rate') }}</label>
                                    <input type="number" step="0.01" name="monthly_rate" class="form-control">
                                </div>

                                <div class="col-md-6 mt-3">
                                    <label>{{ __('dashboard.monthly_min_rate') }}</label>
                                    <input type="number" step="0.01" name="monthly_min_rate" class="form-control">
                                </div>
                            </div>
                            <hr class="my-4">

                            <h6 class="text-center mb-3">{{ __('dashboard.assigned_units') }}</h6>

                            <table class="table table-sm table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th>{{ __('dashboard.unit') }}</th>
                                        <th>{{ __('dashboard.low') }}</th>
                                        <th>{{ __('dashboard.high') }}</th>
                                        <th>{{ __('dashboard.daily_min') }}</th>
                                        <th>{{ __('dashboard.monthly') }}</th>
                                        <th>{{ __('dashboard.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="assigned-units-body">
                                    @foreach ($assignedRates as $rate)
                                        <tr class="assigned-unit-row" data-unit-type-id="{{ $rate->unit_type_id }}">
                                            <td>{{ $rate->unit->unit_number }}</td>
                                            <td>{{ $rate->low_weekday_rate }}</td>
                                            <td>{{ $rate->high_weekday_rate }}</td>
                                            <td>{{ $rate->daily_min_rate }}</td>
                                            <td>{{ $rate->monthly_rate }}</td>
                                            <td>
                                                @can('custom_rate.delete')
                                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                        data-bs-target="#deleteCustomRateModal{{ $rate->id }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endcan
                                                @can('custom_rate.edit')
                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                        data-bs-target="#editCustomRateModal{{ $rate->id }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                @endcan

                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr id="no-assigned-units-message" style="display: none;">
                                        <td colspan="6" class="text-muted">
                                            {{ __('dashboard.no_assigned_units_for_this_type') }}</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">
                                {{ __('dashboard.save_custom_rate') }}
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        @foreach ($assignedRates as $rate)
            <div class="modal fade" id="editCustomRateModal{{ $rate->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('setup-sidebar.custom_rate.update', $rate->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="modal-header">
                                <h5 class="modal-title">
                                    {{ __('dashboard.edit_custom_rate') }} – {{ __('dashboard.unit') }}
                                    <strong>{{ $rate->unit->unit_number }}</strong>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.low_weekday_rate') }}</label>
                                        <input type="number" step="0.01" name="low_weekday_rate"
                                            value="{{ $rate->low_weekday_rate }}" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.high_weekday_rate') }}</label>
                                        <input type="number" step="0.01" name="high_weekday_rate"
                                            value="{{ $rate->high_weekday_rate }}" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.daily_min_rate') }}</label>
                                        <input type="number" step="0.01" name="daily_min_rate"
                                            value="{{ $rate->daily_min_rate }}" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.monthly_rate') }}</label>
                                        <input type="number" step="0.01" name="monthly_rate"
                                            value="{{ $rate->monthly_rate }}" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('dashboard.monthly_min_rate') }}</label>
                                        <input type="number" step="0.01" name="monthly_min_rate"
                                            value="{{ $rate->monthly_min_rate }}" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                                <button type="submit"
                                    class="btn btn-primary">{{ __('dashboard.update_report') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="deleteCustomRateModal{{ $rate->id }}" tabindex="-1" aria-hidden="true">

                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ __('dashboard.delete_custom_rate') }} :
                                <strong>{{ $rate->unit->unit_number }}</strong>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body text-center">
                            <p>
                                {{ __('dashboard.delete_custom_rate_confirmation') }}
                            </p>

                        </div>

                        <!-- Footer -->
                        <div class="modal-footer justify-content-center">

                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ __('dashboard.cancel') }}
                            </button>

                            <form action="{{ route('setup-sidebar.custom_rate.destroy', $rate->id) }}" method="POST">
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



    </main>
</div>
@endsection
@push('scripts')
    <script>
        const toggleBtn = document.querySelector('.n-button.n-button--primary');
        const filterContainer = document.querySelector('.filter-form__container');

        filterContainer.style.display = 'none';

        toggleBtn.addEventListener('click', function() {
            if (filterContainer.style.display === 'none') {
                filterContainer.style.display = 'block';
            } else {
                filterContainer.style.display = 'none';
            }
        });

        document.getElementById('selectAll').addEventListener('change', function() {
            let checkboxes = document.querySelectorAll('.day-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });

        function filterAssignedUnits(unitTypeId) {
            const rows = document.querySelectorAll('#assigned-units-body .assigned-unit-row');
            const messageRow = document.getElementById('no-assigned-units-message');
            let visibleCount = 0;

            rows.forEach(row => {
                if (row.dataset.unitTypeId == unitTypeId) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (visibleCount === 0) {
                messageRow.style.display = '';
            } else {
                messageRow.style.display = 'none';
            }
        }

        function filterAvailableUnits(unitTypeId) {
            const select = document.querySelector('#addUnitModal select[name="unit_id"]');
            if (!select) {
                return;
            }

            select.value = '';
            select.querySelectorAll('option').forEach(option => {
                if (!option.value) {
                    option.hidden = false;
                    return;
                }

                option.hidden = option.dataset.unitTypeId != unitTypeId;
            });
        }

        // When the modal is opened
        document.querySelectorAll('.assign-unit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const unitTypeId = this.dataset.unitTypeId;
                const unitTypeName = this.dataset.unitTypeName;

                document.getElementById('modal_unit_type_id').value = unitTypeId;
                document.getElementById('modal_unit_type_name').value = unitTypeName;

                filterAssignedUnits(unitTypeId);
                filterAvailableUnits(unitTypeId);
            });
        });
    </script>
@endpush
