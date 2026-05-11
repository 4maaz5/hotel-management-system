@extends('admin.reports.layouts.report')

@section('title', __('dashboard.receipt_vouchers_report'))

@section('report_content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.receipt_vouchers') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('dashboard.voucher_no') }}</th>
                            <th>{{ __('dashboard.date') }}</th>
                            <th>{{ __('dashboard.payment_method') }}</th>
                            <th>{{ __('dashboard.received_from') }}</th>
                            <th>{{ __('dashboard.purpose') }}</th>
                            <th class="text-end">{{ __('dashboard.amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vouchers as $voucher)
                            <tr>
                                <td>{{ $voucher->voucher_number }}</td>
                                <td>{{ $voucher->date->format('Y-m-d') }}</td>
                                <td>{{ $voucher->paymentMethod->paymentMethod->name ?? '-' ?? 'N/A' }}</td>
                                <td>{{ $voucher->received_from_name ?? '-' }}</td>
                                <td>{{ $voucher->purpose ?? '-' }}</td>
                                <td class="text-end text-success">SAR {{ number_format($voucher->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">{{ __('dashboard.no_records_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="text-end fw-bold">{{ __('dashboard.total') }}</td>
                            <td class="text-end fw-bold">SAR {{ number_format($vouchers->sum('amount'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @if($vouchers->hasPages())
            <div class="card-footer">
                {{ $vouchers->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection

@section('print_content')
<div class="english-text">
    <h4 class="text-center mb-3">{{ __('dashboard.receipt_vouchers_report') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.voucher_no') }}</th>
                <th>{{ __('dashboard.date') }}</th>
                <th>{{ __('dashboard.payment_method') }}</th>
                <th>{{ __('dashboard.received_from') }}</th>
                <th>{{ __('dashboard.purpose') }}</th>
                <th class="text-end">{{ __('dashboard.amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vouchers as $voucher)
                <tr>
                    <td>{{ $voucher->voucher_number }}</td>
                    <td>{{ $voucher->date->format('Y-m-d') }}</td>
                    <td>{{ $voucher->paymentMethod->name ?? '-' ?? 'N/A' }}</td>
                    <td>{{ $voucher->received_from_name ?? '-' }}</td>
                    <td>{{ $voucher->purpose ?? '-' }}</td>
                    <td class="text-end">SAR {{ number_format($voucher->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
            <tr class="fw-bold">
                <td colspan="5" class="text-end">{{ __('dashboard.total') }}</td>
                <td class="text-end">SAR {{ number_format($vouchers->sum('amount'), 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center mb-3">{{ __('dashboard.receipt_vouchers_report_ar') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.voucher_no_ar') }}</th>
                <th>{{ __('dashboard.date_ar') }}</th>
                <th>{{ __('dashboard.payment_method') }}</th>
                <th>{{ __('dashboard.received_from_ar') }}</th>
                <th>{{ __('dashboard.purpose_ar') }}</th>
                <th class="text-end">{{ __('dashboard.amount_ar') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vouchers as $voucher)
                <tr>
                    <td>{{ $voucher->voucher_number }}</td>
                    <td>{{ $voucher->date->format('Y-m-d') }}</td>
                    <td>{{ $voucher->paymentMethod->name ?? '-' ?? 'N/A' }}</td>
                    <td>{{ $voucher->received_from_name ?? '-' }}</td>
                    <td>{{ $voucher->purpose ?? '-' }}</td>
                    <td class="text-end">ر.س {{ number_format($voucher->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
            <tr class="fw-bold">
                <td colspan="5" class="text-end">{{ __('dashboard.total_ar') }}</td>
                <td class="text-end">ر.س {{ number_format($vouchers->sum('amount'), 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
