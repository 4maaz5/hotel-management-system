@extends('layout.master')
@section('title', 'Dashboard | Payroll')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.employee_payrolls') }}</h1>
        <div class="payroll-grid">
            @forelse($payrollCards as $payroll)
                <div class="payroll-card">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold">
                                {{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}
                            </h6>

                            <p class="mb-1">
                                <strong>{{ __('dashboard.employee_id') }}:</strong> {{ $payroll->employee->employee_id }}
                            </p>

                            <p class="mb-1">
                                <strong>{{ __('dashboard.designation') }}:</strong>
                                {{ $payroll->employee->designation ?? '-' }}
                            </p>

                            <p class="mb-1">
                                <strong>{{ __('dashboard.email') }}:</strong> {{ $payroll->employee->email ?? '-' }}
                            </p>

                            <p class="mb-1">
                                <strong>{{ __('dashboard.join_date') }}:</strong>
                                {{ $payroll->employee->join_date ?? '-' }}
                            </p>

                            <p class="mb-1">
                                <strong>{{ __('dashboard.month') }}:</strong>
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $payroll->month)->format('F Y') }}
                            </p>

                            <p class="mb-1">
                                <strong>{{ __('dashboard.basic_salary') }}:</strong> {{ $payroll->basic_salary }}
                                ({{ __('dashboard.monthly') }})
                            </p>


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
            {{ $payrollCards->links('pagination::bootstrap-5') }}
        </div>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.payroll_list') }}</h4>
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addPayrollModal">
                                    {{ __('dashboard.add_payroll') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <form id="payroll-filter-form" method="GET" action="{{ route('payrolls.filter') }}"
                                        class="form-inline mb-3">

                                        <input type="month" name="month" class="form-control mr-2"
                                            value="{{ request('month') }}" />

                                        <input type="text" name="year" placeholder="{{ __('dashboard.year') }}"
                                            class="form-control mr-2" value="{{ request('year') }}" />

                                        <input type="date" name="start_date" class="form-control mr-2"
                                            value="{{ request('start_date') }}" />
                                        <input type="date" name="end_date" class="form-control mr-2"
                                            value="{{ request('end_date') }}" />

                                        <select name="status" class="form-control mr-2">
                                            <option value="">{{ __('dashboard.all_status') }}</option>
                                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>
                                                {{ __('dashboard.pending') }}</option>
                                            <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>
                                                {{ __('dashboard.paid') }}
                                            </option>
                                            <option value="Cancelled"
                                                {{ request('status') == 'Cancelled' ? 'selected' : '' }}>
                                                {{ __('dashboard.cancelled') }}
                                            </option>
                                        </select>

                                        <!-- Employee and Branch -->
                                        <select name="employee_id" class="form-control mr-2">
                                            <option value="">{{ __('dashboard.all_employees') }}</option>
                                            @foreach ($employees ?? [] as $emp)
                                                <option value="{{ $emp->id }}"
                                                    {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                                    {{ $emp->first_name }} {{ $emp->last_name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @can('viewAny', App\Models\Branch::class) {{-- or check if user is super_admin --}}
                                            <select name="branch_id" class="form-control mr-2">
                                                <option value="">{{ __('dashboard.all_branches') }}</option>
                                                @foreach ($branches ?? [] as $b)
                                                    <option value="{{ $b->id }}"
                                                        {{ request('branch_id') == $b->id ? 'selected' : '' }}>
                                                        {{ $b->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @endcan

                                        <button class="btn btn-primary">{{ __('dashboard.filter') }}</button>
                                    </form>

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead>
                                            <tr>

                                                <th>{{ __('dashboard.employee_id') }}</th>
                                                <th>{{ __('dashboard.employee_name') }}</th>
                                                <th>{{ __('dashboard.designation') }}</th>
                                                <th>{{ __('dashboard.email') }}</th>
                                                <th>{{ __('dashboard.join_date') }}</th>
                                                <th>{{ __('dashboard.month') }}</th>
                                                <th>{{ __('dashboard.basic_salary') }} ({{ __('dashboard.monthly') }})
                                                </th>
                                                <th>{{ __('dashboard.status') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($payrolls as $payroll)
                                                <tr id="payroll-row-{{ $payroll->id }}">
                                                    <td>{{ $payroll->employee->employee_id }}</td>
                                                    <td>{{ $payroll->employee->first_name }}
                                                        {{ $payroll->employee->last_name }}</td>
                                                    <td>{{ $payroll->employee->designation }}</td>
                                                    <td>{{ $payroll->employee->email }}</td>
                                                    <td>{{ $payroll->employee->join_date }}</td>

                                                    <td class="payroll-month">
                                                        {{ \Carbon\Carbon::createFromFormat('Y-m', $payroll->month)->format('F Y') }}
                                                    </td>
                                                    <td class="payroll-basic-salary">{{ $payroll->basic_salary }}</td>
                                                    <td class="payroll-status">
                                                        @if ($payroll->status == 'Paid')
                                                            <span
                                                                class="badge badge-success">{{ __('dashboard.paid') }}</span>
                                                        @elseif ($payroll->status == 'Pending')
                                                            <span
                                                                class="badge badge-warning">{{ __('dashboard.pending') }}</span>
                                                        @else
                                                            <span
                                                                class="badge badge-danger">{{ __('dashboard.cancelled') }}</span>
                                                        @endif
                                                    </td>



                                                    <td>
                                                        <a href="#" class="text-secondary edit-payroll-btn"
                                                            data-id="{{ $payroll->id }}"
                                                            data-name="{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}"
                                                            data-month="{{ $payroll->month }}"
                                                            data-basic_salary="{{ $payroll->basic_salary }}"
                                                            data-allowance="{{ $payroll->allowance }}"
                                                            data-net_pay="{{ $payroll->net_pay }}" data-toggle="modal"
                                                            data-status="{{ $payroll->status }}"
                                                            data-target="#editPayrollModal">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="#" class="text-danger delete-payroll-btn"
                                                            data-id="{{ $payroll->id }}" data-toggle="modal"
                                                            data-target="#deletePayrollModal">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Add Payroll Modal -->
        <div class="modal fade" id="addPayrollModal" tabindex="-1" role="dialog" aria-labelledby="addPayrollModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPayrollModalLabel">{{ __('dashboard.add_payroll_slip') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form id="addPayrollForm">
                            @csrf
                            <!-- Employee ID -->
                            <div class="form-group mb-3">
                                <label for="employee_id">{{ __('dashboard.select_employee') }}</label>
                                <select name="employee_id" id="employee_id" class="form-control" required>
                                    <option value="" selected disabled>-- {{ __('dashboard.select_employee') }} --
                                    </option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">
                                            {{ $employee->first_name }} -
                                            {{ $employee->employee_id ?? 'EMP-' . str_pad($employee->id, 3, '0', STR_PAD_LEFT) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Month -->
                            <div class="form-group mb-3">
                                <label>{{ __('dashboard.month') }}</label>
                                <input type="month" class="form-control" id="month" name="month" required>
                            </div>

                            <!-- Salary -->
                            <div class="form-group mb-3">
                                <label>{{ __('dashboard.basic_salary') }}</label>
                                <input type="number" class="form-control" name="basic_salary" id="basic_salary"
                                    placeholder="{{ __('dashboard.basic_salary') }}" readonly>
                            </div>

                            <!-- Commission -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.commission') }}</label>
                                    <input type="number" class="form-control" name="commission" id="commission"
                                        placeholder="{{ __('dashboard.commission') }}" readonly>
                                </div>

                                <!-- Net Pay -->
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.net_pay') }}</label>
                                    <input type="number" class="form-control" name="net_pay" id="net_pay"
                                        placeholder="{{ __('dashboard.net_pay') }}" readonly>
                                </div>
                            </div>

                            <!-- Allowances -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.allowances') }}</label>
                                    <!-- Allowances -->
                                    <input type="number" class="form-control" name="allowance" id="allowance"
                                        placeholder="{{ __('dashboard.allowances') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ __('dashboard.deductions') }}</label>
                                    <!-- Deductions -->
                                    <input type="number" class="form-control" name="deductions" id="deductions"
                                        placeholder="{{ __('dashboard.deductions') }}" value="0">
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label>{{ __('dashboard.total') }}</label>
                                <!-- Total -->
                                <input type="number" class="form-control" name="total_amount" id="total_amount"
                                    placeholder="{{ __('dashboard.total_salary') }}" readonly>
                            </div>
                            <div class="form-group mb-3">
                                <label>{{ __('dashboard.status') }}</label>
                                <select name="status" class="form-control" required>
                                    <option value="Pending" selected>{{ __('dashboard.pending') }}</option>
                                    <option value="Paid">{{ __('dashboard.paid') }}</option>
                                    <option value="Cancelled">{{ __('dashboard.cancelled') }}</option>
                                </select>
                            </div>


                            <div class="text-right mt-3">
                                <button type="submit" class="btn btn-primary">{{ __('dashboard.save_slip') }}</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>


        <!-- Edit Payroll Modal -->
        <div class="modal fade" id="editPayrollModal" tabindex="-1" role="dialog"
            aria-labelledby="editPayrollModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="editPayrollModalLabel">{{ __('dashboard.edit_payroll_slip') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form id="editPayrollForm">
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="editPayrollId" name="payroll_id">

                            <!-- Employee Name (readonly) -->
                            <div class="form-group mb-3">
                                <label>{{ __('dashboard.employee') }}</label>
                                <input type="text" class="form-control" id="editEmployeeName" readonly>
                            </div>

                            <!-- Month -->
                            <div class="form-group mb-3">
                                <label>{{ __('dashboard.month') }}</label>
                                <input type="month" class="form-control" name="month" id="editMonth" readonly>
                            </div>

                            <!-- Basic Salary -->
                            <div class="form-group mb-3">
                                <label>{{ __('dashboard.basic_salary') }}</label>
                                <input type="number" class="form-control" name="basic_salary" id="editBasicSalary"
                                    readonly>
                            </div>

                            <!-- Allowance -->
                            <div class="form-group mb-3">
                                <label>{{ __('dashboard.allowances') }}</label>
                                <input type="number" class="form-control" name="allowance" id="editAllowance" readonly>
                            </div>

                            <!-- Net Pay -->
                            <div class="form-group mb-3">
                                <label>{{ __('dashboard.net_pay') }}</label>
                                <input type="number" class="form-control" name="net_pay" id="editNetPay" readonly>
                            </div>
                            <div class="form-group mb-3">
                                <label>{{ __('dashboard.status') }}</label>
                                <select name="status" id="editStatus" class="form-control">
                                    <option value="Pending">{{ __('dashboard.pending') }}</option>
                                    <option value="Paid">{{ __('dashboard.paid') }}</option>
                                    <option value="Cancelled">{{ __('dashboard.cancelled') }}</option>
                                </select>
                            </div>


                            <div class="text-right mt-3">
                                <button type="submit"
                                    class="btn btn-primary">{{ __('dashboard.update_payroll') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Payroll Modal -->
        <div class="modal fade" id="deletePayrollModal" tabindex="-1" role="dialog"
            aria-labelledby="deletePayrollModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="deletePayrollModalLabel">{{ __('dashboard.delete_payroll_slip') }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body text-center">
                        <p>{{ __('dashboard.confirm_delete_modal') }}</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                        <button type="button" class="btn btn-danger">{{ __('dashboard.delete') }}</button>
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

            /* ------------------- ADD PAYROLL ------------------- */
            $('#addPayrollForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('dashboard.payroll.payslip.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Payroll Created!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            // hide modal + reset
                            $('#addPayrollModal').modal('hide');
                            $('#addPayrollForm')[0].reset();

                            // dynamically append new row
                            let payroll = response.data;
                            let newRow = `
                        <tr id="payroll-row-${payroll.id}">
                            <td class="payroll-employee-id">${payroll.employee.employee_id}</td>
                            <td class="payroll-employee-name">${payroll.employee.first_name} ${payroll.employee.last_name}</td>
                            <td class="payroll-designation">${payroll.employee.designation}</td>
                            <td class="payroll-email">${payroll.employee.email}</td>
                            <td class="payroll-join-date">${payroll.employee.join_date}</td>
                            <td class="payroll-month">${moment(payroll.month, "YYYY-MM").format("MMMM YYYY")}</td>
                            <td class="payroll-basic-salary">${payroll.basic_salary}</td>
                            <td class="payroll-status">${
    payroll.status === 'Paid'
        ? '<span class="badge badge-success">Paid</span>'
        : payroll.status === 'Pending'
        ? '<span class="badge badge-warning">Pending</span>'
        : '<span class="badge badge-danger">Cancelled</span>'
}</td>

                            <td>
                                <a href="#" class="text-secondary edit-payroll-btn"
                                    data-id="${payroll.id}"
                                    data-name="${payroll.employee.first_name} ${payroll.employee.last_name}"
                                    data-month="${payroll.month}"
                                    data-basic_salary="${payroll.basic_salary}"
                                    data-allowance="${payroll.allowance}"
                                    data-net_pay="${payroll.net_pay}"
                                    data-status="${payroll.status}"
                                    data-toggle="modal" data-target="#editPayrollModal">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" class="text-danger delete-payroll-btn"
                                    data-id="${payroll.id}" data-toggle="modal"
                                    data-target="#deletePayrollModal">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                            $('table tbody').prepend(newRow);
                        }
                    },
                    error: function(xhr) {
                        let message = 'Something went wrong!';

                        // Laravel validation or custom 422 error
                        if (xhr.status === 422) {
                            if (xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors) {
                                // If it's default Laravel validation errors
                                message = Object.values(xhr.responseJSON.errors)[0][0];
                            }
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message
                        });
                    }

                });
            });


            /* ------------------- EDIT MODAL POPULATE ------------------- */
            $(document).on('click', '.edit-payroll-btn', function() {
                let payroll = $(this).data();
                $('#editPayrollId').val(payroll.id);
                $('#editEmployeeName').val(payroll.name);
                $('#editMonth').val(payroll.month);
                $('#editBasicSalary').val(payroll.basic_salary);
                $('#editAllowance').val(payroll.allowance);
                $('#editNetPay').val(payroll.net_pay);
                let status = $(this).data('status');
                $('#editPayrollModal select[name="status"]').val(status);

            });


            /* ------------------- UPDATE PAYROLL ------------------- */
            $(document).on('submit', '#editPayrollForm', function(e) {
                e.preventDefault();

                let payrollId = $('#editPayrollId').val();

                $.ajax({
                    url: `/dashboard/payroll/payslip/update/${payrollId}`,
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Payroll Updated!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Hide modal
                            $('#editPayrollModal').modal('hide');

                            // Get new field values
                            let newMonth = $('#editMonth').val();
                            let newBasic = $('#editBasicSalary').val();
                            let newAllowance = $('#editAllowance').val();
                            let newNetPay = $('#editNetPay').val();


                            // Update the table row (formatted month name)
                            let formattedMonth = moment(newMonth, 'YYYY-MM').format(
                                'MMMM YYYY');
                            let row = $(`#payroll-row-${payrollId}`);

                            row.find('.payroll-month').text(formattedMonth);
                            row.find('.payroll-basic-salary').text(newBasic);


                            // Update data attributes of the edit button
                            let editBtn = row.find('.edit-payroll-btn');
                            editBtn.data('month', newMonth);
                            editBtn.data('basic_salary', newBasic);
                            editBtn.data('allowance', newAllowance);
                            editBtn.data('net_pay', newNetPay);

                            let newStatus = $('#editStatus').val();
                            let badgeClass =
                                newStatus === 'Paid' ?
                                'badge badge-success' :
                                newStatus === 'Pending' ?
                                'badge badge-warning' :
                                'badge badge-danger';

                            row.find('.payroll-status').html(
                                `<span class="${badgeClass}">${newStatus}</span>`);

                            editBtn.data('status', newStatus);

                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let messages = Object.values(errors).flat().join('\n');
                            Swal.fire({
                                icon: 'warning',
                                title: 'Validation Error',
                                text: messages,
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong. Please try again.',
                            });
                        }
                    }
                });
            });



            /* ------------------- DELETE PAYROLL ------------------- */
            let deleteId = null;
            $(document).on('click', '.delete-payroll-btn', function() {
                deleteId = $(this).data('id');
            });

            $('#deletePayrollModal .btn-danger').on('click', function() {
                if (!deleteId) return;

                $.ajax({
                    url: `/dashboard/payroll/payslip/delete/${deleteId}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            $(`#payroll-row-${deleteId}`).remove();
                            $('#deletePayrollModal').modal('hide');
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Delete failed!'
                        });
                    }
                });
            });

        });
        $('#payroll-filter-form').submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: $(this).attr('action'),
                type: 'GET',
                data: $(this).serialize(),
                success: function(res) {
                    $('#tableExport tbody').html(res.html);
                },
                error: function(err) {
                }
            });
        });


        function fetchPayrollData() {
            var employeeId = $('#employee_id').val();
            var month = $('#month').val();

            if (employeeId && month) {
                $.ajax({
                    url: '/dashboard/payroll/get-employee-data/' + employeeId + '/' + month,
                    type: 'GET',
                    success: function(data) {
                        $('#basic_salary').val(data.basic_salary);
                        $('#allowance').val(data.allowance);
                        $('#commission').val(data.commission);
                        $('#net_pay').val(data.net_pay);
                        calculateTotal();
                    }
                });
            }
        }

        // Trigger when employee or month changes
        $('#employee_id, #month').on('change', fetchPayrollData);

        function calculateTotal() {
            let basicSalary = parseFloat($('#basic_salary').val()) || 0;
            let allowance = parseFloat($('#allowance').val()) || 0;
            let deductions = parseFloat($('#deductions').val()) || 0;
            let commission = parseFloat($('#commission').val()) || 0;

            let total = basicSalary + allowance + commission - deductions;

            $('#total_amount').val(total.toFixed(2)); // Update total field
        }

        // Trigger calculation whenever allowance or deductions change
        $('#allowance, #deductions').on('input', calculateTotal);

        // Also trigger when modal loads (optional) or commission changes
        $('#commission').on('input', calculateTotal);
    </script>
@endsection
