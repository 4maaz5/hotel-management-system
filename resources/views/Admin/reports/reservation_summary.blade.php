@extends('admin.reports.layouts.report')

@section('title', __('dashboard.reservation_summary_report'))

@section('report_content')
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.summary') }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-2">
                    <div class="border rounded p-3 text-center">
                        <small class="text-muted">{{ __('dashboard.total') }}</small>
                        <h4 class="text-primary mb-0">{{ $summary['total'] ?? 0 }}</h4>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="border rounded p-3 text-center">
                        <small class="text-muted">{{ __('dashboard.checked_in') }}</small>
                        <h4 class="text-success mb-0">{{ $summary['checked_in'] ?? 0 }}</h4>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="border rounded p-3 text-center">
                        <small class="text-muted">{{ __('dashboard.checked_out') }}</small>
                        <h4 class="text-info mb-0">{{ $summary['checked_out'] ?? 0 }}</h4>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="border rounded p-3 text-center">
                        <small class="text-muted">{{ __('dashboard.pending') }}</small>
                        <h4 class="text-warning mb-0">{{ $summary['pending'] ?? 0 }}</h4>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="border rounded p-3 text-center">
                        <small class="text-muted">{{ __('dashboard.cancelled') }}</small>
                        <h4 class="text-danger mb-0">{{ $summary['cancelled'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.reservations') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('dashboard.reservation_no') }}</th>
                            <th>{{ __('dashboard.guest') }}</th>
                            <th>{{ __('dashboard.room') }}</th>
                            <th>{{ __('dashboard.check_in') }}</th>
                            <th>{{ __('dashboard.check_out') }}</th>
                            <th>{{ __('dashboard.status') }}</th>
                            <th class="text-end">{{ __('dashboard.total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reservations ?? [] as $reservation)
                            <tr>
                                <td>{{ $reservation->reservation_number }}</td>
                                <td>{{ $reservation->guest->first_name ?? '-' }} {{ $reservation->guest->last_name ?? '' }}</td>
                                <td>{{ $reservation->unit->unit_number ?? '-' }}</td>
                                <td>{{ $reservation->check_in_date->format('Y-m-d') }}</td>
                                <td>{{ $reservation->check_out_date->format('Y-m-d') }}</td>
                                <td>
                                    <span class="badge bg-{{ $reservation->status === 'checked_in' ? 'success' : ($reservation->status === 'checked_out' ? 'info' : ($reservation->status === 'cancelled' ? 'danger' : 'warning')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $reservation->status)) }}
                                    </span>
                                </td>
                                <td class="text-end">SAR {{ number_format($reservation->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">{{ __('dashboard.no_records_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('print_content')
<div class="english-text">
    <h4 class="text-center mb-3">{{ __('dashboard.reservation_summary_report') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm mb-3">
        <tr>
            <td>{{ __('dashboard.total') }}</td>
            <td class="text-center">{{ $summary['total'] ?? 0 }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.checked_in') }}</td>
            <td class="text-center">{{ $summary['checked_in'] ?? 0 }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.checked_out') }}</td>
            <td class="text-center">{{ $summary['checked_out'] ?? 0 }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.pending') }}</td>
            <td class="text-center">{{ $summary['pending'] ?? 0 }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.cancelled') }}</td>
            <td class="text-center">{{ $summary['cancelled'] ?? 0 }}</td>
        </tr>
    </table>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.reservation_no') }}</th>
                <th>{{ __('dashboard.guest') }}</th>
                <th>{{ __('dashboard.room') }}</th>
                <th>{{ __('dashboard.check_in') }}</th>
                <th>{{ __('dashboard.check_out') }}</th>
                <th>{{ __('dashboard.status') }}</th>
                <th class="text-end">{{ __('dashboard.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reservations ?? [] as $reservation)
                <tr>
                    <td>{{ $reservation->reservation_number }}</td>
                    <td>{{ $reservation->guest->first_name ?? '-' }} {{ $reservation->guest->last_name ?? '' }}</td>
                    <td>{{ $reservation->unit->unit_number ?? '-' }}</td>
                    <td>{{ $reservation->check_in_date->format('Y-m-d') }}</td>
                    <td>{{ $reservation->check_out_date->format('Y-m-d') }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</td>
                    <td class="text-end">SAR {{ number_format($reservation->total_amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center mb-3">{{ __('dashboard.reservation_summary_report_ar') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm mb-3">
        <tr>
            <td>{{ __('dashboard.total_ar') }}</td>
            <td class="text-center">{{ $summary['total'] ?? 0 }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.checked_in_ar') }}</td>
            <td class="text-center">{{ $summary['checked_in'] ?? 0 }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.checked_out_ar') }}</td>
            <td class="text-center">{{ $summary['checked_out'] ?? 0 }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.pending_ar') }}</td>
            <td class="text-center">{{ $summary['pending'] ?? 0 }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.cancelled_ar') }}</td>
            <td class="text-center">{{ $summary['cancelled'] ?? 0 }}</td>
        </tr>
    </table>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.reservation_no') }}</th>
                <th>{{ __('dashboard.guest_ar') }}</th>
                <th>{{ __('dashboard.room_ar') }}</th>
                <th>{{ __('dashboard.check_in') }}</th>
                <th>{{ __('dashboard.check_out') }}</th>
                <th>{{ __('dashboard.status_ar') }}</th>
                <th class="text-end">{{ __('dashboard.total_ar') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reservations ?? [] as $reservation)
                <tr>
                    <td>{{ $reservation->reservation_number }}</td>
                    <td>{{ $reservation->guest->first_name ?? '-' }} {{ $reservation->guest->last_name ?? '' }}</td>
                    <td>{{ $reservation->unit->unit_number ?? '-' }}</td>
                    <td>{{ $reservation->check_in_date->format('Y-m-d') }}</td>
                    <td>{{ $reservation->check_out_date->format('Y-m-d') }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</td>
                    <td class="text-end">ر.س {{ number_format($reservation->total_amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
