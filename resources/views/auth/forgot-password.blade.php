<x-guest-layout>
    <h1 class="font-display text-3xl text-amiras-ink mb-2">Mot de passe oublié</h1>
    <p class="text-sm text-amiras-taupe mb-8">
        Indiquez votre email, nous vous enverrons un lien pour choisir un nouveau mot de passe.
    </p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center py-3">
            Envoyer le lien de réinitialisation
        </x-primary-button>
    </form>

    <p class="mt-8 text-center text-sm text-amiras-taupe">
        <a href="{{ route('login') }}" class="text-amiras-ink underline hover:text-amiras-gold">Retour à la connexion</a>
    </p>
</x-guest-layout>
