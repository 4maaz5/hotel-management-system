@extends('layouts.app')

@section('title', 'Discount Type')
<style>
    .parent-Contact {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .contact-number.style-number {
        color: #333;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .contact-number.background-icon,
    .contact-number.u-cursor-pointer {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .page-category {
        font-size: 0.875rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .page-header__title {
        font-size: 1.75rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .page-header__subtitle {
        font-size: 1rem;
        color: #6c757d;
    }

    .n-table__top-btns {
        display: flex;
        gap: 0.75rem;
    }

    .n-button {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
    }

    .n-button--green {
        background-color: #2335da;
        color: white;
        border-color: #190cd8;
    }

    .n-button--green:hover {
        background-color: #3759f1;
        border-color: #292ce9;
    }

    .unit-card .card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
        z-index: 10;
    }

    .unit-card:hover .card-overlay {
        opacity: 1;
    }

    .unit-card .card-overlay .btn {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .unit-card .card-overlay .btn i {
        font-size: 16px;
    }
</style>
@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">

        <!-- Page Header -->
        <div class="page-category">{{ __('dashboard.financials') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.discount_types') }}</h2>
                <div class="page-header__subtitle">
                    {{ __('dashboard.set_the_discount_types_you_will_be_use_on_all_of_your_properties') }}</div>
            </div>
            <div class="n-table__top-btns">

                <div>
                    @can('discount_type.add')
                        <a href="{{ route('setup-sidebar.unit.create') }}" class="n-button n-button--green"
                            style="text-decoration:none;" tabindex="0" data-bs-toggle="modal"
                            data-bs-target="#addDiscountTypeModal">
                            {{ __('dashboard.new_discount_type') }}
                        </a>
                    @endcan

                </div>
            </div>
        </div>


        <div class="container mt-5">
            <div class="card">
                <div style="overflow-x: auto;">
                    <table class="table table-bordered table-striped align-middle text-center bg-white mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('dashboard.type') }}</th>
                                <th>{{ __('dashboard.report_name') }}</th>
                                <th>{{ __('dashboard.description') }}</th>
                                <th>{{ __('dashboard.status') }}</th>
                                <th>{{ __('dashboard.actions') }}</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($discountTypes as $key => $discount)
                                <tr>
                                    <td>{{ $key + 1 }}</td>

                                    <td>
                                        {{ ucfirst(str_replace('_', ' ', $discount->type)) }}
                                    </td>

                                    <td>
                                        {{ $discount->report_name }}
                                    </td>

                                    <td>
                                        {{ $discount->description ?? '-' }}
                                    </td>

                                    <td>
                                        @if ($discount->is_active)
                                            <span class="badge bg-success">
                                                {{ __('dashboard.active') }}
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                {{ __('dashboard.inactive') }}
                                            </span>
                                        @endif
                                    </td>

                                    <td>

                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            @can('discount_type.edit')
                                                <!-- Edit Button -->
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                    data-bs-target="#editDiscountModal-{{ $discount->id }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                            @endcan


                                            <!-- Dropdown Menu -->
                                            <div class="dropdown">

                                                <button class="btn btn-light btn-sm" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>

                                                <ul class="dropdown-menu dropdown-menu-end">

                                                    @if ($discount->is_active)
                                                        <li>
                                                            <form
                                                                action="{{ route('setup-sidebar.discount.toggle', $discount->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('PATCH')

                                                                <button class="dropdown-item text-danger" type="submit">
                                                                    <i class="bi bi-toggle-off me-2"></i>
                                                                    {{ __('dashboard.deactivate') }}
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <form
                                                                action="{{ route('setup-sidebar.discount.toggle', $discount->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('PATCH')

                                                                <button class="dropdown-item text-success" type="submit">
                                                                    <i class="bi bi-toggle-on me-2"></i>
                                                                    {{ __('dashboard.activate') }}
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif

                                                </ul>

                                            </div>

                                        </div>

                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6">
                                        {{ __('dashboard.no_discount_found') }}
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </main>

    <!-- Add Discount Type Modal -->
    <div class="modal fade" id="addDiscountTypeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form action="{{ route('setup-sidebar.discount.store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('dashboard.add_discount_type') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <!-- Type -->
                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('dashboard.discount_type') }} *
                            </label>

                            <select name="type" class="form-select" required>
                                <option value="">{{ __('dashboard.select_type') }}</option>
                                <option value="governmental_sector">{{ __('dashboard.governmental_sector') }}</option>
                                <option value="private_sector">{{ __('dashboard.private_sector') }}</option>
                                <option value="trainees">{{ __('dashboard.trainees') }}</option>
                                <option value="social_media">{{ __('dashboard.social_media') }}</option>
                                <option value="aramco_employee">{{ __('dashboard.aramco_employees') }}</option>
                                <option value="offer">{{ __('dashboard.offer') }}</option>
                                <option value="opening">{{ __('dashboard.opening') }}</option>
                                <option value="staff">{{ __('dashboard.staff') }}</option>
                            </select>
                        </div>

                        <!-- Report Name -->
                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('dashboard.report_name') }} *
                            </label>

                            <input type="text" name="report_name" class="form-control mb-2"
                                placeholder="{{ __('dashboard.report_name') }}" required>

                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('dashboard.description') }}
                            </label>

                            <textarea name="description" class="form-control mb-2" placeholder="{{ __('dashboard.description') }}" rows="2"></textarea>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.discard') }}
                        </button>

                        <button type="submit" class="btn btn-primary">
                            {{ __('dashboard.save') }}
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    @foreach ($discountTypes as $discount)
        <!-- Edit Modal -->
        <div class="modal fade" id="editDiscountModal-{{ $discount->id }}" tabindex="-1">

            <div class="modal-dialog">
                <div class="modal-content">

                    <form action="{{ route('setup-sidebar.discount.update', $discount->id) }}" method="POST">

                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ __('dashboard.edit_discount') }}
                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <div class="form-check form-switch mb-3">

                                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                    {{ $discount->is_active ? 'checked' : '' }}>

                                <label class="form-check-label">
                                    {{ __('dashboard.active_discount_type') }}
                                </label>

                            </div>

                            <!-- Type -->
                            <div class="mb-3">
                                <label class="form-label">
                                    {{ __('dashboard.discount_type') }}
                                </label>

                                <select name="type" class="form-select" required>

                                    @php
                                        $types = [
                                            'governmental_sector',
                                            'private_sector',
                                            'trainees',
                                            'social_media',
                                            'aramco_employee',
                                            'offer',
                                            'opening',
                                            'staff',
                                        ];
                                    @endphp

                                    @foreach ($types as $type)
                                        <option value="{{ $type }}"
                                            {{ $discount->type == $type ? 'selected' : '' }}>
                                            {{ __('dashboard.' . $type) }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <!-- Report Name -->
                            <div class="mb-3">
                                <label class="form-label">
                                    {{ __('dashboard.report_name') }}
                                </label>

                                <input type="text" name="report_name" class="form-control"
                                    value="{{ $discount->report_name }}" required>
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label class="form-label">
                                    {{ __('dashboard.description') }}
                                </label>

                                <textarea name="description" class="form-control" rows="3">{{ $discount->description }}</textarea>
                            </div>

                        </div>

                        <div class="modal-footer">

                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ __('dashboard.discard') }}
                            </button>

                            <button type="submit" class="btn btn-primary">
                                {{ __('dashboard.update') }}
                            </button>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    @endforeach

@endsection

@push('scripts')
<script>
(function() {
    document.addEventListener('show.bs.dropdown', function(e) {
        var btn = e.target;
        if (!btn.closest('.table-responsive') && !btn.closest('[style*="overflow"]')) return;
        var menu = btn.closest('.dropdown').querySelector('.dropdown-menu');
        btn._ddFix = { menu: menu, parent: menu.parentNode };
    });
    document.addEventListener('shown.bs.dropdown', function(e) {
        var btn = e.target;
        var ref = btn._ddFix;
        if (!ref || !ref.menu) return;
        var r = ref.menu.getBoundingClientRect();
        document.body.appendChild(ref.menu);
        ref.menu.style.position = 'fixed';
        ref.menu.style.top = r.top + 'px';
        ref.menu.style.left = r.left + 'px';
        ref.menu.style.transform = 'none';
    });
    document.addEventListener('hidden.bs.dropdown', function(e) {
        var btn = e.target;
        var ref = btn._ddFix;
        if (!ref) return;
        if (ref.menu && ref.menu.parentNode === document.body) {
            ref.menu.style.cssText = '';
            ref.parent.appendChild(ref.menu);
        }
        delete btn._ddFix;
    });
})();
</script>
@endpush
