<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id', 'product_id', 'product_unit_id', 'quantity_ordered',
        'quantity_received', 'quantity_rejected', 'qc_status', 'qc_notes',
        'putaway_rack_id', 'unit_price', 'subtotal',
    ];

    protected $casts = [
        'quantity_ordered' => 'decimal:2',
        'quantity_received' => 'decimal:2',
        'quantity_rejected' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productUnit(): BelongsTo
    {
        return $this->belongsTo(ProductUnit::class);
    }

    public function putawayRack(): BelongsTo
    {
        return $this->belongsTo(Rack::class, 'putaway_rack_id');
    }

    public function remainingToReceive(): float
    {
        return (float) $this->quantity_ordered - (float) $this->quantity_received - (float) $this->quantity_rejected;
    }
}
