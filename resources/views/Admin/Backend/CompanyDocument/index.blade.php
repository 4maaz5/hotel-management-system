@extends('layout.master')
@section('title', 'Dashboard | Documents')
@section('main')
    <!-- Main Content -->
    <div class="main-content">

        <h1 class="text-center mb-4">{{ __('dashboard.company_documents') }}</h1>
        <div class="company-docs-grid">
            @forelse($companyDocsCard as $companyDoc)
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-2">{{ $companyDoc->name }}</h6>

                            <p class="mb-1"><strong>{{ __('dashboard.type') }}:</strong> {{ $companyDoc->type }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.issue_by') }}:</strong> {{ $companyDoc->issued_by }}
                            </p>
                            <p class="mb-1"><strong>{{ __('dashboard.issue_date') }}:</strong>
                                {{ $companyDoc->issue_date }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.expiry_date') }}:</strong>
                                {{ $companyDoc->expiration_date }}</p>
                        </div>

                        @if ($companyDoc->file_path)
                            <div class="d-flex justify-content-center my-2">
                                <a href="#" class="view-pdf"
                                    data-file="{{ asset('storage/' . $companyDoc->file_path) }}" title="View PDF">
                                    <i class="fas fa-file-pdf text-secondary" style="font-size: 28px;"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center">
                    {{-- <p>No company documents found.</p> --}}
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $companyDocsCard->links('pagination::bootstrap-5') }}
        </div>
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.company_document') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addCompanyDocModal">
                                    {{ __('dashboard.add_company_document') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <form id="companyDocumentFilterForm" class="mb-3">
                                        <div class="row g-2 align-items-end">

                                            <div class="col-md-2">
                                                <label class="form-label">{{ __('dashboard.type') }}</label>
                                                <input type="text" name="type" class="form-control">
                                            </div>

                                            <div class="col-md-2">
                                                <label class="form-label">{{ __('dashboard.issue_by') }}</label>
                                                <input type="text" name="issued_by" class="form-control">
                                            </div>

                                            <div class="col-md-2">
                                                <label class="form-label">{{ __('dashboard.start_issue_date') }}</label>
                                                <input type="date" name="start_date" class="form-control">
                                            </div>

                                            <div class="col-md-2">
                                                <label class="form-label">{{ __('dashboard.end_issue_date') }}</label>
                                                <input type="date" name="end_date" class="form-control">
                                            </div>

                                            <div class="col-md-2">
                                                <label class="form-label">{{ __('dashboard.expiry_date') }}</label>
                                                <input type="date" name="expiry_date" class="form-control">
                                            </div>

                                            <div class="col-md-2 d-grid">
                                                <button type="submit" class="btn btn-primary">
                                                    {{ __('dashboard.filter') }}
                                                </button>
                                            </div>

                                        </div>
                                    </form>
                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>{{ __('dashboard.document_name') }}</th>
                                                <th>{{ __('dashboard.type') }}</th>
                                                <th>{{ __('dashboard.issue_by') }}</th>
                                                <th>{{ __('dashboard.issue_date') }}</th>
                                                <th>{{ __('dashboard.expiry_date') }}</th>
                                                <th>{{ __('dashboard.file') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="companyDocumentsTbody">
                                            @forelse ($companyDocs as $companyDoc)
                                                <tr id="companyDocRow{{ $companyDoc->id }}">
                                                    <td>{{ $companyDoc->name }}</td>
                                                    <td>{{ $companyDoc->type }}</td>
                                                    <td>{{ $companyDoc->issued_by }}</td>
                                                    <td>{{ $companyDoc->issue_date }}</td>
                                                    <td>{{ $companyDoc->expiration_date }}</td>
                                                    <td>
                                                        @if ($companyDoc->file_path)
                                                            <a href="#" class="view-pdf"
                                                                data-file="{{ asset('storage/' . $companyDoc->file_path) }}"
                                                                title="View PDF">
                                                                <i class="fas fa-file-pdf text-secondary"
                                                                    style="font-size: 18px;"></i>
                                                            </a>
                                                        @endif
                                                    </td>

                                                    <td>

                                                        <a href="#" class="text-secondary editCompanyDocBtn"
                                                            data-id="{{ $companyDoc->id }}"
                                                            data-name="{{ $companyDoc->name }}"
                                                            data-type="{{ $companyDoc->type }}"
                                                            data-issued_by="{{ $companyDoc->issued_by }}"
                                                            data-issue_date="{{ $companyDoc->issue_date }}"
                                                            data-expiration_date="{{ $companyDoc->expiration_date }}"
                                                            data-file_path="{{ $companyDoc->file_path }}"
                                                            data-toggle="modal" data-target="#editCompanyDocModal">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <a href="#" class="text-danger deleteCompanyDocBtn"
                                                            data-id="{{ $companyDoc->id }}" data-toggle="modal"
                                                            data-target="#deleteCompanyDocModal">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
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

        <!-- Add Company Document Modal -->
        <div class="modal fade" id="addCompanyDocModal" tabindex="-1" aria-labelledby="addCompanyDocModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCompanyDocModalLabel">{{ __('dashboard.add_company_document') }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <form id="addCompanyDocForm" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label>{{ __('dashboard.company_name') }}</label>
                                <select name="company_id" id="company_id" class="form-control">
                                    <option value="">{{ __('dashboard.select_company') }}</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Document Name -->
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.document_name') }}</label>
                                <input type="text" class="form-control" placeholder="e.g., رخصة تجارية"
                                    name="name">
                                <span class="text-danger error-text name_error"></span>
                            </div>

                            <!-- Document Type -->
                            <div class="mb-3">
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
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.issue_by') }}</label>
                                <input type="text" class="form-control" placeholder="e.g., وزارة التجارة"
                                    name="issued_by">
                                <span class="text-danger error-text issue_by_error"></span>
                            </div>

                            <!-- Issue Date -->
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.issue_date') }}</label>
                                <input type="date" class="form-control" name="issue_date">
                                <span class="text-danger error-text issue_date_error"></span>
                            </div>

                            <!-- Expiry Date -->
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.expiry_date') }}</label>
                                <input type="date" class="form-control" name="expiration_date">
                                <span class="text-danger error-text expiration_date_error"></span>
                            </div>

                            <!-- Upload File -->
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.upload_document') }} (PDF)</label>
                                <input type="file" class="form-control" name="file">
                                <span class="text-danger error-text file_error"></span>
                            </div>

                            <!-- Buttons -->
                            <div class="text-end">
                                <button type="reset"
                                    class="btn btn-secondary me-2">{{ __('dashboard.reset') }}</button>
                                <button type="submit"
                                    class="btn btn-primary">{{ __('dashboard.save_document') }}</button>
                            </div>
                        </form>

                    </div>

                </div>
            </div>
        </div>

        <!-- Edit Company Document Modal -->
        <div class="modal fade" id="editCompanyDocModal" tabindex="-1" aria-labelledby="editCompanyDocModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCompanyDocModalLabel">{{ __('dashboard.edit_company_document') }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <form id="editCompanyDocForm" enctype="multipart/form-data">
                            @csrf
                            <!-- Hidden ID -->
                            <input type="hidden" name="edit_id" id="editDocId">

                            <!-- Document Name -->
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.document_name') }}</label>
                                <input type="text" class="form-control" placeholder="e.g., رخصة تجارية"
                                    name="name" id="editDocTitle">
                                <span class="text-danger error-text name_error"></span>
                            </div>

                            <!-- Document Type -->
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.document_type') }}</label>
                                <select class="form-control" name="type" id="editDocType">
                                    <option value="" disabled>{{ __('dashboard.select_type') }}</option>
                                    <option value="Legal">{{ __('dashboard.legal') }}</option>
                                    <option value="License">{{ __('dashboard.license') }}</option>
                                    <option value="Contract">{{ __('dashboard.contract') }}</option>
                                    <option value="Tax Certificate">{{ __('dashboard.tax_certificate') }}</option>
                                    <option value="Policy">{{ __('dashboard.policy') }}</option>
                                    <option value="Other">{{ __('dashboard.other') }}</option>
                                </select>
                                <span class="text-danger error-text type_error"></span>
                            </div>

                            <!-- Issued By -->
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.issue_by') }}</label>
                                <input type="text" class="form-control" placeholder="e.g., وزارة التجارة"
                                    name="issued_by" id="editIssuedBy">
                                <span class="text-danger error-text issued_by_error"></span>
                            </div>

                            <!-- Issue Date -->
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.issue_date') }}</label>
                                <input type="date" class="form-control" name="issue_date" id="editIssuedDate">
                                <span class="text-danger error-text issue_date_error"></span>
                            </div>

                            <!-- Expiry Date -->
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.expiry_date') }}</label>
                                <input type="date" class="form-control" name="expiration_date" id="editExpiryDate">
                                <span class="text-danger error-text expiration_date_error"></span>
                            </div>

                            <!-- Upload File -->
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.upload_document') }} (PDF)</label>
                                <input type="file" class="form-control" name="file" id="editDocFile">
                                <span class="text-danger error-text file_error"></span>
                            </div>

                            <!-- Buttons -->
                            <div class="text-end">
                                <button type="reset"
                                    class="btn btn-secondary me-2">{{ __('dashboard.reset') }}</button>
                                <button type="submit"
                                    class="btn btn-primary">{{ __('dashboard.update_document') }}</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- Delete Company Document Modal -->
        <div class="modal fade" id="deleteCompanyDocModal" tabindex="-1" role="dialog"
            aria-labelledby="deleteCompanyDocModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteCompanyDocModalLabel">
                            {{ __('dashboard.delete_company_document') }}</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <p>{{ __('dashboard.are_you_sure') }}</p>
                        {{-- <p class="text-warning"><small>This action cannot be undone.</small></p> --}}
                        <div class="mb-2">
                            <strong>{{ __('dashboard.document_title') }}:</strong> <span id="deleteDocTitle"></span><br>
                            <strong>{{ __('dashboard.document_type') }}:</strong> <span id="deleteDocType"></span>
                        </div>
                        <input type="hidden" id="deleteDocId">
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                        <button type="button" class="btn btn-danger"
                            id="confirmDeleteDocBtn">{{ __('dashboard.delete') }}</button>
                    </div>

                </div>
            </div>
        </div>

        <!-- PDF Modal -->
        <div class="modal fade" id="pdfModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('dashboard.view_pdf') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <iframe src="" id="pdfFrame" frameborder="0"
                            style="width:100%; height:600px;"></iframe>
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
        $(document).ready(function() {
            $('#addCompanyDocForm').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: '{{ route('dashboard.document.company.store') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('span.error-text').text('');
                    },
                    success: function(res) {
                        $('#addCompanyDocModal').modal('hide');
                        $('#addCompanyDocForm')[0].reset();

                        Swal.fire({
                            title: 'Success!',
                            text: 'Company document added successfully!',
                            icon: 'success',
                            timer: 2500,
                            showConfirmButton: false
                        });

                        // Append row dynamically
                        appendCompanyDocRow(res.data);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                $(`.${key}_error`).text(value[0]);
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong while saving the document.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                });
            });

            function appendCompanyDocRow(doc) {
                const html = `
        <tr id="companyDocRow${doc.id}">
            <td>${doc.name}</td>
            <td>${doc.type}</td>
            <td>${doc.issued_by ?? ''}</td>
            <td>${doc.issue_date ?? ''}</td>
            <td>${doc.expiration_date ?? ''}</td>
            <td>
                ${doc.file_path ? `<a href="/storage/${doc.file_path}" target="_blank" title="View PDF"><i class="fas fa-file-pdf text-secondary" style="font-size: 18px;"></i></a>` : ''}
            </td>
            <td>
                <a href="#" class="text-secondary editCompanyDocBtn"
                    data-id="${doc.id}"
                    data-name="${doc.name}"
                    data-type="${doc.type}"
                    data-issued_by="${doc.issued_by ?? ''}"
                    data-issue_date="${doc.issue_date ?? ''}"
                    data-expiration_date="${doc.expiration_date ?? ''}"
                    data-file_path="${doc.file_path ?? ''}"
                    data-toggle="modal" data-target="#editCompanyDocModal">
                    <i class="fas fa-edit"></i>
                </a>
                <a href="#" class="text-danger deleteCompanyDocBtn" data-id="${doc.id}" data-toggle="modal" data-target="#deleteCompanyDocModal">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </td>
        </tr>
    `;
                $('#companyDocumentsTbody').prepend(html); // Add new row at top
            }

        });
        $(document).on('click', '.editCompanyDocBtn', function() {
            const doc = $(this).data();

            $('#editDocId').val(doc.id);
            $('#editDocTitle').val(doc.name);
            $('#editDocType').val(doc.type);
            $('#editIssuedBy').val(doc.issued_by);
            $('#editIssuedDate').val(doc.issue_date);
            $('#editExpiryDate').val(doc.expiration_date);
        });

        $('#editCompanyDocForm').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            const docId = $('input[name="edit_id"]').val();

            $.ajax({
                url: `/dashboard/document/company/${docId}`, // make route like dashboard.document.company.update
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                beforeSend: function() {
                    $('span.error-text').text('');
                },
                success: function(res) {
                    $('#editCompanyDocModal').modal('hide');

                    Swal.fire({
                        title: 'Updated!',
                        text: 'Company document updated successfully!',
                        icon: 'success',
                        timer: 2500,
                        showConfirmButton: false
                    });

                    // Update the row in the table
                    updateCompanyDocRow(res.data);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            $(`#edit${key.charAt(0).toUpperCase() + key.slice(1)}Error`).text(
                                value[0]);
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong while updating the document.',
                            icon: 'error'
                        });
                    }
                }
            });
        });

        function updateCompanyDocRow(doc) {
            const row = $(`#companyDocRow${doc.id}`);
            row.html(`
        <td>${doc.name}</td>
        <td>${doc.type}</td>
        <td>${doc.issued_by ?? ''}</td>
        <td>${doc.issue_date ?? ''}</td>
        <td>${doc.expiration_date ?? ''}</td>
        <td>
            ${doc.file_path ? `<a href="/storage/${doc.file_path}" target="_blank" title="View PDF"><i class="fas fa-file-pdf text-secondary" style="font-size: 18px;"></i></a>` : ''}
        </td>
        <td>
            <a href="#" class="text-secondary editCompanyDocBtn"
               data-id="${doc.id}"
               data-name="${doc.name}"
               data-type="${doc.type}"
               data-issued_by="${doc.issued_by}"
               data-issue_date="${doc.issue_date}"
               data-expiration_date="${doc.expiration_date}"
               data-file_path="${doc.file_path}"
               data-toggle="modal" data-target="#editCompanyDocModal">
               <i class="fas fa-edit"></i>
            </a>
            <a href="#" class="text-danger deleteCompanyDocBtn" data-id="${doc.id}" data-toggle="modal" data-target="#deleteCompanyDocModal">
                <i class="fas fa-trash-alt"></i>
            </a>
        </td>
    `);
        }
        // Capture delete button click
        $(document).on('click', '.deleteCompanyDocBtn', function() {
            const id = $(this).data('id');
            const row = $(this).closest('tr');
            const title = row.find('td').eq(0).text();
            const type = row.find('td').eq(1).text();

            $('#deleteDocId').val(id);
            $('#deleteDocTitle').text(title);
            $('#deleteDocType').text(type);
        });

        // Confirm delete
        $('#confirmDeleteDocBtn').on('click', function() {
            const docId = $('#deleteDocId').val();

            $.ajax({
                url: `/dashboard/document/company/${docId}`, // your delete route
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                success: function(res) {
                    $('#deleteCompanyDocModal').modal('hide');

                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Company document deleted successfully!',
                        icon: 'success',
                        timer: 2500,
                        showConfirmButton: false
                    });

                    // Remove row from table
                    $(`#companyDocRow${docId}`).remove();
                },
                error: function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong while deleting the document.',
                        icon: 'error'
                    });
                }
            });
        });

        $(document).on('click', '.view-pdf', function(e) {
            e.preventDefault();
            let file = $(this).data('file'); // get PDF URL from clicked row
            $('#pdfFrame').attr('src', file); // set iframe src
            $('#pdfModal').modal('show'); // show modal
        });

        // Clear iframe when modal closes
        $('#pdfModal').on('hidden.bs.modal', function() {
            $('#pdfFrame').attr('src', '');
        });

        $('#companyDocumentFilterForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('company.documents.filter') }}",
                method: "GET",
                data: $(this).serialize(),
                success: function(res) {
                    $('#companyDocumentsTbody').html(res.html);
                }
            });
        });
    </script>


@endsection
