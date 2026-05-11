@extends('layouts.app')

@section('title', 'Property Facility')
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
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">

        <!-- Page Header -->
        <div class="page-category">{{ __('dashboard.reporting') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.numbering_options') }}</h2>
                <div class="page-header__subtitle">
                    {{ __('dashboard.set_the_numbering_options_you_will_be_use_on_your_properties') }}</div>
            </div>

        </div>

        <div class="container mt-5">
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-bordered" cellpadding="8" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('dashboard.report_name') }}</th>
                                <th>{{ __('dashboard.naming_method') }}</th>
                                <th>{{ __('dashboard.prefix') }}</th>
                                <th>{{ __('dashboard.starting_no') }}</th>
                                <th>{{ __('dashboard.example') }}</th>
                                <th>{{ __('dashboard.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($settings as $index => $setting)
                                <tr>
                                    <td>{{ $index + 1 }}</td>

                                    <td>{{ $setting->name }}</td>

                                    <td>
                                        {{ $setting->naming_method_label }}
                                    </td>

                                    <td>
                                        {{ $setting->prefix ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $setting->current_sequence }}
                                    </td>

                                    <td>
                                        {{ $setting->example }}
                                    </td>

                                    <td>
                                        @can('numbering_option.edit')
                                            <a href="{{ route('setup-sidebar.numbering_option.index', ['edit' => $setting->id]) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">{{ __('dashboard.no_report_settings_found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>

            </div>
        </div>

    </main>
    @if (isset($editSetting))

        <div class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">

                    <form method="POST" action="{{ route('setup-sidebar.numbering_option.update', $editSetting->id) }}">

                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5>{{ __('dashboard.edit_report_setting') }}</h5>

                            <a href="{{ route('setup-sidebar.numbering_option.index') }}" class="btn-close"></a>
                        </div>

                        <div class="modal-body">

                            {{-- Naming Method --}}
                            <div class="mb-3">
                                <label>{{ __('dashboard.naming_method') }}</label>

                                <select name="naming_method" class="form-control">

                                    @foreach ([
            'sequence' => __('dashboard.sequence_only'),
            'year_sequence' => __('dashboard.year') . ' + ' . __('dashboard.sequence'),
            'prefix_sequence' => __('dashboard.prefix') . ' + ' . __('dashboard.sequence'),
            'prefix_year_sequence' => __('dashboard.prefix') . ' + ' . __('dashboard.year') . ' + ' . __('dashboard.sequence'),
        ] as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ $editSetting->naming_method == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="mb-3 form-check form-switch" id="resetYearlyWrapper" style="display: none;">

                                <input type="hidden" name="reset_yearly" value="0">

                                <input class="form-check-input" type="checkbox" name="reset_yearly" value="1"
                                    {{ $editSetting->reset_yearly ? 'checked' : '' }}>

                                <label class="form-check-label">
                                    {{ __('dashboard.reset_sequence_yearly') }}
                                </label>
                            </div>

                            {{-- Prefix --}}
                            <div class="mb-3">
                                <label>{{ __('dashboard.prefix') }}</label>
                                <input type="text" name="prefix" class="form-control"
                                    value="{{ $editSetting->prefix }}">
                            </div>

                            {{-- Sequence --}}
                            <div class="mb-3">
                                <label>{{ __('dashboard.sequence_start_no') }}</label>
                                <input type="number" name="current_sequence" class="form-control"
                                    value="{{ $editSetting->current_sequence }}">
                            </div>

                        </div>

                        <div class="modal-footer">

                            <a href="{{ route('setup-sidebar.numbering_option.index') }}" class="btn btn-secondary">
                                {{ __('dashboard.cancel') }}
                            </a>

                            <button class="btn btn-primary">
                                {{ __('dashboard.save') }}
                            </button>

                        </div>

                    </form>

                </div>
            </div>
        </div>

    @endif
@endsection
@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            function toggleResetYearly() {

                let method = document.querySelector('[name="naming_method"]').value;

                let wrapper = document.getElementById("resetYearlyWrapper");

                if (method === "year_sequence" ||
                    method === "prefix_year_sequence") {
                    wrapper.style.display = "block";
                } else {
                    wrapper.style.display = "none";
                }
            }
            toggleResetYearly();

            document.querySelector('[name="naming_method"]')
                .addEventListener("change", toggleResetYearly);

        });
    </script>
@endpush
