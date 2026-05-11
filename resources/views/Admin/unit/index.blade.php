@extends('layouts.app')

@section('title', 'Create Unit')

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
        <div class="page-category">{{ __('dashboard.company') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.units') }}</h2>
                <div class="page-header__subtitle">{{ __('dashboard.view_and_manage_units') }}</div>
            </div>
            <div class="n-table__top-btns">
                <button class="n-button n-button--primary">
                    {{ __('dashboard.filter') }}
                </button>
                <div>
                    @can('unit.add')
                        <a href="{{ route('setup-sidebar.unit.create') }}" class="n-button n-button--green"
                            style="text-decoration:none;" tabindex="0">
                            {{ __('dashboard.add_unit') }}
                        </a>
                    @endcan

                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('setup-sidebar.unit.index') }}">
            <div class="filter-form__container mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-3">
                                <select name="block_id" class="form-select">
                                    <option value="">{{ __('dashboard.select_block') }}</option>
                                    @foreach ($blocks as $block)
                                        <option value="{{ $block->id }}"
                                            {{ request('block_id') == $block->id ? 'selected' : '' }}>
                                            {{ $block->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select name="floor_id" class="form-select">
                                    <option value="">{{ __('dashboard.select_floor') }}</option>
                                    @foreach ($floors as $floor)
                                        <option value="{{ $floor->id }}"
                                            {{ request('floor_id') == $floor->id ? 'selected' : '' }}>
                                            {{ $floor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <input type="text" name="unit_number" class="form-control"
                                    placeholder="{{ __('dashboard.enter_unit_no') }}."
                                    value="{{ request('unit_number') }}">
                            </div>

                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">{{ __('dashboard.select_status') }}</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>
                                        {{ __('dashboard.active') }}</option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>
                                        {{ __('dashboard.inactive') }}</option>
                                </select>
                            </div>

                            <div class="col-md-3 mt-3">
                                <select name="unit_class_id" class="form-select">
                                    <option value="">{{ __('dashboard.select_class') }}</option>
                                    @foreach ($unitClasses as $class)
                                        <option value="{{ $class->id }}"
                                            {{ request('unit_class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mt-3">
                                <select name="unit_type_id" class="form-select">
                                    <option value="">{{ __('dashboard.select_unit_type') }}</option>
                                    @foreach ($unitTypes as $type)
                                        <option value="{{ $type->id }}"
                                            {{ request('unit_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mt-3">
                                <button class="btn btn-primary w-100">
                                    {{ __('dashboard.search') }}
                                </button>
                            </div>

                            <div class="col-md-3 mt-3">
                                <a href="{{ route('setup-sidebar.unit.index') }}" class="btn btn-secondary w-100">
                                    {{ __('dashboard.reset') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>


        <div class="container mt-4">
            <div class="row">
                @forelse($units as $unit)
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                        <div class="card text-center h-100 shadow-sm position-relative overflow-hidden unit-card">

                            <div class="card-body d-flex flex-column justify-content-center">
                                <h3 class="card-title fw-bold mb-2">{{ $unit->unit_number }}</h3>

                                <p class="card-text text-muted mb-0">
                                    {{ $unit->unitType->name ?? '-' }}
                                </p>
                            </div>

                            <div class="card-footer">
                                @if ($unit->is_active)
                                    <span class="badge bg-success">{{ __('dashboard.active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('dashboard.inactive') }}</span>
                                @endif
                            </div>

                            <div class="card-overlay d-flex justify-content-center align-items-center">
                                @can('unit.edit')
                                    <a href="{{ route('setup-sidebar.unit.edit', $unit->id) }}"
                                        class="btn btn-sm btn-primary me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endcan
                                @can('unit.view')
                                    <a href="{{ route('setup-sidebar.unit.view', $unit->id) }}"
                                        class="btn btn-sm btn-info me-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endcan

                                @can('unit.delete')
                                    <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#deactivateUserModal{{ $unit->id }}"
                                        {{ __('dashboard.delete_unit_confirmation') }}>
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @endcan

                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-center text-muted">{{ __('dashboard.no_units_found') }}</p>
                    </div>
                @endforelse
            </div>
        </div>

    </main>
    @foreach ($units as $unit)
        <!-- Deactivate Modal -->
        <div class="modal fade" id="deactivateUserModal{{ $unit->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('dashboard.delete_unit') }} :{{ $unit->unit_number }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <p>{{ __('dashboard.delete_unit_confirmation') }}</p>
                        <hr>
                        <dl class="row mb-0">

                            <dt class="col-sm-5">{{ __('dashboard.unit_class') }}</dt>
                            <dd class="col-sm-7">{{ $unit->unitClass->name ?? '-' }}</dd>

                            <dt class="col-sm-5">{{ __('dashboard.status') }}</dt>
                            <dd class="col-sm-7">{{ $unit->is_active === true ? 'Active' : 'Inactive' }}</dd>

                            <dt class="col-sm-5">{{ __('dashboard.description') }}</dt>
                            <dd class="col-sm-7">{{ ucfirst($unit->description ?? '-') }}</dd>
                        </dl>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.cancel') }}
                        </button>

                        <form action="{{ route('setup-sidebar.unit.delete', $unit->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
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
