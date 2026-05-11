<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center">
            @if ($employee->image)
                <img src="{{ asset('storage/' . $employee->image) }}" class="rounded-circle me-3"
                    style="width:80px; height:80px; object-fit:cover;">
            @else
                <div class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center me-3"
                    style="width:80px; height:80px;">
                    No Photo
                </div>
            @endif

            <div>
                <h5 class="mb-0">{{ $employee->first_name }} {{ $employee->last_name }}</h5>
                <small class="text-muted">{{ $employee->designation }}</small>
            </div>
        </div>

        <hr>

        <p><strong>{{ __('dashboard.employee_id') }}:</strong> {{ $employee->employee_id }}</p>
        <p><strong>{{ __('dashboard.email') }}:</strong> {{ $employee->email }}</p>
        <p><strong>{{ __('dashboard.phone') }}:</strong> {{ $employee->phone }}</p>
        <p><strong>{{ __('dashboard.join_date') }}:</strong> {{ $employee->join_date }}</p>

        <p><strong>{{ __('dashboard.branch') }}:</strong> {{ $employee->branch->name ?? '-' }}</p>
        <p><strong>{{ __('dashboard.department') }}:</strong> {{ $employee->department->name ?? '-' }}</p>
        <p><strong>{{ __('dashboard.basic_salary') }}:</strong> {{ $employee->base_salary ?? '-' }}</p>

        {{-- Add more fields if you want --}}
    </div>
</div>
