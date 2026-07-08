<x-shop-layout :title="$category->name.' — '.config('app.name')">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="font-display text-3xl mb-2">{{ $category->name }}</h1>

        @if ($category->description)
            <p class="text-amiras-taupe mb-6">{{ $category->description }}</p>
        @endif

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
