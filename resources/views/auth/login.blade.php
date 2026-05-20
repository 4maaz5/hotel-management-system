@php
    $theme = \App\Models\ThemeCustomization::getTheme();
@endphp

<!DOCTYPE html>
<html class="h-100" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Sign In') }} — {{ config('app.name', 'Reservation Management') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            background: #f0f2f5;
        }

        .login-wrapper {
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

        .form-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            background: #f0f2f5;
        }

        .form-container {
            width: 100%;
            max-width: 420px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .form-header .mobile-logo {
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

        .form-header h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 8px;
            letter-spacing: -0.3px;
        }

        .form-header p {
            color: #6b7280;
            font-size: 15px;
        }

        .login-card {
            background: #fff;
            border-radius: 20px;
            padding: 36px 32px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 8px 32px rgba(0,0,0,0.06);
        }

        .input-group {
            border-radius: 12px;
            overflow: hidden;
            border: 1.5px solid #e5e7eb;
            transition: all 0.2s ease;
            background: #fff;
        }

        .input-group:focus-within {
            border-color: #3a7bd5;
            box-shadow: 0 0 0 3px rgba(58,123,213,0.12);
        }

        .input-group-text {
            background: #fff;
            border: none;
            padding: 0.75rem 1rem;
            color: #9ca3af;
            font-size: 16px;
        }

        .form-control {
            border: none;
            padding: 0.75rem 1rem 0.75rem 0;
            font-size: 0.95rem;
            background: #fff;
            color: #1a1a2e;
        }

        .form-control:focus {
            box-shadow: none;
            background: #fff;
        }

        .form-control::placeholder {
            color: #b0b7c3;
        }

        .password-toggle-btn {
            background: #fff;
            border: none;
            color: #9ca3af;
            padding: 0 1rem;
            cursor: pointer;
            font-size: 16px;
        }

        .password-toggle-btn:hover {
            color: #3a7bd5;
        }

        .form-label-custom {
            font-size: 0.85rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
            display: block;
        }

        .error-message {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 0.6rem 0.9rem;
            border-radius: 8px;
            margin-top: 6px;
            font-size: 0.85rem;
        }

        .form-check-input:checked {
            background-color: #3a7bd5;
            border-color: #3a7bd5;
        }

        .form-check-label {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .forgot-password-link {
            color: #3a7bd5;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .forgot-password-link:hover {
            color: #1e3a5f;
            text-decoration: underline;
        }

        .login-btn {
            background: linear-gradient(135deg, #1e3a5f, #3a7bd5);
            border: none;
            padding: 0.85rem;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 12px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(58,123,213,0.3);
            color: #fff;
        }

        .login-btn:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(58,123,213,0.4);
        }

        .login-btn:active:not(:disabled) {
            transform: translateY(0);
        }

        .login-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .login-btn .spinner-border {
            display: none;
            width: 18px;
            height: 18px;
            border-width: 2px;
        }

        .login-btn.loading .spinner-border {
            display: inline-block;
        }

        .login-btn.loading .btn-text {
            display: none;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
            color: #d1d5db;
            font-size: 0.85rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        @media (min-width: 992px) {
            .brand-side {
                display: flex;
            }
            .form-side {
                padding: 60px 48px;
            }
            .login-card {
                padding: 40px 36px;
            }
        }

        @media (max-width: 440px) {
            .login-card {
                padding: 24px 20px;
                border-radius: 16px;
            }
            .form-header h1 {
                font-size: 20px;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .form-container {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="brand-side">
            <div class="brand-content">
                <div class="brand-logo">
                    <div class="brand-logo-icon">B</div>
                    <span class="brand-logo-text">Booking IT</span>
                </div>
                <h1 class="brand-title">Manage your<br>property operations</h1>
                <p class="brand-subtitle">HR, reservations, reports, and property management — all in one workspace.</p>
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

        <div class="form-side">
            <div class="form-container">
                <div class="form-header">
                    <div class="mobile-logo d-lg-none">B</div>
                    <h1>{{ __('Welcome back') }}</h1>
                    <p>{{ __('Sign in to your account to continue') }}</p>
                </div>

                <div class="login-card">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label-custom" for="email">{{ __('Email Address') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                                <input id="email" type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="you@example.com" name="email" value="{{ old('email') }}" required
                                    autofocus autocomplete="email">
                            </div>
                            @error('email')
                                <div class="error-message mt-2">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label-custom" for="password">{{ __('Password') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" name="password" required autocomplete="current-password">
                                <button class="password-toggle-btn" type="button" id="togglePassword">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="error-message mt-2">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember_me"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember_me">
                                    {{ __('Remember me') }}
                                </label>
                            </div>

                            @if (Route::has('password.request'))
                                <a class="forgot-password-link" href="{{ route('password.request') }}">
                                    {{ __('Forgot password?') }}
                                </a>
                            @endif
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn login-btn" id="loginBtn">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                <span class="btn-text">
                                    <i class="fa-solid fa-arrow-right-to-bracket me-2"></i>
                                    {{ __('Sign In') }}
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleButton = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            if (toggleButton && passwordInput) {
                toggleButton.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    const icon = this.querySelector('i');
                    icon.classList.toggle('fa-eye', !isPassword);
                    icon.classList.toggle('fa-eye-slash', isPassword);
                });
            }

            const loginForm = document.querySelector('form');
            const loginBtn = document.getElementById('loginBtn');

            if (loginForm && loginBtn) {
                loginForm.addEventListener('submit', function () {
                    loginBtn.disabled = true;
                    loginBtn.classList.add('loading');
                });
            }
        });
    </script>
</body>

</html>
