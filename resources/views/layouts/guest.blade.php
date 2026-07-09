<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=work-sans:400,500,600&family=playfair-display:500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex">
            {{-- Panneau de marque : même traitement "bandeau signature" (ébène
            + or) que le hero de l'accueil, pour une première impression
            premium cohérente sur tout le parcours d'authentification. --}}
            <div class="hidden lg:flex lg:w-1/2 relative flex-col justify-between p-12 bg-amiras-ink text-amiras-cream overflow-hidden">
                <div class="absolute inset-0 opacity-25 bg-[radial-gradient(circle_at_20%_20%,rgba(184,146,63,0.6),transparent_60%)]"></div>
                <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_85%_85%,rgba(184,146,63,0.5),transparent_55%)]"></div>

                <a href="{{ route('home') }}" class="relative font-display text-2xl tracking-wide">
                    L'Univers des Amiras
                </a>

                <div class="relative">
                    <p class="font-display text-3xl xl:text-4xl leading-snug max-w-md">
                        L'élégance voilée, pensée pour vous.
                    </p>
                    <p class="mt-4 text-amiras-cream/70 max-w-sm">
                        Suivez vos commandes, gérez vos adresses et retrouvez votre univers en un instant.
                    </p>
                </div>

                <p class="relative text-xs text-amiras-cream/50">&copy; {{ now()->year }} L'Univers des Amiras</p>
            </div>

            <div class="flex-1 flex flex-col justify-center items-center bg-amiras-cream px-6 py-16">
                <div class="w-full max-w-sm">
                    <a href="{{ route('home') }}" class="lg:hidden block text-center font-display text-xl mb-10 text-amiras-ink">
                        L'Univers des Amiras
                    </a>

                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
