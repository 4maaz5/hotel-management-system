@extends('layout.master')
@section('title', 'Dashboard | Branch')
@section('main')
    <!-- Main Content -->
    <div class="main-content">

        <h2 class="text-center all-branches-title">{{ __('dashboard.all_branches') }}</h2>
        <div class="row g-4" id="branchCardsContainer">
            @forelse ($branchesCards as $branch)
                <div class="col-xl-3 col-md-6 mb-4 branch-card">
                    <div class="card shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $branch->name }}</h5>
                            <p class="card-text flex-grow-1">
                                <strong>{{ __('dashboard.location') }}:</strong> {{ $branch->location }} <br>
                                <strong>{{ __('dashboard.manager') }}:</strong> {{ $branch->manager }} <br>
                                <strong>{{ __('dashboard.email') }}:</strong> <span
                                    class="branch-email">{{ $branch->email }}</span> <br>
                                <strong>{{ __('dashboard.phone') }}:</strong> {{ $branch->phone }} <br>
                                <strong>{{ __('dashboard.status') }}:</strong>
                                @if ($branch->status == 'Active')
                                    <span class="badge bg-success">{{ $branch->status }}</span>
                                @else
                                    <span class="badge bg-danger">{{ $branch->status }}</span>
                                @endif
                            </p>

                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center empty-state">
                    <i class="fas fa-inbox"></i>
                    {{-- <p>No branches found.</p> --}}
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $branchesCards->links('pagination::bootstrap-4') }}
        </div>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.branch') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addBranchModal">
                                    {{ __('dashboard.add_branch') }}
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <form id="branchFilterForm" class="mb-3">
                                        <div class="row g-2 align-items-end">

                                            <div class="col-md-3">
                                                <label class="form-label">{{ __('dashboard.branch_name') }}</label>
                                                <input type="text" name="name" id="filter_name" class="form-control"
                                                    placeholder="{{ __('dashboard.search_by_name') }}">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">{{ __('dashboard.location') }}</label>
                                                <input type="text" name="location" id="filter_location"
                                                    class="form-control"
                                                    placeholder="{{ __('dashboard.search_by_location') }}">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">{{ __('dashboard.manager') }}</label>
                                                <input type="text" name="manager" id="filter_manager"
                                                    class="form-control" placeholder="">
                                            </div>

                                            <div class="col-md-2">
                                                <label class="form-label">{{ __('dashboard.status') }}</label>
                                                <select name="status" id="filter_status" class="form-control">
                                                    <option value="">All/الجميع</option>
                                                    <option value="Active">Active/نشيط</option>
                                                    <option value="Inactive">Inactive/غير نشط</option>
                                                </select>
                                            </div>

                                            <div class="col-md-1 d-grid">
                                                <button type="submit" id="branchFilterBtn"
                                                    class="btn btn-primary">{{ __('dashboard.filter') }}</button>
                                            </div>

                                        </div>
                                    </form>

                                    <table class="table table-bordered" id="tableExport">
                                        <thead>
                                            <tr>
                                                <th>{{ __('dashboard.branch_name') }}</th>
                                                <th>{{ __('dashboard.location') }}</th>
                                                <th>{{ __('dashboard.manager') }}</th>
                                                <th>{{ __('dashboard.email') }}</th>
                                                <th>{{ __('dashboard.phone') }}</th>
                                                <th>{{ __('dashboard.status') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @include('Admin.Backend.partials.branches_rows')
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- Add Branch Modal -->
        <div class="modal fade" id="addBranchModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('dashboard.add_new_branch') }}</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form id="addBranchForm" method="POST" action="{{ route('dashboard.branch.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>{{ __('dashboard.branch_name') }}</label>
                                    <input type="text" name="branch_name" id="branch_name" class="form-control">
                                    <div class="invalid-feedback" id="error-branch_name"></div>
                                </div>

                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.company_name') }}</label>
                                    <select name="company_id" id="company_id" class="form-control">
                                        <option value="">{{ __('dashboard.select_company') }}</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->legal_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.brand_name') }}</label>
                                    <select name="brand_id" id="brand_id" class="form-control">
                                        <option value="">{{ __('dashboard.select_brand') }}</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>{{ __('dashboard.location_address') }}</label>
                                    <input type="text" name="branch_address" id="branch_address"
                                        class="form-control">
                                    <div class="invalid-feedback" id="error-branch_address"></div>
                                </div>

                                <div class="form-group col-md-3">
                                    <label>{{ __('dashboard.contact_person_manager') }}</label>
                                    <input type="text" name="branch_manager" id="branch_manager"
                                        class="form-control">
                                    <div class="invalid-feedback" id="error-branch_manager"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>{{ __('dashboard.email') }}</label>
                                    <input type="email" name="branch_email" id="branch_email" class="form-control">
                                    <div class="invalid-feedback" id="error-branch_email"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>{{ __('dashboard.phone') }}</label>
                                    <input type="text" name="branch_phone" id="branch_phone" class="form-control">
                                    <div class="invalid-feedback" id="error-branch_phone"></div>
                                </div>
                            </div>
                            <div class="form-row">

                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.market_price') }}</label>
                                    <input type="number" name="market_price" id="market_price" class="form-control">
                                    <div class="invalid-feedback" id="error-branch_phone"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.sale_price') }}</label>
                                    <input type="number" name="sale_price" id="sale_price" class="form-control">
                                    <div class="invalid-feedback" id="error-branch_phone"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.damage_assist') }}</label>
                                    <input type="number" name="damage_assist" id="damage_assist" class="form-control">
                                    <div class="invalid-feedback" id="error-branch_phone"></div>
                                </div>
                            </div>

                            <div id="document-wrapper">
                                <div class="form-row">
                                    <label>{{ __('dashboard.documents') }}</label>
                                    <div id="" class="col-md-12">
                                        <div class="mb-3 document-input d-flex align-items-center">
                                            <input type="file" class="form-control" name="files[]">
                                            <button type="button"
                                                class="btn btn-success btn-sm ms-2 add-document-btn ml-2">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            <span class="text-danger error-text file_error ms-2"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <!-- Document Name -->
                                    <div class="col-md-4">
                                        <label>{{ __('dashboard.document_name') }}</label>
                                        <input type="text" name="doc_name[]" class="form-control"
                                            placeholder="{{ __('dashboard.document_name') }}">
                                    </div>

                                    <!-- Date Type Toggle -->
                                    <div class="col-md-2">
                                        <label>{{ __('dashboard.date_type') }}</label>
                                        <select name="date_type[]" class="form-control date-type-select">
                                            <option value="hijri">{{ __('dashboard.hijri') }}</option>
                                            <option value="gregorian">{{ __('dashboard.gregorian') }}</option>
                                        </select>
                                    </div>

                                    <!-- Start Date (Hijri by default) -->
                                    <div class="col-md-3 hijri-date-group">
                                        <label for="startHijri">{{ __('dashboard.start_date') }}
                                        </label>
                                        <input type="text" class="hijri-input form-control" name="start_date_hijri[]"
                                            placeholder="iYYYY-iMM-iDD" readonly>
                                        <div class="hijri-picker" style="display:none;"></div>
                                    </div>

                                    <!-- Start Date (Gregorian - hidden by default) -->
                                    <div class="col-md-3 gregorian-date-group" style="display: none;">
                                        <label for="startGregorian">{{ __('dashboard.start_date') }}
                                        </label>
                                        <input type="date" class="gregorian-input form-control"
                                            name="start_date_gregorian[]">
                                    </div>

                                    <!-- End Date (Hijri by default) -->
                                    <div class="col-md-3 hijri-date-group">
                                        <label for="endHijri">{{ __('dashboard.end_date') }}
                                        </label>
                                        <input type="text" class="hijri-input form-control" name="end_date_hijri[]"
                                            placeholder="iYYYY-iMM-iDD" readonly>
                                        <div class="hijri-picker" style="display:none;"></div>
                                    </div>

                                    <!-- End Date (Gregorian - hidden by default) -->
                                    <div class="col-md-3 gregorian-date-group" style="display: none;">
                                        <label for="endGregorian">{{ __('dashboard.end_date') }}
                                        </label>
                                        <input type="date" class="gregorian-input form-control"
                                            name="end_date_gregorian[]">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row ">
                                <div class="form-group col-md-12">
                                    <label>{{ __('dashboard.status') }}</label>
                                    <select name="branch_status" id="branch_status" class="form-control">
                                        <option value="Active">{{ __('dashboard.active') }}</option>
                                        <option value="Inactive">{{ __('dashboard.inactive') }}</option>
                                    </select>
                                    <div class="invalid-feedback" id="error-branch_status"></div>
                                </div>
                            </div>


                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>{{ __('dashboard.building_type') }}</label>
                                    <select name="building_type" id="building_type" class="form-control">
                                        <option value="owned">{{ __('dashboard.owned') }}</option>
                                        <option value="rented">{{ __('dashboard.rented') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div id="rentFields" style="display: none;">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>{{ __('dashboard.total_rent_amount') }}</label>
                                        <input type="number" name="total_rent" id="total_rent" class="form-control">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>{{ __('dashboard.number_of_installments') }}</label>
                                        <input type="number" name="installments" id="installments"
                                            class="form-control">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>{{ __('dashboard.rent_start_date') }}</label>
                                        <input type="date" name="rent_start_date" id="rent_start_date"
                                            class="form-control">
                                    </div>

                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>{{ __('dashboard.rent_end_date') }}</label>
                                        <input type="date" name="rent_end_date" id="rent_expiry_date"
                                            class="form-control">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>{{ __('dashboard.rent_agreement_document') }}</label>
                                        <input type="file" name="rent_agreement" id="rent_agreement"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="submit" id="addBranchSubmit" class="btn btn-primary">
                                    <span id="addBranchSpinner" class="spinner-border spinner-border-sm"
                                        style="display:none"></span>
                                    {{ __('dashboard.save_branch') }}
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>


        <!-- Single Edit Branch Modal -->
        <div class="modal fade" id="editBranchModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('dashboard.edit_branch_details') }}</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="editBranchForm" method="POST" action="{{ route('dashboard.branch.update') }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="branchId" id="edit_branch_id">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.branch_name') }}</label>
                                    <input type="text" class="form-control" name="edit_branch_name"
                                        id="edit_branch_name">
                                    <span class="text-danger small" id="error-edit_branch_name"></span>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('dashboard.location_address') }}</label>
                                    <input type="text" class="form-control" name="branch_location"
                                        id="edit_branch_location">
                                    <span class="text-danger small" id="error-branch_location"></span>
                                </div>
                            </div>


                            <div class="form-row">


                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.contact_person_manager') }}</label>
                                    <input type="text" name="branch_manager" id="edit_branch_manager"
                                        class="form-control">
                                    <div class="invalid-feedback" id="error-branch_manager"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.email') }}</label>
                                    <input type="email" name="branch_email" id="edit_branch_email"
                                        class="form-control">
                                    <div class="invalid-feedback" id="error-branch_email"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.market_price') }}</label>
                                    <input type="number" name="market_price" id="edit_market_price"
                                        class="form-control">
                                    <div class="invalid-feedback" id="error-branch_phone"></div>
                                </div>
                            </div>


                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>{{ __('dashboard.building_type') }}</label>
                                    <select name="building_type" id="edit_building_type" class="form-control">
                                        <option value="owned">{{ __('dashboard.owned') }}</option>
                                        <option value="rented">{{ __('dashboard.rented') }}</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Rented Only Fields -->
                            <div id="editRentedFields" style="display:none;">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>{{ __('dashboard.total_rent_amount') }}</label>
                                        <input type="number" name="total_rent" id="edit_total_rent"
                                            class="form-control">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>{{ __('dashboard.number_of_installments') }}</label>
                                        <input type="number" name="installments" id="edit_installments"
                                            class="form-control">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>{{ __('dashboard.rent_start_date') }}</label>
                                        <input type="date" name="rent_start_date" id="edit_rent_start_date"
                                            class="form-control">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label>{{ __('dashboard.rent_end_date') }}</label>
                                        <input type="date" name="rent_end_date" id="edit_rent_expiry_date"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label>{{ __('dashboard.rent_agreement_document') }}</label>
                                        <input type="file" name="rent_agreement" class="form-control">
                                        <small class="text-muted">{{ __('dashboard.leave_empty') }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.value') }}</label>
                                    <input type="number" name="sale_price" id="edit_sale_price" class="form-control">
                                    <div class="invalid-feedback" id="error-branch_phone"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.phone') }}</label>
                                    <input type="text" name="branch_phone" id="edit_branch_phone"
                                        class="form-control">
                                    <div class="invalid-feedback" id="error-branch_phone"></div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.damage_assist') }}</label>
                                    <input type="number" name="damage_assist" id="edit_damage_assist"
                                        class="form-control">
                                    <div class="invalid-feedback" id="error-branch_phone"></div>
                                </div>
                            </div>

                            <!-- Existing single document fields (for main document) -->
                            <div class="form-row">
                                <div class="col-md-4">
                                    <label>{{ __('dashboard.document_name') }}</label>
                                    <input type="text" name="doc_name[]" class="form-control"
                                        placeholder="{{ __('dashboard.document_name') }}" id="edit_doc_name"
                                        value="{{ old('doc_name.0', $document->doc_name ?? '') }}">
                                </div>

                                <!-- Date Type Toggle for main document -->
                                <div class="col-md-2">
                                    <label>{{ __('dashboard.date_type') }}</label>
                                    <select name="date_type[]" class="form-control date-type-select edit-date-type">
                                        <option value="hijri"
                                            {{ old('date_type.0', isset($document) && $document->date_type == 'hijri' ? 'selected' : 'selected') }}>
                                            {{ __('dashboard.hijri') }}</option>
                                        <option value="gregorian"
                                            {{ old('date_type.0', isset($document) && $document->date_type == 'gregorian' ? 'selected' : '') }}>
                                            {{ __('dashboard.gregorian') }}</option>
                                    </select>
                                </div>

                                <!-- Start Date - Hijri (default) -->
                                <div class="col-md-3 hijri-date-group">
                                    <label for="startHijri">{{ __('dashboard.start_date') }}
                                        ({{ __('dashboard.hijri') }})</label>
                                    <input type="text" class="hijri-input form-control" name="start_date_hijri[]"
                                        id="edit_start_date" placeholder="iYYYY-iMM-iDD" readonly
                                        value="{{ old('start_date_hijri.0', isset($document) && $document->date_type == 'hijri' ? $document->start_date : '') }}">
                                    <div class="hijri-picker" style="display:none;"></div>
                                </div>

                                <!-- Start Date - Gregorian (hidden initially) -->
                                <div class="col-md-3 gregorian-date-group" style="display: none;">
                                    <label for="startGregorian">{{ __('dashboard.start_date') }}
                                        ({{ __('dashboard.gregorian') }})</label>
                                    <input type="date" class="gregorian-input form-control"
                                        name="start_date_gregorian[]" id="edit_start_date"
                                        value="{{ old('start_date_gregorian.0', isset($document) && $document->date_type == 'gregorian' ? $document->start_date : '') }}">
                                </div>

                                <!-- End Date - Hijri (default) -->
                                <div class="col-md-3 hijri-date-group">
                                    <label for="endHijri">{{ __('dashboard.end_date') }}
                                        ({{ __('dashboard.hijri') }})</label>
                                    <input type="text" class="hijri-input form-control" name="end_date_hijri[]"
                                        id="edit_end_date" placeholder="iYYYY-iMM-iDD" readonly
                                        value="{{ old('end_date_hijri.0', isset($document) && $document->date_type == 'hijri' ? $document->end_date : '') }}">
                                    <div class="hijri-picker" style="display:none;"></div>
                                </div>

                                <!-- End Date - Gregorian (hidden initially) -->
                                <div class="col-md-3 gregorian-date-group" style="display: none;">
                                    <label for="endGregorian">{{ __('dashboard.end_date') }}
                                        ({{ __('dashboard.gregorian') }})</label>
                                    <input type="date" class="gregorian-input form-control"
                                        name="end_date_gregorian[]" id="edit_end_date"
                                        value="{{ old('end_date_gregorian.0', isset($document) && $document->date_type == 'gregorian' ? $document->end_date : '') }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <label>{{ __('dashboard.documents') }}</label>
                                <div id="edit-document-wrapper" class="col-md-12">
                                    <div class="mb-3 document-input d-flex align-items-center">
                                        <input type="file" class="form-control" name="files[]">
                                        <button type="button" class="btn btn-success btn-sm ms-2 edit-document-btn ml-2">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <span class="text-danger error-text file_error ms-2"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">

                                <div class="form-group col-md-12">
                                    <label class="form-label">{{ __('dashboard.status') }}</label>
                                    <select class="form-control" name="branch_status" id="edit_branch_status">
                                        <option value="Active">{{ __('dashboard.active') }}</option>
                                        <option value="Inactive">{{ __('dashboard.inactive') }}</option>
                                    </select>
                                    <span class="text-danger small" id="error-branch_status"></span>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="reset"
                                    class="btn btn-secondary me-2">{{ __('dashboard.reset') }}</button>
                                <button type="submit" class="btn btn-primary">
                                    {{ __('dashboard.update_branch') }}
                                    <span class="spinner-border spinner-border-sm ms-1 d-none"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Single View Branch Modal -->
        <div class="modal fade" id="viewBranchModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewBranchModalLabel">{{ __('dashboard.view_branch_details') }}</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="fw-bold">{{ __('dashboard.branch_name') }}:</label>
                                <p id="viewBranchName"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold">{{ __('dashboard.location_address') }}:</label>
                                <p id="viewBranchAddress"></p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="fw-bold">{{ __('dashboard.contact_person_manager') }}:</label>
                                <p id="viewBranchManager"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold">{{ __('dashboard.email') }}:</label>
                                <p id="viewBranchEmail"></p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="fw-bold">{{ __('dashboard.phone') }}:</label>
                                <p id="viewBranchPhone"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold">{{ __('dashboard.status') }}:</label>
                                <p id="viewBranchStatus"></p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="fw-bold">{{ __('dashboard.market_price') }}:</label>
                                <p id="viewMarketPrice"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold">{{ __('dashboard.rent') }}:</label>
                                <p id="viewRent"></p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="fw-bold">{{ __('dashboard.value') }}:</label>
                                <p id="viewSalePrice"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold">{{ __('dashboard.rent_start_date') }}:</label>
                                <p id="viewStartRent"></p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="fw-bold">{{ __('dashboard.rent_end_date') }}:</label>
                                <p id="viewEndRent"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold">{{ __('dashboard.damage_assist') }}:</label>
                                <p id="viewDamageAssist"></p>
                            </div>
                        </div>

                        <!-- Documents Section -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="fw-bold mb-3">{{ __('dashboard.documents') }}</h6>
                                <div id="viewDocumentsContainer">
                                    <!-- Documents will be loaded here dynamically -->
                                </div>
                            </div>
                        </div>

                        <!-- Rent Agreement Section -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="fw-bold mb-3">{{ __('dashboard.rent_documents') }}</h6>
                                <div id="viewRentAgreementContainer">
                                    <!-- Rent agreement will be loaded here dynamically -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('dashboard.close') }}</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Branch Modal -->
        <div class="modal fade" id="deleteBranchModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">{{ __('dashboard.delete_branch') }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <form id="deleteBranchForm" method="POST" action="{{ route('dashboard.branch.delete') }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="branchId" id="deleteBranchId">

                        <div class="modal-body text-center">
                            <p>{{ __('dashboard.confirm_delete_modal') }}</p>
                        </div>

                        <div class="modal-footer justify-content-center">
                            <button type="submit" class="btn btn-danger">{{ __('dashboard.yes_delete') }}</button>
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('bundles/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('bundles/datatables/export-tables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('bundles/datatables/export-tables/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('bundles/datatables/export-tables/jszip.min.js') }}"></script>
    <script src="{{ asset('bundles/datatables/export-tables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('bundles/datatables/export-tables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('bundles/datatables/export-tables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('js/page/datatables.js') }}"></script>
    <script>
        $(function() {
            // CSRF setup for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Add Branch
            $(document).on('submit', '#addBranchForm', function(e) {
                e.preventDefault();

                const $form = $(this);
                const $btn = $form.find('button[type="submit"]');
                const $spinner = $form.find('.spinner-border');

                // Clear previous errors
                $form.find('.is-invalid').removeClass('is-invalid');
                $form.find('[id^="error-"]').text('');

                $btn.prop('disabled', true);
                $spinner?.removeClass('d-none');

                //  USE FormData (IMPORTANT)
                let formData = new FormData(this);

                $.ajax({
                        url: $form.attr('action'),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                    })
                    .done(function(res) {
                        if (res.success) {
                            $('#addBranchModal').modal('hide');
                            $form[0].reset();

                            appendBranchRow(res.data);

                            Swal.fire({
                                icon: 'success',
                                title: 'Created!/مخلوق',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    })
                    .fail(function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors || {};
                            $.each(errors, function(field, messages) {
                                const $el = $('[name="' + field + '"]', $form);
                                $el.addClass('is-invalid');
                                $('#error-' + field, $form).text(messages[0]);
                            });
                        } else if (xhr.status === 419 || xhr.status === 401) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Session Expired/انتهت الجلسة',
                                text: 'Please login again/الرجاء تسجيل الدخول مرة أخرى'
                            }).then(() => {
                                window.location = "{{ route('login') }}";
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Server Error',
                                text: xhr.responseJSON?.message || 'Something went wrong'
                            });
                        }
                    })
                    .always(() => {
                        $btn.prop('disabled', false);
                        $spinner?.addClass('d-none');
                    });
            });


            $(document).on('click', '.viewBranchBtn', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const branchId = $btn.data('id');

                // Fill basic branch details
                $('#viewBranchName').text($btn.data('name'));
                $('#viewBranchAddress').text($btn.data('location'));
                $('#viewBranchManager').text($btn.data('manager'));
                $('#viewBranchEmail').text($btn.data('email'));
                $('#viewBranchPhone').text($btn.data('phone'));
                $('#viewBranchStatus').text($btn.data('status'));
                $('#viewMarketPrice').text($btn.data('market_price'));
                $('#viewRent').text($btn.data('rent'));
                $('#viewStartRent').text($btn.data('rent_start_date'));
                $('#viewEndRent').text($btn.data('rent_end_date'));
                $('#viewDamageAssist').text($btn.data('damage_assist'));
                $('#viewSalePrice').text($btn.data('sale_price'));

                // Clear previous documents
                $('#viewDocumentsContainer').html(
                    '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading documents...</div>'
                );
                $('#viewRentAgreementContainer').html('');

                // Load documents via AJAX
                $.ajax({
                    url: '/dashboard/branch/' + branchId + '/documents',
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            // Load regular documents
                            let documentsHtml = '';
                            if (response.documents && response.documents.length > 0) {
                                documentsHtml = `
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('dashboard.document_name') }}</th>

                                    <th>{{ __('dashboard.issue_date') }}</th>
                                    <th>{{ __('dashboard.expiration_date') }}</th>
                                    <th>{{ __('dashboard.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>`;

                                response.documents.forEach(doc => {
                                    const issueDate = doc.date_type === 'hijri' ?
                                        `${doc.issue_date} <span class="badge bg-info">Hijri</span>` :
                                        doc.issue_date;

                                    const expiryDate = doc.date_type === 'hijri' ?
                                        `${doc.expiration_date} <span class="badge bg-info">Hijri</span>` :
                                        doc.expiration_date;

                                    documentsHtml += `
                            <tr>
                                <td>${doc.name || 'N/A'}</td>

                                <td>${issueDate || '-'}</td>
                                <td>${expiryDate || '-'}</td>
                                <td>
                                    <a href="/storage/${doc.file_path}" target="_blank"
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> {{ __('dashboard.view') }}
                                    </a>
                                </td>
                            </tr>`;
                                });

                                documentsHtml += `</tbody></table>`;
                            } else {
                                documentsHtml =
                                    '<p class="text-muted">{{ __('dashboard.no_documents_found') }}</p>';
                            }

                            $('#viewDocumentsContainer').html(documentsHtml);

                            // Load rent agreement if exists
                            let rentHtml = '';
                            if (response.branch.rent_agreement) {
                                rentHtml = `
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('dashboard.document_name') }}</th>
                                    <th>{{ __('dashboard.rent_start_date') }}</th>
                                    <th>{{ __('dashboard.rent_end_date') }}</th>
                                    <th>{{ __('dashboard.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ __('dashboard.rent_agreement') }}</td>
                                    <td>${response.branch.rent_start_date || '-'}</td>
                                    <td>${response.branch.rent_end_date || '-'}</td>
                                    <td>
                                        <a href="/storage/${response.branch.rent_agreement}" target="_blank"
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> {{ __('dashboard.view') }}
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>`;
                            } else {
                                rentHtml =
                                    '<p class="text-muted">{{ __('dashboard.no_rent_agreement_found') }}</p>';
                            }

                            $('#viewRentAgreementContainer').html(rentHtml);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading documents:', error);
                        $('#viewDocumentsContainer').html(
                            '<p class="text-danger">{{ __('dashboard.error_loading_documents') }}</p>'
                        );
                        $('#viewRentAgreementContainer').html(
                            '<p class="text-danger">{{ __('dashboard.error_loading_documents') }}</p>'
                        );
                    }
                });

                // Show modal
                $('#viewBranchModal').modal('show');
            });

            // Initialize edit modal building type toggle
            $('#edit_building_type').on('change', function() {
                if ($(this).val() === 'rented') {
                    $('#editRentedFields').slideDown();
                } else {
                    $('#editRentedFields').slideUp();
                }
            });

            // Clear modal content when closed
            $('#viewBranchModal').on('hidden.bs.modal', function() {
                $('#viewDocumentsContainer').html('');
                $('#viewRentAgreementContainer').html('');
            });


            // ----------------------
            //  Open Edit Modal
            // ----------------------
            $(document).on('click', '.editBranchBtn', function() {
                const d = $(this).data();

                $('#edit_branch_id').val(d.id);
                $('#edit_branch_name').val(d.name);
                $('#edit_branch_location').val(d.location);
                $('#edit_branch_manager').val(d.manager);
                $('#edit_branch_email').val(d.email);
                $('#edit_branch_phone').val(d.phone);
                $('#edit_market_price').val(d.market_price);
                $('#edit_sale_price').val(d.sale_price);
                $('#edit_rent').val(d.rent);
                $('#edit_total_rent').val(d.total_rent);
                $('#edit_installments').val(d.installments);
                $('#edit_damage_assist').val(d.damage_assist);
                $('#edit_rent_start_date').val(d.rent_start_date);
                $('#edit_rent_expiry_date').val(d.rent_end_date);
                $('#edit_branch_status').val(d.status);
                $('#edit_building_type').val(d.building_type);
                $('#edit_start_date').val(d.start_date);
                $('#edit_end_date').val(d.end_date);
                $('#edit_doc_name').val(d.doc_name);

                // Toggle rented fields
                if (d.building_type === 'rented') {
                    $('#editRentedFields').show();
                } else {
                    $('#editRentedFields').hide();
                }

                $('#editBranchModal').modal('show');
            });


            // ----------------------
            //  Submit Edit Form via AJAX
            // ----------------------
            $(document).on('submit', '#editBranchForm', function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                        url: $(this).attr('action'),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                    })
                    .done(function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    })
                    .fail(function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Update failed', 'error');
                    });
            });


            // Open delete modal and set branchId
            $(document).on('click', '.deleteBranchBtn', function(e) {
                e.preventDefault();
                const branchId = $(this).data('id');
                $('#deleteBranchId').val(branchId);
                $('#deleteBranchModal').modal('show');
            });

            $(document).on('submit', '#deleteBranchForm', function(e) {
                e.preventDefault();
                const $form = $(this);
                const branchId = $('#deleteBranchId').val();
                const $btn = $form.find('button[type="submit"]');
                const url = $form.attr('action');

                $btn.prop('disabled', true);

                $.ajax({
                        url: url,
                        method: 'POST',
                        data: $form.serialize(),
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                    .done(function(res) {
                        if (res.success) {
                            $('#deleteBranchModal').modal('hide'); // hide modal
                            $('#branch-row-' + branchId).remove(); // remove row
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!/تم الحذف!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error/خطأ',
                                text: res.message
                            });
                        }
                    })
                    .fail(function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error/خطأ',
                            text: xhr.responseJSON?.message || 'Something went wrong'
                        });
                    })
                    .always(function() {
                        $btn.prop('disabled', false);
                    });
            });

            // Helpers
            function appendBranchRow(branch) {
                const statusClass = branch.status.trim() === 'Active' ? 'badge-success' : 'badge-danger';

                const html = `
<tr id="branch-row-${branch.id}">
  <td class="branch-name-cell">${branch.name}</td>
  <td class="branch-location-cell">${branch.location}</td>
  <td class="branch-manager-cell">${branch.manager}</td>
  <td class="branch-email-cell">${branch.email}</td>
  <td class="branch-phone-cell">${branch.phone}</td>
  <td class="branch-status-cell"><span class="badge ${statusClass}">${branch.status}</span></td>
  <td>
    <a href="#" class="text-info viewBranchBtn"
       data-id="${branch.id}"
       data-name="${branch.name}"
       data-location="${branch.location}"
       data-manager="${branch.manager}"
       data-email="${branch.email}"
       data-phone="${branch.phone}"
       data-status="${branch.status}">
       <i class="fas fa-eye"></i>
    </a>
    <a href="#" class="text-secondary editBranchBtn"
       data-id="${branch.id}"
       data-name="${branch.name}"
       data-location="${branch.location}"
       data-manager="${branch.manager}"
       data-email="${branch.email}"
       data-phone="${branch.phone}"
       data-status="${branch.status}">
       <i class="fas fa-edit"></i>
    </a>
    <a href="#" class="text-danger deleteBranchBtn" data-id="${branch.id}">
       <i class="fas fa-trash-alt"></i>
    </a>
  </td>
</tr>
`;

                $('#tableExport tbody').prepend(html);
            }



            function updateBranchRow(branch) {
                const row = $('#branch-row-' + branch.id);
                if (row.length) {
                    // Update table cells
                    row.find('td.branch-name-cell').text(branch.name);
                    row.find('td.branch-location-cell').text(branch.location);
                    row.find('td.branch-manager-cell').text(branch.manager);
                    row.find('td.branch-email-cell').text(branch.email);
                    row.find('td.branch-phone-cell').text(branch.phone);

                    // Update status badge
                    const statusCell = row.find('td.branch-status-cell');
                    const status = branch.status.trim().toLowerCase();
                    let statusClass = 'badge-danger';
                    if (status === 'active') statusClass = 'badge-success';
                    statusCell.html(`<span class="badge ${statusClass}">${branch.status}</span>`);

                    // Update button data attributes
                    const editBtn = row.find('.editBranchBtn');
                    const viewBtn = row.find('.viewBranchBtn');
                    [editBtn, viewBtn].forEach(btn => {
                        btn.data('name', branch.name);
                        btn.data('location', branch.location);
                        btn.data('manager', branch.manager);
                        btn.data('email', branch.email);
                        btn.data('phone', branch.phone);
                        btn.data('status', branch.status);
                    });
                }
            }

        });

        $(function() {

            function fetchBranches() {
                $.ajax({
                    url: "{{ route('branches.filter') }}",
                    method: "GET",
                    data: {
                        name: $('#filter_name').val(),
                        location: $('#filter_location').val(),
                        manager: $('#filter_manager').val(),
                        status: $('#filter_status').val()
                    },
                    success: function(res) {
                        $('#tableExport tbody').html(res);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            }

            $('#branchFilterForm').on('submit', function(e) {
                e.preventDefault();
                fetchBranches();
            });

        });




        $('#company_id').on('change', function() {
            var companyId = $(this).val();

            $('#brand_id').html('<option value="">Loading...</option>');

            if (companyId) {
                $.ajax({
                    url: "/get-brands/" + companyId,
                    type: "GET",
                    success: function(data) {
                        $('#brand_id').empty();
                        $('#brand_id').append('<option value="">Select Brand</option>');

                        $.each(data, function(key, brand) {
                            $('#brand_id').append(
                                '<option value="' + brand.id + '">' + brand.name +
                                '</option>'
                            );
                        });
                    }
                });
            } else {
                $('#brand_id').html('<option value="">Select Brand</option>');
            }
        });

        $(document).ready(function() {
            // Show/hide rent fields based on building type
            $('#building_type').on('change', function() {
                if ($(this).val() === 'rented') {
                    $('#rentFields').show();
                } else {
                    $('#rentFields').hide();
                    // Optional: clear rent values
                    $('#monthly_rent, #yearly_cost, #installments_per_year, #rent_agreement, #rent_expiry_date')
                        .val('');
                }
            });

            // Calculate Yearly Cost automatically
            $('#monthly_rent, #installments_per_year').on('input', function() {
                let monthlyRent = parseFloat($('#monthly_rent').val()) || 0;
                let installments = parseInt($('#installments_per_year').val()) || 12;
                let yearlyCost = monthlyRent * installments;
                $('#yearly_cost').val(yearlyCost.toFixed(2));
            });
        });

        // Language setting (get from Laravel)
        const lang = "{{ app()->getLocale() }}"; // 'en' or 'ar'

        // Hijri conversion functions
        function gregorianToHijri(gDate) {
            const jd = (gDate / 86400000) + 2440587.5;
            const islamicEpoch = 1948439.5;
            const days = jd - islamicEpoch;
            const year = Math.floor((30 * days + 10646) / 10631);
            const hijriYearStart = hijriToJD(year, 1, 1) - islamicEpoch;
            const month = Math.min(12, Math.ceil((days - 29 - hijriYearStart) / 29.5) + 1);
            const hijriMonthStart = hijriToJD(year, month, 1) - islamicEpoch;
            const day = Math.floor(days - hijriMonthStart + 1);
            return { year, month, day };
        }

        function hijriToJD(year, month, day) {
            return day + Math.ceil(29.5 * (month - 1)) + (year - 1) * 354 + Math.floor((3 + 11 * year) / 30) + 1948439.5;
        }

        function hijriToGregorian(hYear, hMonth, hDay) {
            const jd = hijriToJD(hYear, hMonth, hDay);
            const gregorianEpoch = 1721425.5;
            const daysSinceGregorianEpoch = jd - gregorianEpoch;
            const gDate = new Date((daysSinceGregorianEpoch) * 86400000);
            return gDate;
        }

        // Initialize Hijri picker for any input
        function initHijriPicker(input, picker) {
            picker.style.direction = lang === 'ar' ? 'rtl' : 'ltr';
            input.addEventListener('click', (e) => {
                e.stopPropagation();
                picker.style.display = 'block';
                // If input already has value, use it as selected date
                let selectedDate = null;
                if (input.value) {
                    const parts = input.value.split('-');
                    if (parts.length === 3) {
                        selectedDate = {
                            year: parseInt(parts[0]),
                            month: parseInt(parts[1]),
                            day: parseInt(parts[2])
                        };
                    }
                }
                renderCalendarForInput(input, picker, selectedDate);
            });

            document.addEventListener('click', (e) => {
                if (!picker.contains(e.target) && e.target !== input) {
                    picker.style.display = 'none';
                }
            });
        }

        // Render calendar for a specific input/picker
        function renderCalendarForInput(input, picker, selectedDate = null) {
            const today = new Date();
            const hijriToday = gregorianToHijri(today.getTime());
            const year = selectedDate ? selectedDate.year : hijriToday.year;
            const month = selectedDate ? selectedDate.month : hijriToday.month;

            // Month & year dropdown
            let html = `<select class="monthSelect form-control form-control-sm mb-2">`;
            const months = lang === 'ar' ? [
                "محرم", "صفر", "ربيع الأول", "ربيع الثاني", "جمادى الأولى", "جمادى الآخرة",
                "رجب", "شعبان", "رمضان", "شوال", "ذو القعدة", "ذو الحجة"
            ] : [
                "Muharram", "Safar", "Rabi I", "Rabi II", "Jumada I", "Jumada II",
                "Rajab", "Sha'ban", "Ramadan", "Shawwal", "Dhul-Qi'dah", "Dhul-Hijjah"
            ];

            for (let m = 1; m <= 12; m++) {
                html += `<option value="${m}" ${m === month ? 'selected' : ''}>${months[m - 1]}</option>`;
            }
            html += `</select>`;

            html += `<select class="yearSelect form-control form-control-sm mb-2">`;
            for (let y = year - 10; y <= year + 10; y++) {
                html += `<option value="${y}" ${y === year ? 'selected' : ''}>${y}</option>`;
            }
            html += `</select>`;

            // Days table
            html += '<table class="hijri-calendar-table w-100"><tr>';
            const weekDays = lang === 'ar' ? ['ح', 'ن', 'ث', 'ر', 'خ', 'ج', 'س'] : ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr',
                'Sa'
            ];
            weekDays.forEach(d => html += `<th class="text-center">${d}</th>`);
            html += '</tr><tr>';

            const daysInMonth = month % 2 === 0 ? 29 : 30;
            for (let d = 1; d <= daysInMonth; d++) {
                html += `<td class="text-center" data-day="${d}" data-month="${month}" data-year="${year}">${d}</td>`;
                if (d % 7 === 0) html += '</tr><tr>';
            }
            html += '</tr></table>';

            picker.innerHTML = html;
            picker.style.position = 'absolute';
            picker.style.zIndex = '1000';
            picker.style.backgroundColor = 'white';
            picker.style.border = '1px solid #ddd';
            picker.style.padding = '10px';
            picker.style.borderRadius = '5px';
            picker.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
            picker.style.minWidth = '250px';

            const monthSelect = picker.querySelector('.monthSelect');
            const yearSelect = picker.querySelector('.yearSelect');

            // Update calendar when month/year changes
            monthSelect.addEventListener('change', () => {
                renderCalendarForInput(input, picker, {
                    year: parseInt(yearSelect.value),
                    month: parseInt(monthSelect.value)
                });
            });

            yearSelect.addEventListener('change', () => {
                renderCalendarForInput(input, picker, {
                    year: parseInt(yearSelect.value),
                    month: parseInt(monthSelect.value)
                });
            });

            // Click on day
            picker.querySelectorAll('td').forEach(td => {
                td.addEventListener('click', () => {
                    const y = td.dataset.year;
                    const m = String(td.dataset.month).padStart(2, '0');
                    const d = String(td.dataset.day).padStart(2, '0');
                    input.value = `${y}-${m}-${d}`;
                    picker.style.display = 'none';
                });
            });
        }

        // Toggle date type (Hijri/Gregorian)
        function toggleDateType(selectElement) {
            const row = selectElement.closest('.form-row');
            const isHijri = selectElement.value === 'hijri';

            // Show/hide date groups
            const hijriGroups = row.querySelectorAll('.hijri-date-group');
            const gregorianGroups = row.querySelectorAll('.gregorian-date-group');

            hijriGroups.forEach(group => {
                group.style.display = isHijri ? 'block' : 'none';
            });

            gregorianGroups.forEach(group => {
                group.style.display = isHijri ? 'none' : 'block';
            });

            // Clear the opposite date inputs
            if (isHijri) {
                row.querySelectorAll('.gregorian-input').forEach(input => input.value = '');
            } else {
                row.querySelectorAll('.hijri-input').forEach(input => input.value = '');
            }
        }

        // Initialize all date fields on page load
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize Hijri pickers
            document.querySelectorAll('.hijri-input').forEach(input => {
                const picker = input.nextElementSibling;
                if (picker) initHijriPicker(input, picker);
            });

            // Initialize date type toggles
            document.querySelectorAll('.date-type-select').forEach(select => {
                toggleDateType(select); // Set initial state
                select.addEventListener('change', function() {
                    toggleDateType(this);
                });
            });
        });

        // Dynamic document addition
        document.addEventListener('click', function(e) {
            // ADD new document
            if (e.target.closest('.add-document-btn')) {
                const wrapper = document.getElementById('document-wrapper');
                const newDiv = document.createElement('div');
                newDiv.classList.add('mb-3', 'document-block', 'border', 'p-3', 'rounded');

                newDiv.innerHTML = `
      <div class="form-row mb-2">
        <label>{{ __('dashboard.documents') }}</label>
        <div class="col-md-12 d-flex align-items-center">
          <input type="file" class="form-control" name="files[]">
          <button type="button" class="btn btn-danger btn-sm ms-2 remove-document-btn ml-2">
            <i class="fas fa-minus"></i>
          </button>
          <span class="text-danger error-text file_error ms-2"></span>
        </div>
      </div>

      <div class="form-row mb-2">
        <div class="col-md-4">
          <label>{{ __('dashboard.document_name') }}</label>
          <input type="text" name="doc_name[]" class="form-control" placeholder="{{ __('dashboard.document_name') }}">
        </div>

        <div class="col-md-2">
          <label>{{ __('dashboard.date_type') }}</label>
          <select name="date_type[]" class="form-control date-type-select">
            <option value="hijri">{{ __('dashboard.hijri') }}</option>
            <option value="gregorian">{{ __('dashboard.gregorian') }}</option>
          </select>
        </div>

        <div class="col-md-3 hijri-date-group">
          <label>{{ __('dashboard.start_date') }} </label>
          <input type="text" class="hijri-input form-control" name="start_date_hijri[]" placeholder="iYYYY-iMM-iDD" readonly>
          <div class="hijri-picker" style="display:none;"></div>
        </div>

        <div class="col-md-3 gregorian-date-group" style="display: none;">
          <label>{{ __('dashboard.start_date') }} </label>
          <input type="date" class="gregorian-input form-control" name="start_date_gregorian[]">
        </div>

        <div class="col-md-3 hijri-date-group">
          <label>{{ __('dashboard.end_date') }}</label>
          <input type="text" class="hijri-input form-control" name="end_date_hijri[]" placeholder="iYYYY-iMM-iDD" readonly>
          <div class="hijri-picker" style="display:none;"></div>
        </div>

        <div class="col-md-3 gregorian-date-group" style="display: none;">
          <label>{{ __('dashboard.end_date') }} </label>
          <input type="date" class="gregorian-input form-control" name="end_date_gregorian[]">
        </div>
      </div>
    `;

                wrapper.appendChild(newDiv);

                // Initialize Hijri pickers for new inputs
                newDiv.querySelectorAll('.hijri-input').forEach(input => {
                    const picker = input.nextElementSibling;
                    if (picker) initHijriPicker(input, picker);
                });

                // Initialize date type toggle for new select
                const dateTypeSelect = newDiv.querySelector('.date-type-select');
                dateTypeSelect.addEventListener('change', function() {
                    toggleDateType(this);
                });
                toggleDateType(dateTypeSelect); // Set initial state
            }

            // REMOVE document
            if (e.target.closest('.remove-document-btn')) {
                const block = e.target.closest('.document-block');
                if (block) block.remove();
            }
        });



        // Toggle date type for edit form
        function toggleEditDateType(selectElement) {
            const row = selectElement.closest('.form-row');
            const isHijri = selectElement.value === 'hijri';

            // Show/hide date groups within this row
            const hijriGroups = row.querySelectorAll('.hijri-date-group');
            const gregorianGroups = row.querySelectorAll('.gregorian-date-group');

            hijriGroups.forEach(group => {
                group.style.display = isHijri ? 'block' : 'none';
            });

            gregorianGroups.forEach(group => {
                group.style.display = isHijri ? 'none' : 'block';
            });

            // Clear the opposite date inputs
            if (isHijri) {
                row.querySelectorAll('.gregorian-input').forEach(input => input.value = '');
            } else {
                row.querySelectorAll('.hijri-input').forEach(input => input.value = '');
            }
        }

        // Initialize edit form on page load
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize Hijri pickers for existing edit inputs
            document.querySelectorAll('.hijri-input').forEach(input => {
                const picker = input.nextElementSibling;
                if (picker) initHijriPicker(input, picker);
            });

            // Initialize date type toggles for edit form
            document.querySelectorAll('.edit-date-type').forEach(select => {
                toggleEditDateType(select); // Set initial state based on selected value
                select.addEventListener('change', function() {
                    toggleEditDateType(this);
                });
            });
        });

        // Dynamic document addition for edit form
        document.addEventListener('click', function(e) {
            // ADD new input for edit
            if (e.target.closest('.edit-document-btn')) {
                const wrapper = document.getElementById('edit-document-wrapper');
                const newDiv = document.createElement('div');
                newDiv.classList.add('mb-3', 'document-input', 'border', 'p-2', 'rounded');

                // Generate a unique index for this new block
                const existingBlocks = wrapper.querySelectorAll('.document-input');
                const newIndex = existingBlocks.length;

                newDiv.innerHTML = `
      <div class="row g-3 align-items-center document-block-inner">
        <!-- Document Name -->
        <div class="col-md-4">
          <label>{{ __('dashboard.document_name') }}</label>
          <input type="text" name="doc_name[]" class="form-control" placeholder="{{ __('dashboard.document_name') }}">
        </div>

        <!-- Date Type Toggle -->
        <div class="col-md-2">
          <label>{{ __('dashboard.date_type') }}</label>
          <select name="date_type[]" class="form-control date-type-select edit-date-type">
            <option value="hijri">{{ __('dashboard.hijri') }}</option>
            <option value="gregorian">{{ __('dashboard.gregorian') }}</option>
          </select>
        </div>

        <!-- Start Date - Hijri -->
        <div class="col-md-3 hijri-date-group">
          <label>{{ __('dashboard.start_date') }} ({{ __('dashboard.hijri') }})</label>
          <input type="text" class="hijri-input form-control" name="start_date_hijri[]" placeholder="iYYYY-iMM-iDD" readonly>
          <div class="hijri-picker" style="display:none;"></div>
        </div>

        <!-- Start Date - Gregorian (hidden initially) -->
        <div class="col-md-3 gregorian-date-group" style="display: none;">
          <label>{{ __('dashboard.start_date') }} ({{ __('dashboard.gregorian') }})</label>
          <input type="date" class="gregorian-input form-control" name="start_date_gregorian[]">
        </div>

        <!-- End Date - Hijri -->
        <div class="col-md-3 hijri-date-group">
          <label>{{ __('dashboard.end_date') }} ({{ __('dashboard.hijri') }})</label>
          <input type="text" class="hijri-input form-control" name="end_date_hijri[]" placeholder="iYYYY-iMM-iDD" readonly>
          <div class="hijri-picker" style="display:none;"></div>
        </div>

        <!-- End Date - Gregorian (hidden initially) -->
        <div class="col-md-3 gregorian-date-group" style="display: none;">
          <label>{{ __('dashboard.end_date') }} ({{ __('dashboard.gregorian') }})</label>
          <input type="date" class="gregorian-input form-control" name="end_date_gregorian[]">
        </div>

        <!-- File input -->
        <div class="col-md-11">
          <label>{{ __('dashboard.documents') }}</label>
          <input type="file" class="form-control" name="files[]">
        </div>

        <!-- Remove button -->
        <div class="col-md-1 mt-5">
          <button type="button" class="btn btn-danger btn-sm remove-document-btn ms-2">
            <i class="fas fa-minus"></i>
          </button>
        </div>

        <div class="col-12">
          <span class="text-danger error-text file_error"></span>
        </div>
      </div>
    `;

                wrapper.appendChild(newDiv);

                // Initialize Hijri pickers for newly added inputs
                newDiv.querySelectorAll('.hijri-input').forEach(input => {
                    const picker = input.nextElementSibling;
                    if (picker) initHijriPicker(input, picker);
                });

                // Initialize date type toggle for the new select
                const dateTypeSelect = newDiv.querySelector('.edit-date-type');
                dateTypeSelect.addEventListener('change', function() {
                    toggleEditDateType(this);
                });
                toggleEditDateType(dateTypeSelect); // Set initial state
            }

            // REMOVE input
            if (e.target.closest('.remove-document-btn')) {
                const block = e.target.closest('.document-input');
                if (block) block.remove();
            }
        });

        // Helper function to pre-populate existing documents in edit form
        function populateExistingEditDocuments(documents) {
            const wrapper = document.getElementById('edit-document-wrapper');

            // Clear existing dynamic inputs (keep the first one)
            const existingDynamicInputs = wrapper.querySelectorAll('.document-input');
            for (let i = 1; i < existingDynamicInputs.length; i++) {
                existingDynamicInputs[i].remove();
            }

            // Add each existing document (skip index 0 which is the main document)
            documents.forEach((doc, index) => {
                if (index === 0) return; // Skip first document as it's already in the form

                const newDiv = document.createElement('div');
                newDiv.classList.add('mb-3', 'document-input', 'border', 'p-2', 'rounded');

                newDiv.innerHTML = `
      <div class="row g-3 align-items-center document-block-inner">
        <!-- Document Name -->
        <div class="col-md-4">
          <label>{{ __('dashboard.document_name') }}</label>
          <input type="text" name="doc_name[]" class="form-control" placeholder="{{ __('dashboard.document_name') }}" value="${doc.doc_name || ''}">
        </div>

        <!-- Date Type Toggle -->
        <div class="col-md-2">
          <label>{{ __('dashboard.date_type') }}</label>
          <select name="date_type[]" class="form-control date-type-select edit-date-type">
            <option value="hijri" ${doc.date_type === 'hijri' ? 'selected' : ''}>{{ __('dashboard.hijri') }}</option>
            <option value="gregorian" ${doc.date_type === 'gregorian' ? 'selected' : ''}>{{ __('dashboard.gregorian') }}</option>
          </select>
        </div>

        <!-- Start Date - Hijri -->
        <div class="col-md-3 hijri-date-group">
          <label>{{ __('dashboard.start_date') }} ({{ __('dashboard.hijri') }})</label>
          <input type="text" class="hijri-input form-control" name="start_date_hijri[]" placeholder="iYYYY-iMM-iDD" readonly value="${doc.date_type === 'hijri' ? doc.start_date : ''}">
          <div class="hijri-picker" style="display:none;"></div>
        </div>

        <!-- Start Date - Gregorian -->
        <div class="col-md-3 gregorian-date-group" style="display: none;">
          <label>{{ __('dashboard.start_date') }} ({{ __('dashboard.gregorian') }})</label>
          <input type="date" class="gregorian-input form-control" name="start_date_gregorian[]" value="${doc.date_type === 'gregorian' ? doc.start_date : ''}">
        </div>

        <!-- End Date - Hijri -->
        <div class="col-md-3 hijri-date-group">
          <label>{{ __('dashboard.end_date') }} ({{ __('dashboard.hijri') }})</label>
          <input type="text" class="hijri-input form-control" name="end_date_hijri[]" placeholder="iYYYY-iMM-iDD" readonly value="${doc.date_type === 'hijri' ? doc.end_date : ''}">
          <div class="hijri-picker" style="display:none;"></div>
        </div>

        <!-- End Date - Gregorian -->
        <div class="col-md-3 gregorian-date-group" style="display: none;">
          <label>{{ __('dashboard.end_date') }} ({{ __('dashboard.gregorian') }})</label>
          <input type="date" class="gregorian-input form-control" name="end_date_gregorian[]" value="${doc.date_type === 'gregorian' ? doc.end_date : ''}">
        </div>

        <!-- File input (for replacement) -->
        <div class="col-md-11">
          <label>{{ __('dashboard.documents') }}</label>
          <input type="file" class="form-control" name="files[]">
          ${doc.file_path ? `<small class="text-muted d-block">Existing file: ${doc.file_name}</small>` : ''}
        </div>

        <!-- Remove button -->
        <div class="col-md-1 mt-5">
          <button type="button" class="btn btn-danger btn-sm remove-document-btn ms-2">
            <i class="fas fa-minus"></i>
          </button>
        </div>

        <div class="col-12">
          <span class="text-danger error-text file_error"></span>
        </div>
      </div>
    `;

                wrapper.appendChild(newDiv);

                // Initialize Hijri pickers
                newDiv.querySelectorAll('.hijri-input').forEach(input => {
                    const picker = input.nextElementSibling;
                    if (picker) initHijriPicker(input, picker);
                });

                // Initialize date type toggle
                const dateTypeSelect = newDiv.querySelector('.edit-date-type');
                dateTypeSelect.addEventListener('change', function() {
                    toggleEditDateType(this);
                });

                // Set initial state based on the document's date type
                setTimeout(() => {
                    toggleEditDateType(dateTypeSelect);
                }, 10);
            });
        }

        $(document).on('click', '.viewBranchBtn', function(e) {
            e.preventDefault();
            const branchId = $(this).data('id');
            // $('#branch-docs-' + branchId).toggleClass('d-none');
        });
    </script>
@endsection
