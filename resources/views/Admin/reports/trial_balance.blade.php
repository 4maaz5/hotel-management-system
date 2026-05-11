@extends('admin.reports.layouts.report')

@section('title', __('dashboard.trial_balance_report'))

@section('report_content')
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.trial_balance_summary') }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <small class="text-muted">{{ __('dashboard.total_receipts') }}</small>
                        <h4 class="text-success mb-0">SAR {{ number_format($receiptsTotal ?? 0, 2) }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <small class="text-muted">{{ __('dashboard.total_payments') }}</small>
                        <h4 class="text-danger mb-0">SAR {{ number_format($paymentsTotal ?? 0, 2) }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <small class="text-muted">{{ __('dashboard.drop_cash') }}</small>
                        <h4 class="text-warning mb-0">SAR {{ number_format($dropCashTotal ?? 0, 2) }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <small class="text-muted">{{ __('dashboard.invoices_total') }}</small>
                        <h4 class="text-primary mb-0">SAR {{ number_format($invoicesTotal ?? 0, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('print_content')
<div class="english-text">
    <h4 class="text-center mb-3">{{ __('dashboard.trial_balance_summary') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm">
        <tr>
            <td>{{ __('dashboard.total_receipts') }}</td>
            <td class="text-end">SAR {{ number_format($receiptsTotal ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.total_payments') }}</td>
            <td class="text-end">SAR {{ number_format($paymentsTotal ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.drop_cash') }}</td>
            <td class="text-end">SAR {{ number_format($dropCashTotal ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.invoices_total') }}</td>
            <td class="text-end">SAR {{ number_format($invoicesTotal ?? 0, 2) }}</td>
        </tr>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center mb-3">{{ __('dashboard.trial_balance_report_ar') }}</h4>
    <p class="text-center mb-3">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered table-sm">
        <tr>
            <td>{{ __('dashboard.total_receipts_ar') }}</td>
            <td class="text-end">ر.س {{ number_format($receiptsTotal ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.total_payments_ar') }}</td>
            <td class="text-end">ر.س {{ number_format($paymentsTotal ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.drop_cash_ar') }}</td>
            <td class="text-end">ر.س {{ number_format($dropCashTotal ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>{{ __('dashboard.total_invoices_ar') }}</td>
            <td class="text-end">ر.س {{ number_format($invoicesTotal ?? 0, 2) }}</td>
        </tr>
    </table>
</div>
@endsection
