@extends('layout.master')
@section('title', 'Dashboard | Subscription')
@section('main')
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center">{{ __('dashboard.all_subscriptions') }}</h1>

        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>{{ __('dashboard.subscriptions') }}</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addSubscriptionModal">
                                    {{ __('dashboard.add_subscription') }}
                                </button>
                            </div>
                            <div class="mb-3 d-flex justify-content-center">
                                <button class="btn btn-primary filter-btn mx-1"
                                    data-status="">{{ __('dashboard.all') }}</button>
                                <button class="btn btn-success filter-btn mx-1"
                                    data-status="active">{{ __('dashboard.active') }}</button>
                                <button class="btn btn-danger filter-btn mx-1"
                                    data-status="expired">{{ __('dashboard.expired') }}</button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-striped table-hover" id="tableExport">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('dashboard.platform_name') }}</th>
                                                <th>{{ __('dashboard.branch') }}</th>
                                                <th>{{ __('dashboard.start_date') }}</th>
                                                <th>{{ __('dashboard.end_date') }}</th>
                                                <th>{{ __('dashboard.contract_amount') }}</th>
                                                <th>{{ __('dashboard.commission') }} %</th>
                                                <th>{{ __('dashboard.status') }}</th>
                                                <th>{{ __('dashboard.notes') }}</th>
                                                <th>{{ __('dashboard.created_at') }}</th>
                                                <th>{{ __('dashboard.days_left') }}</th>
                                                <th>{{ __('dashboard.action') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @forelse ($subscriptions as $subscription)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>

                                                    <!-- Platform -->
                                                    <td>{{ $subscription->platform?->name ?? '-' }}</td>

                                                    <!-- Branch -->
                                                    <td>{{ $subscription->branch?->name ?? '-' }}</td>

                                                    <!-- Start Date -->
                                                    <td>{{ $subscription->subscription_start_date->format('Y-m-d') }}</td>

                                                    <!-- End Date -->
                                                    <td>{{ $subscription->subscription_end_date->format('Y-m-d') }}</td>

                                                    <!-- Contract Amount -->
                                                    <td>{{ number_format($subscription->contract_amount, 2) }}</td>

                                                    <!-- Commission Percentage -->
                                                    <td>{{ $subscription->commission_percentage }}%</td>

                                                    <!-- Status -->
                                                    <td>
                                                        <span
                                                            class="badge badge-{{ $subscription->status === 'active' ? 'success' : ($subscription->status === 'expired' ? 'danger' : 'warning') }}">
                                                            {{ ucfirst($subscription->status) }}
                                                        </span>
                                                    </td>

                                                    <!-- Notes -->
                                                    <td>{{ $subscription->notes ?? '-' }}</td>

                                                    <!-- Created At -->
                                                    <td>{{ $subscription->created_at->format('Y-m-d') }}</td>
                                                    <!-- Days Left -->
                                                    <td>
                                                        @php
                                                            $today = \Carbon\Carbon::today();
                                                            $end = $subscription->subscription_end_date;
                                                            $daysLeft = $today->diffInDays($end, false); // false: negative if past
                                                        @endphp

                                                        @if ($daysLeft > 0)
                                                            {{ $daysLeft }} {{ __('dashboard.days') }}
                                                        @elseif($daysLeft === 0)
                                                            {{ __('dashboard.expiring_today') }}
                                                        @else
                                                            {{ __('dashboard.expired') }} {{ abs($daysLeft) }}
                                                            {{ __('dashboard.days_ago') }}
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <!-- Edit -->
                                                        <a href="#" class="text-secondary" data-toggle="modal"
                                                            data-target="#editSubscriptionModal_{{ $subscription->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <!-- Delete -->
                                                        <a href="#" class="text-danger" data-toggle="modal"
                                                            data-target="#deleteSubscriptionModal_{{ $subscription->id }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center text-muted">
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

        <!-- Add Platform Subscription Modal -->
        <div class="modal fade" id="addSubscriptionModal" tabindex="-1" role="dialog"
            aria-labelledby="addSubscriptionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header text-dark">
                        <h5 class="modal-title" id="addSubscriptionModalLabel">
                            {{ __('dashboard.add_platform_subscription') }}
                        </h5>
                        <button type="button" class="close text-dark" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form action="{{ route('platform-subscriptions.store') }}" method="POST">
                        @csrf

                        <div class="modal-body">
                            <div class="row">

                                <!-- Third Party Platform -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{ __('dashboard.third_party_platform') }} <span
                                                class="text-danger">*</span></label>
                                        <select name="third_party_platform_id" class="form-control">
                                            <option value="">{{ __('dashboard.select_platform') }}</option>
                                            @foreach ($platforms as $platform)
                                                <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('third_party_platform_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Branch (Optional) -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{ __('dashboard.branch') }}</label>
                                        <select name="branch_id" class="form-control">
                                            <option value="">{{ __('dashboard.select_branch') }}
                                                ({{ __('dashboard.optional') }})</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Subscription Start Date -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('dashboard.subscription_start_date') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="subscription_start_date" class="form-control">
                                        @error('subscription_start_date')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Subscription End Date -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('dashboard.subscription_end_date') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="subscription_end_date" class="form-control">
                                        @error('subscription_end_date')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Contract Amount -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('dashboard.contract_amount') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="contract_amount" step="0.01" class="form-control"
                                            placeholder="0.00">
                                        @error('contract_amount')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Commission Percentage -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('dashboard.commission_percentage') }} %</label>
                                        <input type="number" name="commission_percentage" step="0.01"
                                            class="form-control" value="0">
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{ __('dashboard.status') }} <span class="text-danger">*</span></label>
                                        <select name="status" class="form-control" required>
                                            <option value="pending">{{ __('dashboard.pending') }}</option>
                                            <option value="active">{{ __('dashboard.active') }}</option>
                                            <option value="expired">{{ __('dashboard.expired') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{ __('dashboard.notes') }}</label>
                                        <textarea name="notes" class="form-control" rows="3" placeholder="{{ __('dashboard.description') }}"></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Modal Footer -->
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


        @foreach ($subscriptions as $subscription)
            <!-- Edit Platform Subscription Modal for Subscription ID {{ $subscription->id }} -->
            <div class="modal fade" id="editSubscriptionModal_{{ $subscription->id }}" tabindex="-1"
                aria-labelledby="editSubscriptionModalLabel_{{ $subscription->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">

                        <!-- Modal Header -->
                        <div class="modal-header  text-dark">
                            <h5 class="modal-title" id="editSubscriptionModalLabel_{{ $subscription->id }}">
                                {{ __('dashboard.edit_platform_subscription') }}
                            </h5>
                            <button type="button" class="close text-dark" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <form action="{{ route('platform-subscriptions.update', $subscription->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="modal-body">
                                <div class="row">

                                    <!-- Third Party Platform -->
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{ __('dashboard.third_party_platform') }} <span
                                                    class="text-danger">*</span></label>
                                            <select name="third_party_platform_id" class="form-control" required>
                                                <option value="">{{ __('dashboard.select_platform') }}</option>
                                                @foreach ($platforms as $platform)
                                                    <option value="{{ $platform->id }}"
                                                        {{ $subscription->third_party_platform_id == $platform->id ? 'selected' : '' }}>
                                                        {{ $platform->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Branch (Optional) -->
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{ __('dashboard.branch') }}</label>
                                            <select name="branch_id" class="form-control">
                                                <option value="">{{ __('dashboard.select_branch') }}
                                                    ({{ __('dashboard.optional') }})
                                                </option>
                                                @foreach ($branches as $branch)
                                                    <option value="{{ $branch->id }}"
                                                        {{ $subscription->branch_id == $branch->id ? 'selected' : '' }}>
                                                        {{ $branch->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Subscription Start Date -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('dashboard.subscription_start_date') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="subscription_start_date" class="form-control"
                                                value="{{ $subscription->subscription_start_date->format('Y-m-d') }}"
                                                required>
                                        </div>
                                    </div>

                                    <!-- Subscription End Date -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('dashboard.subscription_end_date') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="subscription_end_date" class="form-control"
                                                value="{{ $subscription->subscription_end_date->format('Y-m-d') }}"
                                                required>
                                        </div>
                                    </div>

                                    <!-- Contract Amount -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('dashboard.contract_amount') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" name="contract_amount" step="0.01"
                                                class="form-control" value="{{ $subscription->contract_amount }}"
                                                required>
                                        </div>
                                    </div>

                                    <!-- Commission Percentage -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('dashboard.commission_percentage') }} %</label>
                                            <input type="number" name="commission_percentage" step="0.01"
                                                class="form-control" value="{{ $subscription->commission_percentage }}">
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{ __('dashboard.status') }} <span class="text-danger">*</span></label>
                                            <select name="status" class="form-control" required>
                                                <option value="pending"
                                                    {{ $subscription->status == 'pending' ? 'selected' : '' }}>
                                                    {{ __('dashboard.pending') }}
                                                </option>
                                                <option value="active"
                                                    {{ $subscription->status == 'active' ? 'selected' : '' }}>
                                                    {{ __('dashboard.active') }}
                                                </option>
                                                <option value="expired"
                                                    {{ $subscription->status == 'expired' ? 'selected' : '' }}>
                                                    {{ __('dashboard.expired') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Notes -->
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{ __('dashboard.notes') }}</label>
                                            <textarea name="notes" class="form-control" rows="3">{{ $subscription->notes }}</textarea>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                                <button type="submit" class="btn btn-primary">{{ __('dashboard.update') }}</button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        @endforeach

        @foreach ($subscriptions as $subscription)
            <div class="modal fade" id="deleteSubscriptionModal_{{ $subscription->id }}" tabindex="-1"
                aria-labelledby="deleteSubscriptionModalLabel_{{ $subscription->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deleteSubscriptionModalLabel_{{ $subscription->id }}">
                                {{ __('dashboard.delete_platform_subscription') }}
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Form -->
                        <form method="POST" action="{{ route('platform-subscriptions.destroy', $subscription->id) }}">
                            @csrf
                            @method('DELETE')

                            <div class="modal-body text-center">
                                <p>{{ __('dashboard.confirm_delete_modal') }}
                                    <strong>{{ $subscription->platform?->name ?? 'N/A' }}</strong>?
                                </p>
                            </div>

                            <div class="modal-footer justify-content-center">
                                <button type="submit" class="btn btn-danger">{{ __('dashboard.yes_delete') }}</button>
                                <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
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
                var myModal = new bootstrap.Modal(document.getElementById('addSubscriptionModal'));
                myModal.show();
            });
        </script>
    @endif
    <script>
        $(document).ready(function() {

            // Helper function to format date as YYYY-MM-DD
            function formatDate(isoDate) {
                if (!isoDate) return '-';
                var date = new Date(isoDate);
                var year = date.getFullYear();
                var month = ('0' + (date.getMonth() + 1)).slice(-2);
                var day = ('0' + date.getDate()).slice(-2);
                return year + '-' + month + '-' + day;
            }

            $('.filter-btn').click(function() {
                var status = $(this).data('status');

                $.ajax({
                    url: "{{ route('platform-subscriptions.filter') }}",
                    type: "GET",
                    data: {
                        status: status
                    },
                    success: function(subscriptions) {
                        var tbody = '';

                        if (subscriptions.length > 0) {
                            $.each(subscriptions, function(index, sub) {
                                // Calculate days left
                                var today = new Date();
                                var endDate = new Date(sub.subscription_end_date);
                                var diffTime = endDate - today;
                                var daysLeft = Math.ceil(diffTime / (1000 * 60 * 60 *
                                    24));

                                var daysText = '';
                                if (daysLeft < 0) {
                                    daysText = 'Expired ' + Math.abs(daysLeft) +
                                        ' days ago';
                                } else if (daysLeft === 0) {
                                    daysText = 'Expiring today';
                                } else {
                                    daysText = daysLeft + ' days left';
                                }

                                // Action buttons (Edit & Delete)
                                var actionBtns =
                                    '<a href="#" class="text-secondary mr-2" data-toggle="modal" data-target="#editSubscriptionModal_' +
                                    sub.id + '"><i class="fas fa-edit"></i></a>' +
                                    '<a href="#" class="text-danger" data-toggle="modal" data-target="#deleteSubscriptionModal_' +
                                    sub.id + '"><i class="fas fa-trash-alt"></i></a>';

                                // Build table row
                                tbody += '<tr>' +
                                    '<td>' + (index + 1) + '</td>' +
                                    '<td>' + (sub.platform ? sub.platform.name : '-') +
                                    '</td>' +
                                    '<td>' + (sub.branch ? sub.branch.name : '-') +
                                    '</td>' +
                                    '<td>' + formatDate(sub.subscription_start_date) +
                                    '</td>' +
                                    '<td>' + formatDate(sub.subscription_end_date) +
                                    '</td>' +
                                    '<td>' + parseFloat(sub.contract_amount).toFixed(
                                        2) + '</td>' +
                                    '<td>' + sub.commission_percentage + '%</td>' +
                                    '<td>' + (sub.status.charAt(0).toUpperCase() + sub
                                        .status.slice(1)) + '</td>' +
                                    '<td>' + (sub.notes ? sub.notes : '-') + '</td>' +
                                    '<td>' + formatDate(sub.created_at) + '</td>' +
                                    '<td>' + daysText + '</td>' +
                                    '<td>' + actionBtns + '</td>' +
                                    '</tr>';
                            });
                        } else {
                            tbody =
                                '<tr><td colspan="12" class="text-center text-muted">No subscriptions found</td></tr>';
                        }

                        $('#tableExport tbody').html(tbody);
                    }
                });
            });

        });
    </script>

@endsection
