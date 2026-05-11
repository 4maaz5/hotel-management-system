<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <title>Forgot Password – {{ config('app.name') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f7fa;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .login-card {
            background: #ffffff;
            border: 1px solid #e5e9f2;
            border-radius: 10px;
            padding: 2rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.06);
        }

        .form-label {
            font-weight: 500;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #4856e6;
        }

        .btn-primary {
            background: #4856e6;
            border: none;
        }

        .btn-primary:hover {
            background: #3b4cd3;
        }

        .back-link {
            text-align: center;
            margin-top: 1rem;
        }

        .back-link a {
            text-decoration: none;
            color: #4856e6;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="login-card">

        <h4 class="text-center mb-3">{{ __('dashboard.forgot_password') }}</h4>
        <p class="text-center text-muted mb-4">{{ __('dashboard.enter_email') }}</p>

        @if (session('status'))
            <div class="alert alert-success text-center">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">{{ __('dashboard.email_address') }}</label>
                <input id="email" type="email" name="email"
                    class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required
                    autofocus>

                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2">
                {{ __('dashboard.send_reset_link') }}
            </button>
        </form>

        <div class="back-link">
            <a href="{{ route('login') }}">← {{ __('dashboard.back_to_login') }}</a>
        </div>

    </div>

</body>

</html>
