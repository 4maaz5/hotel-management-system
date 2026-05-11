@extends('layouts.app')

@section('title', 'Cost Center')
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
                <h2 class="page-header__title">{{ __('dashboard.cost_center') }}</h2>
            </div>
            <div class="n-table__top-btns">
                <button class="n-button n-button--primary">
                    {{ __('dashboard.filter') }}
                </button>
                <div>
                    @can('cost_center.add')
                        <a href="#" class="n-button n-button--green" data-bs-toggle="modal"
                            data-bs-target="#addCostCenterModal" style="text-decoration:none;" tabindex="0">
                            {{ __('dashboard.cost_center') }}
                        </a>
                    @endcan

                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('setup-sidebar.cost_center.index') }}">
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

                            <!-- Category Filter -->
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">{{ __('dashboard.category') }}</label>
                                <select name="category_id" class="form-select">
                                    <option value="">{{ __('dashboard.all_categories') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status Filter -->
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
                                <a href="{{ route('setup-sidebar.cost_center.index') }}" class="btn btn-outline-secondary">
                                    {{ __('dashboard.reset') }}
                                </a>
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
                        <th>#</th>
                        <th>{{ __('dashboard.name') }}</th>
                        <th>{{ __('dashboard.status') }}</th>
                        <th>{{ __('dashboard.category') }}</th>
                        <th>{{ __('dashboard.description') }}</th>
                        <th>{{ __('dashboard.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($costCenters as $index => $costCenter)
                        <tr>
                            <td>{{ $index + 1 }}</td>

                            {{-- Name --}}
                            <td>{{ $costCenter->name }}</td>

                            {{-- Status --}}
                            <td>
                                @if ($costCenter->is_active)
                                    <span class="badge bg-success">✔ {{ __('dashboard.active') }}</span>
                                @else
                                    <span class="badge bg-danger">✖ {{ __('dashboard.inactive') }}</span>
                                @endif
                            </td>

                            {{-- Category Name --}}
                            <td>{{ $costCenter->category->name ?? '-' }}</td>

                            {{-- Description --}}
                            <td>{{ $costCenter->description ?? '-' }}</td>

                            {{-- Actions --}}
                            <td>
                                {{-- Edit --}}
                                @can('cost_center.edit')
                                    <a href="#" class="btn btn-sm btn-warning me-1" data-bs-toggle="modal"
                                        data-bs-target="#editCostCenterModal-{{ $costCenter->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                @endcan

                                {{-- Delete --}}
                                @can('cost_center.delete')
                                    <a href="#" class="btn btn-sm btn-danger me-1" data-bs-toggle="modal"
                                        data-bs-target="#deleteCostCenterModal-{{ $costCenter->id }}">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                @endcan

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">{{ __('dashboard.no_cost_centers_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </main>

    <!-- Add Cost Center Modal -->
    <div class="modal fade" id="addCostCenterModal" tabindex="-1" aria-labelledby="addCostCenterLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="addCostCenterLabel">{{ __('dashboard.add_cost_center') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('setup-sidebar.cost_center.store') }}" method="POST">
                    @csrf

                    <div class="modal-body">

                        {{-- Name --}}
                        <div class="mb-3">
                            <label class="form-label">{{ __('dashboard.name') }} *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Category --}}
                        <div class="mb-3">
                            <label class="form-label">{{ __('dashboard.category') }} *</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">{{ __('dashboard.select_category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="mb-3">
                            <label class="form-label">{{ __('dashboard.description') }}</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('dashboard.save') }}</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    @foreach ($costCenters as $costCenter)
        <!-- Edit Cost Center Modal -->
        <div class="modal fade" id="editCostCenterModal-{{ $costCenter->id }}" tabindex="-1"
            aria-labelledby="editCostCenterLabel-{{ $costCenter->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="editCostCenterLabel-{{ $costCenter->id }}">
                            {{ __('dashboard.edit_cost_center') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('setup-sidebar.cost_center.update', $costCenter->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-body">

                            {{-- Name --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.name') }} *</label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $costCenter->name) }}" required>
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Category --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.category') }} *</label>
                                <select name="category_id" class="form-control" required>
                                    <option value="">{{ __('dashboard.select_category') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $costCenter->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.description') }}</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $costCenter->description) }}</textarea>
                            </div>

                            {{-- Status --}}
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_active"
                                    id="is_active_{{ $costCenter->id }}" value="1"
                                    {{ old('is_active', $costCenter->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active_{{ $costCenter->id }}">
                                    {{ __('dashboard.active') }}
                                </label>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ __('dashboard.cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary">
                                {{ __('dashboard.update') }}
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteCostCenterModal-{{ $costCenter->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('dashboard.delete_cost_center') }} : {{ $costCenter->name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <p>{{ __('dashboard.delete_cost_center_confirmation') }}</p>
                        <hr>

                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.cancel') }}
                        </button>

                        <form action="{{ route('setup-sidebar.cost_center.delete', $costCenter->id) }}" method="POST">
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
