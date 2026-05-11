<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Request</title>
    <style>
        body {
            margin: 0;
            padding: 24px;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f7fb;
            color: #1f2937;
        }

        .email-shell {
            max-width: 760px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #dbe2ea;
            border-radius: 18px;
            overflow: hidden;
        }

        .email-header {
            padding: 24px 28px;
            background: linear-gradient(135deg, #0f766e, #1d4ed8);
            color: #ffffff;
        }

        .email-header h1 {
            margin: 0 0 8px;
            font-size: 24px;
        }

        .email-header p {
            margin: 0;
            font-size: 14px;
            opacity: 0.9;
        }

        .email-body {
            padding: 28px;
        }

        .summary-card {
            background: #f8fafc;
            border: 1px solid #dbe2ea;
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 20px;
        }

        .summary-card strong {
            display: block;
            font-size: 16px;
            margin-bottom: 6px;
            color: #0f172a;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table th,
        .details-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            vertical-align: top;
            font-size: 14px;
        }

        .details-table th {
            width: 220px;
            color: #475569;
            background: #f8fafc;
        }

        .notes-box {
            margin-top: 20px;
            padding: 16px;
            background: #f8fafc;
            border: 1px solid #dbe2ea;
            border-radius: 14px;
        }

        .notes-box h2 {
            margin: 0 0 10px;
            font-size: 16px;
            color: #0f172a;
        }

        .footer {
            padding: 18px 28px 28px;
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="email-shell">
        <div class="email-header">
            <h1>New Subscription Request</h1>
            <p>A new integration subscription request was submitted from the setup subscriptions page.</p>
        </div>

        <div class="email-body">
            <div class="summary-card">
                <strong>{{ $requestData['integration_name'] }}</strong>
                <div>{{ $requestData['integration_billing'] }}</div>
                <div>{{ $requestData['integration_price'] }}</div>
            </div>

            <table class="details-table">
                <tr>
                    <th>Property Name</th>
                    <td>{{ $requestData['property_name'] }}</td>
                </tr>
                <tr>
                    <th>Contact Name</th>
                    <td>{{ $requestData['contact_name'] }}</td>
                </tr>
                <tr>
                    <th>Contact Email</th>
                    <td>{{ $requestData['contact_email'] }}</td>
                </tr>
                <tr>
                    <th>Contact Phone</th>
                    <td>{{ $requestData['contact_phone'] ?: 'Not provided' }}</td>
                </tr>
                <tr>
                    <th>Preferred Plan</th>
                    <td>{{ $requestData['requested_plan'] ?: 'Not specified' }}</td>
                </tr>
                <tr>
                    <th>Submitted At</th>
                    <td>{{ $requestData['submitted_at']->format('Y-m-d H:i:s') }}</td>
                </tr>
            </table>

            <div class="notes-box">
                <h2>Additional Notes</h2>
                <div>{{ $requestData['notes'] ?: 'No additional notes were provided.' }}</div>
            </div>
        </div>

        <div class="footer">
            This email was sent automatically by the reservation management subscription request flow.
        </div>
    </div>
</body>
</html>
