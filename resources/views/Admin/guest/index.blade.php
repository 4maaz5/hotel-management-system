@extends('layouts.app')

@section('title', 'Customers | Guest')

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
                <h2 class="page-header__title">{{ __('dashboard.guests') }}</h2>
                <div class="page-header__subtitle">
                    {{ __('dashboard.you_can_see_and_manage_the_guests') }}</div>
            </div>
            <div class="n-table__top-btns">
                <button class="n-button n-button--primary" onclick="toggleFilter()">
                    {{ __('dashboard.filter') }}
                </button>
                @can('guest.add')
                    <button class="n-button n-button--green" data-bs-toggle="modal" data-bs-target="#guestModal">
                        <i class="fas fa-plus"></i>
                        {{ __('dashboard.new_guest') }}
                    </button>
                @endcan

            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('dashboard.guest.index') }}">
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
                                <a href="{{ route('dashboard.guest.index') }}" class="btn btn-outline-secondary">
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
                            <th>{{ __('dashboard.image') }}</th>
                            <th>{{ __('dashboard.name') }}</th>
                            <th>{{ __('dashboard.status') }}</th>
                            <th>{{ __('dashboard.guest_class') }}</th>
                            <th>{{ __('dashboard.mobile_number') }}</th>
                            <th>{{ __('dashboard.email') }}</th>
                            <th>{{ __('dashboard.nationality') }}</th>
                            <th>{{ __('dashboard.id_type') }}</th>
                            <th>{{ __('dashboard.id_number') }}</th>
                            <th class="text-center">{{ __('dashboard.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($guests as $guest)
                            <tr>
                                <td>
                                    @if($guest->profile_image)
                                        <img src="{{ asset('storage/' . $guest->profile_image) }}" alt="{{ $guest->full_name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $guest->full_name }}</td>
                                <td>
                                    @if ($guest->is_active)
                                        <span class="badge bg-success">{{ __('dashboard.active') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('dashboard.inactive') }}</span>
                                    @endif
                                </td>
                                <td>{{ $guest->guestClass->class_name ?? '-' }}</td>
                                <td>{{ $guest->mobile }}</td>
                                <td>{{ $guest->email ?? '-' }}</td>
                                <td>{{ $guest->nationality ?? '-' }}</td>
                                <td>{{ $guest->id_type ? __("dashboard.{$guest->id_type}") : '-' }}</td>
                                <td>{{ $guest->id_number ?? '-' }}</td>
                                <td class="text-center">
                                    @can('guest.view')
                                        <button class="btn btn-sm btn-info me-1" onclick="viewGuest({{ $guest->id }})"
                                            title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    @endcan
                                    @can('guest.edit')
                                        <button class="btn btn-sm btn-primary me-1" onclick="editGuest({{ $guest->id }})"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endcan
                                    @can('guest.delete')
                                        <button class="btn btn-sm btn-danger" onclick="deleteGuest({{ $guest->id }})"
                                            title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endcan

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">{{ __('dashboard.no_data_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($guests->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $guests->links() }}
                    </div>
                @endif
            </div>
        </div>

    </main>

    <!-- Guest Modal -->
    <div class="modal fade" id="guestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="guestModalTitle">{{ __('dashboard.add_new_guest') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="guestForm">
                    @csrf
                    <input type="hidden" name="id" id="guestId">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="section-title">{{ __('dashboard.guest_information') }}</h6>
                            </div>
                        </div>
                        <div class="row g-3">

                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.first_name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="first_name" id="firstName" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.second_name') }}</label>
                                <input type="text" class="form-control" name="second_name" id="secondName">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.middle_name') }}</label>
                                <input type="text" class="form-control" name="middle_name" id="middleName">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.last_name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="last_name" id="lastName" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.gender') }}</label>
                                <select class="form-select" name="gender" id="gender">
                                    <option value="">{{ __('dashboard.select_gender') }}</option>
                                    <option value="male">{{ __('dashboard.male') }}</option>
                                    <option value="female">{{ __('dashboard.female') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.date_of_birth') }}</label>
                                <input type="date" class="form-control" name="date_of_birth" id="dob">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.guest_class') }}</label>
                                <select class="form-select" name="guest_class_id" id="guestClass">
                                    <option value="">{{ __('dashboard.select_guest_class') }}</option>
                                    @foreach ($guestClasses as $class)
                                        <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.car_license_plate') }}</label>
                                <input type="text" class="form-control" name="car_license_plate" id="carPlate">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <h6 class="section-title">{{ __('dashboard.verification_information') }}</h6>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.nationality') }}</label>
                                <select class="form-select" name="nationality" id="nationality">
                                    <option value="">{{ __('dashboard.select_nationality') }}</option>
                                    <option value="SA">Saudi Arabia</option>
                                    <option value="KW">Kuwait</option>
                                    <option value="AE">United Arab Emirates</option>
                                    <option value="BH">Bahrain</option>
                                    <option value="OM">Oman</option>
                                    <option value="QA">Qatar</option>
                                    <option value="EG">Egypt</option>
                                    <option value="IN">India</option>
                                    <option value="PK">Pakistan</option>
                                    <option value="OTHER">Other</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.nationality_code') }}</label>
                                <input type="text" class="form-control" name="nationality_code" id="nationalityCode"
                                    placeholder="{{ __('dashboard.type_nationality_code') }}" maxlength="3">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.guest_type') }}</label>
                                <select class="form-select" name="guest_type" id="guestType">
                                    <option value="">{{ __('dashboard.select_guest_type') }}</option>
                                    <option value="individual">{{ __('dashboard.individual') }}</option>
                                    <option value="family">{{ __('dashboard.family') }}</option>
                                    <option value="corporate">{{ __('dashboard.corporate') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.id_type') }}</label>
                                <select class="form-select" name="id_type" id="idType">
                                    <option value="">{{ __('dashboard.select_id_type') }}</option>
                                    <option value="national_id">{{ __('dashboard.national_id') }}</option>
                                    <option value="passport">{{ __('dashboard.passport') }}</option>
                                    <option value="iqama">{{ __('dashboard.iqama') }}</option>
                                    <option value="driver_license">{{ __('dashboard.driver_license') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.id_number') }}</label>
                                <input type="text" class="form-control" name="id_number" id="idNumber">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.id_serial') }}</label>
                                <select class="form-select" name="id_serial" id="idSerial">
                                    <option value="">{{ __('dashboard.select_id_serial') }}</option>
                                    <option value="first">{{ __('dashboard.first') }}</option>
                                    <option value="second">{{ __('dashboard.second') }}</option>
                                    <option value="third">{{ __('dashboard.third') }}</option>
                                    <option value="last">{{ __('dashboard.last') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.id_issue_country') }}</label>
                                <input type="text" class="form-control" name="id_issue_country" id="idIssueCountry">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.id_expiry_date') }}</label>
                                <input type="date" class="form-control" name="id_expiry_date" id="idExpiryDate">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.visa_number') }}</label>
                                <input type="text" class="form-control" name="visa_number" id="visaNumber">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.arrival_from') }}</label>
                                <input type="text" class="form-control" name="arrival_from" id="arrivalFrom">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.profile_image') }}</label>
                                <div class="text-center">
                                    <img id="imagePreview" src="" alt="Image Preview" class="rounded" style="max-width: 150px; max-height: 150px; display: none; margin-bottom: 10px;">
                                    <input type="file" class="form-control" name="profile_image" id="profileImage" accept="image/*" onchange="previewImage(event)">
                                    <small class="text-muted d-block mt-2">Max: 5MB</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <h6 class="section-title">{{ __('dashboard.contact_information') }}</h6>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.mobile_number') }} <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select class="form-select" name="mobile_dial_code" id="dialCode"
                                        style="max-width: 100px;">
                                        <option value="+966">+966</option>
                                        <option value="+965">+965</option>
                                        <option value="+971">+971</option>
                                        <option value="+973">+973</option>
                                        <option value="+968">+968</option>
                                        <option value="+974">+974</option>
                                        <option value="+20">+20</option>
                                        <option value="+91">+91</option>
                                        <option value="+92">+92</option>
                                    </select>
                                    <input type="tel" class="form-control" name="mobile_number" id="mobileNumber"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.email') }}</label>
                                <input type="email" class="form-control" name="email" id="email">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.work_place') }}</label>
                                <input type="text" class="form-control" name="work_place" id="workPlace">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">{{ __('dashboard.work_phone') }}</label>
                                <input type="tel" class="form-control" name="work_phone" id="workPhone">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">{{ __('dashboard.address') }}</label>
                                <input type="text" class="form-control" name="address" id="address">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                        <button type="submit" class="btn btn-primary"
                            id="saveGuestBtn">{{ __('dashboard.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteGuestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('dashboard.delete_guest') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('dashboard.delete_guest_confirmation') }}</p>
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
        // Show notification function
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

        // Open modal for new guest
        document.getElementById('guestModal').addEventListener('show.bs.modal', function(e) {
            if (!e.relatedTarget) return;
            const button = e.relatedTarget;
            if (button.getAttribute('data-bs-target') === '#guestModal') {
                resetGuestForm();
            }
        });

        function resetGuestForm() {
            document.getElementById('guestModalTitle').textContent = '{{ __('dashboard.add_new_guest') }}';
            document.getElementById('guestForm').reset();
            document.getElementById('guestId').value = '';
            document.getElementById('saveGuestBtn').textContent = '{{ __('dashboard.save') }}';
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('profileImage').value = '';
        }

        // Save guest (create or update)
        document.getElementById('guestForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const guestId = document.getElementById('guestId').value;
            const url = guestId ? '{{ route('dashboard.guest.update', ':id') }}'.replace(':id', guestId) :
                '{{ route('dashboard.guest.store') }}';
            const method = guestId ? 'PUT' : 'POST';

            const formData = new FormData(this);
            formData.append('_token', '{{ csrf_token() }}');

            fetch(url, {
                    method: method,
                    headers: {
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(guestId ? '{{ __('messages.guest_updated_successfully') }}' :
                            '{{ __('messages.guest_created_successfully') }}');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('guestModal'));
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

        // View guest
        function viewGuest(id) {
            fetch('{{ route('dashboard.guest.show', ':id') }}'.replace(':id', id))
                .then(response => response.json())
                .then(guest => {
                    fillGuestForm(guest);
                    document.getElementById('guestModalTitle').textContent = '{{ __('dashboard.view_guest') }}';
                    document.getElementById('saveGuestBtn').style.display = 'none';
                    document.querySelectorAll('#guestForm input, #guestForm select').forEach(el => el.disabled = true);
                    new bootstrap.Modal(document.getElementById('guestModal')).show();
                });
        }

        // Edit guest
        function editGuest(id) {
            fetch('{{ route('dashboard.guest.show', ':id') }}'.replace(':id', id))
                .then(response => response.json())
                .then(guest => {
                    fillGuestForm(guest);
                    document.getElementById('guestModalTitle').textContent = '{{ __('dashboard.edit_guest') }}';
                    document.getElementById('saveGuestBtn').style.display = 'block';
                    document.getElementById('saveGuestBtn').textContent = '{{ __('dashboard.update') }}';
                    document.querySelectorAll('#guestForm input, #guestForm select').forEach(el => el.disabled = false);
                    new bootstrap.Modal(document.getElementById('guestModal')).show();
                });
        }

        function fillGuestForm(guest) {
            document.getElementById('guestId').value = guest.id;
            document.getElementById('firstName').value = guest.first_name || '';
            document.getElementById('secondName').value = guest.second_name || '';
            document.getElementById('middleName').value = guest.middle_name || '';
            document.getElementById('lastName').value = guest.last_name || '';
            document.getElementById('gender').value = guest.gender || '';
            document.getElementById('dob').value = guest.date_of_birth || '';
            document.getElementById('guestClass').value = guest.guest_class_id || '';
            document.getElementById('carPlate').value = guest.car_license_plate || '';
            document.getElementById('nationality').value = guest.nationality || '';
            document.getElementById('nationalityCode').value = guest.nationality_code || '';
            document.getElementById('guestType').value = guest.guest_type || '';
            document.getElementById('idType').value = guest.id_type || '';
            document.getElementById('idNumber').value = guest.id_number || '';
            document.getElementById('idSerial').value = guest.id_serial || '';
            document.getElementById('idIssueCountry').value = guest.id_issue_country || '';
            document.getElementById('idExpiryDate').value = guest.id_expiry_date || '';
            document.getElementById('visaNumber').value = guest.visa_number || '';
            document.getElementById('arrivalFrom').value = guest.arrival_from || '';
            document.getElementById('dialCode').value = guest.mobile_dial_code || '+966';
            document.getElementById('mobileNumber').value = guest.mobile_number || '';
            document.getElementById('email').value = guest.email || '';
            document.getElementById('workPlace').value = guest.work_place || '';
            document.getElementById('workPhone').value = guest.work_phone || '';
            document.getElementById('address').value = guest.address || '';

            // Show existing image if present
            if (guest.profile_image) {
                const imagePreview = document.getElementById('imagePreview');
                imagePreview.src = '{{ url('') }}/storage/' + guest.profile_image;
                imagePreview.style.display = 'block';
            } else {
                document.getElementById('imagePreview').style.display = 'none';
            }
            document.getElementById('profileImage').value = '';
        }

        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imagePreview = document.getElementById('imagePreview');
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }

        // Delete guest
        let deleteGuestId = null;

        function deleteGuest(id) {
            deleteGuestId = id;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteGuestModal'));
            deleteModal.show();
        }

        // Confirm delete
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (deleteGuestId) {
                fetch('{{ route('dashboard.guest.destroy', ':id') }}'.replace(':id', deleteGuestId), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        const deleteModal = bootstrap.Modal.getInstance(document.getElementById(
                            'deleteGuestModal'));
                        deleteModal.hide();
                        if (data.success) {
                            showNotification('{{ __('messages.guest_deleted_successfully') }}');
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

        // Reset form when modal is closed
        document.getElementById('guestModal').addEventListener('hidden.bs.modal', function() {
            resetGuestForm();
            document.querySelectorAll('#guestForm input, #guestForm select').forEach(el => el.disabled = false);
        });
    </script>
@endpush
