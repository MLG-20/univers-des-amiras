<x-shop-layout :title="config('app.name')" :transparent-header="true">
    {{--
        Hero : carrousel géré depuis l'admin (Filament > Contenu du site >
        Hero). Chaque slide a son image, son titre, son sous-titre et son
        bouton propres. Si aucune slide n'est configurée, on retombe sur un
        contenu par défaut en traitement "bandeau signature" (ébène + or) —
        le site n'est jamais vide avant le premier réglage en admin.
    --}}
    @php
        $defaultSlide = [
            'image' => null,
            'title' => "L'élégance voilée, pensée pour vous",
            'subtitle' => 'Voiles, parfums et accessoires choisis avec soin, pour une élégance moderne et raffinée.',
            'cta_label' => 'Découvrir la collection',
            'cta_url' => route('shop.index'),
        ];
    @endphp

    <section
        class="relative w-full min-h-[560px] text-amiras-cream overflow-hidden"
        x-data="{ active: 0, count: {{ max($slides->count(), 1) }} }"
        x-init="count > 1 && setInterval(() => active = (active + 1) % count, 6000)"
    >
        @if ($slides->isEmpty())
            <div class="absolute inset-0 bg-amiras-ink">
                <div class="absolute inset-0 opacity-25 bg-[radial-gradient(circle_at_15%_15%,rgba(184,146,63,0.6),transparent_60%)]"></div>
                <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_85%_85%,rgba(184,146,63,0.5),transparent_55%)]"></div>

                <div class="relative h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col justify-center items-start gap-6">
                    <span class="text-xs uppercase tracking-[0.3em] text-amiras-gold">Depuis Dakar</span>
                    <h1 class="font-display text-4xl sm:text-6xl leading-[1.1] max-w-2xl">{{ $defaultSlide['title'] }}</h1>
                    <p class="text-amiras-cream/70 max-w-md text-base sm:text-lg">{{ $defaultSlide['subtitle'] }}</p>

                    <div class="flex flex-wrap gap-4 mt-2">
                        <a href="{{ $defaultSlide['cta_url'] }}" class="inline-block border border-amiras-gold px-8 py-3 text-sm uppercase tracking-wide hover:bg-amiras-gold hover:text-amiras-ink transition">
                            {{ $defaultSlide['cta_label'] }}
                        </a>

                        @if ($collections->isNotEmpty())
                            <a href="{{ route('shop.category', $collections->first()->slug) }}" class="inline-block border border-amiras-cream/30 px-8 py-3 text-sm uppercase tracking-wide hover:border-amiras-cream transition">
                                {{ $collections->first()->name }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @else
            @foreach ($slides as $index => $slide)
                <div
                    x-show="active === {{ $index }}"
                    x-cloak
                    x-transition.opacity.duration.500ms
                    class="absolute inset-0 bg-amiras-ink bg-cover"
                    @if ($slide->image_path) style="background-image: url('{{ $slide->sizedUrl('large') }}'); background-position: {{ $slide->cssBackgroundPosition() }};" @endif
                >
                    <div class="absolute inset-0 {{ $slide->image_path ? 'bg-amiras-ink/50' : '' }}"></div>
                    @unless ($slide->image_path)
                        <div class="absolute inset-0 opacity-25 bg-[radial-gradient(circle_at_15%_15%,rgba(184,146,63,0.6),transparent_60%)]"></div>
                        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_85%_85%,rgba(184,146,63,0.5),transparent_55%)]"></div>
                    @endunless

                    <div class="relative h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col justify-center items-start gap-6">
                        <span class="text-xs uppercase tracking-[0.3em] text-amiras-gold">Depuis Dakar</span>
                        <h1 class="font-display text-4xl sm:text-6xl leading-[1.1] max-w-2xl">{{ $slide->title }}</h1>

                        @if ($slide->subtitle)
                            <p class="text-amiras-cream/70 max-w-md text-base sm:text-lg">{{ $slide->subtitle }}</p>
                        @endif

                        @if ($slide->cta_label)
                            <a
                                href="{{ $slide->cta_url ?: route('shop.index') }}"
                                class="inline-block border border-amiras-gold px-8 py-3 text-sm uppercase tracking-wide hover:bg-amiras-gold hover:text-amiras-ink transition mt-2"
                            >
                                {{ $slide->cta_label }}
                            </a>
                        @endif
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
                            :class="active === {{ $index }} ? 'w-6 bg-amiras-gold' : 'w-1.5 bg-amiras-cream/50'"
                            aria-label="Aller à la diapositive {{ $index + 1 }}"
                        ></button>
                    @endforeach
                </div>
            @endif
        @endif
    </section>

    {{-- Collections — bannières pleine largeur, une par catégorie racine --}}
    @if ($collections->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <span class="text-xs uppercase tracking-[0.2em] text-amiras-gold">Nos univers</span>
            <h2 class="font-display text-3xl text-amiras-ink mt-1 mb-8">Collections</h2>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                @foreach ($collections as $collection)
                    <a href="{{ route('shop.category', $collection->slug) }}" class="group block">
                        <div class="aspect-[4/5] w-full overflow-hidden rounded-md border border-amiras-ink/10 group-hover:border-amiras-gold transition">
                            <x-shop.image-placeholder :category="$collection" :image="$collection->sizedUrl('medium')" />
                        </div>

                        <div class="mt-3 flex items-center justify-between gap-2">
                            <span class="font-display text-lg sm:text-xl text-amiras-ink group-hover:text-amiras-gold transition">
                                {{ $collection->name }}
                            </span>
                            <span class="text-xs uppercase tracking-wide text-amiras-ink/60 whitespace-nowrap">
                                Voir →
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Nouveautés — défilement horizontal --}}
    @if ($newProducts->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <span class="text-xs uppercase tracking-[0.2em] text-amiras-gold">À découvrir</span>
            <h2 class="font-display text-3xl text-amiras-ink mt-1 mb-8">Nouveautés</h2>

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
            class="relative border-y border-amiras-ink/10 bg-gradient-to-b from-amiras-ivory to-amiras-cream overflow-hidden"
            x-data="{ shown: false }"
            x-init="new IntersectionObserver((entries, obs) => entries.forEach(e => { if (e.isIntersecting) { shown = true; obs.disconnect(); } }), { threshold: 0.2 }).observe($el)"
        >
            {{-- Filet doré décoratif en haut de section. --}}
            <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-amiras-gold/60 to-transparent"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
                <div
                    class="text-center max-w-2xl mx-auto mb-14"
                    style="opacity:0; transform:translateY(1.5rem); transition: opacity .7s ease, transform .7s ease;"
                    :style="shown && 'opacity:1; transform:translateY(0)'"
                >
                    <span class="text-xs uppercase tracking-[0.3em] text-amiras-gold">L'expérience Amiras</span>
                    <h2 class="mt-4 font-display text-3xl sm:text-4xl text-amiras-ink leading-tight">Commandez l'esprit tranquille</h2>
                    <p class="mt-4 text-amiras-taupe">De la commande à votre porte, chaque détail est pensé pour vous simplifier la vie — et vous faire sentir choyée.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 sm:gap-8">
                    @foreach ($settings->trust_items as $index => $text)
                        <div
                            class="trust-card group relative flex flex-col items-center text-center gap-5 rounded-2xl border border-amiras-ink/10 bg-white/50 backdrop-blur-sm px-6 py-12 transition duration-500 hover:-translate-y-2 hover:shadow-2xl"
                            style="opacity:0; transform:translateY(2rem); transition: opacity .7s ease, transform .7s ease, box-shadow .5s ease, border-color .5s ease; transition-delay: {{ $index * 140 }}ms;"
                            :style="shown && 'opacity:1; transform:translateY(0)'"
                        >
                            <div class="flex items-center justify-center h-16 w-16 rounded-full border border-amiras-gold/40 bg-amiras-gold/5 text-amiras-gold transition duration-500 group-hover:bg-amiras-gold group-hover:text-amiras-cream group-hover:scale-110 group-hover:shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $trustIcons[$index % count($trustIcons)] }}" />
                                </svg>
                            </div>

                            <p class="font-display text-lg sm:text-xl text-amiras-ink leading-snug">{{ $text }}</p>

                            <span class="block h-px w-10 bg-amiras-gold/50 transition-all duration-500 group-hover:w-20"></span>
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
        class="relative overflow-hidden border-y border-amiras-ink/10 bg-gradient-to-b from-amiras-cream to-amiras-ivory"
        x-data="{ shown: false, rating: {{ (int) old('rating', 0) }}, hover: 0 }"
        x-init="new IntersectionObserver((entries, obs) => entries.forEach(e => { if (e.isIntersecting) { shown = true; obs.disconnect(); } }), { threshold: 0.15 }).observe($el)"
    >
        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-amiras-gold/60 to-transparent"></div>

        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
            <div
                class="text-center"
                style="transition: opacity .7s ease, transform .7s ease;"
                :style="shown ? 'opacity:1; transform:translateY(0)' : 'opacity:0; transform:translateY(1.5rem)'"
            >
                <span class="text-xs uppercase tracking-[0.3em] text-amiras-gold">Votre avis compte</span>
                <h2 class="mt-3 font-display text-3xl sm:text-4xl text-amiras-ink leading-tight">Partagez votre expérience</h2>
                <p class="mt-3 text-amiras-taupe">Quelques mots suffisent. Votre avis sera publié après vérification par notre équipe.</p>
            </div>

            @if (session('status') === 'review-submitted')
                <p class="mt-8 flex items-center gap-2 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Merci ! Votre avis a bien été envoyé. Il apparaîtra sur le site après vérification.
                </p>
            @endif

            <form
                method="post"
                action="{{ route('shop.reviews.store') }}"
                class="mt-10 space-y-6 rounded-3xl border border-amiras-ink/10 bg-white/70 backdrop-blur-sm p-6 sm:p-10 shadow-sm"
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
                                     :class="(hover || rating) >= star ? 'text-amiras-gold' : 'text-amiras-ink/15'"
                                     viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M11.48 3.5a.56.56 0 011.04 0l2.12 5.11a.56.56 0 00.48.35l5.52.44c.5.04.7.66.32.99l-4.2 3.6a.56.56 0 00-.19.56l1.29 5.38a.56.56 0 01-.84.61l-4.73-2.88a.56.56 0 00-.58 0l-4.73 2.88a.56.56 0 01-.84-.61l1.29-5.38a.56.56 0 00-.19-.56l-4.2-3.6a.56.56 0 01.32-.99l5.52-.44a.56.56 0 00.48-.35L11.48 3.5z" />
                                </svg>
                            </button>
                        </template>
                        <span class="ml-2 text-sm text-amiras-taupe" x-text="rating ? rating + '/5' : 'Optionnel'"></span>
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
                        class="mt-1 block w-full rounded-xl border-amiras-ink/20 text-sm transition focus:border-amiras-gold focus:ring-amiras-gold"
                    >{{ old('comment') }}</textarea>
                    <div class="mt-1 flex items-center justify-between">
                        <x-input-error :messages="$errors->get('comment')" />
                        <span class="ml-auto text-xs text-amiras-taupe"><span x-text="count">0</span>/600</span>
                    </div>
                </div>

                <button
                    type="submit"
                    class="group inline-flex items-center gap-2 rounded-full bg-amiras-ink px-8 py-3 text-sm font-medium uppercase tracking-wide text-amiras-cream transition-all duration-300 hover:bg-amiras-gold hover:text-amiras-ink hover:shadow-lg"
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
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <span class="text-xs uppercase tracking-[0.2em] text-amiras-gold">Avis clients</span>
            <h2 class="font-display text-3xl text-amiras-ink mt-1 mb-8">Elles nous font confiance</h2>

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
