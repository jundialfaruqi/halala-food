<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'consignment_number',
        'store_id',
        'drop_date',
        'settlement_date',
        'status',
        'payment_status',
        'total_sold_amount',
        'total_commission',
        'total_net_received',
        'amount_paid',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'drop_date' => 'date',
            'settlement_date' => 'date',
            'total_sold_amount' => 'decimal:2',
            'total_commission' => 'decimal:2',
            'total_net_received' => 'decimal:2',
            'amount_paid' => 'decimal:2',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ConsignmentItem::class);
    }

    public function cashTransactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class);
    }
}
