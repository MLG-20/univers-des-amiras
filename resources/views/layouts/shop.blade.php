<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white text-gray-900" x-data="{ mobileNavOpen: false }">
        <header class="border-b border-gray-200 sticky top-0 bg-white/95 backdrop-blur z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <a href="{{ route('home') }}" class="font-semibold text-lg tracking-tight">
                        L'Univers des Amiras
                    </a>

                    <nav class="hidden md:flex items-center gap-6 text-sm">
                        @foreach ($navCategories as $navCategory)
                            <a href="{{ route('shop.category', $navCategory) }}" class="text-gray-700 hover:text-gray-950">
                                {{ $navCategory->name }}
                            </a>
                        @endforeach
                    </nav>

                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ route('profile.edit') }}" class="text-sm text-gray-700 hover:text-gray-950">Mon compte</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-950">Connexion</a>
                        @endauth

                        <button
                            type="button"
                            class="md:hidden p-2 -mr-2"
                            @click="mobileNavOpen = !mobileNavOpen"
                            aria-label="Ouvrir le menu"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <nav
                    class="md:hidden pb-4 flex flex-col gap-3 text-sm"
                    x-show="mobileNavOpen"
                    x-cloak
                >
                    @foreach ($navCategories as $navCategory)
                        <a href="{{ route('shop.category', $navCategory) }}" class="text-gray-700 hover:text-gray-950">
                            {{ $navCategory->name }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>

        <footer class="border-t border-gray-200 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-sm text-gray-500">
                &copy; {{ now()->year }} L'Univers des Amiras
            </div>
        </footer>
    </body>
</html>
