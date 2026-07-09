<x-guest-layout>
    <h1 class="font-display text-3xl text-amiras-ink mb-2">Créer un compte</h1>
    <p class="text-sm text-amiras-taupe mb-8">Rejoignez l'Univers des Amiras en quelques instants.</p>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="name" value="Nom" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Mot de passe" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center py-3">
            Créer mon compte
        </x-primary-button>
    </form>

    <p class="mt-8 text-center text-sm text-amiras-taupe">
        Déjà inscrite ?
        <a href="{{ route('login') }}" class="text-amiras-ink underline hover:text-amiras-gold">Connectez-vous</a>
    </p>
</x-guest-layout>
