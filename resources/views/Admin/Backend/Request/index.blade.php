@extends('layout.master')
@section('title', 'Dashboard | Request')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.all_requests') }}</h1>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.request') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#stockRequestModal">
                                    {{ __('dashboard.send_request') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                        <thead>
                                            <tr>

                                                <th>{{ __('dashboard.request_id') }}</th>
                                                <th>{{ __('dashboard.branch') }}</th>
                                                <th>{{ __('dashboard.requested_by') }}</th>
                                                <th>{{ __('dashboard.classify') }}</th>
                                                <th>{{ __('dashboard.status') }}</th>
                                                <th>{{ __('dashboard.created_at') }}</th>
                                                @can('manage_warehouse')
                                                    <th>{{ __('dashboard.print') }}</th>
                                                    <th>{{ __('dashboard.action') }}</th>
                                                @endcan
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($requests as $request)
                                                <tr>
                                                    <td>{{ $request->id }}</td>
                                                    <td>{{ $request->branch->name ?? '-' }}</td>
                                                    <td>{{ $request->requester->name }}</td>
                                                    <td>
                                                        @foreach ($request->items as $item)
                                                            {{ $item->product->name }} x {{ $item->quantity }}<br>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        @if ($request->status == 'pending')
                                                            <span
                                                                class="badge bg-warning">{{ __('dashboard.pending') }}</span>
                                                        @elseif($request->status == 'approved')
                                                            <span
                                                                class="badge bg-primary">{{ __('dashboard.approved') }}</span>
                                                        @elseif($request->status == 'dispatched')
                                                            <span
                                                                class="badge bg-info">{{ __('dashboard.dispatched') }}</span>
                                                        @elseif($request->status == 'received')
                                                            <span
                                                                class="badge bg-success">{{ __('dashboard.received') }}</span>
                                                        @elseif($request->status == 'rejected')
                                                            <span
                                                                class="badge bg-danger">{{ __('dashboard.rejected') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $request->created_at->format('d M Y H:i') }}</td>
                                                    @can('manage_warehouse')
                                                        <td>
                                                            <!-- Print Button -->
                                                            <a href="{{ route('warehouse-request.print', $request->id) }}"
                                                                target="_blank" class="btn btn-primary">
                                                                <i class="fas fa-print"></i> {{ __('dashboard.print') }}
                                                            </a>
                                                        </td>
                                                        <td>

                                                            @if ($request->status == 'pending')
                                                                <form
                                                                    action="{{ route('requests.approve', $request->id) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-primary">{{ __('dashboard.approved') }}</button>
                                                                </form>
                                                            @elseif($request->status == 'approved')
                                                                <form
                                                                    action="{{ route('requests.dispatch', $request->id) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-info">{{ __('dashboard.dispatched') }}</button>
                                                                </form>
                                                            @else
                                                                <span
                                                                    class="text-muted">{{ ucfirst($request->status) }}</span>
                                                            @endif
                                                        </td>
                                                    @endcan
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


    </div>
    <!-- Add Stock Request Item Modal -->
    <div class="modal fade" id="stockRequestModal" tabindex="-1" aria-labelledby="addStockRequestItemModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="addStockRequestItemModalLabel">{{ __('dashboard.add_stock_request_item') }}
                    </h5>
                    <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <form method="POST" action="{{ route('dashboard.requests.store') }}">
                        @csrf

                        <div class="table-responsive">
                            <table class="table table-bordered" id="productsTable">
                                <thead>
                                    <tr>
                                        <th>{{ __('dashboard.classify') }}</th>
                                        <th>{{ __('dashboard.quantity') }}</th>
                                        <th>{{ __('dashboard.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select name="products[0][product_id]" class="form-control" required>
                                                <option value="">{{ __('dashboard.select_classify') }}</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" name="products[0][quantity]"
                                                min="1" value="1" required>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-success" id="addRowBtn">+</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="text-end mt-3">
                            <button type="reset" class="btn btn-secondary me-2">{{ __('dashboard.reset') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('dashboard.submit_request') }}</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Optional JS to add/remove rows dynamically -->
    <script>
        let rowIndex = 1;
        document.getElementById('addRowBtn').addEventListener('click', function() {
            const tableBody = document.querySelector('#productsTable tbody');
            const newRow = document.createElement('tr');

            newRow.innerHTML = `
        <td>
            <select name="products[${rowIndex}][product_id]" class="form-control" required>
                <option value="">{{ __('dashboard.select_classify') }}</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" class="form-control" name="products[${rowIndex}][quantity]" min="1" value="1" required>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger removeRowBtn">-</button>
        </td>
    `;

            tableBody.appendChild(newRow);
            rowIndex++;

            // Add remove functionality
            newRow.querySelector('.removeRowBtn').addEventListener('click', function() {
                newRow.remove();
            });
        });
    </script>

@endsection
