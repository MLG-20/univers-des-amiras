<x-guest-layout>
    <h1 class="font-display text-3xl text-amiras-ink mb-2">Bon retour parmi nous</h1>
    <p class="text-sm text-amiras-taupe mb-8">Connectez-vous pour retrouver votre univers.</p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Mot de passe" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-amiras-ink/30 text-amiras-gold shadow-sm focus:ring-amiras-gold" name="remember">
                <span class="ms-2 text-sm text-amiras-ink/80">Se souvenir de moi</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-amiras-taupe hover:text-amiras-ink underline" href="{{ route('password.request') }}">
                    Mot de passe oublié ?
                </a>
            @endif
        </div>

        <x-primary-button class="w-full justify-center py-3">
            Se connecter
        </x-primary-button>
    </form>

    <p class="mt-8 text-center text-sm text-amiras-taupe">
        Pas encore de compte ?
        <a href="{{ route('register') }}" class="text-amiras-ink underline hover:text-amiras-gold">Créez-en un</a>
    </p>
</x-guest-layout>
