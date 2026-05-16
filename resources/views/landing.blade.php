@php
    $theme = \Illuminate\Support\Facades\Schema::hasTable('theme_customizations')
        ? \App\Models\ThemeCustomization::getTheme()
        : null;
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('dashboard.module_selection') }}</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    @if (app()->getLocale() == 'ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-rtl@5.3.0/css/bootstrap-rtl.min.css">
    @endif
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 40%, rgba(99, 102, 241, 0.08) 0%, transparent 50%),
                        radial-gradient(circle at 70% 60%, rgba(245, 158, 11, 0.06) 0%, transparent 50%);
            z-index: 0;
        }
        .landing-page {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1000px;
            text-align: center;
        }
        .logo-section { margin-bottom: 50px; }
        .logo-section .logo-img {
            width: 80px; height: 80px; border-radius: 20px;
            margin-bottom: 20px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        .welcome-title {
            font-size: 2.2rem; font-weight: 700;
            color: #f1f5f9; margin-bottom: 8px;
        }
        .welcome-subtitle {
            font-size: 1.05rem; color: #94a3b8; margin-bottom: 50px;
        }
        .module-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }
        .module-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02));
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 24px;
            padding: 50px 30px 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            min-height: 340px;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }
        .module-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 4px;
        }
        .module-card.hr-card::before { background: linear-gradient(90deg, #6366f1, #a78bfa, #c4b5fd); }
        .module-card.reservation-card::before { background: linear-gradient(90deg, #f59e0b, #f97316, #ef4444); }
        .module-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            border-radius: 24px;
            opacity: 0;
            transition: opacity 0.4s ease;
            z-index: 0;
        }
        .module-card.hr-card::after {
            background: radial-gradient(circle at 50% 0%, rgba(99,102,241,0.15), transparent 70%);
        }
        .module-card.reservation-card::after {
            background: radial-gradient(circle at 50% 0%, rgba(245,158,11,0.15), transparent 70%);
        }
        .module-card:hover { transform: translateY(-8px); border-color: rgba(255,255,255,0.25); }
        .module-card:hover::after { opacity: 1; }
        .module-card > * { position: relative; z-index: 1; }
        .icon-wrapper {
            width: 100px; height: 100px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 24px; transition: transform 0.3s ease;
        }
        .module-card.hr-card .icon-wrapper { background: linear-gradient(135deg, rgba(99,102,241,0.2), rgba(139,92,246,0.15)); }
        .module-card.reservation-card .icon-wrapper { background: linear-gradient(135deg, rgba(245,158,11,0.2), rgba(249,115,22,0.15)); }
        .module-card:hover .icon-wrapper { transform: scale(1.1); }
        .icon-wrapper svg { width: 48px; height: 48px; }
        .card-title {
            font-size: 1.5rem; font-weight: 700;
            color: #f1f5f9; margin-bottom: 12px;
        }
        .card-desc {
            font-size: 0.92rem; color: #94a3b8;
            line-height: 1.7; max-width: 300px;
        }
        .card-arrow {
            position: absolute;
            bottom: 20px;
            {{ app()->getLocale() == 'ar' ? 'left: 25px;' : 'right: 25px;' }}
            width: 38px; height: 38px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateX({{ app()->getLocale() == 'ar' ? '-' : '' }}10px);
        }
        .module-card.hr-card .card-arrow { background: linear-gradient(135deg, #6366f1, #8b5cf6); }
        .module-card.reservation-card .card-arrow { background: linear-gradient(135deg, #f59e0b, #f97316); }
        .module-card:hover .card-arrow { opacity: 1; transform: translateX(0); }
        .card-arrow svg { width: 18px; height: 18px; color: white; }

        .logout-btn {
            position: fixed;
            top: 20px;
            {{ app()->getLocale() == 'ar' ? 'left: 20px;' : 'right: 20px;' }}
            z-index: 10;
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.12);
            color: #cbd5e1;
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .logout-btn:hover {
            background: rgba(239,68,68,0.15);
            border-color: rgba(239,68,68,0.3);
            color: #fca5a5;
        }

        @media (max-width: 768px) {
            .module-grid { grid-template-columns: 1fr; gap: 20px; max-width: 400px; margin: 0 auto; }
            .module-card { min-height: 280px; padding: 35px 20px 30px; }
            .welcome-title { font-size: 1.6rem; }
            .icon-wrapper { width: 80px; height: 80px; }
            .icon-wrapper svg { width: 38px; height: 38px; }
            .card-title { font-size: 1.2rem; }
        }
    </style>
</head>
<body>
    <div class="landing-page">
        <div class="logo-section">
            <img src="{{ $theme?->logo ? asset($theme->logo) : asset('logo.webp') }}" alt="Logo" class="logo-img">
            <div class="welcome-title">{{ __('dashboard.welcome_back') }}, {{ Auth::user()->name }}!</div>
            <div class="welcome-subtitle">{{ __('dashboard.choose_module') }}</div>
        </div>

        <div class="module-grid">
            <a href="{{ route('dashboard.program') }}" class="module-card hr-card">
                <div class="icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#818cf8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 00-3-3.87" />
                        <path d="M16 3.13a4 4 0 010 7.75" />
                    </svg>
                </div>
                <div class="card-title">{{ __('dashboard.hr_management') }}</div>
                <div class="card-desc">{{ __('dashboard.hr_description') }}</div>
                <div class="card-arrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12" />
                        <polyline points="12 5 19 12 12 19" />
                    </svg>
                </div>
            </a>

            <a href="{{ route('program') }}" class="module-card reservation-card">
                <div class="icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                        <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01" />
                    </svg>
                </div>
                <div class="card-title">{{ __('dashboard.reservation_management') }}</div>
                <div class="card-desc">{{ __('dashboard.reservation_description') }}</div>
                <div class="card-arrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12" />
                        <polyline points="12 5 19 12 12 19" />
                    </svg>
                </div>
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="d-inline">
        @csrf
        <button type="submit" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            {{ __('dashboard.logout') }}
        </button>
    </form>
</body>
</html>
