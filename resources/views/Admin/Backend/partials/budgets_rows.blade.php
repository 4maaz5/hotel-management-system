@forelse ($budgets as $budget)
    <tr data-id="{{ $budget->id }}">
        <td>{{ $budget->branch->id }}</td>
        <td>{{ $budget->branch->name }}</td>
        <td>{{ number_format($budget->total_budget, 2) }}</td>
        <td>{{ number_format($budget->used_budget, 2) }}</td>
        <td>{{ number_format($budget->remaining_budget, 2) }}</td>
        <td>
            @php
                $start = \Carbon\Carbon::parse($budget->start_date);
                $end = \Carbon\Carbon::parse($budget->end_date);
                $totalDays = $start->diffInDays($end);
                $months = intdiv($totalDays, 30);
                $days = $totalDays % 30;
                $duration = $months > 0 ? "{$months} month(s) {$days} day(s)" : "{$days} day(s)";
            @endphp
            {{ $duration }}
        </td>
        <td>
            @php
                $status = $budget->status;
                $badgeClass = match ($status) {
                    'On Track' => 'bg-success text-white',
                    'At Risk' => 'bg-warning text-white',
                    'Over Spent' => 'bg-danger text-white',
                    default => 'bg-secondary text-white',
                };
            @endphp
            <span class="badge {{ $badgeClass }}">{{ $status }}</span>
        </td>
        <td>
            <a href="#" class="text-secondary me-2 edit-btn" data-id="{{ $budget->id }}"><i
                    class="fas fa-edit"></i></a>
            <a href="#" class="text-danger delete-btn" data-id="{{ $budget->id }}"><i
                    class="fas fa-trash-alt"></i></a>
        </td>
    </tr>
@empty
    <tr>
        {{-- <td colspan="8" class="text-center">No budgets found.</td> --}}
    </tr>
@endforelse
