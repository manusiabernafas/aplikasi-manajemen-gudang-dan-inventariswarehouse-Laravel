<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PickingListItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'picking_list_id', 'sales_order_item_id', 'product_id', 'rack_id',
        'stock_batch_id', 'quantity_to_pick', 'quantity_picked', 'sequence',
    ];

    protected $casts = [
        'quantity_to_pick' => 'decimal:2',
        'quantity_picked' => 'decimal:2',
    ];

    public function pickingList(): BelongsTo
    {
        return $this->belongsTo(PickingList::class);
    }

    public function salesOrderItem(): BelongsTo
    {
        return $this->belongsTo(SalesOrderItem::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function rack(): BelongsTo
    {
        return $this->belongsTo(Rack::class);
    }

    public function stockBatch(): BelongsTo
    {
        return $this->belongsTo(StockBatch::class);
    }
}
