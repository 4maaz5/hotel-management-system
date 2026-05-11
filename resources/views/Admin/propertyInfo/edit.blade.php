@extends('layouts.app')

@section('title', 'Edit Property Information')

<style>
    /* Form Panel Styles */
    .form-panel {
        background: white;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .form-panel-header {
        padding: 1rem 1.5rem;
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .form-panel-title {
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .form-panel-icon {
        color: #4a90e2;
        font-size: 1.1rem;
    }

    .form-panel-content {
        padding: 1.5rem;
    }

    .form-panel-collapse-icon {
        transition: transform 0.3s ease;
    }

    .form-panel-collapsed .form-panel-collapse-icon {
        transform: rotate(-90deg);
    }

    /* Form Controls */
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-label-star {
        color: #dc3545;
    }

    .form-label-icon {
        color: #6c757d;
        margin-left: 0.25rem;
        cursor: help;
    }

    .form-control--light {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
        width: 100%;
        transition: border-color 0.15s ease-in-out;
    }

    .form-control--light:focus {
        border-color: #4a90e2;
        box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
        outline: none;
    }

    /* File Upload */
    .custom-file-upload {
        border: 2px dashed #dee2e6;
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background-color: #f8f9fa;
    }

    .custom-file-upload:hover {
        border-color: #4a90e2;
        background-color: #f0f7ff;
    }

    .custom-file-upload svg {
        color: #6c757d;
        margin-bottom: 0.75rem;
    }

    .custom-file-upload__info {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.5rem;
    }

    /* Multi-language Textarea */
    .multi-lang-ta {
        position: relative;
    }

    .multi-lang-ta textarea {
        min-height: 120px;
        resize: vertical;
    }

    .arabic-control {
        direction: rtl;
        text-align: right;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .lang-toggle-btn {
        position: absolute;
        right: 0.75rem;
        top: 0.75rem;
        background: #4a90e2;
        color: white;
        border: none;
        padding: 0.25rem 0.75rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .lang-toggle-btn:hover {
        background: #3a80d2;
    }

    /* Photo Upload Container */
    .photo-container {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        min-height: 150px;
    }

    .photo-upload {
        width: 150px;
        height: 150px;
        border: 2px dashed #dee2e6;
        border-radius: 0.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background-color: #f8f9fa;
    }

    .photo-upload:hover {
        border-color: #4a90e2;
        background-color: #f0f7ff;
    }

    .photo-upload i {
        font-size: 2rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .photo-upload p {
        font-size: 0.875rem;
        color: #6c757d;
        text-align: center;
        margin: 0;
        line-height: 1.4;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e9ecef;
        margin-top: 2rem;
    }

    .n-button {
        padding: 0.5rem 1.5rem;
        border-radius: 0.375rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }

    .n-button--primary {
        background-color: #4a90e2;
        color: white;
        border-color: #4a90e2;
    }

    .n-button--primary:hover {
        background-color: #3a80d2;
        border-color: #3a80d2;
    }

    .n-button--danger-border {
        background-color: transparent;
        color: #dc3545;
        border-color: #dc3545;
    }

    .n-button--danger-border:hover {
        background-color: #dc3545;
        color: white;
    }

    /* Combobox Styling */
    .k-combobox {
        width: 100%;
    }

    .k-input {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
        width: 100%;
    }

    /* Datepicker Styling */
    .k-datepicker {
        width: 100%;
    }

    /* Responsive Grid */
    .row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -0.75rem;
    }

    .col-md-3 {
        flex: 0 0 25%;
        max-width: 25%;
        padding: 0 0.75rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 992px) {
        .col-md-3 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }

    @media (max-width: 768px) {
        .col-md-3 {
            flex: 0 0 100%;
            max-width: 100%;
        }
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

    /* Form Validation Messages */
    .form__input-msg {
        font-size: 0.875rem;
        margin-top: 0.25rem;
        min-height: 1.25rem;
    }

    .form__input-msg .text-danger {
        color: #dc3545;
    }

    .form__input-msg .text-success {
        color: #28a745;
    }

    /* Required Field Indicator */
    .required-field::after {
        content: " *";
        color: #dc3545;
    }
</style>

@section('content')
    <div class="container-fluid py-4 bg-white p-2" style="border-radius:10px;">
        <!-- Page Header -->
        <div class="page-category">{{ __('dashboard.company') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ $property->property_name_en }}</h2>
                <div class="page-header__subtitle">{{ __('dashboard.manage_tourism_license') }}</div>
            </div>
        </div>

        <!-- Form -->
        <div id="form">
            <form method="post" action="{{ route('setup-sidebar.property-info.save') }}" novalidate
                enctype="multipart/form-data">
                @csrf
                <!-- Tourism License Details Panel -->
                <div class="form-panel">
                    <div class="form-panel-header" data-toggle="collapse" data-target="#tourism-license">
                        <div class="form-panel-title">
                            <i class="fas fa-file-alt form-panel-icon"></i>
                            {{ __('dashboard.tourism_license_details') }}
                        </div>
                        <i class="fas fa-chevron-up form-panel-collapse-icon"></i>
                    </div>
                    <div class="form-panel-content collapse show" id="tourism-license">
                        <div class="row">
                            <!-- Tourism Activity Type -->
                            <div class="col-md-3">
                                <label class="form-label required-field">{{ __('dashboard.tourism_activity_type') }}</label>
                                <div class="position-relative">
                                    <select class="form-control form-control--light" name="unitClass" required
                                        value="hotel"
                                        {{ old('unitClass', $property->tourismLicense?->unit_class) == 'hotel' ? 'selected' : '' }}>
                                        <option value="hotel">{{ __('dashboard.hotel') }}</option>
                                        <option value="serviced_apartment">{{ __('dashboard.serviced_apartments') }}
                                        </option>
                                        <option value="apartment_hotel">{{ __('dashboard.apartment_hotel') }}</option>
                                        <option value="resort">{{ __('dashboard.resort') }}</option>
                                        <option value="hotel_villa">{{ __('dashboard.hotel_villa') }}</option>
                                        <option value="hostel">{{ __('dashboard.hostel') }}</option>
                                        <option value="heritage_hotel">{{ __('dashboard.heritage_hotel') }}</option>
                                        <option value="camp">{{ __('dashboard.camp') }}</option>
                                        <option value="holiday_house">{{ __('dashboard.holiday_house') }}</option>
                                        <option value="pop_up_accommodation">{{ __('dashboard.pop_up_accomodation') }}
                                        </option>
                                    </select>
                                </div>
                                <div class="form__input-msg"></div>
                            </div>

                            <!-- Tourism License No. -->
                            <div class="col-md-3">
                                <label class="form-label">{{ __('dashboard.tourism_license_no') }}</label>
                                <div class="position-relative">
                                    <input type="text" name="Tourismlicensenumber" id="Tourismlicensenumber"
                                        class="form-control form-control--light" maxlength="15"
                                        placeholder="{{ __('dashboard.enter_license_number') }}" required
                                        value="{{ old('Tourismlicensenumber', $property->tourismLicense?->license_number ?? '') }}">
                                </div>
                                @error('Tourismlicensenumber')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <div class="form__input-msg"></div>
                            </div>

                            <!-- Tourism License Exp. Date -->
                            <div class="col-md-3">
                                <label class="form-label">
                                    {{ __('dashboard.tourism_license_exp_date') }}
                                    <i class="fas fa-info-circle form-label-icon"
                                        title="Expiration date of your tourism license"></i>
                                </label>
                                <div class="position-relative">
                                    <input type="date" name="tourismLicenseExpDate"
                                        class="form-control form-control--light"
                                        placeholder="{{ __('dashboard.select_expiration_date') }}"
                                        value="{{ old('tourismLicenseExpDate', optional($property->tourismLicense?->license_expiry_date)->format('Y-m-d')) }}">
                                </div>
                                @error('tourismLicenseExpDate')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <div class="form__input-msg"></div>
                            </div>

                            <!-- Number of rooms -->
                            <div class="col-md-3">
                                <label class="form-label">{{ __('dashboard.number_of_rooms') }}</label>
                                <div class="position-relative">
                                    <input type="number" name="NoOfRooms" id="noOfRooms"
                                        class="form-control form-control--light"
                                        placeholder="{{ __('dashboard.enter_number_of_rooms') }}" min="0"
                                        value="{{ old('NoOfRooms', $property->tourismLicense?->number_of_rooms ?? '') }}">
                                </div>
                                <div class="form__input-msg"></div>
                            </div>

                            <!-- Number of beds -->
                            <div class="col-md-3">
                                <label class="form-label">{{ __('dashboard.number_of_beds') }}</label>
                                <div class="position-relative">
                                    <input type="number" name="NoOfBeds" id="noOfBeds"
                                        class="form-control form-control--light"
                                        placeholder="{{ __('dashboard.enter_number_of_beds') }}" min="0"
                                        value="{{ old('NoOfBeds', $property->tourismLicense?->number_of_beds ?? '') }}">
                                </div>
                                <div class="form__input-msg"></div>
                            </div>

                            <!-- Tourism License File -->
                            <div class="col-md-3">
                                <label class="form-label">{{ __('dashboard.tourism_license_file') }}</label>
                                <div class="position-relative">
                                    <label for="file-upload" class="custom-file-upload">
                                        <i class="fas fa-upload fa-2x mb-2"></i>
                                        <div>{{ __('dashboard.upload_document') }}</div>
                                    </label>
                                    <div class="custom-file-upload__info">
                                        PNG, TIFF {{ __('dashboard.files_are_supported') }}
                                    </div>
                                    <input type="file" id="file-upload" accept=".pdf,.tiff" name="file-upload"
                                        style="display: none;">
                                </div>
                                @error('file-upload')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <div class="form__input-msg"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Commercial Details Panel -->
                <div class="form-panel">
                    <div class="form-panel-header" data-toggle="collapse" data-target="#commercial-details">
                        <div class="form-panel-title">
                            <i class="fas fa-briefcase form-panel-icon"></i>
                            {{ __('dashboard.commercial_details') }}
                        </div>
                        <i class="fas fa-chevron-up form-panel-collapse-icon"></i>
                    </div>
                    <div class="form-panel-content collapse show" id="commercial-details">
                        <div class="row">
                            <!-- Comm. Reg. No. -->
                            <div class="col-md-3">
                                <div class="col-md-12 p-0">
                                    <label class="form-label required-field">
                                        {{ __('dashboard.com_reg_no') }}
                                        <i class="fas fa-info-circle form-label-icon"
                                            title="Commercial registration number"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" name="CommercialRegistrationNumber"
                                            id="CommercialRegistrationNumber" class="form-control form-control--light"
                                            placeholder="{{ __('dashboard.enter_registration_number') }}" maxlength="50"
                                            required
                                            value="{{ old('CommercialRegistrationNumber', $property->commercialDetail?->registration_number ?? '') }}">
                                    </div>
                                    @error('CommercialRegistrationNumber')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <div class="form__input-msg"></div>
                                </div>
                            </div>

                            <!-- Comm. Activity License No. -->
                            <div class="col-md-3">
                                <label class="form-label">
                                    {{ __('dashboard.com_activity_license_number') }}
                                    <i class="fas fa-info-circle form-label-icon"
                                        title="Commercial activity license number"></i>
                                </label>
                                <div class="position-relative">
                                    <input type="number" name="CommActivityLicenseNo" id="CommActivityLicenseNo"
                                        class="form-control form-control--light"
                                        placeholder="{{ __('dashboard.enter_license_number') }}" maxlength="15"
                                        value="{{ old('CommActivityLicenseNo', $property->commercialDetail?->activity_license_number ?? '') }}">
                                </div>
                                <div class="form__input-msg"></div>
                            </div>

                            <!-- VAT Reg. No. -->
                            <div class="col-md-3">
                                <div class="col-md-12 p-0">
                                    <label class="form-label required-field">
                                        {{ __('dashboard.vat_reg_no') }}
                                        <i class="fas fa-info-circle form-label-icon" title="VAT registration number"></i>
                                    </label>
                                    <div class="position-relative">
                                        <input type="number" name="taxRegistrationNo" id="TaxRegistrationNo"
                                            class="form-control form-control--light"
                                            placeholder="{{ __('dashboard.enter_vat_number') }}" maxlength="20" required
                                            value="{{ old('taxRegistrationNo', $property->commercialDetail?->vat_registration_number ?? '') }}">
                                    </div>
                                    @error('taxRegistrationNo')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <div class="form__input-msg"></div>
                                </div>
                            </div>

                            <!-- Comm. Reg. File -->
                            <div class="col-md-3">
                                <label class="form-label">
                                    {{ __('dashboard.comm_reg_file') }}
                                    <i class="fas fa-info-circle form-label-icon"
                                        title="Upload commercial registration document"></i>
                                </label>
                                <div class="position-relative">
                                    <label for="file-upload-2" class="custom-file-upload">
                                        <i class="fas fa-upload fa-2x mb-2"></i>
                                        <div>{{ __('dashboard.upload_document') }}</div>
                                    </label>
                                    <div class="custom-file-upload__info">
                                        PNG, TIFF {{ __('dashboard.files_are_supported') }}
                                    </div>
                                    <input type="file" id="file-upload-2" accept=".pdf,.tiff" name="file-upload-2"
                                        style="display: none;">
                                </div>
                                @error('file-upload-2')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <div class="form__input-msg"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Property Details Panel -->
                <div class="form-panel">
                    <div class="form-panel-header" data-toggle="collapse" data-target="#property-details">
                        <div class="form-panel-title">
                            <i class="fas fa-home form-panel-icon"></i>
                            {{ __('dashboard.property_details') }}
                        </div>
                        <i class="fas fa-chevron-up form-panel-collapse-icon"></i>
                    </div>
                    <div class="form-panel-content collapse show" id="property-details">
                        <div class="row">
                            <!-- Distance from Haram -->
                            <div class="col-md-3">
                                <label class="form-label">{{ __('dashboard.distance_from_haram') }} (km)</label>
                                <div class="position-relative">
                                    <input type="number" name="distancefromHaram" id="distancefromHaram"
                                        class="form-control form-control--light"
                                        placeholder="{{ __('dashboard.enter_distance_in_km') }}" maxlength="50"
                                        step="0.1" min="0"
                                        value="{{ old('distancefromHaram', $property->additionalDetail?->distance_from_haram_km ?? '') }}">
                                </div>
                                <div class="form__input-msg"></div>
                            </div>

                            <!-- Property Description (Multi-language) -->
                            <div class="col-12">
                                <div class="multi-lang-ta">
                                    <label class="form-label"
                                        for="DescriptionAr">{{ __('dashboard.property_description') }}</label>
                                    <div class="dropdown">
                                        <div>
                                            <div class="position-relative">
                                                <textarea name="description" class="form-control form-control--light max-height-250" rows="5"
                                                    placeholder="{{ __('dashboard.description') }}">{{ $property->additionalDetail?->description_en ?? '' }}</textarea>

                                            </div>
                                            <div class="form__input-msg"></div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Property Photos Panel -->
                <div class="form-panel">
                    <div class="form-panel-header" data-toggle="collapse" data-target="#property-photos">
                        <div class="form-panel-title">
                            <i class="fas fa-camera form-panel-icon"></i>
                            {{ __('dashboard.property_photos') }}
                        </div>
                        <i class="fas fa-chevron-up form-panel-collapse-icon"></i>
                    </div>
                    <div class="form-panel-content collapse show" id="property-photos">
                        <div class="photo-container">
                            <!-- Photo Upload Area -->
                            <div class="photo-upload" id="photo-upload-trigger">
                                <i class="fas fa-plus-circle"></i>
                                <p>Max File Size:<br>750 KB.<br>(JPG, PNG recommended)</p>
                            </div>
                            <input type="file" name="photos[]" id="photo-input" multiple hidden>
                        </div>
                        @error('photos')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="n-button n-button--danger-border" id="discardBtn">
                        {{ __('dashboard.discard') }}
                    </button>
                    <button type="submit" class="n-button n-button--primary">
                        {{ __('dashboard.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Panel collapse functionality
            document.querySelectorAll('.form-panel-header').forEach(header => {
                header.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const target = document.querySelector(targetId);
                    const icon = this.querySelector('.form-panel-collapse-icon');

                    if (target.classList.contains('show')) {
                        target.classList.remove('show');
                        this.parentElement.classList.add('form-panel-collapsed');
                        icon.classList.remove('fa-chevron-up');
                        icon.classList.add('fa-chevron-down');
                    } else {
                        target.classList.add('show');
                        this.parentElement.classList.remove('form-panel-collapsed');
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-up');
                    }
                });
            });

            // File upload functionality
            document.getElementById('file-upload').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const maxSize = 10 * 1024 * 1024; // 10MB
                    const allowedTypes = ['application/pdf', 'image/tiff'];

                    if (!allowedTypes.includes(file.type)) {
                        alert('Please upload only PDF or TIFF files.');
                        this.value = '';
                        return;
                    }

                    if (file.size > maxSize) {
                        alert('File size must be less than 10MB.');
                        this.value = '';
                        return;
                    }

                    // Update UI to show file is selected
                    const uploadLabel = this.previousElementSibling.previousElementSibling;
                    uploadLabel.innerHTML = `<i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                                            <div>${file.name}</div>`;
                    uploadLabel.style.borderColor = '#28a745';
                    uploadLabel.style.backgroundColor = '#d4edda';
                }
            });

            document.getElementById('file-upload-2').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const maxSize = 10 * 1024 * 1024; // 10MB
                    const allowedTypes = ['application/pdf', 'image/tiff'];

                    if (!allowedTypes.includes(file.type)) {
                        alert('Please upload only PDF or TIFF files.');
                        this.value = '';
                        return;
                    }

                    if (file.size > maxSize) {
                        alert('File size must be less than 10MB.');
                        this.value = '';
                        return;
                    }

                    // Update UI to show file is selected
                    const uploadLabel = this.previousElementSibling.previousElementSibling;
                    uploadLabel.innerHTML = `<i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                                            <div>${file.name}</div>`;
                    uploadLabel.style.borderColor = '#28a745';
                    uploadLabel.style.backgroundColor = '#d4edda';
                }
            });

            // Photo upload functionality
            document.getElementById('photo-upload-trigger').addEventListener('click', function() {
                document.getElementById('photo-input').click();
            });

            document.getElementById('photo-input').addEventListener('change', function(e) {
                const files = e.target.files;
                const maxSize = 750 * 1024; // 750KB
                const allowedTypes = ['image/jpeg', 'image/png'];
                const container = document.querySelector('.photo-container');

                Array.from(files).forEach((file, index) => {
                    if (!allowedTypes.includes(file.type)) {
                        alert(`File ${file.name} is not a JPG or PNG image.`);
                        return;
                    }

                    if (file.size > maxSize) {
                        alert(`File ${file.name} exceeds 750KB limit.`);
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const photoItem = document.createElement('div');
                        photoItem.className = 'photo-item position-relative';
                        photoItem.style.width = '150px';
                        photoItem.style.height = '150px';
                        photoItem.style.borderRadius = '0.5rem';
                        photoItem.style.overflow = 'hidden';

                        const img = document.createElement('img');
                        img.src = event.target.result;
                        img.style.width = '100%';
                        img.style.height = '100%';
                        img.style.objectFit = 'cover';

                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'photo-remove-btn position-absolute';
                        removeBtn.style.top = '5px';
                        removeBtn.style.right = '5px';
                        removeBtn.style.background = 'rgba(220, 53, 69, 0.8)';
                        removeBtn.style.color = 'white';
                        removeBtn.style.border = 'none';
                        removeBtn.style.borderRadius = '50%';
                        removeBtn.style.width = '24px';
                        removeBtn.style.height = '24px';
                        removeBtn.style.display = 'flex';
                        removeBtn.style.alignItems = 'center';
                        removeBtn.style.justifyContent = 'center';
                        removeBtn.style.cursor = 'pointer';
                        removeBtn.innerHTML = '<i class="fas fa-times"></i>';

                        removeBtn.addEventListener('click', function() {
                            photoItem.remove();
                            resetPhotoInput(); // important
                        });

                        photoItem.appendChild(img);
                        photoItem.appendChild(removeBtn);

                        container.insertBefore(photoItem, document.getElementById(
                            'photo-upload-trigger'));
                    };

                    reader.readAsDataURL(file);
                });
            });

            /**
             * Reset file input so user can reselect files after removal
             */
            function resetPhotoInput() {
                const input = document.getElementById('photo-input');
                input.value = '';
            }


            // Discard button functionality
            document.getElementById('discardBtn').addEventListener('click', function() {
                if (confirm('Are you sure you want to discard all changes?')) {
                    // Reset all form fields
                    document.querySelectorAll('input, select, textarea').forEach(field => {
                        if (field.type !== 'file') {
                            field.value = '';
                        }
                    });

                    // Reset file upload displays
                    document.querySelectorAll('.custom-file-upload').forEach(upload => {
                        upload.innerHTML =
                            `<i class="fas fa-upload fa-2x mb-2"></i><div>Upload Document</div>`;
                        upload.style.borderColor = '#dee2e6';
                        upload.style.backgroundColor = '#f8f9fa';
                    });

                    // Reset photo container
                    const photoContainer = document.querySelector('.photo-container');
                    const uploadTrigger = document.getElementById('photo-upload-trigger');
                    photoContainer.innerHTML = '';
                    photoContainer.appendChild(uploadTrigger);

                    alert('All changes have been discarded.');
                }
            });

            // Form submission
            document.querySelector('form').addEventListener('submit', function(e) {
                e.preventDefault();

                // Basic validation
                const requiredFields = this.querySelectorAll('[required]');
                let isValid = true;
                let firstInvalidField = null;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.style.borderColor = '#dc3545';

                        const msgDiv = field.closest('.col-md-3').querySelector('.form__input-msg');
                        if (msgDiv) {
                            msgDiv.innerHTML =
                                '<span class="text-danger">This field is required</span>';
                        }

                        if (!firstInvalidField) {
                            firstInvalidField = field;
                        }
                    } else {
                        field.style.borderColor = '#dee2e6';
                        const msgDiv = field.closest('.col-md-3').querySelector('.form__input-msg');
                        if (msgDiv) {
                            msgDiv.innerHTML = '';
                        }
                    }
                });

                if (!isValid) {
                    alert('Please fill in all required fields.');
                    if (firstInvalidField) {
                        firstInvalidField.focus();
                    }
                    return;
                }

                // Validate tourism license expiration date if provided
                const expDateField = document.querySelector('input[name="tourismLicenseExpDate"]');
                if (expDateField.value) {
                    const expDate = new Date(expDateField.value);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    if (expDate < today) {
                        alert('Tourism license expiration date cannot be in the past.');
                        expDateField.focus();
                        return;
                    }
                }

                // Validate numbers
                const roomsField = document.getElementById('noOfRooms');
                const bedsField = document.getElementById('noOfBeds');

                if (roomsField.value && parseInt(roomsField.value) < 0) {
                    alert('Number of rooms cannot be negative.');
                    roomsField.focus();
                    return;
                }

                if (bedsField.value && parseInt(bedsField.value) < 0) {
                    alert('Number of beds cannot be negative.');
                    bedsField.focus();
                    return;
                }


                // Validate distance
                const distanceField = document.getElementById('distancefromHaram');
                if (distanceField.value && parseFloat(distanceField.value) < 0) {
                    alert('Distance cannot be negative.');
                    distanceField.focus();
                    return;
                }

                const submitBtn = document.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
                submitBtn.disabled = true;

                this.submit();

            });

            // Real-time validation for numeric fields
            document.querySelectorAll('input[type="number"]').forEach(input => {
                input.addEventListener('input', function() {
                    if (this.value < 0) {
                        this.value = 0;
                    }

                    // Validate max length
                    const maxLength = this.getAttribute('maxlength');
                    if (maxLength && this.value.length > maxLength) {
                        this.value = this.value.slice(0, maxLength);
                    }
                });
            });

            // Info icon tooltips
            document.querySelectorAll('.form-label-icon').forEach(icon => {
                icon.addEventListener('mouseenter', function() {
                    const title = this.getAttribute('title');
                    if (title) {
                        // Create tooltip
                        const tooltip = document.createElement('div');
                        tooltip.className = 'tooltip-custom';
                        tooltip.textContent = title;
                        tooltip.style.position = 'absolute';
                        tooltip.style.background = '#333';
                        tooltip.style.color = 'white';
                        tooltip.style.padding = '5px 10px';
                        tooltip.style.borderRadius = '3px';
                        tooltip.style.fontSize = '0.875rem';
                        tooltip.style.zIndex = '1000';
                        tooltip.style.maxWidth = '200px';

                        document.body.appendChild(tooltip);

                        const rect = this.getBoundingClientRect();
                        tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
                        tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth /
                            2) + 'px';

                        this._tooltip = tooltip;
                    }
                });

                icon.addEventListener('mouseleave', function() {
                    if (this._tooltip) {
                        this._tooltip.remove();
                        this._tooltip = null;
                    }
                });
            });
        });
    </script>
@endpush
