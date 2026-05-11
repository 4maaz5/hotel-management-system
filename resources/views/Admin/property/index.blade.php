@extends('layouts.app')
@push('styles')
    <style>
        :root {
            --primary-blue: #1a73e8;
            --danger-color: #dc3545;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --light-bg: #f8f9fa;
            --border-color: #dee2e6;
            --text-dark: #212529;
            --text-light: #6c757d;
        }

        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .page-category {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            font-weight: 500;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-header__title {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .page-header__subtitle {
            color: var(--text-light);
            font-size: 0.95rem;
        }

        /* Filter Form */
        .filter-form {
            background-color: white;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .filter-form label {
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .filter-form .form-control,
        .filter-form .form-select {
            border-radius: 0.375rem;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            font-size: 0.9rem;
        }

        .filter-form .form-control:focus,
        .filter-form .form-select:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.25rem rgba(26, 115, 232, 0.25);
        }

        /* Table Styles */
        .properties-table {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 1.5rem;
        }

        .properties-table table {
            margin-bottom: 0;
        }

        .properties-table thead th {
            background-color: var(--light-bg);
            border-bottom: 2px solid var(--border-color);
            color: var(--text-dark);
            font-weight: 600;
            padding: 1rem 1.25rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .properties-table tbody td {
            padding: 1rem 1.25rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-dark);
        }

        .properties-table tbody tr:last-child td {
            border-bottom: none;
        }

        .properties-table tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 50rem;
            font-size: 0.8rem;
            font-weight: 600;
            text-align: center;
            min-width: 70px;
        }

        .status-active {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(40, 167, 69, 0.2);
        }

        .status-inactive {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
            border: 1px solid rgba(220, 53, 69, 0.2);
        }

        .status-pending {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning-color);
            border: 1px solid rgba(255, 193, 7, 0.2);
        }

        /* Action Buttons */
        .table-actions {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border-color);
            background-color: white;
            color: var(--primary-blue);
            transition: all 0.2s;
        }

        .action-btn:hover {
            background-color: var(--primary-blue);
            color: white;
            border-color: var(--primary-blue);
        }

        .action-btn.edit:hover {
            background-color: var(--primary-blue);
        }

        .action-btn.view:hover {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .action-btn.delete:hover {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }

        /* Pagination */
        .custom-pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .page-size-selector {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .page-size-selector select {
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            padding: 0.375rem 2rem 0.375rem 0.75rem;
            font-size: 0.9rem;
        }

        .pagination-info {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .pagination-controls .page-link {
            border: 1px solid var(--border-color);
            color: var(--primary-blue);
            padding: 0.5rem 0.75rem;
            margin: 0 0.25rem;
            border-radius: 0.375rem;
            font-size: 0.9rem;
        }

        .pagination-controls .page-link:hover {
            background-color: var(--light-bg);
        }

        .pagination-controls .disabled .page-link {
            color: var(--text-light);
            background-color: var(--light-bg);
            border-color: var(--border-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .properties-table {
                overflow-x: auto;
            }

            .properties-table table {
                min-width: 800px;
            }

            .custom-pagination {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }

            .page-size-selector {
                justify-content: center;
            }

            .pagination-controls {
                justify-content: center;
            }
        }

        .contact-info {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: white;
            border-radius: 0.5rem;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            min-width: 220px;
        }

        .contact-number,
        .contact-icon {
            cursor: pointer;
            transition: all 0.2s;
        }

        .contact-number:hover {
            color: var(--primary-blue);
        }

        .contact-icon:hover {
            background-color: var(--primary-blue);
            color: white;
        }



        .contact-number:hover {
            color: var(--primary-blue);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4 bg-white" style="border-radius:10px;">

        <!-- Page Header -->
        <div class="page-category">{{ __('dashboard.company') }}</div>
        <div class="page-header d-flex justify-content-between align-items-start">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.properties') }}</h2>
                <div class="page-header__subtitle">{{ __('dashboard.you_can_see_your_properties_here') }}</div>
            </div>
            <div>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="togglePropertyFilter()">
                    <i class="fas fa-filter me-1"></i>
                    {{ __('dashboard.filter') }}
                </button>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('setup-sidebar.property.index') }}" id="propertyFilterForm">
            <div class="filter-form" id="propertyFilterContent" style="display: none;">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-4">
                        <label for="name">{{ __('dashboard.name/code') }}</label>
                        <input type="text" id="name" name="name" class="form-control"
                            placeholder="{{ __('dashboard.search_by_name_or_code') }}">
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <label for="country">{{ __('dashboard.country') }}</label>
                        <select class="form-select" id="country" disabled name=country>
                            <option value="" selected disabled>{{ __('dashboard.select_country') }}</option>
                            <option value="sa" selected>Saudi Arabia</option>
                            <option value="ae">United Arab Emirates</option>
                            <option value="qa">Qatar</option>
                            <option value="om">Oman</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <label>{{ __('dashboard.status') }}</label>
                        <select class="form-select" name="status">
                            <option value="" selected disabled>{{ __('dashboard.select_status') }}</option>
                            <option value="active">{{ __('dashboard.active') }}</option>
                            <option value="inactive">{{ __('dashboard.inactive') }}</option>
                            {{-- <option value="pending">{{ __('dashboard.pending') }}</option> --}}
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <label>{{ __('dashboard.account_version') }}</label>
                        <select class="form-select" disabled name="account_version">
                            <option value="" selected disabled>{{ __('dashboard.account_version') }}</option>
                            <option value="basic" selected>{{ __('dashboard.basic') }}</option>
                            {{-- <option value="pro">Pro</option>
                            <option value="enterprise">Enterprise</option> --}}
                        </select>
                    </div>
                    <div class="col-12 text-end">
                        <button class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>
                            {{ __('dashboard.search') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Add Property Button Section -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="table-header">
                <h5 class="mb-0 fw-semibold">{{ __('dashboard.properties_list') }}</h5>
                <small class="text-muted">{{ __('dashboard.showing_properties') }}</small>
            </div>
            <div class="table-actions-top">

                @can('property.add')
                        <a href="{{ route('setup-sidebar.property.create') }}" type="button"
                            class="btn btn-primary d-flex align-items-center gap-2" id="addPropertyBtn">
                            <i class="fas fa-plus"></i>
                            {{ __('dashboard.add_property') }}
                        </a>
                @endcan
            </div>
        </div>
        <!-- Properties Table -->
        <div class="properties-table">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ __('dashboard.property') }}</th>
                        <th>{{ __('dashboard.code') }}</th>
                        <th>{{ __('dashboard.status') }}</th>
                        <th>{{ __('dashboard.account_version') }}</th>
                        <th>{{ __('dashboard.country') }}</th>
                        <th>{{ __('dashboard.city') }}</th>
                        <th>{{ __('dashboard.district') }}</th>
                        <th>{{ __('dashboard.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($properties as $property)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div
                                            style="width: 40px; height: 40px; background-color: #e9ecef; border-radius: 0.375rem; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-building text-primary"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $property->property_name_en }}</div>
                                        <small class="text-muted">{{ $property->email ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="fw-semibold">{{ $property->property_code }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower($property->status) }}">
                                    {{ ucfirst(strtolower($property->status)) }}
                                </span>
                            </td>
                            <td>{{ $property->account_version }}</td>
                            <td>{{ $property->country->name_en ?? 'N/A' }}</td>
                            <td>{{ $property->city->name_en ?? 'N/A' }}</td>
                            <td>{{ $property->district->name_en ?? 'N/A' }}</td>
                            <td>
                                <div class="table-actions">
                                    @can('property.edit')
                                        <a href="{{ route('setup-sidebar.property.edit', $property->id) }}"
                                            class="action-btn edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('property.view')
                                        <a href="{{ route('setup-sidebar.property.show', $property->id) }}"
                                            class="action-btn view" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endcan

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">{{ __('dashboard.no_properties_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>

@endsection

@push('scripts')
    <script>
        // Table actions functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Edit button functionality
            document.querySelectorAll('.action-btn.edit').forEach(button => {
                button.addEventListener('click', function() {
                    const propertyName = this.closest('tr').querySelector('.fw-medium').textContent;
                    alert(`Edit property: ${propertyName}`);
                });
            });

            // View button functionality
            document.querySelectorAll('.action-btn.view').forEach(button => {
                button.addEventListener('click', function() {
                    const propertyName = this.closest('tr').querySelector('.fw-medium').textContent;
                    alert(`View property details: ${propertyName}`);
                });
            });

            // Delete button functionality
            document.querySelectorAll('.action-btn.delete').forEach(button => {
                button.addEventListener('click', function() {
                    const propertyName = this.closest('tr').querySelector('.fw-medium').textContent;
                    if (confirm(`Are you sure you want to delete ${propertyName}?`)) {
                        this.closest('tr').style.opacity = '0.5';
                        setTimeout(() => {
                            alert(`${propertyName} deleted successfully!`);
                        }, 300);
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
                        alert(`Searching for: ${searchTerm}`);
                        // Implement search logic here
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
                var filterContent = document.getElementById('propertyFilterContent');
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
