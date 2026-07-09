<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white border border-amiras-ink/20 rounded-md font-semibold text-xs text-amiras-ink uppercase tracking-widest shadow-sm hover:bg-amiras-cream focus:outline-none focus:ring-2 focus:ring-amiras-gold focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
