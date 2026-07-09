<x-shop-layout :title="config('app.name')">
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
                            <x-shop.image-placeholder :category="$collection" />
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
        <section class="border-y border-amiras-ink/10 bg-amiras-ivory">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid grid-cols-1 sm:grid-cols-3 gap-8 text-center">
                @foreach ($settings->trust_items as $index => $text)
                    <div class="flex flex-col items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-amiras-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $trustIcons[$index % count($trustIcons)] }}" />
                        </svg>
                        <p class="text-sm text-amiras-ink">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</x-shop-layout>
