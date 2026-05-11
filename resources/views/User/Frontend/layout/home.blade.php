@extends('User.Frontend.layout.app')

@section('title', 'DRRRAMH – Hospitality Without Borders')

@section('meta_description')
    Experience seamless hotel booking with cultural comfort.
@endsection

@section('content')

    <!-- Hero Section -->
    <section class="relative bg-gradient-hospitality overflow-hidden" id="home">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img src="{{ asset('Frontend/img/DRBZ.jpeg') }}"
                alt="Luxury hotel lobby with warm lighting and cultural decor showcasing hospitality excellence"
                class="w-full h-full object-cover opacity-25"
                onerror="this.src='https://images.pexels.com/photos/258154/pexels-photo-258154.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2'; this.onerror=null;">
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 lg:py-32">
            <div class="flex justify-between items-center gap-8">

                <div class="max-w-2xl text-right hero-text-wrapper hidden sm:block" dir="ltr">
                    <h1 class="font-headline font-extrabold mb-6 heading
       text-6xl sm:text-7xl lg:text-8xl xl:text-9xl
       leading-none"
                        style="color: rgba(255,255,255,0.95);">
                        {{ __('public.durramah') }}
                    </h1>

                    <h3
                        class="font-headline font-extrabold mb-6 description
       text-6xl sm:text-7xl lg:text-8xl xl:text-9xl
       leading-none text-primary">
                        {{ __('public.hospitality_group') }}
                    </h3>
                </div>


                <!-- Right side: Main Text -->
                <div class="max-w-2xl text-right hero-text-wrapper" dir="ltr">
                    <h1 class="text-4xl lg:text-6xl font-headline font-bold mb-6" style="color: rgba(255,255,255,0.9);">
                        {{ __('public.hospitality_without') }}
                        <span class="text-primary">{{ __('public.borders') }}</span>
                    </h1>

                    <p class="text-xl mb-8" style="color: rgba(255,255,255,0.8);">
                        {{ __('public.experience_seamless_cultural_comfort_everywhere') }}
                    </p>
                </div>
            </div>
        </div>

    </section>



    <!-- About Us -->
    <section class="py-16 bg-white" id="about">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Section Header -->
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-headline font-bold text-text-primary mb-4">
                    {{ __('public.about') }} <span class="text-primary">{{ __('public.durramah') }}</span>
                </h2>
                <p class="text-lg text-text-secondary max-w-2xl mx-auto">
                    {{ __('public.Durramah_is_built_on_the_belief') }}

                </p>
            </div>

            <!-- About Highlights -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- Our Vision -->
                <div class="text-center space-y-4">
                    <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-headline font-bold text-text-primary">
                        {{ __('public.our_vision') }}
                    </h3>
                    <p class="text-text-secondary">
                        {{ __('public.to_redefine_hospitality_by_connecting') }}

                    </p>
                </div>

                <!-- Our Mission -->
                <div class="text-center space-y-4">
                    <div class="w-16 h-16 bg-secondary-100 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 11c0-3.866-3.582-7-8-7m8 7c0-3.866 3.582-7 8-7m-8 7v7m0 0H4m8 0h8">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-headline font-bold text-text-primary">
                        {{ __('public.our_mission') }}
                    </h3>
                    <p class="text-text-secondary">
                        {{ __('public.to_deliver_culturally_aware_hospitality') }}

                    </p>
                </div>

                <!-- Our Values -->
                <div class="text-center space-y-4">
                    <div class="w-16 h-16 bg-accent-100 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-10V4m0 14v2m-6-6H6m12 0h-2">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-headline font-bold text-text-primary">
                        {{ __('public.our_values') }}
                    </h3>
                    <p class="text-text-secondary">
                        {{ __('public.authentic_hospitality_cultural_respect') }}

                    </p>
                </div>

            </div>
        </div>
    </section>

    @if ($branches && $branches->isNotEmpty())
        <!-- Our Branches Section -->
        <section class="py-20 bg-gray-50 mt-2" id="branches">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Section Header -->
                <div class="text-center mb-14">
                    <h2 class="text-3xl lg:text-5xl font-headline font-bold text-text-primary mb-4">
                        {{ __('public.our') }} <span class="text-primary">{{ __('public.branches') }}</span>
                    </h2>
                    <p class="text-lg text-text-secondary max-w-2xl mx-auto">
                        {{ __('public.discover_our_carefully_located_branches') }}
                    </p>
                </div>

                <!-- Branch Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                    @php
                        // Static images for branches
                        $branchImages = [
                            'https://images.unsplash.com/photo-1566073771259-6a8506099945', // Riyadh
                            'https://images.unsplash.com/photo-1505693314120-0d443867891c', // Jeddah
                            'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b', // Dubai
                            'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688', // Makkah
                            'https://images.unsplash.com/photo-1493809842364-78817add7ffb', // Madinah
                            'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267', // Doha
                        ];

                        // Static ratings for branches
                        $branchRatings = [4.8, 4.7, 4.9, 4.8, 4.9, 4.7];
                    @endphp

                    @foreach ($branches->slice(0, 6) as $index => $branch)
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition">
                            <img src="{{ $branchImages[$index] ?? 'https://via.placeholder.com/400x200' }}"
                                alt="{{ $branch->name }}" class="w-full h-48 object-cover">

                            <div class="p-6">
                                <h3 class="text-xl font-semibold text-text-primary mb-2">
                                    {{ __('public.branch_name') }}:
                                    {{ $branch->name }}
                                </h3>
                                <p class="text-text-secondary mb-3">
                                    {{ __('public.branch_location') }}:
                                    {{ $branch->location }}
                                </p>
                                <div class="flex items-center justify-between text-sm text-text-secondary">
                                    <span>{{ __('public.ratings') }}
                                        ⭐ {{ $branchRatings[$index] ?? '4.8' }} </span>
                                    <span>📞 {{ $branch->phone }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>
    @endif



    <!-- Rooms Showcase Section -->
    <section class="py-20 bg-gray-50 mt-2" id="rooms">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Section Header -->
            <div class="text-center mb-14">
                <h2 class="text-3xl lg:text-5xl font-headline font-bold text-text-primary mb-4">
                    {{ __('public.our') }} <span class="text-primary">{{ __('public.rooms') }}</span>
                </h2>
                <p class="text-lg text-text-secondary max-w-3xl mx-auto">
                    {{ __('public.thoughtfully_designed_spaces') }}

                </p>
            </div>

            <!-- Rooms Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- Room 1 -->
                <div class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1505693314120-0d443867891c" alt="Deluxe Room"
                            class="w-full h-64 object-cover transform group-hover:scale-105 transition duration-500">
                    </div>
                    <div class="p-6 text-center">
                        <h3 class="text-xl font-semibold text-text-primary mb-2">{{ __('public.deluxe_room') }}</h3>
                        <p class="text-text-secondary">{{ __('public.elegant_interiors_with_modern_amenities') }}

                        </p>
                    </div>
                </div>

                <!-- Room 2 -->
                <div class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2" alt="Executive Suite"
                            class="w-full h-64 object-cover transform group-hover:scale-105 transition duration-500">
                    </div>
                    <div class="p-6 text-center">
                        <h3 class="text-xl font-semibold text-text-primary mb-2">{{ __('public.executive_suite') }}</h3>
                        <p class="text-text-secondary">{{ __('public.spacious_suites_offering') }}

                        </p>
                    </div>
                </div>

                <!-- Room 3 -->
                <div class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427" alt="Family Room"
                            class="w-full h-64 object-cover transform group-hover:scale-105 transition duration-500">
                    </div>
                    <div class="p-6 text-center">
                        <h3 class="text-xl font-semibold text-text-primary mb-2">{{ __('public.family_room') }}</h3>
                        <p class="text-text-secondary">{{ __('public.designed_for_families') }}

                        </p>
                    </div>
                </div>

                <!-- Room 4 -->
                <div class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1502672260266-1c1ef2d93688" alt="Superior Room"
                            class="w-full h-64 object-cover transform group-hover:scale-105 transition duration-500">
                    </div>
                    <div class="p-6 text-center">
                        <h3 class="text-xl font-semibold text-text-primary mb-2">{{ __('public.superior_room') }}</h3>
                        <p class="text-text-secondary">{{ __('public.a_perfect_balance_of_comfort') }}

                        </p>
                    </div>
                </div>

                <!-- Room 5 -->
                <div class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267" alt="Premium King Room"
                            class="w-full h-64 object-cover transform group-hover:scale-105 transition duration-500">
                    </div>
                    <div class="p-6 text-center">
                        <h3 class="text-xl font-semibold text-text-primary mb-2">{{ __('public.premium_king_room') }}</h3>
                        <p class="text-text-secondary">{{ __('public.luxurious_king_sized') }}

                        </p>
                    </div>
                </div>

                <!-- Room 6 -->
                <div class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition">
                    <div class="relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1493809842364-78817add7ffb" alt="Junior Suite"
                            class="w-full h-64 object-cover transform group-hover:scale-105 transition duration-500">
                    </div>
                    <div class="p-6 text-center">
                        <h3 class="text-xl font-semibold text-text-primary mb-2">{{ __('public.junior_suite') }}</h3>
                        <p class="text-text-secondary">{{ __('public.contemporary_design_with') }}

                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <!-- Gallery Section -->
    <section class="py-20 bg-white" id="gallery">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Section Header -->
            <div class="text-center mb-14">
                <h2 class="text-3xl lg:text-5xl font-headline font-bold text-text-primary mb-4">
                    {{ __('public.our') }} <span class="text-primary">{{ __('public.gallery') }}</span>
                </h2>
                <p class="text-lg text-text-secondary max-w-3xl mx-auto">
                    {{ __('public.a_visual_journey_through_our_spaces') }}

                </p>
            </div>

            <!-- Gallery Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

                <!-- Gallery Item -->
                <div class="group relative overflow-hidden rounded-2xl">
                    <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427" alt="Hotel Lobby"
                        class="w-full h-full object-cover aspect-square transform group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition"></div>
                </div>

                <!-- Gallery Item -->
                <div class="group relative overflow-hidden rounded-2xl">
                    <img src="https://images.unsplash.com/photo-1505693314120-0d443867891c" alt="Luxury Room"
                        class="w-full h-full object-cover aspect-square transform group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition"></div>
                </div>

                <!-- Gallery Item -->
                <div class="group relative overflow-hidden rounded-2xl">
                    <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2" alt="Suite Interior"
                        class="w-full h-full object-cover aspect-square transform group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition"></div>
                </div>

                <!-- Gallery Item -->
                <div class="group relative overflow-hidden rounded-2xl">
                    <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427" alt="Family Room"
                        class="w-full h-full object-cover aspect-square transform group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition"></div>
                </div>

                <!-- Gallery Item -->
                <div class="group relative overflow-hidden rounded-2xl">
                    <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267" alt="Hotel Corridor"
                        class="w-full h-full object-cover aspect-square transform group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition"></div>
                </div>

                <!-- Gallery Item -->
                <div class="group relative overflow-hidden rounded-2xl">
                    <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b" alt="Hotel Exterior"
                        class="w-full h-full object-cover aspect-square transform group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition"></div>
                </div>

                <!-- Gallery Item -->
                <div class="group relative overflow-hidden rounded-2xl">
                    <img src="https://images.unsplash.com/photo-1502672260266-1c1ef2d93688" alt="Luxury Bathroom"
                        class="w-full h-full object-cover aspect-square transform group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition"></div>
                </div>

                <!-- Gallery Item -->
                <div class="group relative overflow-hidden rounded-2xl">
                    <img src="https://images.unsplash.com/photo-1493809842364-78817add7ffb" alt="Hotel Lounge"
                        class="w-full h-full object-cover aspect-square transform group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition"></div>
                </div>

            </div>
        </div>
    </section>



    <!-- Guest Reviews Section -->
    <section class="py-20 bg-white mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 ">

            <!-- Section Header -->
            <div class="text-center mb-14">
                <h2 class="text-3xl lg:text-5xl font-headline font-bold text-text-primary mb-4">
                    {{ __('public.guest') }} <span class="text-primary">{{ __('public.reviews') }}</span>
                </h2>
                <p class="text-lg text-text-secondary max-w-2xl mx-auto">{{ __('public.here_what_our_valued_guests') }}

                </p>
            </div>

            <!-- Reviews Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- Review Card 1 -->
                <div class="bg-gray-50 rounded-2xl p-6 shadow hover:shadow-lg transition">
                    <div class="flex items-center mb-4 space-x-4">
                        <img src="https://randomuser.me/api/portraits/men/75.jpg" alt="Arabic Guest"
                            class="w-14 h-14 rounded-full object-cover flex-shrink-0">
                        <div class="flex flex-col justify-center">
                            <h4 class="font-semibold text-text-primary">Ahmed Al-Fahad</h4>
                            <p class="text-sm text-text-secondary">Riyadh, KSA</p>
                        </div>
                    </div>

                    <p class="text-text-secondary mb-4">
                        {{ __('public.exceptional_service_and_warm_hospitality') }}
                    </p>

                    <span class="text-primary font-medium">★★★★★</span>
                </div>

                <!-- Review Card 2 -->
                <div class="bg-gray-50 rounded-2xl p-6 shadow hover:shadow-lg transition">
                    <div class="flex items-center mb-4 space-x-4"> <!-- Added mb-4 and space-x-4 -->
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Arabic Guest"
                            class="w-14 h-14 rounded-full object-cover flex-shrink-0">
                        <div class="flex flex-col justify-center"> <!-- Added flex-col justify-center -->
                            <h4 class="font-semibold text-text-primary">Fatima Al-Harbi</h4>
                            <p class="text-sm text-text-secondary">Jeddah, KSA</p>
                        </div>
                    </div>

                    <p class="text-text-secondary mb-4">
                        {{ __('public.beautiful_rooms') }}
                    </p>

                    <span class="text-primary font-medium">★★★★★</span>
                </div>

                <!-- Review Card 3 -->
                <div class="bg-gray-50 rounded-2xl p-6 shadow hover:shadow-lg transition">
                    <div class="flex items-center mb-4 space-x-4"> <!-- Added mb-4 and space-x-4 -->
                        <img src="https://randomuser.me/api/portraits/men/81.jpg" alt="Arabic Guest"
                            class="w-14 h-14 rounded-full object-cover flex-shrink-0">
                        <div class="flex flex-col justify-center"> <!-- Added flex-col justify-center -->
                            <h4 class="font-semibold text-text-primary">Omar Al-Zahrani</h4>
                            <p class="text-sm text-text-secondary">Dubai, UAE</p>
                        </div>
                    </div>

                    <p class="text-text-secondary mb-4">
                        {{ __('public.perfect_location') }}
                    </p>

                    <span class="text-primary font-medium">★★★★★</span>
                </div>

            </div>

        </div>
    </section>


    {{-- HERO SECTION --}}
    <section class="relative bg-gradient-hospitality py-24">
        <div class="text-center max-w-4xl mx-auto">
            <h1 class="text-5xl font-bold">
                {{ __('public.hospitality_without') }} <span class="text-primary">{{ __('public.borders') }}</span>
            </h1>

            <p class="mt-6 text-lg text-text-secondary">
                {{ __('public.discover_authentic_hospitality_worldwide') }}

            </p>
        </div>
    </section>

    {{-- TRUST INDICATORS --}}
    <section class="py-12 bg-white">
        <div class="grid grid-cols-2 md:grid-cols-4 text-center gap-6">
            <div><strong>50,000+</strong><br>{{ __('public.hotels') }}</div>
            <div><strong>2M+</strong><br>{{ __('public.guests') }}</div>
            <div><strong>4.8★</strong><br>{{ __('public.ratings') }}</div>
            <div><strong>24/7</strong><br>{{ __('public.support') }}</div>
        </div>
    </section>

    <!-- Newsletter Signup -->
    <section class="py-16 bg-gradient-primary text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl lg:text-4xl font-headline font-bold mb-4">
                {{ __('public.stay_connected_to_cultural_hospitality') }}
            </h2>
            <p class="text-lg opacity-90 mb-8 max-w-2xl mx-auto">
                {{ __('public.get_exclusive_cultural_travel') }}

            </p>

            <div class="max-w-md mx-auto">
                <div class="flex flex-col sm:flex-row gap-4">
                    <input type="email" placeholder="{{ __('public.enter_your_email_address') }}"
                        class="flex-1 px-4 py-3 rounded-lg text-text-primary focus:outline-none focus:ring-2 focus:ring-white/20">
                    <button
                        class="bg-white text-primary font-cta font-bold px-6 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                        {{ __('public.subscribe') }}
                    </button>
                </div>
            </div>
        </div>
    </section>
@endsection
