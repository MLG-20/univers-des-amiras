<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-amiras-ink border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amiras-ink/90 focus:bg-amiras-ink/90 active:bg-amiras-ink focus:outline-none focus:ring-2 focus:ring-amiras-gold focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
