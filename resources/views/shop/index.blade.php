<x-shop-layout :title="'Catalogue — '.config('app.name')">
    {{-- Bandeau de tête en Parchemin plein, sans dégradé : le rapport réserve la
         respiration au fond et proscrit les ornements décoratifs (p.9). --}}
    <section class="w-full bg-brand-parchment border-b border-brand-ink/10 py-14 sm:py-20">
        <div class="max-w-shell mx-auto px-4 sm:px-6 lg:px-10 xl:px-12">
            <span class="text-[0.65rem] uppercase tracking-[0.25em] text-brand-signature">La sélection</span>
            <h1 class="font-display text-4xl sm:text-5xl text-brand-ink mt-2">Tout le catalogue</h1>
            <p class="mt-3 text-brand-muted max-w-xl">Hijabs, foulards, cols, parfums et objets à offrir — moins d'objets, mieux choisis.</p>
        </div>
    </section>

    <div class="max-w-shell mx-auto px-4 sm:px-6 lg:px-10 xl:px-12 py-8">
        <x-shop.filter-bar :action="route('shop.index')" :categories="$categories">
            @include('shop.partials.product-grid', ['products' => $products])
        </x-shop.filter-bar>
    </div>
</x-shop-layout>
