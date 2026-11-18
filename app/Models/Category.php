<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Categoría de gasto o ganancia de un hogar, con presupuesto opcional.
 */
class Category extends Model
{
    protected $fillable = ['household_id', 'name', 'type', 'color', 'budget', 'icon'];

    /**
     * El hogar al que pertenece la categoría.
     */
    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    /**
     * Muchos movimientos cargados bajo esta categoría.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}