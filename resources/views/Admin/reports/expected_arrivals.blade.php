@extends('admin.reports.layouts.report')

@section('title', __('dashboard.expected_arrivals_report'))

@section('report_content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.expected_arrivals') }}</h5>
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
                            <th>{{ __('dashboard.adults') }}</th>
                            <th>{{ __('dashboard.children') }}</th>
                            <th>{{ __('dashboard.status') }}</th>
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
                                <td class="text-center">{{ $reservation->adults ?? 0 }}</td>
                                <td class="text-center">{{ $reservation->children ?? 0 }}</td>
                                <td>
                                    <span class="badge bg-{{ $reservation->status === 'checked_in' ? 'success' : 'warning' }}">
                                        {{ ucfirst(str_replace('_', ' ', $reservation->status)) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">{{ __('dashboard.no_records_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(isset($reservations) && $reservations->hasPages())
            <div class="card-footer">
                {{ $reservations->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection

@section('print_content')
<div class="english-text">
    <h4 class="text-center mb-3">{{ __('dashboard.expected_arrivals_report') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.reservation_no') }}</th>
                <th>{{ __('dashboard.guest') }}</th>
                <th>{{ __('dashboard.room') }}</th>
                <th>{{ __('dashboard.check_in') }}</th>
                <th>{{ __('dashboard.check_out') }}</th>
                <th>{{ __('dashboard.adults') }}</th>
                <th>{{ __('dashboard.children') }}</th>
                <th>{{ __('dashboard.status') }}</th>
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
                    <td class="text-center">{{ $reservation->adults ?? 0 }}</td>
                    <td class="text-center">{{ $reservation->children ?? 0 }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center mb-3">{{ __('dashboard.expected_arrivals_report_ar') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.reservation_no') }}</th>
                <th>{{ __('dashboard.guest_ar') }}</th>
                <th>{{ __('dashboard.room_ar') }}</th>
                <th>{{ __('dashboard.check_in') }}</th>
                <th>{{ __('dashboard.check_out') }}</th>
                <th>{{ __('dashboard.adults') }}</th>
                <th>{{ __('dashboard.children') }}</th>
                <th>{{ __('dashboard.status_ar') }}</th>
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
                    <td class="text-center">{{ $reservation->adults ?? 0 }}</td>
                    <td class="text-center">{{ $reservation->children ?? 0 }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
