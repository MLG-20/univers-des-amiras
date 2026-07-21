@if ($products->isEmpty())
    <p class="text-amiras-taupe">Aucun produit ne correspond à ces critères.</p>
@else
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach ($products as $product)
            <x-shop.product-card :product="$product" />
        @endforeach
    </div>

    {{-- data-pagination : seuls ces liens sont interceptés en fetch par
         shop-filters.js. Les liens des cartes produit doivent, eux, naviguer
         normalement (sinon la fiche produit entière serait injectée dans la
         grille — header + footer en double). --}}
    <div class="mt-10" data-pagination>
        {{ $products->links() }}
    </div>
@endif
