<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promissory Note {{ $voucher->voucher_number }}</title>
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
        .amount-collected { font-size: 18px; color: #28a745; font-weight: bold; }
        .amount-remaining { font-size: 16px; color: #dc3545; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; font-size: 12px; color: #6c757d; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-partial { background: #cce5ff; color: #004085; }
        .status-collected { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
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
                <strong>{{ optional($property)->property_name_ar ?? 'أونارا استاي' }}</strong><br>
                {{ optional($property)->address_ar ?? 'المدينة المنورة - الملك فهد' }}<br>
                {{ optional($property)->property_code ?? '1100' }} | {{ optional($property)->report_name_en ?? 'B-IT' }}<br>
                {{ optional($property)->phone ?? '+966 14 124585858' }}<br>
                VAT: {{ optional($property->commercialDetail)->vat_registration_number ?? '333333333338333' }}
            </div>
        </div>
        @endif

        <div class="voucher-header">
            <div>
                <div class="voucher-title">
                    <span class="english-text">PROMISSORY NOTE</span>
                    <span class="arabic-text">سند قبض</span>
                </div>
            </div>
            <div class="text-end">
                <strong>{{ $voucher->voucher_number }}</strong><br>
                <span class="english-text">Date: {{ $voucher->date }}</span>
                <span class="arabic-text">التاريخ: {{ $voucher->date }}</span><br>
                @php
                    $statusClass = match($voucher->status) {
                        'pending' => 'status-pending',
                        'partial' => 'status-partial',
                        'collected' => 'status-collected',
                        'cancelled' => 'status-cancelled',
                        default => 'status-pending'
                    };
                    $statusEn = match($voucher->status) {
                        'pending' => 'Pending',
                        'partial' => 'Partial',
                        'collected' => 'Collected',
                        'cancelled' => 'Cancelled',
                        default => 'Pending'
                    };
                    $statusAr = match($voucher->status) {
                        'pending' => 'معلق',
                        'partial' => 'جزئي',
                        'collected' => 'محصل',
                        'cancelled' => 'ملغي',
                        default => 'معلق'
                    };
                @endphp
                <span class="status-badge {{ $statusClass }}">
                    <span class="english-text">{{ $statusEn }}</span>
                    <span class="arabic-text">{{ $statusAr }}</span>
                </span>
            </div>
        </div>

        <table class="info-table">
            <tr>
                <th data-en="Guest Name" data-ar="اسم الضيف">Guest Name</th>
                <td>{{ $voucher->guest->first_name ?? '' }} {{ $voucher->guest->last_name ?? '' }}</td>
            </tr>
            <tr>
                <th data-en="Reserved To" data-ar="محجوز لـ">Reserved To</th>
                <td>{{ $voucher->reserved_to ?? '-' }}</td>
            </tr>
            <tr>
                <th data-en="Reservation No." data-ar="رقم الحجز">Reservation No.</th>
                <td>{{ $voucher->reservation->reservation_number ?? '-' }}</td>
            </tr>
            <tr>
                <th data-en="Purpose" data-ar="الغرض">Purpose</th>
                <td>{{ $voucher->purpose ?? '-' }}</td>
            </tr>
            <tr>
                <th data-en="Maturity Date" data-ar="تاريخ الاستحقاق">Maturity Date</th>
                <td>{{ $voucher->maturity_date ?? '-' }}</td>
            </tr>
            <tr>
                <th data-en="Maturity Place" data-ar="مكان الاستحقاق">Maturity Place</th>
                <td>{{ $voucher->maturity_place ?? '-' }}</td>
            </tr>
            <tr>
                <th data-en="Payment Method" data-ar="طريقة الدفع">Payment Method</th>
                <td>{{ $voucher->paymentMethod->paymentMethod->name ?? $voucher->paymentMethod->name ?? '-' }}</td>
            </tr>
            @if($voucher->receiving_bank)
            <tr>
                <th data-en="Receiving Bank" data-ar="البنك المستلم">Receiving Bank</th>
                <td>{{ $voucher->receivingBank->name ?? '-' }}</td>
            </tr>
            @endif
            @if($voucher->transaction_number)
            <tr>
                <th data-en="Transaction Number" data-ar="رقم المعاملة">Transaction Number</th>
                <td>{{ $voucher->transaction_number }}</td>
            </tr>
            @endif
            @if($voucher->sending_bank_name)
            <tr>
                <th data-en="Sending Bank" data-ar="البنك المرسل">Sending Bank</th>
                <td>{{ $voucher->sending_bank_name }}</td>
            </tr>
            @endif
            @if($voucher->cheque_number)
            <tr>
                <th data-en="Cheque Number" data-ar="رقم الشيك">Cheque Number</th>
                <td>{{ $voucher->cheque_number }}</td>
            </tr>
            @endif
            @if($voucher->comment)
            <tr>
                <th data-en="Comment" data-ar="تعليق">Comment</th>
                <td>{{ $voucher->comment }}</td>
            </tr>
            @endif
            @if($voucher->cancel_reason)
            <tr>
                <th data-en="Cancel Reason" data-ar="سبب الإلغاء">Cancel Reason</th>
                <td>{{ $voucher->cancel_reason }}</td>
            </tr>
            @endif
        </table>

        <div class="amount-box">
            <div class="amount-label"><span class="english-text" data-en="Total Amount" data-ar="المبلغ الإجمالي">Total Amount</span><span class="arabic-text" data-en="Total Amount" data-ar="المبلغ الإجمالي">المبلغ الإجمالي</span></div>
            <div class="amount-value">{{ number_format($voucher->amount, 2) }} SAR</div>
            @if($voucher->collected_amount > 0)
            <div class="mt-2 amount-collected">
                <span class="english-text" data-en="Collected" data-ar="محصل">Collected</span>: {{ number_format($voucher->collected_amount, 2) }} SAR
            </div>
            @endif
            @if($voucher->status === 'partial')
            <div class="mt-1 amount-remaining">
                <span class="english-text" data-en="Remaining" data-ar="متبقي">Remaining</span>: {{ number_format($voucher->amount - $voucher->collected_amount, 2) }} SAR
            </div>
            @endif
        </div>

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
        window.onload = function() { switchLanguage('en'); };
    </script>
</body>
</html>
