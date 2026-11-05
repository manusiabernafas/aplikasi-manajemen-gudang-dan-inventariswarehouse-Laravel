<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PickingList extends Model
{
    use HasFactory;

    protected $fillable = [
        'picking_number', 'sales_order_id', 'warehouse_id', 'status',
        'assigned_to', 'started_at', 'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PickingListItem::class);
    }

    public static function generateNumber(): string
    {
        return 'PIC-'.now()->format('Ymd').'-'.str_pad((string) (static::whereDate('created_at', now())->count() + 1), 4, '0', STR_PAD_LEFT);
    }
}
