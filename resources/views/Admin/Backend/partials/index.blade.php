@forelse($employees as $employee)
    <tr id="employee-row-{{ $employee->id }}">
        <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
        <td>{{ $employee->employee_id ?? '-' }}</td>
        <td>{{ $employee->email ?? '-' }}</td>
        <td>{{ $employee->phone ?? '-' }}</td>
        <td>{{ $employee->branch->name ?? '-' }}</td>
        <td>{{ $employee->shift->name ?? '-' }}</td>
        <td>{{ $employee->residence_expiry_date ?? '-' }}</td>
        <td>
            <a href="{{ route('dashboard.employee.profile.index', $employee->id) }}" class="text-secondary"
                title="Profile">
                <i class="fas fa-user"></i>
            </a>
            <a href="#" class="text-secondary view-employee-btn" data-id="{{ $employee->id }}" title="View">
                <i class="fas fa-eye"></i>
            </a>
            @can('manage_employee')
                <a href="#" class="text-secondary edit-employee-btn" data-id="{{ $employee->id }}">
                    <i class="fas fa-edit"></i>
                </a>
                <a href="#" class="text-danger deleteEmployeeBtn" data-id="{{ $employee->id }}">
                    <i class="fas fa-trash-alt"></i>
                </a>
            @endcan
        </td>
    </tr>
@empty
    <tr>
        {{-- <td colspan="7" class="text-center">No employees found.</td> --}}
    </tr>
@endforelse

{{-- Pagination --}}
{{-- <tr>
    <td colspan="7">
        <div id="employeePagination" class="mt-3">
            {{ $employees->links('pagination::bootstrap-5') }}
        </div>
    </td>
</tr> --}}
