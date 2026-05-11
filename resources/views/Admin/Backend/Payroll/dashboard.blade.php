@extends('layout.master')
@section('title', 'Dashboard | Payroll')
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
                                            <h5 class="font-15">{{ __('dashboard.salary_this_month') }}</h5>
                                            <h2 class="mb-3 font-18">{{ $monthlySalary }} SAR</h2>

                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                        <div class="banner-img">
                                            <img src="{{ asset('img/banner/salary1.avif') }}" alt="Image Not Found">
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
                                            <h5 class="font-15"> {{ __('dashboard.pending_salary') }}</h5>
                                            <h2 class="mb-3 font-18">{{ $pendingSalary }} SAR</h2>

                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                        <div class="banner-img">
                                            <img src="{{ asset('img/banner/pending.jpg') }}" alt="Image Not Found">
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
                                            <h5 class="font-15">{{ __('dashboard.average_salary') }}</h5>
                                            <h2 class="mb-3 font-18">{{ number_format($averageSalary, 2) }} SAR</h2>

                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                        <div class="banner-img">
                                            <img src="{{ asset('img/banner/average.png') }}" alt="Image Not Found">
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
                                            <h5 class="font-15">{{ __('dashboard.payroll') }} %</h5>
                                            <h2 class="mb-3 font-18">{{ number_format($payrollPercent, 1) }}</h2>

                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                        <div class="banner-img">
                                            <img src="{{ asset('img/banner/percent1.png') }}" alt="Image Not Found">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                                        <strong>{{ __('dashboard.employee_id') }}:</strong>
                                        {{ $payroll->employee->employee_id }}
                                    </p>

                                    <p class="mb-1">
                                        <strong>{{ __('dashboard.designation') }}:</strong>
                                        {{ $payroll->employee->designation ?? '-' }}
                                    </p>

                                    <p class="mb-1">
                                        <strong>{{ __('dashboard.email') }}:</strong>
                                        {{ $payroll->employee->email ?? '-' }}
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
                                        (Monthly)
                                    </p>

                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center">
                        </div>
                    @endforelse
                </div>
            </div>
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $payrollCards->links('pagination::bootstrap-5') }}
            </div>



            <div class="card-body" style="background-color: white;">
                <div class="table-responsive">
                    {{-- <h1 class="">Employee Payrolls</h1> --}}
                    <form id="payroll-filter-form" method="GET" action="{{ route('payrolls.filter') }}"
                        class="form-inline mb-3">

                        <input type="month" name="month" class="form-control mr-2" value="{{ request('month') }}" />

                        <input type="text" name="year" placeholder="{{ __('dashboard.year') }}"
                            class="form-control mr-2" value="{{ request('year') }}" />

                        <input type="date" name="start_date" class="form-control mr-2"
                            value="{{ request('start_date') }}" />
                        <input type="date" name="end_date" class="form-control mr-2"
                            value="{{ request('end_date') }}" />

                        <select name="status" class="form-control mr-2">
                            <option value="">All Status</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>
                                {{ __('dashboard.pending') }}</option>
                            <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>
                                {{ __('dashboard.paid') }}</option>
                            <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>
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
                                <th>{{ __('dashboard.basic_salary') }} ({{ __('dashboard.monthly') }})</th>
                                <th>{{ __('dashboard.status') }}</th>
                                <th>{{ __('dashboard.action') }}</th>
                            </tr>
                        </thead>
                        <tbody id="#payroll-table-body">
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
                                            <span class="badge badge-success">{{ __('dashboard.paid') }}</span>
                                        @elseif ($payroll->status == 'Pending')
                                            <span class="badge badge-warning">{{ __('dashboard.pending') }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ __('dashboard.cancelled') }}</span>
                                        @endif
                                    </td>
                                    <td>

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

        </section>

    </div>

    </div>



    <!-- Delete Payroll Modal -->
    <div class="modal fade" id="deletePayrollModal" tabindex="-1" role="dialog"
        aria-labelledby="deletePayrollModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="deletePayrollModalLabel">{{ __('dashboard.delete_payroll_slip') }}</h5>
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
                    $('#tableExport tbody').html(res.html); // Replace table rows
                },
                error: function(err) {
                    console.log(err);
                }
            });
        });
    </script>
@endsection
