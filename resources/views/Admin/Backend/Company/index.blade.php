@extends('layout.master')
@section('title', 'Dashboard | Company')
@section('main')
    <!-- Main Content -->
    <div class="main-content">

        <h2 class="text-center all-branches-title">{{ __('dashboard.all_companies') }}</h2>
        <div class="row" id="companyCardContainer">

            @forelse ($companyCards as $company)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-4">
                    <div class="card shadow-sm h-100">

                        <!-- Logo -->
                        <div class="text-center p-3">
                            @if ($company->logo)
                                <img src="{{ asset('storage/' . $company->logo) }}" class="img-fluid rounded"
                                    style="height:70px; object-fit:cover;">
                            @else
                                <span class="badge badge-secondary">No Logo</span>
                            @endif
                        </div>

                        <div class="card-body text-center">
                            <h6 class="fw-bold mb-1">{{ $company->legal_name }}</h6>

                            <p class="text-muted small mb-0">{{ $company->address }}</p>
                            <p class="text-muted small mb-0">{{ $company->email }}</p>
                            <p class="text-muted small">{{ $company->phone }}</p>
                        </div>

                    </div>
                </div>
            @empty
            @endforelse

        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $companyCards->links('pagination::bootstrap-4') }}
        </div>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.companies') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addCompanyModal">
                                    {{ __('dashboard.add_company') }}
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <form id="companyFilterForm" class="mb-3">
                                        <div class="row g-2 align-items-end">

                                            <!-- Company Name -->
                                            <div class="col-md-3">
                                                <label class="form-label">{{ __('dashboard.company_name') }}</label>
                                                <input type="text" name="name" id="filter_company_name"
                                                    class="form-control"
                                                    placeholder="{{ __('dashboard.search_by_name') }}">
                                            </div>

                                            {{-- <!-- Legal Name -->
                                            <div class="col-md-3">
                                                <label class="form-label">{{ __('dashboard.legal_name') }}</label>
                                                <input type="text" name="legal_name" id="filter_legal_name"
                                                    class="form-control" placeholder="Search by legal name">
                                            </div> --}}

                                            <!-- CR Number -->
                                            <div class="col-md-3">
                                                <label class="form-label">{{ __('dashboard.cr_number') }}</label>
                                                <input type="text" name="cr_number" id="filter_cr_number"
                                                    class="form-control"
                                                    placeholder="{{ __('dashboard.search_by_cr_number') }}">
                                            </div>

                                            <!-- VAT Number -->
                                            <div class="col-md-3">
                                                <label class="form-label">{{ __('dashboard.vat_number') }}</label>
                                                <input type="text" name="vat_number" id="filter_vat_number"
                                                    class="form-control"
                                                    placeholder="{{ __('dashboard.search_by_vat_number') }}">
                                            </div>

                                            <!-- Email -->
                                            <div class="col-md-3">
                                                <label class="form-label">{{ __('dashboard.email') }}</label>
                                                <input type="text" name="email" id="filter_company_email"
                                                    class="form-control"
                                                    placeholder="{{ __('dashboard.search_by_email') }}">
                                            </div>

                                            {{-- <!-- Phone -->
                                            <div class="col-md-3">
                                                <label class="form-label">{{ __('dashboard.phone') }}</label>
                                                <input type="text" name="phone" id="filter_company_phone"
                                                    class="form-control" placeholder="Search by phone">
                                            </div> --}}

                                            <!-- City -->
                                            <div class="col-md-3">
                                                <label class="form-label">{{ __('dashboard.city') }}</label>
                                                <input type="text" name="city" id="filter_city" class="form-control"
                                                    placeholder="{{ __('dashboard.search_by_city') }}">
                                            </div>

                                            {{-- <!-- Industry Type -->
                                            <div class="col-md-3">
                                                <label class="form-label">{{ __('dashboard.industry_type') }}</label>
                                                <input type="text" name="industry_type" id="filter_industry_type"
                                                    class="form-control" placeholder="Search by industry">
                                            </div> --}}

                                            {{-- <!-- Status -->
                                            <div class="col-md-3">
                                                <label class="form-label">{{ __('dashboard.status') }}</label>
                                                <select name="is_active" id="filter_status" class="form-control">
                                                    <option value="">All Status</option>
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div> --}}

                                            <!-- CR Expiry Date Range -->
                                            <div class="col-md-3">
                                                <label class="form-label">{{ __('dashboard.cr_expiry_form') }}</label>
                                                <input type="date" name="cr_expiry_from" id="filter_cr_expiry_from"
                                                    class="form-control">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">{{ __('dashboard.cr_expiry_to') }}</label>
                                                <input type="date" name="cr_expiry_to" id="filter_cr_expiry_to"
                                                    class="form-control">
                                            </div>

                                            <!-- Buttons -->
                                            <div class="col-md-12 mt-3">
                                                <button type="button" id="companyFilterBtn" class="btn btn-primary">
                                                    <i class="fas fa-filter"></i> {{ __('dashboard.filter') }}
                                                </button>
                                                {{-- <button type="button" id="companyResetBtn" class="btn btn-secondary">
                                                    <i class="fas fa-redo"></i> Reset
                                                </button> --}}
                                            </div>

                                        </div>
                                    </form>

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>{{ __('dashboard.logo') }}</th>
                                                <th>{{ __('dashboard.company_name') }}</th>
                                                <th>{{ __('dashboard.cr_number') }}</th>
                                                <th>{{ __('dashboard.cr_expiry') }}</th>
                                                <th>{{ __('dashboard.vat_number') }}</th>
                                                <th>{{ __('dashboard.city') }}</th>
                                                <th>{{ __('dashboard.email') }}</th>
                                                <th>{{ __('dashboard.phone') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @forelse ($companies as $company)
                                                <tr id="company-row-{{ $company->id }}">
                                                    <!-- Company Logo -->
                                                    <td class="company-logo-cell">
                                                        @if ($company->logo)
                                                            <img src="{{ asset('storage/' . $company->logo) }}"
                                                                alt="Logo" width="45" height="45"
                                                                style="object-fit: cover; border-radius: 6px;">
                                                        @else
                                                            <span class="badge badge-secondary">No Logo</span>
                                                        @endif
                                                    </td>

                                                    <!-- Company Name EN -->
                                                    <td class="company-name-en-cell">{{ $company->legal_name }}</td>



                                                    <!-- CR Number -->
                                                    <td class="company-cr-number-cell">{{ $company->cr_number ?? '-' }}
                                                    </td>

                                                    <!-- CR Expiry -->
                                                    <td class="company-cr-expiry-cell">
                                                        {{ $company->cr_expiry ?? '-' }}
                                                    </td>

                                                    <!-- VAT Number -->
                                                    <td class="company-vat-cell">{{ $company->vat_number ?? '-' }}</td>

                                                    <!-- City -->
                                                    <td class="company-city-cell">{{ $company->city ?? '-' }}</td>

                                                    <!-- Email -->
                                                    <td class="company-email-cell">{{ $company->email ?? '-' }}</td>

                                                    <!-- Phone -->
                                                    <td class="company-phone-cell">{{ $company->phone ?? '-' }}</td>

                                                    <!-- Action Buttons -->
                                                    <td>
                                                        <!-- View -->
                                                        <a href="#" class="text-info viewCompanyBtn"
                                                            data-id="{{ $company->id }}"
                                                            data-name="{{ $company->name }}"
                                                            data-legal_name="{{ $company->legal_name ?? '' }}"
                                                            data-cr_number="{{ $company->cr_number ?? '' }}"
                                                            data-cr_expiry="{{ $company->cr_expiry ?? '' }}"
                                                            data-vat_number="{{ $company->vat_number ?? '' }}"
                                                            data-logo="{{ $company->logo ?? '' }}"
                                                            data-street="{{ $company->street ?? '' }}"
                                                            data-district="{{ $company->district ?? '' }}"
                                                            data-city="{{ $company->city ?? '' }}"
                                                            data-zip_code="{{ $company->zip_code ?? '' }}"
                                                            data-website="{{ $company->website ?? '' }}"
                                                            data-industry_type="{{ $company->industry_type ?? '' }}"
                                                            data-is_active="{{ $company->is_active ?? 1 }}"
                                                            data-email="{{ $company->email ?? '' }}"
                                                            data-phone="{{ $company->phone ?? '' }}">
                                                            <i class="fas fa-eye"></i>
                                                        </a>



                                                        <!-- Edit -->
                                                        <a href="#" class="text-secondary editCompanyBtn"
                                                            data-id="{{ $company->id }}"
                                                            data-name="{{ $company->name }}"
                                                            data-legal_name="{{ $company->legal_name ?? '' }}"
                                                            data-cr_number="{{ $company->cr_number ?? '' }}"
                                                            data-cr_expiry="{{ $company->cr_expiry ?? '' }}"
                                                            data-vat_number="{{ $company->vat_number ?? '' }}"
                                                            data-logo="{{ $company->logo ?? '' }}"
                                                            data-street="{{ $company->street ?? '' }}"
                                                            data-district="{{ $company->district ?? '' }}"
                                                            data-city="{{ $company->city ?? '' }}"
                                                            data-zip_code="{{ $company->zip_code ?? '' }}"
                                                            data-website="{{ $company->website ?? '' }}"
                                                            data-industry_type="{{ $company->industry_type ?? '' }}"
                                                            data-is_active="{{ $company->is_active ?? 1 }}"
                                                            data-email="{{ $company->email ?? '' }}"
                                                            data-phone="{{ $company->phone ?? '' }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>


                                                        <!-- Delete -->
                                                        <a href="#" class="text-danger deleteCompanyBtn"
                                                            data-id="{{ $company->id }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- Add Company Modal -->
        <div class="modal fade" id="addCompanyModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content p-2">

                    <div class="modal-header py-2">
                        <h6 class="modal-title">{{ __('dashboard.add_new_company') }}</h6>
                        <button type="button" class="close text-dark" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body p-3">
                        <form id="addCompanyForm" method="POST" action="{{ route('dashboard.company.store') }}"
                            enctype="multipart/form-data">
                            @csrf

                            <!-- Name & Legal Name -->
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.company_name') }}</label>
                                    <input type="text" name="name" class="form-control form-control-sm">
                                    <div class="invalid-feedback" id="error-name"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.legal_name') }}</label>
                                    <input type="text" name="legal_name" class="form-control form-control-sm">
                                    <div class="invalid-feedback" id="error-legal_name"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.cr_number') }}</label>
                                    <input type="text" name="cr_number" class="form-control form-control-sm">
                                    <div class="invalid-feedback" id="error-cr_number"></div>
                                </div>
                            </div>

                            <!-- CR Number & CR Expiry -->
                            <div class="form-row">

                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.cr_expiry') }}</label>
                                    <input type="date" name="cr_expiry" class="form-control form-control-sm">
                                    <div class="invalid-feedback" id="error-cr_expiry"></div>
                                </div>
                                <!-- VAT Number -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.vat_number') }}</label>
                                    <input type="text" name="vat_number" class="form-control form-control-sm">
                                    <div class="invalid-feedback" id="error-vat_number"></div>
                                </div>
                            </div>
                            <!-- Contact Info -->
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.email') }}</label>
                                    <input type="email" name="email" class="form-control form-control-sm">
                                    <div class="invalid-feedback" id="error-email"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.phone') }}</label>
                                    <input type="text" name="phone" class="form-control form-control-sm">
                                    <div class="invalid-feedback" id="error-phone"></div>
                                </div>
                                <!-- Logo -->
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.logo') }}</label>
                                    <input type="file" name="logo" class="form-control form-control-sm">
                                    <div class="invalid-feedback" id="error-logo"></div>
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.street') }}</label>
                                    <input type="text" name="street" class="form-control form-control-sm">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.district') }}</label>
                                    <input type="text" name="district" class="form-control form-control-sm">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.city') }}</label>
                                    <input type="text" name="city" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.zip_code') }}</label>
                                    <input type="text" name="zip_code" class="form-control form-control-sm">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.website') }}</label>
                                    <input type="text" name="website" class="form-control form-control-sm">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.industry_type') }}</label>
                                    <input type="text" name="industry_type" class="form-control form-control-sm">
                                </div>
                            </div>

                            <!-- Industry & Active -->
                            <div class="form-row">

                                <div class="form-group col-md-12">
                                    <label>{{ __('dashboard.active') }}?</label>
                                    <select name="is_active" class="form-control form-control-sm">
                                        <option value="1" selected>Yes/نعم</option>
                                        <option value="0">No/لا</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <!-- Document Name -->
                                <div class="form-group col-md-4">
                                    <label class="form-label">{{ __('dashboard.document_name') }}</label>
                                    <input type="text" class="form-control"
                                        placeholder="e.g., {{ __('dashboard.trade_license') }}" name="name">
                                    <span class="text-danger error-text name_error"></span>
                                </div>

                                <!-- Document Type -->
                                <div class="form-group col-md-4">
                                    <label class="form-label">{{ __('dashboard.document_type') }}</label>
                                    <select class="form-control" name="type">
                                        <option selected disabled>{{ __('dashboard.select_type') }}</option>
                                        <option>{{ __('dashboard.legal') }}</option>
                                        <option>{{ __('dashboard.license') }}</option>
                                        <option>{{ __('dashboard.contract') }}</option>
                                        <option>{{ __('dashboard.tax_certificate') }}</option>
                                        <option>{{ __('dashboard.policy') }}</option>
                                        <option>{{ __('dashboard.other') }}</option>
                                    </select>
                                    <span class="text-danger error-text type_error"></span>
                                </div>

                                <!-- Issued By -->
                                <div class="form-group col-md-4">
                                    <label class="form-label">{{ __('dashboard.issue_by') }}</label>
                                    <input type="text" class="form-control"
                                        placeholder="e.g.,{{ __('dashboard.ministry_of_commerce') }}" name="issued_by">
                                    <span class="text-danger error-text issue_by_error"></span>
                                </div>

                                <!-- Issue Date -->

                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="form-label">{{ __('dashboard.issue_date') }}</label>
                                    <input type="date" class="form-control" name="issue_date">
                                    <span class="text-danger error-text issue_date_error"></span>
                                </div>

                                <!-- Expiry Date -->
                                <div class="form-group col-md-4">
                                    <label class="form-label">{{ __('dashboard.expiry_date') }}</label>
                                    <input type="date" class="form-control" name="expiration_date">
                                    <span class="text-danger error-text expiration_date_error"></span>
                                </div>

                                <!-- Upload File -->
                                <div class="form-group col-md-4">
                                    <label class="form-label">{{ __('dashboard.upload_document') }} (PDF)</label>
                                    <input type="file" class="form-control" name="file">
                                    <span class="text-danger error-text file_error"></span>
                                </div>
                            </div>

                            <div class="text-right mt-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <span class="spinner-border spinner-border-sm" style="display:none;"></span>
                                    {{ __('dashboard.save_company') }}
                                </button>
                            </div>

                        </form>


                    </div>

                </div>
            </div>
        </div>

        <!-- Edit Company Modal -->
        <div class="modal fade" id="editCompanyModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content p-2">

                    <div class="modal-header py-2">
                        <h6 class="modal-title">{{ __('dashboard.edit_company') }}</h6>
                        <button type="button" class="close text-dark" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body p-3">
                        <form id="editCompanyForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="edit_company_id" name="company_id">

                            <!-- Name & Legal Name -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.company_name') }}</label>
                                    <input type="text" name="name" id="edit_name"
                                        class="form-control form-control-sm">
                                    <div class="invalid-feedback" id="error-edit-name"></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.legal_name') }}</label>
                                    <input type="text" name="legal_name" id="edit_legal_name"
                                        class="form-control form-control-sm">
                                    <div class="invalid-feedback" id="error-edit-legal_name"></div>
                                </div>
                            </div>

                            <!-- CR Number & CR Expiry -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.cr_number') }}</label>
                                    <input type="text" name="cr_number" id="edit_cr_number"
                                        class="form-control form-control-sm">
                                    <div class="invalid-feedback" id="error-edit-cr_number"></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.cr_expiry') }}</label>
                                    <input type="date" name="cr_expiry" id="edit_cr_expiry"
                                        class="form-control form-control-sm">
                                    <div class="invalid-feedback" id="error-edit-cr_expiry"></div>
                                </div>
                            </div>

                            <!-- VAT Number -->
                            <div class="form-group">
                                <label>{{ __('dashboard.vat_number') }}</label>
                                <input type="text" name="vat_number" id="edit_vat_number"
                                    class="form-control form-control-sm">
                                <div class="invalid-feedback" id="error-edit-vat_number"></div>
                            </div>

                            <!-- Logo -->
                            <div class="form-group">
                                <label>{{ __('dashboard.logo') }}</label>
                                <input type="file" name="logo" id="edit_logo"
                                    class="form-control form-control-sm">
                                <div class="invalid-feedback" id="error-edit-logo"></div>

                                <div class="mt-2" id="currentLogoWrap" style="display:none;">
                                    <img id="currentLogoImg" src="" alt="logo"
                                        style="margin:10px;height:60px;object-fit:cover;border-radius:6px;">
                                </div>
                            </div>

                            <!-- Contact Info -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.email') }}</label>
                                    <input type="email" name="email" id="edit_email"
                                        class="form-control form-control-sm">
                                    <div class="invalid-feedback" id="error-edit-email"></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.phone') }}</label>
                                    <input type="text" name="phone" id="edit_phone"
                                        class="form-control form-control-sm">
                                    <div class="invalid-feedback" id="error-edit-phone"></div>
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.street') }}</label>
                                    <input type="text" name="street" id="edit_street"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.district') }}</label>
                                    <input type="text" name="district" id="edit_district"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>{{ __('dashboard.city') }}</label>
                                    <input type="text" name="city" id="edit_city"
                                        class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.zip_code') }}</label>
                                    <input type="text" name="zip_code" id="edit_zip_code"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.website') }}</label>
                                    <input type="text" name="website" id="edit_website"
                                        class="form-control form-control-sm">
                                </div>
                            </div>

                            <!-- Industry & Active -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.industry_type') }}</label>
                                    <input type="text" name="industry_type" id="edit_industry_type"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.active') }}?</label>
                                    <select name="is_active" id="edit_is_active" class="form-control form-control-sm">
                                        <option value="1">Yes/نعم</option>
                                        <option value="0">No/لا</option>
                                    </select>
                                </div>
                            </div>

                            <div class="text-right mt-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <span class="spinner-border spinner-border-sm" style="display:none;"></span>
                                    {{ __('dashboard.update_company') }}
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- View Company Modal -->
        <div class="modal fade" id="viewCompanyModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content p-2">

                    <div class="modal-header py-2">
                        <h6 class="modal-title">{{ __('dashboard.company_details') }}</h6>
                        <button type="button" class="close text-dark" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body p-3">

                        <!-- Logo -->
                        <div class="text-center mb-3">
                            <img id="viewCompanyLogo" src="" alt="Company logo"
                                style="max-height:70px; object-fit:cover; border-radius:6px; display:none;">
                            <div id="viewNoLogo" class="badge badge-secondary" style="display:none;">No Logo</div>
                        </div>

                        <dl class="row small">

                            {{-- <dt class="col-sm-5">Name</dt>
                            <dd class="col-sm-7" id="viewCompanyName">-</dd> --}}

                            <dt class="col-sm-5">{{ __('dashboard.legal_name') }}</dt>
                            <dd class="col-sm-7" id="viewCompanyLegalName">-</dd>

                            <dt class="col-sm-5">{{ __('dashboard.cr_number') }}</dt>
                            <dd class="col-sm-7" id="viewCompanyCRNumber">-</dd>

                            <dt class="col-sm-5">{{ __('dashboard.cr_expiry') }}</dt>
                            <dd class="col-sm-7" id="viewCompanyCRExpiry">-</dd>

                            <dt class="col-sm-5">{{ __('dashboard.vat_number') }}</dt>
                            <dd class="col-sm-7" id="viewCompanyVAT">-</dd>

                            <dt class="col-sm-5">{{ __('dashboard.website') }}</dt>
                            <dd class="col-sm-7" id="viewCompanyWebsite">-</dd>

                            <dt class="col-sm-5">{{ __('dashboard.industry_type') }}</dt>
                            <dd class="col-sm-7" id="viewCompanyIndustry">-</dd>

                            <dt class="col-sm-5">{{ __('dashboard.active') }}</dt>
                            <dd class="col-sm-7" id="viewCompanyActive">-</dd>

                            <dt class="col-sm-5">{{ __('dashboard.street') }}</dt>
                            <dd class="col-sm-7" id="viewCompanyStreet">-</dd>

                            <dt class="col-sm-5">{{ __('dashboard.district') }}</dt>
                            <dd class="col-sm-7" id="viewCompanyDistrict">-</dd>

                            <dt class="col-sm-5">{{ __('dashboard.city') }}</dt>
                            <dd class="col-sm-7" id="viewCompanyCity">-</dd>

                            <dt class="col-sm-5">{{ __('dashboard.zip_code') }}</dt>
                            <dd class="col-sm-7" id="viewCompanyPostalCode">-</dd>

                            <dt class="col-sm-5">{{ __('dashboard.email') }}</dt>
                            <dd class="col-sm-7" id="viewCompanyEmail">-</dd>

                            <dt class="col-sm-5">{{ __('dashboard.phone') }}</dt>
                            <dd class="col-sm-7" id="viewCompanyPhone">-</dd>


                        </dl>

                    </div>

                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                            {{ __('dashboard.close') }}
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Delete Company Modal -->
        <div class="modal fade" id="deleteCompanyModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">{{ __('dashboard.delete_company') }}</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <p>{{ __('dashboard.confirm_delete_modal') }}</p>
                    </div>

                    <form id="deleteCompanyForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                            <button type="button" id="confirmDeleteCompany"
                                class="btn btn-danger">{{ __('dashboard.delete') }}</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function() {
            // CSRF setup for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });



            // Submit add company form
            $(document).on('submit', '#addCompanyForm', function(e) {
                e.preventDefault();
                const $form = $(this);
                const $btn = $form.find('button[type="submit"]');
                const $spinner = $form.find('.spinner-border');

                // Clear previous errors
                $form.find('.is-invalid').removeClass('is-invalid');
                $form.find('[id^="error-"]').text('');

                $btn.prop('disabled', true);
                $spinner.show();

                const formData = new FormData(this);

                $.ajax({
                        url: $form.attr('action'),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json'
                    })
                    .done(function(res) {
                        if (res.success) {
                            $('#addCompanyModal').modal('hide');
                            $form[0].reset();

                            // Append the newly created row with correct data structure
                            appendCompanyRow(res.data);

                            Swal.fire({
                                icon: 'success',
                                title: 'Created!/تم الإنشاء!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Error', res.message || 'Something went wrong', 'error');
                        }
                    })
                    .fail(function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors || {};
                            $.each(errors, function(field, messages) {
                                const $el = $form.find('[name="' + field + '"]');
                                $el.addClass('is-invalid');
                                $('#error-' + field).text(messages[0]);
                            });
                        } else if (xhr.status === 419 || xhr.status === 401) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Session Expired/انتهت الجلسة',
                                text: 'Please login again/يرجى تسجيل الدخول مرة أخرى'
                            }).then(() => window.location = "{{ route('login') }}");
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Server Error/خطأ في الخادم',
                                text: xhr.responseJSON?.message || 'Something went wrong'
                            });
                        }
                    })
                    .always(function() {
                        $btn.prop('disabled', false);
                        $spinner.hide();
                    });
            });

            // Edit Company Button Click
            $(document).on('click', '.editCompanyBtn', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const company = $btn.data();

                // Populate form fields
                $('#edit_company_id').val(company.id);
                $('#edit_name').val(company.name);
                $('#edit_legal_name').val(company.legal_name);
                $('#edit_cr_number').val(company.cr_number);
                $('#edit_cr_expiry').val(company.cr_expiry);
                $('#edit_vat_number').val(company.vat_number);
                $('#edit_email').val(company.email);
                $('#edit_phone').val(company.phone);
                $('#edit_street').val(company.street);
                $('#edit_district').val(company.district);
                $('#edit_city').val(company.city);
                $('#edit_zip_code').val(company.zip_code);
                $('#edit_website').val(company.website);
                $('#edit_industry_type').val(company.industry_type);
                $('#edit_is_active').val(company.is_active);

                // Logo preview
                if (company.logo) {
                    $('#currentLogoImg').attr('src', '/storage/' + company.logo);
                    $('#currentLogoWrap').show();
                } else {
                    $('#currentLogoWrap').hide();
                }

                // Clear previous errors
                $('#editCompanyForm').find('.is-invalid').removeClass('is-invalid');
                $('#editCompanyForm').find('[id^="error-edit-"]').text('');
                $('#edit_logo').val('');

                $('#editCompanyModal').modal('show');
            });

            // Submit Edit Company Form
            $(document).on('submit', '#editCompanyForm', function(e) {
                e.preventDefault();
                const $form = $(this);
                const id = $('#edit_company_id').val();
                const $btn = $form.find('button[type="submit"]');
                const $spinner = $form.find('.spinner-border');

                // Reset errors
                $form.find('.is-invalid').removeClass('is-invalid');
                $form.find('[id^="error-edit-"]').text('');

                $btn.prop('disabled', true);
                $spinner.show();

                const fd = new FormData(this);

                $.ajax({
                        url: `/dashboard/company/update/${id}`,
                        method: 'POST',
                        data: fd,
                        processData: false,
                        contentType: false,
                        dataType: 'json'
                    })
                    .done(function(res) {
                        if (res.success) {
                            $('#editCompanyModal').modal('hide');
                            updateCompanyRow(res.data);
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!/تم التحديث!',
                                text: res.message,
                                timer: 1800,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Error', res.message || 'Unknown error', 'error');
                        }
                    })
                    .fail(function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors || {};
                            $.each(errors, function(field, messages) {
                                const selector = '#edit_' + field;
                                $(selector).addClass('is-invalid');
                                $('#error-edit-' + field).text(messages[0]);
                            });
                        } else {
                            Swal.fire('Error', 'Server error. Try again.', 'error');
                        }
                    })
                    .always(function() {
                        $btn.prop('disabled', false);
                        $spinner.hide();
                    });
            });

            // View Company Button Click
            $(document).on('click', '.viewCompanyBtn', function(e) {
                e.preventDefault();
                const $btn = $(this);

                // Fill modal fields with correct data mapping
                $('#viewCompanyLegalName').text($btn.data('legal_name') || '-');
                $('#viewCompanyCRNumber').text($btn.data('cr_number') || '-');
                $('#viewCompanyCRExpiry').text($btn.data('cr_expiry') || '-');
                $('#viewCompanyVAT').text($btn.data('vat_number') || '-');
                $('#viewCompanyWebsite').text($btn.data('website') || '-');
                $('#viewCompanyIndustry').text($btn.data('industry_type') || '-');
                $('#viewCompanyActive').text($btn.data('is_active') == 1 ? 'Yes' : 'No');
                $('#viewCompanyStreet').text($btn.data('street') || '-');
                $('#viewCompanyDistrict').text($btn.data('district') || '-');
                $('#viewCompanyCity').text($btn.data('city') || '-');
                $('#viewCompanyPostalCode').text($btn.data('zip_code') || '-');
                $('#viewCompanyEmail').text($btn.data('email') || '-');
                $('#viewCompanyPhone').text($btn.data('phone') || '-');

                // Handle logo
                const logo = $btn.data('logo');
                if (logo) {
                    $('#viewCompanyLogo').attr('src', '/storage/' + logo).show();
                    $('#viewNoLogo').hide();
                } else {
                    $('#viewCompanyLogo').hide().attr('src', '');
                    $('#viewNoLogo').show();
                }

                $('#viewCompanyModal').modal('show');
            });

            // Delete Company Button Click
            $(document).on("click", ".deleteCompanyBtn", function(e) {
                e.preventDefault();
                let companyId = $(this).data("id");
                $("#confirmDeleteCompany").data("id", companyId);
                $("#deleteCompanyModal").modal("show");
            });

            // Confirm Delete Company
            $(document).on("click", "#confirmDeleteCompany", function() {
                let id = $(this).data("id");

                $.ajax({
                    url: "/companies/" + id,
                    type: "DELETE",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function(response) {
                        $("#deleteCompanyModal").modal("hide");

                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!/تم الحذف!',
                            text: 'Company has been deleted successfully!/تم حذف الشركة بنجاح!',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        $("#company-row-" + id).remove();
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Failed to delete company!', 'error');
                    }
                });
            });

            // Company Filter
            $(document).on('click', '#companyFilterBtn', function() {
                const data = $('#companyFilterForm').serialize();

                // Show loading state
                const $btn = $(this);
                const originalText = $btn.html();
                $btn.html('<i class="fas fa-spinner fa-spin"></i> Filtering...').prop('disabled', true);

                $.ajax({
                    url: "{{ route('dashboard.company.filter') }}",
                    type: 'GET',
                    data: data,
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            const tbody = $('#tableExport tbody');
                            tbody.empty();

                            if (res.data.length === 0) {
                                tbody.append(
                                    '<tr><td colspan="9" class="text-center">No companies found.</td></tr>'
                                );
                            } else {
                                res.data.forEach(company => {
                                    appendCompanyRow(company);
                                });
                            }

                            // Show result count
                            showFilterResultCount(res.data.length);
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Failed to filter companies', 'error');
                        console.error('Filter error:', xhr.responseJSON);
                    },
                    complete: function() {
                        // Restore button state
                        $btn.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Reset Filter
            $(document).on('click', '#companyResetBtn', function() {
                // Reset form
                $('#companyFilterForm')[0].reset();

                // Reload the original table data
                loadOriginalCompanies();

                // Hide result count
                hideFilterResultCount();
            });

            // Load original companies (without filters)
            function loadOriginalCompanies() {
                $.ajax({
                    url: "{{ route('dashboard.company.index') }}", // Adjust this route to your original data route
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        const tbody = $('#tableExport tbody');
                        tbody.empty();

                        if (res.data && res.data.length > 0) {
                            res.data.forEach(company => {
                                appendCompanyRow(company);
                            });
                        } else {
                            tbody.append(
                                '<tr><td colspan="9" class="text-center">No companies found.</td></tr>'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to load companies', 'error');
                    }
                });
            }

            // Show filter result count
            function showFilterResultCount(count) {
                // Remove existing count if any
                $('.filter-result-count').remove();

                const countHtml = `
        <div class="filter-result-count alert alert-info alert-dismissible fade show mt-3" role="alert">
            <strong>${count}</strong> companies found matching your filters.
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `;

                $('#companyFilterForm').after(countHtml);
            }

            // Hide filter result count
            function hideFilterResultCount() {
                $('.filter-result-count').remove();
            }

            // Enhanced appendCompanyRow function for filter results
            function appendCompanyRow(company) {
                const logoHtml = company.logo ?
                    `<img src="/storage/${company.logo}" alt="Logo" width="45" height="45" style="object-fit: cover; border-radius: 6px;">` :
                    '<span class="badge badge-secondary">No Logo</span>';

                const row = `
        <tr id="company-row-${company.id}">
            <td class="company-logo-cell">${logoHtml}</td>
            <td class="company-name-en-cell">${company.name || ''}</td>
            <td class="company-cr-number-cell">${company.cr_number || '-'}</td>
            <td class="company-cr-expiry-cell">${company.cr_expiry || '-'}</td>
            <td class="company-vat-cell">${company.vat_number || '-'}</td>
            <td class="company-city-cell">${company.city || '-'}</td>
            <td class="company-email-cell">${company.email || '-'}</td>
            <td class="company-phone-cell">${company.phone || '-'}</td>
            <td>
                <a href="#" class="text-info viewCompanyBtn"
                    data-id="${company.id}"
                    data-name="${company.name || ''}"
                    data-legal_name="${company.legal_name || ''}"
                    data-cr_number="${company.cr_number || ''}"
                    data-cr_expiry="${company.cr_expiry || ''}"
                    data-vat_number="${company.vat_number || ''}"
                    data-logo="${company.logo || ''}"
                    data-street="${company.street || ''}"
                    data-district="${company.district || ''}"
                    data-city="${company.city || ''}"
                    data-zip_code="${company.zip_code || ''}"
                    data-website="${company.website || ''}"
                    data-industry_type="${company.industry_type || ''}"
                    data-is_active="${company.is_active || 1}"
                    data-email="${company.email || ''}"
                    data-phone="${company.phone || ''}">
                    <i class="fas fa-eye"></i>
                </a>

                <a href="#" class="text-secondary editCompanyBtn"
                    data-id="${company.id}"
                    data-name="${company.name || ''}"
                    data-legal_name="${company.legal_name || ''}"
                    data-cr_number="${company.cr_number || ''}"
                    data-cr_expiry="${company.cr_expiry || ''}"
                    data-vat_number="${company.vat_number || ''}"
                    data-logo="${company.logo || ''}"
                    data-street="${company.street || ''}"
                    data-district="${company.district || ''}"
                    data-city="${company.city || ''}"
                    data-zip_code="${company.zip_code || ''}"
                    data-website="${company.website || ''}"
                    data-industry_type="${company.industry_type || ''}"
                    data-is_active="${company.is_active || 1}"
                    data-email="${company.email || ''}"
                    data-phone="${company.phone || ''}">
                    <i class="fas fa-edit"></i>
                </a>

                <a href="#" class="text-danger deleteCompanyBtn" data-id="${company.id}">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </td>
        </tr>
    `;

                $('#tableExport tbody').append(row);
            }

            // Enter key support for filter inputs
            $(document).on('keypress', '#companyFilterForm input', function(e) {
                if (e.which === 13) { // Enter key
                    $('#companyFilterBtn').click();
                }
            });

            // Helper function to append a company row to the table
            function appendCompanyRow(company) {
                const logoHtml = company.logo ?
                    `<img src="/storage/${company.logo}" alt="Logo" width="45" height="45" style="object-fit: cover; border-radius: 6px;">` :
                    '<span class="badge badge-secondary">No Logo</span>';

                const row = `
                <tr id="company-row-${company.id}">
                    <td class="company-logo-cell">${logoHtml}</td>
                    <td class="company-name-en-cell">${company.name || ''}</td>
                    <td class="company-cr-number-cell">${company.cr_number || '-'}</td>
                    <td class="company-cr-expiry-cell">${company.cr_expiry || '-'}</td>
                    <td class="company-vat-cell">${company.vat_number || '-'}</td>
                    <td class="company-city-cell">${company.city || '-'}</td>
                    <td class="company-email-cell">${company.email || '-'}</td>
                    <td class="company-phone-cell">${company.phone || '-'}</td>
                    <td>
                        <a href="#" class="text-info viewCompanyBtn"
                            data-id="${company.id}"
                            data-name="${company.name || ''}"
                            data-legal_name="${company.legal_name || ''}"
                            data-cr_number="${company.cr_number || ''}"
                            data-cr_expiry="${company.cr_expiry || ''}"
                            data-vat_number="${company.vat_number || ''}"
                            data-logo="${company.logo || ''}"
                            data-street="${company.street || ''}"
                            data-district="${company.district || ''}"
                            data-city="${company.city || ''}"
                            data-zip_code="${company.zip_code || ''}"
                            data-website="${company.website || ''}"
                            data-industry_type="${company.industry_type || ''}"
                            data-is_active="${company.is_active || 1}"
                            data-email="${company.email || ''}"
                            data-phone="${company.phone || ''}">
                            <i class="fas fa-eye"></i>
                        </a>

                        <a href="#" class="text-secondary editCompanyBtn"
                            data-id="${company.id}"
                            data-name="${company.name || ''}"
                            data-legal_name="${company.legal_name || ''}"
                            data-cr_number="${company.cr_number || ''}"
                            data-cr_expiry="${company.cr_expiry || ''}"
                            data-vat_number="${company.vat_number || ''}"
                            data-logo="${company.logo || ''}"
                            data-street="${company.street || ''}"
                            data-district="${company.district || ''}"
                            data-city="${company.city || ''}"
                            data-zip_code="${company.zip_code || ''}"
                            data-website="${company.website || ''}"
                            data-industry_type="${company.industry_type || ''}"
                            data-is_active="${company.is_active || 1}"
                            data-email="${company.email || ''}"
                            data-phone="${company.phone || ''}">
                            <i class="fas fa-edit"></i>
                        </a>

                        <a href="#" class="text-danger deleteCompanyBtn" data-id="${company.id}">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>
            `;

                $('#tableExport tbody').prepend(row);
            }

            // Helper function to update a company row in the table
            function updateCompanyRow(company) {
                const row = $(`#company-row-${company.id}`);

                if (row.length) {
                    // Update logo
                    const logoCell = row.find('.company-logo-cell');
                    const logoHtml = company.logo ?
                        `<img src="/storage/${company.logo}" alt="Logo" width="45" height="45" style="object-fit: cover; border-radius: 6px;">` :
                        '<span class="badge badge-secondary">No Logo</span>';
                    logoCell.html(logoHtml);

                    // Update other cells
                    row.find('.company-name-en-cell').text(company.name || '');
                    row.find('.company-cr-number-cell').text(company.cr_number || '-');
                    row.find('.company-cr-expiry-cell').text(company.cr_expiry || '-');
                    row.find('.company-vat-cell').text(company.vat_number || '-');
                    row.find('.company-city-cell').text(company.city || '-');
                    row.find('.company-email-cell').text(company.email || '-');
                    row.find('.company-phone-cell').text(company.phone || '-');
                }

                // Update view and edit buttons data attributes
                $(`.viewCompanyBtn[data-id="${company.id}"]`).each(function() {
                    const $btn = $(this);
                    $btn.data('name', company.name);
                    $btn.data('legal_name', company.legal_name);
                    $btn.data('cr_number', company.cr_number);
                    $btn.data('cr_expiry', company.cr_expiry);
                    $btn.data('vat_number', company.vat_number);
                    $btn.data('logo', company.logo);
                    $btn.data('street', company.street);
                    $btn.data('district', company.district);
                    $btn.data('city', company.city);
                    $btn.data('zip_code', company.zip_code);
                    $btn.data('website', company.website);
                    $btn.data('industry_type', company.industry_type);
                    $btn.data('is_active', company.is_active);
                    $btn.data('email', company.email);
                    $btn.data('phone', company.phone);

                    // Update DOM attributes as well
                    $btn.attr('data-name', company.name || '');
                    $btn.attr('data-legal_name', company.legal_name || '');
                    $btn.attr('data-cr_number', company.cr_number || '');
                    $btn.attr('data-cr_expiry', company.cr_expiry || '');
                    $btn.attr('data-vat_number', company.vat_number || '');
                    $btn.attr('data-logo', company.logo || '');
                    $btn.attr('data-street', company.street || '');
                    $btn.attr('data-district', company.district || '');
                    $btn.attr('data-city', company.city || '');
                    $btn.attr('data-zip_code', company.zip_code || '');
                    $btn.attr('data-website', company.website || '');
                    $btn.attr('data-industry_type', company.industry_type || '');
                    $btn.attr('data-is_active', company.is_active || 1);
                    $btn.attr('data-email', company.email || '');
                    $btn.attr('data-phone', company.phone || '');
                });

                $(`.editCompanyBtn[data-id="${company.id}"]`).each(function() {
                    const $btn = $(this);
                    $btn.data('name', company.name);
                    $btn.data('legal_name', company.legal_name);
                    $btn.data('cr_number', company.cr_number);
                    $btn.data('cr_expiry', company.cr_expiry);
                    $btn.data('vat_number', company.vat_number);
                    $btn.data('logo', company.logo);
                    $btn.data('street', company.street);
                    $btn.data('district', company.district);
                    $btn.data('city', company.city);
                    $btn.data('zip_code', company.zip_code);
                    $btn.data('website', company.website);
                    $btn.data('industry_type', company.industry_type);
                    $btn.data('is_active', company.is_active);
                    $btn.data('email', company.email);
                    $btn.data('phone', company.phone);

                    $btn.attr('data-name', company.name || '');
                    $btn.attr('data-legal_name', company.legal_name || '');
                    $btn.attr('data-cr_number', company.cr_number || '');
                    $btn.attr('data-cr_expiry', company.cr_expiry || '');
                    $btn.attr('data-vat_number', company.vat_number || '');
                    $btn.attr('data-logo', company.logo || '');
                    $btn.attr('data-street', company.street || '');
                    $btn.attr('data-district', company.district || '');
                    $btn.attr('data-city', company.city || '');
                    $btn.attr('data-zip_code', company.zip_code || '');
                    $btn.attr('data-website', company.website || '');
                    $btn.attr('data-industry_type', company.industry_type || '');
                    $btn.attr('data-is_active', company.is_active || 1);
                    $btn.attr('data-email', company.email || '');
                    $btn.attr('data-phone', company.phone || '');
                });
            }
        });
    </script>
@endsection
