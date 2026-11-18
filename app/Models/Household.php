<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Hogar: grupo de usuarios que comparte categorías y movimientos.
 */
class Household extends Model
{
    protected $fillable = ['name', 'invite_code'];

    /**
     * Genera un código de invitación aleatorio al crear el hogar si no se indicó uno.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($household) {
            if (empty($household->invite_code)) {
                $household->invite_code = strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Muchos integrantes del hogar, con su rol en la tabla pivote.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Muchas categorías del hogar.
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }
}