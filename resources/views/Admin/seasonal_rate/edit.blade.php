@extends('layouts.app')

@section('title', 'Edit Seasonal Rate')
@section('content')

    <div class="container mt-4 bg-white p-3" style="border-radius:15px;">

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">{{ __('dashboard.edit_seasonal_rate') }}</h2>
            <a href="{{ route('setup-sidebar.unit.create') }}" data-bs-toggle="modal" data-bs-target="#highWeekdaysModal"
                class="btn btn-outline-success" style="text-decoration:none;" tabindex="0">
                {{ __('dashboard.high_week_days') }}
            </a>
        </div>

        <form action="{{ route('setup-sidebar.seasonal_rate.update', $seasonalRate->id) }}" method="POST">
            @csrf
            @method('PUT')


            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.status') }}</label>
                <select class="form-select" name="status">
                    <option value="1" {{ $seasonalRate->is_active == 1 ? 'selected' : '' }}>
                        {{ __('dashboard.active') }}</option>
                    <option value="0" {{ $seasonalRate->is_active == 0 ? 'selected' : '' }}>
                        {{ __('dashboard.inactive') }}</option>
                </select>
            </div>
            <!-- Name -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        {{ __('dashboard.name') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" class="form-control"
                        value="{{ old('name', $seasonalRate->name) }}" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        {{ __('dashboard.start_date') }} <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="start_date" class="form-control"
                        value="{{ old('start_date', $seasonalRate->start_date->format('Y-m-d')) }}" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        {{ __('dashboard.end_date') }} <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="end_date" class="form-control"
                        value="{{ old('end_date', $seasonalRate->end_date->format('Y-m-d')) }}" required>
                </div>
            </div>

            <!-- Description -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('dashboard.description') }}</label>
                    <textarea name="description" rows="4" class="form-control">{{ old('description', $seasonalRate->description) }}</textarea>
                </div>
            </div>

            <!-- Rates Table -->
            @php
                // Map existing unit‑type rates for quick lookup
                $rateMap = $seasonalRate->unitRates->keyBy('unit_type_id');
            @endphp

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th rowspan="2" class="text-start">{{ __('dashboard.unit_type') }}</th>
                            <th colspan="3">{{ __('dashboard.daily_rent') }}</th>
                        </tr>
                        <tr>
                            <th>{{ __('dashboard.low_weekdays_rate') }}</th>
                            <th>{{ __('dashboard.high_weekdays_rate') }}</th>
                            <th>{{ __('dashboard.min_rate') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($unitTypes as $unit)
                            @php
                                $existing = $rateMap->get($unit->id);
                            @endphp
                            <tr>
                                <td class="fw-semibold">
                                    {{ $unit->name }}
                                    <a type="button" class="btn btn-sm btn-outline-primary ms-2 assign-unit-btn float-end"
                                        data-bs-toggle="modal" data-bs-target="#addUnitModal"
                                        data-unit-type-id="{{ $unit->id }}" data-unit-type-name="{{ $unit->name }}">
                                        +
                                    </a>
                                    <input type="hidden" name="rates[{{ $loop->index }}][unit_type_id]"
                                        value="{{ $unit->id }}">
                                </td>

                                <td>
                                    <input type="number" name="rates[{{ $loop->index }}][low_rate]" class="form-control"
                                        min="0" step="0.01"
                                        value="{{ old('rates.' . $loop->index . '.low_rate', $existing->low_weekday_rate ?? '') }}">
                                </td>

                                <td>
                                    <input type="number" name="rates[{{ $loop->index }}][high_rate]" class="form-control"
                                        min="0" step="0.01"
                                        value="{{ old('rates.' . $loop->index . '.high_rate', $existing->high_weekday_rate ?? '') }}">
                                </td>

                                <td>
                                    <input type="number" name="rates[{{ $loop->index }}][min_rate]" class="form-control"
                                        min="0" step="0.01"
                                        value="{{ old('rates.' . $loop->index . '.min_rate', $existing->daily_min_rate ?? '') }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Buttons -->
            <div class="mt-4 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary me-2">
                    {{ __('dashboard.update') }}
                </button>
                <a href="{{ route('setup-sidebar.seasonal_rate.index') }}" class="btn btn-outline-secondary">
                    {{ __('dashboard.cancel') }}
                </a>
            </div>

        </form>

    </div>

    <!-- Assign Unit Modal (same as create, but now shows only custom rates for this season) -->
    <div class="modal fade" id="addUnitModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <form method="POST" action="{{ route('setup-sidebar.seasonal_custom_rate.store') }}">
                    @csrf
                    <!-- Pass the current seasonal rate ID so the new custom rate is linked to it -->
                    <input type="hidden" name="seasonal_rate_id" value="{{ $seasonalRate->id }}">

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
                                        <option value="{{ $unit->id }}">
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
                                        <td>
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteCustomRateModal{{ $rate->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr id="no-assigned-units-message" style="display: none;">
                                    <td colspan="5" class="text-muted">
                                        {{ __('dashboard.no_assigned_units_for_this_type') }}
                                    </td>
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
    <!-- Delete modals for each custom rate (same as in base rates) -->
    @foreach ($assignedRates as $rate)
        <div class="modal fade" id="deleteCustomRateModal{{ $rate->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('dashboard.delete_custom_rate') }} – {{ $rate->unit->unit_number }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p>{{ __('dashboard.delete_custom_rate_confirmation') }}</p>
                        <small class="text-muted">{{ __('dashboard.this_action_cannot_be_undone') }}</small>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.cancel') }}
                        </button>
                        <form action="{{ route('setup-sidebar.seasonal_custom_rate.delete', $rate->id) }}"
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
        document.addEventListener("DOMContentLoaded", function() {
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

                messageRow.style.display = visibleCount === 0 ? '' : 'none';
            }

            document.querySelectorAll('.assign-unit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const unitTypeId = this.dataset.unitTypeId;
                    const unitTypeName = this.dataset.unitTypeName;

                    document.getElementById('modal_unit_type_id').value = unitTypeId;
                    document.getElementById('modal_unit_type_name').value = unitTypeName;

                    filterAssignedUnits(unitTypeId);
                });
            });

            const modal = document.getElementById('addUnitModal');
            if (modal) {
                modal.addEventListener('hidden.bs.modal', function() {
                    document.querySelectorAll('#assigned-units-body .assigned-unit-row').forEach(row => {
                        row.style.display = '';
                    });
                    document.getElementById('no-assigned-units-message').style.display = 'none';
                });
            }
        });
    </script>
@endpush
