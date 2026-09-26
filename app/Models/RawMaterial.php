<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RawMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'stock',
        'min_stock',
        'cost_per_unit',
    ];

    protected function casts(): array
    {
        return [
            'stock' => 'decimal:2',
            'min_stock' => 'decimal:2',
            'cost_per_unit' => 'decimal:2',
        ];
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(ProductRecipe::class);
    }
}
