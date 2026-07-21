<x-shop-layout :title="'Catalogue — '.config('app.name')">
    <section class="w-full bg-gradient-to-br from-brand-parchment via-brand-surface to-brand-muted/20 border-b border-brand-ink/10 py-14 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <span class="text-xs uppercase tracking-[0.2em] text-brand-signature">Boutique</span>
            <h1 class="font-display text-4xl sm:text-5xl text-brand-ink mt-1">Tout le catalogue</h1>
            <p class="mt-3 text-brand-muted max-w-xl">Voiles, parfums, sacs et accessoires — une sélection choisie avec soin.</p>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <x-shop.filter-bar :action="route('shop.index')" :categories="$categories">
            @include('shop.partials.product-grid', ['products' => $products])
        </x-shop.filter-bar>
    </div>
</x-shop-layout>
