<x-shop-layout :title="config('app.name')" :transparent-header="true">
    {{--
        Hero : carrousel géré depuis l'admin (Filament > Contenu du site >
        Hero). Chaque slide a son image, son titre, son sous-titre et son
        bouton propres. Si aucune slide n'est configurée, on retombe sur la
        promesse de marque du rapport d'identité — le site n'est jamais vide
        avant le premier réglage en admin.
    --}}
    @php
        $defaultSlide = [
            'image' => null,
            // Promesse officielle de la marque (rapport d'identité p.2).
            'title' => "L'élégance dans la pudeur",
            'subtitle' => 'Une sélection de hijabs, foulards, accessoires et parfums pensée pour accompagner chaque silhouette avec distinction.',
            'cta_label' => 'Découvrir la collection',
            'cta_url' => route('shop.index'),
        ];
    @endphp

    <section
        class="relative w-full min-h-[560px] text-brand-surface overflow-hidden"
        x-data="{ active: 0, count: {{ max($slides->count(), 1) }} }"
        x-init="count > 1 && setInterval(() => active = (active + 1) % count, 6000)"
    >
        @if ($slides->isEmpty())
            <div class="absolute inset-0 bg-brand-ink">
                <x-shop.fold-backdrop />

                <div class="relative h-full max-w-shell mx-auto px-4 sm:px-6 lg:px-10 xl:px-12 flex flex-col justify-center items-start gap-6 py-24">
                    <span class="text-[0.65rem] uppercase tracking-[0.4em] text-brand-surface/60">Aissatou Store</span>
                    <h1 class="font-display text-4xl sm:text-6xl lg:text-7xl leading-[1.05] max-w-3xl">{{ $defaultSlide['title'] }}</h1>
                    <p class="text-brand-surface/70 max-w-lg text-base sm:text-lg">{{ $defaultSlide['subtitle'] }}</p>

                    {{-- Un seul CTA Garance (règle du rapport p.13) ; le second reste
                         en contour. --}}
                    <div class="flex flex-wrap gap-3 mt-2">
                        <x-shop.cta :href="$defaultSlide['cta_url']" variant="primary">
                            {{ $defaultSlide['cta_label'] }}
                        </x-shop.cta>

                        <x-shop.cta :href="route('shop.index', ['nouveautes' => 1])" on-dark>
                            Explorer les nouveautés
                        </x-shop.cta>
                    </div>
                </div>
            </div>
        @else
            @foreach ($slides as $index => $slide)
                <div
                    x-show="active === {{ $index }}"
                    x-cloak
                    x-transition.opacity.duration.500ms
                    class="absolute inset-0 bg-brand-ink bg-cover"
                    @if ($slide->image_path) style="background-image: url('{{ $slide->sizedUrl('large') }}'); background-position: {{ $slide->cssBackgroundPosition() }};" @endif
                >
                    <div class="absolute inset-0 {{ $slide->image_path ? 'bg-brand-ink/50' : '' }}"></div>
                    @unless ($slide->image_path)
                        <x-shop.fold-backdrop />
                    @endunless

                    <div class="relative h-full max-w-shell mx-auto px-4 sm:px-6 lg:px-10 xl:px-12 flex flex-col justify-center items-start gap-6 py-24">
                        <span class="text-[0.65rem] uppercase tracking-[0.4em] text-brand-surface/60">Aissatou Store</span>
                        <h1 class="font-display text-4xl sm:text-6xl lg:text-7xl leading-[1.05] max-w-3xl">{{ $slide->title }}</h1>

                        @if ($slide->subtitle)
                            <p class="text-brand-surface/70 max-w-lg text-base sm:text-lg">{{ $slide->subtitle }}</p>
                        @endif

                        <div class="flex flex-wrap gap-3 mt-2">
                            @if ($slide->cta_label)
                                <x-shop.cta :href="$slide->cta_url ?: route('shop.index')" variant="primary">
                                    {{ $slide->cta_label }}
                                </x-shop.cta>
                            @endif

                            <x-shop.cta :href="route('shop.index', ['nouveautes' => 1])" on-dark>
                                Explorer les nouveautés
                            </x-shop.cta>
                        </div>
                    </div>
                </div>
            @endforeach

            @if ($slides->count() > 1)
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2 z-10">
                    @foreach ($slides as $index => $slide)
                        <button
                            type="button"
                            @click="active = {{ $index }}"
                            class="h-1.5 rounded-full transition-all"
                            :class="active === {{ $index }} ? 'w-6 bg-brand-accent' : 'w-1.5 bg-brand-surface/50'"
                            aria-label="Aller à la diapositive {{ $index + 1 }}"
                        ></button>
                    @endforeach
                </div>
            @endif
        @endif
    </section>

    {{-- ARCHITECTURE DE L'OFFRE (rapport d'identité p.3) — une tuile par
         catégorie racine.

         Cette section s'intitulait « Collections », ce qui entrait en collision
         frontale avec l'onglet « Collections » de la navigation : dans le
         rapport, Collections est un univers À PART (« le récit éditorial »,
         p.3), pas le nom du sommaire des catégories. Deux entrées portant le
         même mot vers deux destinations différentes, c'est l'inverse du
         « contenu choisi plutôt qu'accumulé » exigé p.16.

         La p.3 fait aussi suivre chaque univers d'un descripteur court — « Le
         geste quotidien », « La matière et le mouvement »… — repris ici depuis
         la description de la catégorie, qui est éditable en admin. --}}
    @if ($collections->isNotEmpty())
        <section class="max-w-shell mx-auto px-4 sm:px-6 lg:px-10 xl:px-12 py-16">
            <span class="text-xs uppercase tracking-[0.2em] text-brand-signature">Architecture de l'offre</span>
            <h2 class="font-display text-3xl text-brand-ink mt-1 mb-8">Nos univers</h2>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                @foreach ($collections as $collection)
                    @php
                        // On ne garde que la PREMIÈRE PROPOSITION de la description —
                        // « Le geste quotidien », « La matière et le mouvement » —
                        // c'est exactement la forme des descripteurs de la p.3. La
                        // coupe se fait au premier deux-points ou à la première
                        // virgule, les deux tournures présentes en base ; une
                        // description libre saisie en admin est simplement tronquée.
                        $descriptor = $collection->description
                            ? Str::limit(rtrim(trim(Str::before(Str::before($collection->description, ' :'), ',')), '.'), 60)
                            : null;
                    @endphp

                    <a href="{{ route('shop.category', $collection->slug) }}" class="group block">
                        <div class="aspect-[4/5] w-full overflow-hidden rounded-sm border border-brand-ink/10 group-hover:border-brand-signature transition">
                            <x-shop.image-placeholder :category="$collection" :image="$collection->sizedUrl('medium')" />
                        </div>

                        <div class="mt-3 flex items-baseline justify-between gap-2">
                            <span class="font-display text-lg sm:text-xl text-brand-ink group-hover:text-brand-signature transition">
                                {{ $collection->name }}
                            </span>
                            <span class="text-xs uppercase tracking-wide text-brand-ink/60 whitespace-nowrap">
                                Voir →
                            </span>
                        </div>

                        @if ($descriptor)
                            <p class="mt-1 text-sm text-brand-muted">{{ $descriptor }}</p>
                        @endif
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- NOUVEAUTÉS — les produits marqués « Nouveauté » en admin.

         La section disparaît si rien n'est marqué : mieux vaut ne rien annoncer
         qu'annoncer de fausses nouveautés, ce que faisait la version précédente
         en affichant les dernières lignes créées. --}}
    @if ($newProducts->isNotEmpty())
        <section class="max-w-shell mx-auto px-4 sm:px-6 lg:px-10 xl:px-12 py-16">
            <div class="mb-8 flex items-baseline justify-between gap-4">
                <div>
                    <span class="text-xs uppercase tracking-[0.2em] text-brand-signature">À découvrir</span>
                    <h2 class="font-display text-3xl text-brand-ink mt-1">Nouveautés</h2>
                </div>

                <a href="{{ route('shop.index', ['nouveautes' => 1]) }}" class="whitespace-nowrap text-xs uppercase tracking-[0.15em] text-brand-ink/60 transition hover:text-brand-signature">
                    Tout voir →
                </a>
            </div>

            {{-- Le défilement continu suppose assez de cartes pour que la boucle
                 ne se voie pas : en dessous, la piste dupliquée laisserait des
                 trous et répéterait visiblement les mêmes produits. On retombe
                 alors sur une grille fixe, qui reste juste avec une seule pièce. --}}
            @if ($newProducts->count() >= 5)
                <div class="overflow-hidden">
                    <div class="flex gap-6 w-max marquee-track">
                        @foreach ([1, 2] as $repeat)
                            @foreach ($newProducts as $product)
                                <div class="w-40 sm:w-56 flex-shrink-0">
                                    <x-shop.product-card :product="$product" />
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            @else
                <div class="grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($newProducts as $product)
                        <x-shop.product-card :product="$product" />
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    {{-- Indicateurs de confiance — texte éditable depuis l'admin (Filament >
    Contenu du site > Réglages du site), icônes fixes assignées par ordre. --}}
    @php
        $trustIcons = [
            'M2.25 8.25h19.5M2.25 8.25v10.5a1.5 1.5 0 001.5 1.5h16.5a1.5 1.5 0 001.5-1.5V8.25M2.25 8.25l1.72-3.44A1.5 1.5 0 015.31 4h13.38a1.5 1.5 0 011.34.81l1.72 3.44M6 15.75h3',
            'M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v11.177m0-11.177L12 4.5m2.25 3.073L18 4.5m-3.75 3.073v11.177',
            'M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z',
        ];
    @endphp

    @if (! empty($settings->trust_items))
        {{-- Révélation au scroll (IntersectionObserver via Alpine, sans plugin) :
             `shown` bascule à true quand la section entre dans le viewport, ce
             qui déclenche l'apparition en cascade des cartes. --}}
        <section
            class="relative border-y border-brand-ink/10 bg-gradient-to-b from-brand-parchment to-brand-surface overflow-hidden"
            x-data="{ shown: false }"
            x-init="new IntersectionObserver((entries, obs) => entries.forEach(e => { if (e.isIntersecting) { shown = true; obs.disconnect(); } }), { threshold: 0.2 }).observe($el)"
        >
            {{-- Filet doré décoratif en haut de section. --}}
            <div class="absolute inset-x-0 top-0 h-px bg-brand-signature/15"></div>

            <div class="max-w-shell mx-auto px-4 sm:px-6 lg:px-10 xl:px-12 py-16 sm:py-24">
                <div
                    class="text-center max-w-2xl mx-auto mb-14"
                    style="opacity:0; transform:translateY(1.5rem); transition: opacity .7s ease, transform .7s ease;"
                    :style="shown && 'opacity:1; transform:translateY(0)'"
                >
                    <span class="text-xs uppercase tracking-[0.3em] text-brand-signature">L'expérience Aissatou</span>
                    <h2 class="mt-4 font-display text-3xl sm:text-4xl text-brand-ink leading-tight">Commandez l'esprit tranquille</h2>
                    <p class="mt-4 text-brand-muted">De la commande à votre porte, chaque détail est pensé pour vous simplifier la vie — et vous faire sentir choyée.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 sm:gap-8">
                    @foreach ($settings->trust_items as $index => $text)
                        <div
                            class="trust-card group relative flex flex-col items-center text-center gap-5 rounded-sm border border-brand-ink/10 bg-white/50 backdrop-blur-sm px-6 py-12 transition duration-500 hover:-translate-y-2 hover:shadow-2xl"
                            style="opacity:0; transform:translateY(2rem); transition: opacity .7s ease, transform .7s ease, box-shadow .5s ease, border-color .5s ease; transition-delay: {{ $index * 140 }}ms;"
                            :style="shown && 'opacity:1; transform:translateY(0)'"
                        >
                            <div class="flex items-center justify-center h-16 w-16 rounded-full border border-brand-signature/40 bg-brand-accent/5 text-brand-signature transition duration-500 group-hover:bg-brand-accent group-hover:text-brand-surface group-hover:scale-110 group-hover:shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $trustIcons[$index % count($trustIcons)] }}" />
                                </svg>
                            </div>

                            <p class="font-display text-lg sm:text-xl text-brand-ink leading-snug">{{ $text }}</p>

                            <span class="block h-px w-10 bg-brand-accent/50 transition-all duration-500 group-hover:w-20"></span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- FORMULAIRE d'avis — placé AVANT le carrousel. Avis créé NON publié, puis
    modéré en admin. Révélation au scroll + notation par étoiles interactive
    (Alpine) + compteur de caractères + bouton animé. L'état hidden n'est appliqué
    QUE par Alpine (:style), donc le formulaire reste utilisable sans JS. --}}
    <section
        id="avis"
        class="relative overflow-hidden border-y border-brand-ink/10 bg-gradient-to-b from-brand-surface to-brand-parchment"
        x-data="{ shown: false, rating: {{ (int) old('rating', 0) }}, hover: 0 }"
        x-init="new IntersectionObserver((entries, obs) => entries.forEach(e => { if (e.isIntersecting) { shown = true; obs.disconnect(); } }), { threshold: 0.15 }).observe($el)"
    >
        <div class="absolute inset-x-0 top-0 h-px bg-brand-signature/15"></div>

        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-10 xl:px-12 py-16 sm:py-20">
            <div
                class="text-center"
                style="transition: opacity .7s ease, transform .7s ease;"
                :style="shown ? 'opacity:1; transform:translateY(0)' : 'opacity:0; transform:translateY(1.5rem)'"
            >
                <span class="text-xs uppercase tracking-[0.3em] text-brand-signature">Votre avis compte</span>
                <h2 class="mt-3 font-display text-3xl sm:text-4xl text-brand-ink leading-tight">Partagez votre expérience</h2>
                <p class="mt-3 text-brand-muted">Quelques mots suffisent. Votre avis sera publié après vérification par notre équipe.</p>
            </div>

            @if (session('status') === 'review-submitted')
                <p class="mt-8 flex items-center gap-2 rounded-sm border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Merci ! Votre avis a bien été envoyé. Il apparaîtra sur le site après vérification.
                </p>
            @endif

            <form
                method="post"
                action="{{ route('shop.reviews.store') }}"
                class="mt-10 space-y-6 rounded-sm border border-brand-ink/10 bg-white/70 backdrop-blur-sm p-6 sm:p-10 shadow-sm"
                style="transition: opacity .8s ease .15s, transform .8s ease .15s;"
                :style="shown ? 'opacity:1; transform:translateY(0)' : 'opacity:0; transform:translateY(2rem)'"
            >
                @csrf

                {{-- Notation par étoiles interactive (survol + clic). --}}
                <div>
                    <x-input-label value="Votre note" />
                    <input type="hidden" name="rating" :value="rating || ''">
                    <div class="mt-2 flex items-center gap-1.5" @mouseleave="hover = 0">
                        <template x-for="star in 5" :key="star">
                            <button
                                type="button"
                                @click="rating = (rating === star ? 0 : star)"
                                @mouseenter="hover = star"
                                class="transition-transform duration-150 hover:scale-125 focus:outline-none"
                            >
                                <svg class="h-9 w-9 transition-colors duration-200"
                                     :class="(hover || rating) >= star ? 'text-brand-signature' : 'text-brand-ink/15'"
                                     viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M11.48 3.5a.56.56 0 011.04 0l2.12 5.11a.56.56 0 00.48.35l5.52.44c.5.04.7.66.32.99l-4.2 3.6a.56.56 0 00-.19.56l1.29 5.38a.56.56 0 01-.84.61l-4.73-2.88a.56.56 0 00-.58 0l-4.73 2.88a.56.56 0 01-.84-.61l1.29-5.38a.56.56 0 00-.19-.56l-4.2-3.6a.56.56 0 01.32-.99l5.52-.44a.56.56 0 00.48-.35L11.48 3.5z" />
                                </svg>
                            </button>
                        </template>
                        <span class="ml-2 text-sm text-brand-muted" x-text="rating ? rating + '/5' : 'Optionnel'"></span>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('rating')" />
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <x-input-label for="author_name" value="Votre nom" />
                        <x-text-input id="author_name" name="author_name" type="text" class="mt-1 block w-full" :value="old('author_name')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('author_name')" />
                    </div>

                    <div>
                        <x-input-label for="location" value="Ville (optionnel)" />
                        <x-text-input id="location" name="location" type="text" class="mt-1 block w-full" :value="old('location')" />
                        <x-input-error class="mt-2" :messages="$errors->get('location')" />
                    </div>
                </div>

                <div x-data="{ count: {{ mb_strlen(old('comment', '')) }} }">
                    <x-input-label for="comment" value="Votre avis" />
                    <textarea
                        id="comment" name="comment" rows="4" required maxlength="600"
                        @input="count = $event.target.value.length"
                        class="mt-1 block w-full rounded-sm border-brand-ink/20 text-sm transition focus:border-brand-signature focus:ring-brand-accent"
                    >{{ old('comment') }}</textarea>
                    <div class="mt-1 flex items-center justify-between">
                        <x-input-error :messages="$errors->get('comment')" />
                        <span class="ml-auto text-xs text-brand-muted"><span x-text="count">0</span>/600</span>
                    </div>
                </div>

                <button
                    type="submit"
                    class="group inline-flex items-center gap-2 rounded-sm bg-brand-ink px-8 py-3 text-sm font-medium uppercase tracking-wide text-brand-surface transition-all duration-300 hover:bg-brand-accent hover:text-brand-surface hover:shadow-lg"
                >
                    <span>Envoyer mon avis</span>
                    <svg class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </form>
        </div>
    </section>

    {{-- Carrousel des avis PUBLIÉS (marquee, même gabarit que « À découvrir »),
    placé APRÈS le formulaire. --}}
    @if ($reviews->isNotEmpty())
        <section class="max-w-shell mx-auto px-4 sm:px-6 lg:px-10 xl:px-12 py-16">
            <span class="text-xs uppercase tracking-[0.2em] text-brand-signature">Avis clients</span>
            <h2 class="font-display text-3xl text-brand-ink mt-1 mb-8">Elles nous font confiance</h2>

            <div class="overflow-hidden">
                <div class="flex gap-6 w-max marquee-track">
                    @foreach ([1, 2] as $repeat)
                        @foreach ($reviews as $review)
                            <div class="w-72 sm:w-80 flex-shrink-0">
                                <x-shop.review-card :review="$review" />
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-shop-layout>
