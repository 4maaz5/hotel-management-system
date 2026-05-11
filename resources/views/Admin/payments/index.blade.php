@extends('layouts.app')

@section('title', 'Payment Methods')
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
                <h2 class="page-header__title">{{ __('dashboard.payment_methods') }}</h2>
                <div class="page-header__subtitle">
                    {{ __('dashboard.set_the_payment_methods_you_will_be_use_on_this_property') }}</div>
            </div>
            <div class="n-table__top-btns">

                <div>
                    @can('payment_method.add')
                        <a href="#" class="n-button n-button--green" style="text-decoration:none;" tabindex="0"
                            data-bs-toggle="modal" data-bs-target="#paymentConfigModal">
                            {{ __('dashboard.add_payment') }}
                        </a>
                    @endcan

                </div>
            </div>
        </div>

        <div class="container mt-5">
            <!-- Table -->
            <div class="table-responsive">
                <div class="card">

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:70px;"></th>
                                    <th style="width:400px;">{{ __('dashboard.payment_method') }}</th>
                                    <th>{{ __('dashboard.status') }}</th>
                                    <th style="width:130px;">{{ __('dashboard.actions') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($payments as $payment)
                                    <tr>
                                        <td><i class="bi bi-arrows-move"></i></td>

                                        <td>
                                            {{ $payment->paymentMethod->name ?? '-' }}
                                        </td>

                                        <td>
                                            @if ($payment->is_active)
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
                                            @can('payment_method.edit')
                                                <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#editModal{{ $payment->id }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                            @endcan

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            {{ __('dashboard.no_payment_method_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Payment Method Config Modal -->
    <div class="modal fade" id="paymentConfigModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('setup-sidebar.payments.store') }}" method="POST">
                @csrf

                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('dashboard.adding_payment_method') }}
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">

                        <!-- Payment Method -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                {{ __('dashboard.payment_method') }} <span class="text-danger">*</span>
                            </label>

                            <select name="payment_method_id" class="form-select" required>
                                <option value="">{{ __('dashboard.select_payment_method') }}</option>

                                @foreach ($paymentMethods ?? [] as $method)
                                    <option value="{{ $method->id }}">
                                        {{ $method->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">
                                {{ __('dashboard.description') }}
                            </label>

                            <textarea name="description" rows="4" class="form-control" placeholder="{{ __('dashboard.enter_description') }}"></textarea>
                        </div>

                    </div>

                    <!-- Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.cancel') }}
                        </button>

                        <button type="submit" class="btn btn-primary">
                            {{ __('dashboard.save') }}
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    @foreach ($payments as $payment)
        <div class="modal fade" id="editModal{{ $payment->id }}" tabindex="-1">
            <div class="modal-dialog">

                <form action="{{ route('setup-sidebar.payments.update', $payment->id) }}" method="POST">

                    @csrf
                    @method('PUT')

                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ __('dashboard.edit_payment_method') }}
                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <!-- Status -->
                            <div class="form-check form-switch mb-3">
                                <input type="checkbox" name="is_active" class="form-check-input"
                                    {{ $payment->is_active ? 'checked' : '' }}>

                                <label class="form-check-label">
                                    {{ __('dashboard.active_payment_method') }}
                                </label>
                            </div>

                            <!-- Payment Method Name -->
                            <div class="mb-3">
                                <label class="form-label">
                                    {{ __('dashboard.payment_method') }}
                                </label>

                                <input type="text" class="form-control" value="{{ $payment->paymentMethod->name }}"
                                    readonly>
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label class="form-label">
                                    {{ __('dashboard.description') }}
                                </label>

                                <textarea name="description" class="form-control" rows="4">{{ $payment->description }}</textarea>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ __('dashboard.discard') }}
                            </button>

                            <button class="btn btn-primary">
                                {{ __('dashboard.update') }}
                            </button>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    @endforeach
@endsection
