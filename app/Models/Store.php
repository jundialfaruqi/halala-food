<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_name',
        'phone',
        'address',
        'route',
        'commission_rate',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'commission_rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function consignments(): HasMany
    {
        return $this->hasMany(Consignment::class);
    }
}
