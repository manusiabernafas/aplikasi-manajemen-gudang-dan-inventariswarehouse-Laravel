<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_opname_id', 'product_id', 'rack_id', 'system_qty',
        'physical_qty', 'variance_qty', 'unit_cost', 'notes',
    ];

    protected $casts = [
        'system_qty' => 'decimal:2',
        'physical_qty' => 'decimal:2',
        'variance_qty' => 'decimal:2',
        'unit_cost' => 'decimal:2',
    ];

    public function stockOpname(): BelongsTo
    {
        return $this->belongsTo(StockOpname::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function rack(): BelongsTo
    {
        return $this->belongsTo(Rack::class);
    }
}
