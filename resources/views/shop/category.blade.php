@php $categoryImage = $category->sizedUrl('large'); @endphp

{{-- Header transparent sur le hero de catégorie (puis opaque au scroll), comme
     l'accueil — mais UNIQUEMENT quand la catégorie a une image (hero sombre,
     texte crème lisible). Sans image, le bandeau est clair : on garde le header
     opaque classique. --}}
<x-shop-layout :title="$category->name.' — '.config('app.name')" :transparent-header="(bool) $categoryImage">
    {{-- Bandeau (hero) de catégorie plein largeur. S'il y a une image de
    catégorie (celle réglée en admin et affichée dans « Nos Univers » sur
    l'accueil), on l'utilise en fond avec voile sombre + texte crème, comme le
    hero. Sinon, repli sur le dégradé de la charte + texte encre. --}}
    <section
        class="relative w-full border-b border-amiras-ink/10 overflow-hidden bg-cover bg-center flex items-center {{ $categoryImage ? 'bg-amiras-ink min-h-[300px] sm:min-h-[380px]' : 'bg-gradient-to-br from-amiras-ivory via-amiras-cream to-amiras-taupe/20 py-14 sm:py-20' }}"
        @if ($categoryImage) style="background-image: url('{{ $categoryImage }}');" @endif
    >
        @if ($categoryImage)
            <div class="absolute inset-0 bg-amiras-ink/55"></div>
        @endif

        <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-display text-4xl sm:text-5xl {{ $categoryImage ? 'text-amiras-cream' : 'text-amiras-ink' }}">{{ $category->name }}</h1>

            @if ($category->description)
                <p class="mt-3 max-w-xl {{ $categoryImage ? 'text-amiras-cream/85' : 'text-amiras-taupe' }}">{{ $category->description }}</p>
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
