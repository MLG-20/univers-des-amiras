<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600&family=fraunces:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body
        class="font-sans antialiased bg-brand-surface text-brand-ink"
        x-data="{ mobileNavOpen: false, cartOpen: {{ session('cart_opened') ? 'true' : 'false' }} }"
        :class="(cartOpen || mobileNavOpen) && 'overflow-hidden'"
    >
        <header
            x-data="{ scrolled: false }"
            x-init="scrolled = window.pageYOffset > 20"
            @scroll.window="scrolled = window.pageYOffset > 20"
            @if ($transparentHeader)
                {{-- Accueil : transparent sur le hero sombre (texte crème), puis
                     fond crème opaque dès qu'on scrolle OU que le menu mobile s'ouvre. --}}
                :class="(scrolled || mobileNavOpen)
                    ? 'bg-brand-surface/95 backdrop-blur border-brand-ink/10 text-brand-ink shadow-sm'
                    : 'bg-transparent border-transparent text-brand-surface'"
                class="fixed top-0 inset-x-0 z-30 border-b transition-colors duration-300"
            @else
                class="sticky top-0 z-30 border-b border-brand-ink/10 bg-brand-surface/95 backdrop-blur text-brand-ink"
            @endif
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <a href="{{ route('home') }}" class="font-display text-lg sm:text-xl tracking-wide whitespace-nowrap">
                        Aissatou Store
                    </a>

                    <nav class="hidden md:flex items-center gap-8 text-sm uppercase tracking-wide">
                        <a href="{{ route('home') }}" class="text-current opacity-80 hover:opacity-100 border-b border-transparent hover:border-brand-signature pb-1 transition">
                            Accueil
                        </a>

                        <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <a
                                href="{{ route('shop.index') }}"
                                class="flex items-center gap-1 text-current opacity-80 hover:opacity-100 border-b border-transparent hover:border-brand-signature pb-1 transition"
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
                                class="absolute left-0 top-full w-64 bg-white border border-brand-ink/10 shadow-lg normal-case tracking-normal py-2 z-40"
                            >
                                <a href="{{ route('shop.index') }}" class="block px-4 py-2 text-sm font-medium text-brand-ink hover:bg-brand-surface">
                                    Tout le catalogue
                                </a>

                                @foreach ($navCategories as $navCategory)
                                    <div class="border-t border-brand-ink/10 mt-1 pt-1">
                                        <a
                                            href="{{ route('shop.category', $navCategory['slug']) }}"
                                            class="block px-4 py-2 text-sm text-brand-ink hover:bg-brand-surface"
                                        >
                                            {{ $navCategory['name'] }}
                                        </a>

                                        @foreach ($navCategory['children'] as $child)
                                            <a
                                                href="{{ route('shop.category', $child['slug']) }}"
                                                class="block pl-8 pr-4 py-1.5 text-sm text-brand-ink/70 hover:bg-brand-surface hover:text-brand-ink"
                                            >
                                                {{ $child['name'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <a href="{{ route('shop.about') }}" class="text-current opacity-80 hover:opacity-100 border-b border-transparent hover:border-brand-signature pb-1 transition">
                            À propos
                        </a>
                        <a href="{{ route('shop.contact') }}" class="text-current opacity-80 hover:opacity-100 border-b border-transparent hover:border-brand-signature pb-1 transition">
                            Contact
                        </a>
                    </nav>

                    <div class="flex items-center gap-3 sm:gap-5 text-sm">
                        @auth
                            <a href="{{ route('dashboard') }}" class="hidden md:inline-block text-current opacity-80 hover:opacity-100">Mon compte</a>
                        @else
                            <a href="{{ route('login') }}" class="hidden md:inline-block text-current opacity-80 hover:opacity-100">Connexion</a>
                        @endauth

                        <button type="button" @click="cartOpen = true" class="relative text-current opacity-80 hover:opacity-100" aria-label="Ouvrir le panier">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
                            </svg>
                            @if ($cartItemCount > 0)
                                <span class="absolute -right-2 -top-2 flex h-5 w-5 items-center justify-center rounded-full bg-brand-accent text-[10px] font-medium text-brand-surface">{{ $cartItemCount }}</span>
                            @endif
                        </button>

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
                    x-transition.opacity.duration.200ms
                    x-cloak
                >
                    <a href="{{ route('home') }}" class="text-current opacity-80 hover:opacity-100">Accueil</a>
                    {{-- Catalogue repliable : sous-catégories cachées par défaut ;
                         l'utilisateur déplie/replie via le chevron. --}}
                    <div x-data="{ catOpen: false }">
                        <button
                            type="button"
                            @click="catOpen = !catOpen"
                            class="flex w-full items-center justify-between text-current opacity-80 hover:opacity-100"
                            :aria-expanded="catOpen"
                        >
                            <span>Catalogue</span>
                            <svg class="h-4 w-4 transition-transform duration-200" :class="catOpen && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>

                        <div x-show="catOpen" x-cloak x-transition.opacity.duration.200ms class="mt-3 flex flex-col gap-3">
                            <a href="{{ route('shop.index') }}" class="pl-4 normal-case text-brand-ink/70 hover:text-brand-ink">Tout le catalogue</a>

                            @foreach ($navCategories as $navCategory)
                                <a href="{{ route('shop.category', $navCategory['slug']) }}" class="pl-4 normal-case text-brand-ink/70 hover:text-brand-ink">
                                    {{ $navCategory['name'] }}
                                </a>
                                @foreach ($navCategory['children'] as $child)
                                    <a href="{{ route('shop.category', $child['slug']) }}" class="pl-8 normal-case text-brand-ink/60 hover:text-brand-ink">
                                        {{ $child['name'] }}
                                    </a>
                                @endforeach
                            @endforeach
                        </div>
                    </div>

                    <a href="{{ route('shop.about') }}" class="text-current opacity-80 hover:opacity-100">À propos</a>
                    <a href="{{ route('shop.contact') }}" class="text-current opacity-80 hover:opacity-100">Contact</a>

                    {{-- Compte + panier : présents dans le menu mobile puisque masqués
                         dans la barre du haut sur petit écran. --}}
                    <div class="mt-2 flex flex-col gap-3 border-t border-brand-ink/10 pt-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-current opacity-80 hover:opacity-100">Mon compte</a>
                        @else
                            <a href="{{ route('login') }}" class="text-current opacity-80 hover:opacity-100">Connexion</a>
                        @endauth
                        <button type="button" @click="mobileNavOpen = false; cartOpen = true" class="text-left text-current opacity-80 hover:opacity-100">
                            Panier
                            @if ($cartItemCount > 0)
                                ({{ $cartItemCount }})
                            @endif
                        </button>
                    </div>
                </nav>
            </div>
        </header>

        @include('shop.partials.cart-drawer')

        <main>
            {{ $slot }}
        </main>

        {{-- Pied de page en traitement "bandeau signature" (ébène + or), en
        écho au hero — referme la page sur la même identité forte. Accroche
        et réseaux sociaux éditables depuis Filament > Contenu du site >
        Réglages du site. --}}
        <footer class="mt-16 bg-brand-ink text-brand-surface border-t-2 border-brand-signature">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 grid grid-cols-2 sm:grid-cols-4 gap-10">
                <div class="col-span-2 sm:col-span-1">
                    <span class="font-display text-xl text-brand-surface">Aissatou Store</span>
                    {{-- Signature officielle de la marque (rapport d'identité p.5) :
                         le slogan accompagne le nom sur les supports larges. --}}
                    <p class="mt-1 text-[0.65rem] uppercase tracking-[0.25em] text-brand-sage">L'élégance dans la pudeur</p>

                    @if ($footerSettings->footer_tagline)
                        <p class="mt-2 text-sm text-brand-surface/60">{{ $footerSettings->footer_tagline }}</p>
                    @endif

                    @if ($footerSettings->social_instagram || $footerSettings->social_facebook || $footerSettings->social_tiktok)
                        <div class="flex items-center gap-4 mt-5">
                            @if ($footerSettings->social_instagram)
                                <a href="{{ $footerSettings->social_instagram }}" target="_blank" rel="noopener" class="text-brand-surface/60 hover:text-brand-accent transition" aria-label="Instagram">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75h-9a3.75 3.75 0 00-3.75 3.75v9a3.75 3.75 0 003.75 3.75h9a3.75 3.75 0 003.75-3.75v-9a3.75 3.75 0 00-3.75-3.75z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM16.5 7.5h.008v.008h-.008V7.5z" />
                                    </svg>
                                </a>
                            @endif

                            @if ($footerSettings->social_facebook)
                                <a href="{{ $footerSettings->social_facebook }}" target="_blank" rel="noopener" class="text-brand-surface/60 hover:text-brand-accent transition" aria-label="Facebook">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 3H14a4.5 4.5 0 00-4.5 4.5v3H7v3.5h2.5V21H13v-7h2.5l.75-3.5H13v-2c0-.828.672-1.5 1.5-1.5h2.75V3z" />
                                    </svg>
                                </a>
                            @endif

                            @if ($footerSettings->social_tiktok)
                                <a href="{{ $footerSettings->social_tiktok }}" target="_blank" rel="noopener" class="text-brand-surface/60 hover:text-brand-accent transition" aria-label="TikTok">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12a4 4 0 104 4V4a5 5 0 005 5" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                <div>
                    <p class="text-xs uppercase tracking-[0.15em] text-brand-sage mb-3">Boutique</p>
                    <ul class="space-y-2 text-sm text-brand-surface/70">
                        <li><a href="{{ route('shop.index') }}" class="hover:text-brand-surface">Catalogue</a></li>
                        @foreach ($navCategories as $navCategory)
                            <li><a href="{{ route('shop.category', $navCategory['slug']) }}" class="hover:text-brand-surface">{{ $navCategory['name'] }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-[0.15em] text-brand-sage mb-3">La marque</p>
                    <ul class="space-y-2 text-sm text-brand-surface/70">
                        <li><a href="{{ route('shop.about') }}" class="hover:text-brand-surface">À propos</a></li>
                        <li><a href="{{ route('shop.contact') }}" class="hover:text-brand-surface">Contact</a></li>
                    </ul>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-[0.15em] text-brand-sage mb-3">Mon compte</p>
                    <ul class="space-y-2 text-sm text-brand-surface/70">
                        @auth
                            <li><a href="{{ route('profile.edit') }}" class="hover:text-brand-surface">Mon compte</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-brand-surface">Connexion</a></li>
                        @endauth
                        <li><a href="{{ route('shop.cart') }}" class="hover:text-brand-surface">Panier</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-brand-surface/10">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-sm text-brand-surface/50">
                    &copy; {{ now()->year }} Aissatou Store
                </div>
            </div>
        </footer>
    </body>
</html>
