<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation {{ $reservation->reservation_number }}</title>
    @php
        $property = $reservation->property;
        $guest = $reservation->guest;
    @endphp
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f7fb;
            color: #1f2937;
        }
        .email-container {
            max-width: 760px;
            margin: 0 auto;
            background: #ffffff;
            padding: 32px;
            border-radius: 18px;
        }
        .hero {
            margin-bottom: 28px;
            padding-bottom: 20px;
            border-bottom: 2px solid #2563eb;
        }
        .hero h1 {
            margin: 0 0 10px;
            font-size: 28px;
            color: #2563eb;
        }
        .hero p {
            margin: 0;
            color: #6b7280;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 28px;
        }
        .summary-card {
            padding: 16px;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
        }
        .summary-card small {
            display: block;
            margin-bottom: 6px;
            color: #6b7280;
            font-weight: 600;
        }
        .summary-card strong {
            font-size: 16px;
        }
        .totals {
            margin-top: 24px;
            padding: 18px;
            border-radius: 14px;
            background: #eff6ff;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 8px 0;
        }
        .footer {
            margin-top: 32px;
            padding-top: 18px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 13px;
        }
        @media (max-width: 640px) {
            .email-container {
                padding: 22px;
            }
            .summary-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="hero">
            <h1>Booking Confirmation</h1>
            <p>Your direct booking request has been saved successfully.</p>
        </div>

        <p>Hello {{ $guest?->full_name ?: 'Guest' }},</p>
        <p>Thank you for booking with {{ $property->property_name_en ?? 'our property' }}. Your reservation was created in our system and the details are below.</p>

        <div class="summary-grid">
            <div class="summary-card">
                <small>Booking Reference</small>
                <strong>{{ $reservation->reservation_number }}</strong>
            </div>
            <div class="summary-card">
                <small>Status</small>
                <strong>{{ strtoupper($reservation->status) }}</strong>
            </div>
            <div class="summary-card">
                <small>Check-in</small>
                <strong>{{ optional($reservation->check_in_date)->format('Y-m-d') }}</strong>
            </div>
            <div class="summary-card">
                <small>Check-out</small>
                <strong>{{ optional($reservation->check_out_date)->format('Y-m-d') }}</strong>
            </div>
            <div class="summary-card">
                <small>Assigned Unit</small>
                <strong>{{ $reservation->unit?->unit_number ?? '-' }}</strong>
            </div>
            <div class="summary-card">
                <small>Guests</small>
                <strong>{{ (int) $reservation->adults + (int) $reservation->children }}</strong>
            </div>
        </div>

        <div class="totals">
            <div class="totals-row">
                <span>Grand Total</span>
                <strong>SAR {{ number_format((float) $reservation->grand_total, 2) }}</strong>
            </div>
            <div class="totals-row">
                <span>Balance</span>
                <strong>SAR {{ number_format((float) $reservation->balance, 2) }}</strong>
            </div>
        </div>

        <div class="footer">
            <div>{{ $property->property_name_en ?? 'Property' }}</div>
            @if($property?->email)
                <div>{{ $property->email }}</div>
            @endif
            @if($property?->phone)
                <div>{{ $property->phone }}</div>
            @endif
        </div>
    </div>
</body>
</html>
