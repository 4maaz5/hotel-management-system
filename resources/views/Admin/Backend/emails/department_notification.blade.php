<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 30px;
        }

        .email-header {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            color: #333333;
            margin-bottom: 20px;
        }

        .email-body {
            font-size: 16px;
            line-height: 1.6;
            color: #555555;
        }

        .email-footer {
            margin-top: 30px;
            font-size: 14px;
            color: #999999;
            text-align: center;
        }

        .highlight {
            background-color: #f0f8ff;
            padding: 10px;
            border-radius: 4px;
            display: inline-block;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            {{ __('dashboard.new_notification') }}
        </div>
        <div class="email-body">
            <p>{{ __('dashboard.hello') }},</p>
            <p class="highlight">{{ $messageText }}</p>
            <p>{{ __('dashboard.thank_you') }},<br>{{ __('dashboard.hr_management_system') }}</p>
        </div>
        <div class="email-footer">
            &copy; {{ date('Y') }} {{ __('dashboard.all_rights_reserved') }}
        </div>
    </div>
</body>

</html>
