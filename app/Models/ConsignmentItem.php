<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsignmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'consignment_id',
        'product_id',
        'quantity_dropped',
        'price_per_item',
        'quantity_remaining',
        'quantity_returned',
        'quantity_sold',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'quantity_dropped' => 'integer',
            'price_per_item' => 'decimal:2',
            'quantity_remaining' => 'integer',
            'quantity_returned' => 'integer',
            'quantity_sold' => 'integer',
            'subtotal' => 'decimal:2',
        ];
    }

    public function consignment(): BelongsTo
    {
        return $this->belongsTo(Consignment::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
