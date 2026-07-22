@props([
    // Sans `href`, le composant rend un <button> : la même règle de style doit
    // s'appliquer qu'on navigue ou qu'on soumette un formulaire. C'est ce qui
    // manquait pour que « Ajouter au panier » puisse être le CTA Garance de la
    // fiche produit sans recopier les classes à la main.
    'href' => null,
    'type' => 'submit',
    // Prop explicite plutôt que la directive `@disabled` : celle-ci ne
    // s'applique qu'aux balises HTML, pas aux balises de composant.
    'disabled' => false,
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

    $base = 'inline-block rounded-sm px-8 py-3.5 text-xs font-medium uppercase tracking-[0.15em] transition duration-300 '
        .'disabled:opacity-40 disabled:pointer-events-none '.$classes;
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $base]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" @disabled($disabled) {{ $attributes->merge(['class' => $base]) }}>
        {{ $slot }}
    </button>
@endif
