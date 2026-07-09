<x-shop-layout :title="'Mes commandes — '.config('app.name')">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="font-display text-3xl mb-6">Mon compte</h1>

        @include('account.partials.nav')

        <div class="p-8 bg-white border border-amiras-ink/10 rounded-md text-center">
            <p class="text-amiras-ink">Vous n'avez pas encore passé de commande.</p>
            <p class="text-sm text-amiras-taupe mt-2">
                Votre historique de commandes apparaîtra ici une fois le tunnel de commande en ligne.
            </p>
            <a href="{{ route('shop.index') }}" class="inline-block mt-4 text-sm underline text-amiras-ink hover:text-amiras-gold">
                Continuer mes achats
            </a>
        </div>
    </div>
</x-shop-layout>
