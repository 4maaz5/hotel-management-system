@extends('layouts.app')

@section('title', 'Manage Products')

@section('content')
    <main class="u-white-bg py-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
                <div>
                    <div class="text-muted text-uppercase small fw-semibold">Channel Manager</div>
                    <h2 class="fw-bold mb-2">Manage Products</h2>
                    <p class="text-muted mb-0">These are the room products shown on the public website. Their website names, summaries, slugs, and SEO settings are managed from Unit Type Customization.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('setup-sidebar.typeCustomization.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-layer-group me-2"></i>All Room Types
                    </a>
                    <a href="{{ route('setup-sidebar.typeCustomization.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>New Product
                    </a>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">Product</th>
                                    <th class="py-3">Public Status</th>
                                    <th class="py-3">Slug</th>
                                    <th class="py-3">SEO</th>
                                    <th class="py-3">Linked Units</th>
                                    <th class="text-end px-4 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $row)
                                    @php
                                        $product = $row['product'];
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="fw-semibold">{{ $row['websiteName'] }}</div>
                                            <div class="text-muted small">{{ $product->website_name_ar ?: $product->name }}</div>
                                            <div class="text-muted small">Internal name: {{ $product->name }}</div>
                                        </td>
                                        <td class="py-3">
                                            @if ($product->is_published_online)
                                                <span class="badge bg-success-subtle text-success">Published</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary">Hidden</span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            <code>{{ $product->website_slug ?: 'slug-auto' }}</code>
                                        </td>
                                        <td class="py-3">
                                            @if ($row['seoReady'])
                                                <span class="badge bg-success-subtle text-success">SEO ready</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning">Needs SEO fields</span>
                                            @endif
                                        </td>
                                        <td class="py-3">{{ $row['assignedUnits'] }}</td>
                                        <td class="text-end px-4 py-3">
                                            <div class="d-inline-flex gap-2 flex-wrap justify-content-end">
                                                @if ($product->is_published_online && $row['previewSlug'])
                                                    <a href="{{ route('booking.rooms.show', ['roomType' => $row['previewSlug']]) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary">
                                                        Preview
                                                    </a>
                                                @endif
                                                <a href="{{ route('setup-sidebar.typeCustomization.edit', $product->id) }}" class="btn btn-sm btn-primary">
                                                    Edit Product
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-4 text-center text-muted">No room products have been created yet.</td>
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
