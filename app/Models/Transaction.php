<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'type', 'amount',
        'currency', 'exchange_rate', 'amount_gs',
        'description', 'date',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'integer',
        'exchange_rate' => 'decimal:2',
        'amount_gs' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
