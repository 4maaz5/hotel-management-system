@extends('layouts.app')

@section('title', 'Outlet Setup')
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
        <div class="page-category">{{ __('dashboard.outlets') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.outlet_setup') }}</h2>
            </div>
            <div class="n-table__top-btns">
                <button class="n-button n-button--primary" type="button" data-bs-toggle="collapse"
                    data-bs-target="#filterCollapse">
                    {{ __('dashboard.filter') }}
                </button>
                <div>
                    @can('outlet_setup.add')
                        <a href="{{ route('setup-sidebar.unit.create') }}" class="n-button n-button--green"
                            data-bs-toggle="modal" data-bs-target="#addOutletModal" style="text-decoration:none;"
                            tabindex="0">
                            {{ __('dashboard.new_outlet') }}
                        </a>
                    @endcan

                </div>
            </div>
        </div>

        <!-- Filter Collapse -->
        <div class="collapse mb-3" id="filterCollapse">
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <form method="GET" action="{{ route('setup-sidebar.outlet_setup.index') }}">
                        <div class="row g-4 align-items-end">

                            <!-- Status -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label fw-semibold">
                                    {{ __('dashboard.status') }}
                                </label>
                                <select name="status" class="form-select form-select-md">
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

                            <!-- Name -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label fw-semibold">
                                    {{ __('dashboard.name') }}
                                </label>
                                <input type="text" name="name" value="{{ request('name') }}" class="form-control"
                                    placeholder="{{ __('dashboard.enter_name') }}">
                            </div>

                            <!-- Code -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label fw-semibold">
                                    {{ __('dashboard.outlet_code') }}
                                </label>
                                <input type="number" name="outlet_code" min="0" max="999"
                                    value="{{ request('outlet_code') }}" class="form-control"
                                    placeholder="{{ __('dashboard.enter_outlet_code') }}">
                            </div>

                            <!-- Operating Status -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label fw-semibold">
                                    {{ __('dashboard.operating_status') }}
                                </label>
                                <select name="operating_status" class="form-select">
                                    <option value="">
                                        {{ __('dashboard.all_operating_status') }}
                                    </option>
                                    <option value="open" {{ request('operating_status') == 'open' ? 'selected' : '' }}>
                                        {{ __('dashboard.open') }}
                                    </option>
                                    <option value="closed" {{ request('operating_status') == 'closed' ? 'selected' : '' }}>
                                        {{ __('dashboard.closed') }}
                                    </option>
                                    <option value="renovation"
                                        {{ request('operating_status') == 'renovation' ? 'selected' : '' }}>
                                        {{ __('dashboard.under_renovation') }}
                                    </option>
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div class="col-12 d-flex justify-content-end gap-2 pt-2">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-search me-1"></i>
                                    {{ __('dashboard.search') }}
                                </button>

                                <a href="{{ route('setup-sidebar.outlet_setup.index') }}"
                                    class="btn btn-light border px-4">
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
            <table class="table table-bordered table-striped align-middle text-center bg-white">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('dashboard.status') }}</th>
                        <th>{{ __('dashboard.operating_status') }}</th>
                        <th>{{ __('dashboard.code') }}</th>
                        <th>{{ __('dashboard.name') }}</th>
                        <th>{{ __('dashboard.description') }}</th>
                        <th>{{ __('dashboard.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($outlets as $key => $outlet)
                        <tr>
                            <td>{{ $key + 1 }}</td>

                            <!-- Operating Status -->
                            <td>
                                @if ($outlet->operating_status == 'open')
                                    <span class="badge bg-success">✔ Open</span>
                                @elseif($outlet->operating_status == 'closed')
                                    <span class="badge bg-danger">✖ Closed</span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        {{ ucfirst($outlet->operating_status) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Status (Active/Inactive) -->
                            <td>
                                @if ($outlet->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>

                            <!-- Outlet Code -->
                            <td>{{ $outlet->outlet_code }}</td>

                            <!-- Name -->
                            <td>{{ $outlet->name }}</td>

                            <!-- Description -->
                            <td>{{ $outlet->description }}</td>

                            <!-- Actions -->
                            <td>
                                @can('outlet_setup.edit')
                                    <!-- Edit Button -->
                                    <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal"
                                        data-bs-target="#editOutletModal{{ $outlet->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                @endcan

                                @can('outlet_setup.delete')
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteCustomRateModal{{ $outlet->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endcan

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No outlets found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>


        </div>
    </main>


    <!-- Add Outlet Modal -->
    <div class="modal fade" id="addOutletModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('setup-sidebar.outlet_setup.store') }}" id="addOutletForm">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('dashboard.add_outlet') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="row">
                            <!-- Operating Status -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    {{ __('dashboard.operating_status') }} *
                                </label>
                                <select name="operating_status" class="form-control @error('operating_status') is-invalid @enderror" required>
                                    <option value="">
                                        {{ __('dashboard.select_status') }}
                                    </option>
                                    <option value="open" {{ old('operating_status') == 'open' ? 'selected' : '' }}>
                                        {{ __('dashboard.open') }}
                                    </option>
                                    <option value="closed" {{ old('operating_status') == 'closed' ? 'selected' : '' }}>
                                        {{ __('dashboard.closed') }}
                                    </option>
                                    <option value="renovation" {{ old('operating_status') == 'renovation' ? 'selected' : '' }}>
                                        {{ __('dashboard.under_renovation') }}
                                    </option>
                                </select>
                                @error('operating_status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Outlet Code -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    {{ __('dashboard.outlet_code') }} *
                                </label>
                                <input type="text" name="outlet_code" maxlength="3" class="form-control @error('outlet_code') is-invalid @enderror"
                                    value="{{ old('outlet_code') }}"
                                    placeholder="{{ __('dashboard.enter_3_digit_code') }}" required>
                                @error('outlet_code')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Name -->
                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('dashboard.name') }} *
                            </label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('dashboard.description') }}
                            </label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">
                            {{ __('dashboard.save_outlet') }}
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.cancel') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach ($outlets as $outlet)
        <!-- Edit Modal -->
        <div class="modal fade" id="editOutletModal{{ $outlet->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <form action="{{ route('setup-sidebar.outlet_setup.update', $outlet->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ __('dashboard.edit_outlet') }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <!-- Status -->
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="status" value="1"
                                    {{ $outlet->status ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    {{ __('dashboard.active') }}
                                </label>
                            </div>

                            <div class="row">

                                <!-- Operating Status -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        {{ __('dashboard.operating_status') }} *
                                    </label>
                                    <select name="operating_status" class="form-control @error('operating_status') is-invalid @enderror" required>
                                        <option value="open"
                                            {{ old('operating_status', $outlet->operating_status) == 'open' ? 'selected' : '' }}>
                                            {{ __('dashboard.open') }}
                                        </option>
                                        <option value="closed"
                                            {{ old('operating_status', $outlet->operating_status) == 'closed' ? 'selected' : '' }}>
                                            {{ __('dashboard.closed') }}
                                        </option>
                                        <option value="renovation"
                                            {{ old('operating_status', $outlet->operating_status) == 'renovation' ? 'selected' : '' }}>
                                            {{ __('dashboard.under_renovation') }}
                                        </option>
                                    </select>
                                    @error('operating_status')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Outlet Code -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        {{ __('dashboard.outlet_code') }} *
                                    </label>
                                    <input type="text" name="outlet_code" maxlength="3" class="form-control @error('outlet_code') is-invalid @enderror"
                                        value="{{ old('outlet_code', $outlet->outlet_code) }}" required>
                                    @error('outlet_code')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Name -->
                            <div class="mb-3">
                                <label class="form-label">
                                    {{ __('dashboard.name') }} *
                                </label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $outlet->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label class="form-label">
                                    {{ __('dashboard.description') }}
                                </label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $outlet->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">
                                {{ __('dashboard.update') }}
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ __('dashboard.cancel') }}
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteCustomRateModal{{ $outlet->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('dashboard.delete_outlet') }} – {{ $outlet->name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p>{{ __('dashboard.delete_outlet_confirmation') }}</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.cancel') }}
                        </button>
                        <form action="{{ route('setup-sidebar.outlet_setup.delete', $outlet->id) }}" method="POST">
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

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var addModal = document.getElementById('addOutletModal');
                if (addModal) {
                    var modal = new bootstrap.Modal(addModal);
                    modal.show();
                }
            });
        </script>
    @endif
@endsection
