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
        <div class="page-category">{{ __('dashboard.general_settings') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.property_facilities') }}</h2>
                <div class="page-header__subtitle">
                    {{ __('dashboard.set_the_property_facilities_you_will_be_use_in_this_property') }}</div>
            </div>
            <div class="n-table__top-btns">
                <button class="n-button n-button--primary" type="button" data-bs-toggle="collapse"
                    data-bs-target="#filterCollapse">
                    {{ __('dashboard.filter') }}
                </button>
                <div>
                    @can('property_facility.add')
                        <a href="{{ route('setup-sidebar.unit.create') }}" class="n-button n-button--green"
                            style="text-decoration:none;" tabindex="0" data-bs-toggle="modal"
                            data-bs-target="#addFacilityModal">
                            {{ __('dashboard.new_facility') }}
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        <div class="collapse mb-3" id="filterCollapse">

            <form method="GET" action="{{ route('setup-sidebar.property_facility.index') }}">

                <div class="card card-body">

                    <div class="row g-3">

                        <!-- Status -->
                        <div class="col-lg-2 col-md-3">
                            <label class="form-label">
                                {{ __('dashboard.status') }}
                            </label>

                            <select name="status" class="form-select">
                                <option value="">
                                    {{ __('dashboard.all') }}
                                </option>

                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>
                                    {{ __('dashboard.active') }}
                                </option>

                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>
                                    {{ __('dashboard.inactive') }}
                                </option>
                            </select>
                        </div>

                        <!-- Category -->
                        <div class="col-lg-3 col-md-3">
                            <label class="form-label">
                                {{ __('dashboard.facility_category') }}
                            </label>

                            <select name="category_id" class="form-select">

                                <option value="">
                                    {{ __('dashboard.all') }}
                                </option>

                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="col-lg-2 col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                {{ __('dashboard.search') }}
                            </button>
                        </div>

                        <div class="col-lg-2 col-md-3 d-flex align-items-end">
                            <a href="{{ route('setup-sidebar.property_facility.index') }}"
                                class="btn btn-outline-secondary w-100">
                                {{ __('dashboard.reset') }}
                            </a>
                        </div>

                    </div>

                </div>

            </form>

        </div>

        <div class="container my-4">
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center align-middle bg-white">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">{{ __('dashboard.property_facility') }}</th>
                            <th scope="col">{{ __('dashboard.status') }}</th>
                            <th scope="col">{{ __('dashboard.category') }}</th>
                            <th scope="col">{{ __('dashboard.description') }}</th>
                            <th scope="col">{{ __('dashboard.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse($propertyFacilities as $facility)
                            <tr>
                                <td>{{ $facility->facility->name ?? '-' }}</td>

                                <td>
                                    <span class="badge bg-{{ $facility->status ? 'success' : 'danger' }}">
                                        {{ $facility->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                <td>{{ $facility->category->name ?? '-' }}</td>

                                <td>
                                    {{ $facility->description ?? '-' }}
                                </td>

                                <td>
                                    <div class="d-flex align-items-center gap-2 justify-content-center">
                                        @can('property_facility.view')
                                            <!-- View -->
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#viewFacilityModal{{ $facility->id }}">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        @endcan

                                        @can('property_facility.edit')
                                            <!-- Edit -->
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#editFacilityModal{{ $facility->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        @endcan


                                        <div class="dropdown position-static">

                                            <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>

                                            <ul class="dropdown-menu dropdown-menu-end">

                                                <li>
                                                    @can('property_facility.delete')
                                                        <a href="#" class="dropdown-item text-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deletepropertyFacilityModal{{ $facility->id }}">
                                                            {{ __('dashboard.delete') }}
                                                        </a>
                                                    @endcan

                                                </li>

                                                <li>
                                                    <form
                                                        action="{{ route('setup-sidebar.property_facility.toggleUpdate', $facility->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            {{ $facility->status ? __('dashboard.deactivate') : __('dashboard.activate') }}
                                                        </button>
                                                    </form>
                                                </li>

                                            </ul>

                                        </div>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    {{ __('dashboard.no_properties_facilities_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div class="modal fade" id="addFacilityModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <form action="{{ route('setup-sidebar.property_facility.store') }}" method="POST">

                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('dashboard.add_property_facility') }}
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <!-- Category Dropdown -->
                        <div class="mb-3">
                            <label>{{ __('dashboard.facility_category') }}</label>

                            <select name="facility_category_id" id="categoryDropdown" class="form-control" required>

                                <option value="">{{ __('dashboard.select_category') }}</option>

                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ $category->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <!-- Facility Dropdown (Dependent) -->
                        <div class="mb-3">
                            <label>{{ __('dashboard.property_facility') }}</label>

                            <select name="facility_id" id="facilityDropdown" class="form-control" required>

                                <option value="">{{ __('dashboard.select_facility') }}</option>

                            </select>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label>{{ __('dashboard.description') }} ({{ __('dashboard.optional') }})</label>

                            <textarea name="description_en" class="form-control"></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.cancel') }}
                        </button>

                        <button class="btn btn-primary">
                            {{ __('dashboard.save') }}
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    @foreach ($propertyFacilities as $facility)
        <div class="modal fade" id="editFacilityModal{{ $facility->id }}">
            <div class="modal-dialog modal-lg">

                <div class="modal-content">

                    <form action="{{ route('setup-sidebar.property_facility.update', $facility->id) }}" method="POST">

                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ __('dashboard.edit_property_facility') }}
                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <!-- Category Dropdown -->
                            <div class="mb-3">
                                <label>{{ __('dashboard.facility_category') }}</label>

                                <select name="facility_category_id" class="form-control" required>

                                    <option value="">
                                        {{ __('dashboard.select_category') }}
                                    </option>

                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $facility->facility_category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <!-- Facility Dropdown -->
                            <div class="mb-3">
                                <label>{{ __('dashboard.property_facility') }}</label>

                                <select name="facility_id" class="form-control" required>

                                    <option value="">
                                        {{ __('dashboard.select_facility') }}
                                    </option>

                                    @foreach ($facilities as $f)
                                        <option value="{{ $f->id }}"
                                            {{ $facility->facility_id == $f->id ? 'selected' : '' }}>
                                            {{ $f->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label>{{ __('dashboard.description') }}</label>

                                <textarea name="description_en" class="form-control">{{ $facility->description }}</textarea>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ __('dashboard.cancel') }}
                            </button>

                            <button class="btn btn-primary">
                                {{ __('dashboard.update') }}
                            </button>
                        </div>

                    </form>

                </div>

            </div>
        </div>

        <div class="modal fade" id="viewFacilityModal{{ $facility->id }}">
            <div class="modal-dialog modal-lg">

                <div class="modal-content">

                    <form action="#" method="POST">

                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ __('dashboard.view_property_facility') }}
                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <!-- Category Dropdown -->
                            <div class="mb-3">
                                <label>{{ __('dashboard.facility_category') }}</label>

                                <select name="facility_category_id" class="form-control" required disabled>

                                    <option value="">
                                        {{ __('dashboard.select_category') }}
                                    </option>

                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $facility->facility_category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <!-- Facility Dropdown -->
                            <div class="mb-3">
                                <label>{{ __('dashboard.property_facility') }}</label>

                                <select name="facility_id" class="form-control" required disabled>

                                    <option value="">
                                        {{ __('dashboard.select_facility') }}
                                    </option>

                                    @foreach ($facilities as $f)
                                        <option value="{{ $f->id }}"
                                            {{ $facility->facility_id == $f->id ? 'selected' : '' }}>
                                            {{ $f->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label>{{ __('dashboard.description') }}</label>

                                <textarea name="description_en" class="form-control" disabled>{{ $facility->description }}</textarea>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ __('dashboard.cancel') }}
                            </button>
                        </div>

                    </form>

                </div>

            </div>
        </div>

        <div class="modal fade" id="deletepropertyFacilityModal{{ $facility->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('dashboard.delete_facility_modal') }} – {{ $facility->category->name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p>{{ __('dashboard.delete_property_facility_confirmation') }}</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.cancel') }}
                        </button>
                        <form action="{{ route('setup-sidebar.property_facility.delete', $facility->id) }}"
                            method="POST">
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
        document.addEventListener('DOMContentLoaded', function() {
            const categoryDropdown = document.getElementById('categoryDropdown');
            const facilityDropdown = document.getElementById('facilityDropdown');

            if (!categoryDropdown || !facilityDropdown) {
                return;
            }

            categoryDropdown.addEventListener('change', function() {
                const categoryId = this.value;
                const facilitiesUrl = @json(url('/app/admin/get-facilities'));

                facilityDropdown.innerHTML =
                    '<option value="">{{ __('dashboard.select_facility') }}</option>';

                if (!categoryId) {
                    return;
                }

                fetch(`${facilitiesUrl}?category_id=${encodeURIComponent(categoryId)}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Failed to load facilities (${response.status})`);
                        }

                        return response.json();
                    })
                    .then(data => {
                        data.forEach(item => {
                            facilityDropdown.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                        });
                    })
                    .catch(error => {
                        console.error('Failed to load facilities:', error);
                    });
            });
        });
    </script>
@endpush
