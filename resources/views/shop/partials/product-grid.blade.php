@if ($products->isEmpty())
    <p class="text-amiras-taupe">Aucun produit ne correspond à ces critères.</p>
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
