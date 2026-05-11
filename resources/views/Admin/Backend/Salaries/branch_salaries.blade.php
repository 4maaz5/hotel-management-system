@extends('layout.master')
@section('title', 'Dashboard | Salary Distribution')
@section('main')

    <div class="main-content">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ __('dashboard.branch_salary_distribution') }}</h4>
                {{-- <span class="badge bg-light text-dark">{{ date('F Y') }}</span> --}}
            </div>

            <div class="card-body">

                <!-- Branch Summary Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="text-muted mb-3">{{ __('dashboard.branch_summary') }}</h5>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card border-0 bg-light">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                <h6 class="text-muted mb-1">{{ __('dashboard.total_employees') }}</h6>
                                <h3 class="mb-0">{{ $summary['total_employees'] }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card border-0 bg-light">
                            <div class="card-body text-center">
                                <i class="fas fa-money-bill-wave fa-2x text-info mb-2"></i>
                                <h6 class="text-muted mb-1">{{ __('dashboard.total_salary') }}</h6>
                                <h3 class="mb-0">{{ number_format($summary['total_salary'], 2) }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card border-0 bg-light">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <h6 class="text-muted mb-1">{{ __('dashboard.paid_salary') }}</h6>
                                <h3 class="mb-0 text-success">{{ number_format($summary['paid_salary'], 2) }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card border-0 bg-light">
                            <div class="card-body text-center">
                                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                <h6 class="text-muted mb-1">{{ __('dashboard.pending_salary') }}</h6>
                                <h3 class="mb-0 text-warning">{{ number_format($summary['pending_salary'], 2) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Payroll Table Section -->
                <form action="{{ route('finance.branch-salaries.pay') }}" method="POST">
                    @csrf

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-muted mb-0">{{ __('dashboard.employee_payroll_details') }}</h5>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> {{ __('dashboard.mark_selected_as_paid') }}
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="select-all" class="form-check-input">
                                    </th>
                                    <th>{{ __('dashboard.employee_name') }}</th>
                                    <th class="text-end">{{ __('dashboard.base_salary') }}</th>
                                    <th class="text-end">{{ __('dashboard.allowance') }}</th>
                                    <th class="text-end">{{ __('dashboard.commission') }}</th>
                                    <th class="text-end">{{ __('dashboard.net_pay') }}</th>
                                    <th class="text-center">{{ __('dashboard.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payrolls as $payroll)
                                    <tr>
                                        <td class="text-center">
                                            @if ($payroll->status == 'Pending')
                                                <input type="checkbox" name="payroll_ids[]" value="{{ $payroll->id }}"
                                                    class="form-check-input payroll-checkbox">
                                            @else
                                                <i class="fas fa-check text-success"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-primary text-white me-2">
                                                    {{ strtoupper(substr($payroll->employee->first_name, 0, 1)) }}{{ strtoupper(substr($payroll->employee->last_name, 0, 1)) }}
                                                </div>
                                                <strong>{{ $payroll->employee->first_name }}
                                                    {{ $payroll->employee->last_name }}</strong>
                                            </div>
                                        </td>
                                        <td class="text-end">{{ number_format($payroll->basic_salary, 2) }}</td>
                                        <td class="text-end">{{ number_format($payroll->allowance, 2) }}</td>
                                        <td class="text-end">{{ number_format($payroll->commission_amount, 2) }}</td>
                                        <td class="text-end"><strong>{{ number_format($payroll->net_pay, 2) }}</strong>
                                        </td>
                                        <td class="text-center">
                                            @if ($payroll->status == 'Pending')
                                                <span
                                                    class="badge bg-warning text-dark">{{ __('dashboard.pending') }}</span>
                                            @else
                                                <span class="badge bg-success">{{ __('dashboard.paid') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                            {{ __('dashboard.no_payroll_records_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $payrolls->links('pagination::bootstrap-5') }}
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script>
        // Select All Checkbox Functionality
        document.getElementById('select-all')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.payroll-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Update select-all checkbox when individual checkboxes change
        document.querySelectorAll('.payroll-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allCheckboxes = document.querySelectorAll('.payroll-checkbox');
                const checkedCheckboxes = document.querySelectorAll('.payroll-checkbox:checked');
                const selectAll = document.getElementById('select-all');

                if (selectAll) {
                    selectAll.checked = allCheckboxes.length === checkedCheckboxes.length;
                }
            });
        });
    </script>

@endsection
