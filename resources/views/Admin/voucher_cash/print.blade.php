<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drop Cash Voucher {{ $voucher->voucher_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .method-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .method-cash { background: #e3f2fd; color: #1565c0; }
        .method-bank_transfer { background: #e8f5e9; color: #2e7d32; }
        .method-other { background: #f5f5f5; color: #616161; }
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
                    <span class="english-text">DROP CASH VOUCHER</span>
                    <span class="arabic-text">قسيمة إسقاط النقدية</span>
                </div>
            </div>
            <div class="text-end">
                <strong>{{ $voucher->voucher_number }}</strong><br>
                <span class="english-text">Date: {{ $voucher->created_at ? $voucher->created_at->format('d-m-Y H:i') : '-' }}</span>
                <span class="arabic-text">التاريخ: {{ $voucher->created_at ? $voucher->created_at->format('d-m-Y H:i') : '-' }}</span><br>
                <span class="method-badge method-{{ $voucher->drop_method }}">
                    {{ ucfirst(str_replace('_', ' ', $voucher->drop_method)) }}
                </span>
            </div>
        </div>

        <table class="info-table">
            <tr>
                <th data-en="Dropped By" data-ar="أسلمة بواسطة">Dropped By</th>
                <td>{{ $voucher->user->name ?? '-' }}</td>
            </tr>
            <tr>
                <th data-en="Date / Time From" data-ar="التاريخ / الوقت من">Date / Time From</th>
                <td>{{ $voucher->date_from ? $voucher->date_from->format('d-m-Y H:i') : '-' }}</td>
            </tr>
            <tr>
                <th data-en="Date / Time To" data-ar="التاريخ / الوقت إلى">Date / Time To</th>
                <td>{{ $voucher->date_to ? $voucher->date_to->format('d-m-Y H:i') : '-' }}</td>
            </tr>
            <tr>
                <th data-en="Drop Method" data-ar="طريقة الإسقاط">Drop Method</th>
                <td>{{ ucfirst(str_replace('_', ' ', $voucher->drop_method)) }}
                    @if($voucher->drop_method === 'bank_transfer' && $voucher->bank)
                        - {{ $voucher->bank->name }}
                    @endif
                </td>
            </tr>
            @if($voucher->drop_method === 'bank_transfer' && $voucher->bank)
            <tr>
                <th data-en="Bank Account" data-ar="حساب البنك">Bank Account</th>
                <td>{{ $voucher->bank->account_number ?? '-' }}</td>
            </tr>
            <tr>
                <th data-en="IBAN" data-ar="IBAN">IBAN</th>
                <td>{{ $voucher->bank->iban ?? '-' }}</td>
            </tr>
            @endif
            <tr>
                <th data-en="Paid To" data-ar="دفع إلى">Paid To</th>
                <td>{{ $voucher->paid_to }}</td>
            </tr>
            <tr>
                <th data-en="Purpose" data-ar="الغرض">Purpose</th>
                <td>{{ $voucher->purpose }}</td>
            </tr>
            @if($voucher->comment)
            <tr>
                <th data-en="Comment" data-ar="تعليق">Comment</th>
                <td>{{ $voucher->comment }}</td>
            </tr>
            @endif
        </table>

        <div class="amount-box">
            <div class="amount-label">
                <span class="english-text" data-en="Dropped Amount" data-ar="المبلغ المسقط">Dropped Amount</span>
                <span class="arabic-text" data-en="Dropped Amount" data-ar="المبلغ المسقط">المبلغ المسقط</span>
            </div>
            <div class="amount-value">{{ number_format($voucher->amount, 2) }} SAR</div>
        </div>

        <div class="footer">
            <p class="mb-0">
                <span class="english-text" data-en="Thank you!" data-ar="شكرا لتعاملكم معنا!">Thank you!</span>
                <span class="arabic-text" data-en="Thank you!" data-ar="شكرا لتعاملكم معنا!">شكرا لتعاملكم معنا!</span>
            </p>
            <p class="mb-0">
                <span class="english-text" data-en="Generated on" data-ar="تم الإنشاء في">Generated on</span>: {{ now()->format('Y-m-d H:i:s') }}
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
