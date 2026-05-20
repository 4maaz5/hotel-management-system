@extends('layouts.app')

@section('title', 'Taxes and Fees')
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
        <div class="page-category">{{ __('dashboard.financials') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.taxes_and_fees_customization') }}</h2>
                <div class="page-header__subtitle">
                    {{ __('dashboard.view_and_manage_taxes_and_fees_for_the_system') }}</div>
            </div>
            <div class="n-table__top-btns">
                <button class="n-button n-button--primary">
                    {{ __('dashboard.filter') }}
                </button>
                <div>
                    @can('tax_customization.add')
                        <a href="{{ route('setup-sidebar.taxes.create') }}" class="n-button n-button--green"
                            style="text-decoration:none;" tabindex="0">
                            {{ __('dashboard.new_tax_fee') }}
                        </a>
                    @endcan

                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('setup-sidebar.taxes.index') }}">

            <div class="filter-form__container is-open mb-4">
                <div class="card">
                    <div class="card-body">

                        <div class="row g-3">

                            <!-- Status -->
                            <div class="col-lg-3">
                                <label>{{ __('dashboard.status') }}</label>

                                <select name="status" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                        {{ __('dashboard.active') }}
                                    </option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                        {{ __('dashboard.inactive') }}
                                    </option>
                                </select>
                            </div>

                            <!-- Tax Name -->
                            <div class="col-lg-3">
                                <label>{{ __('dashboard.tax_name') }}</label>

                                <input type="text" name="name" class="form-control" value="{{ request('name') }}"
                                    placeholder="{{ __('dashboard.tax_name') }}">
                            </div>

                            <!-- Method -->
                            <div class="col-lg-3">
                                <label>{{ __('dashboard.method') }}</label>

                                <select name="method" class="form-select">

                                    <option value="">{{ __('dashboard.all') }}</option>

                                    <option value="percentage" {{ request('method') == 'percentage' ? 'selected' : '' }}>
                                        {{ __('dashboard.percentage') }}
                                    </option>

                                    <option value="fixed_amount_reservation"
                                        {{ request('method') == 'fixed_amount_reservation' ? 'selected' : '' }}>
                                        {{ __('dashboard.fixed_amount_for_reservation') }}
                                    </option>

                                    <option value="fixed_amount_per_night"
                                        {{ request('method') == 'fixed_amount_per_night' ? 'selected' : '' }}>
                                        {{ __('dashboard.fixed_amount_per_night') }}
                                    </option>

                                </select>
                            </div>

                            <!-- Expenses Switch -->
                            <div class="col-lg-3 d-flex align-items-end">

                                <div class="form-check form-switch">

                                    <input class="form-check-input" type="checkbox" name="is_expenses"
                                        {{ request('is_expenses') ? 'checked' : '' }}>

                                    <label class="form-check-label ms-2">
                                        {{ __('dashboard.use_for_expenses_vouchers') }}
                                    </label>

                                </div>
                            </div>

                            <!-- Start Date From -->
                            <div class="col-lg-3">
                                <label>{{ __('dashboard.start_date') }} {{ __('dashboard.from') }}</label>

                                <input type="date" name="start_from" value="{{ request('start_from') }}"
                                    class="form-control">
                            </div>

                            <!-- Start Date To -->
                            <div class="col-lg-3">
                                <label>{{ __('dashboard.start_date') }} {{ __('dashboard.to') }}</label>

                                <input type="date" name="start_to" value="{{ request('start_to') }}"
                                    class="form-control">
                            </div>

                            <!-- Search Button -->
                            <div class="col-12">
                                <div class="text-end">
                                    <button class="btn btn-primary">
                                        {{ __('dashboard.search') }}
                                    </button>
                                    <a href="{{ route('setup-sidebar.taxes.index') }}" class="btn btn-secondary">
                                        {{ __('dashboard.reset') }}
                                    </a>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

        </form>

        <div class="container mt-5">
            <table class="table table-bordered table-striped align-middle text-center bg-white">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('dashboard.name') }}</th>
                        <th>{{ __('dashboard.type') }}</th>
                        <th>{{ __('dashboard.status') }}</th>
                        <th>{{ __('dashboard.applied_on') }}</th>
                        <th>{{ __('dashboard.method') }}</th>
                        <th>{{ __('dashboard.amount') }}</th>
                        <th>{{ __('dashboard.start_date') }}</th>
                        <th>{{ __('dashboard.end_date') }}</th>
                        <th>{{ __('dashboard.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customizations as $item)
                        <tr>
                            <!-- Name -->
                            <td>{{ ucfirst(str_replace('_', ' ', $item->custom_name)) }}</td>

                            <!-- Type -->
                            <td>
                                {{ ucfirst($item->type) }}
                            </td>

                            <!-- Status -->
                            <td>
                                @if (
                                    $item->start_date <= now()->toDateString() &&
                                        (is_null($item->end_date) || $item->end_date >= now()->toDateString()))
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>

                            <!-- Applied On -->
                            <td>
                                {{ implode(', ', array_map(fn($v) => ucfirst($v), $item->applied_on ?? [])) }}
                            </td>

                            <!-- Method -->
                            <td>
                                {{ ucfirst(str_replace('_', ' ', $item->method)) }}
                            </td>

                            <!-- Amount -->
                            <td>
                                @if ($item->method === 'percentage')
                                    {{ $item->amount }}%
                                @else
                                    {{ number_format($item->amount, 2) }} SAR
                                @endif
                            </td>

                            <!-- Start Date -->
                            <td>
                                {{ \Carbon\Carbon::parse($item->start_date)->format(system_date_format()) }}
                            </td>

                            <!-- End Date -->
                            <td>
                                {{ $item->end_date ? \Carbon\Carbon::parse($item->end_date)->format(system_date_format()) : '-' }}
                            </td>

                            <!-- Actions -->
                            <td>
                                @can('tax_customization.view')
                                    <a href="{{ route('setup-sidebar.taxes.view', $item->id) }}"
                                        class="btn btn-sm btn-primary me-1">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                @endcan
                                @can('tax_customization.edit')
                                    <a href="{{ route('setup-sidebar.taxes.edit', $item->id) }}"
                                        class="btn btn-sm btn-warning me-1">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                @endcan
                                @can('tax_customization.delete')
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteCustomRateModal{{ $item->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endcan

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                {{ __('dashboard.no_customization_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </main>

    @foreach ($customizations as $customization)
        <div class="modal fade" id="deleteCustomRateModal{{ $customization->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('dashboard.delete_custom_rate') }} – {{ $customization->name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p>{{ __('dashboard.tax_customization_delete_confirmation') }}</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.cancel') }}
                        </button>
                        <form action="{{ route('setup-sidebar.taxes.delete', $customization->id) }}" method="POST">
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
    </script>
@endpush
