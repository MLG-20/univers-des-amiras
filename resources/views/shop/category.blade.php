<x-shop-layout :title="$category->name.' — '.config('app.name')">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-semibold mb-2">{{ $category->name }}</h1>

        @if ($category->description)
            <p class="text-gray-500 mb-6">{{ $category->description }}</p>
        @endif

        @if ($category->children->isNotEmpty())
            <div class="flex flex-wrap gap-2 mb-8">
                @foreach ($category->children as $child)
                    <a
                        href="{{ route('shop.category', $child) }}"
                        class="text-sm px-3 py-1.5 rounded-full border border-gray-300 hover:border-gray-500"
                    >
                        {{ $child->name }}
                    </a>
                @endforeach
            </div>
        @endif

        @if ($products->isEmpty())
            <p class="text-gray-500">Aucun produit dans cette catégorie pour le moment.</p>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($products as $product)
                    <x-shop.product-card :product="$product" />
                @endforeach
            </div>

            <div class="mt-10">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</x-shop-layout>
