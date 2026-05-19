@extends('layouts.app')

@section('title', 'Users Management')

<style>
    /* Contact Information */
    .contact-info {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .contact-number {
        color: var(--text-dark);
        font-weight: 500;
    }

    .contact-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: var(--light-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-blue);
        text-decoration: none;
        transition: all 0.2s;
    }

    .contact-icon:hover {
        background-color: var(--primary-blue);
        color: white;
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
    }

    .n-button--primary {
        background-color: white;
        color: var(--primary-blue);
        border-color: var(--border-color);
    }

    .n-button--primary:hover {
        background-color: #f8f9fa;
        border-color: var(--primary-blue);
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
        background-color: white;
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
    }

    .filter-form--dark .form-control {
        background-color: #495057;
        border-color: #6c757d;
        color: white;
        width: 100%;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
    }

    .filter-form--dark .form-control::placeholder {
        color: #adb5bd;
    }

    .filter-form--dark .form-control:focus {
        background-color: #495057;
        border-color: #4a90e2;
        color: white;
        box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
        outline: none;
    }

    /* Grid/Table Styles */
    .k-grid {
        background: white;
        border-radius: 0.5rem;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .k-grid .k-grid-header {
        background-color: #f8f9fa;
        padding: 0;
    }

    .k-grid .k-header {
        background-color: #f8f9fa;
        padding: 1rem 1.25rem;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #e9ecef;
    }

    .k-grid .k-grid-content {
        min-height: 300px;
    }

    .k-grid .k-grid-table {
        width: 100%;
    }

    .k-grid .k-grid-table td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e9ecef;
        vertical-align: middle;
    }

    .k-grid .k-grid-norecords {
        text-align: center;
        padding: 3rem;
        color: #6c757d;
    }

    /* Column Widths */
    .k-grid col:nth-child(1) {
        width: 70px;
    }

    .k-grid col:nth-child(2) {
        width: auto;
    }

    .k-grid col:nth-child(3) {
        width: auto;
    }

    .k-grid col:nth-child(4) {
        width: 100px;
    }

    .k-grid col:nth-child(5) {
        width: auto;
    }

    .k-grid col:nth-child(6) {
        width: auto;
    }

    .k-grid col:nth-child(7) {
        width: 250px;
    }

    .k-grid col:nth-child(8) {
        width: auto;
    }

    .k-grid col:nth-child(9) {
        width: auto;
    }

    .k-grid col:nth-child(10) {
        width: 160px;
    }

    /* Status Badges */
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 500;
        display: inline-block;
    }

    .status-active {
        background-color: #d4edda;
        color: #155724;
    }

    .status-inactive {
        background-color: #f8d7da;
        color: #721c24;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    /* User Avatar */
    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        font-weight: 600;
    }

    /* Properties List */
    .properties-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .property-tag {
        background-color: #e9ecef;
        padding: 0.25rem 0.75rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        color: #495057;
        white-space: nowrap;
    }

    /* Pagination */
    .k-pager-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 0;
        border-top: 1px solid var(--border-color);
        background: white;
        border-radius: 0 0 0.5rem 0.5rem;
    }

    .n-pager__sizes {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .n-pager__info {
        color: var(--text-muted);
        font-size: 0.875rem;
    }

    .k-pager-nav {
        width: 32px;
        height: 32px;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border-color);
        color: var(--primary-blue);
        text-decoration: none;
    }

    .k-pager-nav:hover {
        background-color: var(--primary-blue);
        color: white;
    }

    .k-state-disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .k-state-disabled:hover {
        background-color: transparent;
        color: var(--primary-blue);
    }

    /* Form Input Messages */
    .form__input-msg {
        font-size: 0.75rem;
        margin-top: 0.25rem;
        min-height: 1rem;
    }

    /* Combobox Styling */
    .k-combobox {
        width: 100%;
    }

    .k-dropdown-wrap {
        background-color: #495057;
        border-color: #6c757d;
    }

    .k-input {
        background-color: #495057;
        border: 1px solid #6c757d;
        color: white;
        width: 100%;
        padding: 0.5rem 0.75rem;
    }

    .k-input::placeholder {
        color: #adb5bd;
    }

    .k-select {
        background-color: #495057;
        border-left: 1px solid #6c757d;
    }

    .k-i-arrow-s,
    .k-i-close {
        color: #adb5bd;
    }

    /* Button Styling */
    .button--primary {
        background-color: #4a90e2;
        color: white;
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 0.375rem;
        font-weight: 500;
        cursor: pointer;
    }

    .button--primary:hover {
        background-color: #3a80d2;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .n-table__top-btns {
            align-self: flex-end;
        }
    }

    @media (max-width: 768px) {
        .k-grid {
            overflow-x: auto;
        }

        .filter-form .row>div {
            margin-bottom: 1rem;
        }
    }
</style>

