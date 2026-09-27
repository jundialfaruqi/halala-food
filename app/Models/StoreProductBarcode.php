<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreProductBarcode extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'product_id',
        'barcode',
        'barcode_type',
        'store_sku',
        'custom_product_name',
        'custom_price',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'custom_price' => 'decimal:2',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the effective display name for printing.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->custom_product_name ?: ($this->product?->name ?? 'Produk Halala');
    }

    /**
     * Get the effective price for printing.
     */
    public function getDisplayPriceAttribute(): float
    {
        if ($this->custom_price !== null && (float) $this->custom_price > 0) {
            return (float) $this->custom_price;
        }

        return (float) ($this->product?->retail_price ?? 0);
    }
}
