@extends('layouts.app')

@section('title', 'Outlet Categories')
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
                <h2 class="page-header__title">{{ __('dashboard.item_categories') }}</h2>
            </div>
            <div class="n-table__top-btns">
                <button class="n-button n-button--primary" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    {{ __('dashboard.filter') }}
                </button>
                <div>
                    @can('item_categories.add')
                        <a href="{{ route('setup-sidebar.unit.create') }}" class="n-button n-button--green" data-bs-toggle="modal"
                            data-bs-target="#addItemCategoryModal" style="text-decoration:none;" tabindex="0">
                            {{ __('dashboard.new_category') }}
                        </a>
                    @endcan

                </div>
            </div>
        </div>

        <div class="collapse mb-3 bg-white p-3" id="filterCollapse">
            <form method="GET" action="{{ route('setup-sidebar.item_category.index') }}">

                <div class="row g-3 align-items-end">

                    <!-- Status -->
                    <div class="col-xl-2 col-md-4">
                        <label>{{ __('dashboard.status') }}</label>

                        <select name="status" class="form-select">
                            <option value="">{{ __('dashboard.select_status') }}</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>
                                {{ __('dashboard.active') }}
                            </option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>
                                {{ __('dashboard.inactive') }}
                            </option>
                        </select>
                    </div>

                    <!-- Outlet -->
                    <div class="col-xl-3 col-md-4">
                        <label>{{ __('dashboard.outlet') }}</label>

                        <select name="outlet" class="form-select">
                            <option value="">{{ __('dashboard.select_outlet') }}</option>

                            @foreach ($outlets as $outlet)
                                <option value="{{ $outlet->id }}"
                                    {{ request('outlet') == $outlet->id ? 'selected' : '' }}>
                                    {{ $outlet->name_en ?? $outlet->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <!-- Name -->
                    <div class="col-xl-3 col-md-4">
                        <label>{{ __('dashboard.name') }}</label>

                        <input type="text" name="name" value="{{ request('name') }}" class="form-control"
                            placeholder="{{ __('dashboard.name') }}">
                    </div>

                    <!-- NTMP Category -->
                    <div class="col-xl-3 col-md-4">
                        <label>NTMP Category</label>

                        <select name="ntmp" class="form-select">
                            <option value="">Select Category</option>

                            @php
                                $ntmpCategories = [
                                    'other',
                                    'laundry',
                                    'wifi_internet',
                                    'car_parking',
                                    'food',
                                    'food_beverages',
                                    'beverages',
                                    'breakfast',
                                    'lunch',
                                    'dinner',
                                    'swimming_pool',
                                    'gym',
                                    'minibar',
                                ];
                            @endphp

                            @foreach ($ntmpCategories as $ntmp)
                                <option value="{{ $ntmp }}" {{ request('ntmp') == $ntmp ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $ntmp)) }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <!-- Search Button -->
                    <div class="col-xl-1 col-md-8 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>

                </div>

            </form>
        </div>
        <div class="container mt-5">
            <table class="table table-bordered table-striped align-middle text-center bg-white">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('dashboard.status') }}</th>
                        <th>{{ __('dashboard.name') }}</th>
                        <th>NTMP {{ __('dashboard.category') }}</th>
                        <th>{{ __('dashboard.outlet') }}</th>
                        <th>{{ __('dashboard.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>

                            <!-- Status -->
                            <td>
                                @if ($category->status)
                                    <span class="badge bg-success">
                                        ✓ {{ __('dashboard.active') }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        {{ __('dashboard.inactive') }}
                                    </span>
                                @endif
                            </td>

                            <!-- Name -->
                            <td>
                                {{ $category->name }}
                            </td>

                            <!-- NTMP Category -->
                            <td>
                                {{ ucfirst(str_replace('_', ' ', $category->ntmp_category)) }}
                            </td>

                            <!-- Outlet -->
                            <td>
                                {{ $category->outlet->name ?? 'N/A' }}
                            </td>

                            <!-- Actions -->
                            <td>
                                @can('item_categories.edit')
                                    <!-- Edit -->
                                    <button class="btn btn-sm btn-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#editCategoryModal{{ $category->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                @endcan

                                @can('item_categories.delete')
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteCategoryModal{{ $category->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted">
                                {{ __('dashboard.no_data_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

    <!-- Add Item Category Modal -->
    <div class="modal fade" id="addItemCategoryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <form method="POST" action="{{ route('setup-sidebar.item_category.store') }}">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('dashboard.add_item_category') }}
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <!-- Outlet -->
                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('dashboard.outlet') }} *
                            </label>

                            <select name="outlet_id" class="form-select" required>
                                <option value="">
                                    {{ __('dashboard.select_outlet') }}
                                </option>

                                @foreach ($outlets as $outlet)
                                    <option value="{{ $outlet->id }}">
                                        {{ $outlet->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <!-- Name -->
                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('dashboard.category_name') }} *
                            </label>

                            <input type="text" name="name" class="form-control" required
                                placeholder="{{ __('dashboard.enter_category_name') }}">
                        </div>

                        <!-- NTMP Category -->
                        <div class="mb-3">
                            <label class="form-label">
                                NTMP {{ __('dashboard.category') }} *
                            </label>

                            <select name="ntmp_category" class="form-select" required>
                                <option value="">
                                    {{ __('dashboard.select_ntmp_category') }}
                                </option>

                                @php
                                    $ntmpCategories = [
                                        'other',
                                        'laundry',
                                        'wifi_internet',
                                        'car_parking',
                                        'food',
                                        'food_beverages',
                                        'beverages',
                                        'breakfast',
                                        'lunch',
                                        'dinner',
                                        'swimming_pool',
                                        'gym',
                                        'minibar',
                                        'spa',
                                        'conference_hall',
                                        'room_service',
                                        'transportation',
                                        'security',
                                    ];
                                @endphp

                                @foreach ($ntmpCategories as $ntmp)
                                    <option value="{{ $ntmp }}">
                                        {{ ucfirst(str_replace('_', ' ', $ntmp)) }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('dashboard.description') }}
                            </label>

                            <textarea name="description" class="form-control" rows="3" placeholder="{{ __('dashboard.enter_description') }}"></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="submit" class="btn btn-success">
                            {{ __('dashboard.save_category') }}
                        </button>

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.cancel') }}
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>

    @foreach ($categories as $category)
        <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <form method="POST" action="{{ route('setup-sidebar.item_category.update', $category->id) }}">

                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ __('dashboard.edit_category') }}
                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <!-- Status -->
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="status" value="1"
                                    {{ $category->status ? 'checked' : '' }}>

                                <label class="form-check-label">
                                    {{ __('dashboard.status') }}
                                </label>
                            </div>

                            <!-- Outlet -->
                            <div class="mb-3">
                                <label class="form-label">
                                    {{ __('dashboard.outlet') }} *
                                </label>

                                <select name="outlet_id" class="form-select" required>

                                    @foreach ($outlets as $outlet)
                                        <option value="{{ $outlet->id }}"
                                            {{ $category->outlet_id == $outlet->id ? 'selected' : '' }}>

                                            {{ $outlet->name }}

                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <!-- Category Name -->
                            <div class="mb-3">
                                <label class="form-label">
                                    {{ __('dashboard.category_name') }} *
                                </label>

                                <input type="text" name="name" class="form-control" value="{{ $category->name }}"
                                    required placeholder="{{ __('dashboard.enter_category_name') }}">
                            </div>

                            <!-- NTMP Category -->
                            <div class="mb-3">
                                <label class="form-label">
                                    NTMP {{ __('dashboard.category') }} *
                                </label>

                                <select name="ntmp_category" class="form-select" required>

                                    @php
                                        $ntmpCategories = [
                                            'other',
                                            'laundry',
                                            'wifi_internet',
                                            'car_parking',
                                            'food',
                                            'food_beverages',
                                            'beverages',
                                            'breakfast',
                                            'lunch',
                                            'dinner',
                                            'swimming_pool',
                                            'gym',
                                            'minibar',
                                            'spa',
                                            'conference_hall',
                                            'room_service',
                                            'transportation',
                                            'security',
                                        ];
                                    @endphp

                                    @foreach ($ntmpCategories as $ntmp)
                                        <option value="{{ $ntmp }}"
                                            {{ $category->ntmp_category == $ntmp ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $ntmp)) }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label class="form-label">
                                    {{ __('dashboard.description') }}
                                </label>

                                <textarea name="description" class="form-control" rows="3"
                                    placeholder="{{ __('dashboard.enter_description') }}">{{ $category->description }}
                        </textarea>
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

        <div class="modal fade" id="deleteCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('dashboard.delete_category') }} – {{ $category->name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p>{{ __('dashboard.delete_category_confirmation') }}</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.cancel') }}
                        </button>
                        <form action="{{ route('setup-sidebar.item_category.delete', $category->id) }}" method="POST">
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
