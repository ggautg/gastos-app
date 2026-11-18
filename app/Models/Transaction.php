<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Movimiento (gasto o ganancia) cargado por un usuario dentro de una categoría.
 */
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

    /**
     * La categoría a la que pertenece el movimiento.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * El usuario que cargó el movimiento.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
