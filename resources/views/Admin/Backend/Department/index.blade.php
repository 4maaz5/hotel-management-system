@extends('layout.master')
@section('title', 'Dashboard | Department')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.all_departments') }}</h1>
        <div class="departments-grid">
            @forelse($departmentCards as $department)
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-2">{{ $department->name }}</h6>
                            <p class="mb-1"><strong>{{ __('dashboard.department_id') }}:</strong> {{ $loop->iteration }}
                            </p>
                            <p class="mb-1"><strong>{{ __('dashboard.branch') }}:</strong>
                                {{ $department->branch->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center col-span-full">
                    {{-- <p>No departments found.</p> --}}
                </div>
            @endforelse
        </div>
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $departmentCards->links('pagination::bootstrap-5') }}
        </div>


        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.departments') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addDeptModal">
                                    {{ __('dashboard.add_department') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <form id="departmentFilterForm" class="mb-3">
                                        <div class="row g-2 align-items-end">

                                            <div class="col-md-4">
                                                <label class="form-label">{{ __('dashboard.department_name') }}</label>
                                                <input type="text" name="name" id="filter_dept_name"
                                                    class="form-control"
                                                    placeholder="{{ __('dashboard.search_by_name') }}">
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">{{ __('dashboard.branch') }}</label>
                                                <select name="branch_id" id="filter_branch_id" class="form-control">
                                                    <option value="">{{ __('dashboard.all_branches') }}</option>
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-2 d-grid">
                                                <button type="button" id="departmentFilterBtn"
                                                    class="btn btn-primary">{{ __('dashboard.filter') }}</button>
                                            </div>

                                        </div>
                                    </form>

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>{{ __('dashboard.department_id') }}</th>
                                                <th>{{ __('dashboard.department_name') }}</th>
                                                <th>{{ __('dashboard.branch') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($departments as $department)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $department->name }}</td>
                                                    <td>{{ $department->branch->name }}</td>
                                                    <td>

                                                        <a href="#" class="text-secondary editDepartmentBtn"
                                                            data-id="{{ $department->id }}"
                                                            data-name="{{ $department->name }}"
                                                            data-branch-id="{{ $department->branch_id }}"
                                                            data-branch-name="{{ $department->branch->name }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="#" class="text-danger deleteDepartmentBtn"
                                                            data-id="{{ $department->id }}"
                                                            data-name="{{ $department->name }}" data-toggle="modal"
                                                            data-target="#deleteDeptModal">
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

        <!-- Add Department Modal -->
        <div class="modal fade" id="addDeptModal" tabindex="-1" aria-labelledby="addDeptModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="addDeptModalLabel">{{ __('dashboard.add_department') }}</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <form id="addDeptForm" method="post" action="{{ route('dashboard.branch.department.store') }}">
                            @csrf
                            <div class="row">
                                {{-- <!-- Branch -->
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('dashboard.company') }}</label>
                                    <select name="branch_id" class="form-control" required>
                                        <option value="">-- Select Company --</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Branch -->
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('dashboard.branch') }}</label>
                                    <select name="branch_id" class="form-control" required>
                                        <option value="">-- Select Brand --</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                </div> --}}
                                <!-- Branch -->
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('dashboard.branch') }}:</label>
                                    <label
                                        class="form-label">{{ __('dashboard.company') }}→{{ __('dashboard.brand') }}→{{ __('dashboard.branch') }}</label>
                                    <select name="branch_id" class="form-control" required>
                                        <option value="">-- {{ __('dashboard.select_branch') }} --</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">
                                                {{ $branch->company->name ?? 'N/A' }} →
                                                {{ $branch->brand->name ?? 'N/A' }} → {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>


                                <!-- Department Name -->
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('dashboard.department_name') }}</label>
                                    <input type="text" class="form-control" name="dep_name"
                                        placeholder="e.g., {{ __('dashboard.housekeeping') }}" required>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="text-end mt-3">
                                <button type="reset" class="btn btn-secondary me-2">{{ __('dashboard.reset') }}</button>
                                <button type="submit"
                                    class="btn btn-primary">{{ __('dashboard.add_department') }}</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>


        <!-- Edit Department Modal -->
        <div class="modal fade" id="editDeptModal" tabindex="-1" aria-labelledby="editDeptModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="editDeptModalLabel">{{ __('dashboard.edit_department') }}</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form id="editDeptForm" method="POST"
                            action="{{ route('dashboard.branch.department.update') }}">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="id" id="edit_department_id">

                            <div class="row">
                                <!-- Branch -->
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('dashboard.branch') }}</label>
                                    <select name="branch_id" id="edit_branch_id" class="form-control" required>
                                        <option value="">-- {{ __('dashboard.select_branch') }} --</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Department Name -->
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('dashboard.branch_name') }}</label>
                                    <input type="text" class="form-control" id="edit_dep_name" name="dep_name"
                                        required>
                                </div>
                            </div>

                            <div class="text-end mt-3">
                                <button type="submit"
                                    class="btn btn-primary">{{ __('dashboard.update_department') }}</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- Delete Department Modal -->
        <div class="modal fade" id="deleteDeptModal" tabindex="-1" aria-labelledby="deleteDeptModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">{{ __('dashboard.delete_department') }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <form id="deleteDeptForm" method="POST" action="{{ route('dashboard.branch.department.delete') }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="department_id" id="delete_department_id">

                        <div class="modal-body text-center">
                            <p>{{ __('dashboard.department_delete') }}<strong id="delete_department_name"></strong>?</p>
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
        $(document).on('submit', '#addDeptForm', function(e) {
            e.preventDefault();

            let $form = $(this);
            let $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        $('#addDeptModal').modal('hide');
                        $form[0].reset();

                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Append new row to the table
                        let newRow = `
<tr>
     <td>New</td>
    <td>${res.data.name}</td>
    <td>${res.data.branch}</td>
    <td>
        <a href="#" class="text-secondary editDepartmentBtn"
           data-id="${res.data.id}"
           data-name="${res.data.name}"
           data-branch-id="${res.data.branch_id}"
           data-branch-name="${res.data.branch}">
           <i class="fas fa-edit"></i>
        </a>
        <a href="#" class="text-danger deleteDepartmentBtn"
           data-id="${res.data.id}"
           data-name="${res.data.name}">
           <i class="fas fa-trash-alt"></i>
        </a>
    </td>
</tr>`;
                        $('#tableExport tbody').append(newRow);


                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Something went wrong',
                    });
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        });
        // ----------------------
        //  Open Edit Modal
        // ----------------------
        $(document).on('click', '.editDepartmentBtn', function() {
            const id = $(this).attr('data-id');
            const name = $(this).attr('data-name');
            const branchId = $(this).attr('data-branch-id');
            const branchName = $(this).attr('data-branch-name');

            $('#edit_department_id').val(id);
            $('#edit_dep_name').val(name);

            if ($('#edit_branch_id option[value="' + branchId + '"]').length === 0) {
                $('#edit_branch_id').append(`<option value="${branchId}">${branchName}</option>`);
            }

            $('#edit_branch_id').val(branchId);
            $('#editDeptModal').modal('show');
        });




        // ----------------------
        // Submit Edit Form via AJAX
        // ----------------------
        $(document).on('submit', '#editDeptForm', function(e) {
            e.preventDefault();

            const $form = $(this);
            const $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST', // Laravel treats PUT via _method
                data: $form.serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        $('#editDeptModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Find the row
                        let row = $('#tableExport tbody')
                            .find(`.editDepartmentBtn[data-id="${res.data.id}"]`)
                            .closest('tr');

                        // Update table cells
                        row.find('td:nth-child(2)').text(res.data.name); // Department Name
                        row.find('td:nth-child(3)').text(res.data.branch); // Branch Name

                        // 🔹 Update the edit button attributes dynamically
                        let editBtn = row.find('.editDepartmentBtn');
                        editBtn.attr('data-name', res.data.name); // update name
                        editBtn.attr('data-branch-id', res.data.branch_id); // update branch id
                        editBtn.attr('data-branch-name', res.data.branch); // update branch name

                        // 🔹 Update delete button if you use name there
                        row.find('.deleteDepartmentBtn').attr('data-name', res.data.name);
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Something went wrong',
                    });
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        });



        // ----------------------
        //  Open Delete Modal
        // ----------------------
        $(document).on('click', '.deleteDepartmentBtn', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');

            $('#delete_department_id').val(id);
            $('#delete_department_name').text(name);

            $('#deleteDeptModal').modal('show');
        });

        // ----------------------
        // Submit Delete Form (AJAX)
        // ----------------------
        $(document).on('submit', '#deleteDeptForm', function(e) {
            e.preventDefault();

            const $form = $(this);
            const departmentId = $('#delete_department_id').val();
            const url = $form.attr('action');
            const $btn = $form.find('button[type="submit"]');

            $btn.prop('disabled', true);

            $.ajax({
                url: url,
                method: 'POST', // because @method('DELETE') is included
                data: $form.serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        $('#deleteDeptModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // Remove the row
                        $(`#tableExport tbody tr`).filter(function() {
                            return $(this).find('.deleteDepartmentBtn').data('id') ==
                                departmentId;
                        }).remove();

                        // Reorder serial numbers
                        $('#tableExport tbody tr').each(function(index) {
                            $(this).find('td:first').text(index + 1); // first td is serial #
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Something went wrong',
                    });
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        });

        $(function() {
            function fetchDepartments() {
                $.ajax({
                    url: "{{ route('departments.filter') }}",
                    method: "GET",
                    data: {
                        name: $('#filter_dept_name').val(),
                        branch_id: $('#filter_branch_id').val()
                    },
                    success: function(res) {
                        $('tbody').html(res.html);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            }

            $('#departmentFilterBtn').on('click', function() {
                fetchDepartments();
            });

        });
    </script>
@endsection
