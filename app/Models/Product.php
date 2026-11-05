<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'sku', 'barcode', 'name', 'description', 'base_unit',
        'costing_method', 'min_stock', 'max_stock', 'current_stock',
        'average_cost', 'image_path', 'is_active',
    ];

    protected $casts = [
        'min_stock' => 'decimal:2',
        'max_stock' => 'decimal:2',
        'current_stock' => 'decimal:2',
        'average_cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(ProductUnit::class);
    }

    public function stockBatches(): HasMany
    {
        return $this->hasMany(StockBatch::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->min_stock;
    }

    public function stockStatus(): string
    {
        if ($this->current_stock <= 0) {
            return 'out_of_stock';
        }

        if ($this->current_stock <= $this->min_stock) {
            return 'low_stock';
        }

        if ($this->max_stock && $this->current_stock >= $this->max_stock) {
            return 'overstock';
        }

        return 'healthy';
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->barcode)) {
                $product->barcode = static::generateBarcode();
            }
        });
    }

    public static function generateBarcode(): string
    {
        do {
            $barcode = '2'.now()->format('ymd').str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        } while (static::where('barcode', $barcode)->exists());

        return $barcode;
    }
}
