@extends('layout.master')
@section('title', 'Dashboard | Brands')
@section('main')
    <!-- Main Content -->
    <div class="main-content">

        <h2 class="text-center all-branches-title">{{ __('dashboard.all_brands') }}</h2>
        <div class="row g-4" id="branchCardsContainer">
            @forelse ($brandCards as $brand)
                <div class="col-xl-3 col-md-6 mb-4 branch-card">
                    <div class="card shadow-sm h-100">
                        <div class="card-body d-flex flex-column align-items-center text-center">
                            {{-- Brand Logo --}}
                            @if ($brand->logo)
                                <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}" class="mb-3"
                                    style="max-width: 100px; max-height: 100px; object-fit: contain;">
                            @else
                                <img src="{{ asset('images/default-logo.png') }}" alt="No Logo" class="mb-3"
                                    style="max-width: 100px; max-height: 100px; object-fit: contain;">
                            @endif

                            {{-- Brand Name --}}
                            <h5 class="card-title"><b>{{ __('dashboard.brand_name') }}: </b>{{ $brand->name }}</h5>

                            {{-- Company Name --}}
                            <p class="card-text flex-grow-1">
                                <strong>{{ __('dashboard.company_name') }}:</strong> {{ $brand->company->legal_name }} <br>
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>{{ __('dashboard.no_brands_found') }}</p>
                </div>
            @endforelse
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $brandCards->links('pagination::bootstrap-4') }}
        </div>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.brand') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addBrandModal">
                                    {{ __('dashboard.add_brand') }}
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    {{-- <form id="branchFilterForm" class="mb-3">
                                        <div class="row g-2 align-items-end">

                                            <div class="col-md-3">
                                                <label class="form-label">Branch Name</label>
                                                <input type="text" name="name" id="filter_name" class="form-control"
                                                    placeholder="Search by name">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">Location</label>
                                                <input type="text" name="location" id="filter_location"
                                                    class="form-control" placeholder="Search by location">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">Manager</label>
                                                <input type="text" name="manager" id="filter_manager"
                                                    class="form-control" placeholder="Search by manager">
                                            </div>

                                            <div class="col-md-2">
                                                <label class="form-label">Status</label>
                                                <select name="status" id="filter_status" class="form-control">
                                                    <option value="">All</option>
                                                    <option value="Active">Active</option>
                                                    <option value="Inactive">Inactive</option>
                                                </select>
                                            </div>

                                            <div class="col-md-1 d-grid">
                                                <button type="button" id="branchFilterBtn"
                                                    class="btn btn-primary">Filter</button>
                                            </div>

                                        </div>
                                    </form> --}}
                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>{{ __('dashboard.brand_name') }}</th>
                                                <th>{{ __('dashboard.company_name') }}</th>
                                                <th>{{ __('dashboard.logo') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($brands as $brand)
                                                <tr id="brand-row-{{ $brand->id }}">
                                                    <td class="brand-name-cell">{{ $brand->name }}</td>
                                                    <td class="brand-company-cell">{{ $brand->company->legal_name ?? '' }}
                                                    </td>
                                                    <td class="brand-logo-cell">
                                                        @if ($brand->logo)
                                                            <img src="{{ asset('storage/' . $brand->logo) }}"
                                                                alt="{{ $brand->name }}" width="50" height="50">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="#" class="text-info viewBrandBtn"
                                                            data-id="{{ $brand->id }}" data-name="{{ $brand->name }}"
                                                            data-company="{{ $brand->company->name ?? '' }}"
                                                            data-logo="{{ $brand->logo ? asset('storage/' . $brand->logo) : '' }}">
                                                            <i class="fas fa-eye"></i>
                                                        </a>

                                                        <a href="#" class="text-secondary editBrandBtn"
                                                            data-id="{{ $brand->id }}" data-name="{{ $brand->name }}"
                                                            data-company_id="{{ $brand->company_id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>


                                                        <a href="#" class="text-danger deleteBrandBtn"
                                                            data-id="{{ $brand->id }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>

                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">
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

        <!-- Add Brand Modal -->
        <div class="modal fade" id="addBrandModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('dashboard.add_new_brand') }}</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form id="addBrandForm" method="POST" action="{{ route('dashboard.brand.store') }}"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label>{{ __('dashboard.select_company') }}</label>
                                <select name="company_id" id="company_id" class="form-control">
                                    <option value="">{{ __('dashboard.select_company') }}</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->legal_name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="error-company_id"></div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('dashboard.brand_name') }}</label>
                                <input type="text" name="brand_name" id="brand_name" class="form-control">
                                <div class="invalid-feedback" id="error-brand_name"></div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('dashboard.logo') }}</label>
                                <input type="file" name="brand_logo" id="brand_logo" class="form-control">
                                <div class="invalid-feedback" id="error-brand_logo"></div>
                            </div>

                            <div class="text-right">
                                <button type="submit" id="addBrandSubmit" class="btn btn-primary">
                                    <span id="addBrandSpinner" class="spinner-border spinner-border-sm"
                                        style="display:none"></span>
                                    {{ __('dashboard.save_brand') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Brand Modal -->
        <div class="modal fade" id="editBrandModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('dashboard.brand_details') }}</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="editBrandForm" method="POST" action="{{ route('dashboard.brand.update') }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="brandId" id="edit_brand_id">

                            <div class="form-group">
                                <label>{{ __('dashboard.select_company') }}</label>
                                <select name="company_id" id="edit_company_id" class="form-control">
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->legal_name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="error-edit_company_id"></div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('dashboard.brand_name') }}</label>
                                <input type="text" name="brand_name" id="edit_brand_name" class="form-control">
                                <div class="invalid-feedback" id="error-edit_brand_name"></div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('dashboard.logo') }}</label>
                                <input type="file" name="brand_logo" id="edit_brand_logo" class="form-control">
                                <div class="invalid-feedback" id="error-edit_brand_logo"></div>
                            </div>

                            <div class="text-end">
                                <button type="reset"
                                    class="btn btn-secondary me-2">{{ __('dashboard.reset') }}</button>
                                <button type="submit" class="btn btn-primary">
                                    {{ __('dashboard.update_brand') }}
                                    <span class="spinner-border spinner-border-sm ms-1 d-none"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- View Brand Modal -->
        <div class="modal fade" id="viewBrandModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('dashboard.brand_details') }}</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="fw-bold">{{ __('dashboard.brand_name') }}:</label>
                                <p id="viewBrandName"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold">{{ __('dashboard.company_name') }}:</label>
                                <p id="viewBrandCompany"></p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="fw-bold">{{ __('dashboard.logo') }}:</label>
                                <p id="viewBrandLogo"></p>
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


        <!-- Delete Brand Modal -->
        <div class="modal fade" id="deleteBrandModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">{{ __('dashboard.delete_brand') }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>

                    <form id="deleteBrandForm" method="POST" action="{{ route('dashboard.brand.delete') }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="brandId" id="deleteBrandId">

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
        // CSRF setup for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Add Brand
        $(document).on('submit', '#addBrandForm', function(e) {
            e.preventDefault();
            const $form = $(this);
            const $btn = $form.find('button[type="submit"]');
            const $spinner = $form.find('.spinner-border');

            // Clear previous errors
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('[id^="error-"]').text('');

            $btn.prop('disabled', true);
            $spinner.show();

            // Use FormData for file upload
            let formData = new FormData(this);

            $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                })
                .done(function(res) {
                    if (res.success) {
                        $('#addBrandModal').modal('hide');
                        $form[0].reset();
                        appendBrandRow(res.data); // Add the new brand to the table dynamically

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
                            text: 'Please login again/الرجاء تسجيل الدخول مرة أخرى',
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
                    $spinner.hide();
                });
        });

        function appendBrandRow(brand) {
            const row = `
        <tr id="brand-row-${brand.id}">
            <td>${brand.name}</td>
            <td>${brand.company_name}</td>
            <td>${brand.logo ? `<img src="${brand.logo}" width="50" height="50">` : '-'}</td>
            <td>
                <a href="#" class="text-info viewBrandBtn" data-id="${brand.id}" data-name="${brand.name}" data-company="${brand.company_name}" data-logo="${brand.logo}"><i class="fas fa-eye"></i></a>
                <a href="#" class="text-secondary editBrandBtn" data-id="${brand.id}" data-name="${brand.name}" data-company_id="${brand.company_id}" data-logo="${brand.logo}"><i class="fas fa-edit"></i></a>
                <a href="#" class="text-danger deleteBrandBtn" data-id="${brand.id}"><i class="fas fa-trash-alt"></i></a>
            </td>
        </tr>
    `;
            $('#tableExport tbody').prepend(row); // Add on top
        }

        // View Brand Modal
        $(document).on('click', '.viewBrandBtn', function(e) {
            e.preventDefault();

            const brandName = $(this).data('name');
            const companyName = $(this).data('company');
            const brandLogo = $(this).data('logo');

            $('#viewBrandName').text(brandName);
            $('#viewBrandCompany').text(companyName);

            if (brandLogo) {
                $('#viewBrandLogo').html(`<img src="${brandLogo}" width="100" height="100" class="img-thumbnail">`);
            } else {
                $('#viewBrandLogo').text('-');
            }

            $('#viewBrandModal').modal('show');
        });

        // Open Edit Brand Modal
        $(document).on('click', '.editBrandBtn', function(e) {
            e.preventDefault();

            const brandId = $(this).data('id');
            const brandName = $(this).data('name');
            const companyId = $(this).data('company_id');

            // Reset previous errors and file input
            $('#editBrandForm .is-invalid').removeClass('is-invalid');
            $('#editBrandForm [id^="error-"]').text('');
            $('#edit_brand_logo').val(''); // reset file input only

            // Populate form fields AFTER reset
            $('#edit_brand_id').val(brandId);
            $('#edit_brand_name').val(brandName);
            $('#edit_company_id').val(companyId);

            // Show modal
            $('#editBrandModal').modal('show');
        });

        // Update Brand
        $(document).on('submit', '#editBrandForm', function(e) {
            e.preventDefault();
            const $form = $(this);
            const $btn = $form.find('button[type="submit"]');
            const $spinner = $form.find('.spinner-border');

            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('[id^="error-"]').text('');

            $btn.prop('disabled', true);
            $spinner.removeClass('d-none');

            let formData = new FormData(this);

            $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                })
                .done(function(res) {
                    if (res.success) {
                        $('#editBrandModal').modal('hide');

                        // Update row in table
                        const row = $(`#brand-row-${res.data.id}`);
                        row.find('.brand-name-cell').text(res.data.name);
                        row.find('.brand-company-cell').text(res.data.company_name);
                        row.find('.brand-logo-cell').html(res.data.logo ?
                            `<img src="${res.data.logo}" width="50" height="50">` : '-');

                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!/تم التحديث',
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
                            const $el = $('[name="' + field + '"]', $form);
                            $el.addClass('is-invalid');
                            $('#error-edit_' + field).text(messages[0]);
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
                    $spinner.addClass('d-none');
                });
        });

        // Open Delete Brand Modal
        $(document).on('click', '.deleteBrandBtn', function(e) {
            e.preventDefault();
            const brandId = $(this).data('id');
            $('#deleteBrandId').val(brandId);
            $('#deleteBrandModal').modal('show');
        });

        // Delete Brand via AJAX
        $(document).on('submit', '#deleteBrandForm', function(e) {
            e.preventDefault();

            const $form = $(this);
            const $btn = $form.find('button[type="submit"]');
            const brandId = $('#deleteBrandId').val();

            $btn.prop('disabled', true);

            $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                })
                .done(function(res) {
                    if (res.success) {
                        $('#deleteBrandModal').modal('hide');

                        // Remove the deleted row from table
                        $(`#brand-row-${brandId}`).remove();

                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!/تم الحذف',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
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
                .always(() => {
                    $btn.prop('disabled', false);
                });
        });
    </script>
@endsection
