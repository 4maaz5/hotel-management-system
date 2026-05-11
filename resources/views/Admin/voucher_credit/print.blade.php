<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credit Note {{ $creditNote->credit_note_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        @media print { .no-print { display: none !important; } body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .print-container { max-width: 800px; margin: 0 auto; background: #fff; padding: 30px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .voucher-header { display: flex; justify-content: space-between; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #4a6cf7; }
        .voucher-title { font-size: 28px; font-weight: bold; color: #4a6cf7; }
        .arabic-text { direction: rtl; unicode-bidi: embed; text-align: right; display: none; }
        .english-text { direction: ltr; unicode-bidi: embed; text-align: left; }
        .lang-ar .arabic-text { display: inline; }
        .lang-ar .english-text { display: none; }
        .box { border: 1px solid #000; padding: 15px; margin-bottom: 20px; }
        .box-title { font-weight: bold; border-bottom: 2px solid #4a6cf7; padding-bottom: 5px; margin-bottom: 10px; font-size: 16px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table th, .info-table td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .info-table th { background: #f8f9fa; font-weight: 600; }
        .amount-box { background: #f8f9fa; padding: 20px; text-align: center; border-radius: 10px; margin: 20px 0; }
        .amount-label { font-size: 14px; color: #6c757d; }
        .amount-value { font-size: 32px; font-weight: bold; color: #4a6cf7; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; font-size: 12px; color: #6c757d; }
        .invoice-type-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .invoice-type-b2b { background: #e7f3ff; color: #004085; }
        .invoice-type-b2c { background: #e8f5e9; color: #155724; }
        .qr-section {
            margin-top: 30px;
            text-align: center;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
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
            <div>
                <strong>{{ optional($property)->property_name_ar ?? 'Property Name' }}</strong><br>
                {{ optional($property)->address_ar ?? 'Address' }}<br>
                {{ optional($property)->property_code ?? '' }} | {{ optional($property)->report_name_en ?? '' }}<br>
                {{ optional($property)->phone ?? '' }}<br>
                VAT: {{ optional($property->commercialDetail)->vat_registration_number ?? '' }}
            </div>
        </div>
        @endif

        <div class="voucher-header">
            <div>
                <div class="voucher-title">
                    <span class="english-text">CREDIT NOTE</span>
                    <span class="arabic-text">إشعار دائن</span>
                </div>
            </div>
            <div class="text-end">
                <strong>{{ $creditNote->credit_note_number }}</strong><br>
                <span class="english-text">Date: {{ $creditNote->cn_date ? $creditNote->cn_date->format('d-m-Y') : '-' }}</span>
                <span class="arabic-text">التاريخ: {{ $creditNote->cn_date ? $creditNote->cn_date->format('d-m-Y') : '-' }}</span><br>
                <span class="invoice-type-badge {{ $creditNote->invoice_type === 'B2B' ? 'invoice-type-b2b' : 'invoice-type-b2c' }}">
                    {{ $creditNote->invoice_type }}
                </span>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <h6>
                    <span class="english-text">Bill To:</span>
                    <span class="arabic-text">فاتورة إلى:</span>
                </h6>
                <p class="mb-0">
                    <strong>
                        <span class="english-text">{{ $creditNote->guest->first_name ?? '' }} {{ $creditNote->guest->last_name ?? '' }}</span>
                        <span class="arabic-text">{{ $creditNote->guest->first_name ?? '' }} {{ $creditNote->guest->last_name ?? '' }}</span>
                    </strong><br>
                    <span class="english-text">{{ $creditNote->guest->email ?? '-' }}</span>
                    <span class="arabic-text">{{ $creditNote->guest->email ?? '-' }}</span><br>
                    <span class="english-text">{{ $creditNote->guest->phone ?? '-' }}</span>
                    <span class="arabic-text">{{ $creditNote->guest->phone ?? '-' }}</span>
                </p>
            </div>
            <div class="col-md-6 text-end">
                <p>
                    <span class="english-text"><strong>Unit:</strong> {{ $creditNote->reservation->unit->unit_number ?? '-' }}</span>
                    <span class="arabic-text"><strong>الوحدة:</strong> {{ $creditNote->reservation->unit->unit_number ?? '-' }}</span><br>
                    <span class="english-text"><strong>Check-in:</strong> {{ $creditNote->period_from ? $creditNote->period_from->format('d-m-Y') : '-' }}</span>
                    <span class="arabic-text"><strong>تسجيل الدخول:</strong> {{ $creditNote->period_from ? $creditNote->period_from->format('d-m-Y') : '-' }}</span><br>
                    <span class="english-text"><strong>Check-out:</strong> {{ $creditNote->period_to ? $creditNote->period_to->format('d-m-Y') : '-' }}</span>
                    <span class="arabic-text"><strong>تسجيل الخروج:</strong> {{ $creditNote->period_to ? $creditNote->period_to->format('d-m-Y') : '-' }}</span>
                </p>
            </div>
        </div>

        <table class="info-table">
            <tr>
                <th data-en="Reservation / Order No." data-ar="رقم الحجز / الطلب">Reservation / Order No.</th>
                <td>{{ $creditNote->reservation->reservation_number ?? '-' }}</td>
            </tr>
            <tr>
                <th data-en="Outlet" data-ar="النقطة">Outlet</th>
                <td>{{ $creditNote->outlet->name ?? '-' }}</td>
            </tr>
            <tr>
                <th data-en="Invoice No." data-ar="رقم الفاتورة">Invoice No.</th>
                <td>{{ $creditNote->invoice_number ?? '-' }}</td>
            </tr>
        </table>

        <div class="amount-box">
            <div class="amount-label"><span class="english-text" data-en="Credit Amount" data-ar="مبلغ الائتمان">Credit Amount</span><span class="arabic-text" data-en="Credit Amount" data-ar="مبلغ الائتمان">مبلغ الائتمان</span></div>
            <div class="amount-value">{{ number_format($creditNote->amount, 2) }} SAR</div>
        </div>

        @if($creditNote->qr_code)
        <div class="qr-section">
            <div id="qrcode"></div>
            <p class="text-muted mb-0">
                <span class="english-text">ZATCA QR Code</span>
                <span class="arabic-text">رمز QR للزاتكا</span>
            </p>
        </div>
        @endif

        <div class="footer">
            <p class="mb-0"><span class="english-text" data-en="Thank you!" data-ar="شكرا لتعاملكم معنا!">Thank you!</span><span class="arabic-text" data-en="Thank you!" data-ar="شكرا لتعاملكم معنا!">شكرا لتعاملكم معنا!</span></p>
            <p class="mb-0"><span class="english-text" data-en="Generated on" data-ar="تم الإنشاء في">Generated on</span>: {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>

    <script>
        var currentLang = 'en';
        function switchLanguage(lang) {
            currentLang = lang;
            const content = document.getElementById('printContent');
            if (!content) return;
            content.classList.remove('lang-ar');
            
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
        window.onload = function() {
            switchLanguage('en');

            @if($creditNote->qr_code)
            new QRCode(document.getElementById("qrcode"), {
                text: "{{ $creditNote->qr_code }}",
                width: 128,
                height: 128
            });
            @endif
        };
    </script>
</body>
</html>
