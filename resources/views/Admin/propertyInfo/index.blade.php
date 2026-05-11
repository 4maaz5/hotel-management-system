@extends('layouts.app')

@section('title', 'Property Information')

@push('styles')
    <style>
        /* Progress Bar Styles */
        .progress-container {
            background-color: #e9ecef;
            border-radius: 0.375rem;
            height: 24px;
            overflow: hidden;
            position: relative;
        }

        .progress-fill {
            height: 100%;
            border-radius: 0.375rem;
            transition: width 0.6s ease;
            position: relative;
        }

        .progress-0 {
            background-color: #dc3545;
            width: 0%;
        }

        .progress-60 {
            background-color: #ffc107;
            width: 60%;
        }

        .progress-100 {
            background-color: #28a745;
            width: 100%;
        }

        .progress-text {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.3);
            white-space: nowrap;
        }

        /* Property Photos */
        .photos-count {
            background-color: var(--light-bg);
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            padding: 0.5rem 1rem;
            display: inline-block;
            font-weight: 500;
            color: var(--text-dark);
        }

        /* Compact Action Button */
        .compact-action-btn {
            width: 32px;
            height: 32px;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border-color);
            background-color: white;
            color: var(--primary-blue);
            transition: all 0.2s;
            font-size: 0.9rem;
        }

        .compact-action-btn:hover {
            background-color: var(--primary-blue);
            color: white;
            border-color: var(--primary-blue);
        }

        /* Table specific styles for this page */
        .property-info-table tbody td {
            padding: 0.875rem 1.25rem;
        }

        /* Column widths */
        .property-info-table th:nth-child(1),
        .property-info-table td:nth-child(1) {
            width: 250px;
            min-width: 250px;
        }

        .property-info-table th:nth-child(2),
        .property-info-table td:nth-child(2) {
            width: 70px;
            min-width: 70px;
        }

        .property-info-table th:nth-child(3),
        .property-info-table td:nth-child(3) {
            width: 70px;
            min-width: 70px;
        }

        .property-info-table th:nth-child(4),
        .property-info-table td:nth-child(4) {
            width: 180px;
            min-width: 180px;
        }

        .property-info-table th:nth-child(5),
        .property-info-table td:nth-child(5) {
            width: 180px;
            min-width: 180px;
        }

        .property-info-table th:nth-child(6),
        .property-info-table td:nth-child(6) {
            width: 160px;
            min-width: 160px;
        }

        .property-info-table th:nth-child(7),
        .property-info-table td:nth-child(7) {
            width: 123px;
            min-width: 123px;
        }

        /* Filter Form Styles */
        .filter-form {
            background-color: var(--light-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .filter-form label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 0.375rem;
            display: block;
        }

        .filter-form .form-control,
        .filter-form .form-select {
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            padding: 0.5rem 0.875rem;
            font-size: 0.875rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .filter-form .form-control:focus,
        .filter-form .form-select:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
            outline: none;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4 bg-white" style="border-radius:10px;">

        <!-- Page Header -->
        <div class="page-category">{{ __('dashboard.company') }}</div>
        <div class="page-header d-flex justify-content-between align-items-start">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.property_info') }}</h2>
                <div class="page-header__subtitle">{{ __('dashboard.manage_tourism_license') }}</div>
            </div>
            <div>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="togglePropertyFilter()">
                    <i class="fas fa-filter me-1"></i>
                    {{ __('dashboard.filter') }}
                </button>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('setup-sidebar.property-info.index') }}" id="propertyInfoFilterForm">
            <div class="filter-form" id="propertyInfoFilterContent" style="display: none;">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-4">
                        <label for="search">{{ __('dashboard.name_code') }}</label>
                        <input type="text" name="search" id="search" class="form-control"
                            placeholder="{{ __('dashboard.search_by_name_or_code') }}" value="{{ request('search') }}">
                    </div>

                    <div class="col-lg-3 col-md-4">
                        <label>{{ __('dashboard.status') }}</label>
                        <select name="status" class="form-select">
                            <option value="">{{ __('dashboard.all_status') }}</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                {{ __('dashboard.active') }}</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                {{ __('dashboard.inactive') }}</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                {{ __('dashboard.pending') }}</option>
                        </select>
                    </div>

                    <div class="col-12 text-end">
                        <button class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> {{ __('dashboard.search') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>


        <!-- Add Property Button Section -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="table-header">
                <h5 class="mb-0 fw-semibold">{{ __('dashboard.property_information') }}</h5>
                {{-- <small class="text-muted">Showing 4 properties</small> --}}
            </div>
        </div>

        <!-- Property Information Table -->
        <div class="properties-table">
            <div class="table-responsive">
                <table class="table table-hover mb-0 property-info-table">
                    <thead>
                        <tr>
                            <th>{{ __('dashboard.property') }}</th>
                            <th>{{ __('dashboard.code') }}</th>
                            <th>{{ __('dashboard.status') }}</th>
                            <th>{{ __('dashboard.tourism_license_details') }}</th>
                            <th>{{ __('dashboard.commercial_details') }}</th>
                            <th>{{ __('dashboard.property_photos') }}</th>
                            @can('property_info.edit')
                                <th>{{ __('dashboard.actions') }}</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                      @forelse ($properties as $row)
    @php
        $tourism = $row['tourism_progress'];
        $commercial = $row['commercial_progress'];
        $photos = $row['photo_count'];
    @endphp

    <tr>
        <td>
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <div class="icon-box">
                        <i class="fas fa-building text-primary"></i>
                    </div>
                </div>
                <div>
                    <div class="fw-medium">{{ $row['property']->property_name_en }}</div>
                </div>
            </div>
        </td>

        <td class="fw-semibold">{{ $row['property']->property_code }}</td>

        <td>
            <span class="status-badge status-active">{{ __('dashboard.active') }}</span>
        </td>

        <!-- Tourism License -->
        <td>
            <div class="progress-container">
                <div class="progress-fill progress-{{ $tourism }}">
                    <span class="progress-text">{{ $tourism }}% {{ __('dashboard.complete') }}</span>
                </div>
            </div>
            <small class="text-muted d-block">
                {{ $tourism == 100 ? 'Completed' : 'In progress' }}
            </small>
        </td>

        <!-- Commercial -->
        <td>
            <div class="progress-container">
                <div class="progress-fill progress-{{ $commercial }}">
                    <span class="progress-text">{{ $commercial }}% {{ __('dashboard.complete') }}</span>
                </div>
            </div>
            <small class="text-muted d-block">
                {{ $commercial == 100 ? 'Completed' : 'In progress' }}
            </small>
        </td>

        <!-- Photos -->
        <td>
            <span class="photos-count">{{ $photos }} / 10</span>
            <small class="text-muted d-block">
                {{ $photos == 0 ? 'Upload property photos' : 'Photos uploaded' }}
            </small>
        </td>

        <td>
            <div class="table-actions">
                @can('property_info.edit')
                    <a href="{{ route('setup-sidebar.property-info.edit', $row['property']->id) }}"
                        class="compact-action-btn edit">
                        <i class="fas fa-edit"></i>
                    </a>
                @endcan
            </div>
        </td>
    </tr>

@empty

<tr>
    <td colspan="7" class="text-center py-4 text-muted">
        <i class="fas fa-folder-open me-2"></i>
        {{ __('dashboard.no_records_found') }}
    </td>
</tr>

@endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Property Information Page Specific Scripts
        document.addEventListener('DOMContentLoaded', function() {
            // Edit button functionality for property info
            document.querySelectorAll('.compact-action-btn.edit').forEach(button => {
                button.addEventListener('click', function() {
                    const propertyName = this.closest('tr').querySelector('.fw-medium').textContent;
                    const propertyCode = this.closest('tr').querySelector('.fw-semibold')
                        .textContent;

                    // Show edit modal or redirect to edit page
                    alert(
                        `Edit property information: ${propertyName} (${propertyCode})\n\nThis would open the property information edit form.`
                    );

                });
            });

            // Progress bar hover effects
            document.querySelectorAll('.progress-fill').forEach(progressBar => {
                progressBar.addEventListener('mouseenter', function() {
                    const percentage = this.style.width;
                    const parent = this.closest('td');
                    const infoText = parent.querySelector('.text-muted');

                    // Store original text
                    const originalText = infoText.textContent;
                    infoText.dataset.original = originalText;

                    // Show percentage on hover
                    if (percentage === '0%') {
                        infoText.textContent = 'Click to start tourism license process';
                    } else if (percentage === '100%') {
                        infoText.textContent = 'All requirements completed';
                    } else {
                        infoText.textContent = `${percentage} completed - Click to continue`;
                    }
                });

                progressBar.addEventListener('mouseleave', function() {
                    const parent = this.closest('td');
                    const infoText = parent.querySelector('.text-muted');

                    // Restore original text
                    if (infoText.dataset.original) {
                        infoText.textContent = infoText.dataset.original;
                    }
                });

                // Click to update progress (simulation)
                progressBar.addEventListener('click', function() {
                    const currentWidth = parseInt(this.style.width);
                    const propertyName = this.closest('tr').querySelector('.fw-medium').textContent;

                    if (currentWidth < 100) {
                        const newWidth = currentWidth + 20;
                        this.style.width = `${newWidth}%`;
                        this.querySelector('.progress-text').textContent = `${newWidth}% Complete`;

                        // Update progress bar color based on percentage
                        this.className = 'progress-fill';
                        if (newWidth === 100) {
                            this.classList.add('progress-100');
                            alert(`${propertyName}: Congratulations! All requirements completed.`);
                        } else if (newWidth >= 60) {
                            this.classList.add('progress-60');
                            alert(`${propertyName}: Progress updated to ${newWidth}%`);
                        }
                    } else {
                        alert(`${propertyName}: All requirements are already completed!`);
                    }
                });
            });

            // Photos count click to upload
            document.querySelectorAll('.photos-count').forEach(photoCount => {
                photoCount.addEventListener('click', function() {
                    const currentPhotos = this.textContent.split(' / ')[0];
                    const maxPhotos = this.textContent.split(' / ')[1];
                    const propertyName = this.closest('tr').querySelector('.fw-medium').textContent;

                    if (parseInt(currentPhotos) < parseInt(maxPhotos)) {
                        // Simulate photo upload
                        const newCount = parseInt(currentPhotos) + 1;
                        this.textContent = `${newCount} / ${maxPhotos}`;

                        // Update helper text
                        const remaining = parseInt(maxPhotos) - newCount;
                        const helperText = this.nextElementSibling;
                        if (remaining > 0) {
                            helperText.textContent = `${remaining} photos remaining`;
                        } else {
                            helperText.textContent = 'All photos uploaded';
                            this.style.backgroundColor = '#d4edda';
                            this.style.borderColor = '#c3e6cb';
                            this.style.color = '#155724';
                        }

                        alert(`${propertyName}: Photo ${newCount} uploaded successfully!`);
                    } else {
                        alert(`${propertyName}: Maximum photos (${maxPhotos}) already uploaded.`);
                    }
                });
            });

            // Search functionality
            const searchButton = document.querySelector('.btn-primary');
            const searchInput = document.getElementById('name');

            if (searchButton && searchInput) {
                searchButton.addEventListener('click', function() {
                    const searchTerm = searchInput.value.trim();
                    if (searchTerm) {
                        // Filter table rows
                        const rows = document.querySelectorAll('.property-info-table tbody tr');
                        let foundCount = 0;

                        rows.forEach(row => {
                            const propertyName = row.querySelector('.fw-medium').textContent
                                .toLowerCase();
                            const propertyCode = row.querySelector('.fw-semibold').textContent
                                .toLowerCase();

                            if (propertyName.includes(searchTerm.toLowerCase()) ||
                                propertyCode.includes(searchTerm.toLowerCase())) {
                                row.style.display = '';
                                foundCount++;
                            } else {
                                row.style.display = 'none';
                            }
                        });

                        // Update showing count
                        const showingText = document.querySelector('.table-header .text-muted');
                        if (showingText) {
                            showingText.textContent = `Showing ${foundCount} properties`;
                        }

                        // Update pagination info
                        const paginationInfo = document.querySelector('.pagination-info');
                        if (paginationInfo) {
                            paginationInfo.textContent = `1-${foundCount} of ${foundCount} properties`;
                        }
                    } else {
                        // Show all rows if search is empty
                        const rows = document.querySelectorAll('.property-info-table tbody tr');
                        rows.forEach(row => row.style.display = '');

                        const showingText = document.querySelector('.table-header .text-muted');
                        if (showingText) {
                            showingText.textContent = 'Showing 4 properties';
                        }

                        const paginationInfo = document.querySelector('.pagination-info');
                        if (paginationInfo) {
                            paginationInfo.textContent = '1-4 of 4 properties';
                        }
                    }
                });

                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        searchButton.click();
                    }
                });
            }

            // Filter form toggle
            window.togglePropertyFilter = function() {
                var filterContent = document.getElementById('propertyInfoFilterContent');
                if (filterContent) {
                    var currentDisplay = filterContent.style.display;
                    if (currentDisplay === 'none' || currentDisplay === '') {
                        filterContent.style.display = 'block';
                    } else {
                        filterContent.style.display = 'none';
                    }
                }
            }
        });
    </script>
@endpush
