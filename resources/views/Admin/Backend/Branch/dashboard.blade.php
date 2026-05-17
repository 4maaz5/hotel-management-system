@extends('layout.master')
@section('title', 'Dashboard | Branch')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="row ">
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row ">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                        <div class="card-content">
                                            <h5 class="font-15">{{ __('dashboard.total_branches') }}</h5>
                                            <h2 class="mb-3 font-18">{{ $totalBranches }}</h2>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                        <div class="banner-img">
                                            <img src="{{ asset('img/banner/1.jpg') }}" alt="Image Not Found">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row ">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                        <div class="card-content">
                                            <h5 class="font-15">{{ __('dashboard.active_branches') }}</h5>
                                            <h2 class="mb-3 font-18">{{ $activeBranches }}</h2>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                        <div class="banner-img">
                                            <img src="{{ asset('img/banner/2.jpg') }}" alt="Image Not Found">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row ">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                        <div class="card-content">
                                            <h5 class="font-15">{{ __('dashboard.inactive_branches') }}</h5>
                                            <h2 class="mb-3 font-18">{{ $inActiveBranches }}</h2>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                        <div class="banner-img">
                                            <img src="{{ asset('img/banner/3.jpg') }}" alt="Image Not Found">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="card">
                        <div class="card-statistic-4">
                            <div class="align-items-center justify-content-between">
                                <div class="row ">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                        <div class="card-content">
                                            <h5 class="font-15">{{ __('dashboard.total_managers') }}</h5>
                                            <h2 class="mb-3 font-18">{{ $totalManagers }}</h2>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                        <div class="banner-img">
                                            <img src="{{ asset('img/banner/4.jpg') }}" alt="Image Not Found">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                                        class="branch-email">{{ $branch->email }}</span>
                                    <br>
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

            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>{{ __('dashboard.all_branches') }}</h4>
                        {{-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addBranchModal">
                            {{ __('dashboard.add_branch') }}
                        </button> --}}
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
                                        <input type="text" name="location" id="filter_location" class="form-control"
                                            placeholder="{{ __('dashboard.search_by_location') }}">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('dashboard.manager') }}</label>
                                        <input type="text" name="manager" id="filter_manager" class="form-control"
                                            placeholder="">
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
                        <form id="addBranchForm" method="POST" action="{{ route('dashboard.branch.store') }}">
                            @csrf

                            <div class="form-group">
                                <label>{{ __('dashboard.branch_name') }}</label>
                                <input type="text" name="branch_name" id="branch_name" class="form-control">
                                <div class="invalid-feedback" id="error-branch_name"></div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.company_name') }}</label>
                                    <select name="company_id" id="company_id" class="form-control">
                                        <option value="">{{ __('dashboard.select_company') }}</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}" @selected($companies->count() === 1)>{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="error-company_id"></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.brand_name') }}</label>
                                    <select name="brand_id" id="brand_id" class="form-control">
                                        <option value="">{{ __('dashboard.select_brand') }}</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}" data-company-id="{{ $brand->company_id }}" @selected($brands->count() === 1)>{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="error-brand_id"></div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.location_address') }}</label>
                                    <input type="text" name="branch_address" id="branch_address"
                                        class="form-control">
                                    <div class="invalid-feedback" id="error-branch_address"></div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.contact_person_manager') }}</label>
                                    <input type="text" name="branch_manager" id="branch_manager"
                                        class="form-control">
                                    <div class="invalid-feedback" id="error-branch_manager"></div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.email') }}</label>
                                    <input type="email" name="branch_email" id="branch_email" class="form-control">
                                    <div class="invalid-feedback" id="error-branch_email"></div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.phone') }}</label>
                                    <input type="text" name="branch_phone" id="branch_phone" class="form-control">
                                    <div class="invalid-feedback" id="error-branch_phone"></div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('dashboard.status') }}</label>
                                <select name="branch_status" id="branch_status" class="form-control">
                                    <option value="Active">{{ __('dashboard.active') }}</option>
                                    <option value="Inactive">{{ __('dashboard.inactive') }}</option>
                                </select>
                                <div class="invalid-feedback" id="error-branch_status"></div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('dashboard.building_type') }}</label>
                                <select name="building_type" id="building_type" class="form-control">
                                    <option value="owned">{{ __('dashboard.owned') }}</option>
                                    <option value="rented">{{ __('dashboard.rented') }}</option>
                                </select>
                                <div class="invalid-feedback" id="error-building_type"></div>
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


                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.sale_price') }}</label>
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
                                    <label>{{ __('dashboard.rent') }}</label>
                                    <input type="number" name="rent" id="edit_rent" class="form-control">
                                    <div class="invalid-feedback" id="error-branch_phone"></div>
                                </div>
                            </div>

                            <div class="form-row">

                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.rent_start_date') }}</label>
                                    <input type="date" name="rent_start_date" id="edit_rent_start_date"
                                        class="form-control">
                                    <div class="invalid-feedback" id="error-branch_phone"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.rent_end_date') }}</label>
                                    <input type="date" name="rent_end_date" id="edit_rent_end_date"
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

                                <!-- Start Date -->
                                <div class="col-md-6">
                                    <label>{{ __('dashboard.start_date') }}</label>
                                    <input type="date" name="start_date[]" id="edit_start_date" class="form-control">
                                </div>

                                <!-- End Date -->
                                <div class="col-md-6">
                                    <label>{{ __('dashboard.end_date') }}</label>
                                    <input type="date" name="end_date[]" id="edit_end_date" class="form-control">
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
                                <label class="fw-bold">{{ __('dashboard.sale_price') }}:</label>
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
                        <!-- Documents Row for the Branch -->
                        <tr id="branch-docs-{{ $branch->id ?? '' }}" class="bg-light d-none">
                            <td colspan="7">
                                <h6>{{ __('dashboard.documents') }}</h6>
                                @if ($branch->documents ?? '' && $branch->documents->count() > 0)
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('dashboard.document_name') }}</th>
                                                <th>{{ __('dashboard.issue_date') }}</th>
                                                <th>{{ __('dashboard.expiration_date') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($branch->documents as $doc)
                                                <tr>
                                                    <td>{{ $doc->name }}</td>
                                                    <td>{{ $doc->issue_date ?? '-' }}</td>
                                                    <td>{{ $doc->expiration_date ?? '-' }}</td>
                                                    <td>
                                                        <a href="{{ asset('storage/' . $doc->file_path) }}"
                                                            target="_blank"
                                                            class="btn btn-sm btn-info">{{ __('dashboard.view') }}</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p class="text-muted mb-0"></p>
                                @endif
                            </td>
                        </tr>
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

                $.ajax({
                        url: $form.attr('action'),
                        method: 'POST',
                        data: $form.serialize(),
                        dataType: 'json'
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
                            });
                        }
                    })
                    .fail(function(xhr) {
                        if (xhr.status === 422) {

                            // Display inline validation errors
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
                                title: 'Server Error/خطأ في الخادم',
                                text: xhr.responseJSON?.message || 'Something went wrong'
                            });
                        }
                    })
                    .always(() => {
                        $btn.prop('disabled', false);
                        $spinner?.addClass('d-none');
                    });
            });

            // ----------------------
            // Open View Modal
            // ----------------------
            $(document).on('click', '.viewBranchBtn', function(e) {
                e.preventDefault();
                const $btn = $(this);

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

                $('#viewBranchModal').modal('show');
            });

            // ----------------------
            //  Open Edit Modal
            // ----------------------
            $(document).on('click', '.editBranchBtn', function() {

                const data = $(this).data();

                //RESET form to avoid stale values
                $('#editBranchForm')[0].reset();

                // Basic info
                $('#edit_branch_id').val(data.id);
                $('#edit_branch_name').val(data.name);
                $('#edit_branch_location').val(data.location);
                $('#edit_branch_manager').val(data.manager);
                $('#edit_branch_email').val(data.email);
                $('#edit_branch_phone').val(data.phone);

                // Financial info
                $('#edit_market_price').val(data.market_price);
                $('#edit_sale_price').val(data.sale_price);
                $('#edit_rent').val(data.rent);
                $('#edit_damage_assist').val(data.damage_assist);

                $('#edit_rent_start_date').val(data.rent_start_date);
                $('#edit_rent_end_date').val(data.rent_end_date);
                $('#edit_start_date').val(data.start_date);
                $('#edit_end_date').val(data.end_date);


                // Status
                $('#edit_branch_status').val(data.status);

                $('#editBranchModal').modal('show');
            });

            // ----------------------
            //  Submit Edit Form via AJAX
            // ----------------------
            $(document).on('submit', '#editBranchForm', function(e) {
                e.preventDefault();
                const $form = $(this);
                const $btn = $form.find('button[type="submit"]');
                const $spinner = $form.find('.spinner-border');

                $btn.prop('disabled', true);
                $spinner.removeClass('d-none');

                // Clear previous errors
                $form.find('.is-invalid').removeClass('is-invalid');
                $form.find('[id^="error-"]').text('');

                $.ajax({
                        url: $form.attr('action'),
                        method: 'POST', // Laravel will treat PUT via _method
                        data: $form.serialize(),
                        dataType: 'json'
                    })
                    .done(function(res) {
                        if (res.success) {
                            $('#editBranchModal').modal('hide');

                            // Find the row
                            // Update table row dynamically
                            let row = $('#branch-row-' + res.data.id);

                            // Update table cells
                            row.find('.branch-name-cell').text(res.data.name);
                            row.find('.branch-location-cell').text(res.data.location);
                            row.find('.branch-manager-cell').text(res.data.manager);
                            row.find('.branch-email-cell').text(res.data.email);
                            row.find('.branch-phone-cell').text(res.data.phone);
                            row.find('.branch-status-cell').html(
                                res.data.status === 'Active' ?
                                `<span class="badge badge-success">${res.data.status}</span>` :
                                `<span class="badge badge-danger">${res.data.status}</span>`
                            );

                            // Update data attributes for buttons
                            let editBtn = row.find('.editBranchBtn');
                            let viewBtn = row.find('.viewBranchBtn');

                            [editBtn, viewBtn].forEach(btn => {
                                btn.data('name', res.data.name);
                                btn.data('location', res.data.location);
                                btn.data('manager', res.data.manager);
                                btn.data('email', res.data.email);
                                btn.data('phone', res.data.phone);
                                btn.data('status', res.data.status);
                            });


                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!/تم التحديث!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    })
                    .fail(function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors || {};
                            $.each(errors, function(field, messages) {
                                $('#error-' + field).text(messages[0]).closest('input, select')
                                    .addClass('is-invalid');
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error/خطأ',
                                text: xhr.responseJSON?.message || 'Something went wrong'
                            });
                        }
                    })
                    .always(function() {
                        $btn.prop('disabled', false);
                        $spinner.addClass('d-none');
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
                const statusClass = branch.status.trim() === 'Active' ? 'bg-success' : 'bg-secondary';

                const html = `

<tr id="branch-row-${branch.id}">
  <td>${branch.name}</td>
  <td>${branch.location}</td>
  <td>${branch.manager}</td>
  <td>${branch.email}</td>
  <td>${branch.phone}</td>
  <td><span class="badge ${statusClass}">${branch.status}</span></td>
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
                    row.find('td:nth-child(1)').text(branch.name);
                    row.find('td:nth-child(2)').text(branch.location);
                    row.find('td:nth-child(3)').text(branch.manager);
                    row.find('td:nth-child(4)').text(branch.email);
                    row.find('td:nth-child(5)').text(branch.phone);

                    // Update status badge robustly
                    const badge = row.find('td:nth-child(6) span');
                    const status = branch.status.trim().toLowerCase();
                    let statusClass = 'bg-secondary'; // default
                    if (status === 'active') statusClass = 'bg-success';
                    badge.text(branch.status) // keep original text
                        .removeClass('bg-success bg-primary')
                        .addClass(statusClass);
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
            const companyId = $(this).val();

            $('#brand_id').html('<option value="">{{ __('dashboard.select_brand') }}</option>');

            if (!companyId) {
                return;
            }

            $.ajax({
                url: '/get-brands/' + companyId,
                type: 'GET',
                success: function(data) {
                    $('#brand_id').empty().append('<option value="">{{ __('dashboard.select_brand') }}</option>');

                    $.each(data, function(_, brand) {
                        $('#brand_id').append(
                            '<option value="' + brand.id + '">' + brand.name + '</option>'
                        );
                    });

                    if (data.length === 1) {
                        $('#brand_id').val(data[0].id);
                    }
                }
            });
        });

        document.addEventListener('click', function(e) {

            // ADD new input
            if (e.target.closest('.add-document-btn')) {
                const wrapper = document.getElementById('document-wrapper');

                const newDiv = document.createElement('div');
                newDiv.classList.add('mb-3', 'document-block'); // wrapper for each document block

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
                <div class="col-md-6">
                    <label>{{ __('dashboard.start_date') }}</label>
                    <input type="date" name="start_date[]" class="form-control">
                </div>
                <div class="col-md-6">
                    <label>{{ __('dashboard.end_date') }}</label>
                    <input type="date" name="end_date[]" class="form-control">
                </div>
            </div>
        `;

                wrapper.appendChild(newDiv);
            }

            // REMOVE input
            if (e.target.closest('.remove-document-btn')) {
                e.target.closest('.document-block').remove();
            }

        });


        document.addEventListener('click', function(e) {

            // ADD new input for edit
            if (e.target.closest('.edit-document-btn')) {
                const wrapper = document.getElementById('edit-document-wrapper');

                const newDiv = document.createElement('div');
                newDiv.classList.add('mb-3', 'document-input', 'border', 'p-2', 'rounded');

                newDiv.innerHTML = `
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="file" class="form-control" name="files[]">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" name="start_date[]">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" name="end_date[]">
                </div>
                <div class="col-md-2 d-flex align-items-center">
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
            }

            // REMOVE input
            if (e.target.closest('.remove-document-btn')) {
                e.target.closest('.document-input').remove();
            }

        });
        $(document).on('click', '.viewBranchBtn', function(e) {
            e.preventDefault();
            const branchId = $(this).data('id');
            // $('#branch-docs-' + branchId).toggleClass('d-none');
        });
    </script>
@endsection
