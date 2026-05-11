@extends('layouts.app')

@section('title', 'Vendors')

@php
    $theme = \App\Models\ThemeCustomization::getTheme();
@endphp

<style>
    .Parent-Contact {
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
        color: {{ $theme->dashboard_card_title_color }};
        margin-bottom: 0.5rem;
    }

    .page-header__subtitle {
        font-size: 1rem;
        color: #6c757d;
    }

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
        border-color: {{ $theme->primary_color }};
    }

    .n-button--green {
        background-color: {{ $theme->button_primary_color }};
        color: white;
        border-color: {{ $theme->button_primary_color }};
    }

    .n-button--green:hover {
        background-color: {{ $theme->button_secondary_color ?? $theme->button_primary_color }};
        border-color: {{ $theme->button_secondary_color ?? $theme->button_primary_color }};
    }

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
</style>
@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">

        <!-- Page Header -->
        <div class="page-category">{{ __('dashboard.vendors') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.vendors') }}</h2>
                <div class="page-header__subtitle">
                    {{ __('dashboard.you_can_see_and_manage_the_vendors') }}
                </div>
            </div>
            <div class="n-table__top-btns">
                <button class="n-button n-button--primary" data-bs-toggle="collapse" data-bs-target="#filterContainer">

                    <i class="bi bi-funnel"></i>
                    {{ __('dashboard.filter') }}

                </button>
                @can('vendor.add')
                    <button class="n-button n-button--green" data-bs-toggle="modal" data-bs-target="#vendorModal">
                        <i class="fas fa-plus"></i>
                        {{ __('dashboard.new_vendor') }}
                    </button>
                @endcan

            </div>
        </div>

        <div class="collapse {{ request()->hasAny(['search', 'email', 'phone', 'status', 'vat', 'from_date', 'to_date']) ? 'show' : '' }}"
            id="filterContainer">

            <div class="card mb-4">
                <div class="card-body">

                    <form method="GET" action="{{ route('dashboard.vendor.index') }}">

                        <div class="row g-3">

                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">{{ __('dashboard.name') }}</label>
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control">
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">{{ __('dashboard.email') }}</label>
                                <input type="text" name="email" value="{{ request('email') }}" class="form-control">
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">{{ __('dashboard.phone') }}</label>
                                <input type="text" name="phone" value="{{ request('phone') }}" class="form-control">
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">{{ __('dashboard.vat_number') }}</label>
                                <input type="text" name="vat" value="{{ request('vat') }}" class="form-control">
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">{{ __('dashboard.status') }}</label>

                                <select name="status" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>
                                        {{ __('dashboard.active') }}
                                    </option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>
                                        {{ __('dashboard.inactive') }}
                                    </option>
                                </select>

                            </div>

                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">{{ __('dashboard.created_from') }}</label>
                                <input type="date" name="from_date" value="{{ request('from_date') }}"
                                    class="form-control">
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">{{ __('dashboard.created_to') }}</label>
                                <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
                            </div>

                            <div class="col-lg-3 col-md-6 d-flex align-items-end">

                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-search"></i>
                                    {{ __('dashboard.search') }}
                                </button>

                                <a href="{{ route('dashboard.vendor.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise"></i>
                                    {{ __('dashboard.reset') }}
                                </a>

                            </div>

                        </div>

                    </form>

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('dashboard.name') }}</th>
                            <th>{{ __('dashboard.status') }}</th>
                            <th>{{ __('dashboard.phone') }}</th>
                            <th>{{ __('dashboard.email') }}</th>
                            <th>{{ __('dashboard.vat_registration_number') }}</th>
                            <th>{{ __('dashboard.commercial_registration_number') }}</th>
                            <th class="text-center">{{ __('dashboard.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendors as $vendor)
                            <tr>
                                <td>{{ $vendor->name }}</td>
                                <td>
                                    @if ($vendor->is_active)
                                        <span class="badge bg-success">{{ __('dashboard.active') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('dashboard.inactive') }}</span>
                                    @endif
                                </td>
                                <td>{{ $vendor->dial_code ? $vendor->dial_code . ' ' . $vendor->phone : $vendor->phone }}
                                </td>
                                <td>{{ $vendor->email ?? '-' }}</td>
                                <td>{{ $vendor->vat_registration_number ?? '-' }}</td>
                                <td>{{ $vendor->commercial_registration_number ?? '-' }}</td>
                                <td class="text-center" style="white-space: nowrap;">
                                    @can('vendor.view')
                                        <button class="btn btn-sm btn-info me-1" data-bs-toggle="modal"
                                            data-bs-target="#viewVendorModal{{ $vendor->id }}"
                                            title="{{ __('dashboard.view') }}" style="padding: 0.15rem 0.4rem; font-size: 0.75rem;">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    @endcan
                                    @can('vendor.edit')
                                        <button class="btn btn-sm btn-primary me-1" data-bs-toggle="modal"
                                            data-bs-target="#editVendorModal{{ $vendor->id }}" style="padding: 0.15rem 0.4rem; font-size: 0.75rem;">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endcan
                                    @can('vendor.delete')
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteVendorModal{{ $vendor->id }}"
                                            title="{{ __('dashboard.delete') }}" style="padding: 0.15rem 0.4rem; font-size: 0.75rem;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endcan

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">{{ __('dashboard.no_data_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($vendors->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $vendors->links() }}
                    </div>
                @endif
            </div>
        </div>

    </main>

    <!-- Vendor Modal -->
    <div class="modal fade" id="vendorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="vendorModalTitle">{{ __('dashboard.add_new_vendor') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="Post" action="{{ route('dashboard.vendor.store') }}">
                    @csrf
                    <input type="hidden" name="id" id="vendorId">
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Status Toggle -->
                            <div class="col-md-12" id="statusField" style="display: none;">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="is_active"
                                        id="vendorStatus">
                                    <label class="form-check-label"
                                        for="vendorStatus">{{ __('dashboard.status') }}</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('dashboard.vendor_name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="vendorName" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.dial_code') }}</label>
                                <input type="text" class="form-control" name="dial_code" id="vendorDialCode"
                                    placeholder="+966">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.phone') }}</label>
                                <input type="text" class="form-control" name="phone" id="vendorPhone">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">{{ __('dashboard.email') }}</label>
                                <input type="email" class="form-control" name="email" id="vendorEmail">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">{{ __('dashboard.vat_registration_number') }}</label>
                                <input type="text" class="form-control" name="vat_registration_number"
                                    id="vendorVatNumber">
                            </div>
                            <div class="col-md-4">
                                <label
                                    class="form-label-custom">{{ __('dashboard.commercial_registration_number') }}</label>
                                <input type="text" class="form-control" name="commercial_registration_number"
                                    id="vendorCommercialReg">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">{{ __('dashboard.postal_code') }}</label>
                                <input type="text" class="form-control" name="postal_code" id="vendorPostalCode">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label-custom">{{ __('dashboard.vendor_description') }}</label>
                                <textarea class="form-control" name="description" id="vendorDescription" rows="2"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label-custom">{{ __('dashboard.vendor_address') }}</label>
                                <textarea class="form-control" name="address" id="vendorAddress" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                        <button type="submit" class="btn btn-primary"
                            id="saveVendorBtn">{{ __('dashboard.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach ($vendors as $vendor)
        <div class="modal fade" id="editVendorModal{{ $vendor->id }}" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('dashboard.edit_vendor') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form method="POST" action="{{ route('dashboard.vendor.update', $vendor->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="modal-body">
                            <div class="row g-3">

                                <!-- Status -->
                                <div class="col-md-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                            {{ $vendor->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label">
                                            {{ __('dashboard.status') }}
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label-custom">
                                        {{ __('dashboard.vendor_name') }}
                                    </label>
                                    <input type="text" class="form-control" name="name"
                                        value="{{ $vendor->name }}" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label-custom">
                                        {{ __('dashboard.dial_code') }}
                                    </label>
                                    <input type="text" class="form-control" name="dial_code"
                                        value="{{ $vendor->dial_code }}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label-custom">
                                        {{ __('dashboard.phone') }}
                                    </label>
                                    <input type="text" class="form-control" name="phone"
                                        value="{{ $vendor->phone }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label-custom">
                                        {{ __('dashboard.email') }}
                                    </label>
                                    <input type="email" class="form-control" name="email"
                                        value="{{ $vendor->email }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label-custom">
                                        {{ __('dashboard.vat_registration_number') }}
                                    </label>
                                    <input type="text" class="form-control" name="vat_registration_number"
                                        value="{{ $vendor->vat_registration_number }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label-custom">
                                        {{ __('dashboard.commercial_registration_number') }}
                                    </label>
                                    <input type="text" class="form-control" name="commercial_registration_number"
                                        value="{{ $vendor->commercial_registration_number }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label-custom">
                                        {{ __('dashboard.postal_code') }}
                                    </label>
                                    <input type="text" class="form-control" name="postal_code"
                                        value="{{ $vendor->postal_code }}">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label-custom">
                                        {{ __('dashboard.vendor_description') }}
                                    </label>
                                    <textarea class="form-control" name="description" rows="2">{{ $vendor->description }}</textarea>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label-custom">
                                        {{ __('dashboard.vendor_address') }}
                                    </label>
                                    <textarea class="form-control" name="address" rows="2">{{ $vendor->address }}</textarea>
                                </div>

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

        <div class="modal fade" id="viewVendorModal{{ $vendor->id }}" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('dashboard.view_vendor') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">

                            <!-- Status -->
                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" disabled
                                        {{ $vendor->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        {{ __('dashboard.status') }}
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('dashboard.vendor_name') }}</label>
                                <input type="text" class="form-control" value="{{ $vendor->name }}" disabled>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.dial_code') }}</label>
                                <input type="text" class="form-control" value="{{ $vendor->dial_code }}" disabled>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.phone') }}</label>
                                <input type="text" class="form-control" value="{{ $vendor->phone }}" disabled>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label-custom">{{ __('dashboard.email') }}</label>
                                <input type="email" class="form-control" value="{{ $vendor->email }}" disabled>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label-custom">{{ __('dashboard.vat_registration_number') }}</label>
                                <input type="text" class="form-control"
                                    value="{{ $vendor->vat_registration_number }}" disabled>
                            </div>

                            <div class="col-md-4">
                                <label
                                    class="form-label-custom">{{ __('dashboard.commercial_registration_number') }}</label>
                                <input type="text" class="form-control"
                                    value="{{ $vendor->commercial_registration_number }}" disabled>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label-custom">{{ __('dashboard.postal_code') }}</label>
                                <input type="text" class="form-control" value="{{ $vendor->postal_code }}" disabled>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label-custom">{{ __('dashboard.vendor_description') }}</label>
                                <textarea class="form-control" rows="2" disabled>{{ $vendor->description }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label-custom">{{ __('dashboard.vendor_address') }}</label>
                                <textarea class="form-control" rows="2" disabled>{{ $vendor->address }}</textarea>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.close') }}
                        </button>
                    </div>

                </div>
            </div>
        </div>


        <div class="modal fade" id="deleteVendorModal{{ $vendor->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('dashboard.delete_vendor') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p>
                            {{ __('dashboard.delete_vendor_confirmation') }}
                        </p>

                        <strong class="text-danger">
                            {{ $vendor->name }}
                        </strong>
                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.cancel') }}
                        </button>

                        <form method="POST" action="{{ route('dashboard.vendor.destroy', $vendor->id) }}">
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
