@if ($payrolls->count() > 0)
    @foreach ($payrolls as $payroll)
        <tr>
            <td>{{ $payroll->employee->employee_id }}</td>
            <td>{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</td>
            <td>{{ $payroll->employee->designation }}</td>
            <td>{{ $payroll->employee->email }}</td>
            <td>{{ $payroll->employee->join_date }}</td>
            <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $payroll->month)->format('F Y') }}</td>
            <td>{{ $payroll->basic_salary }}</td>
            <td>
                <a href="#" class="text-danger delete-payroll-btn" data-id="{{ $payroll->id }}" data-toggle="modal"
                    data-target="#deletePayrollModal">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        {{-- <td colspan="8" class="text-center">No records found</td> --}}
    </tr>
@endif
