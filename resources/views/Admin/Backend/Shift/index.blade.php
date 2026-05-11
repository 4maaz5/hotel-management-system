@extends('layout.master')
@section('title', 'Dashboard | Shifts')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.employee_shift') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addShiftModal">
                                    {{ __('dashboard.add_shift') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>{{ __('dashboard.shift_name') }}</th>
                                                <th>{{ __('dashboard.start_time') }}</th>
                                                <th>{{ __('dashboard.end_time') }}</th>
                                                <th>{{ __('dashboard.branch') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="companyDocumentsTbody">
                                            @forelse ($shifts as $shift)
                                                <tr id="companyDocRow{{ $shift->id }}">
                                                    <td>{{ $shift->name }}</td>
                                                    <td>{{ $shift->branch->name }}</td>
                                                    <td>{{ $shift->start_time }}</td>
                                                    <td>{{ $shift->end_time }}</td>
                                                    <td>
                                                        <!-- Edit -->
                                                        <a class="text-warning editShiftBtn" data-toggle="modal"
                                                            data-target="#editShiftModal__{{ $shift->id }}"
                                                            title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <a href="#" class="text-danger deleteShiftBtn"
                                                            data-id="{{ $shift->id }}" data-name="{{ $shift->name }}"
                                                            title="Delete">
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
        <div class="modal fade" id="addShiftModal" tabindex="-1" aria-labelledby="addShiftModalLabel" aria-hidden="true">

            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="addShiftModalLabel">
                            {{ __('dashboard.add_shift') }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <form method="Post" action="{{ route('dashboard.shift.store') }}" id="addShiftForm"
                            enctype="multipart/form-data">
                            @csrf

                            <!-- Shift Name -->
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.shift_name') }}</label>
                                <input type="text" class="form-control"
                                    placeholder="e.g., {{ __('dashboard.morning_shift') }}" name="name">
                                <span class="text-danger error-text name_error"></span>
                            </div>

                            <!-- Branch -->
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.branch') }}</label>
                                <select name="branch_id" class="form-control">
                                    <option value="">{{ __('dashboard.select_branch') }}</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">
                                            {{ $branch->company->name ?? 'N/A' }} →
                                            {{ $branch->brand->name ?? 'N/A' }} → {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text branch_id_error"></span>
                            </div>

                            <!-- Start Time -->
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.start_time') }}</label>
                                <input type="time" class="form-control" name="start_time">
                                <span class="text-danger error-text start_time_error"></span>
                            </div>

                            <!-- End Time -->
                            <div class="mb-3">
                                <label class="form-label">{{ __('dashboard.end_time') }}</label>
                                <input type="time" class="form-control" name="end_time">
                                <span class="text-danger error-text end_time_error"></span>
                            </div>

                            <!-- Submit / Reset Buttons -->
                            <div class="text-end">
                                <button type="reset" class="btn btn-secondary me-2">
                                    {{ __('dashboard.reset') }}
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    {{ __('dashboard.save_shift') }}
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>


        <!-- Edit Company Document Modal -->
        @forelse ($shifts as $shift)
            <div class="modal fade" id="editShiftModal__{{ $shift->id }}" tabindex="-1"
                aria-labelledby="editShiftModalLabel__{{ $shift->id }}" aria-hidden="true">

                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h5 class="modal-title" id="editShiftModalLabel__{{ $shift->id }}">
                                {{ __('dashboard.edit_shift') }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="modal-body">
                            <form id="editShiftForm__{{ $shift->id }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="shift_id" value="{{ $shift->id }}">

                                <!-- Shift Name -->
                                <div class="mb-3">
                                    <label class="form-label">{{ __('dashboard.shift_name') }}</label>
                                    <input type="text" class="form-control" name="name"
                                        value="{{ $shift->name }}"
                                        placeholder="e.g., {{ __('dashboard.morning_shift') }}">
                                    <span class="text-danger error-text name_error"></span>
                                </div>

                                <!-- Branch -->
                                <div class="mb-3">
                                    <label class="form-label">{{ __('dashboard.branch') }}</label>
                                    <select name="branch_id" class="form-control">
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ $shift->branch_id == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-text branch_id_error"></span>
                                </div>

                                <!-- Start Time -->
                                <div class="mb-3">
                                    <label class="form-label">{{ __('dashboard.start_time') }}</label>
                                    <input type="time" class="form-control" name="start_time"
                                        value="{{ $shift->start_time }}">
                                    <span class="text-danger error-text start_time_error"></span>
                                </div>

                                <!-- End Time -->
                                <div class="mb-3">
                                    <label class="form-label">{{ __('dashboard.end_time') }}</label>
                                    <input type="time" class="form-control" name="end_time"
                                        value="{{ $shift->end_time }}">
                                    <span class="text-danger error-text end_time_error"></span>
                                </div>

                                <!-- Buttons -->
                                <div class="text-end">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        {{ __('dashboard.cancel') }}
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('dashboard.update_shift') }}
                                    </button>
                                </div>

                            </form>
                        </div>

                    </div>
                </div>
            </div>
        @empty

        @endforelse

        <!-- Delete Shift Modal -->
        <div class="modal fade" id="deleteShiftModal" tabindex="-1" role="dialog"
            aria-labelledby="deleteShiftModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteShiftModalLabel">{{ __('dashboard.delete_shift') }}</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <p>{{ __('dashboard.confirm_delete_modal') }}</p>
                        <div class="mb-2">
                            <strong>{{ __('dashboard.shift_name') }}:</strong> <span id="deleteShiftName"></span>
                        </div>
                        <input type="hidden" id="deleteShiftId">
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">
                            {{ __('dashboard.cancel') }}
                        </button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteShiftBtn">
                            {{ __('dashboard.delete') }}
                        </button>
                    </div>

                </div>
            </div>
        </div>


    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {

            $("#addShiftForm").on('submit', function(e) {
                e.preventDefault();

                let form = this;

                $.ajax({
                    url: "{{ route('dashboard.shift.store') }}",
                    method: "POST",
                    data: new FormData(form),
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $(form).find("span.error-text").text("");
                    },
                    success: function(response) {

                        if (response.status == 0) {
                            // Show validation errors
                            $.each(response.error, function(prefix, val) {
                                $(form).find("span." + prefix + "_error").text(val[0]);
                            });
                        }

                        if (response.status == 1) {
                            // Success
                            Swal.fire({
                                icon: 'success',
                                title: 'Success/نجاح',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            // Reset form
                            form.reset();

                            // Close modal
                            $("#addShiftModal").modal('hide');

                            // Reload table or page
                            location.reload();
                        }
                    }
                });
            });

        });

        $(document).ready(function() {

            // Handle ALL edit forms dynamically
            $(document).on('submit', 'form[id^="editShiftForm__"]', function(e) {
                e.preventDefault();

                let form = this;
                let shiftId = $(form).find('input[name="shift_id"]').val();

                $.ajax({
                    url: "{{ route('dashboard.shift.update') }}",
                    method: "POST", // PUT also ok but POST+_method is used
                    data: new FormData(form),
                    processData: false,
                    contentType: false,

                    beforeSend: function() {
                        $(form).find("span.error-text").text("");
                    },

                    success: function(response) {

                        if (response.status == 0) {
                            $.each(response.error, function(prefix, val) {
                                $(form).find('span.' + prefix + '_error').text(val[0]);
                            });
                        }

                        if (response.status == 1) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Updated/تم التحديث!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            // Close modal
                            $("#editShiftModal__" + shiftId).modal('hide');

                            // Refresh page or table
                            location.reload();
                        }
                    }
                });

            });

        });

        $(document).ready(function() {

            // Open modal & set shift data
            $(document).on('click', '.deleteShiftBtn', function(e) {
                e.preventDefault();

                let id = $(this).data('id');
                let name = $(this).data('name');

                $('#deleteShiftId').val(id);
                $('#deleteShiftName').text(name);

                $('#deleteShiftModal').modal('show');
            });

            // Confirm delete
            $('#confirmDeleteShiftBtn').click(function() {
                let id = $('#deleteShiftId').val();

                $.ajax({
                    url: "/dashboard/shift/" + id, // RESTful route
                    method: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.status == 1) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted/تم الحذف!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            // Close modal
                            $('#deleteShiftModal').modal('hide');

                            // Remove row or reload
                            location.reload();
                        }
                    }
                });
            });

        });
    </script>


@endsection
