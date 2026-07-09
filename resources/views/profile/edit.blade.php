<x-shop-layout :title="'Mon compte — '.config('app.name')">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="font-display text-3xl mb-6">Mon compte</h1>

        @include('account.partials.nav')

        <div class="space-y-6 max-w-xl">
            <div class="p-4 sm:p-8 bg-white border border-amiras-ink/10 rounded-md">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="p-4 sm:p-8 bg-white border border-amiras-ink/10 rounded-md">
                @include('profile.partials.update-password-form')
            </div>

            <div class="p-4 sm:p-8 bg-white border border-amiras-ink/10 rounded-md">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-shop-layout>
