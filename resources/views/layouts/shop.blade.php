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
                        <a href="{{ route('home') }}" class="text-amiras-ink/80 hover:text-amiras-ink border-b border-transparent hover:border-amiras-gold pb-1 transition">
                            Accueil
                        </a>

                        <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <a
                                href="{{ route('shop.index') }}"
                                class="flex items-center gap-1 text-amiras-ink/80 hover:text-amiras-ink border-b border-transparent hover:border-amiras-gold pb-1 transition"
                            >
                                Catalogue
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </a>

                            <div
                                x-show="open"
                                x-cloak
                                x-transition
                                class="absolute left-0 top-full w-64 bg-white border border-amiras-ink/10 shadow-lg normal-case tracking-normal py-2 z-40"
                            >
                                <a href="{{ route('shop.index') }}" class="block px-4 py-2 text-sm font-medium text-amiras-ink hover:bg-amiras-cream">
                                    Tout le catalogue
                                </a>

                                @foreach ($navCategories as $navCategory)
                                    <div class="border-t border-amiras-ink/10 mt-1 pt-1">
                                        <a
                                            href="{{ route('shop.category', $navCategory['slug']) }}"
                                            class="block px-4 py-2 text-sm text-amiras-ink hover:bg-amiras-cream"
                                        >
                                            {{ $navCategory['name'] }}
                                        </a>

                                        @foreach ($navCategory['children'] as $child)
                                            <a
                                                href="{{ route('shop.category', $child['slug']) }}"
                                                class="block pl-8 pr-4 py-1.5 text-sm text-amiras-ink/70 hover:bg-amiras-cream hover:text-amiras-ink"
                                            >
                                                {{ $child['name'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <a href="{{ route('shop.about') }}" class="text-amiras-ink/80 hover:text-amiras-ink border-b border-transparent hover:border-amiras-gold pb-1 transition">
                            À propos
                        </a>
                        <a href="{{ route('shop.contact') }}" class="text-amiras-ink/80 hover:text-amiras-ink border-b border-transparent hover:border-amiras-gold pb-1 transition">
                            Contact
                        </a>
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
                    <a href="{{ route('home') }}" class="text-amiras-ink/80 hover:text-amiras-ink">Accueil</a>
                    <a href="{{ route('shop.index') }}" class="text-amiras-ink/80 hover:text-amiras-ink">Catalogue</a>

                    @foreach ($navCategories as $navCategory)
                        <a href="{{ route('shop.category', $navCategory['slug']) }}" class="pl-4 normal-case text-amiras-ink/70 hover:text-amiras-ink">
                            {{ $navCategory['name'] }}
                        </a>
                        @foreach ($navCategory['children'] as $child)
                            <a href="{{ route('shop.category', $child['slug']) }}" class="pl-8 normal-case text-amiras-ink/60 hover:text-amiras-ink">
                                {{ $child['name'] }}
                            </a>
                        @endforeach
                    @endforeach

                    <a href="{{ route('shop.about') }}" class="text-amiras-ink/80 hover:text-amiras-ink">À propos</a>
                    <a href="{{ route('shop.contact') }}" class="text-amiras-ink/80 hover:text-amiras-ink">Contact</a>
                </nav>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>

        {{-- Pied de page en traitement "bandeau signature" (ébène + or), en
        écho au hero — referme la page sur la même identité forte. Accroche
        et réseaux sociaux éditables depuis Filament > Contenu du site >
        Réglages du site. --}}
        <footer class="mt-16 bg-amiras-ink text-amiras-cream border-t-2 border-amiras-gold">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 grid grid-cols-2 sm:grid-cols-4 gap-10">
                <div class="col-span-2 sm:col-span-1">
                    <span class="font-display text-xl text-amiras-cream">L'Univers des Amiras</span>

                    @if ($footerSettings->footer_tagline)
                        <p class="mt-2 text-sm text-amiras-cream/60">{{ $footerSettings->footer_tagline }}</p>
                    @endif

                    @if ($footerSettings->social_instagram || $footerSettings->social_facebook || $footerSettings->social_tiktok)
                        <div class="flex items-center gap-4 mt-5">
                            @if ($footerSettings->social_instagram)
                                <a href="{{ $footerSettings->social_instagram }}" target="_blank" rel="noopener" class="text-amiras-cream/60 hover:text-amiras-gold transition" aria-label="Instagram">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75h-9a3.75 3.75 0 00-3.75 3.75v9a3.75 3.75 0 003.75 3.75h9a3.75 3.75 0 003.75-3.75v-9a3.75 3.75 0 00-3.75-3.75z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM16.5 7.5h.008v.008h-.008V7.5z" />
                                    </svg>
                                </a>
                            @endif

                            @if ($footerSettings->social_facebook)
                                <a href="{{ $footerSettings->social_facebook }}" target="_blank" rel="noopener" class="text-amiras-cream/60 hover:text-amiras-gold transition" aria-label="Facebook">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 3H14a4.5 4.5 0 00-4.5 4.5v3H7v3.5h2.5V21H13v-7h2.5l.75-3.5H13v-2c0-.828.672-1.5 1.5-1.5h2.75V3z" />
                                    </svg>
                                </a>
                            @endif

                            @if ($footerSettings->social_tiktok)
                                <a href="{{ $footerSettings->social_tiktok }}" target="_blank" rel="noopener" class="text-amiras-cream/60 hover:text-amiras-gold transition" aria-label="TikTok">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12a4 4 0 104 4V4a5 5 0 005 5" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                <div>
                    <p class="text-xs uppercase tracking-[0.15em] text-amiras-gold mb-3">Boutique</p>
                    <ul class="space-y-2 text-sm text-amiras-cream/70">
                        <li><a href="{{ route('shop.index') }}" class="hover:text-amiras-cream">Catalogue</a></li>
                        @foreach ($navCategories as $navCategory)
                            <li><a href="{{ route('shop.category', $navCategory['slug']) }}" class="hover:text-amiras-cream">{{ $navCategory['name'] }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-[0.15em] text-amiras-gold mb-3">La marque</p>
                    <ul class="space-y-2 text-sm text-amiras-cream/70">
                        <li><a href="{{ route('shop.about') }}" class="hover:text-amiras-cream">À propos</a></li>
                        <li><a href="{{ route('shop.contact') }}" class="hover:text-amiras-cream">Contact</a></li>
                    </ul>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-[0.15em] text-amiras-gold mb-3">Mon compte</p>
                    <ul class="space-y-2 text-sm text-amiras-cream/70">
                        @auth
                            <li><a href="{{ route('profile.edit') }}" class="hover:text-amiras-cream">Mon compte</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-amiras-cream">Connexion</a></li>
                        @endauth
                        <li><a href="{{ route('shop.cart') }}" class="hover:text-amiras-cream">Panier</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-amiras-cream/10">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-sm text-amiras-cream/50">
                    &copy; {{ now()->year }} L'Univers des Amiras
                </div>
            </div>
        </footer>
    </body>
</html>
