@if ($payrolls->count() > 0)
    @foreach ($payrolls as $payroll)
        <tr>
            <td>{{ $loop->iteration }}</td>
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
                <a href="{{ route('dashboard.payroll.payslip.download', $payroll->id) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-download"></i> Slip
                </a>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        {{-- <td colspan="8" class="text-center">No records found</td> --}}
    </tr>
@endif
