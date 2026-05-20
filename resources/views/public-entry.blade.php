<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'HR Management') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            background: #f0f2f5;
        }

        .wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        .brand-side {
            display: none;
            flex: 1;
            background: linear-gradient(135deg, #1e3a5f 0%, #2a5f8a 50%, #3a7bd5 100%);
            position: relative;
            overflow: hidden;
            padding: 60px;
            flex-direction: column;
            justify-content: center;
            color: #fff;
        }

        .brand-side::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 600px;
            height: 600px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
        }

        .brand-side::after {
            content: '';
            position: absolute;
            bottom: -20%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.02);
            border-radius: 50%;
        }

        .brand-content {
            position: relative;
            z-index: 1;
            max-width: 480px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 40px;
        }

        .brand-logo-icon {
            width: 52px;
            height: 52px;
            background: rgba(255,255,255,0.15);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 700;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .brand-logo-text {
            font-size: 22px;
            font-weight: 600;
            letter-spacing: -0.3px;
        }

        .brand-title {
            font-size: 36px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 16px;
            letter-spacing: -0.5px;
        }

        .brand-subtitle {
            font-size: 16px;
            line-height: 1.6;
            opacity: 0.8;
            margin-bottom: 48px;
        }

        .brand-features {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .brand-feature {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 15px;
            opacity: 0.85;
        }

        .brand-feature i {
            width: 24px;
            font-size: 18px;
            opacity: 0.7;
        }

        .content-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            background: #f0f2f5;
        }

        .content-container {
            width: 100%;
            max-width: 480px;
            animation: fadeIn 0.5s ease-out;
        }

        .content-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .content-header .mobile-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #1e3a5f, #3a7bd5);
            border-radius: 16px;
            color: #fff;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .content-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 8px;
            letter-spacing: -0.3px;
        }

        .content-header p {
            color: #6b7280;
            font-size: 15px;
            max-width: 360px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .feature-cards {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 32px;
        }

        .feature-card {
            display: flex;
            align-items: center;
            gap: 14px;
            background: #fff;
            border-radius: 14px;
            padding: 16px 18px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03), 0 4px 16px rgba(0,0,0,0.04);
            border: 1px solid #f0f0f0;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .feature-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
            color: #fff;
        }

        .feature-icon.blue    { background: #3a7bd5; }
        .feature-icon.green   { background: #10b981; }
        .feature-icon.amber   { background: #f59e0b; }
        .feature-icon.purple  { background: #8b5cf6; }

        .feature-info h3 {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a2e;
            margin: 0 0 2px;
        }

        .feature-info p {
            font-size: 13px;
            color: #9ca3af;
            margin: 0;
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }

        .actions a {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0.8rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .actions .primary {
            background: linear-gradient(135deg, #1e3a5f, #3a7bd5);
            color: #fff;
            box-shadow: 0 4px 14px rgba(58,123,213,0.3);
        }

        .actions .primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(58,123,213,0.4);
        }

        .actions .secondary {
            background: #fff;
            border: 1.5px solid #e5e7eb;
            color: #374151;
        }

        .actions .secondary:hover {
            border-color: #d1d5db;
            background: #f9fafb;
        }

        .footer-text {
            text-align: center;
            font-size: 0.8rem;
            color: #9ca3af;
        }

        @media (min-width: 992px) {
            .brand-side { display: flex; }
            .content-side { padding: 60px 48px; }
        }

        @media (max-width: 440px) {
            .actions { flex-direction: column; }
            .content-header h1 { font-size: 22px; }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="brand-side">
            <div class="brand-content">
                <div class="brand-logo">
                    <div class="brand-logo-icon">B</div>
                    <span class="brand-logo-text">Booking IT</span>
                </div>
                <h1 class="brand-title">Your all-in-one<br>property workspace</h1>
                <p class="brand-subtitle">HR, reservations, reports, and property management — all in one platform.</p>
                <div class="brand-features">
                    <div class="brand-feature">
                        <i class="fa-regular fa-calendar-check"></i>
                        Reservation management
                    </div>
                    <div class="brand-feature">
                        <i class="fa-regular fa-building"></i>
                        Multi-property support
                    </div>
                    <div class="brand-feature">
                        <i class="fa-regular fa-chart-bar"></i>
                        Reports &amp; analytics
                    </div>
                    <div class="brand-feature">
                        <i class="fa-regular fa-clock"></i>
                        24/7 operations dashboard
                    </div>
                </div>
            </div>
        </div>

        <div class="content-side">
            <div class="content-container">
                <div class="content-header">
                    <div class="mobile-logo d-lg-none">B</div>
                    <h1>{{ config('app.name', 'HR Management') }}</h1>
                    <p>Sign in to manage HR, reservations, reports, and property operations from your workspace.</p>
                </div>

                <div class="feature-cards">
                    <div class="feature-card">
                        <div class="feature-icon blue"><i class="fa-regular fa-building"></i></div>
                        <div class="feature-info">
                            <h3>Property Management</h3>
                            <p>Manage rooms, rates, inventory &amp; availability</p>
                        </div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon green"><i class="fa-regular fa-calendar-check"></i></div>
                        <div class="feature-info">
                            <h3>Reservations</h3>
                            <p>Handle bookings, check-ins, check-outs &amp; payments</p>
                        </div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon amber"><i class="fa-regular fa-users"></i></div>
                        <div class="feature-info">
                            <h3>HR &amp; Staff</h3>
                            <p>Employee management, attendance &amp; payroll</p>
                        </div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon purple"><i class="fa-regular fa-chart-bar"></i></div>
                        <div class="feature-info">
                            <h3>Reports</h3>
                            <p>Real-time analytics and operational insights</p>
                        </div>
                    </div>
                </div>

                <div class="actions">
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="primary">
                            <i class="fa-solid fa-arrow-right-to-bracket"></i>
                            Login
                        </a>
                    @endif
                    @if (Route::has('register.tenant'))
                        <a href="{{ route('register.tenant') }}" class="secondary">
                            <i class="fa-solid fa-plus"></i>
                            Create company
                        </a>
                    @endif
                </div>

                <p class="footer-text">
                    &copy; {{ date('Y') }} {{ config('app.name', 'HR Management') }}. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
