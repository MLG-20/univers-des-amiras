<x-shop-layout :title="$category->name.' — '.config('app.name')">
    {{-- Bandeau de catégorie plein largeur, même traitement visuel que les
    bandes collection de l'accueil (resources/views/shop/home.blade.php) —
    donne du poids à la page même sans photo de catégorie. --}}
    <section class="w-full bg-gradient-to-br from-amiras-ivory via-amiras-cream to-amiras-taupe/20 border-b border-amiras-ink/10 py-14 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-display text-4xl sm:text-5xl text-amiras-ink">{{ $category->name }}</h1>

            @if ($category->description)
                <p class="mt-3 text-amiras-taupe max-w-xl">{{ $category->description }}</p>
            @endif
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if ($category->children->isNotEmpty())
            <div class="flex flex-wrap gap-2 mb-8">
                @foreach ($category->children as $child)
                    <a
                        href="{{ route('shop.category', $child) }}"
                        class="text-sm px-3 py-1.5 rounded-full border border-amiras-ink/20 hover:border-amiras-gold"
                    >
                        {{ $child->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <x-shop.filter-bar :action="route('shop.category', $category)">
            @include('shop.partials.product-grid', ['products' => $products])
        </x-shop.filter-bar>
    </div>
</x-shop-layout>
