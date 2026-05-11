@extends('layout.master')
@section('title', 'Dashboard | Attendance')
@section('main')
    <div class="main-content">
        <div class="card">
            <div class="card-header">
                <h4>{{ __('dashboard.commission_report') }}</h4>
            </div>

            <div class="card-body">

                <!-- Filters -->
                <form method="GET" action="#">
                    <div class="row">

                        <div class="col-md-3">
                            <label>{{ __('dashboard.employee') }}</label>
                            <select name="employee_id" class="form-control">
                                <option value="">All/الجميع</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>{{ __('dashboard.branch') }}</label>
                            <select name="branch_id" class="form-control">
                                <option value="">All/الجميع</option>
                                @foreach ($branches as $b)
                                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- <div class="col-md-2">
                            <label>Month</label>
                            <input type="month" name="month" class="form-control">
                        </div> --}}

                        <div class="col-md-2">
                            <label>{{ __('dashboard.date_from') }}</label>
                            <input type="date" name="date_from" class="form-control">
                        </div>

                        <div class="col-md-2">
                            <label>{{ __('dashboard.date_to') }}</label>
                            <input type="date" name="date_to" class="form-control">
                        </div>

                    </div>

                    <div class="mt-3">
                        <button class="btn btn-primary">{{ __('dashboard.filter') }}</button>

                        <a href="{{ route('finance.reports.commission.excel', request()->all()) }}"
                            class="btn btn-success">
                            {{ __('dashboard.export_excel') }}
                        </a>

                        <a href="{{ route('finance.reports.commission.pdf', request()->all()) }}" class="btn btn-danger">
                            {{ __('dashboard.export_pdf') }}
                        </a>
                    </div>
                </form>

                <hr>

                <!-- Table -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{ __('dashboard.employee') }}</th>
                            <th>{{ __('dashboard.branch') }}</th>
                            <th>{{ __('dashboard.sale_amount') }}</th>
                            <th>{{ __('dashboard.commission') }} %</th>
                            <th>{{ __('dashboard.commission_earned') }}</th>
                            <th>{{ __('dashboard.date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalCommission = 0;
                            $totalAmount = 0;
                        @endphp
                        @foreach ($records as $rec)
                            @php
                                $percent = $rec->employee?->commission_percentage ?? 0; // default to 0 if null
                                $amount = $rec->amount ?? 0;
                                $commission = ($amount * $percent) / 100;
                                $totalCommission += $commission;
                                $totalAmount += $amount;
                                $employeeName =
                                    ($rec->employee?->first_name ?? '') . ' ' . ($rec->employee?->last_name ?? '');
                                $branchName = $rec->branch?->name ?? '';
                                $incomeDate = $rec->income_date ?? '';
                            @endphp
                            <tr>
                                <td>{{ $employeeName }}</td>
                                <td>{{ $branchName }}</td>
                                <td>{{ number_format($amount, 2) }}</td>
                                <td>{{ $percent }}%</td>
                                <td>{{ number_format($commission, 2) }}</td>
                                <td>{{ $incomeDate }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" style="text-align:right;">{{ __('dashboard.total') }}</td>
                            <td>{{ number_format($totalAmount, 2) }}</td>
                            <td>—</td>
                            <td>{{ number_format($totalCommission, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>

            </div>
        </div>
    </div>

@endsection
