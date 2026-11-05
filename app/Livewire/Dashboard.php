<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\StockMovement;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    #[Computed]
    public function totalSkus(): int
    {
        return Product::where('is_active', true)->count();
    }

    #[Computed]
    public function totalValuation(): float
    {
        return (float) Product::where('is_active', true)
            ->selectRaw('SUM(current_stock * average_cost) as total')
            ->value('total');
    }

    #[Computed]
    public function lowStockCount(): int
    {
        return Product::where('is_active', true)
            ->whereColumn('current_stock', '<=', 'min_stock')
            ->count();
    }

    #[Computed]
    public function outOfStockCount(): int
    {
        return Product::where('is_active', true)->where('current_stock', '<=', 0)->count();
    }

    #[Computed]
    public function lowStockProducts()
    {
        return Product::where('is_active', true)
            ->whereColumn('current_stock', '<=', 'min_stock')
            ->orderBy('current_stock')
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function recentMovements()
    {
        return StockMovement::with(['product', 'warehouse', 'creator'])
            ->latest()
            ->limit(8)
            ->get();
    }

    #[Computed]
    public function movementChart(): array
    {
        $days = collect(range(6, 0))->map(fn ($daysAgo) => now()->subDays($daysAgo)->startOfDay());

        return $days->map(function ($day) {
            $inbound = (float) StockMovement::whereIn('type', ['in', 'transfer_in', 'adjustment_in'])
                ->whereBetween('created_at', [$day, $day->copy()->endOfDay()])
                ->sum('quantity');

            $outbound = (float) StockMovement::whereIn('type', ['out', 'transfer_out', 'adjustment_out'])
                ->whereBetween('created_at', [$day, $day->copy()->endOfDay()])
                ->sum('quantity');

            return [
                'label' => $day->translatedFormat('D'),
                'in' => $inbound,
                'out' => $outbound,
            ];
        })->values()->all();
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('layouts.app', ['title' => 'Dashboard']);
    }
}
