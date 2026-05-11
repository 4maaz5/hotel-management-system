@extends('layouts.app')

@section('title', 'Unit Amenities')

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

    /* Kendo Grid */
    .k-grid {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .k-grid-header {
        background-color: #f8f9fa;
        padding: 0 6px 0 0;
    }

    .k-grid-header-wrap {
        overflow-x: auto;
    }

    .k-header {
        background-color: #f8f9fa;
        padding: 1rem 1.25rem;
        text-align: left;
        font-weight: 600;
        color: #495057;
        border-bottom: 1px solid #dee2e6;
        font-size: 0.875rem;
    }

    .k-link {
        color: inherit;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .k-grid-content {
        min-height: 300px;
    }

    .k-grid-table {
        width: 100%;
        border-collapse: collapse;
    }

    .k-grid-table td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #dee2e6;
        font-size: 0.875rem;
    }

    .k-alt {
        background-color: #f8f9fa;
    }

    /* Column Widths */
    .k-grid col:nth-child(1) {
        width: 300px;
    }

    .k-grid col:nth-child(2) {
        width: 100px;
    }

    .k-grid col:nth-child(3) {
        width: 100px;
    }

    .k-grid col:nth-child(5) {
        width: 160px;
    }

    /* Status Tags */
    .n-table__tag {
        padding: 0.25rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 500;
        display: inline-block;
    }

    .n-table__tag--green {
        background-color: #d4edda;
        color: #155724;
    }

    /* Table Actions */
    .n-table-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .n-table-action {
        width: 32px;
        height: 32px;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: white;
        border: 1px solid #dee2e6;
        color: #333;
        cursor: pointer;
        padding: 0;
    }

    .n-table-action:hover {
        background-color: #4a90e2;
        color: white;
        border-color: #4a90e2;
    }

    .n-table__icon {
        width: 32px;
        height: 32px;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: white;
        border: 1px solid #dee2e6;
        color: #333;
        cursor: pointer;
    }

    .n-table__icon:hover {
        background-color: #f8f9fa;
    }

    /* Kendo Combobox */
    .k-combobox {
        width: 100%;
    }

    .k-dropdown-wrap {
        background-color: #495057;
        border: 1px solid #6c757d;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
    }

    .k-input {
        background: transparent;
        border: none;
        color: white;
        padding: 0.5rem 0.75rem;
        width: 100%;
        font-size: 0.875rem;
    }

    .k-input::placeholder {
        color: #adb5bd;
    }

    .k-select {
        padding: 0 0.5rem;
        border-left: 1px solid #6c757d;
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .k-icon {
        font-size: 0.875rem;
        color: #adb5bd;
    }

    .k-clear-value {
        padding-right: 0.25rem;
        cursor: pointer;
    }

    /* Button */
    .button--raised {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .button--primary {
        background-color: #4a90e2;
        color: white;
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 0.375rem;
        font-weight: 500;
        cursor: pointer;
        font-size: 0.875rem;
    }

    .button--primary:hover {
        background-color: #3a80d2;
    }

    /* Pagination */
    .k-pager-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        background: white;
        border-top: 1px solid #dee2e6;
        border-radius: 0 0 0.5rem 0.5rem;
    }

    .n-pager__sizes {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
    }

    .k-pager-sizes select {
        padding: 0.25rem 0.5rem;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        font-size: 0.875rem;
    }

    .n-pager__info {
        color: #6c757d;
        font-size: 0.875rem;
    }

    .k-pager-nav {
        width: 32px;
        height: 32px;
        border-radius: 0.375rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #dee2e6;
        color: #333;
        text-decoration: none;
        margin: 0 0.125rem;
        font-size: 0.75rem;
    }

    .k-pager-nav.k-state-disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .k-pager-nav:hover:not(.k-state-disabled) {
        background-color: #f8f9fa;
    }

    .k-pager-nav .k-icon {
        font-size: 0.75rem;
    }

    /* Alignment Classes */
    .u-flex-end {
        display: flex;
        justify-content: flex-end;
    }

    .u-align-center {
        text-align: center;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .n-table__top-btns {
            width: 100%;
            justify-content: flex-end;
        }

        .filter-form__container {
            overflow-x: auto;
        }

        .k-pager-wrap {
            flex-wrap: wrap;
            gap: 1rem;
        }
    }
</style>

@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">

        <!-- Page Header -->
        <div class="page-category">{{ __('dashboard.company') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.amenities') }}</h2>
                <div class="page-header__subtitle">{{ __('dashboard.view_and_manage_unit_amenities') }}</div>
            </div>
            <div class="n-table__top-btns">
                <button class="n-button n-button--primary">
                    {{ __('dashboard.filter') }}
                </button>
                <div>
                    @can('amenity.add')
                        <a href="#" data-bs-toggle="modal" data-bs-target="#addAmenityModal" class="n-button n-button--green"
                            style="text-decoration:none;" tabindex="0">
                            {{ __('dashboard.add_amenity') }}
                        </a>
                    @endcan

                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('setup-sidebar.amenity.index') }}">
            <div class="filter-form__container mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">

                            <!-- Name Filter -->
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">{{ __('dashboard.name') }}</label>
                                <input type="text" name="name" value="{{ request('name') }}" class="form-control"
                                    placeholder="{{ __('dashboard.enter_name') }}">
                            </div>

                            <!-- Description Filter -->
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">{{ __('dashboard.description') }}</label>
                                <input type="text" name="description" value="{{ request('description') }}"
                                    class="form-control" placeholder="{{ __('dashboard.enter_description') }}">
                            </div>

                            <!-- Status Filter (optional) -->
                            <div class="col-lg-2 col-md-4">
                                <label class="form-label">{{ __('dashboard.status') }}</label>
                                <select name="is_active" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>
                                        {{ __('dashboard.active') }}
                                    </option>
                                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>
                                        {{ __('dashboard.inactive') }}
                                    </option>
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div class="col-lg-2 col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">{{ __('dashboard.search') }}</button>
                                <a href="{{ route('setup-sidebar.amenity.index') }}" class="btn btn-outline-secondary">
                                    {{ __('dashboard.reset') }}
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>


        <!-- Kendo Grid -->
        <div class="k-widget k-grid">
            <div role="grid" class="k-grid-aria-root" aria-rowcount="6" aria-colcount="5">
                <!-- Grid Header -->
                <div role="presentation" class="k-grid-header" style="padding: 0px 6px 0px 0px;">
                    <div role="presentation" data-scrollable="" class="k-grid-content">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>{{ __('dashboard.name') }}</th>
                                    <th>{{ __('dashboard.status') }}</th>
                                    <th>{{ __('dashboard.description') }}</th>
                                    <th>{{ __('dashboard.actions') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($amenities as $index => $amenity)
                                    <tr>
                                        <td>
                                            <strong>{{ $amenity->name }}</strong>
                                        </td>

                                        <td>
                                            @if ($amenity->is_active)
                                                <span class="badge bg-success">
                                                    {{ __('dashboard.active') }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    {{ __('dashboard.inactive') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $amenity->description }}
                                        </td>

                                        <td class="text-end">
                                            <div class="d-flex align-items-center gap-1">
                                                @can('amenity.edit')
                                                    <!-- Edit Button -->
                                                    <a href="#" data-bs-toggle="modal"
                                                        data-bs-target="#editAmenityModal{{ $amenity->id }}"
                                                        class="btn btn-sm btn-primary" title="{{ __('dashboard.edit') }}">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                @endcan

                                                @can('amenity.view')
                                                    <!-- View Button -->
                                                    <a href="#" data-bs-toggle="modal"
                                                        data-bs-target="#viewAmenityModal{{ $amenity->id }}"
                                                        class="btn btn-sm btn-secondary" title="{{ __('dashboard.view') }}">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                @endcan

                                                <!-- Dropdown -->
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-secondary " type="button"
                                                        id="actionMenu{{ $amenity->id }}" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu"
                                                        aria-labelledby="actionMenu{{ $amenity->id }}">

                                                        <li>
                                                            <a href="#" class="dropdown-item" data-bs-toggle="modal"
                                                                data-bs-target="#copyModal{{ $amenity->id }}">
                                                                <i class="bi bi-copy text-danger"></i>
                                                                {{ __('dashboard.copy') }}
                                                            </a>
                                                        </li>
                                                        <li>
                                                            @can('amenity.delete')
                                                                <a href="#" class="dropdown-item" data-bs-toggle="modal"
                                                                    data-bs-target="#deactivateUserModal{{ $amenity->id }}">
                                                                    <i class="bi bi-trash text-danger"></i>
                                                                    {{ __('dashboard.delete') }}
                                                                </a>
                                                            @endcan
                                                        </li>
                                                    </ul>
                                                </div>

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            {{ __('dashboard.no_records_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{-- Pagination --}}
                        <div class="mt-3 d-flex justify-content-end">
                            {{ $amenities->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Amenity Modal -->
    <div class="modal fade" id="addAmenityModal" tabindex="-1" aria-labelledby="addAmenityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="addAmenityModalLabel">{{ __('dashboard.new_amenity') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Body -->
                <form method="POST" action="{{ route('setup-sidebar.amenity.store') }}" id="addAmenityForm">
                    @csrf

                    <div class="modal-body">

                        <!-- Amenity Name (Select) -->
                        <div class="mb-3">
                            <label for="amenity_name" class="form-label">{{ __('dashboard.name') }}</label>
                            <select name="name" id="amenity_name" class="form-select amenity-name">
                                <option value="">{{ __('dashboard.select_amenity') }}</option>
                                @php
                                    $amenityOptions = [
                                        __('dashboard.air_conditioning'),
                                        __('dashboard.balcony'),
                                        __('dashboard.view'),
                                        __('dashboard.terrace'),
                                        __('dashboard.washing_machine'),
                                        __('dashboard.cribs'),
                                        __('dashboard.clothes_rack'),
                                        __('dashboard.gym'),
                                        __('dashboard.parking'),
                                        __('dashboard.infinity_pool'),
                                        __('dashboard.pool_cover'),
                                        __('dashboard.hot_tub'),
                                        __('dashboard.iron'),
                                        __('dashboard.tv'),
                                        __('dashboard.mini_bar'),
                                        __('dashboard.elevator'),
                                    ];

                                @endphp
                                @foreach ($amenityOptions as $option)
                                    <option value="{{ $option }}" {{ old('name') == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="amenity_description" class="form-label">{{ __('dashboard.description') }}</label>
                            <textarea class="form-control" id="amenity_description" name="description" rows="3"
                                placeholder="{{ __('dashboard.enter_description') }}">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <!-- Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">{{ __('dashboard.save') }}</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    @foreach ($amenities as $amenity)
        <!-- Edit Amenity Modal -->
        <div class="modal fade" id="editAmenityModal{{ $amenity->id }}" tabindex="-1"
            aria-labelledby="editAmenityModalLabel{{ $amenity->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAmenityModalLabel{{ $amenity->id }}">
                            {{ __('dashboard.edit_amenity') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Body -->
                    <form method="POST" action="{{ route('setup-sidebar.amenity.update', $amenity->id) }}"
                        id="editForm{{ $amenity->id }}">
                        @csrf
                        @method('PUT')

                        <div class="modal-body">

                            <!-- Status Toggle -->
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="amenityStatus{{ $amenity->id }}"
                                    name="is_active" value="1" {{ $amenity->is_active ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="amenityStatus{{ $amenity->id }}">{{ __('dashboard.status') }}</label>
                                @error('is_active')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Amenity Name (Select) -->
                            <div class="mb-3">
                                <label for="amenity_name{{ $amenity->id }}"
                                    class="form-label">{{ __('dashboard.name') }}</label>
                                <select name="name" id="amenity_name{{ $amenity->id }}"
                                    class="form-select amenity-name">
                                    <option value="">{{ __('dashboard.select_amenity') }}</option>
                                    @php
                                        $amenityOptions = [
                                            __('dashboard.air_conditioning'),
                                            __('dashboard.balcony'),
                                            __('dashboard.view'),
                                            __('dashboard.terrace'),
                                            __('dashboard.washing_machine'),
                                            __('dashboard.cribs'),
                                            __('dashboard.clothes_rack'),
                                            __('dashboard.gym'),
                                            __('dashboard.parking'),
                                            __('dashboard.infinity_pool'),
                                            __('dashboard.pool_cover'),
                                            __('dashboard.hot_tub'),
                                            __('dashboard.iron'),
                                            __('dashboard.tv'),
                                            __('dashboard.mini_bar'),
                                            __('dashboard.elevator'),
                                        ];
                                    @endphp
                                    @foreach ($amenityOptions as $option)
                                        <option value="{{ $option }}"
                                            {{ $amenity->name == $option ? 'selected' : '' }}>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label for="amenity_description{{ $amenity->id }}"
                                    class="form-label">{{ __('dashboard.description') }}</label>
                                <textarea class="form-control" id="amenity_description{{ $amenity->id }}" name="description" rows="3"
                                    placeholder="{{ __('dashboard.enter_description') }}">{{ $amenity->description }}</textarea>
                                @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <!-- Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                {{ __('dashboard.cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary">{{ __('dashboard.save') }}</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        <!-- view Amenity Modal -->
        <div class="modal fade" id="viewAmenityModal{{ $amenity->id }}" tabindex="-1"
            aria-labelledby="editAmenityModalLabel{{ $amenity->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAmenityModalLabel{{ $amenity->id }}">
                            {{ __('dashboard.view_amenity') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Body -->
                    <form method="POST" action="#" id="editAmenityForm{{ $amenity->id }}">
                        @csrf
                        @method('PUT')

                        <div class="modal-body">

                            <!-- Status Toggle -->
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="amenityStatus{{ $amenity->id }}"
                                    name="is_active" value="1" {{ $amenity->is_active ? 'checked' : '' }} disabled>
                                <label class="form-check-label"
                                    for="amenityStatus{{ $amenity->id }}">{{ __('dashboard.status') }}</label>
                                @error('is_active')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Amenity Name (Select) -->
                            <div class="mb-3">
                                <label for="amenity_name{{ $amenity->id }}"
                                    class="form-label">{{ __('dashboard.name') }}</label>
                                <select name="name" id="amenity_name{{ $amenity->id }}" class="form-select" disabled
                                    required>
                                    <option value="">{{ __('dashboard.select_amenity') }}</option>
                                    @php
                                        $amenityOptions = [
                                            __('dashboard.air_conditioning'),
                                            __('dashboard.balcony'),
                                            __('dashboard.view'),
                                            __('dashboard.terrace'),
                                            __('dashboard.washing_machine'),
                                            __('dashboard.cribs'),
                                            __('dashboard.clothes_rack'),
                                            __('dashboard.gym'),
                                            __('dashboard.parking'),
                                            __('dashboard.infinity_pool'),
                                            __('dashboard.pool_cover'),
                                            __('dashboard.hot_tub'),
                                            __('dashboard.iron'),
                                            __('dashboard.tv'),
                                            __('dashboard.mini_bar'),
                                            __('dashboard.elevator'),
                                        ];
                                    @endphp
                                    @foreach ($amenityOptions as $option)
                                        <option value="{{ $option }}"
                                            {{ $amenity->name == $option ? 'selected' : '' }}>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label for="amenity_description{{ $amenity->id }}"
                                    class="form-label">{{ __('dashboard.description') }}</label>
                                <textarea class="form-control" id="amenity_description{{ $amenity->id }}" name="description" rows="3"
                                    placeholder="{{ __('dashboard.enter_description') }}" disabled>{{ $amenity->description }}</textarea>
                                @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <!-- Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                {{ __('dashboard.cancel') }}
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        <div class="modal fade" id="deactivateUserModal{{ $amenity->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('dashboard.delete_amenity') }} : {{ $amenity->name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <p>{{ __('dashboard.delete_amenity_confirmation') }}</p>
                        <hr>

                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.cancel') }}
                        </button>

                        <form action="{{ route('setup-sidebar.amenity.delete', $amenity->id) }}" method="POST">
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

        <div class="modal fade" id="copyModal{{ $amenity->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('dashboard.apply_amenity') }} : {{ $amenity->name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <p>{{ __('dashboard.you_are_going_to_apply_this_amenity_to_all_active_units_belonged_to_this_property_Units_that_already_had_this_amenity_will_not_be_affected') }}
                        </p>

                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.cancel') }}
                        </button>

                        <form action="{{ route('setup-sidebar.amenity.copy', $amenity->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-primary">
                                {{ __('dashboard.apply') }}
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
        const toggleBtn = document.querySelector('.n-button--primary');
        const filterContainer = document.querySelector('.filter-form__container');

        filterContainer.style.display = 'none';

        toggleBtn.addEventListener('click', function() {
            if (filterContainer.style.display === 'none') {
                filterContainer.style.display = 'block';
            } else {
                filterContainer.style.display = 'none';
            }
        });

        $(document).ready(function() {

            function validateAmenityForm(form) {
                let isValid = true;

                $(form).find('.text-danger').remove();
                $(form).find('.is-invalid').removeClass('is-invalid');

                const nameField = $(form).find('.amenity-name');
                if (!nameField.val() || nameField.val() === '') {
                    nameField.addClass('is-invalid');
                    $('<div class="text-danger">Amenity name is required</div>').insertAfter(nameField);
                    isValid = false;
                }

                return isValid;
            }

            $('#addAmenityForm').on('submit', function(e) {
                if (!validateAmenityForm(this)) {
                    e.preventDefault();
                }
            });

            $('form[id^="editForm"]').on('submit', function(e) {
                if (!validateAmenityForm(this)) {
                    e.preventDefault();
                }
            });

            $('#addAmenityModal, div[id^="editAmenityModal"]').on('shown.bs.modal', function() {
                $(this).find('.text-danger').remove();
                $(this).find('.is-invalid').removeClass('is-invalid');
            });

        });
    </script>
@endpush
