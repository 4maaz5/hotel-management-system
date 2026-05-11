<header class="bg-white shadow-cultural-card sticky top-0 z-50">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            {{-- Logo --}}
            <div class="flex items-center space-x-2">
                <img src="{{ asset('Frontend/img/durramah.jpg') }}" alt="Durramah Logo" class="w-20 h-auto">
                <span class="text-xl font-headline font-bold text-primary">{{ __('public.durramah') }}</span>
            </div>

            <div class="hidden md:flex items-center gap-8">
                <a href="#home" class="text-primary font-medium">
                    {{ __('public.home') }}
                </a>

                <a href="#about" class="text-text-secondary">
                    {{ __('public.about_us') }}
                </a>

                <a href="#branches" class="text-text-secondary">
                    {{ __('public.branches') }}
                </a>

                <a href="#rooms" class="text-text-secondary">
                    {{ __('public.rooms') }}
                </a>

                <a href="#gallery" class="text-text-secondary">
                    {{ __('public.gallery') }}
                </a>

                {{-- Language Switch --}}
                <a href="{{ route('lang.switch', app()->getLocale() === 'en' ? 'ar' : 'en') }}"
                    class="px-3 py-1 border rounded-md">
                    {{ app()->getLocale() === 'en' ? 'AR' : 'EN' }}
                </a>
            </div>


            {{-- Mobile Menu Button --}}
            <button id="mobileMenuBtn" class="md:hidden">
                ☰
            </button>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobileMenu" class="hidden md:hidden mt-2">
            <a href="#home" class="block py-2">{{ __('public.home') }}</a>
            <a href="#about" class="block py-2">{{ __('public.about_us') }}</a>
            <a href="#branches" class="block py-2">{{ __('public.branches') }}</a>
            <a href="#rooms" class="block py-2">{{ __('public.rooms') }}</a>
            <a href="#gallery" class="block py-2">{{ __('public.gallery') }}</a>
            {{-- Language Switch --}}
            <a href="{{ route('lang.switch', app()->getLocale() === 'en' ? 'ar' : 'en') }}"
                class="block py-2">
                {{ app()->getLocale() === 'en' ? 'AR' : 'EN' }}
            </a>
        </div>
    </nav>
</header>
