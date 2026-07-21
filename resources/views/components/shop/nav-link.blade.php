@props(['link'])

{{-- Entrée de la navigation principale. Extraite en composant parce que la
     navigation est désormais scindée de part et d'autre de la signature
     (cf. layouts/shop.blade.php) : sans ça, le même balisage serait dupliqué
     dans les deux moitiés et les deux copies divergeraient à la première
     retouche.

     Pas d'`opacity-80` au repos : sur le hero photographique, l'onglet Parchemin
     déjà transparent passait sous le seuil de lisibilité (contrôle p.16). Le
     survol se signale par le filet Garance — l'« Edit Cut » du langage graphique
     (p.9) — et non par un gain d'opacité. --}}
@if ($link['children'])
    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
        <a
            href="{{ $link['url'] }}"
            class="flex items-center gap-1 text-current border-b border-transparent hover:border-brand-accent pb-1 transition"
        >
            {{ $link['label'] }}
        </a>

        <div
            x-show="open"
            x-cloak
            x-transition
            class="absolute left-0 top-full w-56 bg-white border border-brand-ink/10 shadow-lg normal-case tracking-normal py-2 z-40"
        >
            @foreach ($link['children'] as $child)
                <a
                    href="{{ route('shop.category', $child['slug']) }}"
                    class="block px-4 py-2 text-sm text-brand-ink/80 hover:bg-brand-parchment hover:text-brand-ink"
                >
                    {{ $child['name'] }}
                </a>
            @endforeach
        </div>
    </div>
@else
    <a href="{{ $link['url'] }}" class="text-current border-b border-transparent hover:border-brand-accent pb-1 transition">
        {{ $link['label'] }}
    </a>
@endif
