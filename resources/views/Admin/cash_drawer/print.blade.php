<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Drawer Balance Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: white;
            padding: 20px;
        }

        .header-controls {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            print: hidden;
        }

        .header-controls button {
            padding: 8px 16px;
            cursor: pointer;
            border: 1px solid #ddd;
            background: white;
            border-radius: 4px;
        }

        .header-controls button:hover {
            background: #f5f5f5;
        }

        .content {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
        }

        .section {
            margin-bottom: 40px;
            page-break-after: avoid;
        }

        .section-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .section-subtitle {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background-color: #f5f5f5;
            border-bottom: 2px solid #333;
        }

        table th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        table td {
            padding: 10px 12px;
            border: 1px solid #ddd;
        }

        table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        .amount-column {
            text-align: right;
            font-weight: 500;
        }

        .count-column {
            text-align: center;
        }

        .total-row {
            background-color: #e8f4f8 !important;
            font-weight: bold;
            border-top: 2px solid #333;
        }

        .arabic-section {
            direction: rtl;
            unicode-bidi: embed;
            margin-top: 40px;
        }

        .english-section {
            direction: ltr;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .header-controls {
                display: none !important;
            }

            .content {
                max-width: 100%;
            }

            .section {
                page-break-inside: avoid;
            }

            table {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="header-controls">
        <button onclick="setLanguage('en')">English Only</button>
        <button onclick="setLanguage('ar')">Arabic Only</button>
        <button onclick="setLanguage('both')">Both Languages</button>
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>

    <div class="content">
        <!-- English Section -->
        <div id="english-section" class="section english-section">
            <div class="section-title">Cash Drawer Balance Report</div>
            <div class="section-subtitle">{{ $startDate ?? '' }} to {{ $endDate ?? '' }}</div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 60%;">Transaction Type</th>
                        <th style="width: 20%;" class="count-column">Count</th>
                        <th style="width: 20%;" class="amount-column">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Cash Received</td>
                        <td class="count-column">{{ $cashReceivedCount }}</td>
                        <td class="amount-column">SAR {{ number_format($cashReceived, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Security Deposit Received</td>
                        <td class="count-column">{{ $securityDepositCount }}</td>
                        <td class="amount-column">SAR {{ number_format($securityDepositsReceived, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Cash Paid Out</td>
                        <td class="count-column">{{ $cashPaidOutCount }}</td>
                        <td class="amount-column">SAR {{ number_format($cashPaidOut, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Security Deposit Paid Out</td>
                        <td class="count-column">{{ $securityDepositPaidCount }}</td>
                        <td class="amount-column">SAR {{ number_format($securityDepositsPaidOut, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Drop Cash Vouchers</td>
                        <td class="count-column">{{ $dropCashCount }}</td>
                        <td class="amount-column">SAR {{ number_format($dropCashTotal, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>Current Balance</td>
                        <td class="count-column">-</td>
                        <td class="amount-column">SAR {{ number_format($currentBalance, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Arabic Section -->
        <div id="arabic-section" class="section arabic-section" style="display: none;">
            <div class="section-title">تقرير رصيد درج النقد</div>
            <div class="section-subtitle">{{ $startDate ?? '' }} إلى {{ $endDate ?? '' }}</div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 20%;" class="amount-column">المبلغ</th>
                        <th style="width: 20%;" class="count-column">العدد</th>
                        <th style="width: 60%; text-align: right;">نوع المعاملة</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="amount-column">ر.س {{ number_format($cashReceived, 2) }}</td>
                        <td class="count-column">{{ $cashReceivedCount }}</td>
                        <td style="text-align: right;">النقد المستلم</td>
                    </tr>
                    <tr>
                        <td class="amount-column">ر.س {{ number_format($securityDepositsReceived, 2) }}</td>
                        <td class="count-column">{{ $securityDepositCount }}</td>
                        <td style="text-align: right;">الضمان المستلم</td>
                    </tr>
                    <tr>
                        <td class="amount-column">ر.س {{ number_format($cashPaidOut, 2) }}</td>
                        <td class="count-column">{{ $cashPaidOutCount }}</td>
                        <td style="text-align: right;">النقد المدفوع</td>
                    </tr>
                    <tr>
                        <td class="amount-column">ر.س {{ number_format($securityDepositsPaidOut, 2) }}</td>
                        <td class="count-column">{{ $securityDepositPaidCount }}</td>
                        <td style="text-align: right;">الضمان المدفوع</td>
                    </tr>
                    <tr>
                        <td class="amount-column">ر.س {{ number_format($dropCashTotal, 2) }}</td>
                        <td class="count-column">{{ $dropCashCount }}</td>
                        <td style="text-align: right;">قسائم إسقاط النقد</td>
                    </tr>
                    <tr class="total-row">
                        <td class="amount-column">ر.س {{ number_format($currentBalance, 2) }}</td>
                        <td class="count-column">-</td>
                        <td style="text-align: right;">الرصيد الحالي</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function setLanguage(lang) {
            const enSection = document.getElementById('english-section');
            const arSection = document.getElementById('arabic-section');

            if (lang === 'ar') {
                enSection.style.display = 'none';
                arSection.style.display = 'block';
            } else if (lang === 'both') {
                enSection.style.display = 'block';
                arSection.style.display = 'block';
            } else {
                enSection.style.display = 'block';
                arSection.style.display = 'none';
            }
        }

        // Set English as default
        setLanguage('en');
    </script>
</body>
</html>
