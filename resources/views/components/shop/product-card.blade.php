@props(['product'])

<a href="{{ route('shop.product', $product) }}" class="group block">
    <div class="aspect-square w-full overflow-hidden rounded-md bg-amiras-ivory border border-amiras-ink/10 group-hover:border-amiras-gold transition">
        @if ($image = $product->primaryImage())
            <img
                src="{{ Illuminate\Support\Facades\Storage::disk('public')->url($image->path) }}"
                alt="{{ $product->name }}"
                loading="lazy"
                class="h-full w-full object-cover object-center"
            >
        @else
            <div class="h-full w-full flex items-center justify-center text-amiras-taupe text-sm">
                Pas d'image
            </div>
        @endif
    </div>

    <div class="mt-3 flex flex-col gap-1">
        <h3 class="text-sm text-amiras-ink/90">{{ $product->name }}</h3>
        <p class="text-sm font-medium text-amiras-ink">{{ number_format($product->price, 0, ',', ' ') }} FCFA</p>

        @unless ($product->isInStock())
            <span class="text-xs text-amiras-bordeaux">Rupture de stock</span>
        @endunless
    </div>
</a>
