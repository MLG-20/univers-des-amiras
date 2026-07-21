@props(['product'])

<a href="{{ route('shop.product', $product) }}" class="group block">
    <div class="aspect-square w-full overflow-hidden rounded-md bg-brand-parchment border border-brand-ink/10 group-hover:border-brand-signature transition">
        @if ($image = $product->primaryImage())
            <img
                src="{{ $image->sizedUrl('thumb') }}"
                srcset="{{ $image->sizedUrl('thumb') }} 480w, {{ $image->sizedUrl('medium') }} 800w"
                sizes="(min-width: 1024px) 25vw, (min-width: 640px) 33vw, 50vw"
                alt="{{ $product->name }}"
                loading="lazy"
                class="h-full w-full object-cover object-center"
            >
        @else
            <x-shop.image-placeholder :category="$product->category" />
        @endif
    </div>

    <div class="mt-3 flex flex-col gap-1">
        <span class="text-[0.65rem] uppercase tracking-[0.15em] text-brand-muted">{{ $product->category->name }}</span>
        <h3 class="font-display text-base text-brand-ink group-hover:text-brand-signature transition">{{ $product->name }}</h3>
        <p class="text-sm font-medium text-brand-ink">{{ number_format($product->price, 0, ',', ' ') }} FCFA</p>

        @unless ($product->isInStock())
            <span class="text-xs text-brand-signature">Rupture de stock</span>
        @endunless
    </div>
</a>
