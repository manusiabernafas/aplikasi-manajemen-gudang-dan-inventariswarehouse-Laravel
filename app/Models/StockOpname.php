<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpname extends Model
{
    use HasFactory;

    protected $fillable = [
        'opname_number', 'warehouse_id', 'status', 'scheduled_date', 'frozen_at',
        'completed_at', 'notes', 'created_by', 'approved_by',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'frozen_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockOpnameItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public static function generateNumber(): string
    {
        return 'OPN-'.now()->format('Ymd').'-'.str_pad((string) (static::whereDate('created_at', now())->count() + 1), 4, '0', STR_PAD_LEFT);
    }
}
