<div class="mx-auto max-w-2xl space-y-6">
    <div>
        <a href="{{ route('sales-orders.show', $salesOrder) }}" class="text-xs font-medium text-slate-500 hover:text-slate-700">&larr; Kembali ke Sales Order</a>
        <h2 class="mt-1 text-lg font-semibold text-slate-900">Buat Surat Jalan</h2>
        <p class="text-xs text-slate-500">SO {{ $salesOrder->so_number }} &middot; {{ $salesOrder->customer->name }}</p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="mb-3 text-sm font-semibold text-slate-900">Item yang Dikirim</h3>
        <div class="space-y-2 text-sm">
            @foreach ($salesOrder->items as $item)
                <div class="flex justify-between border-b border-slate-100 py-1.5">
                    <span class="text-slate-700">{{ $item->product->name }}</span>
                    <span class="font-medium text-slate-800">{{ number_format($item->quantity_packed) }} {{ $item->product->base_unit }}</span>
                </div>
            @endforeach
        </div>

        <form wire:submit="save" class="mt-6 space-y-4">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <x-form.input type="text" wire:model="driverName" label="Nama Driver" name="driverName" />
                <x-form.input type="text" wire:model="vehicleNumber" label="No. Kendaraan" name="vehicleNumber" />
            </div>
            <x-form.textarea wire:model="notes" rows="2" label="Catatan" />

            <div class="flex justify-end pt-2">
                <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-emerald-500">
                    Terbitkan Surat Jalan
                </button>
            </div>
        </form>
    </div>
</div>
