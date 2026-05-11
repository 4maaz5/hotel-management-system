@extends('layout.master')
@section('title', 'Dashboard | Leaves')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.all_leaves') }}</h1>
        <div class="leave-grid">
            @forelse($leaveCards as $leave)
                <div class="leave-card">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold">
                                {{ $leave->employee->first_name }} {{ $leave->employee->last_name }}
                            </h6>
                            <p class="mb-1"><strong>{{ __('dashboard.leave_type') }}:</strong>
                                {{ ucfirst($leave->leave_type) }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.start_date') }}:</strong> {{ $leave->start_date }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.end_date') }}:</strong> {{ $leave->end_date }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.reason') }}:</strong> {{ $leave->reason ?? '-' }}
                            </p>
                            <p class="mb-1">
                                <strong>{{ __('dashboard.status') }}:</strong>
                                <span
                                    class="badge
                            @if ($leave->status == 'approved') bg-success
                            @elseif($leave->status == 'rejected') bg-danger
                            @elseif($leave->status == 'in_progress') bg-warning
                            @else bg-secondary @endif">
                                    {{ ucfirst($leave->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            @empty
            @endforelse
        </div>
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $leaveCards->links('pagination::bootstrap-5') }}
        </div>
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.employee_leaves') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addLeaveModal">
                                    {{ __('dashboard.add_new_leave') }}
                                </button>

                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <form id="leaveFilterForm" class="mb-3">
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

                                            <div class="col-md-2">
                                                <label>{{ __('dashboard.leave_type') }}</label>
                                                <select name="leave_type" id="leave_type" class="form-control">
                                                    <option value="">{{ __('dashboard.all_types') }}</option>
                                                    <option value="sick">{{ __('dashboard.sick') }}</option>
                                                    <option value="annual">{{ __('dashboard.annual') }}</option>
                                                    <option value="maternity">{{ __('dashboard.maternity') }}</option>
                                                    <option value="emergency">{{ __('dashboard.emergency') }}</option>
                                                    <option value="unpaid">{{ __('dashboard.unpaid') }}</option>
                                                    <option value="paternity">{{ __('dashboard.peternity') }}</option>
                                                    <option value="compensatory">{{ __('dashboard.compensatory') }}
                                                    </option>
                                                    <option value="bereavement">{{ __('dashboard.bereavement') }}</option>
                                                </select>
                                            </div>

                                            <div class="col-md-2">
                                                <label>{{ __('dashboard.status') }}</label>
                                                <select name="status" id="status" class="form-control">
                                                    <option value="">{{ __('dashboard.all_status') }}</option>
                                                    <option value="pending">{{ __('dashboard.pending') }}</option>
                                                    <option value="approved">{{ __('dashboard.approved') }}</option>
                                                    <option value="rejected">{{ __('dashboard.rejected') }}</option>
                                                    <option value="in_progress">{{ __('dashboard.in_progress') }}</option>
                                                </select>
                                            </div>

                                            <div class="col-md-2">
                                                <label>{{ __('dashboard.start_date') }}</label>
                                                <input type="date" name="start_date" id="start_date"
                                                    class="form-control">
                                            </div>

                                            <div class="col-md-2">
                                                <label>{{ __('dashboard.end_date') }}</label>
                                                <input type="date" name="end_date" id="end_date" class="form-control">
                                            </div>

                                            <div class="col-md-1 d-flex align-items-end">
                                                <button type="button" id="filterBtn"
                                                    class="btn btn-primary w-100">{{ __('dashboard.filter') }}</button>
                                            </div>
                                        </div>
                                    </form>

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead>
                                            <tr>
                                            <tr>
                                                <th>{{ __('dashboard.employee') }}</th>
                                                <th>{{ __('dashboard.leave_type') }}</th>
                                                <th>{{ __('dashboard.start_date') }}</th>
                                                <th>{{ __('dashboard.end_date') }}</th>
                                                <th>{{ __('dashboard.reason') }}</th>
                                                <th>{{ __('dashboard.status') }}</th>
                                                <th>{{ __('dashboard.view') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>

                                            </tr>
                                        </thead>
                                        <tbody id="leaveTable">
                                            @include('Admin.Backend.partials.leaves')
                                        </tbody>
                                    </table>
                                    <div id="leavePagination" class="d-flex justify-content-center mt-3">
                                        {{ $leaves->links('pagination::bootstrap-5') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Add Leave Modal -->
        <div class="modal fade" id="addLeaveModal" tabindex="-1" role="dialog" aria-labelledby="addLeaveModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="addLeaveModalLabel">{{ __('dashboard.add_new_leave') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form id="leaveForm" action="{{ route('dashboard.leaves.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.employee_name') }}</label>
                                    <select name="employee_id" class="form-control mr-2">
                                        <option value="">{{ __('dashboard.all_employees') }}</option>
                                        @foreach ($employees ?? [] as $emp)
                                            <option value="{{ $emp->id }}"
                                                {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                                {{ $emp->first_name }} {{ $emp->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.leave_type') }}</label>
                                    <select name="leave_type" class="form-control">
                                        <option selected disabled>{{ __('dashboard.sick_leave_type') }}</option>
                                        <option value="sick">{{ __('dashboard.sick_leave') }}</option>
                                        <option value="annual">{{ __('dashboard.leave_type') }}</option>
                                        <option value="maternity">{{ __('dashboard.maternity_leave') }}</option>
                                        <option value="emergency">{{ __('dashboard.emergency_leave') }}</option>
                                        <option value="unpaid">{{ __('dashboard.unpaid_leave') }}</option>
                                        <option value="paternity">{{ __('dashboard.paternity_leave') }}</option>
                                        <option value="compensatory">{{ __('dashboard.compensatory') }}</option>
                                        <option value="bereavement">{{ __('dashboard.bereavement') }}</option>
                                    </select>
                                </div>


                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.start_date') }}</label>
                                    <input type="date" name="start_date" class="form-control">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.end_date') }}</label>
                                    <input type="date" name="end_date" class="form-control">

                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>{{ __('dashboard.status') }}</label>
                                    <select class="form-control" name="status" id="editLeaveStatus">
                                        <option value="pending">{{ __('dashboard.pending') }}</option>
                                        <option value="approved">{{ __('dashboard.approved') }}</option>
                                        <option value="rejected">{{ __('dashboard.rejected') }}</option>
                                        <option value="in_progress">{{ __('dashboard.in_progress') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>{{ __('dashboard.reason') }}</label>
                                <textarea name="reason" class="form-control" rows="3"></textarea>
                            </div>

                            <div id="form-errors" class="text-danger small"></div>
                        </div>
                        <div class="form-row m-3">
                            <div class="form-group col-md-4">
                                <label>{{ __('dashboard.paid_type') }}</label>
                                <select name="payment_type" class="form-control">
                                    <option value="paid">{{ __('dashboard.paid') }}</option>
                                    <option value="unpaid">{{ __('dashboard.unpaid') }}</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label>{{ __('dashboard.travel_responsibility') }}</label>
                                <select name="travel_responsibility" class="form-control">
                                    <option value="">N/A</option>
                                    <option value="company">{{ __('dashboard.company_sponsored') }}</option>
                                    <option value="employee">{{ __('dashboard.employee_sponsored') }}</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label>{{ __('dashboard.total_days') }}</label>
                                <input type="number" name="total_days" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="form-row m-3">
                            <div class="form-group col-md-12" id="ticketAmountDiv" style="display:none;">
                                <label>{{ __('dashboard.ticket_amount') }}</label>
                                <input type="number" name="ticket_amount" class="form-control" step="0.01"
                                    min="0">
                            </div>
                        </div>

                        <div class="form-group m-3">
                            <label>{{ __('dashboard.travel_documents') }}</label>
                            <input type="file" name="documents[]" class="form-control" multiple>
                        </div>




                        <div class="modal-footer">
                            <button type="button" class="btn btn-light"
                                data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('dashboard.submit_leave') }}</button>
                        </div>
                    </form>


                </div>
            </div>
        </div>

        <!-- Edit Leave Modal -->
        <div class="modal fade" id="editLeaveModal" tabindex="-1" role="dialog" aria-labelledby="editLeaveModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="editLeaveModalLabel">{{ __('dashboard.edit_leave') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form id="editLeaveForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="editLeaveId" name="leave_id">

                        <div class="form-row m-3">
                            <div class="form-group col-md-6">
                                <label>{{ __('dashboard.employee_name') }}</label>
                                <select name="employee_id" class="form-control mr-2">
                                    <option value="">{{ __('dashboard.all_employees') }}</option>
                                    @foreach ($employees ?? [] as $emp)
                                        <option value="{{ $emp->id }}"
                                            {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->first_name }} {{ $emp->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>{{ __('dashboard.leave_type') }}</label>
                                <select class="form-control" name="leave_type" id="editLeaveType">
                                    <option value="sick">{{ __('dashboard.sick_leave') }}</option>
                                    <option value="annual">{{ __('dashboard.leave_type') }}</option>
                                    <option value="maternity">{{ __('dashboard.maternity_leave') }}</option>
                                    <option value="emergency">{{ __('dashboard.emergency_leave') }}</option>
                                    <option value="unpaid">{{ __('dashboard.unpaid_leave') }}</option>
                                    <option value="paternity">{{ __('dashboard.paternity_leave') }}</option>
                                    <option value="compensatory">{{ __('dashboard.compensatory') }}</option>
                                    <option value="bereavement">{{ __('dashboard.bereavement') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row m-3">
                            <div class="form-group col-md-6">
                                <label>{{ __('dashboard.start_date') }}</label>
                                <input type="date" class="form-control" name="start_date" id="editLeaveStartDate">
                            </div>
                            <div class="form-group col-md-6">
                                <label>{{ __('dashboard.end_date') }}</label>
                                <input type="date" class="form-control" name="end_date" id="editLeaveEndDate">
                            </div>
                        </div>
                        <div class="form-row m-3">
                            <div class="form-group col-md-12">
                                <label>{{ __('dashboard.status') }}</label>
                                <select class="form-control" name="status" id="editLeaveStatus">
                                    <option value="pending">{{ __('dashboard.pending') }}</option>
                                    <option value="approved">{{ __('dashboard.approved') }}</option>
                                    <option value="rejected">{{ __('dashboard.rejected') }}</option>
                                    <option value="in_progress">{{ __('dashboard.in_progress') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row m-3">
                            <div class="form-group col-md-4">
                                <label>{{ __('dashboard.paid_type') }}</label>
                                <select name="payment_type" class="form-control">
                                    <option value="paid">{{ __('dashboard.paid') }}</option>
                                    <option value="unpaid">{{ __('dashboard.unpaid') }}</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label>{{ __('dashboard.travel_responsibility') }}</label>
                                <select name="travel_responsibility" class="form-control" disabled>
                                    <option value="">N/A</option>
                                    <option value="company">{{ __('dashboard.company_sponsored') }}</option>
                                    <option value="employee">{{ __('dashboard.employee_sponsored') }}</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label>{{ __('dashboard.total_days') }}</label>
                                <input type="number" name="total_days" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="form-row m-3">
                            <div class="form-group col-md-12" id="ticketAmountDiv" style="display:none;">
                                <label>{{ __('dashboard.ticket_amount') }}</label>
                                <input type="number" name="ticket_amount" class="form-control" step="0.01"
                                    min="0">
                            </div>
                        </div>
                        <div class="form-group m-3">
                            <label>{{ __('dashboard.reason') }}</label>
                            <textarea class="form-control" rows="3" name="reason" id="editLeaveReason"></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light"
                                data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('dashboard.update_leave') }}</button>
                        </div>
                    </form>


                </div>
            </div>
        </div>

        <!-- Delete Leave Modal -->
        <div class="modal fade" id="deleteLeaveModal" tabindex="-1" aria-labelledby="deleteLeaveModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteLeaveModalLabel">{{ __('dashboard.delete_leave') }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form id="deleteLeaveForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="deleteLeaveId" name="id">

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
    <script>
        $(document).ready(function() {
            $('#leaveForm').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let formData = new FormData(this); // REQUIRED FOR FILES

                $('#form-errors').html('');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false, // ❗ IMPORTANT
                    contentType: false, // ❗ IMPORTANT
                    success: function(response) {
                        if (response.status === 'success') {
                            form[0].reset();
                            $('#addLeaveModal').modal('hide');

                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorHtml = '<ul>';

                            $.each(errors, function(key, messages) {
                                errorHtml += `<li>${messages[0]}</li>`;
                            });

                            errorHtml += '</ul>';
                            $('#form-errors').html(errorHtml);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong!'
                            });
                        }
                    }
                });
            });
        });

        $(document).on('click', '.edit-leave-btn', function() {
            let leave = $(this).data();

            $('#editLeaveId').val(leave.id);
            $('#editLeaveType').val(leave.leave_type);
            $('#editLeaveStartDate').val(leave.start_date);
            $('#editLeaveEndDate').val(leave.end_date);
            $('#editLeaveReason').val(leave.reason);
            $('#editLeaveStatus').val(leave.status);
            $('select[name="employee_id"]').val(leave.employee_id);
            $('select[name="payment_type"]').val(leave.payment_type);
            $('select[name="travel_responsibility"]').val(leave.travel_responsibility);
            $('input[name="total_days"]').val(leave.total_days);
            $('input[name="ticket_amount"]').val(leave.ticket_amount);

            // Show/hide ticket amount input
            if (leave.travel_responsibility === 'company') {
                $('#ticketAmountDiv').show();
            } else {
                $('#ticketAmountDiv').hide();
            }

            @if (auth()->user()->hasRole('employee'))
                $('#editLeaveStatus').prop('disabled', true);
            @else
                $('#editLeaveStatus').prop('disabled', false);
            @endif
        });

        // total days in edit modal
        $(document).on('change', '#editLeaveStartDate, #editLeaveEndDate', function() {
            let start = $('#editLeaveStartDate').val();
            let end = $('#editLeaveEndDate').val();

            if (!start || !end) {
                $('input[name="total_days"]').val('');
                return;
            }

            let startDate = new Date(start);
            let endDate = new Date(end);

            if (endDate < startDate) {
                $('input[name="total_days"]').val('');
                return;
            }

            let diffTime = endDate - startDate;
            let diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;

            $('input[name="total_days"]').val(diffDays);
        });

        $(document).on('submit', '#editLeaveForm', function(e) {
            e.preventDefault();

            let leaveId = $('#editLeaveId').val();
            let formData = $(this).serialize();

            $.ajax({
                url: `/dashboard/leaves/update`, // same route as your Laravel route
                type: 'PUT',
                data: formData,
                success: function(response) {
                    if (response.status === 'success') {
                        $('#editLeaveModal').modal('hide');

                        let leaveId = $('#editLeaveId').val();
                        let row = $('#leaveTable').find(`tr[data-id="${leaveId}"]`);

                        // Update text values
                        row.find('.leave-type').text($('#editLeaveType').val());
                        row.find('.leave-start').text($('#editLeaveStartDate').val());
                        row.find('.leave-end').text($('#editLeaveEndDate').val());
                        row.find('.leave-reason').text($('#editLeaveReason').val());

                        // Update status badge dynamically
                        let status = $('#editLeaveStatus').val();
                        let badgeClass =
                            status === 'approved' ? 'bg-success' :
                            status === 'rejected' ? 'bg-danger' :
                            status === 'in_progress' ? 'bg-warning' : 'bg-secondary';

                        row.find('.leave-status span')
                            .attr('class', `badge ${badgeClass}`)
                            .text(status.charAt(0).toUpperCase() + status.slice(1));

                        //  SweetAlert success popup
                        Swal.fire({
                            icon: 'success',
                            title: 'Leave Updated',
                            text: 'The leave record has been updated successfully.',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            location.reload();
                        });
                    }
                },


                error: function(xhr) {
                    // Remove previous inline errors
                    $('#editLeaveForm .text-danger').remove();

                    if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            let input = $('#editLeaveForm [name="' + key + '"]');
                            if (input.length) {
                                input.after('<span class="text-danger">' + value[0] +
                                    '</span>');
                            }
                        });
                    } else {
                        alert('Something went wrong!');
                    }
                }
            });
        });
        //  When Delete button is clicked
        $(document).on('click', '.delete-leave-btn', function() {
            let leaveId = $(this).data('id');
            $('#deleteLeaveId').val(leaveId);
        });

        //  Handle delete form submission
        $(document).on('submit', '#deleteLeaveForm', function(e) {
            e.preventDefault();

            let leaveId = $('#deleteLeaveId').val();

            $.ajax({
                url: `/dashboard/leaves/${leaveId}/delete`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        $('#deleteLeaveModal').modal('hide');
                        // Remove the row dynamically from table
                        $(`a[data-id="${leaveId}"]`).closest('tr').fadeOut(400, function() {
                            $(this).remove();
                        });

                        // ✅ Show success alert
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong while deleting!'
                    });
                }
            });
        });

        $(document).ready(function() {
            $('#filterBtn').on('click', function() {
                fetchLeaves(1);
            });

            $(document).on('click', '#leavePagination a', function(e) {
                e.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                if (page) fetchLeaves(page);
            });

            function fetchLeaves(page) {
                let employee_id = $('#employee_id').val();
                let leave_type = $('#leave_type').val();
                let status = $('#status').val();
                let start_date = $('#start_date').val();
                let end_date = $('#end_date').val();

                $.ajax({
                    url: "{{ route('leaves.filter.ajax') }}",
                    type: 'GET',
                    data: {
                        employee_id: employee_id,
                        leave_type: leave_type,
                        status: status,
                        start_date: start_date,
                        end_date: end_date,
                        page: page
                    },
                    beforeSend: function() {
                        $('#leaveTable').html(
                            '<tr><td colspan="7" class="text-center">Loading...</td></tr>');
                    },
                    success: function(data) {
                        $('#leaveTable').html(data.html);
                        $('#leavePagination').html(data.pagination);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            }
        });

        $(document).on(
            'change',
            '#addLeaveModal input[name="start_date"], #addLeaveModal input[name="end_date"]',
            function() {

                let $form = $(this).closest('form');

                let start = $form.find('input[name="start_date"]').val();
                let end = $form.find('input[name="end_date"]').val();

                if (!start || !end) {
                    $form.find('input[name="total_days"]').val('');
                    return;
                }

                let startDate = new Date(start);
                let endDate = new Date(end);

                if (endDate < startDate) {
                    $form.find('input[name="total_days"]').val('');
                    return;
                }

                let diffTime = endDate - startDate;
                let diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;

                $form.find('input[name="total_days"]').val(diffDays);
            }
        );

        $(document).ready(function() {
            $('select[name="travel_responsibility"]').on('change', function() {
                if ($(this).val() === 'company') {
                    $('#ticketAmountDiv').show();
                } else {
                    $('#ticketAmountDiv').hide();
                    $('input[name="ticket_amount"]').val('');
                }
            });
        });
        $(document).on('click', '.view-pdf', function(e) {
            e.preventDefault();
            let file = $(this).data('file');
            $('#pdfFrame').attr('src', file);
            $('#pdfModal').modal('show');
        });
    </script>

@endsection
