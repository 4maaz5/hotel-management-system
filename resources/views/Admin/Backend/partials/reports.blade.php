<div class="container-fluid">

    {{-- Company Header --}}
    <div class="mb-3 text-center">
        @if ($company->logo)
            <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->legal_name }}" class="img-fluid mb-2"
                style="height:80px; object-fit:cover;">
        @else
            <span class="badge bg-secondary mb-2">No Logo</span>
        @endif
        <h4 class="mb-0">{{ $company->legal_name }}</h4>
        <small class="text-muted">{{ $company->email }} | {{ $company->phone }}</small>
    </div>

    <div class="row" id="brandsContainer">
        @foreach ($brands as $brand)
            <div class="col-6 col-md-3 mb-3 text-center">
                <div class="card brand-card" data-id="{{ $brand->id }}" style="cursor:pointer;">
                    @if ($brand->logo)
                        <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}"
                            class="img-fluid mb-1" style="height:60px; object-fit:cover;">
                    @else
                        <span class="badge bg-secondary mb-1">No Logo</span>
                    @endif
                    <div>{{ $brand->name }}</div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Container for branch drill-down --}}
    <div id="brandBranchesContainer" class="mt-4"></div>



</div>
<script>
    $(document).ready(function() {
        $('.brand-card').click(function() {
            let brandId = $(this).data('id');

            // AJAX request to get branches
            $.ajax({
                url: '/dashboard/brand/' + brandId + '/branches',
                type: 'GET',
                success: function(response) {
                    // Populate the branch container
                    $('#brandBranchesContainer').html(response);
                },
                error: function() {
                    alert('Failed to load branches!');
                }
            });
        });
    });
    $(document).ready(function() {
        $('.brand-card').click(function() {
            let brandId = $(this).data('id');

            // AJAX request to get branches
            $.ajax({
                url: '/dashboard/brand/' + brandId + '/branches',
                type: 'GET',
                success: function(response) {
                    // Populate the branch container
                    $('#brandBranchesContainer').html(response);
                },
                error: function() {
                    alert('Failed to load branches!');
                }
            });
        });
    });
</script>
