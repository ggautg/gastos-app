<?php

namespace App\Http\Controllers;

use App\Models\Household;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

abstract class Controller
{
    use AuthorizesRequests;

    protected function currentHousehold(): Household
    {
        $household = auth()->user()->currentHousehold();

        abort_unless($household, 403, 'No tenés un hogar configurado.');

        return $household;
    }

    protected function roleIn(Household $household): string
    {
        return $household->users()->findOrFail(auth()->id())->pivot->role;
    }

    protected function authorizeViewerCannotEdit(Household $household): void
    {
        abort_if($this->roleIn($household) === 'viewer', 403, 'No tenés permiso para modificar datos en este household.');
    }

    protected function month(Request $request): int
    {
        return max(1, min(12, $request->integer('month', now()->month)));
    }

    protected function year(Request $request): int
    {
        return max(2000, min(2100, $request->integer('year', now()->year)));
    }

    protected function day(Request $request): ?int
    {
        $day = $request->integer('day');

        return $day >= 1 && $day <= 31 ? $day : null;
    }
}
