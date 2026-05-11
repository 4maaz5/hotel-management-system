@extends('admin.reports.layouts.report')

@section('title', __('dashboard.invoices_report'))

@section('report_content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.invoices') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('dashboard.invoice_no') }}</th>
                            <th>{{ __('dashboard.date') }}</th>
                            <th>{{ __('dashboard.guest') }}</th>
                            <th>{{ __('dashboard.reservation') }}</th>
                            <th class="text-end">{{ __('dashboard.total') }}</th>
                            <th>{{ __('dashboard.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices ?? [] as $invoice)
                            <tr>
                                <td>{{ $invoice->invoice_number ?? '#' . $invoice->id }}</td>
                                <td>{{ $invoice->issue_date->format('Y-m-d') }}</td>
                                <td>{{ $invoice->reservation->guest->first_name ?? '-' }} {{ $invoice->reservation->guest->last_name ?? '' }}</td>
                                <td>{{ $invoice->reservation->reservation_number ?? '-' }}</td>
                                <td class="text-end">SAR {{ number_format($invoice->total, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'cancelled' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($invoice->status ?? 'pending') }}
                                    </span>
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
        @if(isset($invoices) && $invoices->hasPages())
            <div class="card-footer">
                {{ $invoices->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection

@section('print_content')
<div class="english-text">
    <h4 class="text-center mb-3">{{ __('dashboard.invoices_report') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.invoice_no') }}</th>
                <th>{{ __('dashboard.date') }}</th>
                <th>{{ __('dashboard.guest') }}</th>
                <th>{{ __('dashboard.reservation') }}</th>
                <th class="text-end">{{ __('dashboard.total') }}</th>
                <th>{{ __('dashboard.status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices ?? [] as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_number ?? '#' . $invoice->id }}</td>
                    <td>{{ $invoice->issue_date->format('Y-m-d') }}</td>
                    <td>{{ $invoice->reservation->guest->first_name ?? '-' }} {{ $invoice->reservation->guest->last_name ?? '' }}</td>
                    <td>{{ $invoice->reservation->reservation_number ?? '-' }}</td>
                    <td class="text-end">SAR {{ number_format($invoice->total, 2) }}</td>
                    <td>{{ ucfirst($invoice->status ?? 'pending') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center mb-3">{{ __('dashboard.invoice_report_ar') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.invoice_no_ar') }}</th>
                <th>{{ __('dashboard.invoice_date_ar') }}</th>
                <th>{{ __('dashboard.guest_ar') }}</th>
                <th>{{ __('dashboard.reservation') }}</th>
                <th class="text-end">{{ __('dashboard.total_ar') }}</th>
                <th>{{ __('dashboard.status_ar') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices ?? [] as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_number ?? '#' . $invoice->id }}</td>
                    <td>{{ $invoice->issue_date->format('Y-m-d') }}</td>
                    <td>{{ $invoice->reservation->guest->first_name ?? '-' }} {{ $invoice->reservation->guest->last_name ?? '' }}</td>
                    <td>{{ $invoice->reservation->reservation_number ?? '-' }}</td>
                    <td class="text-end">ر.س {{ number_format($invoice->total, 2) }}</td>
                    <td>{{ ucfirst($invoice->status ?? 'pending') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
