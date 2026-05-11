@extends('layouts.app')

@section('title', 'Manage Inventory')

@section('content')
    <main class="u-white-bg py-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
                <div>
                    <div class="text-muted text-uppercase small fw-semibold">Channel Manager</div>
                    <h2 class="fw-bold mb-2">Manage Inventory</h2>
                    <p class="text-muted mb-0">Use this screen as the selling-control checkpoint for the public website. Inventory and pricing still come from your live unit and rate setup.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('setup-sidebar.base_rate.index') }}" class="btn btn-outline-secondary">Base Rates</a>
                    <a href="{{ route('setup-sidebar.seasonal_rate.index') }}" class="btn btn-outline-secondary">Seasonal Rates</a>
                    <a href="{{ route('setup-sidebar.special_rate.index') }}" class="btn btn-outline-secondary">Special Rates</a>
                    <a href="{{ route('setup-sidebar.rate_plan.index') }}" class="btn btn-primary">Rate Plans</a>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase mb-2">Published Products</div>
                            <div class="fs-3 fw-bold">{{ $publishedProductsCount }}</div>
                            <div class="text-muted">Room products currently exposed to the direct website.</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase mb-2">Active Units</div>
                            <div class="fs-3 fw-bold">{{ $activeUnitsCount }}</div>
                            <div class="text-muted">Units that can support live availability calculations.</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase mb-2">Rate Plans</div>
                            <div class="fs-3 fw-bold">{{ $ratePlanCount }}</div>
                            <div class="text-muted">Existing rate logic the public website can reuse.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-1">Website Selling Overview</h5>
                    <p class="text-muted mb-0">This page does not replace your operational setup. It shows which room products are connected to real active units and therefore ready to sell on the direct website.</p>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">Product</th>
                                    <th class="py-3">Website Status</th>
                                    <th class="py-3">Active Units</th>
                                    <th class="py-3">Inventory Readiness</th>
                                    <th class="text-end px-4 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $row)
                                    @php
                                        $product = $row['product'];
                                        $ready = $product->is_published_online && $row['active_units'] > 0;
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="fw-semibold">{{ $product->website_name_en ?: $product->name }}</div>
                                            <div class="text-muted small">{{ $product->website_name_ar ?: '-' }}</div>
                                        </td>
                                        <td class="py-3">
                                            @if ($product->is_published_online)
                                                <span class="badge bg-success-subtle text-success">Published online</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary">Hidden online</span>
                                            @endif
                                        </td>
                                        <td class="py-3">{{ $row['active_units'] }}</td>
                                        <td class="py-3">
                                            @if ($ready)
                                                <span class="badge bg-success-subtle text-success">Ready to sell</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning">Needs setup</span>
                                            @endif
                                        </td>
                                        <td class="text-end px-4 py-3">
                                            <div class="d-inline-flex gap-2 flex-wrap justify-content-end">
                                                <a href="{{ route('setup-sidebar.typeCustomization.edit', $product->id) }}" class="btn btn-sm btn-outline-secondary">Edit Product</a>
                                                <a href="{{ route('setup-sidebar.rate_plan.index') }}" class="btn btn-sm btn-primary">Review Rates</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-4 text-center text-muted">No products are available yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
