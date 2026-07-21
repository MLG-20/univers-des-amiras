{{-- Page d'attente des sections présentes dans la maquette mais dont le module
     arrive en Phase 2.2 (Collections, Journal, Liste d'envies).

     Elle existe pour que la navigation de la maquette soit complète dès
     maintenant sans livrer de lien mort. À supprimer, avec sa route, dès que le
     module correspondant est livré — cf. docs/files/02.2-modules-maquette.md --}}
<x-shop-layout :title="$heading.' — '.config('app.name')">
    <section class="w-full bg-brand-parchment border-b border-brand-ink/10 py-14 sm:py-20">
        <div class="max-w-shell mx-auto px-4 sm:px-6 lg:px-10 xl:px-12">
            <span class="text-[0.65rem] uppercase tracking-[0.25em] text-brand-signature">Bientôt</span>
            <h1 class="font-display text-4xl sm:text-5xl text-brand-ink mt-2">{{ $heading }}</h1>
            <p class="mt-3 text-brand-muted max-w-xl">{{ $intro }}</p>
        </div>
    </section>

    <div class="max-w-shell mx-auto px-4 sm:px-6 lg:px-10 xl:px-12 py-20">
        <div class="max-w-lg">
            <p class="text-brand-ink/80">
                Cette section est en cours de préparation. En attendant, la sélection complète
                est disponible dans le catalogue.
            </p>

            <div class="mt-8">
                <x-shop.cta :href="route('shop.index')" variant="primary">
                    Voir le catalogue
                </x-shop.cta>
            </div>
        </div>
    </div>
</x-shop-layout>