@section('content')
    <div class="container-fluid py-4 bg-white" style="border-radius:10px;">

        <!-- Page Header -->
        <div class="page-category">{{ __('dashboard.company') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.users') }}</h2>
                <div class="page-header__subtitle">{{ __('dashboard.view_and_manage_your_users') }}</div>
            </div>
            <div class="n-table__top-btns">
                <button class="n-button n-button--primary">
                    <i class="fas fa-filter"></i>
                    {{ __('dashboard.filter') }}
                </button>
                @can('user.add')
                    <a href="{{ route('setup-sidebar.property-user.create') }}" class="n-button n-button--green"
                        style="text-decoration:none;">
                        <i class="fas fa-plus"></i>
                        {{ __('dashboard.new_user') }}
                    </a>
                @endcan

            </div>
        </div>

        <!-- Filter Form -->
        <div class="filter-form__container" style="display: none;">
            <form action="{{ route('setup-sidebar.property-user.index') }}" method="GET" class="filter-form"
                style="background-color: white; padding: 15px; border-radius: 8px;">
                <div class="row">
                    <div class="col-lg-3 col-md-4">
                        <label for="Properties">{{ __('dashboard.properties') }}</label>
                        <input id="Properties" type="text" class="form-control" name="properties"
                            placeholder="Enter Properties" value="{{ request('properties') }}">
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <label for="FullName">{{ __('dashboard.full_name') }}</label>
                        <input id="FullName" type="text" class="form-control" name="full_name" placeholder="Full Name"
                            value="{{ request('full_name') }}">
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <label for="Role">{{ __('dashboard.role') }}</label>
                        <input id="Role" type="text" class="form-control" name="role" placeholder="Role"
                            value="{{ request('role') }}">
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <label for="UserType">{{ __('dashboard.user_type') }}</label>
                        <select id="UserType" class="form-control" name="user_type">
                            <option value="">{{ __('dashboard.select_user_type') }}</option>
                            <option value="admin" {{ request('user_type') == 'admin' ? 'selected' : '' }}>
                                {{ __('dashboard.admin') }}</option>
                            <option value="manager" {{ request('user_type') == 'manager' ? 'selected' : '' }}>
                                {{ __('dashboard.manager') }}
                            </option>
                            <option value="staff" {{ request('user_type') == 'staff' ? 'selected' : '' }}>
                                {{ __('dashboard.staff') }}</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <label for="UserName">{{ __('dashboard.username') }}</label>
                        <input id="UserName" type="text" class="form-control" name="username" placeholder="Username"
                            value="{{ request('username') }}">
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <label for="status">{{ __('dashboard.status') }}</label>
                        <select id="status" class="form-control" name="status">
                            <option value="">{{ __('dashboard.select_status') }}</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                {{ __('dashboard.active') }}</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                {{ __('dashboard.inactive') }}
                            </option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                {{ __('dashboard.pending') }}</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <label for="mobileNumber">{{ __('dashboard.mobile_number') }}</label>
                        <input name="mobile_number" type="text" id="mobileNumber" class="form-control"
                            placeholder="Mobile Number" value="{{ request('mobile_number') }}">
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <label for="email">{{ __('dashboard.email') }}</label>
                        <input name="email" type="email" id="email" class="form-control" placeholder="Email"
                            value="{{ request('email') }}">
                    </div>
                    <div class="col-lg-12 u-flex-end">
                        <button class="button button--primary mt-3">{{ __('dashboard.search') }}</button>
                    </div>
                </div>
            </form>
        </div>


        <!-- Users Grid -->
        <div class="k-grid">

            <div class="k-grid-content">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{ __('dashboard.name') }}</th>
                            <th>{{ __('dashboard.username') }}</th>
                            <th>{{ __('dashboard.status') }}</th>
                            <th>{{ __('dashboard.role') }}</th>
                            <th>{{ __('dashboard.user_type') }}</th>
                            <th>{{ __('dashboard.properties') }}</th>
                            <th>{{ __('dashboard.mobile') }}</th>
                            <th>{{ __('dashboard.email') }}</th>
                            <th>{{ __('dashboard.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->profile_data['first_name_en'] ?? '-' }}</td>
                                <td>{{ $user->name }}</td>
                                <td>
                                    <span class="badge bg-success">{{ ucfirst($user->status) }}</span>
                                </td>
                                <td>{{ $user->roles->pluck('name')->implode(', ') ?: '-' }}</td>
                                <td>{{ ucfirst($user->user_type) }}</td>
                                <td>
                                    {{ $user->assignedProperties->pluck('property_name_en')->filter()->implode(', ') ?: ($user->property?->property_name_en ?? '-') }}
                                </td>
                                <td>{{ $user->contact_info['mobile_number'] ?? '-' }}</td>
                                <td>{{ $user->contact_info['email'] ?? '-' }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        @can('user.edit')
                                            <!-- Edit Button -->
                                            <a href="{{ route('setup-sidebar.property-user.edit', $user->id) }}"
                                                class="btn btn-sm btn-primary" title="{{ __('dashboard.edit') }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        @endcan

                                        @can('user.view')
                                            <!-- View Button -->
                                            <a href="{{ route('setup-sidebar.property-user.view', $user->id) }}"
                                                class="btn btn-sm btn-secondary" title="{{ __('dashboard.view') }}">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @endcan


                                        <!-- Dropdown -->
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary " type="button"
                                                id="actionMenu{{ $user->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="actionMenu{{ $user->id }}">
                                                <li>
                                                     @if (Auth::user()->hasRole('Administrator'))
                                                     @else
                                                    @can('user.delete')
                                                        @if ($user->canBeDeletedFromTenantDashboard(auth()->user()))
                                                            <a href="#" class="dropdown-item" data-bs-toggle="modal"
                                                                data-bs-target="#deactivateUserModal{{ $user->id }}">
                                                                <i class="bi bi-person-x text-danger">
                                                                    {{ __('dashboard.deactivate') }}</i>
                                                            </a>
                                                        @endif
                                                    @endcan
                                                    @endif

                                                </li>
                                                <li>
                                                    @can('user.assign_outlet')
                                                        <a href="#" class="dropdown-item" data-bs-toggle="modal"
                                                            data-bs-target="#assignOutletModal{{ $user->id }}">
                                                            <i class="bi bi-building-check text-primary">
                                                                {{ __('dashboard.assign_outlet') }}</i>
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
                                <td colspan="9" class="text-center">{{ __('dashboard.no_records_available') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


        </div>
        <div class="d-flex justify-content-center mt-3">
            {{ $users->links() }}
        </div>
    </div>
@endsection
@foreach ($users as $user)
    @if ($user->canBeDeletedFromTenantDashboard(auth()->user()))
    <!-- Deactivate Modal -->
    <div class="modal fade" id="deactivateUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ __('dashboard.deactivate_user') }} : {{ $user->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <p>{{ __('dashboard.deactivate_user_confirmation') }}</p>
                    <hr>
                    <dl class="row mb-0">
                        <dt class="col-sm-5">{{ __('dashboard.full_name') }}</dt>
                        <dd class="col-sm-7">{{ $user->profile_data['first_name_en'] ?? '-' }}</dd>

                        <dt class="col-sm-5">{{ __('dashboard.username') }}</dt>
                        <dd class="col-sm-7">{{ $user->profile_data['username'] ?? '-' }}</dd>

                        <dt class="col-sm-5">{{ __('dashboard.status') }}</dt>
                        <dd class="col-sm-7">{{ ucfirst($user->status ?? '-') }}</dd>
                    </dl>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('dashboard.cancel') }}
                    </button>

                    <form action="{{ route('setup-sidebar.property-user.delete', $user->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger">
                            {{ __('dashboard.deactivate') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif


    <!-- Assign Property & Outlet Modal -->
    <div class="modal fade" id="assignOutletModal{{ $user->id }}" tabindex="-1"
        aria-labelledby="assignOutletModalLabel{{ $user->id }}" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                {{-- Modal Header --}}
                <div class="modal-header">
                    <h5 class="modal-title" id="assignOutletModalLabel{{ $user->id }}">
                        {{ __('dashboard.assign_outlet') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Form --}}
                <form action="{{ route('setup-sidebar.property-user.assign', $user->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    {{-- Modal Body --}}
                    <div class="modal-body">

                        {{-- Property --}}
                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('dashboard.select_property') }}
                            </label>
                            <select name="property_id" class="form-select" required>
                                <option value="">{{ __('dashboard.select_property') }}</option>

                                @foreach ($properties as $property)
                                    <option value="{{ $property->id }}"
                                        {{ (int) $user->branch_id === (int) $property->branch_id ? 'selected' : '' }}>
                                        {{ $property->property_name_en }}
                                    </option>
                                @endforeach
                            </select>

                        </div>

                        {{-- Outlet --}}
                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('dashboard.select_outlet') }}
                            </label>
                            <select name="outlet_id" class="form-select" required>
                                <option value="">{{ __('dashboard.select_outlet') }}</option>

                                @foreach ($outlets as $outlet)
                                    <option value="{{ $outlet->id }}"
                                        {{ (int) $user->outlet_id === (int) $outlet->id ? 'selected' : '' }}>
                                        {{ $outlet->name }}
                                    </option>
                                @endforeach
                            </select>

                        </div>
                        {{-- Currently Assigned --}}
                        @if ($user->property || $user->outlet)
                            <hr>

                            <h6 class="text-muted mb-2">
                                {{ __('dashboard.current_assignment') }}
                            </h6>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <th style="width: 35%;">
                                                {{ __('dashboard.property') }}
                                            </th>
                                            <th>
                                                {{ __('dashboard.outlet') }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <td>
                                                {{ $user->property?->property_name_en ?? '-' }}
                                            </td>
                                            <td>
                                                {{ $user->outlet?->name ?? '-' }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endif

                    </div>

                    {{-- Modal Footer --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.cancel') }}
                        </button>

                        <button type="submit" class="btn btn-primary">
                            {{ __('dashboard.assign') }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endforeach
@push('scripts')
    <script>
        const toggleBtn = document.querySelector('.n-button.n-button--primary');
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
    </script>
@endpush
