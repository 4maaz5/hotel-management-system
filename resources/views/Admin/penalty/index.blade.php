@extends('layouts.app')

@section('title', 'Rules Penalty')
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
        <div class="page-category">{{ __('dashboard.rules') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.penalties') }}</h2>
            </div>
            <div class="n-table__top-btns">

                <button class="n-button n-button--primary" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    {{ __('dashboard.filter') }}
                </button>
                <div>
                    @can('penalties.add')
                        <a href="{{ route('setup-sidebar.penalty.create') }}" class="n-button n-button--green"
                            style="text-decoration:none;" tabindex="0">
                            {{ __('dashboard.new_penalty') }}
                        </a>
                    @endcan

                </div>
            </div>
        </div>

        <!-- Filter Collapse -->
        <div class="collapse mb-3" id="filterCollapse">
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <form method="GET" action="{{ route('setup-sidebar.penalty.index') }}">
                        <div class="row g-4 align-items-end">

                            {{-- Name --}}
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label fw-semibold">
                                    {{ __('dashboard.name') }}
                                </label>

                                <input type="text" name="name" value="{{ request('name') }}" class="form-control"
                                    placeholder="{{ __('dashboard.enter_name') }}">
                            </div>

                            {{-- Amount --}}
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label fw-semibold">
                                    {{ __('dashboard.amount') }}
                                </label>

                                <input type="number" name="value" step="0.01" value="{{ request('value') }}"
                                    class="form-control" placeholder="{{ __('dashboard.enter_amount') }}">
                            </div>

                            {{-- Type --}}
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label fw-semibold">
                                    {{ __('dashboard.type') }}
                                </label>

                                <select name="penalty_type" class="form-select">
                                    <option value="">
                                        {{ __('dashboard.all') }}
                                    </option>

                                    <option value="currency" {{ request('penalty_type') == 'currency' ? 'selected' : '' }}>
                                        $
                                    </option>

                                    <option value="percentage"
                                        {{ request('penalty_type') == 'percentage' ? 'selected' : '' }}>
                                        %
                                    </option>
                                </select>
                            </div>

                            {{-- Category --}}
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label fw-semibold">
                                    {{ __('dashboard.category') }}
                                </label>

                                <select name="category" class="form-select">
                                    <option value="">
                                        {{ __('dashboard.all') }}
                                    </option>

                                    <option value="user_defined"
                                        {{ request('category') == 'user_defined' ? 'selected' : '' }}>
                                        User Defined
                                    </option>

                                    <option value="early_checkin"
                                        {{ request('category') == 'early_checkin' ? 'selected' : '' }}>
                                        Early Check-In
                                    </option>

                                    <option value="late_checkout"
                                        {{ request('category') == 'late_checkout' ? 'selected' : '' }}>
                                        Late Check-Out
                                    </option>

                                    <option value="no_show" {{ request('category') == 'no_show' ? 'selected' : '' }}>
                                        Cancel / No Show
                                    </option>
                                </select>
                            </div>

                            {{-- Status --}}
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label fw-semibold">
                                    {{ __('dashboard.status') }}
                                </label>

                                <select name="status" class="form-select">
                                    <option value="">
                                        {{ __('dashboard.all_status') }}
                                    </option>

                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>
                                        {{ __('dashboard.active') }}
                                    </option>

                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>
                                        {{ __('dashboard.inactive') }}
                                    </option>
                                </select>
                            </div>

                            {{-- Buttons --}}
                            <div class="col-12 d-flex justify-content-end gap-2 pt-2">

                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-search me-1"></i>
                                    {{ __('dashboard.search') }}
                                </button>

                                <a href="{{ route('setup-sidebar.penalty.index') }}" class="btn btn-light border px-4">
                                    <i class="bi bi-arrow-clockwise me-1"></i>
                                    {{ __('dashboard.reset') }}
                                </a>

                            </div>

                        </div>
                    </form>

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
                                <th>{{ __('dashboard.name') }}</th>
                                <th style="width:120px;">{{ __('dashboard.amount') }}</th>
                                <th style="width:120px;">{{ __('dashboard.type') }}</th>
                                {{-- <th style="width:120px;">{{ __('dashboard.calculated_of') }}</th> --}}
                                <th style="width:120px;">{{ __('dashboard.category') }}</th>
                                <th>{{ __('dashboard.status') }}</th>
                                <th style="width:130px;">{{ __('dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                            @forelse($penalties as $penalty)
                                <tr>
                                    <td><i class="bi bi-arrows-move text-muted"></i></td>

                                    <td>{{ $penalty->name }}</td>

                                    <td>
                                        {{ $penalty->value ?? '-' }}
                                    </td>

                                    <td>
                                        {{ ucfirst($penalty->penalty_type ?? '-') }}
                                    </td>

                                    {{-- <td>
                                        {{ $penalty->calculated_of ?? '-' }}
                                    </td> --}}

                                    <td>
                                        {{ str_replace('_', ' ', $penalty->category) }}
                                    </td>

                                    <td>
                                        @if ($penalty->is_active)
                                            <span class="badge bg-success">{{ __('dashboard.active') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ __('dashboard.inactive') }}</span>
                                        @endif
                                    </td>

                                    <td>
                                        @can('penalties.edit')
                                            <a href="{{ route('setup-sidebar.penalty.edit', $penalty->id) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        @endcan
                                        @can('penalties.delete')
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteCustomRateModal{{ $penalty->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endcan

                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="8">
                                        {{ __('dashboard.no_penalty_records_found') }}
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>

                </div>

            </div>

        </div>

    </main>



    @foreach ($penalties as $penalty)
        <div class="modal fade" id="deleteCustomRateModal{{ $penalty->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('dashboard.delete_penalty') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p>{{ __('dashboard.delete_penalty_confirmation') }}</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.cancel') }}
                        </button>
                        <form action="{{ route('setup-sidebar.penalty.delete', $penalty->id) }}" method="POST">
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
