<h6>Departments for {{ $branch->name }}</h6>

<ul class="list-group">
    @foreach ($departments as $dept)
        <li class="list-group-item dept-item" data-id="{{ $dept->id }}" style="cursor:pointer;">
            {{ $dept->name }}
        </li>
    @endforeach
</ul>

{{-- Employees List Loaded Here --}}
<div id="employeesContainer" class="mt-3"></div>

{{-- Employee Detail Loaded Here --}}
<div id="employeeDetailContainer" class="mt-3"></div>

<script>
    $('.dept-item').click(function() {
        let deptId = $(this).data('id');
        $('#employeeDetailContainer').html(''); // Clear detail when switching dept

        $.ajax({
            url: '/dashboard/department/' + deptId + '/employees',
            type: 'GET',
            success: function(result) {
                $('#employeesContainer').html(result);
            }
        });
    });
</script>
