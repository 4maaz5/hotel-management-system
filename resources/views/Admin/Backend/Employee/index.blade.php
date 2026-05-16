@extends('layout.master')
@section('title', 'Dashboard | Employee')
@section('main')
    <div class="main-content">
        <h2 class="text-center mb-4">{{ __('dashboard.all_employees') }}</h2>
        <div class="row flex-wrap g-3 ml-5" id="employeeCardsContainer">
            @forelse ($employeeCards as $employee)
                <div class="employee-card">
                    <div class="card shadow-sm h-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="card-title">{{ $employee->first_name }} {{ $employee->last_name }}</h5>
                                <p class="card-text mb-2">
                                    <strong>ID:</strong> {{ $employee->employee_id }}<br>
                                    <strong>{{ __('dashboard.email') }}:</strong> {{ $employee->email ?? '-' }}<br>
                                    <strong>{{ __('dashboard.phone') }}:</strong> {{ $employee->phone ?? '-' }}<br>
                                    <strong>{{ __('dashboard.branch') }}:</strong> {{ $employee->branch->name ?? '-' }}<br>
                                    <strong>{{ __('dashboard.residence_expiry') }}:</strong>
                                    {{ $employee->residence_expiry_date ?? '-' }}
                                </p>
                            </div>
                            <div class="mt-2 d-flex justify-content-center action-buttons">
                                <a href="/dashboard/profile/{{ $employee->id }}" class="text-secondary" title="Profile">
                                    <i class="fas fa-user"></i>
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center empty-state">
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $employeeCards->links('pagination::bootstrap-5') }}
        </div>

        <section class="section mt-2">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.employees') }}</h4>

                                <div class="d-flex align-items-center">

                                    <button type="button" class="btn btn-primary px-4 py-2" data-toggle="modal"
                                        data-target=".bd-example-modal-lg">
                                        {{ __('dashboard.add_new_employee') }}
                                    </button>




                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="card-body">
                                <div class="table-responsive">
                                    <!-- Filters -->
                                    <form id="employeeFilterForm"
                                        class="mb-3 d-flex flex-wrap align-items-center gap-2 p-3 bg-light rounded shadow-sm">

                                        <select name="branch_id" class="form-control" id="branchSelect"
                                            style="width: 220px;">
                                            <option value="all">{{ __('dashboard.all_branches') }}</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>

                                        <select name="department_id" class="form-control ml-5" id="departmentSelect"
                                            style="width: 220px;">
                                            <option value="all">{{ __('dashboard.all_departments') }}</option>
                                            @foreach ($departments as $dept)
                                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                            @endforeach
                                        </select>

                                        <input type="text" name="search" class="form-control ml-5"
                                            placeholder="{{ __('dashboard.search_by_name_or') }}" style="width: 260px;">
                                        <input type="phone" name="phone" class="form-control ml-5"
                                            placeholder="{{ __('dashboard.search_by_phone_number') }}"
                                            style="width: 260px;">

                                        <button type="submit"
                                            class="btn btn-primary px-4 ml-2">{{ __('dashboard.filter') }}</button>
                                    </form>

                                    <div id="employeeTableContainer">
                                        <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('dashboard.full_name') }}</th>
                                                    <th>{{ __('dashboard.employee_id') }}</th>
                                                    <th>{{ __('dashboard.email') }}</th>
                                                    <th>{{ __('dashboard.phone') }}</th>
                                                    <th>{{ __('dashboard.branch') }}</th>
                                                    <th>{{ __('dashboard.shift') }}</th>
                                                    <th>{{ __('dashboard.residence_expiry') }}</th>
                                                    <th>{{ __('dashboard.action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="employeeTableBody">
                                                @include('Admin.Backend.partials.index')
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </section>
        <!-- Single Modal with 4 Steps -->
        <div class="modal fade bd-example-modal-lg" id="addEmployeeModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('dashboard.add_new_employee') }}</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <form id="createEmployeeForm" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <!-- Progress Bar -->
                            <div class="progress mb-4" style="height: 8px;">
                                <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="text-center mb-3">
                                <span class="badge badge-primary">Step 1 of 4/الخطوة 1 من 4</span>
                            </div>

                            <!-- Step 1: Personal Information with Bank Details -->
                            <div class="step" id="step1">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>{{ __('dashboard.first_name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="first_name"
                                            placeholder="{{ __('dashboard.last_name') }}" required>
                                        <span class="text-danger error-text first_name_error"></span>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>{{ __('dashboard.last_name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="last_name"
                                            placeholder="{{ __('dashboard.last_name') }}" required>
                                        <span class="text-danger error-text last_name_error"></span>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>{{ __('dashboard.email') }} <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="email"
                                            placeholder="{{ __('dashboard.email') }}" required>
                                        <span class="text-danger error-text email_error"></span>
                                    </div>
                                </div>


                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>{{ __('dashboard.phone_number') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="phone"
                                            placeholder="{{ __('dashboard.phone') }}" required>
                                        <span class="text-danger error-text phone_error"></span>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>{{ __('dashboard.designation') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="designation"
                                            placeholder="{{ __('dashboard.designation') }}" required>
                                        <span class="text-danger error-text designation_error"></span>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="shift_id">{{ __('dashboard.shift') }}</label>
                                        <select name="shift_id" class="form-control">
                                            <option value="">{{ __('dashboard.select_shift') }}</option>
                                            @foreach ($shifts as $shift)
                                                <option value="{{ $shift->id }}"
                                                    {{ isset($employee) && $employee->shift_id == $shift->id ? 'selected' : '' }}>
                                                    {{ $shift->name }} ({{ $shift->start_time }} -
                                                    {{ $shift->end_time }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger error-text shift_error"></span>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>{{ __('dashboard.select_company') }} <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" name="company_id" required>
                                            <option value="" selected disabled>{{ __('dashboard.select_company') }}
                                            </option>
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}">{{ $company->legal_name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger error-text branch_id_error"></span>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>{{ __('dashboard.select_brand') }} <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" name="brand_id" required>
                                            <option value="" selected disabled>{{ __('dashboard.select_brand') }}
                                            </option>
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger error-text department_id_error"></span>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>{{ __('dashboard.select_branch') }} <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" name="branch_id" id="branch_id" required>
                                            <option value="" selected disabled>{{ __('dashboard.select_branch') }}
                                            </option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger error-text branch_id_error"></span>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>{{ __('dashboard.select_department') }} <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" name="department_id" id="department_id" required>
                                            <option value="" selected disabled>
                                                {{ __('dashboard.select_department') }}</option>

                                        </select>
                                        <span class="text-danger error-text department_id_error"></span>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>{{ __('dashboard.join_date') }} <span class="text-danger">*</span></label>
                                        <input type="datetime-local" class="form-control" name="join_date" required>
                                        <span class="text-danger error-text join_date_error"></span>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>{{ __('dashboard.residence_expiry') }}</label>
                                        <input type="datetime-local" class="form-control" name="residence_expiry_date">
                                        <span class="text-danger error-text residence_expiry_date_error"></span>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>{{ __('dashboard.overtime') }}</label>
                                        <input type="hour" class="form-control" name="overtime">
                                        <span class="text-danger error-text residence_expiry_date_error"></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label
                                        style="font-weight: 600; color: #2d3748; margin-bottom: 0.5rem; display: block;">
                                        <i class="fas fa-image mr-2"
                                            style="color: #667eea;"></i>{{ __('dashboard.employee_image') }}
                                    </label>
                                    <div style="position: relative;">
                                        <input type="file" class="form-control" name="image" accept="image/*"
                                            style="border: 2px dashed #cbd5e0; border-radius: 12px; padding: 2rem 1rem; background: linear-gradient(135deg, rgba(102, 126, 234, 0.02) 0%, rgba(118, 75, 162, 0.02) 100%); cursor: pointer; transition: all 0.3s ease; font-size: 0.9rem; color: #4a5568;"
                                            onmouseover="this.style.borderColor='#667eea'; this.style.background='linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%)'"
                                            onmouseout="this.style.borderColor='#cbd5e0'; this.style.background='linear-gradient(135deg, rgba(102, 126, 234, 0.02) 0%, rgba(118, 75, 162, 0.02) 100%)'">
                                        <small
                                            style="display: block; margin-top: 0.5rem; color: #718096; font-size: 0.8rem;">
                                            <i class="fas fa-info-circle mr-1"></i>{{ __('dashboard.supported_format') }}:
                                            JPG, Jpeg, PNG (Max
                                            2MB)
                                        </small>
                                    </div>
                                    <span class="text-danger error-text image_error"
                                        style="font-size: 0.85rem; margin-top: 0.25rem; display: block;"></span>
                                </div>

                                <!-- Bank Details in Step 1 -->
                                <hr>
                                <h6 class="font-weight-bold text-primary">{{ __('dashboard.Bank_Payment_Details') }}</h6>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>{{ __('dashboard.bank_name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="bank_name"
                                            placeholder="{{ __('dashboard.bank_name') }}" required>
                                        <span class="text-danger error-text bank_name_error"></span>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>{{ __('dashboard.account_number') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="account_number"
                                            placeholder="{{ __('dashboard.account_number') }}" required>
                                        <span class="text-danger error-text account_number_error"></span>
                                    </div>
                                </div>


                            </div>

                            <!-- Step 2: Insurance Details -->
                            <div class="step" id="step2" style="display:none;">
                                <h5 class="font-weight-bold text-primary mb-3">{{ __('dashboard.insurance_details') }}
                                </h5>
                                <div id="insuranceContainer">
                                    <div class="insuranceRow border p-3 mb-3 rounded">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0">{{ __('dashboard.insurance') }} #1</h6>
                                            <button type="button" class="btn btn-sm btn-danger remove-insurance"
                                                style="display:none;">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label>{{ __('dashboard.provider_name') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control"
                                                    name="insurances[0][provider_name]"
                                                    placeholder="{{ __('dashboard.provider_name') }}" required>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>{{ __('dashboard.policy_number') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control"
                                                    name="insurances[0][policy_number]"
                                                    placeholder="{{ __('dashboard.policy_number') }}" required>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>{{ __('dashboard.policy_type') }}</label>
                                                <select class="form-control" name="insurances[0][policy_type]">
                                                    <option value="Health">{{ __('dashboard.health') }}</option>
                                                    <option value="Life">{{ __('dashboard.life') }}</option>
                                                    <option value="Accident">{{ __('dashboard.accident') }}</option>
                                                    <option value="Other">{{ __('dashboard.other') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label>{{ __('dashboard.start_date') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="date" class="form-control"
                                                    name="insurances[0][start_date]" required>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>{{ __('dashboard.expiry_date') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="date" class="form-control"
                                                    name="insurances[0][expiry_date]" required>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>{{ __('dashboard.premium_amount') }}</label>
                                                <input type="number" class="form-control"
                                                    name="insurances[0][premium_amount]"
                                                    placeholder="{{ __('dashboard.premium_amount') }}" step="0.01">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>{{ __('dashboard.insurance_document') }}</label>
                                            <input type="file" class="form-control" name="insurances[0][document]"
                                                accept=".pdf,.jpg,.jpeg,.png">
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="addInsuranceRow" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-plus mr-1"></i> {{ __('dashboard.add_more_insurance') }}
                                </button>
                            </div>

                            <!-- Step 3: Extra Documents -->
                            <div class="step" id="step3" style="display:none;">
                                <h5 class="font-weight-bold text-primary mb-3">{{ __('dashboard.extra_documents') }}</h5>
                                <div id="documentContainer">
                                    <div class="documentRow border p-3 mb-3 rounded">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0">{{ __('dashboard.documents') }} #1</h6>
                                            <button type="button" class="btn btn-sm btn-danger remove-document"
                                                style="display:none;">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-3">
                                                <label>{{ __('dashboard.document_type') }} <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" name="documents[0][type]" required>
                                                    <option value="Iqama">{{ __('dashboard.iqama') }}</option>
                                                    <option value="Passport">{{ __('dashboard.passport') }}</option>
                                                    <option value="Driving License">{{ __('dashboard.driving_license') }}
                                                    </option>
                                                    <option value="Visa">{{ __('dashboard.visa') }}</option>
                                                    <option value="Work Permit">{{ __('dashboard.work_permit') }}
                                                    </option>
                                                    <option value="National Identity">
                                                        {{ __('dashboard.national_identity') }}</option>
                                                    <option value="Academic Qualification">
                                                        {{ __('dashboard.academic_qualification') }}</option>
                                                    <option value="Experience">{{ __('dashboard.experience') }}</option>
                                                    <option value="Other">{{ __('dashboard.other') }}</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label>{{ __('dashboard.document_number') }}</label>
                                                <input type="text" class="form-control"
                                                    name="documents[0][document_number]"
                                                    placeholder="{{ __('dashboard.document_number') }}">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label>{{ __('dashboard.issue_date') }}</label>
                                                <input type="date" class="form-control"
                                                    name="documents[0][issue_date]">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label>{{ __('dashboard.expiry_date') }}</label>
                                                <input type="date" class="form-control"
                                                    name="documents[0][expiry_date]">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>{{ __('dashboard.file') }}</label>
                                            <input type="file" class="form-control" name="documents[0][document_path]"
                                                accept=".pdf,.jpg,.jpeg,.png">
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="addDocumentRow" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-plus mr-1"></i> {{ __('dashboard.add_more_documents') }}
                                </button>
                            </div>

                            <!-- Step 4: Salary & Review -->
                            <div class="step" id="step4" style="display:none;">
                                <h5 class="font-weight-bold text-primary mb-3">{{ __('dashboard.salary_commission') }}
                                </h5>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>{{ __('dashboard.base_salary') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="base_salary"
                                            placeholder="{{ __('dashboard.base_salary') }}" step="0.01" required>
                                        <span class="text-danger error-text base_salary_error"></span>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>{{ __('dashboard.salary_type') }}</label>
                                        <select class="form-control" name="salary_type">
                                            <option value="monthly">{{ __('dashboard.monthly') }}</option>
                                            <option value="weekly">{{ __('dashboard.weekly') }}</option>
                                            <option value="daily">{{ __('dashboard.daily') }}</option>
                                            <option value="hourly">{{ __('dashboard.hourly') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="is_commission"
                                            id="is_commission">
                                        <label class="form-check-label"
                                            for="is_commission">{{ __('dashboard.commission_based') }}</label>
                                    </div>
                                </div>

                                <div id="commissionFields" style="display:none;">
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label>{{ __('dashboard.commission_percentage') }} (%)</label>
                                            <input type="number" class="form-control" name="commission_percentage"
                                                placeholder="{{ __('dashboard.commission_percentage') }}" step="0.01"
                                                min="0" max="100">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>{{ __('dashboard.commission_type') }}</label>
                                            <select class="form-control" name="commission_type">
                                                <option value="sales">{{ __('dashboard.sales') }}</option>
                                                <option value="profit">{{ __('dashboard.profit') }}</option>
                                                <option value="revenue">{{ __('dashboard.revenue') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <!-- Review Section -->
                                <h6 class="font-weight-bold text-primary mb-3">{{ __('dashboard.review') }}</h6>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>

                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="font-weight-bold text-primary mb-2">
                                            {{ __('dashboard.personal_bank') }}</h6>
                                        <div id="review-personal-info" class="small">
                                            <!-- Will be populated by JavaScript -->
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="font-weight-bold text-primary mb-2">
                                            {{ __('dashboard.salary_details') }}</h6>
                                        <div id="review-salary-info" class="small">
                                            <!-- Will be populated by JavaScript -->
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6 class="font-weight-bold text-primary mb-2">
                                            {{ __('dashboard.insurance_documents') }}</h6>
                                        <div id="review-additional-info" class="small">
                                            <!-- Will be populated by JavaScript -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light"
                                data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                            <button type="button" class="btn btn-secondary" id="prevStep" style="display:none;">
                                <i class="fas fa-arrow-left mr-1"></i> {{ __('dashboard.previous') }}
                            </button>
                            <button type="button" class="btn btn-primary" id="nextStep">
                                {{ __('dashboard.next') }} <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                            <button type="submit" class="btn btn-success" id="submitEmployeeForm"
                                style="display:none;">
                                <i class="fas fa-check mr-1"></i> {{ __('dashboard.save_employee') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Single Dynamic View Employee Modal -->
        <div class="modal fade bd-example-modal-lg" id="viewEmployeeModal" tabindex="-1" role="dialog"
            aria-labelledby="viewEmployeeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="viewEmployeeModalLabel">{{ __('dashboard.employee_details') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body text-center">
                        <div id="employeeViewContent">
                            <p class="text-muted">{{ __('dashboard.loading') }}...</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Edit Employee Modal -->
        <div class="modal fade bd-example-modal-lg" id="editEmployeeModal" tabindex="-1" role="dialog"
            aria-labelledby="editEmployeeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="editEmployeeModalLabel">{{ __('dashboard.edit_employee') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form id="editEmployeeForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="modal-body">

                            <!-- Personal Info -->
                            <div class="form-group">
                                <label class="text-center">{{ __('dashboard.employee_image') }}</label>
                                <div class="text-center mb-2">
                                    <img id="editImagePreview" src="https://randomuser.me/api/portraits/men/75.jpg"
                                        class="rounded-circle border border-secondary"
                                        style="width: 120px; height: 120px; object-fit: cover;">
                                </div>
                                <input type="file" class="form-control" name="image" id="editEmployeeImage"
                                    accept="image/*">
                                <span class="text-danger error-text image_error"></span>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.first_name') }}</label>
                                    <input type="text" class="form-control" name="first_name"
                                        placeholder="{{ __('dashboard.first_name') }}">
                                    <span class="text-danger error-text first_name_error"></span>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.last_name') }}</label>
                                    <input type="text" class="form-control" name="last_name"
                                        placeholder="{{ __('dashboard.last_name') }}">
                                    <span class="text-danger error-text last_name_error"></span>
                                </div>
                            </div>

                            <div class="form-row">

                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.email') }}</label>
                                    <input type="email" class="form-control" name="email"
                                        placeholder="{{ __('dashboard.email') }}">
                                    <span class="text-danger error-text email_error"></span>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.phone_number') }}</label>
                                    <input type="text" class="form-control" name="phone"
                                        placeholder="{{ __('dashboard.phone') }}">
                                    <span class="text-danger error-text phone_error"></span>
                                </div>
                            </div>

                            <div class="form-row">

                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.designation') }}</label>
                                    <input type="text" class="form-control" name="designation"
                                        placeholder="{{ __('dashboard.designation') }}">
                                    <span class="text-danger error-text designation_error"></span>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="shift_id">{{ __('dashboard.shift') }}</label>
                                    <select name="shift_id" class="form-control">
                                        <option value="">{{ __('dashboard.select_shift') }}</option>
                                        @foreach ($shifts as $shift)
                                            <option value="{{ $shift->id }}"
                                                {{ isset($employee) && $employee->shift_id == $shift->id ? 'selected' : '' }}>
                                                {{ $shift->name }} ({{ $shift->start_time }} -
                                                {{ $shift->end_time }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.select_company') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" name="company_id" required>
                                        <option value="" selected disabled>{{ __('dashboard.select_company') }}
                                        </option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->legal_name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-text branch_id_error"></span>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.select_brand') }} <span class="text-danger">*</span></label>
                                    <select class="form-control" name="brand_id" required>
                                        <option value="" selected disabled>{{ __('dashboard.select_brand') }}
                                        </option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-text department_id_error"></span>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.select_branch') }} <span class="text-danger">*</span></label>
                                    <select class="form-control" name="branch_id">
                                        <option selected disabled>{{ __('dashboard.select_branch') }}</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-text branch_id_error"></span>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.select_departments') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" name="department_id">
                                        <option selected disabled>{{ __('dashboard.select_departments') }}</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-text department_id_error"></span>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.join_date') }}</label>
                                    <input type="date" class="form-control" name="join_date">
                                    <span class="text-danger error-text join_date_error"></span>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.residence_expiry') }}</label>
                                    <input type="date" class="form-control" name="residence_expiry_date">
                                    <span class="text-danger error-text residence_expiry_date_error"></span>
                                </div>
                            </div>



                            <hr>
                            <h6 class="font-weight-bold text-primary">{{ __('dashboard.Bank_Payment_Details') }}</h6>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.bank_name') }}</label>
                                    <input type="text" class="form-control" name="bank_name"
                                        placeholder="{{ __('dashboard.bank_name') }}">
                                    <span class="text-danger error-text bank_name_error"></span>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.account_number') }}</label>
                                    <input type="text" class="form-control" name="account_number"
                                        placeholder="{{ __('dashboard.account_number') }}">
                                    <span class="text-danger error-text account_number_error"></span>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light"
                                data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                            <button type="submit"
                                class="btn btn-primary">{{ __('dashboard.update_employee') }}</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- Delete Employee Modal -->
        <div class="modal fade" id="deleteEmployeeModal" tabindex="-1" aria-labelledby="deleteEmployeeModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteEmployeeModalLabel">{{ __('dashboard.delete_employee') }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form id="deleteEmployeeForm" action="{{ route('dashboard.employee.destroy') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="deleteEmployeeId" name="id">

                        <div class="modal-body text-center">
                            <p class="mb-0">{{ __('dashboard.confirm_delete_modal') }}</p>
                        </div>

                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                            <button type="submit" class="btn btn-danger">{{ __('dashboard.delete') }}</button>
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
        $(document).ready(function() {
            let currentStep = 1;
            const totalSteps = 4;
            let insuranceCount = 1;
            let documentCount = 1;

            // Initialize modal
            $('#addEmployeeModal').on('show.bs.modal', function() {
                resetForm();
            });

            // Next Step Handler
            $('#nextStep').on('click', function() {
                if (validateStep(currentStep)) {
                    if (currentStep < totalSteps) {
                        currentStep++;
                        updateStepDisplay();
                    }
                }
            });

            // Previous Step Handler
            $('#prevStep').on('click', function() {
                if (currentStep > 1) {
                    currentStep--;
                    updateStepDisplay();
                }
            });

            // Update step display
            function updateStepDisplay() {
                // Hide all steps
                $('.step').hide();

                // Show current step
                $('#step' + currentStep).show();

                // Update progress bar
                const progress = (currentStep / totalSteps) * 100;
                $('.progress-bar').css('width', progress + '%');

                // Update step badge
                $('.badge').text('Step ' + currentStep + ' of ' + totalSteps);

                // Update buttons
                $('#prevStep').toggle(currentStep > 1);
                $('#nextStep').toggle(currentStep < totalSteps);
                $('#submitEmployeeForm').toggle(currentStep === totalSteps);

                // Populate review if on last step
                if (currentStep === totalSteps) {
                    populateReviewStep();
                }
            }

            // Validate current step
            function validateStep(step) {
                let isValid = true;

                // Clear previous errors
                $('#step' + step + ' .error-text').text('');
                $('#step' + step + ' .is-invalid').removeClass('is-invalid');

                // Validate required fields in current step
                $('#step' + step + ' [required]').each(function() {
                    const $element = $(this);
                    let value = $element.val();

                    // Handle select elements
                    if ($element.is('select')) {
                        value = $element.val();
                    }

                    if (!value || (typeof value === 'string' && !value.trim())) {
                        isValid = false;
                        $element.addClass('is-invalid');
                        // Find the error span - it could be a sibling or next element
                        const $errorSpan = $element.siblings('.error-text');
                        if ($errorSpan.length) {
                            $errorSpan.text('This field is required');
                        }
                    }
                });

                // Step-specific validations
                if (step === 1) {
                    // Email validation
                    const email = $('#step1 [name="email"]').val();
                    if (email && !isValidEmail(email)) {
                        isValid = false;
                        $('#step1 [name="email"]').addClass('is-invalid');
                        $('#step1 [name="email"]').siblings('.error-text').text(
                            'Please enter a valid email address');
                    }

                    // Phone validation
                    const phone = $('#step1 [name="phone"]').val();
                    if (phone && !isValidPhone(phone)) {
                        isValid = false;
                        $('#step1 [name="phone"]').addClass('is-invalid');
                        $('#step1 [name="phone"]').siblings('.error-text').text(
                            'Please enter a valid phone number');
                    }

                    // Select validation (for branch and department)
                    const branchId = $('#step1 [name="branch_id"]').val();
                    const departmentId = $('#step1 [name="department_id"]').val();

                    if (!branchId) {
                        isValid = false;
                        $('#step1 [name="branch_id"]').addClass('is-invalid');
                        $('#step1 [name="branch_id"]').siblings('.error-text').text('Please select a branch');
                    }

                    if (!departmentId) {
                        isValid = false;
                        $('#step1 [name="department_id"]').addClass('is-invalid');
                        $('#step1 [name="department_id"]').siblings('.error-text').text(
                            'Please select a department');
                    }
                }

                // For steps 2 and 3, validate all dynamic rows
                if (step === 2 || step === 3) {
                    $(`#step${step} [name$="[provider_name]"], #step${step} [name$="[type]"]`).each(function() {
                        if (!$(this).val().trim()) {
                            isValid = false;
                            $(this).addClass('is-invalid');
                        }
                    });
                }

                return isValid;
            }

            // Email validation helper
            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            // Phone validation helper
            function isValidPhone(phone) {
                const phoneRegex = /^[\+]?[0-9\s\-\(\)]{8,}$/;
                return phoneRegex.test(phone.replace(/\s/g, ''));
            }

            // Add Insurance Row
            $('#addInsuranceRow').on('click', function() {
                const newRow = `
            <div class="insuranceRow border p-3 mb-3 rounded">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">{{ __('dashboard.insurance') }} #${insuranceCount + 1}</h6>
                    <button type="button" class="btn btn-sm btn-danger remove-insurance">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>{{ __('dashboard.provider_name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="insurances[${insuranceCount}][provider_name]"  required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>{{ __('dashboard.policy_number') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="insurances[${insuranceCount}][policy_number]"  required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>{{ __('dashboard.policy_type') }}</label>
                        <select class="form-control" name="insurances[${insuranceCount}][policy_type]">
                            <option value="Health">{{ __('dashboard.health') }}</option>
                            <option value="Life">{{ __('dashboard.life') }}</option>
                            <option value="Accident">{{ __('dashboard.accident') }}</option>
                            <option value="Other">{{ __('dashboard.other') }}</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>{{ __('dashboard.start_date') }} <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="insurances[${insuranceCount}][start_date]" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>{{ __('dashboard.expiry_date') }} <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="insurances[${insuranceCount}][expiry_date]" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>{{ __('dashboard.premium_amount') }}</label>
                        <input type="number" class="form-control" name="insurances[${insuranceCount}][premium_amount]" placeholder="{{ __('dashboard.premium_amount') }}" step="0.01">
                    </div>
                </div>
                <div class="form-group">
                    <label>{{ __('dashboard.insurance_document') }}</label>
                    <input type="file" class="form-control" name="insurances[${insuranceCount}][document]" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>
        `;

                $('#insuranceContainer').append(newRow);
                insuranceCount++;

                // Show remove buttons on all rows if there's more than one
                if ($('.insuranceRow').length > 1) {
                    $('.remove-insurance').show();
                }
            });

            // Remove Insurance Row
            $(document).on('click', '.remove-insurance', function() {
                if ($('.insuranceRow').length > 1) {
                    $(this).closest('.insuranceRow').remove();
                    // Re-index insurance rows
                    insuranceCount = 0;
                    $('.insuranceRow').each(function(index) {
                        $(this).find('input, select').each(function() {
                            const name = $(this).attr('name');
                            if (name && name.includes('insurances')) {
                                $(this).attr('name', name.replace(/insurances\[\d+\]/,
                                    'insurances[' + index + ']'));
                            }
                        });
                        // Update title
                        $(this).find('h6').text('Insurance #' + (index + 1));
                        insuranceCount = index + 1;
                    });

                    // Hide remove button if only one row left
                    if ($('.insuranceRow').length === 1) {
                        $('.remove-insurance').hide();
                    }
                }
            });

            // Add Document Row
            $('#addDocumentRow').on('click', function() {
                const newRow = `
            <div class="documentRow border p-3 mb-3 rounded">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">{{ __('dashboard.document') }} #${documentCount + 1}</h6>
                    <button type="button" class="btn btn-sm btn-danger remove-document">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>{{ __('dashboard.document_type') }} <span class="text-danger">*</span></label>
                        <select class="form-control" name="documents[${documentCount}][type]" required>
                            <option value="Iqama">{{ __('dashboard.iqama') }}</option>
                            <option value="Passport">{{ __('dashboard.passport') }}</option>
                            <option value="Driving License">{{ __('dashboard.driving_license') }}</option>
                            <option value="Visa">{{ __('dashboard.visa') }}</option>
                            <option value="Work Permit">{{ __('dashboard.work_permit') }}</option>
                             <option value="National Identity">
                                                        {{ __('dashboard.national_identity') }}</option>
                                                    <option value="Academic Qualification">
                                                        {{ __('dashboard.academic_qualification') }}</option>
                                                    <option value="Experience">{{ __('dashboard.experience') }}</option>
                            <option value="Other">{{ __('dashboard.other') }}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>{{ __('dashboard.document_number') }}</label>
                        <input type="text" class="form-control" name="documents[${documentCount}][document_number]" >
                    </div>
                    <div class="form-group col-md-3">
                        <label>{{ __('dashboard.issue_date') }}</label>
                        <input type="date" class="form-control" name="documents[${documentCount}][issue_date]">
                    </div>
                    <div class="form-group col-md-3">
                        <label>{{ __('dashboard.expiry_date') }}</label>
                        <input type="date" class="form-control" name="documents[${documentCount}][expiry_date]">
                    </div>
                </div>
                <div class="form-group">
                    <label>{{ __('dashboard.file') }}</label>
                    <input type="file" class="form-control" name="documents[${documentCount}][document_path]" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>
        `;

                $('#documentContainer').append(newRow);
                documentCount++;

                // Show remove buttons on all rows if there's more than one
                if ($('.documentRow').length > 1) {
                    $('.remove-document').show();
                }
            });

            // Remove Document Row
            $(document).on('click', '.remove-document', function() {
                if ($('.documentRow').length > 1) {
                    $(this).closest('.documentRow').remove();
                    // Re-index document rows
                    documentCount = 0;
                    $('.documentRow').each(function(index) {
                        $(this).find('input, select').each(function() {
                            const name = $(this).attr('name');
                            if (name && name.includes('documents')) {
                                $(this).attr('name', name.replace(/documents\[\d+\]/,
                                    'documents[' + index + ']'));
                            }
                        });
                        // Update title
                        $(this).find('h6').text('Document #' + (index + 1));
                        documentCount = index + 1;
                    });

                    // Hide remove button if only one row left
                    if ($('.documentRow').length === 1) {
                        $('.remove-document').hide();
                    }
                }
            });

            // Commission checkbox toggle
            $(document).on('change', '#is_commission', function() {
                if ($(this).is(':checked')) {
                    $('#commissionFields').show();
                    $('[name="commission_percentage"]').prop('required', true);
                } else {
                    $('#commissionFields').hide();
                    $('[name="commission_percentage"]').prop('required', false);
                }
            });

            // Populate review step
            function populateReviewStep() {
                // Get branch and department names
                const branchName = $('[name="branch_id"] option:selected').text() || '-';
                const departmentName = $('[name="department_id"] option:selected').text() || '-';

                const personalInfo = `
            <p><strong>{{ __('dashboard.employee_name') }}:</strong> ${$('[name="first_name"]').val()} ${$('[name="last_name"]').val()}</p>
            <p><strong>{{ __('dashboard.employee_id') }}:</strong> ${$('[name="employee_id"]').val()}</p>
            <p><strong>{{ __('dashboard.email') }}:</strong> ${$('[name="email"]').val()}</p>
            <p><strong>{{ __('dashboard.phone') }}:</strong> ${$('[name="phone"]').val()}</p>
            <p><strong>{{ __('dashboard.designation') }}:</strong> ${$('[name="designation"]').val()}</p>
            <p><strong>{{ __('dashboard.branch') }}:</strong> ${branchName}</p>
            <p><strong>{{ __('dashboard.department') }}:</strong> ${departmentName}</p>
            <p><strong>{{ __('dashboard.join_date') }}:</strong> ${formatDate($('[name="join_date"]').val())}</p>
            ${$('[name="residence_expiry_date"]').val() ? `<p><strong>{{ __('dashboard.residence_expiry') }}:</strong> ${formatDate($('[name="residence_expiry_date"]').val())}</p>` : ''}
            <p><strong>{{ __('dashboard.bank_name') }}:</strong> ${$('[name="bank_name"]').val()}</p>
            <p><strong>{{ __('dashboard.account_number') }}:</strong> ${$('[name="account_number"]').val()}</p>
            ${$('[name="iban_number"]').val() ? `<p><strong>IBAN:</strong> ${$('[name="iban_number"]').val()}</p>` : ''}
        `;

                $('#review-personal-info').html(personalInfo);

                const salary = $('[name="base_salary"]').val();
                const salaryType = $('[name="salary_type"]').val();
                const isCommission = $('#is_commission').is(':checked');
                const commissionPercent = $('[name="commission_percentage"]').val();
                const commissionType = $('[name="commission_type"]').val();

                let salaryInfo = `
            <p><strong>{{ __('dashboard.base_salary') }}:</strong> ${salary} (${salaryType})</p>
            <p><strong>{{ __('dashboard.commission_based') }}:</strong> ${isCommission ? 'Yes' : 'No'}</p>
        `;

                if (isCommission) {
                    salaryInfo +=
                        `<p><strong>{{ __('dashboard.commission') }}:</strong> ${commissionPercent}% (${commissionType})</p>`;
                }

                $('#review-salary-info').html(salaryInfo);

                // Additional info summary
                const insuranceCount = $('.insuranceRow').length;
                const documentCount = $('.documentRow').length;
                const additionalInfo = `
            <p><strong>{{ __('dashboard.insurance_policies') }}:</strong> ${insuranceCount}</p>
            <p><strong>{{ __('dashboard.additional_documents') }}:</strong> ${documentCount}</p>
        `;

                $('#review-additional-info').html(additionalInfo);
            }

            // Format date for display
            function formatDate(dateString) {
                if (!dateString) return '-';
                try {
                    const date = new Date(dateString);
                    if (isNaN(date.getTime())) return dateString;

                    if (dateString.includes('T')) {
                        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    } else {
                        return date.toLocaleDateString();
                    }
                } catch (e) {
                    return dateString;
                }
            }

            // Form submission
            $('#createEmployeeForm').on('submit', function(e) {
                e.preventDefault();

                if (!validateStep(currentStep)) {
                    Swal.fire('Error', 'Please fix the errors in the current step.', 'error');
                    return;
                }

                const $btn = $('#submitEmployeeForm');
                const originalText = $btn.html();

                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

                const formData = new FormData(this);

                // IMPORTANT: Set the correct route URL here
                $.ajax({
                    url: "{{ route('dashboard.employee.profile.store') }}", // Make sure this route exists
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#addEmployeeModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!/  تم بنجاح!',
                                text: 'Employee added successfully!/تم إضافة الموظف بنجاح!',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            // Refresh employee table or redirect
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            $btn.prop('disabled', false).html(originalText);
                            Swal.fire('Error', response.message || 'Failed to save employee.',
                                'error');
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(originalText);

                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            let errorMessage = 'Please fix the following errors:<br>';
                            for (const field in errors) {
                                errorMessage += `• ${errors[field][0]}<br>`;
                            }
                            Swal.fire('Validation Error', errorMessage, 'error');
                        } else if (xhr.status === 405) {
                            Swal.fire('Error',
                                'Method not allowed. Please check if the route exists.',
                                'error');
                        } else {
                            Swal.fire('Error', 'Failed to save employee. Please try again.',
                                'error');
                        }
                    }
                });
            });

            // Reset form
            function resetForm() {
                currentStep = 1;
                insuranceCount = 1;
                documentCount = 1;

                // Reset form fields
                $('#createEmployeeForm')[0].reset();

                // Clear dynamic containers (keep first row)
                $('.insuranceRow:not(:first)').remove();
                $('.documentRow:not(:first)').remove();

                // Reset UI
                $('.error-text').text('');
                $('.is-invalid').removeClass('is-invalid');
                $('#commissionFields').hide();
                $('.remove-insurance').hide();
                $('.remove-document').hide();

                updateStepDisplay();
            }
        });

        $(document).on('click', '.edit-employee-btn', function(e) {
            e.preventDefault();
            let id = $(this).data('id');

            $.ajax({
                url: `/dashboard/employee/${id}/edit`,
                type: 'GET',
                success: function(res) {
                    if (res.success) {
                        const emp = res.data;
                        const modal = $('#editEmployeeModal');

                        // Fill modal fields
                        modal.find('input[name="first_name"]').val(emp.first_name);
                        modal.find('input[name="last_name"]').val(emp.last_name);
                        modal.find('input[name="employee_id"]').val(emp.employee_id);
                        modal.find('input[name="email"]').val(emp.email);
                        modal.find('input[name="phone"]').val(emp.phone);
                        modal.find('input[name="designation"]').val(emp.designation);

                        modal.find('select[name="branch_id"]').val(emp.branch_id);
                        modal.find('select[name="company_id"]').val(emp.company_id);
                        modal.find('select[name="brand_id"]').val(emp.brand_id);
                        modal.find('select[name="department_id"]').val(emp.department_id);

                        //  NEW: Set Shift Selected
                        modal.find('select[name="shift_id"]').val(emp.shift_id);

                        modal.find('input[name="join_date"]').val(
                            emp.join_date ? emp.join_date.replace(' ', 'T') : ''
                        );

                        modal.find('input[name="residence_expiry_date"]').val(
                            emp.residence_expiry_date ? emp.residence_expiry_date.replace(' ',
                                'T') : ''
                        );

                        modal.find('input[name="bank_name"]').val(emp.bank_name);
                        modal.find('input[name="account_number"]').val(emp.account_number);

                        // Image preview
                        modal.find('#editImagePreview').attr('src',
                            emp.image ? `/storage/${emp.image}` :
                            'https://randomuser.me/api/portraits/men/75.jpg'
                        );

                        modal.data('id', emp.id);

                        modal.modal('show');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Failed to fetch employee data',
                        'error');
                }
            });
        });




        $('#editEmployeeForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const id = $('#editEmployeeModal').data('id');
            const formData = new FormData(form[0]);
            const btn = form.find('button[type="submit"]');

            btn.prop('disabled', true).text('Updating...');
            form.find('.error-text').text('');

            $.ajax({
                url: `/dashboard/employee/${id}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    if (res.success) {
                        $('#editEmployeeModal').modal('hide');
                        updateEmployeeRow(res.data);

                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!/  تم التحديث!',
                            text: res.message ||
                                'Employee updated successfully/!تم تحديث الموظف بنجاح!',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, messages) {
                            form.find('.' + field + '_error').text(messages[0]);
                        });
                    } else {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Server Error', 'error');
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).text('Update Employee');
                }
            });
        });

        function updateEmployeeRow(employee) {
            const row = $(`#employee-row-${employee.id}`);

            if (row.length) {
                row.find('td:eq(0)').text(`${employee.first_name} ${employee.last_name}`);
                row.find('td:eq(1)').text(employee.employee_id || '-');
                row.find('td:eq(2)').text(employee.email || '-');
                row.find('td:eq(3)').text(employee.phone || '-');
                row.find('td:eq(4)').text(employee.branch?.name || '-');
                row.find('td:eq(5)').text(employee.shift?.name || '-');
                row.find('td:eq(6)').text(employee.residence_expiry_date || '-');
            }
        }


        // Open delete modal and set employee ID
        $(document).on('click', '.deleteEmployeeBtn', function(e) {
            e.preventDefault();
            const employeeId = $(this).data('id');
            $('#deleteEmployeeId').val(employeeId);
            $('#deleteEmployeeModal').modal('show');
        });

        // Handle delete form submission
        $(document).on('submit', '#deleteEmployeeForm', function(e) {
            e.preventDefault();

            const $form = $(this);
            const employeeId = $('#deleteEmployeeId').val();
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
                        $('#deleteEmployeeModal').modal('hide');
                        $(`#employee-row-${employeeId}`).remove();

                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!/  تم الحذف!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message
                        });
                    }
                })
                .fail(function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Something went wrong'
                    });
                })
                .always(function() {
                    $btn.prop('disabled', false);
                });
        });

        $(document).on('click', '.view-employee-btn', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const modal = $('#viewEmployeeModal');
            const content = $('#employeeViewContent');

            content.html('<p class="text-muted">Loading...</p>');
            modal.modal('show');

            $.ajax({
                url: `/dashboard/employee/view/${id}`,
                type: 'GET',
                success: function(res) {
                    if (res.success && res.data) {
                        const emp = res.data;
                        const html = `
                    <div class="text-center mb-3">
                        <img src="${emp.image ? '/storage/' + emp.image : 'https://randomuser.me/api/portraits/men/75.jpg'}"
                            class="rounded-circle border border-secondary"
                            style="width: 120px; height: 120px; object-fit: cover;">
                        <h5 class="mt-2">${emp.first_name} ${emp.last_name}</h5>
                    </div>

                    <table class="table table-bordered">
                        <tr><th>{{ __('dashboard.employee_id') }}</th><td>${emp.employee_id}</td></tr>
                        <tr><th>{{ __('dashboard.email') }}</th><td>${emp.email || '-'}</td></tr>
                        <tr><th>{{ __('dashboard.phone') }}</th><td>${emp.phone || '-'}</td></tr>

                        <tr><th>{{ __('dashboard.branch') }}</th><td>${emp.branch?.name || '-'}</td></tr>
                        <tr><th>{{ __('dashboard.department') }}</th><td>${emp.department?.name || '-'}</td></tr>
                        <tr><th>{{ __('dashboard.join_date') }}</th><td>${emp.join_date || '-'}</td></tr>
                        <tr><th>{{ __('dashboard.residence_expiry') }}</th><td>${emp.residence_expiry_date || '-'}</td></tr>
                        <tr><th>{{ __('dashboard.bank_name') }}</th><td>${emp.bank_name || '-'}</td></tr>
                        <tr><th>{{ __('dashboard.account_number') }}</th><td>${emp.account_number || '-'}</td></tr>
                    </table>
                `;
                        content.html(html);
                    } else {
                        content.html('<p class="text-danger">Employee details not found.</p>');
                    }
                },
                error: function(xhr) {
                    content.html('<p class="text-danger">Error loading employee data.</p>');
                }
            });
        });


        $(document).ready(function() {

            $('#employeeFilterForm').on('submit', function(e) {
                e.preventDefault();
                fetchEmployees(1);
            });

            $(document).on('click', '#employeePagination a', function(e) {
                e.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                fetchEmployees(page);
            });

            function fetchEmployees(page) {
                var data = $('#employeeFilterForm').serialize() + '&page=' + page;

                $.ajax({
                    url: "{{ route('employees.filter') }}",
                    type: 'GET',
                    data: data,
                    success: function(response) {
                        // Replace the table body with partial rows from server
                        $('#employeeTableBody').html(response);
                    },
                    error: function(xhr) {
                    }
                });
            }

        });

        //employee dropdowns loading
        $(document).ready(function() {
            // When Company changes, load Brands
            $('select[name="company_id"]').on('change', function() {
                let companyId = $(this).val();
                let brandSelect = $('select[name="brand_id"]');
                brandSelect.html('<option value="" selected disabled>Loading...</option>');
                $('select[name="branch_id"]').html(
                    '<option value="" selected disabled>Select Branch</option>');
                $('select[name="employee_id"]').html(
                    '<option value="" selected disabled>Select Employee</option>');

                if (companyId) {
                    $.get('/dashboard/brands/' + companyId, function(data) {
                        let options = '<option value="" selected disabled>Select Brand</option>';
                        $.each(data, function(key, brand) {
                            options += `<option value="${brand.id}">${brand.name}</option>`;
                        });
                        brandSelect.html(options);
                    });
                }
            });

            // When Brand changes, load Branches
            $('select[name="brand_id"]').on('change', function() {
                let brandId = $(this).val();
                let branchSelect = $('select[name="branch_id"]');
                branchSelect.html('<option value="" selected disabled>Loading...</option>');
                $('select[name="employee_id"]').html(
                    '<option value="" selected disabled>Select Employee</option>');

                if (brandId) {
                    $.get('/dashboard/branches/' + brandId, function(data) {
                        let options = '<option value="" selected disabled>Select Branch</option>';
                        $.each(data, function(key, branch) {
                            options +=
                                `<option value="${branch.id}">${branch.name}</option>`;
                        });
                        branchSelect.html(options);
                    });
                }
            });

            // When Branch changes, load Employees
            $('select[name="branch_id"]').on('change', function() {
                let branchId = $(this).val();
                let employeeSelect = $('select[name="department_id"]');
                employeeSelect.html('<option value="" selected disabled>Loading...</option>');

                if (branchId) {
                    $.get('/dashboard/employees/' + branchId, function(data) {
                        let options =
                            '<option value="" selected disabled>Select Department</option>';
                        $.each(data, function(key, emp) {
                            options +=
                                `<option value="${emp.id}">${emp.name}</option>`;
                        });
                        employeeSelect.html(options);
                    });
                }
            });
        });


        $(document).ready(function() {
            $('#branchSelect').on('change', function() {
                var branchId = $(this).val();

                if (branchId === 'all') {
                    $('#departmentSelect').html('<option value="all">All Departments</option>');
                    return;
                }

                $.ajax({
                    url: '/get-department/' + branchId,
                    type: 'GET',
                    success: function(data) {
                        var options = '<option value="all">All Departments</option>';
                        $.each(data, function(key, dept) {
                            options += '<option value="' + dept.id + '">' + dept.name +
                                '</option>';
                        });
                        $('#departmentSelect').html(options);
                    }
                });
            });
        });

        $(document).ready(function() {
            $('#branch_id').on('change', function() {
                var branchId = $(this).val();
                if (branchId) {
                    $.ajax({
                        url: '/branches/' + branchId + '/departments',
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#department_id').empty().append(
                                '<option selected disabled>Select Department</option>');
                            $.each(data, function(index, department) {
                                $('#department_id').append('<option value="' +
                                    department.id + '">' + department.name +
                                    '</option>');
                            });
                        }
                    });
                } else {
                    $('#department_id').empty().append(
                        '<option selected disabled>Select Department</option>');
                }
            });
        });

        $('#step1 [name="email"]').on('blur', function() {
            const $email = $(this);
            const emailVal = $email.val().trim();
            const $errorSpan = $email.siblings('.error-text');

            if (!emailVal) return;

            $.ajax({
                url: '/dashboard/employee/check-email',
                method: 'POST',
                data: {
                    email: emailVal,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {

                    if (res.employee_exists) {
                        $email.addClass('is-invalid');
                        $errorSpan.text(
                            'Email already exists in Employees records/البريد الإلكتروني موجود بالفعل في سجلات الموظفين'
                        );
                    } else {
                        $email.removeClass('is-invalid');
                        $errorSpan.text('');
                    }
                }
            });
        });

        $('#step1 [name="image"]').on('change', function() {

            const input = this;
            const $field = $(this);
            const $errorSpan = $field.closest('.form-group').find('.error-text');

            if (!input.files.length) return;

            let formData = new FormData();
            formData.append('image', input.files[0]);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: '/dashboard/employee/check-image',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.invalid) {
                        $field.addClass('is-invalid');
                        $errorSpan.text(
                            'Invalid image (JPG, PNG, WEBP, max 2MB) / صورة غير صالحة'
                        );
                        input.value = '';
                    } else {
                        $field.removeClass('is-invalid');
                        $errorSpan.text('');
                    }
                }
            });
        });

        $('#step1 [name="phone"]').on('blur', function() {

            const $phone = $(this);
            const phoneVal = $phone.val().trim();
            const $errorSpan = $phone.siblings('.error-text');

            if (!phoneVal) return;

            $.ajax({
                url: '/dashboard/employee/check-phone',
                method: 'POST',
                data: {
                    phone: phoneVal,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    if (res.phone_exists) {
                        $phone.addClass('is-invalid');
                        $errorSpan.text(
                            'Phone already exists / رقم الهاتف موجود مسبقاً'
                        );
                    } else {
                        $phone.removeClass('is-invalid');
                        $errorSpan.text('');
                    }
                }
            });
        });
    </script>
@endsection
