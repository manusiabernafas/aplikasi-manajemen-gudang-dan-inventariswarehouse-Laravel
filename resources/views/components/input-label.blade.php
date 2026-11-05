@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-medium text-slate-600']) }}>
    {{ $value ?? $slot }}
</label>
