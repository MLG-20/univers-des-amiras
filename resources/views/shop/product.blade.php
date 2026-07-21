@php
    $images = $product->images;
    $variants = $product->variants;
@endphp

<x-shop-layout :title="$product->name.' — '.config('app.name')">
    <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
        x-data="{
            activeImage: 0,
            touchStartX: 0,
            onSwipe(event, count) {
                const deltaX = event.changedTouches[0].screenX - this.touchStartX;
                if (Math.abs(deltaX) < 40 || count <= 1) return;
                this.activeImage = deltaX < 0
                    ? (this.activeImage + 1) % count
                    : (this.activeImage - 1 + count) % count;
            },
        }"
    >
        <nav class="text-sm text-brand-muted mb-6">
            <a href="{{ route('shop.category', $product->category) }}" class="hover:text-brand-ink">
                {{ $product->category->name }}
            </a>
            <span class="mx-1">/</span>
            <span class="text-brand-ink">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div>
                <div
                    class="relative aspect-square w-full overflow-hidden rounded-md bg-brand-parchment touch-pan-y"
                    x-on:touchstart="touchStartX = $event.changedTouches[0].screenX"
                    x-on:touchend="onSwipe($event, {{ $images->count() }})"
                >
                    @forelse ($images as $index => $image)
                        <img
                            x-show="activeImage === {{ $index }}"
                            x-cloak
                            x-transition.opacity.duration.300ms
                            src="{{ $image->sizedUrl('large') }}"
                            srcset="{{ $image->sizedUrl('medium') }} 800w, {{ $image->sizedUrl('large') }} 1400w"
                            sizes="(min-width: 1024px) 50vw, 100vw"
                            alt="{{ $product->name }}"
                            loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                            class="h-full w-full object-cover object-center select-none"
                            draggable="false"
                        >
                    @empty
                        <x-shop.image-placeholder :category="$product->category" />
                    @endforelse
                </div>

                @if ($images->count() > 1)
                    <div class="mt-4 flex gap-3 overflow-x-auto">
                        @foreach ($images as $index => $image)
                            <button
                                type="button"
                                @click="activeImage = {{ $index }}"
                                class="w-16 h-16 flex-shrink-0 rounded-md overflow-hidden border"
                                :class="activeImage === {{ $index }} ? 'border-brand-signature' : 'border-brand-ink/10'"
                            >
                                <img
                                    src="{{ $image->sizedUrl('thumb') }}"
                                    alt="{{ $product->name }}"
                                    loading="lazy"
                                    class="h-full w-full object-cover object-center"
                                >
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <h1 class="font-display text-3xl">{{ $product->name }}</h1>
                <p class="mt-2 text-xl text-brand-ink">
                    {{ number_format($product->price, 0, ',', ' ') }} FCFA
                </p>

                @if ($product->isInStock())
                    <p class="mt-1 text-sm text-brand-muted">En stock</p>
                @else
                    <p class="mt-1 text-sm text-brand-signature">Rupture de stock</p>
                @endif

                @if ($product->description)
                    <p class="mt-6 text-brand-ink/80 whitespace-pre-line">{{ $product->description }}</p>
                @endif

                @if ($errors->has('variant_id') || $errors->has('quantity'))
                    <p class="mt-4 text-sm text-brand-signature">
                        {{ $errors->first('variant_id') ?: $errors->first('quantity') }}
                    </p>
                @endif

                <form method="POST" action="{{ route('shop.cart.store') }}" class="mt-8">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    @if ($variants->isNotEmpty())
                        <div>
                            <label for="variant" class="block text-xs uppercase tracking-wide text-brand-muted mb-2">
                                Choisir une variante
                            </label>
                            <select
                                id="variant"
                                name="variant_id"
                                required
                                class="w-full rounded-md border border-brand-ink/20 focus:border-brand-signature focus:ring-brand-accent text-sm"
                            >
                                <option value="">— Sélectionner —</option>
                                @foreach ($variants as $variant)
                                    @php
                                        $label = collect($variant->attributes ?? [])
                                            ->map(fn ($value, $key) => ucfirst($key).': '.$value)
                                            ->implode(', ');
                                        $price = $variant->price_override ?? $product->price;
                                    @endphp
                                    <option value="{{ $variant->id }}" @disabled($variant->stock <= 0)>
                                        {{ $label ?: $variant->sku }} — {{ number_format($price, 0, ',', ' ') }} FCFA
                                        @if ($variant->stock <= 0) (rupture) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="mt-4 flex items-center gap-4">
                        <label for="quantity" class="text-xs uppercase tracking-wide text-brand-muted">Quantité</label>
                        <input
                            type="number"
                            id="quantity"
                            name="quantity"
                            value="1"
                            min="1"
                            max="50"
                            class="w-20 rounded-md border border-brand-ink/20 focus:border-brand-signature focus:ring-brand-accent text-sm"
                        >
                    </div>

                    <button
                        type="submit"
                        @disabled(! $product->isInStock())
                        class="mt-6 w-full sm:w-auto px-8 py-3 border border-brand-signature text-brand-ink text-sm uppercase tracking-wide hover:bg-brand-accent hover:text-white transition disabled:opacity-40 disabled:pointer-events-none"
                    >
                        Ajouter au panier
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-shop-layout>
