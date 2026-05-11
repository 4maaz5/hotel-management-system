@extends('admin.reports.layouts.report')

@section('title', __('dashboard.financial_transactions_report'))

@section('report_content')
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.summary') }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <small class="text-muted">{{ __('dashboard.total_receipts') }}</small>
                        <h4 class="text-success mb-0">SAR {{ number_format($totalReceipts ?? 0, 2) }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <small class="text-muted">{{ __('dashboard.total_payments') }}</small>
                        <h4 class="text-danger mb-0">SAR {{ number_format($totalPayments ?? 0, 2) }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <small class="text-muted">{{ __('dashboard.refunds') }}</small>
                        <h4 class="text-warning mb-0">SAR {{ number_format($totalRefunds ?? 0, 2) }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center bg-primary text-white">
                        <small>{{ __('dashboard.net_amount') }}</small>
                        <h4 class="mb-0">SAR {{ number_format($netAmount ?? 0, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.receipts') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('dashboard.voucher_no') }}</th>
                            <th>{{ __('dashboard.date') }}</th>
                            <th>{{ __('dashboard.received_from') }}</th>
                            <th>{{ __('dashboard.purpose') }}</th>
                            <th class="text-end">{{ __('dashboard.amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receipts ?? [] as $voucher)
                            <tr>
                                <td>{{ $voucher->voucher_number }}</td>
                                <td>{{ $voucher->date->format('Y-m-d') }}</td>
                                <td>{{ $voucher->received_from_name ?? '-' }}</td>
                                <td>{{ $voucher->purpose ?? '-' }}</td>
                                <td class="text-end text-success">SAR {{ number_format($voucher->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">{{ __('dashboard.no_records_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.payments') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('dashboard.voucher_no') }}</th>
                            <th>{{ __('dashboard.date') }}</th>
                            <th>{{ __('dashboard.paid_to') }}</th>
                            <th>{{ __('dashboard.purpose') }}</th>
                            <th class="text-end">{{ __('dashboard.amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments ?? [] as $voucher)
                            <tr>
                                <td>{{ $voucher->voucher_number }}</td>
                                <td>{{ $voucher->date->format('Y-m-d') }}</td>
                                <td>{{ $voucher->vendor_name ?? '-' }}</td>
                                <td>{{ $voucher->purpose ?? '-' }}</td>
                                <td class="text-end {{ $voucher->voucher_type === 'refund' ? 'text-warning' : 'text-danger' }}">
                                    SAR {{ number_format($voucher->amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">{{ __('dashboard.no_records_found') }}</td>
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
    <h4 class="text-center mb-3">{{ __('dashboard.financial_transactions_report') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    
    <h5 class="mt-4">{{ __('dashboard.summary') }}</h5>
    <table class="table table-bordered table-sm">
        <tr>
            <td>{{ __('dashboard.total_receipts') }}</td>
            <td class="text-end text-success">SAR {{ number_format($totalReceipts ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.total_payments') }}</td>
            <td class="text-end text-danger">SAR {{ number_format($totalPayments ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.refunds') }}</td>
            <td class="text-end text-warning">SAR {{ number_format($totalRefunds ?? 0, 2) }}</td>
        </tr>
        <tr class="fw-bold">
            <td>{{ __('dashboard.net_amount') }}</td>
            <td class="text-end">SAR {{ number_format($netAmount ?? 0, 2) }}</td>
        </tr>
    </table>

    <h5 class="mt-4">{{ __('dashboard.receipts') }}</h5>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.voucher_no') }}</th>
                <th>{{ __('dashboard.date') }}</th>
                <th>{{ __('dashboard.received_from') }}</th>
                <th>{{ __('dashboard.purpose') }}</th>
                <th class="text-end">{{ __('dashboard.amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($receipts ?? [] as $voucher)
                <tr>
                    <td>{{ $voucher->voucher_number }}</td>
                    <td>{{ $voucher->date->format('Y-m-d') }}</td>
                    <td>{{ $voucher->received_from_name ?? '-' }}</td>
                    <td>{{ $voucher->purpose ?? '-' }}</td>
                    <td class="text-end">SAR {{ number_format($voucher->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>

    <h5 class="mt-4">{{ __('dashboard.payments') }}</h5>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.voucher_no') }}</th>
                <th>{{ __('dashboard.date') }}</th>
                <th>{{ __('dashboard.paid_to') }}</th>
                <th>{{ __('dashboard.purpose') }}</th>
                <th class="text-end">{{ __('dashboard.amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments ?? [] as $voucher)
                <tr>
                    <td>{{ $voucher->voucher_number }}</td>
                    <td>{{ $voucher->date->format('Y-m-d') }}</td>
                    <td>{{ $voucher->vendor_name ?? '-' }}</td>
                    <td>{{ $voucher->purpose ?? '-' }}</td>
                    <td class="text-end">SAR {{ number_format($voucher->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="arabic-text">
    <h4 class="text-center mb-3">{{ __('dashboard.financial_transactions_report_ar') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    
    <h5 class="mt-4">{{ __('dashboard.summary_ar') }}</h5>
    <table class="table table-bordered table-sm">
        <tr>
            <td>{{ __('dashboard.total_receipts_ar') }}</td>
            <td class="text-end">ر.س {{ number_format($totalReceipts ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.total_payments_ar') }}</td>
            <td class="text-end">ر.س {{ number_format($totalPayments ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.refunds_ar') }}</td>
            <td class="text-end">ر.س {{ number_format($totalRefunds ?? 0, 2) }}</td>
        </tr>
        <tr class="fw-bold">
            <td>{{ __('dashboard.net_amount_ar') }}</td>
            <td class="text-end">ر.س {{ number_format($netAmount ?? 0, 2) }}</td>
        </tr>
    </table>

    <h5 class="mt-4">{{ __('dashboard.receipts_ar') }}</h5>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.voucher_no_ar') }}</th>
                <th>{{ __('dashboard.date_ar') }}</th>
                <th>{{ __('dashboard.received_from_ar') }}</th>
                <th>{{ __('dashboard.purpose_ar') }}</th>
                <th class="text-end">{{ __('dashboard.amount_ar') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($receipts ?? [] as $voucher)
                <tr>
                    <td>{{ $voucher->voucher_number }}</td>
                    <td>{{ $voucher->date->format('Y-m-d') }}</td>
                    <td>{{ $voucher->received_from_name ?? '-' }}</td>
                    <td>{{ $voucher->purpose ?? '-' }}</td>
                    <td class="text-end">ر.س {{ number_format($voucher->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>

    <h5 class="mt-4">{{ __('dashboard.payments_ar') }}</h5>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>{{ __('dashboard.voucher_no_ar') }}</th>
                <th>{{ __('dashboard.date_ar') }}</th>
                <th>{{ __('dashboard.paid_to_ar') }}</th>
                <th>{{ __('dashboard.purpose_ar') }}</th>
                <th class="text-end">{{ __('dashboard.amount_ar') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments ?? [] as $voucher)
                <tr>
                    <td>{{ $voucher->voucher_number }}</td>
                    <td>{{ $voucher->date->format('Y-m-d') }}</td>
                    <td>{{ $voucher->vendor_name ?? '-' }}</td>
                    <td>{{ $voucher->purpose ?? '-' }}</td>
                    <td class="text-end">ر.س {{ number_format($voucher->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
