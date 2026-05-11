@extends('layouts.app')

@section('title', __('dashboard.cash_drawer_balance'))

@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0">{{ __('dashboard.cash_drawer_balance') }}</h2>
                    <small class="text-muted">{{ __('dashboard.you_can_deposit_withdraw_and_drop_cash_from_here') }}</small>
                </div>
                <div class="d-flex gap-2">
                    <form method="GET" action="{{ route('dashboard.cash_drawer.index') }}" class="d-flex gap-2">
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate ?? '' }}">
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate ?? '' }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    <button class="btn btn-primary" onclick="openPrintModal()">
                        <i class="fas fa-print"></i>
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-purple d-flex align-items-center dropdown-toggle" type="button"
                            data-bs-toggle="dropdown">
                            {{ __('dashboard.more_actions') }} <i class="fas fa-caret-down ms-1"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('dashboard.cash_drawer.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}">
                                    <i class="fas fa-file-export me-2"></i>{{ __('dashboard.export') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <div class="row fw-bold border-bottom pb-2 mb-3">
                        <div class="col-md-2 text-center">{{ __('dashboard.transactions') }}</div>
                        <div class="col-md-2">{{ __('dashboard.count') }}</div>
                        <div class="col-md-8">{{ __('dashboard.amount') }} <i class="fas fa-sync-alt ms-2 text-muted"></i></div>
                    </div>

                    <div class="row align-items-center py-1 border-bottom">
                        <div class="col-md-2 text-center">{{ __('dashboard.cash_received') }}</div>
                        <div class="col-md-2">{{ $cashReceivedCount }}</div>
                        <div class="col-md-8 fw-semibold text-success">SAR {{ number_format($cashReceived, 2) }}</div>
                    </div>

                    <div class="row align-items-center py-1 border-bottom">
                        <div class="col-md-2 text-center">{{ __('dashboard.security_deposit_received') }}</div>
                        <div class="col-md-2">{{ $securityDepositCount }}</div>
                        <div class="col-md-8 fw-semibold text-success">SAR {{ number_format($securityDepositsReceived, 2) }}</div>
                    </div>

                    <div class="row align-items-center py-1 border-bottom">
                        <div class="col-md-2">{{ __('dashboard.cash_paid_out') }}</div>
                        <div class="col-md-2">{{ $cashPaidOutCount }}</div>
                        <div class="col-md-8 fw-semibold text-danger">SAR {{ number_format($cashPaidOut, 2) }}</div>
                    </div>

                    <div class="row align-items-center py-1 border-bottom">
                        <div class="col-md-2">{{ __('dashboard.security_deposit_paid_out') }}</div>
                        <div class="col-md-2">{{ $securityDepositPaidCount }}</div>
                        <div class="col-md-8 fw-semibold text-danger">SAR {{ number_format($securityDepositsPaidOut, 2) }}</div>
                    </div>

                    <div class="row align-items-center py-1 border-bottom">
                        <div class="col-md-2">{{ __('dashboard.drop_cash_vouchers') }}</div>
                        <div class="col-md-2">{{ $dropCashCount }}</div>
                        <div class="col-md-8 fw-semibold text-danger">SAR {{ number_format($dropCashTotal, 2) }}</div>
                    </div>

                    <div class="row pt-2 mt-2 border-top">
                        <div class="col-md-2 text-center fw-bold">{{ __('dashboard.current_balance') }}
                            <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Cash Received - Cash Paid Out - Drop Cash"></i>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-8 fw-bold text-primary fs-5">SAR {{ number_format($currentBalance, 2) }}</div>
                    </div>
                </div>
            </div>

            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('dashboard.cash_drawer_balance') }}</h5>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse"
                        data-bs-target="#cashDrawerCollapse" aria-expanded="false" aria-controls="cashDrawerCollapse">
                        {{ __('dashboard.expand') }} <i class="fas fa-chevron-down ms-1"></i>
                    </button>
                </div>

                <div class="collapse" id="cashDrawerCollapse">
                    <div class="card-body">
                        <ul class="cash-drawer-tabs mb-3" id="cashDrawerTabs" role="tablist">
                            <li class="cash-drawer-tab-item" role="presentation">
                                <button class="cash-drawer-tab-link active" id="transactions-tab" data-bs-toggle="tab"
                                    data-bs-target="#transactions" type="button" role="tab"
                                    aria-controls="transactions" aria-selected="true">
                                    {{ __('dashboard.transactions') }}
                                </button>
                            </li>
                            <li class="cash-drawer-tab-item" role="presentation">
                                <button class="cash-drawer-tab-link" id="vouchers-tab" data-bs-toggle="tab" data-bs-target="#vouchers"
                                    type="button" role="tab" aria-controls="vouchers" aria-selected="false">
                                    {{ __('dashboard.vouchers') }}
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="cashDrawerTabContent">
                            <div class="tab-pane fade show active" id="transactions" role="tabpanel"
                                aria-labelledby="transactions-tab">
                                <table class="table table-striped table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('dashboard.transaction') }}</th>
                                            <th>{{ __('dashboard.count') }}</th>
                                            <th>{{ __('dashboard.amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ __('dashboard.cash_received') }}</td>
                                            <td>{{ $cashReceivedCount }}</td>
                                            <td class="text-success fw-semibold">SAR {{ number_format($cashReceived, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('dashboard.security_deposit_received') }}</td>
                                            <td>{{ $securityDepositCount }}</td>
                                            <td class="text-success fw-semibold">SAR {{ number_format($securityDepositsReceived, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('dashboard.cash_paid_out') }}</td>
                                            <td>{{ $cashPaidOutCount }}</td>
                                            <td class="text-danger fw-semibold">SAR {{ number_format($cashPaidOut, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('dashboard.security_deposit_paid_out') }}</td>
                                            <td>{{ $securityDepositPaidCount }}</td>
                                            <td class="text-danger fw-semibold">SAR {{ number_format($securityDepositsPaidOut, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('dashboard.drop_cash_vouchers') }}</td>
                                            <td>{{ $dropCashCount }}</td>
                                            <td class="text-danger fw-semibold">SAR {{ number_format($dropCashTotal, 2) }}</td>
                                        </tr>
                                        <tr class="table-primary">
                                            <td class="fw-bold">{{ __('dashboard.current_balance') }}</td>
                                            <td>-</td>
                                            <td class="fw-bold text-primary">SAR {{ number_format($currentBalance, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="tab-pane fade" id="vouchers" role="tabpanel" aria-labelledby="vouchers-tab">
                                <table class="table table-striped table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('dashboard.voucher_no') }}.</th>
                                            <th>{{ __('dashboard.type') }}</th>
                                            <th>{{ __('dashboard.payment_method') }}</th>
                                            <th>{{ __('dashboard.amount') }}</th>
                                            <th>{{ __('dashboard.date_time') }}</th>
                                            <th>{{ __('dashboard.paid_to') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentVouchers as $voucher)
                                            <tr>
                                                <td>{{ $voucher->voucher_number }}</td>
                                                <td>
                                                    @if(isset($voucher->voucher_type))
                                                        <span class="badge bg-{{ $voucher->voucher_type === 'refund' ? 'warning' : 'info' }}">
                                                            {{ ucfirst($voucher->voucher_type) }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-success">Receipt</span>
                                                    @endif
                                                </td>
                                                <td>{{ $voucher->paymentMethod->paymentMethod->name ?? 'N/A' }}</td>
                                                <td>SAR {{ number_format($voucher->amount, 2) }}</td>
                                                <td>{{ $voucher->date->format('Y/m/d') }} {{ $voucher->time ? $voucher->time->format('h:i A') : '' }}</td>
                                                <td>{{ $voucher->received_from_name ?? $voucher->vendor_name ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">{{ __('dashboard.no_vouchers_found') }}</td>
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
    </main>

<!-- Print Modal -->
<div class="modal fade" id="printModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header no-print">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary" onclick="switchPrintLang('en')">English</button>
                    <button type="button" class="btn btn-outline-primary" onclick="switchPrintLang('ar')">Arabic</button>
                    <button type="button" class="btn btn-outline-primary" onclick="switchPrintLang('both')">Both</button>
                </div>
                    <div class="btn-group ms-3" role="group">
                    <button type="button" class="btn btn-primary" onclick="printCashDrawer()"><i class="fas fa-print"></i> Print</button>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                @php
                    $letterHead = $printingOption?->letter_head ?? $globalSetting?->letter_head ?? false;
                    $blankPaper = $printingOption?->blank_paper ?? $globalSetting?->blank_paper ?? false;
                    $showPropertyInfo = $letterHead && !$blankPaper;
                @endphp

                @if($showPropertyInfo)
                <div class="property-info-box english-text mb-4 p-3 border rounded">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>{{ optional($property)->property_name_en ?? 'Onara Stay' }}</strong><br>
                            {{ optional($property)->address_en ?? 'King Fahd Road - Madinah' }}<br>
                            {{ optional($property)->property_code ?? '1100' }} | {{ optional($property)->report_name_en ?? 'B-IT' }}<br>
                            {{ optional($property)->phone ?? '+966 14 124585858' }}<br>
                            VAT: {{ optional($property->commercialDetail)->vat_registration_number ?? '333333333338333' }}
                        </div>
                        <div class="text-end">
                            <small class="text-muted">{{ now()->format('Y-m-d H:i') }}</small>
                        </div>
                    </div>
                </div>
                <div class="property-info-box arabic-text mb-4 p-3 border rounded" style="display: none;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>{{ optional($property)->property_name_ar ?? 'أونارا استاي' }}</strong><br>
                            {{ optional($property)->address_ar ?? 'المدينة المنورة - الملك فهد' }}<br>
                            {{ optional($property)->property_code ?? '1100' }} | {{ optional($property)->report_name_en ?? 'B-IT' }}<br>
                            {{ optional($property)->phone ?? '+966 14 124585858' }}<br>
                            VAT: {{ optional($property->commercialDetail)->vat_registration_number ?? '333333333338333' }}
                        </div>
                        <div class="text-start">
                            <small class="text-muted">{{ now()->format('Y-m-d H:i') }}</small>
                        </div>
                    </div>
                </div>
                @endif

                <div class="english-text">
                    <div style="text-align: center; margin-bottom: 0.5rem;">
                        <h3>Cash Drawer Balance</h3>
                        <p class="text-muted mb-0">{{ $startDate ?? '' }} to {{ $endDate ?? '' }}</p>
                    </div>

                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50%;">Transactions</th>
                                <th class="text-center" style="width: 15%;">Count</th>
                                <th class="text-end" style="width: 35%;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Cash Received</td>
                                <td class="text-center">{{ $cashReceivedCount }}</td>
                                <td class="text-end">SAR {{ number_format($cashReceived, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Security Deposit Received</td>
                                <td class="text-center">{{ $securityDepositCount }}</td>
                                <td class="text-end">SAR {{ number_format($securityDepositsReceived, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Cash Paid Out</td>
                                <td class="text-center">{{ $cashPaidOutCount }}</td>
                                <td class="text-end">SAR {{ number_format($cashPaidOut, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Security Deposit Paid Out</td>
                                <td class="text-center">{{ $securityDepositPaidCount }}</td>
                                <td class="text-end">SAR {{ number_format($securityDepositsPaidOut, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Drop Cash Vouchers</td>
                                <td class="text-center">{{ $dropCashCount }}</td>
                                <td class="text-end">SAR {{ number_format($dropCashTotal, 2) }}</td>
                            </tr>
                            <tr class="table-primary fw-bold">
                                <td>Current Balance</td>
                                <td class="text-center">-</td>
                                <td class="text-end">SAR {{ number_format($currentBalance, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="arabic-text">
                    <div style="text-align: center; margin-bottom: 0.5rem;">
                        <h3>رصيد درج النقد</h3>
                        <p class="text-muted mb-0">{{ $startDate ?? '' }} إلى {{ $endDate ?? '' }}</p>
                    </div>

                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50%; text-align: right;">المعاملات</th>
                                <th class="text-center" style="width: 15%;">العدد</th>
                                <th class="text-end" style="width: 35%;">المبلغ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: right;">النقد المستلم</td>
                                <td class="text-center">{{ $cashReceivedCount }}</td>
                                <td class="text-end">ر.س {{ number_format($cashReceived, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">الضمان المستلم</td>
                                <td class="text-center">{{ $securityDepositCount }}</td>
                                <td class="text-end">ر.س {{ number_format($securityDepositsReceived, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">النقد المدفوع</td>
                                <td class="text-center">{{ $cashPaidOutCount }}</td>
                                <td class="text-end">ر.س {{ number_format($cashPaidOut, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">الضمان المدفوع</td>
                                <td class="text-center">{{ $securityDepositPaidCount }}</td>
                                <td class="text-end">ر.س {{ number_format($securityDepositsPaidOut, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">قسائم إسقاط النقد</td>
                                <td class="text-center">{{ $dropCashCount }}</td>
                                <td class="text-end">ر.س {{ number_format($dropCashTotal, 2) }}</td>
                            </tr>
                            <tr class="table-primary fw-bold">
                                <td style="text-align: right;">الرصيد الحالي</td>
                                <td class="text-center">-</td>
                                <td class="text-end">ر.س {{ number_format($currentBalance, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Default screen view - show only English */
    .modal-body .english-text { display: block; direction: ltr; }
    .modal-body .arabic-text { display: none; direction: rtl; }

    /* Arabic mode - show only Arabic */
    .lang-ar .modal-body .english-text { display: none !important; }
    .lang-ar .modal-body .arabic-text { display: block !important; }

    /* Both mode - show both */
    .lang-both .modal-body .english-text { display: block !important; }
    .lang-both .modal-body .arabic-text { display: block !important; }

    .lang-both .english-text {
        margin-bottom: 3rem;
        padding-bottom: 2rem;
        border-bottom: 2px solid #dee2e6;
    }

    .lang-both .arabic-text {
        margin-top: 1rem;
    }

    /* RTL styles for Arabic mode */
    .lang-ar .modal-body {
        direction: rtl;
    }

    .lang-ar .modal-body .property-info-box,
    .lang-ar .modal-body .english-text,
    .lang-ar .modal-body .arabic-text {
        direction: rtl;
        text-align: right;
    }

    .lang-ar .modal-body .arabic-text {
        direction: rtl;
    }

    .lang-ar .modal-body .english-text {
        direction: ltr;
    }

    .lang-ar .modal-body .table th,
    .lang-ar .modal-body .table td {
        text-align: right;
    }

    .lang-ar .modal-body .text-end {
        text-align: left !important;
    }

    .lang-ar .modal-body .text-start {
        text-align: right !important;
    }

    /* Cash Drawer Custom Tabs - Override Global Styles */
    #cashDrawerTabs.cash-drawer-tabs {
        list-style: none !important;
        padding: 0 !important;
        margin: 0 !important;
        display: flex !important;
        gap: 0 !important;
        border-bottom: 2px solid #dee2e6 !important;
    }

    #cashDrawerTabs .cash-drawer-tab-item {
        list-style: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    #cashDrawerTabs .cash-drawer-tab-link {
        display: block !important;
        padding: 10px 20px !important;
        text-decoration: none !important;
        color: #6c757d !important;
        background: transparent !important;
        border: none !important;
        border-bottom: 2px solid transparent !important;
        margin-bottom: -2px !important;
        transition: all 0.2s ease !important;
        font-weight: 500 !important;
        border-radius: 0 !important;
        cursor: pointer !important;
    }

    #cashDrawerTabs .cash-drawer-tab-link:hover {
        color: #4a6cf7 !important;
        background: #f8f9fa !important;
    }

    #cashDrawerTabs .cash-drawer-tab-link.active {
        color: #4a6cf7 !important;
        background: transparent !important;
        border-bottom: 2px solid #4a6cf7 !important;
        font-weight: 600 !important;
    }

    #cashDrawerTabs + .tab-content .tab-pane {
        padding-top: 15px !important;
    }

    @media print {
        /* Hide everything */
        html, body {
            display: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        /* Show print modal - hide Bootstrap modal backdrop and overlay */
        .modal-backdrop, 
        .modal {
            display: none !important;
        }
        
        /* Show print window content */
        #printModal {
            display: block !important;
            position: relative !important;
            width: 100% !important;
            height: auto !important;
            margin: 0 !important;
            padding: 0 !important;
            background: white !important;
            border: none !important;
        }
        
        #printModal .modal-dialog {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        #printModal .modal-content {
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            box-shadow: none !important;
            background: white !important;
        }
        
        #printModal .modal-header,
        #printModal .modal-footer,
        #printModal .btn-close,
        .no-print {
            display: none !important;
        }
        
        #printModal .modal-body {
            display: block !important;
            padding: 10px !important;
            margin: 0 !important;
            overflow: visible !important;
            max-height: none !important;
        }
        
        /* Language-based visibility - show only selected language content */
        #printModal .arabic-text { display: none !important; }
        #printModal .english-text { display: block !important; }
        
        .lang-en #printModal .arabic-text { display: none !important; }
        .lang-en #printModal .english-text { display: block !important; }
        
        .lang-ar #printModal .arabic-text { display: block !important; }
        .lang-ar #printModal .english-text { display: none !important; }
        
        .lang-both #printModal .arabic-text { display: block !important; }
        .lang-both #printModal .english-text { display: block !important; }
        
        /* Property info box - hide by default, show based on language */
        #printModal .property-info-box.arabic-text { display: none !important; }
        #printModal .property-info-box.english-text { display: none !important; }
        
        .lang-en #printModal .property-info-box.arabic-text { display: none !important; }
        .lang-en #printModal .property-info-box.english-text { display: block !important; }
        
        .lang-ar #printModal .property-info-box.arabic-text { display: block !important; }
        .lang-ar #printModal .property-info-box.english-text { display: none !important; }
        
        .lang-both #printModal .property-info-box.arabic-text { display: block !important; }
        .lang-both #printModal .property-info-box.english-text { display: block !important; }

        /* Spacing fixes */
        .english-text, .arabic-text {
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .property-info-box {
            margin-bottom: 15px !important;
            border-radius: 8px !important;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0 !important;
            page-break-inside: avoid;
        }

        .table th, .table td {
            border: 1px solid #000 !important;
            padding: 8px !important;
        }

        .table-light { 
            background-color: #f5f5f5 !important; 
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }

</style>

@push('scripts')
<script>
    function openPrintModal() {
        document.documentElement.classList.remove('lang-ar', 'lang-both');
        document.documentElement.classList.add('lang-en');
        var modal = new bootstrap.Modal(document.getElementById('printModal'));
        modal.show();
    }

    function switchPrintLang(lang) {
        document.documentElement.classList.remove('lang-en', 'lang-ar', 'lang-both');
        if (lang === 'en') {
            document.documentElement.classList.add('lang-en');
        } else if (lang === 'ar') {
            document.documentElement.classList.add('lang-ar');
        } else if (lang === 'both') {
            document.documentElement.classList.add('lang-both');
        }
    }

    function printCashDrawer() {
        var printContent = document.querySelector('#printModal .modal-body').cloneNode(true);
        var langClass = document.documentElement.classList.contains('lang-ar') ? 'lang-ar' : 
                       document.documentElement.classList.contains('lang-both') ? 'lang-both' : 'lang-en';
        
        var printWindow = window.open('', '_blank', 'width=800,height=600');
        printWindow.document.write('<!DOCTYPE html><html><head><title>Cash Drawer Balance</title>');
        printWindow.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">');
        printWindow.document.write('<style>');
        printWindow.document.write('body { padding: 20px; font-family: Arial, sans-serif; }');
        printWindow.document.write('.arabic-text { display: none; }');
        printWindow.document.write('.lang-ar .arabic-text { display: block; direction: rtl; text-align: right; }');
        printWindow.document.write('.lang-ar .english-text { display: none; }');
        printWindow.document.write('.lang-both .arabic-text, .lang-both .english-text { display: block; }');
        printWindow.document.write('.property-info-box { margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; }');
        printWindow.document.write('.table { width: 100%; border-collapse: collapse; margin: 15px 0; }');
        printWindow.document.write('.table th, .table td { border: 1px solid #000; padding: 8px; }');
        printWindow.document.write('.table-light { background: #f5f5f5; }');
        printWindow.document.write('@media print { body { padding: 0; } }');
        printWindow.document.write('</style></head><body class="' + langClass + '">');
        printWindow.document.write(printContent.innerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.focus();
        setTimeout(function() {
            printWindow.print();
        }, 250);
    }
</script>
@endpush
@endsection
