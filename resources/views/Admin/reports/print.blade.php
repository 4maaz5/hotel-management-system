@extends('layouts.print')

@php
$reportTitle = $reportTitle ?? 'Report';
$letterHead = $printingOption?->letter_head ?? $globalSetting?->letter_head ?? false;
$blankPaper = $printingOption?->blank_paper ?? $globalSetting?->blank_paper ?? false;
$showPropertyInfo = $letterHead && !$blankPaper;
@endphp

@section('title', $reportTitle)

@push('styles')
<style>
    body { padding: 20px; font-family: Arial, sans-serif; }
    .english-text { direction: ltr; text-align: left; }
    .arabic-text { direction: rtl; text-align: right; display: none; }
    .arabic-text .text-end { text-align: left !important; }
    .lang-ar .english-text { display: none; }
    .lang-ar .arabic-text { display: block; }
    .lang-both .english-text { display: block; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #dee2e6; }
    .lang-both .arabic-text { display: block; }
    .lang-ar { direction: rtl; }
    .lang-ar .english-text, .lang-ar .arabic-text { direction: rtl; }
    .lang-ar table th, .lang-ar table td { text-align: right; }
    .property-info-box { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    h4 { margin-top: 0; }
    table { margin-bottom: 15px; width: 100%; }
    .text-center { text-align: center; }
    .text-end { text-align: right; }
    @media print {
        body { padding: 0; }
        .lang-ar .arabic-text { display: block !important; }
        .lang-ar .english-text { display: none !important; }
        .lang-both .english-text { display: block !important; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #000; }
        .lang-both .arabic-text { display: block !important; }
        .english-text { display: block !important; }
        .arabic-text { display: block !important; }
    }
</style>
@endpush

@section('content')
@if($showPropertyInfo)
<div class="property-info-box english-text">
    <div class="d-flex justify-content-between">
        <div>
            <strong>{{ optional($property)->property_name_en ?? 'Hotel Name' }}</strong><br>
            {{ optional($property)->address_en ?? 'Address' }}<br>
            {{ optional($property)->property_code ?? '' }}<br>
            {{ optional($property)->phone ?? '' }}<br>
            VAT: {{ optional($property->commercialDetail)->vat_registration_number ?? '' }}
        </div>
        <div>
            <small class="text-muted">{{ now()->format('Y-m-d H:i') }}</small>
        </div>
    </div>
</div>
<div class="property-info-box arabic-text">
    <div class="d-flex justify-content-between">
        <div>
            <strong>{{ optional($property)->property_name_ar ?? 'اسم الفندق' }}</strong><br>
            {{ optional($property)->address_ar ?? 'العنوان' }}<br>
            {{ optional($property)->property_code ?? '' }}<br>
            {{ optional($property)->phone ?? '' }}<br>
            VAT: {{ optional($property->commercialDetail)->vat_registration_number ?? '' }}
        </div>
        <div>
            <small class="text-muted">{{ now()->format('Y-m-d H:i') }}</small>
        </div>
    </div>
</div>
@endif

{{-- Financial Transactions Report --}}
@if($reportType === 'financial_transactions')
<div class="english-text">
    <h4 class="text-center">{{ __('dashboard.financial_transactions_report') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <tr><td>{{ __('dashboard.total_receipts') }}</td><td class="text-end">SAR {{ number_format($totalReceipts ?? 0, 2) }}</td></tr>
        <tr><td>{{ __('dashboard.total_payments') }}</td><td class="text-end">SAR {{ number_format($totalPayments ?? 0, 2) }}</td></tr>
        <tr><td>{{ __('dashboard.refunds') }}</td><td class="text-end">SAR {{ number_format($totalRefunds ?? 0, 2) }}</td></tr>
        <tr><td>{{ __('dashboard.net_amount') }}</td><td class="text-end">SAR {{ number_format($netAmount ?? 0, 2) }}</td></tr>
    </table>
    <h5>{{ __('dashboard.receipts') }}</h5>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.voucher_no') }}</th><th>{{ __('dashboard.date') }}</th><th>{{ __('dashboard.received_from') }}</th><th class="text-end">{{ __('dashboard.amount') }}</th></tr></thead>
        <tbody>
            @forelse($receipts as $voucher)
                <tr><td>{{ $voucher->voucher_number }}</td><td>{{ $voucher->date->format('Y-m-d') }}</td><td>{{ $voucher->received_from_name ?? '-' }}</td><td class="text-end">SAR {{ number_format($voucher->amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
    <h5>{{ __('dashboard.payments') }}</h5>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.voucher_no') }}</th><th>{{ __('dashboard.date') }}</th><th>{{ __('dashboard.paid_to') }}</th><th class="text-end">{{ __('dashboard.amount') }}</th></tr></thead>
        <tbody>
            @forelse($payments as $voucher)
                <tr><td>{{ $voucher->voucher_number }}</td><td>{{ $voucher->date->format('Y-m-d') }}</td><td>{{ $voucher->vendor_name ?? '-' }}</td><td class="text-end">SAR {{ number_format($voucher->amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center">{{ __('dashboard.financial_transactions_report_ar') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <tr><td>{{ __('dashboard.total_receipts_ar') }}</td><td class="text-end">ر.س {{ number_format($totalReceipts ?? 0, 2) }}</td></tr>
        <tr><td>{{ __('dashboard.total_payments_ar') }}</td><td class="text-end">ر.س {{ number_format($totalPayments ?? 0, 2) }}</td></tr>
        <tr><td>{{ __('dashboard.refunds_ar') }}</td><td class="text-end">ر.س {{ number_format($totalRefunds ?? 0, 2) }}</td></tr>
        <tr><td>{{ __('dashboard.net_amount_ar') }}</td><td class="text-end">ر.س {{ number_format($netAmount ?? 0, 2) }}</td></tr>
    </table>
    <h5>{{ __('dashboard.receipts_ar') }}</h5>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.voucher_no_ar') }}</th><th>{{ __('dashboard.date_ar') }}</th><th>{{ __('dashboard.received_from_ar') }}</th><th class="text-end">{{ __('dashboard.amount_ar') }}</th></tr></thead>
        <tbody>
            @forelse($receipts as $voucher)
                <tr><td>{{ $voucher->voucher_number }}</td><td>{{ $voucher->date->format('Y-m-d') }}</td><td>{{ $voucher->received_from_name ?? '-' }}</td><td class="text-end">ر.س {{ number_format($voucher->amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
    <h5>{{ __('dashboard.payments_ar') }}</h5>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.voucher_no_ar') }}</th><th>{{ __('dashboard.date_ar') }}</th><th>{{ __('dashboard.paid_to_ar') }}</th><th class="text-end">{{ __('dashboard.amount_ar') }}</th></tr></thead>
        <tbody>
            @forelse($payments as $voucher)
                <tr><td>{{ $voucher->voucher_number }}</td><td>{{ $voucher->date->format('Y-m-d') }}</td><td>{{ $voucher->vendor_name ?? '-' }}</td><td class="text-end">ر.س {{ number_format($voucher->amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- Daily Transactions Report --}}
@if($reportType === 'daily_transactions')
<div class="english-text">
    <h4 class="text-center">{{ __('dashboard.daily_transactions') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.date') }}</th><th class="text-center">{{ __('dashboard.receipts_count') }}</th><th class="text-end">{{ __('dashboard.receipts_amount') }}</th><th class="text-center">{{ __('dashboard.payments_count') }}</th><th class="text-end">{{ __('dashboard.payments_amount') }}</th><th class="text-end">{{ __('dashboard.net') }}</th></tr></thead>
        <tbody>
            @forelse($dailyData as $date => $data)
                <tr><td>{{ $date }}</td><td class="text-center">{{ $data['receipts_count'] }}</td><td class="text-end">SAR {{ number_format($data['receipts_amount'], 2) }}</td><td class="text-center">{{ $data['payments_count'] }}</td><td class="text-end">SAR {{ number_format($data['payments_amount'], 2) }}</td><td class="text-end">SAR {{ number_format($data['receipts_amount'] - $data['payments_amount'], 2) }}</td></tr>
            @empty
                <tr><td colspan="6" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center">{{ __('dashboard.daily_transactions_report_ar') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.date_ar') }}</th><th class="text-center">{{ __('dashboard.receipts_count_ar') }}</th><th class="text-end">{{ __('dashboard.receipts_amount_ar') }}</th><th class="text-center">{{ __('dashboard.payments_count_ar') }}</th><th class="text-end">{{ __('dashboard.payments_amount_ar') }}</th><th class="text-end">{{ __('dashboard.net_ar') }}</th></tr></thead>
        <tbody>
            @forelse($dailyData as $date => $data)
                <tr><td>{{ $date }}</td><td class="text-center">{{ $data['receipts_count'] }}</td><td class="text-end">ر.س {{ number_format($data['receipts_amount'], 2) }}</td><td class="text-center">{{ $data['payments_count'] }}</td><td class="text-end">ر.س {{ number_format($data['payments_amount'], 2) }}</td><td class="text-end">ر.س {{ number_format($data['receipts_amount'] - $data['payments_amount'], 2) }}</td></tr>
            @empty
                <tr><td colspan="6" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- Trial Balance Report --}}
@if($reportType === 'trial_balance')
<div class="english-text">
    <h4 class="text-center">{{ __('dashboard.trial_balance_summary') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <tr><td>{{ __('dashboard.total_receipts') }}</td><td class="text-end">SAR {{ number_format($receiptsTotal ?? 0, 2) }}</td></tr>
        <tr><td>{{ __('dashboard.total_payments') }}</td><td class="text-end">SAR {{ number_format($paymentsTotal ?? 0, 2) }}</td></tr>
        <tr><td>{{ __('dashboard.drop_cash') }}</td><td class="text-end">SAR {{ number_format($dropCashTotal ?? 0, 2) }}</td></tr>
        <tr><td>{{ __('dashboard.invoices_total') }}</td><td class="text-end">SAR {{ number_format($invoicesTotal ?? 0, 2) }}</td></tr>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center">{{ __('dashboard.trial_balance_report_ar') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <tr><td>{{ __('dashboard.total_receipts_ar') }}</td><td class="text-end">ر.س {{ number_format($receiptsTotal ?? 0, 2) }}</td></tr>
        <tr><td>{{ __('dashboard.total_payments_ar') }}</td><td class="text-end">ر.س {{ number_format($paymentsTotal ?? 0, 2) }}</td></tr>
        <tr><td>{{ __('dashboard.drop_cash_ar') }}</td><td class="text-end">ر.س {{ number_format($dropCashTotal ?? 0, 2) }}</td></tr>
        <tr><td>{{ __('dashboard.total_invoices_ar') }}</td><td class="text-end">ر.س {{ number_format($invoicesTotal ?? 0, 2) }}</td></tr>
    </table>
</div>
@endif

{{-- Tax Report --}}
@if($reportType === 'tax')
<div class="english-text">
    <h4 class="text-center">{{ __('dashboard.tax_report') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered mb-3">
        <tr><td>{{ __('dashboard.taxable_amount') }}</td><td class="text-end">SAR {{ number_format($totalTaxableAmount ?? 0, 2) }}</td></tr>
        <tr><td>{{ __('dashboard.tax_amount') }}</td><td class="text-end">SAR {{ number_format($totalTaxAmount ?? 0, 2) }}</td></tr>
        <tr><td>{{ __('dashboard.total_with_tax') }}</td><td class="text-end">SAR {{ number_format($totalAmount ?? 0, 2) }}</td></tr>
    </table>
    <h5>{{ __('dashboard.invoices') }}</h5>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.invoice_no') }}</th><th>{{ __('dashboard.date') }}</th><th>{{ __('dashboard.guest') }}</th><th class="text-end">{{ __('dashboard.total') }}</th></tr></thead>
        <tbody>
            @forelse($invoices as $invoice)
                <tr><td>{{ $invoice->invoice_number ?? '#' . $invoice->id }}</td><td>{{ $invoice->issue_date->format('Y-m-d') }}</td><td>{{ $invoice->reservation->guest->first_name ?? '-' }} {{ $invoice->reservation->guest->last_name ?? '' }}</td><td class="text-end">SAR {{ number_format($invoice->total, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center">{{ __('dashboard.tax_report_ar') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered mb-3">
        <tr><td>{{ __('dashboard.taxable_amount_ar') }}</td><td class="text-end">ر.س {{ number_format($totalTaxableAmount ?? 0, 2) }}</td></tr>
        <tr><td>{{ __('dashboard.tax_amount_ar') }}</td><td class="text-end">ر.س {{ number_format($totalTaxAmount ?? 0, 2) }}</td></tr>
        <tr><td>{{ __('dashboard.total_with_tax_ar') }}</td><td class="text-end">ر.س {{ number_format($totalAmount ?? 0, 2) }}</td></tr>
    </table>
    <h5>{{ __('dashboard.invoices_ar') }}</h5>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.invoice_no_ar') }}</th><th>{{ __('dashboard.invoice_date_ar') }}</th><th>{{ __('dashboard.guest_ar') }}</th><th class="text-end">{{ __('dashboard.total_ar') }}</th></tr></thead>
        <tbody>
            @forelse($invoices as $invoice)
                <tr><td>{{ $invoice->invoice_number ?? '#' . $invoice->id }}</td><td>{{ $invoice->issue_date->format('Y-m-d') }}</td><td>{{ $invoice->reservation->guest->first_name ?? '-' }} {{ $invoice->reservation->guest->last_name ?? '' }}</td><td class="text-end">ر.س {{ number_format($invoice->total, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- Receipt Vouchers --}}
@if($reportType === 'receipt_vouchers')
<div class="english-text">
    <h4 class="text-center">{{ __('dashboard.receipt_vouchers_report') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.voucher_no') }}</th><th>{{ __('dashboard.date') }}</th><th>{{ __('dashboard.received_from') }}</th><th class="text-end">{{ __('dashboard.amount') }}</th></tr></thead>
        <tbody>
            @forelse($vouchers as $voucher)
                <tr><td>{{ $voucher->voucher_number }}</td><td>{{ $voucher->date->format('Y-m-d') }}</td><td>{{ $voucher->received_from_name ?? '-' }}</td><td class="text-end">SAR {{ number_format($voucher->amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center">{{ __('dashboard.receipt_vouchers_report_ar') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.voucher_no_ar') }}</th><th>{{ __('dashboard.date_ar') }}</th><th>{{ __('dashboard.received_from_ar') }}</th><th class="text-end">{{ __('dashboard.amount_ar') }}</th></tr></thead>
        <tbody>
            @forelse($vouchers as $voucher)
                <tr><td>{{ $voucher->voucher_number }}</td><td>{{ $voucher->date->format('Y-m-d') }}</td><td>{{ $voucher->received_from_name ?? '-' }}</td><td class="text-end">ر.س {{ number_format($voucher->amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- Payment Vouchers --}}
@if($reportType === 'payment_vouchers')
<div class="english-text">
    <h4 class="text-center">{{ __('dashboard.payments_report') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.voucher_no') }}</th><th>{{ __('dashboard.date') }}</th><th>{{ __('dashboard.paid_to') }}</th><th class="text-end">{{ __('dashboard.amount') }}</th></tr></thead>
        <tbody>
            @forelse($vouchers as $voucher)
                <tr><td>{{ $voucher->voucher_number }}</td><td>{{ $voucher->date->format('Y-m-d') }}</td><td>{{ $voucher->vendor_name ?? '-' }}</td><td class="text-end">SAR {{ number_format($voucher->amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center">{{ __('dashboard.payment_vouchers_report_ar') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.voucher_no_ar') }}</th><th>{{ __('dashboard.date_ar') }}</th><th>{{ __('dashboard.paid_to_ar') }}</th><th class="text-end">{{ __('dashboard.amount_ar') }}</th></tr></thead>
        <tbody>
            @forelse($vouchers as $voucher)
                <tr><td>{{ $voucher->voucher_number }}</td><td>{{ $voucher->date->format('Y-m-d') }}</td><td>{{ $voucher->vendor_name ?? '-' }}</td><td class="text-end">ر.س {{ number_format($voucher->amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- Invoices --}}
@if($reportType === 'invoices')
<div class="english-text">
    <h4 class="text-center">{{ __('dashboard.invoices_report') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.invoice_no') }}</th><th>{{ __('dashboard.date') }}</th><th>{{ __('dashboard.guest') }}</th><th class="text-end">{{ __('dashboard.total') }}</th></tr></thead>
        <tbody>
            @forelse($invoices as $invoice)
                <tr><td>{{ $invoice->invoice_number ?? '#' . $invoice->id }}</td><td>{{ $invoice->issue_date->format('Y-m-d') }}</td><td>{{ $invoice->reservation->guest->first_name ?? '-' }} {{ $invoice->reservation->guest->last_name ?? '' }}</td><td class="text-end">SAR {{ number_format($invoice->total, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center">{{ __('dashboard.invoice_report_ar') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.invoice_no_ar') }}</th><th>{{ __('dashboard.invoice_date_ar') }}</th><th>{{ __('dashboard.guest_ar') }}</th><th class="text-end">{{ __('dashboard.total_ar') }}</th></tr></thead>
        <tbody>
            @forelse($invoices as $invoice)
                <tr><td>{{ $invoice->invoice_number ?? '#' . $invoice->id }}</td><td>{{ $invoice->issue_date->format('Y-m-d') }}</td><td>{{ $invoice->reservation->guest->first_name ?? '-' }} {{ $invoice->reservation->guest->last_name ?? '' }}</td><td class="text-end">ر.س {{ number_format($invoice->total, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- Credit Notes --}}
@if($reportType === 'credit_notes')
<div class="english-text">
    <h4 class="text-center">{{ __('dashboard.credit_notes_report') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.credit_note_no') }}</th><th>{{ __('dashboard.date') }}</th><th>{{ __('dashboard.guest') }}</th><th class="text-end">{{ __('dashboard.amount') }}</th></tr></thead>
        <tbody>
            @forelse($creditNotes as $note)
                <tr><td>{{ $note->credit_note_number ?? '#' . $note->id }}</td><td>{{ $note->cn_date ? $note->cn_date->format('Y-m-d') : '-' }}</td><td>{{ $note->guest->first_name ?? '-' }} {{ $note->guest->last_name ?? '' }}</td><td class="text-end">SAR {{ number_format($note->amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center">{{ __('dashboard.credit_note_report_ar') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.credit_note_no_ar') }}</th><th>{{ __('dashboard.date_ar') }}</th><th>{{ __('dashboard.guest_ar') }}</th><th class="text-end">{{ __('dashboard.amount_ar') }}</th></tr></thead>
        <tbody>
            @forelse($creditNotes as $note)
                <tr><td>{{ $note->credit_note_number ?? '#' . $note->id }}</td><td>{{ $note->created_at }}</td><td>{{ $note->guest->first_name ?? '-' }} {{ $note->guest->last_name ?? '' }}</td><td class="text-end">ر.س {{ number_format($note->amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- Promissory Notes --}}
@if($reportType === 'promissory_notes')
<div class="english-text">
    <h4 class="text-center">{{ __('dashboard.promissory_notes_report') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.note_no') }}</th><th>{{ __('dashboard.issue_date') }}</th><th>{{ __('dashboard.guest') }}</th><th class="text-end">{{ __('dashboard.amount') }}</th></tr></thead>
        <tbody>
            @forelse($promissoryNotes as $note)
                <tr><td>{{ $note->note_number ?? '#' . $note->id }}</td><td>{{ $note->date->format('Y-m-d') }}</td><td>{{ $note->guest->first_name ?? '-' }} {{ $note->guest->last_name ?? '' }}</td><td class="text-end">SAR {{ number_format($note->amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center">{{ __('dashboard.promissory_note_report_ar') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.note_no_ar') }}</th><th>{{ __('dashboard.issue_date_ar') }}</th><th>{{ __('dashboard.guest_ar') }}</th><th class="text-end">{{ __('dashboard.amount_ar') }}</th></tr></thead>
        <tbody>
            @forelse($promissoryNotes as $note)
                <tr><td>{{ $note->note_number ?? '#' . $note->id }}</td><td>{{ $note->date->format('Y-m-d') }}</td><td>{{ $note->guest->first_name ?? '-' }} {{ $note->guest->last_name ?? '' }}</td><td class="text-end">ر.س {{ number_format($note->amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- Drop Cash --}}
@if($reportType === 'drop_cash')
<div class="english-text">
    <h4 class="text-center">{{ __('dashboard.drop_cash_report') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.voucher_no') }}</th><th>{{ __('dashboard.date') }}</th><th>{{ __('dashboard.paid_to') }}</th><th class="text-end">{{ __('dashboard.amount') }}</th></tr></thead>
        <tbody>
            @forelse($dropCash as $drop)
                <tr><td>{{ $drop->voucher_number }}</td><td>{{ $drop->date_from->format('Y-m-d') }}</td><td>{{ $drop->paid_to ?? '-' }}</td><td class="text-end">SAR {{ number_format($drop->amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center">{{ __('dashboard.drop_cash_report_ar') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.voucher_no_ar') }}</th><th>{{ __('dashboard.date_ar') }}</th><th>{{ __('dashboard.paid_to_ar') }}</th><th class="text-end">{{ __('dashboard.amount_ar') }}</th></tr></thead>
        <tbody>
            @forelse($dropCash as $drop)
                <tr><td>{{ $drop->voucher_number }}</td><td>{{ $drop->date_from->format('Y-m-d') }}</td><td>{{ $drop->paid_to ?? '-' }}</td><td class="text-end">ر.س {{ number_format($drop->amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- Reservation Balances --}}
@if($reportType === 'reservation_balances')
<div class="english-text">
    <h4 class="text-center">{{ __('dashboard.reservation_balances_report') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.reservation_no') }}</th><th>{{ __('dashboard.guest') }}</th><th>{{ __('dashboard.check_in') }}</th><th class="text-end">{{ __('dashboard.total') }}</th></tr></thead>
        <tbody>
            @forelse($reservations as $reservation)
                <tr><td>{{ $reservation->reservation_number }}</td><td>{{ $reservation->guest->first_name ?? '-' }} {{ $reservation->guest->last_name ?? '' }}</td><td>{{ $reservation->check_in_date->format('Y-m-d') }}</td><td class="text-end">SAR {{ number_format($reservation->total_amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center">{{ __('dashboard.reservation_balances_report_ar') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.reservation_no_ar') }}</th><th>{{ __('dashboard.guest_ar') }}</th><th>{{ __('dashboard.check_in_ar') }}</th><th class="text-end">{{ __('dashboard.total_ar') }}</th></tr></thead>
        <tbody>
            @forelse($reservations as $reservation)
                <tr><td>{{ $reservation->reservation_number }}</td><td>{{ $reservation->guest->first_name ?? '-' }} {{ $reservation->guest->last_name ?? '' }}</td><td>{{ $reservation->check_in_date->format('Y-m-d') }}</td><td class="text-end">ر.س {{ number_format($reservation->total_amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- Guest Ledger --}}
@if($reportType === 'guest_ledger')
<div class="english-text">
    <h4 class="text-center">{{ __('dashboard.guest_ledger_report') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.reservation_no') }}</th><th>{{ __('dashboard.guest') }}</th><th>{{ __('dashboard.check_in') }}</th><th class="text-end">{{ __('dashboard.total') }}</th></tr></thead>
        <tbody>
            @forelse($reservations as $reservation)
                <tr><td>{{ $reservation->reservation_number }}</td><td>{{ $reservation->guest->first_name ?? '-' }} {{ $reservation->guest->last_name ?? '' }}</td><td>{{ $reservation->check_in_date->format('Y-m-d') }}</td><td class="text-end">SAR {{ number_format($reservation->total_amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center">{{ __('dashboard.guest_ledger_report_ar') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.reservation_no_ar') }}</th><th>{{ __('dashboard.guest_ar') }}</th><th>{{ __('dashboard.check_in_ar') }}</th><th class="text-end">{{ __('dashboard.total_ar') }}</th></tr></thead>
        <tbody>
            @forelse($reservations as $reservation)
                <tr><td>{{ $reservation->reservation_number }}</td><td>{{ $reservation->guest->first_name ?? '-' }} {{ $reservation->guest->last_name ?? '' }}</td><td>{{ $reservation->check_in_date->format('Y-m-d') }}</td><td class="text-end">ر.س {{ number_format($reservation->total_amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- City Ledger --}}
@if($reportType === 'city_ledger')
<div class="english-text">
    <h4 class="text-center">{{ __('dashboard.city_ledger_report') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.reservation_no') }}</th><th>{{ __('dashboard.guest') }}</th><th>{{ __('dashboard.check_in') }}</th><th class="text-end">{{ __('dashboard.total') }}</th></tr></thead>
        <tbody>
            @forelse($reservations as $reservation)
                <tr><td>{{ $reservation->reservation_number }}</td><td>{{ $reservation->guest->first_name ?? '-' }} {{ $reservation->guest->last_name ?? '' }}</td><td>{{ $reservation->check_in_date->format('Y-m-d') }}</td><td class="text-end">SAR {{ number_format($reservation->total_amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center">{{ __('dashboard.city_ledger_report_ar') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.reservation_no_ar') }}</th><th>{{ __('dashboard.guest_ar') }}</th><th>{{ __('dashboard.check_in_ar') }}</th><th class="text-end">{{ __('dashboard.total_ar') }}</th></tr></thead>
        <tbody>
            @forelse($reservations as $reservation)
                <tr><td>{{ $reservation->reservation_number }}</td><td>{{ $reservation->guest->first_name ?? '-' }} {{ $reservation->guest->last_name ?? '' }}</td><td>{{ $reservation->check_in_date->format('Y-m-d') }}</td><td class="text-end">ر.س {{ number_format($reservation->total_amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- Revenue by Source --}}
@if($reportType === 'revenue_by_source')
<div class="english-text">
    <h4 class="text-center">{{ __('dashboard.revenue_by_source_report') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.source') }}</th><th class="text-center">{{ __('dashboard.reservations') }}</th><th class="text-end">{{ __('dashboard.total_revenue') }}</th></tr></thead>
        <tbody>
            @forelse($bySource as $source => $data)
                <tr><td>{{ $source }}</td><td class="text-center">{{ $data['count'] }}</td><td class="text-end">SAR {{ number_format($data['total'], 2) }}</td></tr>
            @empty
                <tr><td colspan="3" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center">{{ __('dashboard.revenue_by_source_report_ar') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.source_ar') }}</th><th class="text-center">{{ __('dashboard.reservations_count_ar') }}</th><th class="text-end">{{ __('dashboard.total_revenue_ar') }}</th></tr></thead>
        <tbody>
            @forelse($bySource as $source => $data)
                <tr><td>{{ $source }}</td><td class="text-center">{{ $data['count'] }}</td><td class="text-end">ر.س {{ number_format($data['total'], 2) }}</td></tr>
            @empty
                <tr><td colspan="3" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- Reservation Revenue --}}
@if($reportType === 'reservation_revenue')
<div class="english-text">
    <h4 class="text-center">{{ __('dashboard.reservation_revenue_reports') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered mb-3">
        <tr><td>{{ __('dashboard.total_revenue') }}</td><td class="text-end">SAR {{ number_format($totalRevenue ?? 0, 2) }}</td></tr>
        <tr><td>{{ __('dashboard.total_paid') }}</td><td class="text-end">SAR {{ number_format($totalPaid ?? 0, 2) }}</td></tr>
        <tr><td>{{ __('dashboard.outstanding') }}</td><td class="text-end">SAR {{ number_format($totalOutstanding ?? 0, 2) }}</td></tr>
    </table>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.reservation_no') }}</th><th>{{ __('dashboard.guest') }}</th><th class="text-end">{{ __('dashboard.total') }}</th></tr></thead>
        <tbody>
            @forelse($reservations as $reservation)
                <tr><td>{{ $reservation->reservation_number }}</td><td>{{ $reservation->guest->first_name ?? '-' }} {{ $reservation->guest->last_name ?? '' }}</td><td class="text-end">SAR {{ number_format($reservation->total_amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="3" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center">{{ __('dashboard.reservation_revenue_report_ar') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered mb-3">
        <tr><td>{{ __('dashboard.total_revenue_ar') }}</td><td class="text-end">ر.س {{ number_format($totalRevenue ?? 0, 2) }}</td></tr>
        <tr><td>{{ __('dashboard.total_paid_ar') }}</td><td class="text-end">ر.س {{ number_format($totalPaid ?? 0, 2) }}</td></tr>
        <tr><td>{{ __('dashboard.outstanding_ar') }}</td><td class="text-end">ر.س {{ number_format($totalOutstanding ?? 0, 2) }}</td></tr>
    </table>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.reservation_no_ar') }}</th><th>{{ __('dashboard.guest_ar') }}</th><th class="text-end">{{ __('dashboard.total_ar') }}</th></tr></thead>
        <tbody>
            @forelse($reservations as $reservation)
                <tr><td>{{ $reservation->reservation_number }}</td><td>{{ $reservation->guest->first_name ?? '-' }} {{ $reservation->guest->last_name ?? '' }}</td><td class="text-end">ر.س {{ number_format($reservation->total_amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="3" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- Reservation Summary --}}
@if($reportType === 'reservation_summary')
<div class="english-text">
    <h4 class="text-center">{{ __('dashboard.reservation_summary_report') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered mb-3">
        <tr><td>{{ __('dashboard.total') }}</td><td class="text-center">{{ $summary['total'] ?? 0 }}</td></tr>
        <tr><td>{{ __('dashboard.checked_in') }}</td><td class="text-center">{{ $summary['checked_in'] ?? 0 }}</td></tr>
        <tr><td>{{ __('dashboard.checked_out') }}</td><td class="text-center">{{ $summary['checked_out'] ?? 0 }}</td></tr>
        <tr><td>{{ __('dashboard.pending') }}</td><td class="text-center">{{ $summary['pending'] ?? 0 }}</td></tr>
        <tr><td>{{ __('dashboard.cancelled') }}</td><td class="text-center">{{ $summary['cancelled'] ?? 0 }}</td></tr>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center">{{ __('dashboard.reservation_summary_report_ar') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered mb-3">
        <tr><td>{{ __('dashboard.total_ar') }}</td><td class="text-center">{{ $summary['total'] ?? 0 }}</td></tr>
        <tr><td>{{ __('dashboard.checked_in_ar') }}</td><td class="text-center">{{ $summary['checked_in'] ?? 0 }}</td></tr>
        <tr><td>{{ __('dashboard.checked_out_ar') }}</td><td class="text-center">{{ $summary['checked_out'] ?? 0 }}</td></tr>
        <tr><td>{{ __('dashboard.pending_ar') }}</td><td class="text-center">{{ $summary['pending'] ?? 0 }}</td></tr>
        <tr><td>{{ __('dashboard.cancelled_ar') }}</td><td class="text-center">{{ $summary['cancelled'] ?? 0 }}</td></tr>
    </table>
</div>
@endif

{{-- Occupancy --}}
@if($reportType === 'occupancy')
<div class="english-text">
    <h4 class="text-center">{{ __('dashboard.occupancy_report') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered mb-3">
        <tr><td>{{ __('dashboard.total_units') }}</td><td class="text-center">{{ $totalUnits ?? 0 }}</td></tr>
        <tr><td>{{ __('dashboard.occupancy_rate') }}</td><td class="text-center">{{ $occupancyRate ?? 0 }}%</td></tr>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center">{{ __('dashboard.occupancy_report_ar') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered mb-3">
        <tr><td>{{ __('dashboard.total_units_ar') }}</td><td class="text-center">{{ $totalUnits ?? 0 }}</td></tr>
        <tr><td>{{ __('dashboard.occupancy_rate_ar') }}</td><td class="text-center">{{ $occupancyRate ?? 0 }}%</td></tr>
    </table>
</div>
@endif

{{-- Reservation Details --}}
@if($reportType === 'reservation_details')
<div class="english-text">
    <h4 class="text-center">{{ __('dashboard.reservation_details_report') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.reservation_no') }}</th><th>{{ __('dashboard.guest') }}</th><th>{{ __('dashboard.check_in') }}</th><th>{{ __('dashboard.check_out') }}</th><th class="text-end">{{ __('dashboard.total') }}</th></tr></thead>
        <tbody>
            @forelse($reservations as $reservation)
                <tr><td>{{ $reservation->reservation_number }}</td><td>{{ $reservation->guest->first_name ?? '-' }} {{ $reservation->guest->last_name ?? '' }}</td><td>{{ $reservation->check_in_date->format('Y-m-d') }}</td><td>{{ $reservation->check_out_date->format('Y-m-d') }}</td><td class="text-end">SAR {{ number_format($reservation->total_amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="5" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center">{{ __('dashboard.reservation_details_report_ar') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.reservation_no_ar') }}</th><th>{{ __('dashboard.guest_ar') }}</th><th>{{ __('dashboard.check_in_ar') }}</th><th>{{ __('dashboard.check_out_ar') }}</th><th class="text-end">{{ __('dashboard.total_ar') }}</th></tr></thead>
        <tbody>
            @forelse($reservations as $reservation)
                <tr><td>{{ $reservation->reservation_number }}</td><td>{{ $reservation->guest->first_name ?? '-' }} {{ $reservation->guest->last_name ?? '' }}</td><td>{{ $reservation->check_in_date->format('Y-m-d') }}</td><td>{{ $reservation->check_out_date->format('Y-m-d') }}</td><td class="text-end">ر.س {{ number_format($reservation->total_amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="5" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- Expected Arrivals --}}
@if($reportType === 'expected_arrivals')
<div class="english-text">
    <h4 class="text-center">{{ __('dashboard.expected_arrivals_report') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.reservation_no') }}</th><th>{{ __('dashboard.guest') }}</th><th>{{ __('dashboard.check_in') }}</th><th>{{ __('dashboard.status') }}</th></tr></thead>
        <tbody>
            @forelse($reservations as $reservation)
                <tr><td>{{ $reservation->reservation_number }}</td><td>{{ $reservation->guest->first_name ?? '-' }} {{ $reservation->guest->last_name ?? '' }}</td><td>{{ $reservation->check_in_date->format('Y-m-d') }}</td><td>{{ $reservation->status }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center">{{ __('dashboard.expected_arrivals_report_ar') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.reservation_no_ar') }}</th><th>{{ __('dashboard.guest_ar') }}</th><th>{{ __('dashboard.check_in_ar') }}</th><th>{{ __('dashboard.status_ar') }}</th></tr></thead>
        <tbody>
            @forelse($reservations as $reservation)
                <tr><td>{{ $reservation->reservation_number }}</td><td>{{ $reservation->guest->first_name ?? '-' }} {{ $reservation->guest->last_name ?? '' }}</td><td>{{ $reservation->check_in_date->format('Y-m-d') }}</td><td>{{ $reservation->status }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- Expected Departures --}}
@if($reportType === 'expected_departures')
<div class="english-text">
    <h4 class="text-center">{{ __('dashboard.expected_departures_report') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.reservation_no') }}</th><th>{{ __('dashboard.guest') }}</th><th>{{ __('dashboard.check_out') }}</th><th>{{ __('dashboard.status') }}</th></tr></thead>
        <tbody>
            @forelse($reservations as $reservation)
                <tr><td>{{ $reservation->reservation_number }}</td><td>{{ $reservation->guest->first_name ?? '-' }} {{ $reservation->guest->last_name ?? '' }}</td><td>{{ $reservation->check_out_date->format('Y-m-d') }}</td><td>{{ $reservation->status }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center">{{ __('dashboard.expected_departures_report_ar') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.reservation_no_ar') }}</th><th>{{ __('dashboard.guest_ar') }}</th><th>{{ __('dashboard.check_out_ar') }}</th><th>{{ __('dashboard.status_ar') }}</th></tr></thead>
        <tbody>
            @forelse($reservations as $reservation)
                <tr><td>{{ $reservation->reservation_number }}</td><td>{{ $reservation->guest->first_name ?? '-' }} {{ $reservation->guest->last_name ?? '' }}</td><td>{{ $reservation->check_out_date->format('Y-m-d') }}</td><td>{{ $reservation->status }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- Night Audit Summary --}}
@if($reportType === 'night_audit_summary')
<div class="english-text">
    <h4 class="text-center">{{ __('dashboard.night_audit_summary_report') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered mb-3">
        <tr><td>{{ __('dashboard.total') }}</td><td class="text-center">{{ $summary['total'] ?? 0 }}</td></tr>
        <tr><td>{{ __('dashboard.completed') }}</td><td class="text-center">{{ $summary['completed'] ?? 0 }}</td></tr>
        <tr><td>{{ __('dashboard.pending') }}</td><td class="text-center">{{ $summary['pending'] ?? 0 }}</td></tr>
        <tr><td>{{ __('dashboard.failed') }}</td><td class="text-center">{{ $summary['failed'] ?? 0 }}</td></tr>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center">{{ __('dashboard.night_audit_summary_report_ar') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered mb-3">
        <tr><td>{{ __('dashboard.total_ar') }}</td><td class="text-center">{{ $summary['total'] ?? 0 }}</td></tr>
        <tr><td>{{ __('dashboard.completed_ar') }}</td><td class="text-center">{{ $summary['completed'] ?? 0 }}</td></tr>
        <tr><td>{{ __('dashboard.pending_ar') }}</td><td class="text-center">{{ $summary['pending'] ?? 0 }}</td></tr>
        <tr><td>{{ __('dashboard.failed_ar') }}</td><td class="text-center">{{ $summary['failed'] ?? 0 }}</td></tr>
    </table>
</div>
@endif

{{-- Night Audit History --}}
@if($reportType === 'night_audit_history')
<div class="english-text">
    <h4 class="text-center">{{ __('dashboard.night_audit_history_report') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.date_time') }}</th><th>{{ __('dashboard.user') }}</th><th>{{ __('dashboard.status') }}</th><th>{{ __('dashboard.description') }}</th></tr></thead>
        <tbody>
            @forelse($audits as $audit)
                <tr><td>{{ $audit->start_date_time->format('Y-m-d H:i') }}</td><td>{{ $audit->user->name ?? '-' }}</td><td>{{ $audit->status }}</td><td>{{ $audit->description ?? '-' }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center">{{ __('dashboard.night_audit_history_report_ar') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.date_time_ar') }}</th><th>{{ __('dashboard.user_ar') }}</th><th>{{ __('dashboard.status_ar') }}</th><th>{{ __('dashboard.description_ar') }}</th></tr></thead>
        <tbody>
            @forelse($audits as $audit)
                <tr><td>{{ $audit->start_date_time->format('Y-m-d H:i') }}</td><td>{{ $audit->user->name ?? '-' }}</td><td>{{ $audit->status }}</td><td>{{ $audit->description ?? '-' }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- Housekeeping Status --}}
@if($reportType === 'housekeeping_status')
<div class="english-text">
    <h4 class="text-center">{{ __('dashboard.housekeeping_status_report') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.room_no') }}</th><th>{{ __('dashboard.floor') }}</th><th>{{ __('dashboard.room_type') }}</th><th>{{ __('dashboard.status') }}</th></tr></thead>
        <tbody>
            @forelse($units as $unit)
                <tr><td>{{ $unit->room_number ?? '-' }}</td><td>{{ $unit->floor->name ?? '-' }}</td><td>{{ $unit->unitType->name ?? '-' }}</td><td>{{ $unit->housekeeping_status ?? '-' }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="arabic-text">
    <h4 class="text-center">{{ __('dashboard.housekeeping_status_report_ar') }}</h4>
    <p class="text-center">{{ __('dashboard.date_from_ar') }}: {{ $filters['date_from'] }} | {{ __('dashboard.date_to_ar') }}: {{ $filters['date_to'] }}</p>
    <table class="table table-bordered">
        <thead><tr><th>{{ __('dashboard.room_no') }}</th><th>{{ __('dashboard.floor_ar') }}</th><th>{{ __('dashboard.room_type_ar') }}</th><th>{{ __('dashboard.hk_status_ar') }}</th></tr></thead>
        <tbody>
            @forelse($units as $unit)
                <tr><td>{{ $unit->room_number ?? '-' }}</td><td>{{ $unit->floor->name ?? '-' }}</td><td>{{ $unit->unitType->name ?? '-' }}</td><td>{{ $unit->housekeeping_status ?? '-' }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center">{{ __('dashboard.no_records_found_ar') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

@endsection

@push('scripts')
<script>
    function switchLanguage(lang) {
        document.documentElement.classList.remove('lang-ar', 'lang-both');
        if (lang === 'ar') {
            document.documentElement.classList.add('lang-ar');
            document.documentElement.style.direction = 'rtl';
            document.documentElement.lang = 'ar';
        } else if (lang === 'both') {
            document.documentElement.classList.add('lang-both');
            document.documentElement.style.direction = 'ltr';
            document.documentElement.lang = 'en';
        } else {
            document.documentElement.style.direction = 'ltr';
            document.documentElement.lang = 'en';
        }
    }
</script>
@endpush
