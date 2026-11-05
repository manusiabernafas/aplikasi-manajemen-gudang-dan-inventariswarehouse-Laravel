<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'do_number', 'sales_order_id', 'warehouse_id', 'driver_name', 'vehicle_number',
        'status', 'shipped_at', 'delivered_at', 'notes', 'created_by',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateNumber(): string
    {
        return 'DO-'.now()->format('Ymd').'-'.str_pad((string) (static::whereDate('created_at', now())->count() + 1), 4, '0', STR_PAD_LEFT);
    }
}
