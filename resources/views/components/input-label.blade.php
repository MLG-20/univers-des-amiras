@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-amiras-ink']) }}>
    {{ $value ?? $slot }}
</label>
