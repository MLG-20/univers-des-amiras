@php
    $images = $product->images;
    $variants = $product->variants;
@endphp

<x-shop-layout :title="$product->name.' — '.config('app.name')">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ activeImage: 0 }">
        <nav class="text-sm text-gray-500 mb-6">
            <a href="{{ route('shop.category', $product->category) }}" class="hover:text-gray-800">
                {{ $product->category->name }}
            </a>
            <span class="mx-1">/</span>
            <span class="text-gray-800">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div>
                <div class="aspect-square w-full overflow-hidden rounded-lg bg-gray-100">
                    @forelse ($images as $index => $image)
                        <img
                            x-show="activeImage === {{ $index }}"
                            x-cloak
                            src="{{ Illuminate\Support\Facades\Storage::disk('public')->url($image->path) }}"
                            alt="{{ $product->name }}"
                            class="h-full w-full object-cover object-center"
                        >
                    @empty
                        <div class="h-full w-full flex items-center justify-center text-gray-400 text-sm">
                            Pas d'image
                        </div>
                    @endforelse
                </div>

                @if ($images->count() > 1)
                    <div class="mt-4 flex gap-3 overflow-x-auto">
                        @foreach ($images as $index => $image)
                            <button
                                type="button"
                                @click="activeImage = {{ $index }}"
                                class="w-16 h-16 flex-shrink-0 rounded-md overflow-hidden border-2"
                                :class="activeImage === {{ $index }} ? 'border-gray-900' : 'border-transparent'"
                            >
                                <img
                                    src="{{ Illuminate\Support\Facades\Storage::disk('public')->url($image->path) }}"
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
                <h1 class="text-2xl font-semibold">{{ $product->name }}</h1>
                <p class="mt-2 text-xl font-medium text-gray-950">
                    {{ number_format($product->price, 0, ',', ' ') }} FCFA
                </p>

                @if ($product->isInStock())
                    <p class="mt-1 text-sm text-green-700">En stock</p>
                @else
                    <p class="mt-1 text-sm text-red-600">Rupture de stock</p>
                @endif

                @if ($product->description)
                    <p class="mt-6 text-gray-700 whitespace-pre-line">{{ $product->description }}</p>
                @endif

                @if ($variants->isNotEmpty())
                    <div class="mt-8">
                        <label for="variant" class="block text-sm font-medium text-gray-800 mb-2">
                            Choisir une variante
                        </label>
                        <select id="variant" name="variant_id" required class="w-full rounded-md border-gray-300 text-sm">
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
            </div>
        </div>
    </div>
</x-shop-layout>
