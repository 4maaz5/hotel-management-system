@forelse($attendances as $attendance)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $attendance->employee->first_name ?? 'N/A' }} {{ $attendance->employee->last_name ?? '' }}</td>
        <td>{{ $attendance->date }}</td>
        <td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '-' }}</td>
        <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '-' }}</td>
        <td>
            @if ($attendance->status == 'Present')
                <span class="badge badge-success">{{ __('dashboard.present') }}</span>
            @elseif ($attendance->status == 'Absent')
                <span class="badge badge-danger">{{ __('dashboard.absent') }}</span>
            @else
                <span class="badge badge-warning">{{ __('dashboard.leave') }}</span>
            @endif
        </td>

        <td>{{ $attendance->overtime_hours ?? '-' }}</td>
        <td>
            @if ($attendance->overtime_status == 'pending')
                <span class="badge badge-warning">{{ __('dashboard.pending') }}</span>
            @elseif ($attendance->status == 'approved')
                <span class="badge badge-success">{{ __('dashboard.approved') }}</span>
            @else
                <span class="badge badge-warning">{{ __('dashboard.cancelled') }}</span>
            @endif
        </td>
        <td>
            <a href="#" class="text-secondary edit-attendance-btn" data-toggle="modal"
                data-target="#editAttendanceModal" data-id="{{ $attendance->id }}"
                data-employee_id="{{ $attendance->employee_id }}" data-date="{{ $attendance->date }}"
                data-check_in="{{ $attendance->check_in }}" data-check_out="{{ $attendance->check_out }}"
                data-status="{{ $attendance->status }}" data-overtime-status="{{ $attendance->overtime_status }}"
                data-overtime_hours="{{ $attendance->overtime_hours }}">
                <i class="fas fa-edit"></i>
            </a>
            <a href="#" class="text-danger delete-attendance-btn" data-toggle="modal"
                data-target="#deleteAttendanceModal" data-id="{{ $attendance->id }}"
                data-employee="{{ $attendance->employee->first_name ?? 'N/A' }} {{ $attendance->employee->last_name ?? '' }}"
                data-date="{{ $attendance->date }}">
                <i class="fas fa-trash-alt"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center text-muted">No attendance records found.</td>
    </tr>
@endforelse
