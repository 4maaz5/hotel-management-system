@extends('layouts.app')

@section('title', 'Floors Management')

<style>
    /* Contact Information */
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
        background-color: #28a745;
        color: white;
        border-color: #28a745;
    }

    .n-button--green:hover {
        background-color: #218838;
        border-color: #1e7e34;
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
    <main class="u-white-bg bg-white p-2" style="border-radius:10px;">

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Page Header -->
        <div class="page-category">{{ __('dashboard.company') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.floors') }}</h2>
                <div class="page-header__subtitle">{{ __('dashboard.view_and_manage_your_floors') }}</div>
            </div>
            <div class="n-table__top-btns">
                <button class="n-button n-button--primary">
                    {{ __('dashboard.filter') }}
                </button>
                <div>
                    @can('floor.add')
                        <a href="#" data-bs-toggle="modal" data-bs-target="#addFloorModal"
                            class="btn btn-primary" style="text-decoration:none;" tabindex="0">
                            {{ __('dashboard.new_floor') }}
                        </a>
                    @endcan

                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('setup-sidebar.floors.index') }}">
            <div class="filter-form__container mb-4">
                <div class="card">
                    <div class="card-body">

                        <div class="row g-3">

                            <div class="col-lg-3 col-md-4">
                                <label class="form-label">{{ __('dashboard.block_name') }}</label>
                                <select name="block_id" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    @foreach ($blocks as $b)
                                        <option value="{{ $b->id }}"
                                            {{ request('block_id') == $b->id ? 'selected' : '' }}>
                                            {{ $b->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-3 col-md-4">
                                <label class="form-label">{{ __('dashboard.floor_name') }}</label>
                                <input type="text" name="name" value="{{ request('name') }}" class="form-control"
                                    placeholder="{{ __('dashboard.enter_floor_name') }}">
                            </div>

                            <div class="col-lg-3 col-md-4">
                                <label class="form-label">{{ __('dashboard.order') }}</label>
                                <input type="number" name="order" value="{{ request('order') }}" class="form-control"
                                    placeholder="{{ __('dashboard.enter_order') }}">
                            </div>

                            <div class="col-lg-3 col-md-4">
                                <label class="form-label">{{ __('dashboard.description') }}</label>
                                <input type="text" name="description" value="{{ request('description') }}"
                                    class="form-control" placeholder="{{ __('dashboard.enter_description') }}">
                            </div>

                            <div class="col-lg-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    {{ __('dashboard.search') }}
                                </button>

                                <a href="{{ route('setup-sidebar.floors.index') }}" class="btn btn-outline-secondary">
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
                                    <th>{{ __('dashboard.block_name') }}</th>
                                    <th>{{ __('dashboard.order') }}</th>
                                    <th>{{ __('dashboard.status') }}</th>
                                    <th>{{ __('dashboard.floor') }}</th>
                                    <th>{{ __('dashboard.description') }}</th>
                                    <th>{{ __('dashboard.actions') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($floors as $index => $floor)
                                    <tr>
                                        <td>
                                            <strong>{{ $floor->block->name }}</strong>
                                        </td>

                                        <td>
                                            {{ $floor->order ?? '—' }}
                                        </td>

                                        <td>
                                            @if ($floor->is_active)
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
                                            <span class="badge bg-primary">
                                                {{ $floor->name }}
                                            </span>
                                        </td>

                                        <td>
                                            {{ $floor->description }}
                                        </td>

                                        <td class="text-end">
                                            <div class="d-flex align-items-center gap-1">
                                                @can('floor.edit')
                                                    <!-- Edit Button -->
                                                    <a href="#" data-bs-toggle="modal"
                                                        data-bs-target="#editFloorModal{{ $floor->id }}"
                                                        class="btn btn-sm btn-primary" title="{{ __('dashboard.edit') }}">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                @endcan

                                                @can('floor.view')
                                                    <!-- View Button -->
                                                    <a href="#" data-bs-toggle="modal"
                                                        data-bs-target="#viewFloorModal{{ $floor->id }}"
                                                        class="btn btn-sm btn-secondary" title="{{ __('dashboard.view') }}">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                @endcan


                                                <!-- Dropdown -->
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-secondary " type="button"
                                                        id="actionMenu{{ $floor->id }}" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu"
                                                        aria-labelledby="actionMenu{{ $floor->id }}">
                                                        <li>
                                                            @can('floor.delete')
                                                                <a href="#" class="dropdown-item"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#deactivateUserModal{{ $floor->id }}">
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
                                            {{ __('dashboard.no_floors_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{-- Pagination --}}
                        <div class="mt-3 d-flex justify-content-end">
                            {{ $floors->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Floor Modal -->
    <div class="modal fade" id="addFloorModal" tabindex="-1" aria-labelledby="addFloorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="addFloorModalLabel">{{ __('dashboard.add_floor') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Body -->
                <form method="POST" action="{{ route('setup-sidebar.floors.store') }}" id="addForm">
                    @csrf


                    <div class="modal-body">

                        @if ($block)
                            <input type="hidden" name="block_id" value="{{ $block->id }}">
                            <div class="mb-3">
                                <strong>{{ __('dashboard.block_name') }}:</strong> {{ $block->name }}
                            </div>
                        @endif

                        <!-- Floor Order -->
                        <div class="mb-3">
                            <label for="floor_order" class="form-label">{{ __('dashboard.order') }}</label>
                            <input type="number" class="form-control" id="floor_order" name="order"
                                placeholder="{{ __('dashboard.enter_order') }}" value="{{ old('order') }}">
                            @error('order')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Floor Name -->
                        <div class="mb-3">
                            <label for="floor_name" class="form-label">{{ __('dashboard.floor_name') }}</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="{{ __('dashboard.enter_floor_name') }}" value="{{ old('name') }}">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="floor_description" class="form-label">{{ __('dashboard.description') }}</label>
                            <textarea class="form-control" id="floor_description" name="description" rows="3"
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

    @foreach ($floors as $floor)
        <div class="modal fade" id="editFloorModal{{ $floor->id }}" tabindex="-1"
            aria-labelledby="editFloorModalLabel{{ $floor->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="editFloorModalLabel{{ $floor->id }}">
                            {{ __('dashboard.edit_floor') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>



                    <!-- Body -->
                    <form method="POST" action="{{ route('setup-sidebar.floors.update', $floor->id) }}" id="editForm">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="edit_floor_id" value="{{ $floor->id }}">

                        <div class="modal-body">
                            @if ($block)
                                <div class="mb-3">
                                    <strong>{{ __('dashboard.block_name') }}:</strong> {{ $block->name }}
                                </div>
                            @endif
                            <!-- Status Toggle -->
                            <div class="mb-3">
                                <label class="form-label d-block">{{ __('dashboard.status') }}</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="edit_status_{{ $floor->id }}"
                                        name="is_active" value="1" {{ $floor->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="edit_status_{{ $floor->id }}">
                                        {{ $floor->is_active ? __('dashboard.active') : __('dashboard.inactive') }}
                                    </label>
                                </div>
                            </div>

                            <!-- Order -->
                            <div class="mb-3">
                                <label for="edit_order_{{ $floor->id }}"
                                    class="form-label">{{ __('dashboard.order') }}</label>
                                <input type="number" class="form-control" id="edit_order_{{ $floor->id }}"
                                    name="order" value="{{ old('order', $floor->order) }}">
                            </div>

                            <!-- Floor Name -->
                            <div class="mb-3">
                                <label for="edit_name_{{ $floor->id }}"
                                    class="form-label">{{ __('dashboard.name') }}</label>
                                <input type="text" class="form-control" id="edit_name" name="name"
                                    value="{{ old('name', $floor->name) }}">
                                @error('name')
                                    <span class="text text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label for="edit_description_{{ $floor->id }}"
                                    class="form-label">{{ __('dashboard.description') }}</label>
                                <textarea class="form-control" id="edit_description_{{ $floor->id }}" name="description" rows="3">{{ old('description', $floor->description) }}</textarea>
                            </div>

                        </div>

                        <!-- Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('dashboard.update') }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="viewFloorModal{{ $floor->id }}" tabindex="-1"
            aria-labelledby="editFloorModalLabel{{ $floor->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="editFloorModalLabel{{ $floor->id }}">
                            {{ __('dashboard.view_floor') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>



                    <!-- Body -->
                    <form method="POST" action="#">
                        @csrf
                        @method('PUT')


                        <div class="modal-body">
                            @if ($block)
                                <div class="mb-3">
                                    <strong>{{ __('dashboard.block_name') }}:</strong> {{ $block->name }}
                                </div>
                            @endif
                            <!-- Status Toggle -->
                            <div class="mb-3">
                                <label class="form-label d-block">{{ __('dashboard.status') }}</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="edit_status_{{ $floor->id }}"
                                        name="is_active" value="1" {{ $floor->is_active ? 'checked' : '' }}
                                        disabled>
                                    <label class="form-check-label" for="edit_status_{{ $floor->id }}">
                                        {{ $floor->is_active ? __('dashboard.active') : __('dashboard.inactive') }}
                                    </label>
                                </div>
                            </div>

                            <!-- Order -->
                            <div class="mb-3">
                                <label for="edit_order_{{ $floor->id }}"
                                    class="form-label">{{ __('dashboard.order') }}</label>
                                <input type="number" class="form-control" id="edit_order_{{ $floor->id }}"
                                    name="order" value="{{ old('order', $floor->order) }}" disabled>
                            </div>

                            <!-- Floor Name -->
                            <div class="mb-3">
                                <label for="edit_name_{{ $floor->id }}"
                                    class="form-label">{{ __('dashboard.name') }}</label>
                                <input type="text" class="form-control" id="edit_name_{{ $floor->id }}"
                                    name="name" value="{{ old('name', $floor->name) }}" disabled>
                                @error('name')
                                    <span class="text text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label for="edit_description_{{ $floor->id }}"
                                    class="form-label">{{ __('dashboard.description') }}</label>
                                <textarea class="form-control" id="edit_description_{{ $floor->id }}" name="description" rows="3"
                                    disabled>{{ old('description', $floor->description) }}</textarea>
                            </div>

                        </div>

                        <!-- Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deactivateUserModal{{ $floor->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('dashboard.delete_floor') }} : {{ $floor->name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <p>{{ __('dashboard.delete_floor_confirmation') }}</p>
                        <hr>

                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.cancel') }}
                        </button>

                        <form action="{{ route('setup-sidebar.floors.delete', $floor->id) }}" method="POST">
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
        const toggleBtn = document.querySelector('.n-button--primary');
        const filterContainer = document.querySelector('.filter-form__container');

        filterContainer.style.display = 'none';

        // Toggle on click
        toggleBtn.addEventListener('click', function() {
            if (filterContainer.style.display === 'none') {
                filterContainer.style.display = 'block';
            } else {
                filterContainer.style.display = 'none';
            }
        });

        $(document).ready(function() {
            $('#addForm').on('submit', function(e) {
                e.preventDefault();

                $('.text-danger').remove();
                $('.is-invalid').removeClass('is-invalid');

                let isValid = true;

                // Validate name
                const name = $('#name').val().trim();
                if (!name) {
                    $('#name').addClass('is-invalid');
                    $('#name').parent().append('<span class="text-danger">Name is required</span>');
                    isValid = false;
                }

                if (!isValid) {
                    return false;
                }

                this.submit();
            });
        });

        $(document).ready(function() {
            // Handle add form submission
            $('#addForm').on('submit', function(e) {
                e.preventDefault();

                $('.text-danger').remove();
                $('.is-invalid').removeClass('is-invalid');

                let isValid = true;

                // Validate name
                const name = $('#name').val().trim();
                if (!name) {
                    $('#name').addClass('is-invalid');
                    $('#name').parent().append('<span class="text-danger">Name is required</span>');
                    isValid = false;
                }

                if (!isValid) {
                    return false;
                }

                this.submit();
            });

            $('form[id^="editForm"]').on('submit', function(e) {
                e.preventDefault();

                $(this).find('.text-danger').remove();
                $(this).find('.is-invalid').removeClass('is-invalid');

                let isValid = true;

                const nameInput = $(this).find('input[name="name"]');
                const name = nameInput.val().trim();

                if (!name) {
                    nameInput.addClass('is-invalid');
                    nameInput.parent().append('<span class="text-danger">Name is required</span>');
                    isValid = false;
                }

                if (!isValid) {
                    return false;
                }

                this.submit();
            });
        });
    </script>
@endpush
