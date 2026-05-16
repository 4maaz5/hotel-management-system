@extends('admin.reports.layouts.report')

@section('title', __('dashboard.reservation_revenue_reports'))

@section('report_content')
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.summary') }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="border rounded p-3 text-center">
                        <small class="text-muted">{{ __('dashboard.total_revenue') }}</small>
                        <h4 class="text-primary mb-0">SAR {{ number_format($totalRevenue ?? 0, 2) }}</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 text-center">
                        <small class="text-muted">{{ __('dashboard.total_paid') }}</small>
                        <h4 class="text-success mb-0">SAR {{ number_format($totalPaid ?? 0, 2) }}</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 text-center">
                        <small class="text-muted">{{ __('dashboard.outstanding') }}</small>
                        <h4 class="{{ ($totalOutstanding ?? 0) > 0 ? 'text-danger' : 'text-success' }} mb-0">SAR {{ number_format($totalOutstanding ?? 0, 2) }}</h4>
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
                            <th class="text-end">{{ __('dashboard.total') }}</th>
                            <th class="text-end">{{ __('dashboard.paid') }}</th>
                            <th class="text-end">{{ __('dashboard.balance') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reservations ?? [] as $reservation)
                            <tr>
                                <td>{{ $reservation->reservation_number }}</td>
                                <td>{{ $reservation->guest->first_name ?? '-' }} {{ $reservation->guest->last_name ?? '' }}</td>
                                <td>{{ $reservation->unit->unit_number ?? '-' }}</td>
                                <td class="text-end">SAR {{ number_format($reservation->grand_total, 2) }}</td>
                                <td class="text-end text-success">SAR {{ number_format($reservation->paid_amount ?? 0, 2) }}</td>
                                <td class="text-end {{ ($reservation->balance ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                                    SAR {{ number_format($reservation->balance ?? 0, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">{{ __('dashboard.no_records_found') }}</td>
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
    <h4 class="text-center mb-3">{{ __('dashboard.reservation_revenue_report_ar') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm mb-3">
        <tr>
            <td>{{ __('dashboard.total_revenue') }}</td>
            <td class="text-end">SAR {{ number_format($totalRevenue ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.total_paid') }}</td>
            <td class="text-end">SAR {{ number_format($totalPaid ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.outstanding') }}</td>
            <td class="text-end">SAR {{ number_format($totalOutstanding ?? 0, 2) }}</td>
        </tr>
    </table>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.reservation_no') }}</th>
                <th>{{ __('dashboard.guest') }}</th>
                <th>{{ __('dashboard.room') }}</th>
                <th class="text-end">{{ __('dashboard.total') }}</th>
                <th class="text-end">{{ __('dashboard.paid') }}</th>
                <th class="text-end">{{ __('dashboard.balance') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reservations ?? [] as $reservation)
                <tr>
                    <td>{{ $reservation->reservation_number }}</td>
                    <td>{{ $reservation->guest->first_name ?? '-' }} {{ $reservation->guest->last_name ?? '' }}</td>
                    <td>{{ $reservation->unit->unit_number ?? '-' }}</td>
                    <td class="text-end">SAR {{ number_format($reservation->grand_total, 2) }}</td>
                    <td class="text-end">SAR {{ number_format($reservation->paid_amount ?? 0, 2) }}</td>
                    <td class="text-end">SAR {{ number_format($reservation->balance ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center mb-3">{{ __('dashboard.reservation_revenue_report_ar') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm mb-3">
        <tr>
            <td>{{ __('dashboard.total_revenue') }}</td>
            <td class="text-end">ر.س {{ number_format($totalRevenue ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.total_paid') }}</td>
            <td class="text-end">ر.س {{ number_format($totalPaid ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.outstanding') }}</td>
            <td class="text-end">ر.س {{ number_format($totalOutstanding ?? 0, 2) }}</td>
        </tr>
    </table>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.reservation_no') }}</th>
                <th>{{ __('dashboard.guest_ar') }}</th>
                <th>{{ __('dashboard.room_ar') }}</th>
                <th class="text-end">{{ __('dashboard.total_ar') }}</th>
                <th class="text-end">{{ __('dashboard.paid') }}</th>
                <th class="text-end">{{ __('dashboard.balance_ar') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reservations ?? [] as $reservation)
                <tr>
                    <td>{{ $reservation->reservation_number }}</td>
                    <td>{{ $reservation->guest->first_name ?? '-' }} {{ $reservation->guest->last_name ?? '' }}</td>
                    <td>{{ $reservation->unit->unit_number ?? '-' }}</td>
                    <td class="text-end">ر.س {{ number_format($reservation->grand_total, 2) }}</td>
                    <td class="text-end">ر.س {{ number_format($reservation->paid_amount ?? 0, 2) }}</td>
                    <td class="text-end">ر.س {{ number_format($reservation->balance ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
