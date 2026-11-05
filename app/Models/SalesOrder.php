<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'so_number', 'customer_id', 'warehouse_id', 'status', 'order_date',
        'required_date', 'total_amount', 'notes', 'created_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'required_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function pickingLists(): HasMany
    {
        return $this->hasMany(PickingList::class);
    }

    public function deliveryOrders(): HasMany
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateNumber(): string
    {
        return 'SO-'.now()->format('Ymd').'-'.str_pad((string) (static::whereDate('created_at', now())->count() + 1), 4, '0', STR_PAD_LEFT);
    }
}
