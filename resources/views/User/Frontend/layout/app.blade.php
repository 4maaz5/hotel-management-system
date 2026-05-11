<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Durramah')</title>
    <!-- Favicon -->
    <link rel="icon" type="image/jpg" href="{{ asset('Frontend/img/durramah.jpg') }}">

    <meta name="description" content="@yield('meta_description')">

    <link rel="stylesheet" href="{{ asset('Frontend/css/main.css') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @stack('styles')
</head>

<body class="bg-background text-text-primary">

    {{-- Header --}}
    @include('User.Frontend.layout.header')

    {{-- Page Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('User.Frontend.layout.footer')

    {{-- Scripts --}}
    <script>
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
    </script>

    @stack('scripts')
</body>

</html>
