 @forelse($leaves as $leave)
     <tr data-id="{{ $leave->id }}">
         <td class="leave-employee">
             {{ $leave->employee->first_name }}
             {{ $leave->employee->last_name }}
         </td>
         <td class="leave-type">{{ ucfirst($leave->leave_type) }}</td>
         <td class="leave-start">{{ $leave->start_date }}</td>
         <td class="leave-end">{{ $leave->end_date }}</td>
         <td class="leave-reason">{{ $leave->reason ?? '-' }}</td>
         <td class="leave-status">
             <span
                 class="badge
                    @if ($leave->status == 'approved') bg-success
                    @elseif($leave->status == 'rejected') bg-danger
                    @elseif($leave->status == 'in_progress') bg-warning
                    @else bg-secondary @endif">
                 {{ ucfirst($leave->status) }}
             </span>
         </td>
         <td>
             @foreach ($leave->documents as $doc)
                 <a href="#" class="view-pdf" data-file="{{ asset('storage/' . $doc->file_path) }}"
                     title="View PDF">
                     <i class="fas fa-file-pdf text-secondary" style="font-size: 18px;"></i>
                 </a>
             @endforeach

         </td>
         <td>

             <a href="#" class="text-secondary edit-leave-btn" data-toggle="modal" data-target="#editLeaveModal"
                 data-id="{{ $leave->id }}" data-employee_id="{{ $leave->employee_id }}"
                 data-leave_type="{{ $leave->leave_type }}" data-start_date="{{ $leave->start_date }}"
                 data-end_date="{{ $leave->end_date }}" data-reason="{{ $leave->reason }}"
                 data-status="{{ $leave->status }}" data-payment_type="{{ $leave->payment_type }}"
                 data-travel_responsibility="{{ $leave->travel_responsibility }}"
                 data-total_days="{{ $leave->total_days }}" data-ticket_amount="{{ $leave->ticket_amount }}">
                 <i class="fas fa-edit"></i>
             </a>

             <a href="#" class="text-danger delete-leave-btn" data-toggle="modal" data-target="#deleteLeaveModal"
                 data-id="{{ $leave->id }}">
                 <i class="fas fa-trash-alt"></i>
             </a>

         </td>
     </tr>
 @empty
     <tr>
         {{-- <td colspan="6" class="text-center">No leave records found.</td> --}}
     </tr>
 @endforelse
