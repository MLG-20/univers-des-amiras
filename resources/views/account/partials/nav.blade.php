@php
    $accountLinks = [
        'dashboard' => 'Tableau de bord',
        'profile.edit' => 'Informations personnelles',
        'account.addresses.index' => 'Adresses',
        'account.orders.index' => 'Commandes',
    ];
@endphp

<nav class="flex flex-wrap gap-6 border-b border-brand-ink/10 mb-8 text-sm uppercase tracking-wide">
    @foreach ($accountLinks as $routeName => $label)
        <a
            href="{{ route($routeName) }}"
            class="pb-3 border-b -mb-px {{ request()->routeIs($routeName) ? 'border-brand-signature text-brand-ink' : 'border-transparent text-brand-ink/60 hover:text-brand-ink' }}"
        >
            {{ $label }}
        </a>
    @endforeach
</nav>
