<div class="row mb-3">
    <div class="col-md-6">
        <div class="card text-center ">
            <h6 class="mt-4">{{ __('dashboard.total_products') }}</h6>
            <h4 class="mb-3">{{ $totalProducts }}</h4>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card text-center">
            <h6 class="mt-4">{{ __('dashboard.total_stock') }}</h6>
            <h4 class="mb-3">{{ $totalQuantity }}</h4>
        </div>
    </div>
</div>

<hr>

<h5>{{ __('dashboard.inventory_details') }}</h5>

@foreach ($inventories->groupBy('room_id') as $roomId => $items)
    <h6 class="mt-3">
        {{ $items->first()->room->name ?? __('dashboard.no_section') }}
    </h6>

    <table class="table table-bordered mb-4">
        <thead>
            <tr>
                <th>{{ __('dashboard.product') }}</th>
                <th>{{ __('dashboard.quantity_left') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $inventory)
                <tr>
                    <td>{{ $inventory->product->name }}</td>
                    <td>{{ $inventory->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endforeach

<hr>

<h5>{{ __('dashboard.dispatched_stock') }}</h5>

<table class="table table-striped">
    <thead>
        <tr>
            <th>{{ __('dashboard.product') }}</th>
            <th>{{ __('dashboard.quantity') }}</th>
            <th>{{ __('dashboard.branch') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse($dispatched as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->stockRequest->branch->name ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center text-muted">
                    {{ __('dashboard.no_data') }}
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
