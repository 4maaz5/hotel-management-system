<h5>{{ __('dashboard.branches_for') }} {{ $brand->name }}</h5>
<div class="row">
    @foreach ($branches as $branch)
        <div class="col-6 col-md-3 mb-3 text-center">
            <div class="card branch-card" data-id="{{ $branch->id }}" style="cursor:pointer;">

                <div> {{ $branch->name }}</div>
            </div>
        </div>
    @endforeach
</div>

{{-- Container for branch departments --}}
<div id="branchDepartmentsContainer-{{ $brand->id }}" class="mt-3"></div>

<script>
    $('.branch-card').click(function() {
        let branchId = $(this).data('id');

        $.ajax({
            url: '/dashboard/branch/' + branchId + '/departments',
            type: 'GET',
            success: function(response) {
                $('#branchDepartmentsContainer-' + {{ $brand->id }}).html(response);
            },
            error: function() {
                alert('Failed to load departments!');
            }
        });
    });
</script>
