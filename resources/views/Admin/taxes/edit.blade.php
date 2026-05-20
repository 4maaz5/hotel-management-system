@extends('layouts.app')

@section('title', 'Edit tax and fee')
@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">

        <div class="container mt-4">

            <div class="mb-4">
                <h4 class="fw-bold">
                    {{ __('dashboard.financial') }} - {{ __('dashboard.edit_taxes_and_fees_customization') }}
                </h4>
            </div>

            <form action="{{ route('setup-sidebar.taxes.update', $tax->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Use for expenses -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_expenses"
                                {{ $tax->is_expenses ? 'checked' : '' }}>
                            <label class="form-check-label ms-2">
                                {{ __('dashboard.use_for_expenses_vouchers') }}
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Type -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            {{ __('dashboard.type') }}
                        </label>

                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" value="tax"
                                    {{ $tax->type == 'tax' ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    {{ __('dashboard.tax') }}
                                </label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" value="fee"
                                    {{ $tax->type == 'fee' ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    {{ __('dashboard.fee') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Name -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">
                            <span id="nameLabel">
                                {{ $tax->type == 'fee' ? __('dashboard.fee_name') : __('dashboard.tax_name') }}
                            </span>
                        </label>

                        <select class="form-select" name="custom_name" id="customNameDropdown" required>

                            @if ($tax->type == 'fee')
                                <option value="lodging_fee" {{ $tax->custom_name == 'lodging_fee' ? 'selected' : '' }}>
                                    {{ __('dashboard.lodging_fees') }}
                                </option>
                                <option value="tourism_fee" {{ $tax->custom_name == 'tourism_fee' ? 'selected' : '' }}>
                                    {{ __('dashboard.tourism_fee') }}
                                </option>
                            @else
                                <option value="vat" {{ $tax->custom_name == 'vat' ? 'selected' : '' }}>
                                    {{ __('dashboard.vat') }}
                                </option>
                            @endif

                        </select>
                    </div>
                </div>

                <!-- Method & Amount -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">
                            {{ __('dashboard.method') }}
                        </label>

                        <select class="form-select" name="method" required>
                            <option value="percentage" {{ $tax->method == 'percentage' ? 'selected' : '' }}>
                                {{ __('dashboard.percentage') }}
                            </option>
                            <option value="fixed_amount_reservation"
                                {{ $tax->method == 'fixed_amount_reservation' ? 'selected' : '' }}>
                                {{ __('dashboard.fixed_amount_for_reservation') }}
                            </option>
                            <option value="fixed_amount_per_night"
                                {{ $tax->method == 'fixed_amount_per_night' ? 'selected' : '' }}>
                                {{ __('dashboard.fixed_amount_per_night') }}
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">
                            {{ __('dashboard.amount') }}
                        </label>

                        <input type="number" class="form-control" name="amount" value="{{ $tax->amount }}" required>
                    </div>
                </div>

                <!-- Applied On -->
                @php
                    $appliedOn = $tax->applied_on ?? [];
                @endphp

                <div class="row mb-3">
                    <div class="col-md-3">

                        <label class="form-label">
                            {{ __('dashboard.applied_on') }}
                        </label>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="checkAll"
                                {{ count($appliedOn) == 3 ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold">
                                {{ __('dashboard.all') }}
                            </label>
                        </div>

                        @foreach (['rent', 'penalties', 'extras'] as $item)
                            <div class="form-check">
                                <input class="form-check-input applied-checkbox" type="checkbox" name="applied_on[]"
                                    value="{{ $item }}" {{ in_array($item, $appliedOn) ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    {{ __('dashboard.' . $item) }}
                                </label>
                            </div>
                        @endforeach

                    </div>
                </div>

                <!-- Dates -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label class="form-label">
                            {{ __('dashboard.start_date') }}
                        </label>
                        <input type="date" class="form-control" name="start_date"
                            value="{{ $tax->start_date ? \Carbon\Carbon::parse($tax->start_date)->format('Y-m-d') : '' }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">
                            {{ __('dashboard.end_date') }}
                        </label>
                        <input type="date" class="form-control" name="end_date"
                            value="{{ $tax->end_date ? \Carbon\Carbon::parse($tax->end_date)->format('Y-m-d') : '' }}">
                    </div>
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-end">
                    <a href="{{ route('setup-sidebar.taxes.index') }}" class="btn btn-outline-danger me-3">
                        {{ __('dashboard.discard') }}
                    </a>

                    <button type="submit" class="btn btn-primary">
                        {{ __('dashboard.update_customization') }}
                    </button>
                </div>

            </form>
        </div>
    </main>
@endsection
@push('scripts')
    <script>
        window.translations = {
            fee_name: "{{ __('dashboard.fee_name') }}",
            tax_name: "{{ __('dashboard.tax_name') }}",
            select_fee_name: "{{ __('dashboard.select_the_fee_name') }}",
            select_tax_name: "{{ __('dashboard.select_the_tax_name') }}",
            lodging_fees: "{{ __('dashboard.lodging_fees') }}",
            tourism_fee: "{{ __('dashboard.tourism_fee') }}",
            vat: "{{ __('dashboard.vat') }}",
        };
        document.addEventListener("DOMContentLoaded", function() {

            const radios = document.querySelectorAll('input[name="type"]');
            const dropdown = document.getElementById('customNameDropdown');
            const label = document.getElementById('nameLabel');

            radios.forEach(radio => {
                radio.addEventListener('change', function() {

                    dropdown.innerHTML = '';

                    if (this.value === 'fee') {

                        label.textContent = window.translations.fee_name;

                        dropdown.innerHTML = `
                    <option value="">${window.translations.select_fee_name}</option>
                    <option value="lodging_fee">${window.translations.lodging_fees}</option>
                    <option value="tourism_fee">${window.translations.tourism_fee}</option>
                `;

                    } else {

                        label.textContent = window.translations.tax_name;

                        dropdown.innerHTML = `
                    <option value="">${window.translations.select_tax_name}</option>
                    <option value="vat">${window.translations.vat}</option>
                `;
                    }
                });
            });

        });

        document.addEventListener("DOMContentLoaded", function() {

            const checkAll = document.getElementById("checkAll");
            const checkboxes = document.querySelectorAll(".applied-checkbox");

            // When ALL is checked
            checkAll.addEventListener("change", function() {
                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                });
            });

            // If any single checkbox is unchecked → uncheck ALL
            checkboxes.forEach(cb => {
                cb.addEventListener("change", function() {
                    if (!this.checked) {
                        checkAll.checked = false;
                    }

                    // If all are checked manually → check ALL
                    const allChecked = Array.from(checkboxes).every(c => c.checked);
                    if (allChecked) {
                        checkAll.checked = true;
                    }
                });
            });

        });
    </script>
@endpush
