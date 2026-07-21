@props([
    'href',
    'variant' => 'secondary',
    'onDark' => false,
])

{{-- Boutons du design system (rapport d'identité p.13).

     Règle explicite du rapport : « Un seul CTA principal Garance par écran. Les
     actions secondaires restent en contour ou en texte. » La variante `primary`
     est donc à n'utiliser qu'une fois par page — d'où ce composant, qui rend la
     règle visible à la lecture du Blade plutôt qu'enfouie dans des classes.

     `onDark` adapte la variante secondaire aux fonds Encre (hero, pied de page),
     où une bordure Encre serait invisible. --}}
@php
    $classes = match ($variant) {
        'primary' => 'bg-brand-accent text-brand-surface hover:bg-brand-signature',
        default => $onDark
            ? 'border border-brand-surface/40 text-brand-surface hover:border-brand-surface'
            : 'border border-brand-ink/30 text-brand-ink hover:border-brand-ink',
    };
@endphp

<a
    href="{{ $href }}"
    {{ $attributes->merge([
        'class' => 'inline-block rounded-sm px-8 py-3.5 text-xs font-medium uppercase tracking-[0.15em] transition duration-300 '.$classes,
    ]) }}
>
    {{ $slot }}
</a>
