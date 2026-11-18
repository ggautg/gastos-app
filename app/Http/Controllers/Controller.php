<?php

namespace App\Http\Controllers;

use App\Models\Household;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

/**
 * Controlador base: acceso al hogar activo, al rol del usuario y a los filtros de fecha.
 */
abstract class Controller
{
    use AuthorizesRequests;

    /**
     * Devuelve el hogar activo del usuario o corta con 403 si no tiene ninguno.
     */
    protected function currentHousehold(): Household
    {
        $household = auth()->user()->currentHousehold();

        abort_unless($household, 403, 'No tenés un hogar configurado. Creá uno desde «Mi hogar» para empezar.');

        return $household;
    }

    /**
     * Devuelve el rol (owner o viewer) del usuario en el hogar; falla si no lo integra.
     */
    protected function roleIn(Household $household): string
    {
        return $household->users()->findOrFail(auth()->id())->pivot->role;
    }

    /**
     * Chequea permiso de edición: solo el dueño puede modificar el hogar, un viewer recibe 403.
     */
    protected function authorizeViewerCannotEdit(Household $household): void
    {
        abort_if($this->roleIn($household) === 'viewer', 403, 'No tenés permiso para modificar los datos de este hogar. Solo el dueño puede hacerlo.');
    }

    /**
     * Lee el mes del request y lo acota entre 1 y 12; por defecto, el mes actual.
     */
    protected function month(Request $request): int
    {
        return max(1, min(12, $request->integer('month', now()->month)));
    }

    /**
     * Lee el año del request y lo acota entre 2000 y 2100; por defecto, el año actual.
     */
    protected function year(Request $request): int
    {
        return max(2000, min(2100, $request->integer('year', now()->year)));
    }

    /**
     * Devuelve el día del request solo si está entre 1 y 31; si no, devuelve null.
     */
    protected function day(Request $request): ?int
    {
        $day = $request->integer('day');

        return $day >= 1 && $day <= 31 ? $day : null;
    }
}
