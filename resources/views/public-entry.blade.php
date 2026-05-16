<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'HR Management') }}</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: #f7f7f2;
            color: #1f2933;
            font-family: Arial, Helvetica, sans-serif;
        }

        main {
            width: min(520px, calc(100% - 32px));
            padding: 40px 32px;
            background: #ffffff;
            border: 1px solid #e4e4dc;
            border-radius: 8px;
            box-shadow: 0 16px 36px rgba(31, 41, 51, 0.08);
            text-align: center;
        }

        h1 {
            margin: 0 0 12px;
            font-size: 28px;
            line-height: 1.2;
        }

        p {
            margin: 0 0 28px;
            color: #667085;
            line-height: 1.6;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        a {
            display: inline-flex;
            min-height: 42px;
            align-items: center;
            justify-content: center;
            padding: 0 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 700;
        }

        .primary {
            background: #23395d;
            color: #ffffff;
        }

        .secondary {
            border: 1px solid #cfd5df;
            color: #23395d;
        }
    </style>
</head>
<body>
    <main>
        <h1>{{ config('app.name', 'HR Management') }}</h1>
        <p>Sign in to manage HR, reservations, reports, and property operations from your workspace.</p>
        <div class="actions">
            @if (Route::has('login'))
                <a href="{{ route('login') }}" class="primary">Login</a>
            @endif
            @if (Route::has('register.tenant'))
                <a href="{{ route('register.tenant') }}" class="secondary">Create company</a>
            @endif
        </div>
    </main>
</body>
</html>
