@extends('layouts.app')

@section('title', 'Loyalty Program')
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

    /* Page Header */
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

    /* Table Top Buttons */
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

    .n-button--primary {
        background-color: white;
        color: #333;
        border-color: #dee2e6;
    }

    .n-button--primary:hover {
        background-color: #f8f9fa;
        border-color: #4a90e2;
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

    /* Filter Form */
    .filter-form__container {
        background-color: #343a40;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .filter-form {
        padding: 1.5rem;
    }

    .filter-form--dark label {
        color: #e9ecef;
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.875rem;
    }

    .filter-form--dark .form-control {
        background-color: #495057;
        border: 1px solid #6c757d;
        color: white;
        width: 100%;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }

    .filter-form--dark .form-control::placeholder {
        color: #adb5bd;
    }

    .form__input-msg {
        font-size: 0.75rem;
        margin-top: 0.25rem;
        min-height: 1rem;
        color: #6c757d;
    }

    /* Overlay hidden by default */
    .unit-card .card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        /* semi-transparent overlay */
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
        z-index: 10;
    }

    /* Show overlay on hover */
    .unit-card:hover .card-overlay {
        opacity: 1;
    }

    /* Style buttons */
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
        <div class="page-category">{{ __('dashboard.general_settings') }}</div>
        <div class="page-header">
            <div>
                <h2 class="page-header__title">{{ __('dashboard.loyalty_program_settings') }}</h2>
                <div class="page-header__subtitle">
                    {{ __('dashboard.you_can_set_loyalty_settings_by_which_your_customers_can_be_promoted_to_selected_classifications_based_on_certain_settings') }}
                </div>
            </div>
            <div class="n-table__top-btns">
                <a href="#" class="n-button n-button--green" style="text-decoration:none;" data-bs-toggle="modal"
                    data-bs-target="#loyaltyAutoSettingModal">

                    {{ __('dashboard.loyalty_settings') }}
                </a>
                <button class="n-button n-button--primary">
                    {{ __('dashboard.filter') }}
                </button>
                <div>
                    @can('loyalty_setting.add')
                        <a href="#" class="n-button n-button--green" style="text-decoration:none;" tabindex="0"
                            data-bs-toggle="modal" data-bs-target="#addLoyaltyModal">
                            {{ __('dashboard.loyalty_settings') }}
                        </a>
                    @endcan

                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('setup-sidebar.loyalty_program.index') }}">
            <div class="filter-form__container mb-4">
                <div class="card">
                    <div class="card-body">

                        <div class="row g-3">

                            <!-- Criteria Filter -->
                            <div class="col-lg-4 col-md-4">
                                <label class="form-label">
                                    {{ __('dashboard.criteria') }}
                                </label>

                                <select name="criteria" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>

                                    <option value="total_reservations"
                                        {{ request('criteria') == 'total_reservations' ? 'selected' : '' }}>
                                        {{ __('dashboard.total_reservations') }}
                                    </option>

                                    <option value="total_spent"
                                        {{ request('criteria') == 'total_spent' ? 'selected' : '' }}>
                                        {{ __('dashboard.total_spent') }}
                                    </option>

                                    <option value="total_nights"
                                        {{ request('criteria') == 'total_nights' ? 'selected' : '' }}>
                                        {{ __('dashboard.total_nights') }}
                                    </option>
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div class="col-lg-3 col-md-4">
                                <label class="form-label">
                                    {{ __('dashboard.status') }}
                                </label>

                                <select name="is_active" class="form-select">
                                    <option value="">{{ __('dashboard.all') }}</option>

                                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>
                                        {{ __('dashboard.active') }}
                                    </option>

                                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>
                                        {{ __('dashboard.inactive') }}
                                    </option>
                                </select>
                            </div>

                            <!-- Created By Filter -->
                            <div class="col-lg-3 col-md-4">
                                <label class="form-label">
                                    {{ __('dashboard.created_by') }}
                                </label>

                                <input type="text" name="created_by" value="{{ request('created_by') }}"
                                    class="form-control" placeholder="{{ __('dashboard.created_by') }}">
                            </div>

                            <!-- Buttons -->
                            <div class="col-lg-2 col-md-4 d-flex align-items-end">

                                <button type="submit" class="btn btn-primary me-2">
                                    {{ __('dashboard.search') }}
                                </button>

                                <a href="{{ route('setup-sidebar.loyalty_program.index') }}"
                                    class="btn btn-outline-secondary">
                                    {{ __('dashboard.reset') }}
                                </a>

                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </form>

        <div class="container mt-5">
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('dashboard.critaria') }}</th>
                                <th>{{ __('dashboard.score') }}</th>
                                <th>{{ __('dashboard.upgrade_to_class') }}</th>
                                <th>{{ __('dashboard.conclusion') }}</th>
                                <th style="width:100px;">{{ __('dashboard.status') }}</th>
                                <th>{{ __('dashboard.created_by') }}</th>
                                <th>{{ __('dashboard.created_date') }}</th>
                                <th style="width:150px;">{{ __('dashboard.actions') }}</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($loyaltySettings as $setting)
                                <tr>
                                    <td>
                                        {{ __('dashboard.' . $setting->criteria) }}
                                    </td>
                                    <td>
                                        {{ $setting->threshold_value }}
                                    </td>
                                    <td>
                                        {{ $setting->guestClass->class_name ?? '-' }}
                                    </td>
                                    <td>
                                        {{ ucfirst(str_replace('_', ' ', $setting->criteria)) }}
                                        {{ __('dashboard.upgrade_when_reach') }}
                                        {{ $setting->threshold_value }}
                                    </td>
                                    <td>
                                        @if ($setting->is_active)
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
                                        {{ $setting->created_by ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $setting->created_at->format('Y-m-d h:i A') }}
                                    </td>
                                    <td>
                                        @can('loyalty_setting.edit')
                                            <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#editLoyaltyModal{{ $setting->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan

                                        @can('loyalty_setting.view')
                                            <a href="#" class="btn btn-sm btn-secondary" data-bs-toggle="modal"
                                                data-bs-target="#viewLoyaltyModal{{ $setting->id }}">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @endcan

                                        @can('loyalty_setting.delete')
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteLoyaltyModal{{ $setting->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endcan
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        {{ __('dashboard.no_data_found') }}
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </main>

    <!-- Add Loyalty Modal -->
    <div class="modal fade" id="addLoyaltyModal" tabindex="-1">

        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <form method="POST" action="{{ route('setup-sidebar.loyalty_program.store') }}">

                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('dashboard.add_loyalty_setting') }}
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="row g-3">

                            <!-- Criteria -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    {{ __('dashboard.criteria') }}
                                </label>

                                <select name="criteria" class="form-select" required>
                                    <option value="">
                                        {{ __('dashboard.select') }}
                                    </option>

                                    <option value="total_reservations">
                                        {{ __('dashboard.total_reservations') }}
                                    </option>

                                    <option value="total_spent">
                                        {{ __('dashboard.total_spent') }}
                                    </option>

                                    <option value="total_nights">
                                        {{ __('dashboard.total_nights') }}
                                    </option>

                                </select>
                            </div>

                            <!-- Threshold Score -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    {{ __('dashboard.score') }}
                                </label>

                                <input type="number" name="threshold_value" class="form-control" required
                                    placeholder="{{ __('dashboard.enter_score') }}">
                            </div>

                            <!-- Upgrade Class -->
                            <div class="col-md-12">
                                <label class="form-label">
                                    {{ __('dashboard.upgrade_to_class') }}
                                </label>

                                <select name="upgrade_to_class_id" class="form-select" required>

                                    <option value="">
                                        {{ __('dashboard.select') }}
                                    </option>

                                    @foreach (\App\Models\GuestClass::all() as $class)
                                        <option value="{{ $class->id }}">
                                            {{ $class->class_name ?? 'Class' }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            {{-- <!-- Status -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    {{ __('dashboard.status') }}
                                </label>

                                <select name="is_active" class="form-select">
                                    <option value="1">
                                        {{ __('dashboard.active') }}
                                    </option>

                                    <option value="0">
                                        {{ __('dashboard.inactive') }}
                                    </option>
                                </select>
                            </div> --}}

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

    <div class="modal fade" id="loyaltyAutoSettingModal">

        <div class="modal-dialog">
            <div class="modal-content">

                <form method="POST" action="{{ route('setup-sidebar.loyalty_program.autoUpgrade') }}">

                    @csrf

                    <div class="modal-header">
                        <h5>
                            {{ __('dashboard.loyalty_settings') }}
                        </h5>
                    </div>

                    <div class="modal-body">

                        <div class="form-check form-switch">

                            <input type="hidden" name="auto_loyalty_upgrade" value="0">

                            <input class="form-check-input" type="checkbox" name="auto_loyalty_upgrade" value="1"
                                {{ optional(\App\Models\LoyaltyAutoSetting::first())->auto_loyalty_upgrade ? 'checked' : '' }}>

                            <label class="form-check-label">
                                {{ __('dashboard.automatically_upgrade_guests_classes') }}
                            </label>

                        </div>

                        <p class="text-muted small mt-2">
                            {{ __('dashboard.loyalty_auto_description') }}
                        </p>

                    </div>

                    <div class="modal-footer">

                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.discard') }}
                        </button>

                        <button class="btn btn-primary">
                            {{ __('dashboard.save') }}
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>

    @foreach ($loyaltySettings as $setting)
        <!-- Edit Modal -->
        <div class="modal fade" id="editLoyaltyModal{{ $setting->id }}" tabindex="-1">

            <div class="modal-dialog modal-lg">

                <div class="modal-content">

                    <form method="POST" action="{{ route('setup-sidebar.loyalty_program.update', $setting->id) }}">

                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ __('dashboard.edit_loyalty_setting') }}
                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <div class="row g-3">

                                <!-- Criteria -->
                                <div class="col-md-6">
                                    <label class="form-label">
                                        {{ __('dashboard.criteria') }}
                                    </label>

                                    <select name="criteria" class="form-select">

                                        <option value="total_reservations"
                                            {{ $setting->criteria == 'total_reservations' ? 'selected' : '' }}>
                                            {{ __('dashboard.total_reservations') }}
                                        </option>

                                        <option value="total_spent"
                                            {{ $setting->criteria == 'total_spent' ? 'selected' : '' }}>
                                            {{ __('dashboard.total_spent') }}
                                        </option>

                                        <option value="total_nights"
                                            {{ $setting->criteria == 'total_nights' ? 'selected' : '' }}>
                                            {{ __('dashboard.total_nights') }}
                                        </option>

                                    </select>
                                </div>

                                <!-- Score -->
                                <div class="col-md-6">
                                    <label class="form-label">
                                        {{ __('dashboard.score') }}
                                    </label>

                                    <input type="number" name="threshold_value" class="form-control"
                                        value="{{ $setting->threshold_value }}">
                                </div>

                                <!-- Upgrade Class -->
                                <div class="col-md-6">
                                    <label class="form-label">
                                        {{ __('dashboard.upgrade_to_class') }}
                                    </label>

                                    <select name="upgrade_to_class_id" class="form-select">

                                        @foreach (\App\Models\GuestClass::all() as $class)
                                            <option value="{{ $class->id }}"
                                                {{ $setting->upgrade_to_class_id == $class->id ? 'selected' : '' }}>

                                                {{ $class->class_name ?? '-' }}

                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="col-md-6">

                                    <label class="form-label d-block">
                                        {{ __('dashboard.status') }}
                                    </label>

                                    <div class="form-check form-switch">

                                        <input type="hidden" name="is_active" value="0">

                                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                            {{ $setting->is_active ? 'checked' : '' }}>

                                        <label class="form-check-label">
                                            {{ $setting->is_active ? __('dashboard.active') : __('dashboard.inactive') }}
                                        </label>

                                    </div>

                                </div>

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

        <!-- View Modal -->
        <div class="modal fade" id="viewLoyaltyModal{{ $setting->id }}" tabindex="-1">

            <div class="modal-dialog modal-lg">

                <div class="modal-content">

                    <form method="POST" action="#">

                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ __('dashboard.view_loyalty_setting') }}
                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <div class="row g-3">

                                <!-- Criteria -->
                                <div class="col-md-6">
                                    <label class="form-label">
                                        {{ __('dashboard.criteria') }}
                                    </label>

                                    <select name="criteria" class="form-select" disabled>

                                        <option value="total_reservations"
                                            {{ $setting->criteria == 'total_reservations' ? 'selected' : '' }}>
                                            {{ __('dashboard.total_reservations') }}
                                        </option>

                                        <option value="total_spent"
                                            {{ $setting->criteria == 'total_spent' ? 'selected' : '' }}>
                                            {{ __('dashboard.total_spent') }}
                                        </option>

                                        <option value="total_nights"
                                            {{ $setting->criteria == 'total_nights' ? 'selected' : '' }}>
                                            {{ __('dashboard.total_nights') }}
                                        </option>

                                    </select>
                                </div>

                                <!-- Score -->
                                <div class="col-md-6">
                                    <label class="form-label">
                                        {{ __('dashboard.score') }}
                                    </label>

                                    <input type="number" name="threshold_value" class="form-control"
                                        value="{{ $setting->threshold_value }}" disabled>
                                </div>

                                <!-- Upgrade Class -->
                                <div class="col-md-6">
                                    <label class="form-label">
                                        {{ __('dashboard.upgrade_to_class') }}
                                    </label>

                                    <select name="upgrade_to_class_id" class="form-select" disabled>

                                        @foreach (\App\Models\GuestClass::all() as $class)
                                            <option value="{{ $class->id }}"
                                                {{ $setting->upgrade_to_class_id == $class->id ? 'selected' : '' }}>

                                                {{ $class->class_name ?? '-' }}

                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="col-md-6">

                                    <label class="form-label d-block">
                                        {{ __('dashboard.status') }}
                                    </label>

                                    <div class="form-check form-switch">

                                        <input type="hidden" name="is_active" value="0">

                                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                            {{ $setting->is_active ? 'checked' : '' }} disabled>

                                        <label class="form-check-label">
                                            {{ $setting->is_active ? __('dashboard.active') : __('dashboard.inactive') }}
                                        </label>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="modal-footer">

                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ __('dashboard.discard') }}
                            </button>

                        </div>

                    </form>

                </div>

            </div>
        </div>

        <div class="modal fade" id="deleteLoyaltyModal{{ $setting->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('dashboard.delete_loyalty_setting') }} – {{ __('dashboard.' . $setting->criteria) }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p>{{ __('dashboard.delete_loyalty_setting_confirmation') }}</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('dashboard.cancel') }}
                        </button>
                        <form action="{{ route('setup-sidebar.loyalty_program.delete', $setting->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                {{ __('dashboard.delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
@push('scripts')
    <script>
        const toggleBtn = document.querySelector('.n-button.n-button--primary');
        const filterContainer = document.querySelector('.filter-form__container');

        filterContainer.style.display = 'none';

        toggleBtn.addEventListener('click', function() {
            if (filterContainer.style.display === 'none') {
                filterContainer.style.display = 'block';
            } else {
                filterContainer.style.display = 'none';
            }
        });
    </script>
@endpush
