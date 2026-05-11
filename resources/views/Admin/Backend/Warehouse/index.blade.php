@extends('layout.master')
@section('title', 'Dashboard | Warehouse')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.all_warehouses') }}</h1>
        <div class="departments-grid">

        </div>
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{-- {{ $departmentCards->links('pagination::bootstrap-5') }} --}}
        </div>


        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.warehouse') }}</h4>
                                @if (Auth::user()->hasRole('super_admin'))
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                        data-target="#warehouseModal">
                                        {{ __('dashboard.add_warehouse') }}
                                    </button>
                                @endif

                            </div>

                            <div class="card-body">
                                <div class="table-responsive">


                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>{{ __('dashboard.warehouse_id') }}</th>
                                                <th>{{ __('dashboard.warehouse_name') }}</th>
                                                <th>{{ __('dashboard.branch') }}</th>
                                                <th>{{ __('dashboard.type') }}</th>
                                                @if (Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('warehouse_manager'))
                                                    <th>{{ __('dashboard.action') }}</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($warehouses as $warehouse)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $warehouse->name }}</td>
                                                    <td>{{ $warehouse->branch->name ?? '-' }}</td>
                                                    <td>{{ $warehouse->type }}</td>
                                                    @if (Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('warehouse_manager'))
                                                        <td>

                                                            <a href="javascript:void(0)"
                                                                class="text-info viewWarehouseReportBtn"
                                                                data-id="{{ $warehouse->id }}" title="View Report">
                                                                <i class="fas fa-eye"></i>
                                                            </a>

                                                            <!-- Edit Warehouse Button -->
                                                            <a href="#" class="text-secondary editWarehouseBtn"
                                                                data-id="{{ $warehouse->id }}"
                                                                data-name="{{ $warehouse->name }}"
                                                                data-branch-id="{{ $warehouse->branch_id }}"
                                                                data-type="{{ $warehouse->type }}">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="#" class="text-danger deleteWarehouseBtn"
                                                                data-id="{{ $warehouse->id }}"
                                                                data-name="{{ $warehouse->name }}" data-toggle="modal"
                                                                data-target="#deleteWarehouseModal">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </a>

                                                        </td>
                                                    @endif
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
        <div class="modal fade" id="warehouseModal" tabindex="-1" aria-labelledby="addDeptModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="addDeptModalLabel">{{ __('dashboard.add_warehouse') }}</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <form id="addWarehouseForm" method="post" action="{{ route('dashboard.warehouse.store') }}">
                            @csrf
                            <div class="row">
                                <!-- Branch -->
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('dashboard.warehouse_name') }}:</label>
                                    <label
                                        class="form-label">{{ __('dashboard.company') }}→{{ __('dashboard.brand') }}→{{ __('dashboard.branch') }}</label>
                                    <select name="branch_id" class="form-control">
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
                                    <label class="form-label">{{ __('dashboard.warehouse_name') }}</label>
                                    <input type="text" class="form-control" name="warehouse_name"
                                        placeholder="e.g., {{ __('dashboard.warehouse_name') }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="type">{{ __('dashboard.warehouse_type') }}</label>
                                <select name="type" id="type" class="form-control" required>
                                    <option value="">-- {{ __('dashboard.select_warehouse_type') }} --</option>
                                    <option value="main">{{ __('dashboard.main_warehouse') }}</option>
                                    <option value="branch">{{ __('dashboard.branch_warehouse') }}</option>
                                </select>
                            </div>


                            <!-- Buttons -->
                            <div class="text-end mt-3">
                                <button type="reset" class="btn btn-secondary me-2">{{ __('dashboard.reset') }}</button>
                                <button type="submit" class="btn btn-primary">{{ __('dashboard.add_warehouse') }}</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>


        <!-- Edit Warehouse Modal -->
        <div class="modal fade" id="editWarehouseModal" tabindex="-1" aria-labelledby="editWarehouseModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="editWarehouseModalLabel">{{ __('dashboard.edit_warehouse') }}</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form id="editWarehouseForm" method="POST" action="{{ route('dashboard.warehouse.update') }}">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="id" id="edit_warehouse_id">

                            <div class="row">
                                <!-- Branch -->
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('dashboard.branch') }}</label>
                                    <select name="branch_id" id="edit_branch_id" class="form-control">
                                        <option value="">-- {{ __('dashboard.select_branch') }} --</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Warehouse Name -->
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('dashboard.warehouse_name') }}</label>
                                    <input type="text" class="form-control" id="edit_warehouse_name" name="name"
                                        required>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Warehouse Type -->
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('dashboard.type') }}</label>
                                    <select name="type" id="edit_warehouse_type" class="form-control">
                                        <option value="">-- {{ __('dashboard.select_type') }} --</option>
                                        <option value="main">{{ __('dashboard.main_warehouse') }}</option>
                                        <option value="branch">{{ __('dashboard.branch_warehouse') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="text-end mt-3">
                                <button type="submit"
                                    class="btn btn-primary">{{ __('dashboard.update_warehouse') }}</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>


        <!-- Delete Department Modal -->
        <div class="modal fade" id="deleteWarehouseModal" tabindex="-1" aria-labelledby="deleteWarehouseModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">{{ __('dashboard.delete_warehouse') }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <form id="deleteWarehouseForm" method="POST" action="{{ route('dashboard.warehouse.delete') }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="warehouse_id" id="delete_warehouse_id">

                        <div class="modal-body text-center">
                            <p>{{ __('dashboard.warehouse_delete') }} <strong id="delete_warehouse_name"></strong>?</p>
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

        <div class="modal fade" id="warehouseReportModal" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('dashboard.warehouse_report') }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body" id="warehouseReportContent">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                        </div>
                    </div>

                </div>
            </div>
        </div>


    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).on('submit', '#addWarehouseForm', function(e) {
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
                        $('#warehouseModal').modal('hide');
                        $form[0].reset();

                        Swal.fire({
                            icon: 'success',
                            title: 'Success/نجاح',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });

                        // Append new row to the table
                        let newRow = `
<tr>
     <td>New</td>
    <td>${res.data.name}</td>
    <td>${res.data.branch}</td>
    <td>${res.data.type}</td>
    <td>
        <a href="#" class="text-secondary editDepartmentBtn"
           data-id="${res.data.id}"
           data-name="${res.data.name}"
           data-branch-id="${res.data.branch_id}"
           data-branch-name="${res.data.branch}"
           data-type="${res.data.type}">
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
                        title: 'Error/خطأ',
                        text: xhr.responseJSON?.message || 'Something went wrong',
                    });
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        });
        $(document).ready(function() {
            $('.editWarehouseBtn').click(function(e) {
                e.preventDefault();

                const id = $(this).data('id');
                const name = $(this).data('name');
                const branchId = $(this).data('branch-id');
                const type = $(this).data('type'); // New: get type

                // Set form values
                $('#edit_warehouse_id').val(id);
                $('#edit_warehouse_name').val(name);
                $('#edit_warehouse_type').val(type);

                // Handle branch dropdown: hide/disable if main
                if (type === 'main') {
                    $('#edit_branch_id').val('').prop('disabled', true);
                } else {
                    $('#edit_branch_id').val(branchId).prop('disabled', false);
                }

                // Show modal
                $('#editWarehouseModal').modal('show');
            });
        });


        // Submit Edit Warehouse Form via AJAX
        $(document).on('submit', '#editWarehouseForm', function(e) {
            e.preventDefault();

            const $form = $(this);
            const $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        $('#editWarehouseModal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!/تم التحديث',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Find the row corresponding to this warehouse
                        let row = $('#tableExport tbody')
                            .find(`.editWarehouseBtn[data-id="${res.data.id}"]`)
                            .closest('tr');

                        // Update table cells
                        row.find('td:nth-child(2)').text(res.data.name); // Warehouse Name
                        row.find('td:nth-child(3)').text(res.data.branch); // Branch Name

                        //  Update the edit button attributes dynamically
                        let editBtn = row.find('.editWarehouseBtn');
                        editBtn.attr('data-name', res.data.name);
                        editBtn.attr('data-branch-id', res.data.branch_id);
                        editBtn.attr('data-branch-name', res.data.branch);

                        //  Update delete button if you use name there
                        row.find('.deleteWarehouseBtn').attr('data-name', res.data.name);
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error/خطأ',
                        text: xhr.responseJSON?.message || 'Something went wrong',
                    });
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        });


        //  Open Delete Modal
        $(document).on('click', '.deleteWarehouseBtn', function(e) {
            e.preventDefault();

            const id = $(this).data('id');
            const name = $(this).data('name');

            $('#delete_warehouse_id').val(id);
            $('#delete_warehouse_name').text(name);

            $('#deleteWarehouseModal').modal('show');
        });


        // Submit Delete Warehouse
        $(document).on('submit', '#deleteWarehouseForm', function(e) {
            e.preventDefault();

            const $form = $(this);
            const $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST', // DELETE via _method
                data: $form.serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        $('#deleteWarehouseModal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!/تم الحذف',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Remove the row from the table
                        $('#tableExport tbody')
                            .find(`.deleteWarehouseBtn[data-id="${res.data.id}"]`)
                            .closest('tr')
                            .remove();
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error/خطأ',
                        text: xhr.responseJSON?.message || 'Something went wrong',
                    });
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        });

        $('.viewWarehouseReportBtn').on('click', function() {
            let warehouseId = $(this).data('id');

            $('#warehouseReportModal').modal('show');
            $('#warehouseReportContent').html(
                '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i></div>'
            );

            $.get("{{ url('/dashboard/warehouses') }}/" + warehouseId + "/report", function(data) {
                $('#warehouseReportContent').html(data);
            });
        });
    </script>
@endsection
