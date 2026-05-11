@php
    $theme = \App\Models\ThemeCustomization::getTheme();
@endphp

<!DOCTYPE html>
<html class="h-100" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reservation Management</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Login Styles -->
    <style>
        body {
            background: linear-gradient(135deg, {{ $theme->login_bg_color }} 0%, {{ $theme->login_bg_color }} 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px;
            color: {{ $theme->login_text_color }};
        }

        .login-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background: {{ $theme->login_card_bg }};
        }

        .login-card-body {
            padding: 3rem;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 3rem;
        }

        .logo-svg {
            max-width: 180px;
            height: auto;
            margin: 0 auto;
        }

        /* Input Group Styling */
        .input-group {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid {{ $theme->input_border_color }};
            transition: all 0.3s ease;
        }

        .input-group:focus-within {
            border-color: {{ $theme->button_primary_color }};
            box-shadow: 0 2px 20px rgba(102, 126, 234, 0.2);
        }

        .input-group-text {
            background-color: {{ $theme->input_bg_color }};
            border: none;
            padding: 1rem 1.25rem;
            color: {{ $theme->button_primary_color }};
        }

        .form-control {
            border: none;
            padding: 1rem;
            font-size: 1rem;
            background-color: {{ $theme->input_bg_color }};
            color: {{ $theme->input_text_color }};
        }

        .form-control:focus {
            box-shadow: none;
            background-color: {{ $theme->input_bg_color }};
            color: {{ $theme->input_text_color }};
        }

        .form-control::placeholder {
            color: #999;
        }

        /* Password Toggle Button */
        .password-toggle-btn {
            background-color: {{ $theme->input_bg_color }};
            border: none;
            color: {{ $theme->button_primary_color }};
            padding: 0 1.25rem;
            cursor: pointer;
        }

        .password-toggle-btn:hover {
            background-color: {{ $theme->table_row_even }};
        }

        /* Remember Me & Forgot Password */
        .form-check-input:checked {
            background-color: {{ $theme->button_primary_color }};
            border-color: {{ $theme->button_primary_color }};
        }

        .form-check-label {
            color: #666;
            font-size: 0.9rem;
        }

        .forgot-password-link {
            color: {{ $theme->button_primary_color }};
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        .forgot-password-link:hover {
            color: {{ $theme->button_secondary_color }};
            text-decoration: underline;
        }

        /* Login Button */
        .login-btn {
            background: linear-gradient(135deg, {{ $theme->button_primary_color }} 0%, {{ $theme->button_primary_color }} 100%);
            border: none;
            padding: 1rem;
            font-weight: 600;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .login-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
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

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        /* Error Messages */
        .error-message {
            background-color: #ffe6e6;
            border-left: 4px solid #dc3545;
            padding: 0.75rem 1rem;
            border-radius: 5px;
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }

        .error-icon {
            color: #dc3545;
            margin-right: 8px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-card-body {
                padding: 2rem;
            }

            body {
                padding: 10px;
            }

            .logo-svg {
                max-width: 150px;
            }
        }

        @media (max-width: 576px) {
            .login-card-body {
                padding: 1.5rem;
            }

            .logo-svg {
                max-width: 120px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-card">
                    <div class="login-card-body">
                        <!-- Logo -->
                        <div class="logo-container">
                            <svg viewBox="0 0 316 316" xmlns="http://www.w3.org/2000/svg" class="logo-svg">
                                <circle cx="158" cy="158" r="150" fill="url(#gradient)" />
                                <text x="158" y="158" text-anchor="middle" dy="0.35em"
                                    font-family="Arial, sans-serif" font-size="80" font-weight="bold" fill="white">
                                    B-IT
                                </text>
                                <path d="M80 100 L150 100 L150 130 L80 130 Z" fill="#ffffff" opacity="0.2" />
                                <path d="M166 100 L236 100 L236 130 L166 130 Z" fill="#ffffff" opacity="0.2" />
                                <circle cx="110" cy="210" r="15" fill="#ffffff" opacity="0.3" />
                                <circle cx="206" cy="210" r="15" fill="#ffffff" opacity="0.3" />
                                <defs>
                                    <linearGradient id="gradient" x1="0%" y1="0%" x2="100%"
                                        y2="100%">
                                        <stop offset="0%" stop-color="#667eea" />
                                        <stop offset="100%" stop-color="#764ba2" />
                                    </linearGradient>
                                </defs>
                            </svg>
                        </div>

                        <!-- Login Form -->
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Email Input -->
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        placeholder="Email Address" name="email" value="{{ old('email') }}" required
                                        autofocus autocomplete="email">
                                </div>
                                @error('email')
                                    <div class="error-message mt-2">
                                        <i class="fas fa-exclamation-circle error-icon"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Password Input -->
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Password" name="password" required autocomplete="current-password">
                                    <button class="password-toggle-btn" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="error-message mt-2">
                                        <i class="fas fa-exclamation-circle error-icon"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Remember Me & Forgot Password -->
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

                            <!-- Submit Button -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn login-btn text-white" id="loginBtn">
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    <span class="btn-text">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        {{ __('Sign In') }}
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Password Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            if (toggleButton && passwordInput) {
                toggleButton.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    // Toggle eye icon
                    const icon = this.querySelector('i');
                    if (type === 'password') {
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    } else {
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    }
                });
            }

            // Loading state for login button
            const loginForm = document.querySelector('form[action="{{ route('login') }}"]');
            const loginBtn = document.getElementById('loginBtn');

            if (loginForm && loginBtn) {
                loginForm.addEventListener('submit', function() {
                    loginBtn.disabled = true;
                    loginBtn.classList.add('loading');
                });
            }
        });
    </script>
</body>

</html>
