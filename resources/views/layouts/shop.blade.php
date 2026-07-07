<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=work-sans:400,500,600&family=playfair-display:500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-amiras-cream text-amiras-ink" x-data="{ mobileNavOpen: false }">
        <header class="border-b border-amiras-ink/10 sticky top-0 bg-amiras-cream/95 backdrop-blur z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <a href="{{ route('home') }}" class="font-display text-xl tracking-wide">
                        L'Univers des Amiras
                    </a>

                    <nav class="hidden md:flex items-center gap-8 text-sm uppercase tracking-wide">
                        @foreach ($navCategories as $navCategory)
                            <a
                                href="{{ route('shop.category', $navCategory) }}"
                                class="text-amiras-ink/80 hover:text-amiras-ink border-b border-transparent hover:border-amiras-gold pb-1 transition"
                            >
                                {{ $navCategory->name }}
                            </a>
                        @endforeach
                    </nav>

                    <div class="flex items-center gap-5 text-sm">
                        @auth
                            <a href="{{ route('profile.edit') }}" class="text-amiras-ink/80 hover:text-amiras-ink">Mon compte</a>
                        @else
                            <a href="{{ route('login') }}" class="text-amiras-ink/80 hover:text-amiras-ink">Connexion</a>
                        @endauth

                        <a href="{{ route('shop.cart') }}" class="text-amiras-ink/80 hover:text-amiras-ink flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.994-4.694 2.598-7.172.106-.43-.27-.828-.712-.828H5.106M7.5 14.25L5.106 5.272M7.5 14.25L5.25 18.75m9-14.25L21.75 5.25" />
                            </svg>
                            @if ($cartItemCount > 0)
                                <span>{{ $cartItemCount }}</span>
                            @endif
                        </a>

                        <button
                            type="button"
                            class="md:hidden p-2 -mr-2"
                            @click="mobileNavOpen = !mobileNavOpen"
                            aria-label="Ouvrir le menu"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <nav
                    class="md:hidden pb-4 flex flex-col gap-3 text-sm uppercase tracking-wide"
                    x-show="mobileNavOpen"
                    x-cloak
                >
                    @foreach ($navCategories as $navCategory)
                        <a href="{{ route('shop.category', $navCategory) }}" class="text-amiras-ink/80 hover:text-amiras-ink">
                            {{ $navCategory->name }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>

        <footer class="border-t border-amiras-ink/10 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-sm text-amiras-taupe">
                &copy; {{ now()->year }} L'Univers des Amiras
            </div>
        </footer>
    </body>
</html>
