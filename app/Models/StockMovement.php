<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'warehouse_id', 'rack_id', 'stock_batch_id', 'type',
        'quantity', 'unit_cost', 'balance_after', 'reference_type', 'reference_id',
        'notes', 'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    const IN_TYPES = ['in', 'transfer_in', 'adjustment_in'];

    const OUT_TYPES = ['out', 'transfer_out', 'adjustment_out'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function rack(): BelongsTo
    {
        return $this->belongsTo(Rack::class);
    }

    public function stockBatch(): BelongsTo
    {
        return $this->belongsTo(StockBatch::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isInbound(): bool
    {
        return in_array($this->type, self::IN_TYPES, true);
    }
}
