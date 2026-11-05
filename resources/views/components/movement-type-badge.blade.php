@props(['type'])

@php
    $styles = [
        'in' => ['label' => 'Masuk', 'class' => 'bg-emerald-100 text-emerald-700'],
        'transfer_in' => ['label' => 'Transfer Masuk', 'class' => 'bg-emerald-100 text-emerald-700'],
        'adjustment_in' => ['label' => 'Adjust Masuk', 'class' => 'bg-emerald-100 text-emerald-700'],
        'out' => ['label' => 'Keluar', 'class' => 'bg-rose-100 text-rose-700'],
        'transfer_out' => ['label' => 'Transfer Keluar', 'class' => 'bg-rose-100 text-rose-700'],
        'adjustment_out' => ['label' => 'Adjust Keluar', 'class' => 'bg-rose-100 text-rose-700'],
    ];

    $style = $styles[$type] ?? ['label' => $type, 'class' => 'bg-slate-100 text-slate-600'];
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $style['class'] }}">
    {{ $style['label'] }}
</span>
