<!DOCTYPE html>
<html class="h-100" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - Reservation Management</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Styles -->
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
        }

        .reset-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 450px;
            margin: 0 auto;
        }

        .reset-card-body {
            padding: 3rem;
        }

        /* Logo Styling */
        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-svg {
            max-width: 120px;
            height: auto;
            margin: 0 auto 1rem;
        }

        .logo-title {
            color: #333;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .logo-subtitle {
            color: #666;
            font-size: 0.9rem;
        }

        /* Information Alert */
        .info-alert {
            background-color: #f0f8ff;
            border: 1px solid #d0e7ff;
            border-left: 4px solid #667eea;
            border-radius: 10px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }

        .info-alert i {
            color: #667eea;
        }

        /* Success Alert */
        .success-alert {
            background-color: #f0fff4;
            border: 1px solid #c6f6d5;
            border-left: 4px solid #38a169;
            border-radius: 10px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }

        .success-alert i {
            color: #38a169;
        }

        /* Input Group */
        .input-group {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .input-group:focus-within {
            border-color: #667eea;
            box-shadow: 0 2px 20px rgba(102, 126, 234, 0.2);
        }

        .input-group-text {
            background-color: #f8f9fa;
            border: none;
            padding: 1rem 1.25rem;
            color: #667eea;
        }

        .form-control {
            border: none;
            padding: 1rem;
            font-size: 1rem;
        }

        .form-control:focus {
            box-shadow: none;
        }

        /* Error Message */
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

        /* Submit Button */
        .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 1rem;
            font-weight: 600;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            width: 100%;
            color: white;
            margin-top: 1rem;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .submit-btn i {
            margin-right: 8px;
        }

        /* Back to Login Link */
        .back-link {
            color: #667eea;
            text-decoration: none;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            margin-top: 1.5rem;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .back-link i {
            margin-right: 6px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .reset-card-body {
                padding: 2rem;
            }

            body {
                padding: 10px;
            }
        }

        @media (max-width: 576px) {
            .reset-card-body {
                padding: 1.5rem;
            }

            .logo-svg {
                max-width: 100px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="reset-card">
                    <div class="reset-card-body">

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
                            <h3 class="logo-title">Reset Password</h3>
                            <p class="logo-subtitle">Reservation Management System</p>
                        </div>

                        <!-- Forgot Password Form -->
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <!-- Information Alert -->
                            <div class="info-alert">
                                <p class="mb-0">
                                    <i class="fas fa-key me-2"></i>
                                    {{ __('Forgot your password? Enter your email below.') }}
                                </p>
                            </div>

                            <!-- Session Status -->
                            @if (session('status'))
                                <div class="success-alert">
                                    <i class="fas fa-check me-2"></i>
                                    {{ session('status') }}
                                </div>
                            @endif

                            <!-- Email Address -->
                            <div class="mb-4">
                                <label for="email" class="form-label text-muted mb-2">
                                    {{ __('Email Address') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        placeholder="Enter your email address" name="email"
                                        value="{{ old('email') }}" required autofocus>
                                </div>

                                @error('email')
                                    <div class="error-message mt-2">
                                        <i class="fas fa-exclamation-circle error-icon"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="submit-btn">
                                <i class="fas fa-paper-plane me-2"></i>
                                {{ __('Send Reset Link') }}
                            </button>

                            <!-- Back to Login -->
                            <div class="text-center mt-4">
                                <a href="{{ route('login') }}" class="back-link">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    {{ __('Back to Login') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
