@props(['product'])

<a href="{{ route('shop.product', $product) }}" class="group block">
    <div class="aspect-square w-full overflow-hidden rounded-lg bg-gray-100">
        @if ($image = $product->primaryImage())
            <img
                src="{{ Illuminate\Support\Facades\Storage::disk('public')->url($image->path) }}"
                alt="{{ $product->name }}"
                loading="lazy"
                class="h-full w-full object-cover object-center group-hover:opacity-90 transition"
            >
        @else
            <div class="h-full w-full flex items-center justify-center text-gray-400 text-sm">
                Pas d'image
            </div>
        @endif
    </div>

    <div class="mt-3 flex flex-col gap-1">
        <h3 class="text-sm text-gray-800">{{ $product->name }}</h3>
        <p class="text-sm font-medium text-gray-950">{{ number_format($product->price, 0, ',', ' ') }} FCFA</p>

        @unless ($product->isInStock())
            <span class="text-xs text-red-600">Rupture de stock</span>
        @endunless
    </div>
</a>
