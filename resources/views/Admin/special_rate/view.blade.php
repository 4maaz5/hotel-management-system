@extends('layouts.app')

@section('title', 'View Seasonal Rate')
@section('content')

    <div class="container mt-4 bg-white p-3" style="border-radius:15px;">

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">{{ __('dashboard.view_seasonal_rate') }}</h2>
        </div>

        <form action="#" method="POST">
            @csrf
            @method('PUT')
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('dashboard.status') }}</label>
                <select class="form-select" name="status" disabled>
                    <option value="1" {{ $seasonalRate->is_active == 1 ? 'selected' : '' }}>{{ __('dashboard.active') }}
                    </option>
                    <option value="0" {{ $seasonalRate->is_active == 0 ? 'selected' : '' }}>
                        {{ __('dashboard.inactive') }}
                    </option>
                </select>
            </div>
            <!-- Name -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        {{ __('dashboard.name') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $seasonalRate->name) }}"
                        disabled>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        {{ __('dashboard.start_date') }} <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="start_date" class="form-control"
                        value="{{ old('start_date', $seasonalRate->start_date->format('Y-m-d')) }}" disabled>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        {{ __('dashboard.end_date') }} <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="end_date" class="form-control"
                        value="{{ old('end_date', $seasonalRate->end_date->format('Y-m-d')) }}" disabled>
                </div>
            </div>

            <!-- Description -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('dashboard.description') }}</label>
                    <textarea name="description" rows="4" disabled class="form-control">{{ old('description', $seasonalRate->description) }}</textarea>
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
                            <th>{{ __('dashboard.rate') }}</th>
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
                                    <input type="number" name="rates[{{ $loop->index }}][rate]" class="form-control"
                                        min="0" step="0.01"
                                        value="{{ old('rates.' . $loop->index . '.rate', $existing->rate ?? '') }}">
                                </td>

                                <td>
                                    <input type="number" name="rates[{{ $loop->index }}][min_rate]" class="form-control"
                                        min="0" step="0.01"
                                        value="{{ old('rates.' . $loop->index . '.min_rate', $existing->min_rate ?? '') }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Buttons -->
            <div class="mt-4 d-flex justify-content-end">

                <a href="{{ route('setup-sidebar.special_rate.index') }}" class="btn btn-outline-secondary">
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

                        <h6 class="text-center mb-3">{{ __('dashboard.assigned_units') }}</h6>

                        <table class="table table-sm table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>{{ __('dashboard.unit') }}</th>
                                    <th>{{ __('dashboard.rate') }}</th>
                                    <th>{{ __('dashboard.min_rate') }}</th>
                                </tr>
                            </thead>
                            <tbody id="assigned-units-body">
                                @foreach ($assignedRates as $rate)
                                    <tr class="assigned-unit-row" data-unit-type-id="{{ $rate->unit_type_id }}">
                                        <td>{{ $rate->unit->unit_number }}</td>
                                        <td>{{ $rate->rate }}</td>
                                        <td>{{ $rate->min_rate }}</td>

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

                    <button type="button" class="btn btn-secondary float-end m-3" data-bs-dismiss="modal">
                        {{ __('dashboard.cancel') }}
                    </button>

                </form>

            </div>
        </div>
    </div>



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
