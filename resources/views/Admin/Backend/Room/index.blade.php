@extends('layout.master')
@section('title', 'Dashboard | Rooms')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.all_sections') }}</h1>
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.section') }}</h4>
                                @if (Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('warehouse_manager'))
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                        data-target="#roomModal">
                                        {{ __('dashboard.add_section') }}
                                    </button>
                                @endif
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>{{ __('dashboard.section_id') }}</th>
                                                <th>{{ __('dashboard.section_name') }}</th>
                                                <th>{{ __('dashboard.warehouse') }}</th>
                                                @if (Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('warehouse_manager'))
                                                    <th>{{ __('dashboard.action') }}</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($rooms as $room)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $room->name }}</td>
                                                    <td>{{ $room->warehouse->name }}</td>
                                                    @if (Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('warehouse_manager'))
                                                        <td>
                                                            <!-- Edit Warehouse Button -->
                                                            <a href="#" class="text-secondary editWarehouseBtn"
                                                                data-id="{{ $room->id }}"
                                                                data-name="{{ $room->name }}"
                                                                data-branch-id="{{ $room->warehouse_id }}">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="#" class="text-danger deleteWarehouseBtn"
                                                                data-id="{{ $room->id }}"
                                                                data-name="{{ $room->name }}" data-toggle="modal"
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
        <div class="modal fade" id="roomModal" tabindex="-1" aria-labelledby="addDeptModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="addDeptModalLabel">{{ __('dashboard.add_section') }}</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <form id="addRoomForm" method="post" action="{{ route('dashboard.room.store') }}">
                            @csrf
                            <div class="row">
                                <!-- Branch -->
                                <div class="form-group col-md-6">

                                    <label class="form-label">{{ __('dashboard.branch_name') }}</label>
                                    <label class="form-label">{{ __('dashboard.warehouse_name') }}:</label>
                                    <select name="warehouse_id" class="form-control" required>
                                        <option value="">-- {{ __('dashboard.select_warehouse') }} --</option>

                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">
                                                {{ $warehouse->branch->name ?? 'N/A' }} →
                                                {{ $warehouse->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                                <!-- Department Name -->
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('dashboard.section_name') }}</label>
                                    <input type="text" class="form-control" name="room_name"
                                        placeholder="e.g., {{ __('dashboard.section_name') }}" required>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="text-end mt-3">
                                <button type="reset" class="btn btn-secondary me-2">{{ __('dashboard.reset') }}</button>
                                <button type="submit" class="btn btn-primary">{{ __('dashboard.add_section') }}</button>
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
                        <h5 class="modal-title" id="editWarehouseModalLabel">{{ __('dashboard.edit_section') }}</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form id="editWarehouseForm" method="POST" action="{{ route('dashboard.room.update') }}">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="id" id="edit_room_id">

                            <div class="row">
                                <!-- Branch -->
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('dashboard.warehouse') }}</label>
                                    <select name="warehouse_id" id="edit_warehouse_id" class="form-control" required>
                                        <option value="">-- {{ __('dashboard.select_warehouse') }} --</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Warehouse Name -->
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('dashboard.section_name') }}</label>
                                    <input type="text" class="form-control" id="edit_room_name" name="name"
                                        required>
                                </div>
                            </div>

                            <div class="text-end mt-3">
                                <button type="submit"
                                    class="btn btn-primary">{{ __('dashboard.update_section') }}</button>
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
                        <h5 class="modal-title">{{ __('dashboard.delete_section') }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <form id="deleteWarehouseForm" method="POST" action="{{ route('dashboard.room.delete') }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="room_id" id="delete_room_id">

                        <div class="modal-body text-center">
                            <p>{{ __('dashboard.section_delete') }} <strong id="delete_room_name"></strong>?</p>
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
    <script>
        $(document).on('submit', '#addRoomForm', function(e) {
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
                        $('#roomModal').modal('hide');
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
                const warehouseId = $(this).data('branch-id');

                $('#edit_room_id').val(id);
                $('#edit_room_name').val(name);
                $('#edit_warehouse_id').val(warehouseId);

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

            $('#delete_room_id').val(id);
            $('#delete_room_name').text(name);

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

        });
    </script>
@endsection
