<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'consignment_price',
        'retail_price',
        'stock_ready',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'consignment_price' => 'decimal:2',
            'retail_price' => 'decimal:2',
            'stock_ready' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(ProductRecipe::class);
    }

    public function productions(): HasMany
    {
        return $this->hasMany(Production::class);
    }

    public function consignmentItems(): HasMany
    {
        return $this->hasMany(ConsignmentItem::class);
    }

    public function barcodes(): HasMany
    {
        return $this->hasMany(StoreProductBarcode::class);
    }

    public function getMaterialCostAttribute(): float
    {
        return (float) $this->recipes->sum(function ($r) {
            $costPerUnit = $r->rawMaterial?->cost_per_unit ?? 0;

            return (float) $r->quantity_needed * (float) $costPerUnit;
        });
    }
}
