@extends('layouts.app')

@section('title', 'Edit Rate Plan')

@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">

        <!-- Page Header -->
        <div class="page-category">{{ __('dashboard.company') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.edit_rate_plan') }}</h2>
                <div class="page-header__subtitle">{{ __('dashboard.update_rate_plan_details') }}</div>
            </div>
        </div>

        <div class="container-fluid">

            <form action="{{ route('setup-sidebar.rate_plan.update', $ratePlan->id) }}" method="POST">
                @csrf
                @method('PUT')


                <div class="col-md-4 mb-3">
                    <label class="form-label">{{ __('dashboard.status') }}</label>
                    <select class="form-select" name="status">
                        <option value="1" {{ $ratePlan->is_active == 1 ? 'selected' : '' }}>
                            {{ __('dashboard.active') }}</option>
                        <option value="0" {{ $ratePlan->is_active == 0 ? 'selected' : '' }}>
                            {{ __('dashboard.inactive') }}</option>
                    </select>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    {{ __('dashboard.name') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $ratePlan->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    {{ __('dashboard.description') }}
                                </label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $ratePlan->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <ul class="nav nav-tabs mb-3" id="ratePlanTabs" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active" style="color:black;" data-bs-toggle="tab"
                            data-bs-target="#rent-rate">
                            {{ __('dashboard.rent_rate') }}
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" style="color:black;" data-bs-toggle="tab"
                            data-bs-target="#meals">
                            {{ __('dashboard.meals') }}
                        </button>
                    </li>
                </ul>

                <div class="tab-content">

                    <div class="tab-pane fade show active" id="rent-rate">
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle mb-0">
                                        <thead class="table-light text-center">
                                            <tr>
                                                <th rowspan="2" class="text-start">{{ __('dashboard.unit_type') }}</th>
                                                <th colspan="2">{{ __('dashboard.rent_rate') }}</th>
                                            </tr>
                                            <tr>
                                                <th>{{ __('dashboard.daily') }}</th>
                                                <th>{{ __('dashboard.monthly') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $existingRates = $ratePlan->unitTypeRates->keyBy('id');
                                            @endphp

                                            @foreach ($unitTypes as $index => $unit)
                                                @php
                                                    $rateForUnit = $existingRates->get($unit->id);
                                                @endphp
                                                <tr>
                                                    <td class="fw-semibold">
                                                        {{ $unit->name }}
                                                        <a type="button"
                                                            class="btn btn-sm btn-outline-primary ms-2 assign-unit-btn float-end"
                                                            data-bs-toggle="modal" data-bs-target="#addUnitModal"
                                                            data-unit-type-id="{{ $unit->id }}"
                                                            data-unit-type-name="{{ $unit->name }}">
                                                            +
                                                        </a>
                                                        <input type="hidden"
                                                            name="rates[{{ $index }}][unit_type_id]"
                                                            value="{{ $unit->id }}">
                                                    </td>

                                                    <td>
                                                        <input type="number" step="0.01" min="0"
                                                            name="rates[{{ $index }}][daily_rate]"
                                                            class="form-control text-center"
                                                            value="{{ old('rates.' . $index . '.daily_rate', $rateForUnit->pivot->daily_rate ?? '') }}">
                                                    </td>

                                                    <td>
                                                        <input type="number" step="0.01" min="0"
                                                            name="rates[{{ $index }}][monthly_rate]"
                                                            class="form-control text-center"
                                                            value="{{ old('rates.' . $index . '.monthly_rate', $rateForUnit->pivot->monthly_rate ?? '') }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="tab-pane fade" id="meals">
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle mb-0">
                                        <thead class="table-light text-center">
                                            <tr>
                                                <th rowspan="2" class="text-center"></th>
                                                <th rowspan="2" class="text-start">{{ __('dashboard.meal') }}</th>
                                                <th colspan="2">{{ __('dashboard.price') }}</th>
                                            </tr>
                                            <tr>
                                                <th>{{ __('dashboard.adult') }}</th>
                                                <th>{{ __('dashboard.child') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $staticMeals = ['Breakfast', 'Lunch', 'Dinner'];
                                                $existingMeals = $ratePlan->meals->keyBy('meal_name');
                                            @endphp

                                            @foreach ($staticMeals as $index => $meal)
                                                @php
                                                    $existing = $existingMeals->get($meal);
                                                @endphp
                                                <tr>
                                                    <td class="text-center">
                                                        <input type="checkbox" name="meals[{{ $index }}][enabled]"
                                                            value="1" {{ $existing ? 'checked' : '' }}>
                                                        <input type="hidden" name="meals[{{ $index }}][meal_type]"
                                                            value="{{ $meal }}">
                                                    </td>

                                                    <td class="fw-semibold">{{ $meal }}</td>

                                                    <td>
                                                        <input type="number" step="0.01" min="0"
                                                            name="meals[{{ $index }}][adult_price]"
                                                            class="form-control text-center"
                                                            value="{{ old('meals.' . $index . '.adult_price', $existing->adult_price ?? '') }}">
                                                    </td>

                                                    <td>
                                                        <input type="number" step="0.01" min="0"
                                                            name="meals[{{ $index }}][child_price]"
                                                            class="form-control text-center"
                                                            value="{{ old('meals.' . $index . '.child_price', $existing->child_price ?? '') }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


                <div class="text-end mt-4">
                    <a href="{{ route('setup-sidebar.rate_plan.index') }}" class="btn btn-outline-danger me-2">
                        {{ __('dashboard.discard') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        {{ __('dashboard.update_rate_plan') }}
                    </button>
                </div>

            </form>
        </div>

    </main>

    <div class="modal fade" id="addUnitModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('setup-sidebar.seasonal_custom_rate.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('dashboard.assign_unit_custom_rate') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="unit_type_id" id="modal_unit_type_id">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('dashboard.unit_Type') }}</label>
                                <input type="text" id="modal_unit_type_name" class="form-control" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('dashboard.select_unit') }}</label>
                                <select name="unit_id" class="form-select form-select-sm" required>
                                    <option value="">{{ __('dashboard.select_unit') }}</option>
                                    @foreach ($availableUnits as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->unit_number }}</option>
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
                                    <td colspan="6" class="text-muted">
                                        {{ __('dashboard.no_assigned_units_for_this_type') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">{{ __('dashboard.save_custom_rate') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete modals for assigned rates (same as create) --}}
    @foreach ($assignedRates as $rate)
        <div class="modal fade" id="deleteCustomRateModal{{ $rate->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('dashboard.delete_custom_rate') }} – {{ $rate->unit->unit_number }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p>{{ __('dashboard.delete_custom_rate_confirmation') }}</p>
                        <small class="text-muted">{{ __('dashboard.this_action_cannot_be_undone') }}</small>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                        <form action="{{ route('setup-sidebar.seasonal_custom_rate.delete', $rate->id) }}"
                            method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">{{ __('dashboard.delete') }}</button>
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
