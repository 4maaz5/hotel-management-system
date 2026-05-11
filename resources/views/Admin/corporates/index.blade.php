@extends('layouts.app')

@section('title', 'Customers | Corporates')

@php
    $theme = \App\Models\ThemeCustomization::getTheme();
@endphp

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

    padding: 0.5rem 1rem;

    .n-button {
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

    .filter-form--dark .form-select {
        background-color: #495057;
        border: 1px solid #6c757d;
        color: white;
    }

    .form__input-msg {
        font-size: 0.75rem;
        margin-top: 0.25rem;
        min-height: 1rem;
        color: #6c757d;
    }

    .unit-card .card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
        z-index: 10;
    }

    .unit-card:hover .card-overlay {
        opacity: 1;
    }

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
        <div class="page-category">{{ __('dashboard.customers') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.corporates') }}</h2>
                <div class="page-header__subtitle">
                    {{ __('dashboard.you_can_see_and_manage_the_corporates_profile') }}</div>
            </div>
            <div class="n-table__top-btns">
                <button class="btn btn-light" onclick="toggleFilter()">
                    {{ __('dashboard.filter') }}
                </button>
                @can('corporate.add')
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#corporateModal">
                        <i class="fas fa-plus"></i>
                        {{ __('dashboard.new_corporate') }}
                    </button>
                @endcan
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('dashboard.corporate.index') }}">
            <div class="filter-form__container mb-4" id="filterContainer" style="display: none;">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">{{ __('dashboard.name') }}</label>
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                    placeholder="{{ __('dashboard.enter_name') }}">
                            </div>
                            <div class="col-lg-2 col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">{{ __('dashboard.search') }}</button>
                                <a href="{{ route('dashboard.corporate.index') }}" class="btn btn-outline-secondary">
                                    {{ __('dashboard.reset') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

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
                            <th>{{ __('dashboard.contact_person_name') }}</th>
                            <th>{{ __('dashboard.contact_person_phone') }}</th>
                            <th>{{ __('dashboard.discount') }}</th>
                            <th class="text-center">{{ __('dashboard.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($corporates as $corporate)
                            <tr>
                                <td>{{ $corporate->name }}</td>
                                <td>
                                    @if ($corporate->is_active)
                                        <span class="badge bg-success">{{ __('dashboard.active') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('dashboard.inactive') }}</span>
                                    @endif
                                </td>
                                <td>{{ $corporate->phone ?? '-' }}</td>
                                <td>{{ $corporate->email ?? '-' }}</td>
                                <td>{{ $corporate->vat_registration_number ?? '-' }}</td>
                                <td>{{ $corporate->contact_person_name ?? '-' }}</td>
                                <td>{{ $corporate->contact_person_phone ?? '-' }}</td>
                                <td>
                                    @if ($corporate->discount_value)
                                        {{ $corporate->discount_value }}{{ $corporate->discount_type === 'percentage' ? '%' : '' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                 <td class="text-center" style="white-space: nowrap;">
                                    @can('corporate.view')
                                        <button class="btn btn-sm btn-info me-1" onclick="viewCorporate({{ $corporate->id }})"
                                            title="View" style="padding: 0.15rem 0.4rem; font-size: 0.75rem;">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    @endcan
                                    @can('corporate.edit')
                                        <button class="btn btn-sm btn-primary me-1"
                                            onclick="editCorporate({{ $corporate->id }})" title="Edit" style="padding: 0.15rem 0.4rem; font-size: 0.75rem;">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endcan
                                    @can('corporate.delete')
                                        <button class="btn btn-sm btn-danger" onclick="deleteCorporate({{ $corporate->id }})"
                                            title="Delete" style="padding: 0.15rem 0.4rem; font-size: 0.75rem;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">{{ __('dashboard.no_data_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($corporates->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $corporates->links() }}
                    </div>
                @endif
            </div>
        </div>

    </main>

    <!-- Corporate Modal -->
    <div class="modal fade" id="corporateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="corporateModalTitle">{{ __('dashboard.add_new_corporate') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="corporateForm">
                    @csrf
                    <input type="hidden" name="id" id="corporateId">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="section-title">{{ __('dashboard.corporate_information') }}</h6>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.corporate_name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="corporateName" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.postal_code') }}</label>
                                <input type="text" class="form-control" name="postal_code" id="corporatePostalCode">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.vat_registration_number') }}</label>
                                <input type="text" class="form-control" name="vat_registration_number"
                                    id="corporateVatNumber">
                            </div>
                            <div class="col-md-3">
                                <label
                                    class="form-label-custom">{{ __('dashboard.commercial_registration_number') }}</label>
                                <input type="text" class="form-control" name="commercial_registration_number"
                                    id="corporateCommercialRegNumber">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.discount_method') }}</label>
                                <select class="form-select" name="discount_type" id="corporateDiscountType">
                                    <option value="">{{ __('dashboard.select_discount_method') }}</option>
                                    <option value="percentage">{{ __('dashboard.percentage') }}</option>
                                    <option value="fixed">{{ __('dashboard.fixed_amount') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.discount') }}</label>
                                <input type="number" class="form-control" name="discount_value"
                                    id="corporateDiscountValue" value="0" min="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.status') }}</label>
                                <select class="form-select" name="is_active" id="corporateIsActive">
                                    <option value="1">{{ __('dashboard.active') }}</option>
                                    <option value="0">{{ __('dashboard.inactive') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h6 class="section-title">{{ __('dashboard.address_information') }}</h6>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.country') }}</label>
                                <select class="form-select" name="country" id="corporateCountry">
                                    <option value="">{{ __('dashboard.select_country') }}</option>
                                    <option value="SA">Saudi Arabia</option>
                                    <option value="KW">Kuwait</option>
                                    <option value="AE">United Arab Emirates</option>
                                    <option value="BH">Bahrain</option>
                                    <option value="OM">Oman</option>
                                    <option value="QA">Qatar</option>
                                    <option value="EG">Egypt</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.city') }}</label>
                                <input type="text" class="form-control" name="city" id="corporateCity">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.district') }}</label>
                                <input type="text" class="form-control" name="district" id="corporateDistrict">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.street') }}</label>
                                <input type="text" class="form-control" name="street" id="corporateStreet">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.building_number') }}</label>
                                <input type="text" class="form-control" name="building_number"
                                    id="corporateBuildingNumber">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.secondary_number') }}</label>
                                <input type="text" class="form-control" name="secondary_number"
                                    id="corporateSecondaryNumber">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label-custom">{{ __('dashboard.address') }}</label>
                                <textarea class="form-control" name="address" id="corporateAddress" rows="2"></textarea>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <h6 class="section-title">{{ __('dashboard.contact_information') }}</h6>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.email') }}</label>
                                <input type="email" class="form-control" name="email" id="corporateEmail">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.phone') }}</label>
                                <input type="tel" class="form-control" name="phone" id="corporatePhone">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.contact_person_name') }}</label>
                                <input type="text" class="form-control" name="contact_person_name"
                                    id="corporateContactPerson">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.contact_person_phone') }}</label>
                                <input type="tel" class="form-control" name="contact_person_phone"
                                    id="corporateContactPersonPhone">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                        <button type="submit" class="btn btn-primary"
                            id="saveCorporateBtn">{{ __('dashboard.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteCorporateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('dashboard.delete_corporate') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('dashboard.delete_corporate_confirmation') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                    <button type="button" class="btn btn-danger"
                        id="confirmDeleteBtn">{{ __('dashboard.delete') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Container -->
    <div id="notificationContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>
@endsection
@push('scripts')
    <script>
        function showNotification(message, type = 'success') {
            const container = document.getElementById('notificationContainer');
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} alert-dismissible fade show`;
            notification.role = 'alert';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            container.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 4000);
        }

        function toggleFilter() {
            const filterContainer = document.getElementById('filterContainer');
            filterContainer.style.display = filterContainer.style.display === 'none' ? 'block' : 'none';
        }

        document.getElementById('corporateModal').addEventListener('show.bs.modal', function(e) {
            if (!e.relatedTarget) return;
            const button = e.relatedTarget;
            if (button.getAttribute('data-bs-target') === '#corporateModal') {
                resetCorporateForm();
            }
        });

        function resetCorporateForm() {
            document.getElementById('corporateModalTitle').textContent = '{{ __('dashboard.add_new_corporate') }}';
            document.getElementById('corporateForm').reset();
            document.getElementById('corporateId').value = '';
            document.getElementById('corporateIsActive').value = '1';
            document.getElementById('corporateDiscountValue').value = '0';
            document.getElementById('saveCorporateBtn').textContent = '{{ __('dashboard.save') }}';
        }

        document.getElementById('corporateForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const corporateId = document.getElementById('corporateId').value;
            const url = corporateId ? '{{ route('dashboard.corporate.update', ':id') }}'.replace(':id',
                    corporateId) :
                '{{ route('dashboard.corporate.store') }}';
            const method = corporateId ? 'PUT' : 'POST';

            const formData = new FormData(this);
            formData.append('_token', '{{ csrf_token() }}');

            fetch(url, {
                    method: method,
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(corporateId ? '{{ __('messages.corporate_updated_successfully') }}' :
                            '{{ __('messages.corporate_created_successfully') }}');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('corporateModal'));
                        modal.hide();
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showNotification(data.message || '{{ __('messages.error_occurred') }}', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('{{ __('messages.error_occurred') }}', 'danger');
                });
        });

        function viewCorporate(id) {
            fetch('{{ route('dashboard.corporate.show', ':id') }}'.replace(':id', id))
                .then(response => response.json())
                .then(corporate => {
                    fillCorporateForm(corporate);
                    document.getElementById('corporateModalTitle').textContent =
                        '{{ __('dashboard.view_corporate') }}';
                    document.getElementById('saveCorporateBtn').style.display = 'none';
                    document.querySelectorAll('#corporateForm input, #corporateForm select, #corporateForm textarea')
                        .forEach(el => el.disabled = true);
                    new bootstrap.Modal(document.getElementById('corporateModal')).show();
                });
        }

        function editCorporate(id) {
            fetch('{{ route('dashboard.corporate.show', ':id') }}'.replace(':id', id))
                .then(response => response.json())
                .then(corporate => {
                    fillCorporateForm(corporate);
                    document.getElementById('corporateModalTitle').textContent =
                        '{{ __('dashboard.edit_corporate') }}';
                    document.getElementById('saveCorporateBtn').style.display = 'block';
                    document.getElementById('saveCorporateBtn').textContent = '{{ __('dashboard.update') }}';
                    document.querySelectorAll('#corporateForm input, #corporateForm select, #corporateForm textarea')
                        .forEach(el => el.disabled = false);
                    new bootstrap.Modal(document.getElementById('corporateModal')).show();
                });
        }

        function fillCorporateForm(corporate) {
            document.getElementById('corporateId').value = corporate.id;
            document.getElementById('corporateName').value = corporate.name || '';
            document.getElementById('corporatePostalCode').value = corporate.postal_code || '';
            document.getElementById('corporateVatNumber').value = corporate.vat_registration_number || '';
            document.getElementById('corporateCommercialRegNumber').value = corporate.commercial_registration_number || '';
            document.getElementById('corporateDiscountType').value = corporate.discount_type || '';
            document.getElementById('corporateDiscountValue').value = corporate.discount_value || 0;
            document.getElementById('corporateIsActive').value = corporate.is_active ? '1' : '0';
            document.getElementById('corporateCountry').value = corporate.country || '';
            document.getElementById('corporateCity').value = corporate.city || '';
            document.getElementById('corporateDistrict').value = corporate.district || '';
            document.getElementById('corporateStreet').value = corporate.street || '';
            document.getElementById('corporateBuildingNumber').value = corporate.building_number || '';
            document.getElementById('corporateSecondaryNumber').value = corporate.secondary_number || '';
            document.getElementById('corporateAddress').value = corporate.address || '';
            document.getElementById('corporateEmail').value = corporate.email || '';
            document.getElementById('corporatePhone').value = corporate.phone || '';
            document.getElementById('corporateContactPerson').value = corporate.contact_person_name || '';
            document.getElementById('corporateContactPersonPhone').value = corporate.contact_person_phone || '';
        }

        let deleteCorporateId = null;

        function deleteCorporate(id) {
            deleteCorporateId = id;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteCorporateModal'));
            deleteModal.show();
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (deleteCorporateId) {
                fetch('{{ route('dashboard.corporate.destroy', ':id') }}'.replace(':id', deleteCorporateId), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        const deleteModal = bootstrap.Modal.getInstance(document.getElementById(
                            'deleteCorporateModal'));
                        deleteModal.hide();
                        if (data.success) {
                            showNotification('{{ __('messages.corporate_deleted_successfully') }}');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showNotification(data.message || '{{ __('messages.error_occurred') }}', 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('{{ __('messages.error_occurred') }}', 'danger');
                    });
            }
        });

        document.getElementById('corporateModal').addEventListener('hidden.bs.modal', function() {
            resetCorporateForm();
            document.querySelectorAll('#corporateForm input, #corporateForm select, #corporateForm textarea')
                .forEach(el => el.disabled = false);
        });
    </script>
@endpush
