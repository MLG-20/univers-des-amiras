<x-shop-layout :title="'Mon compte — '.config('app.name')">
    @php
        $user = auth()->user();
        $firstName = \Illuminate\Support\Str::of($user->name)->trim()->explode(' ')->first();

        // Tuiles d'accès rapide. `icon` = tableau de tracés SVG (viewBox 24).
        $tiles = [
            [
                'route' => 'account.orders.index',
                'title' => 'Mes commandes',
                'desc' => 'Suivez l\'état de vos commandes et votre historique.',
                'icon' => ['M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z'],
            ],
            [
                'route' => 'account.addresses.index',
                'title' => 'Mes adresses',
                'desc' => 'Gérez vos adresses de livraison en un instant.',
                'icon' => ['M15 10.5a3 3 0 11-6 0 3 3 0 016 0z', 'M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z'],
            ],
            [
                'route' => 'profile.edit',
                'title' => 'Mes informations',
                'desc' => 'Nom, e-mail et mot de passe.',
                'icon' => ['M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
            ],
            [
                'route' => 'shop.index',
                'title' => 'Continuer mes achats',
                'desc' => 'Découvrez les nouveautés et collections.',
                'icon' => ['M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z', 'M18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z'],
            ],
            [
                'route' => 'shop.cart',
                'title' => 'Mon panier',
                'desc' => 'Retrouvez les articles en attente.',
                'icon' => ['M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.994-4.694 2.598-7.172.106-.43-.27-.828-.712-.828H5.106M7.5 14.25L5.106 5.272M7.5 14.25L5.25 18.75m9-14.25L21.75 5.25'],
            ],
        ];
    @endphp

    {{-- En-tête chaleureux, personnalisé — même traitement premium (ébène + or)
    que le hero de l'accueil, pour une première impression cohérente après
    connexion. --}}
    <section class="relative overflow-hidden bg-brand-ink text-brand-surface">
        <div class="absolute inset-0 opacity-25 bg-[radial-gradient(circle_at_15%_20%,rgba(74,24,51,0.55),transparent_60%)]"></div>
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_85%_90%,rgba(74,24,51,0.5),transparent_55%)]"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-16">
            <span class="text-xs uppercase tracking-[0.3em] text-brand-sage">Votre espace</span>
            <h1 class="mt-3 font-display text-4xl sm:text-5xl">Bonjour {{ $firstName }}</h1>
            <p class="mt-3 max-w-xl text-brand-surface/70">Bienvenue dans votre univers. Vos commandes, vos adresses et vos informations, réunis en un seul endroit.</p>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @include('account.partials.nav')

        {{-- Tuiles d'accès rapide. --}}
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($tiles as $tile)
                <a
                    href="{{ route($tile['route']) }}"
                    class="group relative flex items-start gap-4 rounded-2xl border border-brand-ink/10 bg-white p-6 transition duration-300 hover:-translate-y-1 hover:border-brand-signature/40 hover:shadow-xl"
                >
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-brand-accent/10 text-brand-signature transition duration-300 group-hover:scale-110 group-hover:bg-brand-accent group-hover:text-brand-surface">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            @foreach ($tile['icon'] as $path)
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}" />
                            @endforeach
                        </svg>
                    </div>

                    <div class="min-w-0 pr-6">
                        <h3 class="font-display text-lg text-brand-ink">{{ $tile['title'] }}</h3>
                        <p class="mt-1 text-sm text-brand-muted">{{ $tile['desc'] }}</p>
                    </div>

                    <svg class="absolute right-5 top-6 h-4 w-4 text-brand-ink/20 transition-all duration-300 group-hover:translate-x-1 group-hover:text-brand-signature" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            @endforeach
        </div>

        {{-- Carte profil résumé. --}}
        <div class="mt-8 flex flex-col gap-4 rounded-2xl border border-brand-ink/10 bg-brand-parchment p-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-brand-ink font-display text-xl text-brand-surface">
                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-display text-lg text-brand-ink">{{ $user->name }}</p>
                    <p class="text-sm text-brand-muted">{{ $user->email }}</p>
                    <p class="mt-1 text-xs text-brand-muted">Membre depuis {{ $user->created_at?->translatedFormat('F Y') }}</p>
                </div>
            </div>

            <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 self-start rounded-full border border-brand-ink/20 px-5 py-2 text-sm text-brand-ink transition hover:border-brand-signature hover:text-brand-signature sm:self-auto">
                Modifier mon profil
            </a>
        </div>
    </div>
</x-shop-layout>
