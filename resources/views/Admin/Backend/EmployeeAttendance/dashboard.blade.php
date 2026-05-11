@extends('layout.master')
@section('title', 'Dashboard | Attendance')
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
                                            <h5 class="font-15">{{ __('dashboard.total_attendance') }}</h5>
                                            <h2 class="mb-3 font-18">{{ $totalAttendance }}</h2>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                        <div class="banner-img">
                                            <img src="{{ asset('img/banner/5.jpg') }}" alt="Image Not Found">
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
                                            <h5 class="font-15"> {{ __('dashboard.present_today') }}</h5>
                                            <h2 class="mb-3 font-18">{{ $presentToday }}</h2>

                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                        <div class="banner-img">
                                            <img src="{{ asset('img/banner/6.jpg') }}" alt="Image Not Found">
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
                                            <h5 class="font-15">{{ __('dashboard.absent_today') }}</h5>
                                            <h2 class="mb-3 font-18">{{ $absentToday }}</h2>

                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                        <div class="banner-img">
                                            <img src="{{ asset('img/banner/5.jpg') }}" alt="Image Not Found">
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
                                            <h5 class="font-15">{{ __('dashboard.attendance') }} %</h5>
                                            <h2 class="mb-3 font-18">{{ $attendancePercentage }}</h2>

                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                        <div class="banner-img">
                                            <img src="{{ asset('img/banner/6.jpg') }}" alt="Image Not Found">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <h2 class="text-center mb-4">{{ __('dashboard.attendance_records') }}</h2>

            <div class="d-flex flex-wrap justify-content-start" id="attendanceCardsContainer">
                @forelse ($attendancesCards as $attendance)
                    <div class="attendance-card-wrapper">
                        <div class="card shadow-sm h-100 attendance-card">
                            <div class="card-body d-flex flex-column justify-content-between">

                                <!-- Employee Info -->
                                <div>
                                    <h5 class="card-title">
                                        {{ $attendance->employee->first_name ?? 'N/A' }}
                                        {{ $attendance->employee->last_name ?? '' }}
                                    </h5>

                                    <p class="card-text mb-2">
                                        <strong>{{ __('dashboard.date') }}:</strong> {{ $attendance->date }} <br>
                                        <strong>{{ __('dashboard.check_in') }}:</strong>
                                        {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '-' }}
                                        <br>
                                        <strong>{{ __('dashboard.check_out') }}:</strong>
                                        {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '-' }}
                                        <br>
                                        <strong>{{ __('dashboard.status') }}:</strong>
                                        @if ($attendance->status == 'Present')
                                            <span class="badge badge-success">{{ __('dashboard.present') }}</span>
                                        @elseif ($attendance->status == 'Absent')
                                            <span class="badge badge-danger">{{ __('dashboard.absent') }}</span>
                                        @else
                                            <span class="badge badge-warning">{{ __('dashboard.leave') }}</span>
                                        @endif
                                        <br>
                                        <strong>{{ __('dashboard.overtime') }}:</strong>
                                        {{ $attendance->overtime_hours ?? '-' }}
                                    </p>
                                </div>



                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $attendancesCards->links('pagination::bootstrap-5') }}
            </div>

            <div class="card-body" style="background-color: white;">
                <div class="table-responsive">
                    <form id="attendanceFilterForm" class="mb-3">
                        <div class="row g-3">


                            <div class="col-md-3">
                                <label>{{ __('dashboard.employee') }}</label>
                                <select name="employee_id" id="employee_id" class="form-control">
                                    <option value="">{{ __('dashboard.all_employees') }}</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->first_name }}
                                            {{ $emp->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>{{ __('dashboard.start_date') }}</label>
                                <input type="date" name="start_date" id="start_date" class="form-control">
                            </div>

                            <div class="col-md-3">
                                <label>{{ __('dashboard.end_date') }}</label>
                                <input type="date" name="end_date" id="end_date" class="form-control">
                            </div>

                            <div class="col-md-2">
                                <label>{{ __('dashboard.status') }}</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">{{ __('dashboard.all_status') }}</option>
                                    <option value="Present">{{ __('dashboard.present') }}</option>
                                    <option value="Absent">{{ __('dashboard.absent') }}</option>
                                    <option value="Leave">{{ __('dashboard.leave') }}</option>
                                </select>
                            </div>

                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" id="filterBtn"
                                    class="btn btn-primary w-100">{{ __('dashboard.filter') }}</button>
                            </div>
                        </div>
                    </form>

                    <!-- Div to show filtered results -->
                    <div id="attendanceResults"></div>
                    <table class="table table-striped table-bordered" id="tableExport" style="width:100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('dashboard.employee') }}</th>
                                <th>{{ __('dashboard.date') }}</th>
                                <th>{{ __('dashboard.check_in') }}</th>
                                <th>{{ __('dashboard.check_out') }}</th>
                                <th>{{ __('dashboard.status') }}</th>
                                <th>{{ __('dashboard.overtime') }}</th>
                                <th>{{ __('dashboard.status') }}</th>
                                <th>{{ __('dashboard.action') }}</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceTableBody">
                            @include('Admin.Backend.partials.attendance')
                        </tbody>
                    </table>
                </div>
            </div>


        </section>
        <!-- Edit Attendance Modal -->
        <div class="modal fade" id="editAttendanceModal" tabindex="-1" role="dialog"
            aria-labelledby="editAttendanceModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="editAttendanceModalLabel">{{ __('dashboard.edit_attendance') }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form id="editAttendanceForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="editAttendanceId">

                        <div class="modal-body">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="editEmployeeName">{{ __('dashboard.employee') }}</label>
                                    <select id="editEmployeeName" class="form-control" name="employee_id" required>
                                        <option disabled>{{ __('dashboard.select_employee') }}</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}">
                                                {{ $employee->first_name . ' ' . $employee->last_name }}
                                                ({{ $employee->employee_id ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="editAttendanceDate">{{ __('dashboard.date') }}</label>
                                    <input type="date" class="form-control" id="editAttendanceDate" name="date"
                                        required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="editClockIn">{{ __('dashboard.clock_in') }}</label>
                                    <input type="time" class="form-control" id="editClockIn" name="clock_in">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="editClockOut">{{ __('dashboard.clock_out') }}</label>
                                    <input type="time" class="form-control" id="editClockOut" name="clock_out">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="editStatus">{{ __('dashboard.status') }}</label>
                                <select id="editStatus" class="form-control" name="status">
                                    <option value="Present">{{ __('dashboard.present') }}</option>
                                    <option value="Absent">{{ __('dashboard.absent') }}</option>
                                    <option value="Leave">{{ __('dashboard.leave') }}</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="editOvertimeHours">{{ __('dashboard.overtime') }}
                                    ({{ __('dashboard.hours') }})</label>
                                <input type="number" class="form-control" id="editOvertimeHours" name="overtime_hours">
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light"
                                data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                            <button type="submit"
                                class="btn btn-primary">{{ __('dashboard.update_attendance') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Attendance Modal -->
        <div class="modal fade" id="deleteAttendanceModal" tabindex="-1" aria-labelledby="deleteAttendanceModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteAttendanceModalLabel">{{ __('dashboard.delete_attendance') }}
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form id="deleteAttendanceForm">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="deleteAttendanceId" name="id">

                        <div class="modal-body text-center">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <p>{{ __('dashboard.confirm_delete_modal') }}</p>
                            <p>
                                <strong>{{ __('dashboard.employee') }}:</strong> <span id="deleteEmployeeName"></span><br>
                                <strong>{{ __('dashboard.date') }}:</strong> <span id="deleteAttendanceDate"></span>
                            </p>
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
            $('#addAttendanceModal form').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let url = form.attr('action');
                let formData = form.serialize();

                // Clear old validation messages and invalid styles
                form.find('.text-danger').remove();
                form.find('.is-invalid').removeClass('is-invalid');

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData,
                    success: function(response) {
                        if (response.status === 'success') {
                            // Reset form and close modal
                            form[0].reset();
                            $('#addAttendanceModal').modal('hide');

                            // Optional: reload or append row
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 || xhr.status === 409) {
                            let errors = xhr.responseJSON.errors;

                            $.each(errors, function(key, messages) {
                                let input = form.find(`[name="${key}"]`);

                                if (input.length > 0) {
                                    input.addClass('is-invalid');
                                    input.after(
                                        `<span class="text-danger">${messages[0]}</span>`
                                    );
                                }
                            });
                        } else {
                            console.error("Unexpected error:", xhr);
                        }
                    }
                });
            });
        });
        //  Edit Attendance Modal - Fill Data
        $(document).on('click', '.edit-attendance-btn', function() {
            let attendance = $(this).data();

            $('#editAttendanceId').val(attendance.id);
            $('#editEmployeeName').val(attendance.employee_id);
            $('#editAttendanceDate').val(attendance.date);
            $('#editClockIn').val(attendance.check_in);
            $('#editClockOut').val(attendance.check_out);
            $('#editStatus').val(attendance.status);
            $('#editOvertimeHours').val(attendance.overtime_hours);
        });

        //  Handle Edit Form Submission
        $(document).on('submit', '#editAttendanceForm', function(e) {
            e.preventDefault();

            let formData = $(this).serialize();

            $.ajax({
                url: "{{ route('dashboard.employee.attendance.update') }}",
                type: 'PUT',
                data: formData,
                success: function(response) {
                    if (response.status === 'success') {
                        $('#editAttendanceModal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        // Refresh table dynamically
                        $('#tableExport').load(location.href + ' #tableExport');
                    }
                },
                error: function(xhr) {
                    $('#editAttendanceForm .text-danger').remove();

                    if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            let input = $('#editAttendanceForm [name="' + key + '"]');
                            if (input.length) {
                                input.after('<span class="text-danger">' + value[0] +
                                    '</span>');
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Something went wrong while updating attendance!'
                        });
                    }
                }
            });
        });
        //  Show Delete Modal with Data
        $(document).on('click', '.delete-attendance-btn', function() {
            let id = $(this).data('id');
            let employee = $(this).data('employee');
            let date = $(this).data('date');

            $('#deleteAttendanceId').val(id);
            $('#deleteEmployeeName').text(employee);
            $('#deleteAttendanceDate').text(date);
        });

        // Handle Delete Form Submission
        $(document).on('submit', '#deleteAttendanceForm', function(e) {
            e.preventDefault();

            let id = $('#deleteAttendanceId').val();

            $.ajax({
                url: `/dashboard/employee/attendance/${id}`, // make sure this route exists
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === 'success') {
                        $('#deleteAttendanceModal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        // Reload table dynamically
                        $('#tableExport').load(location.href + ' #tableExport');
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong while deleting attendance.'
                    });
                }
            });
        });

        $(document).ready(function() {
            $('#filterBtn').on('click', function() {
                let name = $('#name').val(); // new
                let employee_id = $('#employee_id').val();
                let status = $('#status').val();
                let start_date = $('#start_date').val();
                let end_date = $('#end_date').val();

                $.ajax({
                    url: "{{ route('attendance.filter.ajax') }}",
                    type: 'GET',
                    data: {
                        name: name, // new
                        employee_id: employee_id,
                        status: status,
                        start_date: start_date,
                        end_date: end_date
                    },
                    beforeSend: function() {
                        $('#attendanceTableBody').html(
                            '<tr><td colspan="8" class="text-center">Loading...</td></tr>');
                    },
                    success: function(data) {
                        $('#attendanceTableBody').html(data);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });

        });
    </script>
@endsection
