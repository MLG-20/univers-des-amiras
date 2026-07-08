<x-shop-layout :title="config('app.name')">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="font-display text-3xl mb-6">Tout le catalogue</h1>

        <x-shop.filter-bar :action="route('shop.index')" :categories="$categories">
            @include('shop.partials.product-grid', ['products' => $products])
        </x-shop.filter-bar>
    </div>
</x-shop-layout>
