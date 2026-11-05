@props(['label' => null, 'name' => null, 'wrapperClass' => ''])

<div class="{{ $wrapperClass }}">
    @if ($label)
        <label @if($attributes->get('id')) for="{{ $attributes->get('id') }}" @endif class="mb-1.5 block text-xs font-medium text-slate-600">{{ $label }}</label>
    @endif

    <textarea {{ $attributes->merge(['class' => 'w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 shadow-sm transition-colors duration-150 hover:border-slate-300 focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/10']) }}></textarea>

    @if ($name)
        @error($name) <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    @endif
</div>
