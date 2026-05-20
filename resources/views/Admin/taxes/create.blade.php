@extends('layouts.app')

@section('title', 'Add Tax and Fees')
@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">

        <div class="container mt-4">

            <!-- Page Header -->
            <div class="mb-4">
                <h4 class="fw-bold">
                    {{ __('dashboard.financial') }} - {{ __('dashboard.taxes_and_fees_customization') }}
                </h4>
                <p class="mb-0">
                    {{ __('dashboard.fill_the_information_to_add_new_customized_tax_or_fee') }}
                </p>
            </div>

            <form action="{{ route('setup-sidebar.taxes.store') }}" method="POST">
                @csrf

                <!-- Use for expenses -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="isExpenses" name="is_expenses">
                            <label class="form-check-label ms-2" for="isExpenses">
                                {{ __('dashboard.use_for_expenses_vouchers') }}
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Type -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('dashboard.type') }} <span
                                class="text-danger">*</span></label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" value="tax" checked>
                                <label class="form-check-label">{{ __('dashboard.tax') }}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" value="fee">
                                <label class="form-check-label">{{ __('dashboard.fee') }}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tax / Fee Name -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">
                            <span id="nameLabel">{{ __('dashboard.tax_name') }}</span>
                            <span class="text-danger">*</span>
                        </label>

                        <select class="form-select" name="custom_name" id="customNameDropdown" required>
                            <option value="">{{ __('dashboard.select_the_tax_name') }}</option>
                            <option value="vat">{{ __('dashboard.vat') }}</option>
                        </select>
                    </div>
                </div>

                <!-- Method & Amount -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('dashboard.method') }} <span class="text-danger">*</span></label>
                        <select class="form-select" name="method" required>
                            <option value="">{{ __('dashboard.select_the_method') }}</option>
                            <option value="percentage">{{ __('dashboard.percentage') }} (%)</option>
                            <option value="fixed_amount_reservation">{{ __('dashboard.fixed_amount_for_reservation') }}
                            </option>
                            <option value="fixed_amount_per_night">{{ __('dashboard.fixed_amount_per_night') }}</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">{{ __('dashboard.amount') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="amount"
                                placeholder="{{ __('dashboard.type_the_amount') }}" required>
                            <span class="input-group-text">SAR</span>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">
                            {{ __('dashboard.applied_on') }} <span class="text-danger">*</span>
                        </label>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="checkAll">
                            <label class="form-check-label fw-semibold" for="checkAll">
                                {{ __('dashboard.all') }}
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input applied-checkbox" type="checkbox" name="applied_on[]"
                                value="rent" id="rent">
                            <label class="form-check-label" for="rent">
                                {{ __('dashboard.rent') }}
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input applied-checkbox" type="checkbox" name="applied_on[]"
                                value="penalties" id="penalties">
                            <label class="form-check-label" for="penalties">
                                {{ __('dashboard.penalties') }}
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input applied-checkbox" type="checkbox" name="applied_on[]"
                                value="extras" id="extras">
                            <label class="form-check-label" for="extras">
                                {{ __('dashboard.extras') }}
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Max Length -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('dashboard.does_this_tax_fee_has_a_max_length') }}</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="has_max_length" value="1">
                                <label class="form-check-label">{{ __('dashboard.yes') }}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="has_max_length" value="0"
                                    checked>
                                <label class="form-check-label">{{ __('dashboard.no') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">{{ __('dashboard.max_length') }} ({{ __('dashboard.nights') }})</label>
                        <input type="number" class="form-control" name="max_length"
                            placeholder="{{ __('dashboard.enter_nights') }}">
                    </div>
                </div>

                <!-- Start & End Date -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('dashboard.start_date') }} <span
                                class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="start_date" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">{{ __('dashboard.end_date') }}</label>
                        <input type="date" class="form-control" name="end_date">
                    </div>
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-end">
                    <a href="{{ route('setup-sidebar.taxes.index') }}" type="reset"
                        class="btn btn-outline-danger me-3">
                        {{ __('dashboard.discard') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        {{ __('dashboard.create_customization') }}
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
