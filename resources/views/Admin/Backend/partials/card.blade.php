@forelse($employees as $employee)
    <tr id="employee-row-{{ $employee->id }}">
        <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
        <td>{{ $employee->employee_id ?? '-' }}</td>
        <td>{{ $employee->email ?? '-' }}</td>
        <td>{{ $employee->phone ?? '-' }}</td>
        <td>{{ $employee->branch->name ?? '-' }}</td>
        <td>{{ $employee->residence_expiry_date ?? '-' }}</td>
        <td>
            <a href="#" class="text-info" title="View Card" data-toggle="modal"
                data-target="#viewCardModal__{{ $employee->id }}">
                <i class="fas fa-id-card" style="font-size: 24px;"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
        {{-- <td colspan="7" class="text-center">No employees found.</td> --}}
    </tr>
@endforelse
