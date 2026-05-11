@extends('layouts.app')

@section('title', 'Print Option')

@section('content')
<div class=" bg-white p-3" style="border-radius:15px;">
    <div class="page-category mb-3">{{ __('dashboard.reporting') }}</div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h4">{{ __('dashboard.printing_options') }}</h2>
            <small class="">{{ __('dashboard.select_the_default_options_for_every_voucher_type') }}

        </div>
        <form method="POST" action="{{ route('setup-sidebar.print_option.update') }}">
            @csrf
            @can('printing_option.edit')
                <button class="btn btn-primary">{{ __('dashboard.save_changes') }}</button>
            @endcan

    </div>
    <div class="mb-4">

        <label class="fw-bold">
            {{ __('dashboard.default_contract_template') }}
        </label>

        <div class="row mt-2">

            {{-- Double Language --}}
            <div class="col-md-6">
                <div class="card p-3 mb-2">
                    <div class="form-check">

                        <input class="form-check-input" type="radio" name="contract_template_type" value="double"
                            {{ optional($reportSetting)->contract_template_type == 'double' ? 'checked' : '' }}>

                        <label class="form-check-label ms-2 text-dark">
                            <strong>{{ __('dashboard.double_language') }}</strong><br>

                            <small class="text-muted">
                                {{ __('dashboard.report_will_include_both_arabic_english_language_at_the_same_time') }}
                            </small>
                        </label>

                    </div>
                </div>
            </div>

            {{-- Single Language --}}
            <div class="col-md-6">
                <div class="card p-3 mb-2">
                    <div class="form-check">

                        <input class="form-check-input" type="radio" name="contract_template_type" value="single"
                            {{ optional($reportSetting)->contract_template_type == 'single' ? 'checked' : '' }}>

                        <label class="form-check-label ms-2 text-dark">
                            <strong>{{ __('dashboard.single_language') }}</strong><br>

                            <small class="text-muted">
                                {{ __('dashboard.report_will_include_single_language_per_time') }}
                            </small>
                        </label>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="mb-4">
        <label class="fw-bold">{{ __('dashboard.default_printing_paper') }}</label>
        <div class="small mb-2">{{ __('dashboard.apply_for_all_selected_reports') }}</div>
        <div class="btn-group">
            <button type="button" id="selectLetterHead"
                class="btn btn-outline-secondary">{{ __('dashboard.letter_head_paper') }}</button>
            <button type="button" id="selectBlankPaper"
                class="btn btn-outline-secondary">{{ __('dashboard.blank_paper') }}</button>
        </div>
    </div>

    <table class="table table-bordered table-striped bg-white">
        <thead>
            <tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th>{{ __('dashboard.report_name') }}</th>
                <th>{{ __('dashboard.letter_head_paper') }}</th>
                <th>{{ __('dashboard.blank_paper') }}</th>
                <th>{{ __('dashboard.cashier_paper') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($options as $option)
                <tr>
                    <td>
                        <input type="checkbox" class="enable-checkbox" name="reports[{{ $option->id }}][enabled]"
                            {{ $option->enabled ? 'checked' : '' }}>
                    </td>
                    <td>{{ $option->report_name }}</td>
                    <td>
                        <input type="checkbox" class="letter-checkbox" name="reports[{{ $option->id }}][letter_head]"
                            {{ $option->letter_head ? 'checked' : '' }}>
                    </td>
                    <td>
                        <input type="checkbox" class="blank-checkbox" name="reports[{{ $option->id }}][blank_paper]"
                            {{ $option->blank_paper ? 'checked' : '' }}>
                    </td>
                    <td>
                        @if ($option->report_key === 'invoice_walk_in')
                            <input type="checkbox" name="reports[{{ $option->id }}][cashier_paper]"
                                {{ $option->cashier_paper ? 'checked' : '' }}>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </form>
</div>
@endsection
@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const enableCheckboxes = document.querySelectorAll(".enable-checkbox");
            const letterCheckboxes = document.querySelectorAll(".letter-checkbox");
            const blankCheckboxes = document.querySelectorAll(".blank-checkbox");

            document.getElementById("selectAll").addEventListener("change", function() {
                let checked = this.checked;
                enableCheckboxes.forEach(cb => cb.checked = checked);
            });

            document.getElementById("selectLetterHead").addEventListener("click", function() {

                enableCheckboxes.forEach((enable, index) => {
                    if (enable.checked) {
                        letterCheckboxes[index].checked = true;
                        blankCheckboxes[index].checked = false;
                    }
                });

            });

            document.getElementById("selectBlankPaper").addEventListener("click", function() {

                enableCheckboxes.forEach((enable, index) => {
                    if (enable.checked) {
                        blankCheckboxes[index].checked = true;
                        letterCheckboxes[index].checked = false;
                    }
                });

            });

            letterCheckboxes.forEach((letter, index) => {
                letter.addEventListener("change", function() {
                    if (this.checked) {
                        blankCheckboxes[index].checked = false;
                    }
                });
            });

            blankCheckboxes.forEach((blank, index) => {
                blank.addEventListener("change", function() {
                    if (this.checked) {
                        letterCheckboxes[index].checked = false;
                    }
                });
            });

        });
    </script>
@endpush
