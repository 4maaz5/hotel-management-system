@forelse ($employeeDocs as $employeeDoc)
    <tr id="employeeDocRow{{ $employeeDoc->id }}">

        <td>{{ $employeeDoc->employee->employee_id }}</td>
        <td>{{ $employeeDoc->employee->first_name }}</td>
        <td>{{ $employeeDoc->type }}</td>
        <td>{{ $employeeDoc->document_number }}</td>
        <td>{{ $employeeDoc->issue_date }}</td>
        <td>{{ $employeeDoc->expiration_date }}</td>

        <td>
            @if ($employeeDoc->hasStoredFile())
                <a href="#" class="view-pdf" data-file="{{ route('dashboard.document.employee.file', $employeeDoc) }}">
                    <i class="fas fa-file-pdf text-secondary" style="font-size: 18px;"></i>
                </a>
            @elseif ($employeeDoc->file_path)
                <span class="text-muted small">{{ __('File missing') }}</span>
            @endif
        </td>

        <td>
            <a href="#" class="text-secondary editEmployeeDocBtn" data-id="{{ $employeeDoc->id }}"
                data-employee="{{ $employeeDoc->employee->id }}" data-type="{{ $employeeDoc->type }}"
                data-doc_number="{{ $employeeDoc->document_number }}" data-issue_date="{{ $employeeDoc->issue_date }}"
                data-expiry_date="{{ $employeeDoc->expiration_date }}">
                <i class="fas fa-edit"></i>
            </a>

            <a href="#" class="text-danger deleteEmployeeDocBtn" data-id="{{ $employeeDoc->id }}">
                <i class="fas fa-trash-alt"></i>
            </a>
        </td>

    </tr>
@empty
    <tr>
        {{-- <td colspan="8" class="text-center text-muted">No records found.</td> --}}
    </tr>
@endforelse
