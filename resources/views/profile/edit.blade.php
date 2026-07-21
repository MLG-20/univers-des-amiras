<x-shop-layout :title="'Mon compte — '.config('app.name')">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="font-display text-3xl mb-6">Mon compte</h1>

        @include('account.partials.nav')

        <div class="space-y-6 max-w-xl">
            <div class="p-6 sm:p-8 bg-white border border-brand-ink/10 rounded-2xl">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="p-6 sm:p-8 bg-white border border-brand-ink/10 rounded-2xl">
                @include('profile.partials.update-password-form')
            </div>

            {{-- Zone sensible : léger fond rouge pour signaler la suppression. --}}
            <div class="p-6 sm:p-8 bg-red-50/40 border border-red-200/60 rounded-2xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-shop-layout>
