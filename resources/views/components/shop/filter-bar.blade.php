@props(['action', 'categories' => null])

<div
    x-data="productFilters({
        action: @js($action),
        initial: {
            q: @js(request('q', '')),
            category_id: @js((string) request('category_id', '')),
            min_price: @js((string) request('min_price', '')),
            max_price: @js((string) request('max_price', '')),
            in_stock: @js(request()->boolean('in_stock')),
        },
    })"
>
    <form @submit.prevent="apply()" class="flex flex-wrap items-end gap-4 mb-8">
        <div class="flex flex-col gap-1">
            <label class="text-xs uppercase tracking-wide text-brand-muted" for="filter-q">Rechercher</label>
            <input
                id="filter-q"
                type="search"
                x-model="filters.q"
                @input.debounce.400ms="apply()"
                placeholder="Nom du produit, catégorie..."
                class="rounded-md border-brand-ink/20 text-sm focus:border-brand-signature focus:ring-brand-accent"
            >
        </div>

        @if ($categories)
            <div class="flex flex-col gap-1">
                <label class="text-xs uppercase tracking-wide text-brand-muted" for="filter-category">Catégorie</label>
                <select
                    id="filter-category"
                    x-model="filters.category_id"
                    @change="apply()"
                    class="rounded-md border-brand-ink/20 text-sm focus:border-brand-signature focus:ring-brand-accent"
                >
                    <option value="">Toutes</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="flex flex-col gap-1">
            <label class="text-xs uppercase tracking-wide text-brand-muted" for="filter-min">Prix min</label>
            <input
                id="filter-min"
                type="number"
                min="0"
                x-model="filters.min_price"
                @input.debounce.400ms="apply()"
                class="w-28 rounded-md border-brand-ink/20 text-sm focus:border-brand-signature focus:ring-brand-accent"
            >
        </div>

        <div class="flex flex-col gap-1">
            <label class="text-xs uppercase tracking-wide text-brand-muted" for="filter-max">Prix max</label>
            <input
                id="filter-max"
                type="number"
                min="0"
                x-model="filters.max_price"
                @input.debounce.400ms="apply()"
                class="w-28 rounded-md border-brand-ink/20 text-sm focus:border-brand-signature focus:ring-brand-accent"
            >
        </div>

        <label class="flex items-center gap-2 text-sm pb-2">
            <input type="checkbox" x-model="filters.in_stock" @change="apply()">
            En stock uniquement
        </label>
    </form>

    <div x-ref="grid">
        {{ $slot }}
    </div>
</div>
