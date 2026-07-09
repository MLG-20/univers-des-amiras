@props(['category' => null])

@php
    $initial = mb_strtoupper(mb_substr((string) ($category->name ?? 'A'), 0, 1));
@endphp

{{-- Repli élégant en l'absence de photo produit : reprend l'élément
signature du doc UX/UI (boîte à bordure fine dorée) plutôt qu'une icône
générique — cohérent avec le hero et les bannières collection. --}}
<div {{ $attributes->merge(['class' => 'h-full w-full flex items-center justify-center bg-gradient-to-br from-amiras-ivory via-amiras-cream to-amiras-taupe/10']) }}>
    <div class="flex items-center justify-center w-[38%] aspect-square border border-amiras-gold/50">
        <span class="font-display text-lg sm:text-2xl lg:text-3xl text-amiras-gold/80">
            {{ $initial }}
        </span>
    </div>
</div>
