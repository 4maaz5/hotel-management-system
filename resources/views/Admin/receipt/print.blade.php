<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt Voucher {{ $voucher->voucher_number }}</title>
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
        .voucher-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #4a6cf7;
        }
        .voucher-title {
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
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table th, .info-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .info-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .amount-box {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 10px;
            margin: 20px 0;
        }
        .amount-label {
            font-size: 14px;
            color: #6c757d;
        }
        .amount-value {
            font-size: 32px;
            font-weight: bold;
            color: #4a6cf7;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
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
                <div class="property-line"><strong>{{ optional($property)->property_name_ar ?? 'أونارا استاي' }}</strong></div>
                <div class="property-line">{{ optional($property)->address_ar ?? 'المدينة المنورة - الملك فهد' }}</div>
                <div class="property-line">{{ optional($property)->property_code ?? '1100' }} | {{ optional($property)->report_name_en ?? 'B-IT' }}</div>
                <div class="property-line">{{ optional($property)->phone ?? '+966 14 124585858' }}</div>
                <div class="property-line">
                    <span class="arabic">الرقم الضريبي: <span class="ltr">{{ optional($property->commercialDetail)->vat_registration_number ?? '333333333338333' }}</span></span>
                </div>
            </div>
        </div>
        @endif

        <div class="voucher-header">
            <div>
                <div class="voucher-title">
                    <span class="english-text">RECEIPT VOUCHER</span>
                    <span class="arabic-text">سند قبض</span>
                </div>
            </div>
            <div class="text-end">
                <strong>{{ $voucher->voucher_number }}</strong><br>
                <span class="english-text">Date: {{ $voucher->date }}</span>
                <span class="arabic-text">التاريخ: {{ $voucher->date }}</span>
            </div>
        </div>

        <table class="info-table">
            <tr>
                <th data-en="Received From" data-ar="استلم من">Received From</th>
                <td>
                    <span class="english-text">{{ $voucher->received_from_name ?? '-' }}</span>
                    <span class="arabic-text">{{ $voucher->received_from_name ?? '-' }}</span>
                </td>
            </tr>
            <tr>
                <th data-en="Purpose" data-ar="الغرض">Purpose</th>
                <td>
                    <span class="english-text">{{ $voucher->purpose ?? '-' }}</span>
                    <span class="arabic-text">{{ $voucher->purpose ?? '-' }}</span>
                </td>
            </tr>
            <tr>
                <th data-en="Reservation No." data-ar="رقم الحجز">Reservation No.</th>
                <td>{{ $voucher->reservation->reservation_number ?? '-' }}</td>
            </tr>
            <tr>
                <th data-en="Payment Method" data-ar="طريقة الدفع">Payment Method</th>
                <td>{{ $voucher->paymentMethod->paymentMethod->name ?? $voucher->paymentMethod->name ?? '-' }}</td>
            </tr>
            @if($voucher->comment)
            <tr>
                <th data-en="Comment" data-ar="تعليق">Comment</th>
                <td>
                    <span class="english-text">{{ $voucher->comment }}</span>
                    <span class="arabic-text">{{ $voucher->comment }}</span>
                </td>
            </tr>
            @endif
        </table>

        <div class="amount-box">
            <div class="amount-label">
                <span class="english-text">Amount Received</span>
                <span class="arabic-text">المبلغ المستلم</span>
            </div>
            <div class="amount-value">{{ number_format($voucher->amount, 2) }} SAR</div>
        </div>

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
        };
    </script>
</body>
</html>
