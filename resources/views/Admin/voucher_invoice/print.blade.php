<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .print-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #4a6cf7;
        }
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #4a6cf7;
        }
        .company-info {
            text-align: right;
        }
        .arabic-text {
            direction: rtl;
            unicode-bidi: embed;
            text-align: right;
            display: none;
        }
        .english-text {
            direction: ltr;
            unicode-bidi: embed;
            text-align: left;
        }
        .lang-ar .arabic-text { display: inline; }
        .lang-ar .english-text { display: none; }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-table th, .details-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .details-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .total-section {
            margin-top: 20px;
            text-align: right;
        }
        .total-row {
            display: flex;
            justify-content: flex-end;
            padding: 5px 0;
        }
        .total-label {
            width: 150px;
            font-weight: 600;
        }
        .total-value {
            width: 120px;
        }
        .grand-total {
            font-size: 18px;
            font-weight: bold;
            color: #4a6cf7;
            border-top: 2px solid #4a6cf7;
            padding-top: 10px;
        }
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
        }
        .status-paid { background: #d1fae5; color: #059669; }
        .status-pending { background: #fef3c7; color: #d97706; }
        .status-partial { background: #dbeafe; color: #2563eb; }
        .status-cancelled { background: #fee2e2; color: #dc2626; }

        .qr-section {
            margin-top: 30px;
            text-align: center;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        .box {
            border: 1px solid #000;
            padding: 15px;
            margin-bottom: 20px;
        }
        .box-title {
            font-weight: bold;
            border-bottom: 2px solid #4a6cf7;
            padding-bottom: 5px;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .property-lines {
            font-size: 12px;
            line-height: 1.8;
        }
        .property-line {
            margin-bottom: 3px;
        }
        .property-meta {
            margin-top: 8px;
        }
        .property-meta .arabic {
            direction: rtl;
        }
        .property-meta .ltr {
            direction: ltr;
            unicode-bidi: embed;
        }
    </style>
</head>
<body>
    <div class="print-container" id="printContent">
        @php
            $letterHead = $printingOption?->letter_head ?? $globalSetting?->letter_head ?? false;
            $blankPaper = $printingOption?->blank_paper ?? $globalSetting?->blank_paper ?? false;
            $showPropertyInfo = $letterHead && !$blankPaper;
        @endphp

        @if($showPropertyInfo)
        <div class="box">
            <div class="box-title">Property Information</div>

            <div class="property-lines">
                <div class="property-line"><strong>{{ optional($property)->property_name_ar ?? '-' }}</strong></div>
                <div class="property-line">{{ optional($property)->address_ar ?? '-' }}</div>
                <div class="property-line">{{ optional($property)->property_code ?? '-' }} | {{ optional($property)->report_name_en ?? '-' }}</div>
                <div class="property-line">{{ optional($property)->phone ?? '-' }}</div>
                <div class="property-line property-meta">
                    <span class="arabic">الرقم الضريبي: <span class="ltr">{{ optional($property->commercialDetail)->vat_registration_number ?? '-' }}
                    <br>
                    <span class="arabic">السجل التجاري: <span class="ltr">{{ optional($property->commercialDetail)->registration_number ?? '-' }}</span> :R.C</span>
                </div>
            </div>
        </div>
        @endif

        <div class="invoice-header">
            <div>
                <div class="invoice-title">
                    <span class="english-text">INVOICE</span>
                    <span class="arabic-text">فاتورة</span>
                </div>
            </div>
            <div class="text-end">
                <strong>{{ $invoice->invoice_number }}</strong><br>
                <span class="english-text">Date: {{ $invoice->issue_date->format('Y-m-d') }}</span>
                <span class="arabic-text">التاريخ: {{ $invoice->issue_date->format('Y-m-d') }}</span>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <h6>
                    <span class="english-text">Bill To:</span>
                    <span class="arabic-text">فاتورة إلى:</span>
                </h6>
                <p>
                    <strong>
                        <span class="english-text">{{ $invoice->reservation->guest->first_name ?? '' }} {{ $invoice->reservation->guest->last_name ?? '' }}</span>
                        <span class="arabic-text">{{ $invoice->reservation->guest->first_name ?? '' }} {{ $invoice->reservation->guest->last_name ?? '' }}</span>
                    </strong><br>
                    <span class="english-text">{{ $invoice->reservation->guest->email ?? '' }}</span>
                    <span class="arabic-text">{{ $invoice->reservation->guest->email ?? '' }}</span><br>
                    <span class="english-text">{{ $invoice->reservation->guest->phone ?? '' }}</span>
                    <span class="arabic-text">{{ $invoice->reservation->guest->phone ?? '' }}</span>
                </p>
            </div>
            <div class="col-md-6 text-end">
                <p>
                    <span class="english-text"><strong>Unit:</strong> {{ $invoice->reservation->unit->unit_number ?? '-' }}</span>
                    <span class="arabic-text"><strong>الوحدة:</strong> {{ $invoice->reservation->unit->unit_number ?? '-' }}</span><br>
                    <span class="english-text"><strong>Check-in:</strong> {{ $invoice->reservation->check_in_date ?? '-' }}</span>
                    <span class="arabic-text"><strong>تسجيل الدخول:</strong> {{ $invoice->reservation->check_in_date ?? '-' }}</span><br>
                    <span class="english-text"><strong>Check-out:</strong> {{ $invoice->reservation->check_out_date ?? '-' }}</span>
                    <span class="arabic-text"><strong>تسجيل الخروج:</strong> {{ $invoice->reservation->check_out_date ?? '-' }}</span><br>
                    <span class="english-text"><strong>Nights:</strong> {{ $invoice->reservation->nights ?? 0 }}</span>
                    <span class="arabic-text"><strong>الليالي:</strong> {{ $invoice->reservation->nights ?? 0 }}</span>
                </p>
                <p>
                    <span class="english-text"><strong>Status:</strong> </span>
                    <span class="arabic-text"><strong>الحالة:</strong> </span>
                    <span class="status-badge status-{{ $invoice->status }}">
                        <span class="english-text">{{ ucfirst($invoice->status) }}</span>
                        <span class="arabic-text">
                            @if($invoice->status == 'paid') مدفوع
                            @elseif($invoice->status == 'pending') قيد الانتظار
                            @elseif($invoice->status == 'partial') جزئي
                            @else ملغي
                            @endif
                        </span>
                    </span>
                </p>
            </div>
        </div>

        <table class="details-table">
            <thead>
                <tr>
                    <th data-en="Description" data-ar="الوصف">Description</th>
                    <th data-en="Qty" data-ar="الكمية" style="width: 80px;">Qty</th>
                    <th data-en="Unit Price" data-ar="سعر الوحدة" style="width: 120px;">Unit Price</th>
                    <th data-en="Total" data-ar="المجموع" style="width: 120px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>
                        <span class="english-text">{{ $item->description }}</span>
                        <span class="arabic-text">{{ $item->description }}</span>
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end">{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                <div class="total-label">
                    <span class="english-text">Subtotal:</span>
                    <span class="arabic-text">المجموع الفرعي:</span>
                </div>
                <div class="total-value">{{ number_format($invoice->subtotal, 2) }}</div>
            </div>
            @if($invoice->discount_amount > 0)
            <div class="total-row">
                <div class="total-label">
                    <span class="english-text">Discount:</span>
                    <span class="arabic-text">الخصم:</span>
                </div>
                <div class="total-value">-{{ number_format($invoice->discount_amount, 2) }}</div>
            </div>
            @endif
            @if($invoice->tax_amount > 0)
            <div class="total-row">
                <div class="total-label">
                    {{-- <span class="english-text">VAT ({{ $invoice->reservation->property->country == 'SA' ? '15%' : '' }}):</span> --}}
                    <span class="arabic-text">الضريبة:</span>
                </div>
                <div class="total-value">{{ number_format($invoice->tax_amount, 2) }}</div>
            </div>
            @endif
            @if($invoice->security_deposit > 0)
            <div class="total-row">
                <div class="total-label">
                    <span class="english-text">Security Deposit:</span>
                    <span class="arabic-text">الوديعة الأمنية:</span>
                </div>
                <div class="total-value">{{ number_format($invoice->security_deposit, 2) }}</div>
            </div>
            @endif
            <div class="total-row grand-total">
                <div class="total-label">
                    <span class="english-text">Total:</span>
                    <span class="arabic-text">الإجمالي:</span>
                </div>
                <div class="total-value">{{ number_format($invoice->total, 2) }} SAR</div>
            </div>
            @if($invoice->paid_amount > 0)
            <div class="total-row">
                <div class="total-label">
                    <span class="english-text">Paid:</span>
                    <span class="arabic-text">المدفوع:</span>
                </div>
                <div class="total-value">-{{ number_format($invoice->paid_amount, 2) }}</div>
            </div>
            <div class="total-row grand-total" style="color: #dc2626;">
                <div class="total-label">
                    <span class="english-text">Balance Due:</span>
                    <span class="arabic-text">الرصيد المستحق:</span>
                </div>
                <div class="total-value">{{ number_format($invoice->balance, 2) }} SAR</div>
            </div>
            @endif
        </div>

        @if($invoice->qr_code)
        <div class="qr-section">
            <div id="qrcode"></div>
            <p class="text-muted mb-0">
                <span class="english-text">ZATCA QR Code</span>
                <span class="arabic-text">رمز QR للزاتكا</span>
            </p>
        </div>
        @endif

        @if($invoice->notes)
        <div class="mt-3 p-3 bg-light rounded">
            <strong>
                <span class="english-text">Notes:</span>
                <span class="arabic-text">ملاحظات:</span>
            </strong>
            <p class="mb-0">{{ $invoice->notes }}</p>
        </div>
        @endif

        <div class="footer">
            <p class="mb-0">
                <span class="english-text">Thank you for your business!</span>
                <span class="arabic-text">شكرا لتعاملكم معنا!</span>
            </p>
            <p class="mb-0">
                <span class="english-text">Generated on: {{ now()->format('Y-m-d H:i:s') }}</span>
                <span class="arabic-text">تاريخ الإنشاء: {{ now()->format('Y-m-d H:i:s') }}</span>
            </p>
        </div>
    </div>

    <script>
        var currentLang = 'en';

        function switchLanguage(lang) {
            currentLang = lang;
            const content = document.getElementById('printContent');

            if (!content) return;

            content.classList.remove('lang-ar');

            // Handle elements with english-text and arabic-text classes
            if (lang === 'ar') {
                content.classList.add('lang-ar');
                document.documentElement.lang = 'ar';
                document.documentElement.dir = 'rtl';
                document.querySelectorAll('.english-text').forEach(el => el.style.display = 'none');
                document.querySelectorAll('.arabic-text').forEach(el => el.style.display = 'inline');
            } else if (lang === 'both') {
                document.documentElement.lang = 'en';
                document.documentElement.dir = 'ltr';
                document.querySelectorAll('.english-text').forEach(el => el.style.display = 'inline');
                document.querySelectorAll('.arabic-text').forEach(el => el.style.display = 'inline');
            } else {
                document.documentElement.lang = 'en';
                document.documentElement.dir = 'ltr';
                document.querySelectorAll('.english-text').forEach(el => el.style.display = 'inline');
                document.querySelectorAll('.arabic-text').forEach(el => el.style.display = 'none');
            }

            // Handle table headers with data-en and data-ar attributes
            document.querySelectorAll('th[data-en]').forEach(th => {
                if (lang === 'ar' && th.getAttribute('data-ar')) {
                    th.textContent = th.getAttribute('data-ar');
                } else if (lang === 'both') {
                    th.textContent = th.getAttribute('data-en') + ' / ' + th.getAttribute('data-ar');
                } else {
                    th.textContent = th.getAttribute('data-en');
                }
            });
        }

        function printContract() {
            window.print();
        }

        window.onload = function() {
            switchLanguage('en');

            @if($invoice->qr_code)
            // Generate QR code
            new QRCode(document.getElementById("qrcode"), {
                text: "{{ $invoice->qr_code }}",
                width: 128,
                height: 128
            });
            @endif
        };
    </script>
</body>
</html>
