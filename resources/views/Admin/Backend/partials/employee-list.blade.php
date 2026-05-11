<h5>{{ __('dashboard.employees_in') }} {{ $department->name }} {{ __('dashboard.department') }}</h5>

<p class="text-muted">
    {{ __('dashboard.total_employees') }}: {{ $employees->count() }}
</p>

@if ($employees->isEmpty())
    <p class="text-muted">No employees found.</p>
@else
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('dashboard.employee_id') }}</th>
                <th>{{ __('dashboard.name') }}</th>
                <th>{{ __('dashboard.designation') }}</th>
                <th>{{ __('dashboard.basic_salary') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employees as $emp)
                <tr class="employee-row" data-id="{{ $emp->id }}" style="cursor:pointer;">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $emp->employee_id }}</td>
                    <td>{{ $emp->first_name }} {{ $emp->last_name }}</td>
                    <td>{{ $emp->designation }}</td>
                    <td>{{ $emp->base_salary }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

<script>
    $('.employee-row').click(function() {
        let empId = $(this).data('id');

        $.ajax({
            url: '/dashboard/employee/' + empId + '/details',
            type: 'GET',
            success: function(result) {
                $('#employeeDetailContainer').html(result);
            }
        });
    });
</script>
