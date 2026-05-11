@forelse ($departments as $department)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $department->name }}</td>
        <td>{{ $department->branch->name ?? 'N/A' }}</td>
        <td>
            <a href="#" class="text-secondary editDepartmentBtn" data-id="{{ $department->id }}"
                data-name="{{ $department->name }}" data-branch-id="{{ $department->branch_id }}"
                data-branch-name="{{ $department->branch->name ?? '' }}">
                <i class="fas fa-edit"></i>
            </a>
            <a href="#" class="text-danger deleteDepartmentBtn" data-id="{{ $department->id }}"
                data-name="{{ $department->name }}" data-toggle="modal" data-target="#deleteDeptModal">
                <i class="fas fa-trash-alt"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
        {{-- <td colspan="4" class="text-center">No departments found.</td> --}}
    </tr>
@endforelse
