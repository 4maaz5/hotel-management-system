<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Night Audit Report - {{ $audit->period_date_from->format('Y-m-d') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 40px; background: #f5f5f5; }
        .print-btn { position: fixed; top: 20px; right: 20px; z-index: 1000; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #333; }
        .header h1 { font-size: 24px; color: #333; margin-bottom: 10px; }
        .header p { color: #666; font-size: 14px; }
        .info-box { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .info-label { color: #666; font-weight: 500; }
        .info-value { color: #333; font-weight: 600; }
        .section { margin-bottom: 25px; }
        .section-title { font-size: 16px; font-weight: 600; color: #333; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 1px solid #ddd; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; font-weight: 600; color: #333; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-primary { color: #007bff; }
        .total-row { background: #f8f9fa; font-weight: 600; }
        .summary-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 25px; }
        .card { background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center; }
        .card-label { font-size: 12px; color: #666; margin-bottom: 5px; }
        .card-value { font-size: 24px; font-weight: 700; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666; font-size: 12px; }
        @media print {
            body { padding: 0; background: #fff; }
            .print-btn { display: none !important; }
            .container { box-shadow: none; padding: 20px; max-width: 100%; }
        }
    </style>
</head>
<body>
    <button class="btn btn-primary print-btn" onclick="window.print()">
        <i class="fas fa-print me-2"></i> Print / Save as PDF
    </button>
    <div class="container">
        <div class="header">
            <h1>{{ optional($property)->property_name_en ?? 'Hotel' }} - Night Audit Report</h1>
            <p>Generated on {{ now()->format('Y-m-d H:i') }}</p>
        </div>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Audit ID:</span>
                <span class="info-value">#{{ str_pad($audit->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Period:</span>
                <span class="info-value">{{ $audit->period_date_from->format('Y-m-d') }} to {{ $audit->period_date_to->format('Y-m-d') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Night Count:</span>
                <span class="info-value">{{ $audit->night_count }} night(s)</span>
            </div>
            <div class="info-row">
                <span class="info-label">Audited By:</span>
                <span class="info-value">{{ $audit->user->name ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Completed At:</span>
                <span class="info-value">{{ $audit->end_date_time->format('Y-m-d H:i') }}</span>
            </div>
        </div>

        <div class="summary-cards">
            <div class="card">
                <div class="card-label">Total Received</div>
                <div class="card-value text-success">SAR {{ number_format($summary['receipts']['total_received'], 2) }}</div>
            </div>
            <div class="card">
                <div class="card-label">Total Paid</div>
                <div class="card-value text-danger">SAR {{ number_format($summary['payments']['total_paid'], 2) }}</div>
            </div>
            <div class="card">
                <div class="card-label">Net Change</div>
                <div class="card-value {{ $summary['net_change'] >= 0 ? 'text-success' : 'text-danger' }}">
                    SAR {{ number_format($summary['net_change'], 2) }}
                </div>
            </div>
        </div>

        <div class="section">
            <h3 class="section-title">Receipts Summary</h3>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-center">Count</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Cash Receipts</td>
                        <td class="text-center">-</td>
                        <td class="text-end">SAR {{ number_format($summary['receipts']['cash_received'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>Card Payments Received</td>
                        <td class="text-center">-</td>
                        <td class="text-end">SAR {{ number_format($summary['receipts']['card_received'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>Other Payments Received</td>
                        <td class="text-center">-</td>
                        <td class="text-end">SAR {{ number_format($summary['receipts']['other_received'], 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>Total Receipts</td>
                        <td class="text-center">{{ $summary['receipts']['count'] }}</td>
                        <td class="text-end text-success">SAR {{ number_format($summary['receipts']['total_received'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h3 class="section-title">Payments Summary</h3>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-center">Count</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Cash Payments</td>
                        <td class="text-center">-</td>
                        <td class="text-end">SAR {{ number_format($summary['payments']['cash_paid'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>Card Payments Made</td>
                        <td class="text-center">-</td>
                        <td class="text-end">SAR {{ number_format($summary['payments']['card_paid'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>Other Payments</td>
                        <td class="text-center">-</td>
                        <td class="text-end">SAR {{ number_format($summary['payments']['other_paid'], 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>Total Payments</td>
                        <td class="text-center">{{ $summary['payments']['count'] }}</td>
                        <td class="text-end text-danger">SAR {{ number_format($summary['payments']['total_paid'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h3 class="section-title">Security Deposits</h3>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-center">Count</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Deposits Received</td>
                        <td class="text-center">-</td>
                        <td class="text-end text-success">SAR {{ number_format($summary['security_deposits']['received'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>Deposits Refunded</td>
                        <td class="text-center">{{ $summary['security_deposits']['refund_count'] }}</td>
                        <td class="text-end text-danger">SAR {{ number_format($summary['security_deposits']['refunded'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h3 class="section-title">Drop Cash</h3>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Cash Drops</td>
                        <td class="text-end">SAR {{ number_format($summary['drop_cash']['cash_drops'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>Bank Transfers</td>
                        <td class="text-end">SAR {{ number_format($summary['drop_cash']['bank_transfers'], 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>Total Drops</td>
                        <td class="text-end text-danger">SAR {{ number_format($summary['drop_cash']['total_drops'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($audit->notes)
        <div class="section">
            <h3 class="section-title">Notes</h3>
            <p style="padding: 15px; background: #f8f9fa; border-radius: 8px;">{{ $audit->notes }}</p>
        </div>
        @endif

        <div class="footer">
            <p>This is an automatically generated Night Audit Report.</p>
            <p>{{ optional($property)->property_name_en ?? 'Hotel' }} | {{ optional($property)->address_en ?? '' }}</p>
        </div>
    </div>
</body>
</html>
