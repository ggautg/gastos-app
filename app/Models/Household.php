<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Household extends Model
{
    protected $fillable = ['name', 'invite_code'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($household) {
            if (empty($household->invite_code)) {
                $household->invite_code = strtoupper(Str::random(8));
            }
        });
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }
}