@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-brand-ink/20 focus:border-brand-signature focus:ring-brand-accent rounded-md shadow-sm']) }}>
