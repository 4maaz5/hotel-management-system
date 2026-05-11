<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Document Expiry Alert</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 5px;
        }

        .header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .details p {
            margin: 5px 0;
        }

        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }

        .highlight {
            font-weight: bold;
        }

        .alert {
            color: #c00;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">{{ __('dashboard.document_expiry_alert') }}</div>

        <p class="alert">{{ $messageText }}</p>

        <div class="details">
            <p><span class="highlight">{{ __('dashboard.document') }}:</span>
                {{ $document->type ?? basename($document->file_path) }}</p>

            <p><span class="highlight">{{ __('dashboard.name') }}:</span>
                {{ $entity->first_name ?? ($entity->name ?? 'N/A') }}</p>
            <p><span class="highlight">{{ __('dashboard.id') }}:</span>
                {{ $entity->employee_id ?? ($entity->id ?? 'N/A') }}</p>
            <p><span class="highlight">{{ __('dashboard.branch') }}:</span> {{ $entity->branch->name ?? 'N/A' }}</p>
            <p><span class="highlight">{{ __('dashboard.expiration_date') }}:</span>
                {{ $document->expiration_date ?? ($document->end_date ?? 'N/A') }}</p>
        </div>

        <div class="footer">
            {{ __('dashboard.please_take_required') }}<br>
            {{ config('app.name') }}
        </div>
    </div>

</body>

</html>
