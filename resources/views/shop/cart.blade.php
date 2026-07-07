<x-shop-layout :title="'Panier — '.config('app.name')">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="font-display text-3xl mb-6">Votre panier</h1>

        @if (session('status'))
            <p class="mb-6 text-sm text-amiras-ink bg-amiras-ivory border border-amiras-gold/40 px-4 py-3 rounded-md">
                {{ session('status') }}
            </p>
        @endif

        @if ($errors->any())
            <p class="mb-6 text-sm text-amiras-bordeaux">{{ $errors->first() }}</p>
        @endif

        @if ($cart->items->isEmpty())
            <p class="text-amiras-taupe">Votre panier est vide.</p>
            <a href="{{ route('shop.index') }}" class="inline-block mt-4 text-sm underline hover:text-amiras-gold">
                Continuer mes achats
            </a>
        @else
            <div class="divide-y divide-amiras-ink/10 border-y border-amiras-ink/10">
                @foreach ($cart->items as $item)
                    @php
                        $variantLabel = collect($item->variant?->attributes ?? [])
                            ->map(fn ($value, $key) => ucfirst($key).': '.$value)
                            ->implode(', ');
                    @endphp
                    <div class="py-5 flex items-center gap-4">
                        <div class="w-20 h-20 flex-shrink-0 rounded-md overflow-hidden bg-amiras-ivory">
                            @if ($image = $item->product->primaryImage())
                                <img
                                    src="{{ Illuminate\Support\Facades\Storage::disk('public')->url($image->path) }}"
                                    alt="{{ $item->product->name }}"
                                    class="h-full w-full object-cover object-center"
                                >
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <a href="{{ route('shop.product', $item->product) }}" class="text-sm text-amiras-ink hover:text-amiras-gold">
                                {{ $item->product->name }}
                            </a>
                            @if ($variantLabel)
                                <p class="text-xs text-amiras-taupe mt-0.5">{{ $variantLabel }}</p>
                            @endif

                            @unless ($item->isAvailable())
                                <p class="text-xs text-amiras-bordeaux mt-1">Cet article n'est plus disponible.</p>
                            @endunless

                            <p class="text-sm text-amiras-ink mt-1">
                                {{ number_format($item->unitPrice(), 0, ',', ' ') }} FCFA
                            </p>
                        </div>

                        <form method="POST" action="{{ route('shop.cart.update', $item) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input
                                type="number"
                                name="quantity"
                                value="{{ $item->quantity }}"
                                min="0"
                                max="50"
                                class="w-16 rounded-md border border-amiras-ink/20 focus:border-amiras-gold focus:ring-amiras-gold text-sm"
                            >
                            <button type="submit" class="text-xs uppercase tracking-wide underline hover:text-amiras-gold">
                                Mettre à jour
                            </button>
                        </form>

                        <p class="w-24 text-right text-sm font-medium text-amiras-ink">
                            {{ number_format($item->lineTotal(), 0, ',', ' ') }} FCFA
                        </p>

                        <form method="POST" action="{{ route('shop.cart.destroy', $item) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-amiras-taupe hover:text-amiras-bordeaux" aria-label="Supprimer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex justify-end">
                <p class="text-lg font-display">
                    Total : {{ number_format($cart->total(), 0, ',', ' ') }} FCFA
                </p>
            </div>
        @endif
    </div>
</x-shop-layout>
