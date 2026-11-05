@props(['label' => null, 'name' => null, 'wrapperClass' => ''])

<div class="{{ $wrapperClass }}">
    @if ($label)
        <label @if($attributes->get('id')) for="{{ $attributes->get('id') }}" @endif class="mb-1.5 block text-xs font-medium text-slate-600">{{ $label }}</label>
    @endif

    <div class="relative">
        <select {{ $attributes->merge(['class' => 'w-full cursor-pointer appearance-none rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 pr-10 text-sm text-slate-800 shadow-sm transition-colors duration-150 hover:border-slate-300 focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400']) }}>
            {{ $slot }}
        </select>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    </div>

    @if ($name)
        @error($name) <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    @endif
</div>
