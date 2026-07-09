<x-guest-layout>
    <h1 class="font-display text-3xl text-amiras-ink mb-2">Vérifiez votre email</h1>
    <p class="text-sm text-amiras-taupe mb-8">
        Merci de votre inscription ! Avant de commencer, confirmez votre adresse email en cliquant sur le lien
        que nous venons de vous envoyer. Si vous ne l'avez pas reçu, nous vous en renvoyons un volontiers.
    </p>

    @if (session('status') == 'verification-link-sent')
        <p class="mb-6 font-medium text-sm text-green-700">
            Un nouveau lien de vérification a été envoyé à l'adresse email indiquée lors de l'inscription.
        </p>
    @endif

    <div class="flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>Renvoyer l'email de vérification</x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="underline text-sm text-amiras-taupe hover:text-amiras-ink">
                Se déconnecter
            </button>
        </form>
    </div>
</x-guest-layout>
