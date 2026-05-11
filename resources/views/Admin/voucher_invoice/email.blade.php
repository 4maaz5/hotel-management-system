<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    @php
        $property = \App\Models\Property::first();
    @endphp
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .email-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
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
            padding: 5px 0;
        }
        .grand-total {
            font-size: 18px;
            font-weight: bold;
            color: #4a6cf7;
            border-top: 2px solid #4a6cf7;
            padding-top: 10px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        .btn-primary {
            display: inline-block;
            padding: 10px 20px;
            background: #4a6cf7;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="invoice-header">
            <div>
                <div class="invoice-title">INVOICE</div>
                <p>Invoice #: {{ $invoice->invoice_number }}</p>
            </div>
            <div>
                <strong>{{ $property->property_name_en ?? 'Property Name' }}</strong>
                <p>Date: {{ $invoice->issue_date->format('Y-m-d') }}</p>
            </div>
        </div>

        <div class="mb-4">
            <h4>Bill To:</h4>
            <p>
                <strong>{{ $invoice->reservation->guest->first_name ?? '' }} {{ $invoice->reservation->guest->last_name ?? '' }}</strong><br>
                {{ $invoice->reservation->guest->email ?? '' }}<br>
                {{ $invoice->reservation->guest->phone ?? '' }}
            </p>
        </div>

        <table class="details-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                <strong>Subtotal:</strong> {{ number_format($invoice->subtotal, 2) }}
            </div>
            @if($invoice->discount_amount > 0)
            <div class="total-row">
                <strong>Discount:</strong> -{{ number_format($invoice->discount_amount, 2) }}
            </div>
            @endif
            @if($invoice->tax_amount > 0)
            <div class="total-row">
                <strong>VAT:</strong> {{ number_format($invoice->tax_amount, 2) }}
            </div>
            @endif
            @if($invoice->security_deposit > 0)
            <div class="total-row">
                <strong>Security Deposit:</strong> {{ number_format($invoice->security_deposit, 2) }}
            </div>
            @endif
            <div class="total-row grand-total">
                <strong>Total:</strong> {{ number_format($invoice->total, 2) }} SAR
            </div>
            @if($invoice->paid_amount > 0)
            <div class="total-row">
                <strong>Paid:</strong> -{{ number_format($invoice->paid_amount, 2) }}
            </div>
            <div class="total-row grand-total" style="color: #dc2626;">
                <strong>Balance Due:</strong> {{ number_format($invoice->balance, 2) }} SAR
            </div>
            @endif
        </div>

        @if($invoice->notes)
        <div class="mt-3 p-3 bg-light rounded">
            <strong>Notes:</strong>
            <p class="mb-0">{{ $invoice->notes }}</p>
        </div>
        @endif

        @if($invoice->qr_code)
        <div class="mt-3 p-3 text-center border rounded">
            {{-- <strong>ZATCA QR Code</strong> --}}
            <div class="mt-2">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($invoice->qr_code) }}" alt="ZATCA QR Code" width="150" height="150">
            </div>
        </div>
        @endif

        <div class="footer">
            <p>Thank you for your business!</p>
            <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
