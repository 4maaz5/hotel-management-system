@extends('layout.master')
@section('title', 'Dashboard | Salary')
@section('main')
    <!-- Main Content -->
    <div class="main-content">

        <h1 class="text-center">{{ __('dashboard.salary_history') }}</h1>

        <div class="salary-grid">
            @forelse($salaryHistoryCards as $index => $payroll)
                <div class="salary-card">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold">
                                {{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}
                            </h6>

                            <p class="mb-1"><strong>{{ __('dashboard.designation') }}:</strong>
                                {{ $payroll->employee->designation ?? '-' }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.month') }}:</strong>
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $payroll->month)->format('F Y') }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.basic_salary') }}:</strong>
                                {{ $payroll->basic_salary }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.allowance') }}:</strong> {{ $payroll->allowance }}
                            </p>
                            <p class="mb-1"><strong>{{ __('dashboard.net_pay') }}:</strong> {{ $payroll->net_pay }}</p>
                            <p class="mb-1"><strong>{{ __('dashboard.generated_on') }}:</strong>
                                {{ $payroll->created_at->format('d M Y') }}
                            </p>

                            {{-- Optional download button --}}
                            <a href="{{ route('dashboard.payroll.payslip.download', $payroll->id) }}"
                                class="btn btn-sm btn-info mt-2">
                                <i class="fas fa-download"></i> {{ __('dashboard.slip') }}
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted">
                </div>
            @endforelse
        </div>
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $salaryHistoryCards->links('pagination::bootstrap-5') }}
        </div>


        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.salary_history') }}</h4>
                                {{-- <a href="{{ route('dashboard.employee.create') }}">
                                    <button class="btn btn-primary">Add Employee</button>
                                </a> --}}
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <form id="payroll-filter-form" method="GET"
                                        action="{{ route('payrolls.salary.filter') }}" class="form-inline mb-3">

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
                                        {{-- <select name="employee_id" class="form-control mr-2">
                                            <option value="">{{ __('dashboard.all_employees') }}</option>
                                            @foreach ($employees ?? [] as $emp)
                                                <option value="{{ $emp->id }}"
                                                    {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                                    {{ $emp->first_name }} {{ $emp->last_name }}
                                                </option>
                                            @endforeach
                                        </select> --}}

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
                                                <th>#</th>
                                                <th>{{ __('dashboard.employee') }}</th>
                                                <th>{{ __('dashboard.designation') }}</th>
                                                <th>{{ __('dashboard.month') }}</th>
                                                <th>{{ __('dashboard.basic_salary') }}</th>
                                                <th>{{ __('dashboard.allowances') }}</th>
                                                <th>{{ __('dashboard.net_pay') }}</th>
                                                <th>{{ __('dashboard.generated_on') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($salaryHistory as $index => $payroll)
                                                <tr id="payroll-row-{{ $payroll->id }}">
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $payroll->employee->first_name }}
                                                        {{ $payroll->employee->last_name }}</td>
                                                    <td>{{ $payroll->employee->designation }}</td>
                                                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $payroll->month)->format('F Y') }}
                                                    </td>
                                                    <td>{{ $payroll->basic_salary }}</td>
                                                    <td>{{ $payroll->allowance }}</td>
                                                    <td>{{ $payroll->net_pay }}</td>
                                                    <td>{{ $payroll->created_at->format('d M Y') }}</td>
                                                    <td>
                                                        <a href="{{ route('dashboard.payroll.payslip.download', $payroll->id) }}"
                                                            class="btn btn-sm btn-info">
                                                            <i class="fas fa-download"></i> Slip
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    {{-- <td colspan="9" class="text-center text-muted">No salary records
                                                        found.</td> --}}
                                                </tr>
                                            @endforelse
                                    </table>
                                    <div id="salaryPagination" class="d-flex justify-content-center mt-3">
                                        {{ $salaryHistory->links('pagination::bootstrap-5') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script>
        $(document).on('submit', '#payroll-filter-form', function(e) {
            e.preventDefault();
            fetchPayrolls(1);
        });

        $(document).on('click', '#salaryPagination a', function(e) {
            e.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            if (page) fetchPayrolls(page);
        });

        function fetchPayrolls(page) {
            var data = $('#payroll-filter-form').serialize();
            if (page) data += '&page=' + page;

            $.ajax({
                url: '{{ route("payrolls.salary.filter") }}',
                type: 'GET',
                data: data,
                dataType: 'json',
                success: function(res) {
                    $('#tableExport tbody').html(res.html);
                    $('#salaryPagination').html(res.pagination);
                },
                error: function(err) {
                }
            });
        }
    </script>
@endsection
