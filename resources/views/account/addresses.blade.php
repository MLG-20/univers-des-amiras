<x-shop-layout :title="'Mes adresses — '.config('app.name')">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="font-display text-3xl mb-6">Mon compte</h1>

        @include('account.partials.nav')

        <div class="grid gap-8 lg:grid-cols-2">
            <div>
                <h2 class="text-lg font-medium text-amiras-ink mb-4">Adresses enregistrées</h2>

                @if (session('status') === 'address-created')
                    <p class="text-sm text-green-700 mb-4">Adresse ajoutée.</p>
                @elseif (session('status') === 'address-updated')
                    <p class="text-sm text-green-700 mb-4">Adresse mise à jour.</p>
                @elseif (session('status') === 'address-deleted')
                    <p class="text-sm text-green-700 mb-4">Adresse supprimée.</p>
                @endif

                @forelse ($addresses as $address)
                    <div class="p-4 mb-4 bg-white border border-amiras-ink/10 rounded-md" x-data="{ editing: false }">
                        <div x-show="!editing">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-medium text-amiras-ink">
                                        {{ $address->recipient_name }}
                                        @if ($address->is_default)
                                            <span class="ml-2 text-xs uppercase tracking-wide text-amiras-gold border border-amiras-gold rounded-md px-2 py-0.5">Par défaut</span>
                                        @endif
                                    </p>
                                    <p class="text-sm text-amiras-ink/80 mt-1">{{ $address->phone }}</p>
                                    <p class="text-sm text-amiras-ink/80">{{ $address->address_line }}, {{ $address->city }}</p>
                                    @if ($address->landmark)
                                        <p class="text-sm text-amiras-taupe">Repère : {{ $address->landmark }}</p>
                                    @endif
                                </div>

                                <div class="flex flex-col gap-2 items-end shrink-0">
                                    <button type="button" x-on:click="editing = true" class="text-sm underline text-amiras-taupe hover:text-amiras-ink">
                                        Modifier
                                    </button>

                                    <form method="post" action="{{ route('account.addresses.destroy', $address) }}" onsubmit="return confirm('Supprimer cette adresse ?');">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="text-sm underline text-red-600 hover:text-red-800">Supprimer</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <form x-show="editing" x-cloak method="post" action="{{ route('account.addresses.update', $address) }}" class="space-y-4">
                            @csrf
                            @method('put')
                            @include('account.partials.address-fields', ['address' => $address])

                            <div class="flex items-center gap-3">
                                <x-primary-button>Enregistrer</x-primary-button>
                                <button type="button" x-on:click="editing = false" class="text-sm text-amiras-taupe hover:text-amiras-ink">Annuler</button>
                            </div>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-amiras-taupe">Vous n'avez pas encore enregistré d'adresse.</p>
                @endforelse
            </div>

            <div>
                <h2 class="text-lg font-medium text-amiras-ink mb-4">Ajouter une adresse</h2>

                <form method="post" action="{{ route('account.addresses.store') }}" class="p-4 bg-white border border-amiras-ink/10 rounded-md space-y-4">
                    @csrf
                    @include('account.partials.address-fields')

                    <x-primary-button>Ajouter l'adresse</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-shop-layout>
