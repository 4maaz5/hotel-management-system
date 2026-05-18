@extends('layout.master')
@section('title', 'Dashboard | Document')
@section('main')
    <!-- Main Content -->
    <div class="main-content">

        <h1 class="text-center">{{ __('dashboard.employee_documents') }}</h1>

        <div class="cards-grid employee-docs-grid">
            @forelse($employeeDocsCard as $doc)
                <div class="card-item">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <p class="mb-1"><strong>{{ __('dashboard.employee_id') }}:</strong>
                                {{ $doc->employee->employee_id ?? '-' }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.employee_name') }}:</strong>
                                {{ $doc->employee->first_name ?? '-' }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.document_type') }}:</strong> {{ $doc->type ?? '-' }}
                            </p>
                            <p class="mb-1"><strong>{{ __('dashboard.document_no') }}:</strong>
                                {{ $doc->document_number ?? '-' }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.issue_date') }}:</strong>
                                {{ $doc->issue_date ?? '-' }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.expiry_date') }}:</strong>
                                {{ $doc->expiration_date ?? '-' }}</p>

                            @if ($doc->hasStoredFile())
                                <div class="text-center my-2">
                                    <a href="#" class="view-pdf"
                                        data-file="{{ route('dashboard.document.employee.file', $doc) }}" title="View PDF">
                                        <i class="fas fa-file-pdf text-secondary" style="font-size: 28px;"></i>
                                    </a>
                                </div>
                            @elseif ($doc->file_path)
                                <span class="text-muted small">{{ __('File missing') }}</span>
                            @else
                                -
                            @endif
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted">
                    {{-- <p>No employee documents found.</p> --}}
                </div>
            @endforelse
        </div>
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $employeeDocsCard->links('pagination::bootstrap-5') }}
        </div>



        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.employee_documents') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addEmployeeDocModal">
                                    {{ __('dashboard.add_employee_document') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <form id="employeeDocFilterForm" class="mb-3">
                                        <div class="row g-2">

                                            <!-- Employee dropdown -->
                                            <div class="col-md-3">
                                                <select id="filter_employee" class="form-control">
                                                    <option value="">{{ __('dashboard.all_employees') }}</option>
                                                    @foreach ($employees as $employee)
                                                        <option value="{{ $employee->id }}">
                                                            {{ $employee->first_name }} ({{ $employee->employee_id }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Document Type -->
                                            <div class="col-md-2">
                                                <input type="text" id="filter_type" class="form-control"
                                                    placeholder="{{ __('dashboard.document_type') }}">
                                            </div>

                                            <!-- Document Number -->
                                            <div class="col-md-2">
                                                <input type="text" id="filter_doc_no" class="form-control"
                                                    placeholder="{{ __('dashboard.document_number') }}">
                                            </div>

                                            <!-- Start Date -->
                                            <div class="col-md-2">
                                                <input type="date" id="filter_start" class="form-control">
                                            </div>

                                            <!-- End Date -->
                                            <div class="col-md-2">
                                                <input type="date" id="filter_end" class="form-control">
                                            </div>

                                            <div class="col-md-1">
                                                <button type="button" id="filterBtn"
                                                    class="btn btn-primary w-100">Filter</button>
                                            </div>
                                        </div>
                                    </form>

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>{{ __('dashboard.employee_id') }}</th>
                                                <th>{{ __('dashboard.employee_name') }}</th>
                                                <th>{{ __('dashboard.document_type') }}</th>
                                                <th>{{ __('dashboard.doc_no') }}</th>
                                                <th>{{ __('dashboard.issue_date') }}</th>
                                                <th>{{ __('dashboard.expiry_date') }}</th>
                                                <th>{{ __('dashboard.file') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="employeeDocTableBody">
                                            @include('Admin.Backend.partials.employee_docs_rows', [
                                                'employeeDocs' => $employeeDocs,
                                            ])
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!--Add document modal -->
        <div class="modal fade" id="addEmployeeDocModal" tabindex="-1" aria-labelledby="addEmployeeDocModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEmployeeDocModalLabel">{{ __('dashboard.add_employee_document') }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form id="addEmployeeDocForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.employee') }}</label>
                                    <select class="form-control" name="employee_id" required>
                                        <option selected disabled>{{ __('dashboard.select_employee') }}</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->first_name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-text employee_id_error"></span>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.document_type') }}</label>
                                    <select name="type" class="form-control">
                                        <option selected disabled>{{ __('dashboard.select_document') }}</option>
                                        <option>{{ __('dashboard.iqama') }}</option>
                                        <option>{{ __('dashboard.passport') }}</option>
                                        <option>{{ __('dashboard.employment_contract') }}</option>
                                        <option>{{ __('dashboard.insurance_card') }}</option>
                                        <option>{{ __('dashboard.other') }}</option>
                                    </select>
                                    <span class="text-danger error-text type_error"></span>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.issue_date') }}</label>
                                    <input type="date" class="form-control" name="issue_date">
                                    <span class="text-danger error-text issue_date_error"></span>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.expiry_date') }}</label>
                                    <input type="date" class="form-control" name="expiry_date">
                                    <span class="text-danger error-text expiry_date_error"></span>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>{{ __('dashboard.doc_no') }}</label>
                                    <input type="text" class="form-control" placeholder="e.g. IQM-5678910"
                                        name="doc_number">
                                    <span class="text-danger error-text doc_number_error"></span>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.upload_document') }} (PDF/Image)</label>
                                    <input type="file" class="form-control" name="file">
                                    <span class="text-danger error-text image_error"></span>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.image') }}</label>
                                    <input type="file" class="form-control" name="image">
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ __('dashboard.save_document') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Employee Document Modal -->
        <div class="modal fade" id="editEmployeeDocModal" tabindex="-1" aria-labelledby="editEmployeeDocModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editEmployeeDocModalLabel">
                            {{ __('dashboard.edit_employee_document') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form id="editEmployeeDocForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="doc_id" id="edit_doc_id">

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.employee') }}</label>
                                    <select name="employee_id" class="form-control" id="edit_employee_id">
                                        <option selected disabled>{{ __('dashboard.select_employee') }}</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->first_name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-text employee_id_error"></span>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.document_type') }}</label>
                                    <select name="type" id="edit_type" class="form-control">
                                        <option>{{ __('dashboard.iqama') }}</option>
                                        <option>{{ __('dashboard.passport') }}</option>
                                        <option>{{ __('dashboard.employment_contract') }}</option>
                                        <option>{{ __('dashboard.insurance_card') }}</option>
                                        <option>{{ __('dashboard.other') }}</option>
                                    </select>
                                    <span class="text-danger error-text type_error"></span>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.issue_date') }}</label>
                                    <input type="date" name="issue_date" id="edit_issue_date" class="form-control">
                                    <span class="text-danger error-text issue_date_error"></span>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.expiry_date') }}</label>
                                    <input type="date" name="expiry_date" id="edit_expiry_date" class="form-control">
                                    <span class="text-danger error-text expiry_date_error"></span>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>{{ __('dashboard.doc_no') }}</label>
                                    <input type="text" name="doc_number" id="edit_doc_number" class="form-control">
                                    <span class="text-danger error-text doc_number_error"></span>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label> {{ __('dashboard.file') }}</label>
                                    <input type="file" name="file" id="edit_file" class="form-control">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.image') }} </label>
                                    <input type="file" name="image" id="edit_image" class="form-control">
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ __('dashboard.update_document') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Employee Document Modal -->
        <div class="modal fade" id="deleteEmployeeDocModal" tabindex="-1" aria-labelledby="deleteEmployeeDocModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteEmployeeDocModalLabel">{{ __('dashboard.delete_document') }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <p>{{ __('dashboard.confirm_delete_modal') }}</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                        <button type="button" class="btn btn-danger"
                            id="confirmDeleteEmployeeDoc">{{ __('dashboard.delete') }}</button>
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

            $('#addEmployeeDocForm').on('submit', function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    url: '/dashboard/document/employee/store',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('span.error-text').text(''); // clear validation errors
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#addEmployeeDocModal').modal('hide');
                            $('#addEmployeeDocForm')[0].reset();

                            Swal.fire({
                                title: 'Success!',
                                text: res.message,
                                icon: 'success',
                                timer: 2500,
                                showConfirmButton: false
                            });

                            appendEmployeeDocRow(res.data);
                        }
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

            /*
             * APPEND NEW ROW (AFTER ADD)
             */
            function appendEmployeeDocRow(doc) {
                const html = `
        <tr id="employeeDocRow${doc.id}">
            <td>${doc.employee?.employee_id || doc.employee_id || '—'}</td>
            <td>${doc.employee?.first_name || '—'}</td>
            <td>${doc.type}</td>
            <td>${doc.document_number}</td>
            <td>${doc.issue_date}</td>
            <td>${doc.expiration_date}</td>
            <td>
                ${doc.file_path ? `
                                                                                                                                                                                                                                                                                                                                                                                    <a href="#" class="view-pdf"
                                                                                                                                                                                                                                                                                                                                                                                       data-file="${doc.file_url}"
                                                                                                                                                                                                                                                                                                                                                                                       title="View PDF">
                                                                                                                                                                                                                                                                                                                                                                                       <i class="fas fa-file-pdf text-secondary" style="font-size: 18px;"></i>
                                                                                                                                                                                                                                                                                                                                                                             </a>` : ''}</td>
<td>
                <a href="#" class="text-secondary editEmployeeDocBtn"
                   data-id="${doc.id}"
                   data-employee="${doc.employee_id}"
                   data-type="${doc.type}"
                   data-doc_number="${doc.document_number}"
                   data-issue_date="${doc.issue_date}"
                   data-expiry_date="${doc.expiration_date}">
                   <i class="fas fa-edit"></i>
                </a>

                <a href="#" class="text-danger deleteEmployeeDocBtn" data-id="${doc.id}">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </td>
        </tr>`;
                $('#tableExport tbody').prepend(html);
            }

            /*
             * OPEN EDIT MODAL
             */
            $(document).on('click', '.editEmployeeDocBtn', function(e) {
                e.preventDefault();

                const doc = $(this).data();

                $('#edit_doc_id').val(doc.id);
                $('#edit_employee_id').val(doc.employee);
                $('#edit_type').val(doc.type);
                $('#edit_doc_number').val(doc.doc_number);
                $('#edit_issue_date').val(doc.issue_date);
                $('#edit_expiry_date').val(doc.expiry_date);

                $('#editEmployeeDocModal').modal('show');
            });

            /*
             * UPDATE EMPLOYEE DOCUMENT
             */
            $('#editEmployeeDocForm').on('submit', function(e) {
                // e.preventDefault();

                const docId = $('#edit_doc_id').val();
                let formData = new FormData(this);

                $.ajax({
                    url: `/dashboard/document/employee/update/${docId}`,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('span.error-text').text('');
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#editEmployeeDocModal').modal('hide');

                            Swal.fire({
                                title: 'Updated!',
                                text: res.message,
                                icon: 'success',
                                timer: 2500,
                                showConfirmButton: false,

                            }).then(() => {
                                location.reload(); // reload after success
                            });


                            // Update the row with corrected data structure
                            updateEmployeeDocRow(res.data);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                $(`.${key}_error`).text(value[0]);
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong while updating the document.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                });
            });

            /*
             * UPDATE ROW
             */
            function updateEmployeeDocRow(doc) {
                const row = $(`#employeeDocRow${doc.id}`);

                // Create the updated HTML for the row
                const updatedHtml = `
                    <td>${doc.employee?.employee_id || doc.employee_id || '—'}</td>
                    <td>${doc.employee?.first_name || '—'}</td>
                    <td>${doc.type}</td>
                    <td>${doc.document_number}</td>
                    <td>${doc.issue_date}</td>
                    <td>${doc.expiration_date}</td>
                     <td>

                                                             <a href="#" class="view-pdf"
                                                                   data-file="${doc.file_url}"
                                                                   title="View PDF">
                                                                   <i class="fas fa-file-pdf text-secondary" style="font-size: 18px;"></i>
                                                                </a> : ''}</td>
                    <td>
                        <a href="#" class="text-secondary editEmployeeDocBtn"
                           data-id="${doc.id}"
                           data-employee="${doc.employee_id}"
                           data-type="${doc.type}"
                           data-doc_number="${doc.document_number}"
                           data-issue_date="${doc.issue_date}"
                           data-expiry_date="${doc.expiration_date}">
                           <i class="fas fa-edit"></i>
                        </a>
                        <a href="#" class="text-danger deleteEmployeeDocBtn" data-id="${doc.id}">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                `;

                // Replace the row content
                row.html(updatedHtml);
            }

            /*
             * DELETE EMPLOYEE DOCUMENT
             */
            let deleteDocId = null;

            $(document).on('click', '.deleteEmployeeDocBtn', function(e) {
                e.preventDefault();
                deleteDocId = $(this).data('id');
                $('#deleteEmployeeDocModal').modal('show');
            });

            $('#confirmDeleteEmployeeDoc').on('click', function() {
                if (!deleteDocId) return;

                $.ajax({
                    url: `/dashboard/document/employee/delete/${deleteDocId}`,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#deleteEmployeeDocModal').modal('hide');

                            // Remove the row immediately
                            const rowToRemove = $(`#employeeDocRow${deleteDocId}`);
                            if (rowToRemove.length) {
                                rowToRemove.remove();
                            }

                            Swal.fire({
                                title: 'Deleted!',
                                text: res.message || 'Document deleted successfully.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: res.message || 'Something went wrong.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON?.message ||
                                'Failed to delete the document.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
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

        $(function() {

            function fetchEmployeeDocs() {
                let data = {
                    employee_id: $('#filter_employee').val(),
                    type: $('#filter_type').val(),
                    document_number: $('#filter_doc_no').val(),
                    start_date: $('#filter_start').val(),
                    end_date: $('#filter_end').val(),
                };

                $.ajax({
                    url: "{{ route('employeeDocs.filter') }}",
                    data: data,
                    beforeSend: function() {
                        $('#employeeDocTableBody').html(
                            '<tr><td colspan="8" class="text-center">Loading...</td></tr>'
                        );
                    },
                    success: function(res) {
                        $('#employeeDocTableBody').html(res.html);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert("Error fetching filtered data!");
                    }
                });
            }

            $("#filterBtn").click(function() {
                fetchEmployeeDocs();
            });

        });
    </script>
@endsection
