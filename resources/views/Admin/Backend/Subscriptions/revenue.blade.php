@extends('layout.master')
@section('title', 'Dashboard | Revenue')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.revenues') }}</h1>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.revenues') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addPlatformRevenueModal">
                                    {{ __('dashboard.add_revenue') }}
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover" id="tableExport">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.agent') }}</th>
                                                <th>{{ __('dashboard.quotation_number') }}</th>
                                                <th>{{ __('dashboard.branch') }}</th>
                                                <th>{{ __('dashboard.amount_collected') }}</th>
                                                <th>{{ __('dashboard.commission') }} %</th>
                                                <th>{{ __('dashboard.commission_amount') }}</th>

                                                <th>{{ __('dashboard.created_at') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @forelse ($revenues as $revenue)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>

                                                    {{-- Agent / Platform --}}
                                                    <td>
                                                        {{ $revenue->subscription->platform->name ?? '-' }}
                                                    </td>

                                                    {{-- Subscription Reference --}}
                                                    <td>
                                                        SUB-{{ $revenue->subscription->id }}
                                                    </td>

                                                    {{-- Branch --}}
                                                    <td>
                                                        {{ $revenue->subscription->branch->name ?? '-' }}
                                                    </td>
                                                    <td>
                                                        {{ $revenue->amount_collected ?? '-' }}
                                                    </td>

                                                    {{-- Commission Percentage --}}
                                                    <td>
                                                        {{ $revenue->subscription->commission_percentage }}%
                                                    </td>

                                                    {{-- Commission Amount --}}
                                                    <td>
                                                        {{ number_format($revenue->commission_amount, 2) }}
                                                    </td>



                                                    {{-- Created At --}}
                                                    <td>
                                                        {{ $revenue->created_at->format('Y-m-d') }}
                                                    </td>
                                                    <td>
                                                        <!-- Edit -->
                                                        <a href="#" class="text-secondary" data-toggle="modal"
                                                            data-target="#editRevenueModal{{ $revenue->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <!-- Delete -->
                                                        <a href="#" class="text-danger" data-toggle="modal"
                                                            data-target="#deleteRevenueModal_{{ $revenue->id }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted">
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>

                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Add Platform Revenue Modal -->
        <div class="modal fade" id="addPlatformRevenueModal" tabindex="-1" role="dialog"
            aria-labelledby="addPlatformRevenueModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header text-dark">
                        <h5 class="modal-title" id="addPlatformRevenueModalLabel">
                            {{ __('dashboard.add_platform_revenue') }}
                        </h5>
                        <button type="button" class="close text-dark" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <!-- Form -->
                    <form action="{{ route('platform-revenues.store') }}" method="POST">
                        @csrf

                        <div class="modal-body">
                            <div class="row">

                                <!-- Subscription -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{ __('dashboard.subscription') }} <span
                                                class="text-danger">*</span></label>
                                        <select name="subscription_id" class="form-control" required>
                                            <option value="">{{ __('dashboard.select_subscription') }}</option>
                                            @foreach ($subscriptions as $subscription)
                                                <option value="{{ $subscription->id }}">
                                                    {{ $subscription->platform?->name ?? '-' }}
                                                    ({{ $subscription->branch?->name ?? '-' }})
                                                    | {{ __('dashboard.ends') }}:
                                                    {{ $subscription->subscription_end_date->format('Y-m-d') }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('subscription_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Amount Collected -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('dashboard.amount_collected') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" name="amount_collected" class="form-control"
                                            placeholder="0.00" required>
                                        @error('amount_collected')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Payment Date -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('dashboard.payment_date') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="payment_date" class="form-control" required>
                                        @error('payment_date')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                {{ __('dashboard.cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary">
                                {{ __('dashboard.save') }}
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        @foreach ($revenues as $revenue)
            <div class="modal fade" id="editRevenueModal{{ $revenue->id }}" tabindex="-1" role="dialog"
                aria-labelledby="editRevenueModalLabel{{ $revenue->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header text-dark">
                            <h5 class="modal-title" id="editRevenueModalLabel{{ $revenue->id }}">
                                {{ __('dashboard.edit_platform_revenue') }}
                            </h5>
                            <button type="button" class="close text-dark" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Form -->
                        <form action="{{ route('platform-revenues.update', $revenue->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="modal-body">
                                <div class="row">

                                    <!-- Subscription -->
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{ __('dashboard.subscription') }} <span
                                                    class="text-danger">*</span></label>
                                            <select name="subscription_id" class="form-control" required>
                                                @foreach ($subscriptions as $subscription)
                                                    <option value="{{ $subscription->id }}"
                                                        {{ $subscription->id == $revenue->subscription_id ? 'selected' : '' }}>
                                                        {{ $subscription->platform?->name ?? '-' }}
                                                        ({{ $subscription->branch?->name ?? '-' }})
                                                        | {{ __('dashboard.ends') }}:
                                                        {{ $subscription->subscription_end_date->format('Y-m-d') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Amount Collected -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('dashboard.amount_collected') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" name="amount_collected"
                                                class="form-control" value="{{ $revenue->amount_collected }}" required>
                                        </div>
                                    </div>

                                    <!-- Payment Date -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('dashboard.payment_date') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="payment_date" class="form-control"
                                                value="{{ \Carbon\Carbon::parse($revenue->payment_date)->format('Y-m-d') }}"
                                                required>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    {{ __('dashboard.cancel') }}
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

        @foreach ($revenues as $revenue)
            <div class="modal fade" id="deleteRevenueModal_{{ $revenue->id }}" tabindex="-1"
                aria-labelledby="deleteRevenueModalLabel_{{ $revenue->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deleteRevenueModalLabel_{{ $revenue->id }}">
                                {{ __('dashboard.delete_platform_revenue') }}
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Form -->
                        <form method="POST" action="{{ route('platform-revenues.destroy', $revenue->id) }}">
                            @csrf
                            @method('DELETE')

                            <div class="modal-body text-center">
                                <p>
                                    {{ __('dashboard.confirm_delete_modal') }}
                                    <br>
                                    <strong>
                                        {{ $revenue->subscription?->platform?->name ?? 'N/A' }}
                                    </strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ __('dashboard.amount') }}:
                                        {{ number_format($revenue->amount_collected, 2) }}
                                    </small>
                                </p>
                            </div>

                            <div class="modal-footer justify-content-center">
                                <button type="submit" class="btn btn-danger">
                                    {{ __('dashboard.yes_delete') }}
                                </button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    {{ __('dashboard.cancel') }}
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        @endforeach



    </div>
    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('addPlatformRevenueModal'));
                myModal.show();
            });
        </script>
    @endif
@endsection
