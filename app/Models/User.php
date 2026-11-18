<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Usuario que se autentica en la app y participa en uno o varios hogares.
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Define los atributos que Laravel convierte automáticamente al leerlos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Muchos hogares a los que pertenece el usuario, con el rol en la tabla pivote.
     */
    public function households(): BelongsToMany
    {
        return $this->belongsToMany(Household::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Devuelve el hogar activo; si el guardado ya no es válido, usa el primero al que pertenece.
     */
    public function currentHousehold(): ?Household
    {
        if ($this->current_household_id) {
            $actual = $this->households()->find($this->current_household_id);

            if ($actual) {
                return $actual;
            }
        }

        return $this->households()->first();
    }

    /**
     * Guarda el hogar indicado como activo para el usuario.
     */
    public function useHousehold(Household $household): void
    {
        $this->forceFill(['current_household_id' => $household->id])->save();
    }
}
