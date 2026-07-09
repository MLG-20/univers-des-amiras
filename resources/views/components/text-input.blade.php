@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-amiras-ink/20 focus:border-amiras-gold focus:ring-amiras-gold rounded-md shadow-sm']) }}>
