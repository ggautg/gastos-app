<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

/**
 * Gestión del hogar activo: integrantes, invitaciones, cambio, renombrado y borrado.
 */
class HouseholdController extends Controller
{
    /**
     * Muestra los datos del hogar, sus integrantes, estadísticas y los hogares del usuario.
     */
    public function show()
    {
        $household = $this->currentHousehold();
        $role = $this->roleIn($household);

        return Inertia::render('Household/Show', [
            'household' => [
                'id' => $household->id,
                'name' => $household->name,
                'invite_code' => $role === 'owner' ? $household->invite_code : null,
            ],
            'members' => $household->users()->get(['users.id', 'users.name', 'users.email']),
            'role' => $role,
            'stats' => [
                'categories' => $household->categories()->count(),
                'transactions' => Transaction::whereHas('category', function ($q) use ($household) {
                    $q->where('household_id', $household->id);
                })->count(),
            ],
            'households' => auth()->user()->households()
                ->get(['households.id', 'households.name'])
                ->map(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'role' => $item->pivot->role,
                    'miembros' => $item->users()->count(),
                    'actual' => $item->id === $household->id,
                ]),
        ]);
    }

    /**
     * Suma al usuario a un hogar por código de invitación como viewer y lo deja como activo.
     */
    public function join(Request $request)
    {
        $validated = $request->validate([
            'invite_code' => 'required|string|max:20',
        ]);

        $household = Household::where('invite_code', strtoupper($validated['invite_code']))->first();

        if (! $household) {
            return back()->withErrors(['invite_code' => 'El código de invitación no es válido. Revisalo e intentá de nuevo.']);
        }

        $user = auth()->user();

        if ($household->users()->whereKey($user->id)->exists()) {
            return back()->withErrors(['invite_code' => 'Ya pertenecés a este hogar.']);
        }

        $household->users()->attach($user->id, ['role' => 'viewer']);
        $user->useHousehold($household);

        return redirect()->route('household.show');
    }

    /**
     * Cambia el hogar activo del usuario a uno al que ya pertenezca.
     */
    public function switch(Request $request)
    {
        $validated = $request->validate([
            'household_id' => ['required', Rule::in(auth()->user()->households()->pluck('households.id'))],
        ]);

        auth()->user()->useHousehold(Household::findOrFail($validated['household_id']));

        return redirect()->route('household.show');
    }

    /**
     * Renombra el hogar activo; requiere permiso de dueño (un viewer recibe 403).
     */
    public function update(Request $request)
    {
        $household = $this->currentHousehold();
        abort_unless($this->roleIn($household) === 'owner', 403, 'Solo el dueño puede cambiar el nombre del hogar.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80'],
        ]);

        $household->update($validated);

        return redirect()->route('household.show');
    }

    /**
     * Borra el hogar con sus categorías y movimientos tras reubicar a los integrantes; requiere dueño.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'confirm' => ['required', 'accepted'],
        ]);

        $household = $this->currentHousehold();
        abort_unless($this->roleIn($household) === 'owner', 403, 'Solo el dueño puede eliminar el hogar.');

        $this->reubicarMiembros($household);

        // La FK de movimientos es RESTRICT en MySQL: primero se borran los movimientos.
        Transaction::whereHas('category', function ($q) use ($household) {
            $q->where('household_id', $household->id);
        })->delete();
        $household->categories()->delete();
        $household->users()->detach();
        $household->delete();

        return redirect()->route('household.show');
    }

    /**
     * Saca al usuario del hogar, cediendo el rol de dueño si hace falta; no permite salir del único hogar.
     */
    public function leave(Request $request)
    {
        $validated = $request->validate([
            'household_id' => ['required', Rule::in(auth()->user()->households()->pluck('households.id'))],
        ]);

        $user = auth()->user();
        $household = Household::findOrFail($validated['household_id']);

        abort_if($user->households()->count() === 1, 403, 'No podés salir del único hogar que tenés.');

        if ($this->roleIn($household) === 'owner') {
            $sucesor = $household->users()->whereKeyNot($user->id)->orderBy('users.id')->first();
            abort_unless($sucesor, 403, 'Sos el único integrante: eliminá el hogar en vez de salir.');
            $household->users()->updateExistingPivot($sucesor->id, ['role' => 'owner']);
        }

        $household->users()->detach($user->id);

        if ($user->current_household_id === $household->id) {
            $user->useHousehold($user->households()->firstOrFail());
        }

        return redirect()->route('household.show');
    }

    // Nadie puede quedarse sin hogar: si no tiene otro, se crea uno propio.
    /**
     * Asegura que cada integrante tenga otro hogar (o le crea uno) antes de borrar el actual.
     */
    private function reubicarMiembros(Household $household): void
    {
        foreach ($household->users()->get() as $miembro) {
            $destino = $miembro->households()->whereKeyNot($household->id)->first();

            if (! $destino) {
                $destino = Household::create(['name' => 'Finanzas de '.$miembro->name]);
                $destino->users()->attach($miembro->id, ['role' => 'owner']);
            }

            $miembro->forceFill(['current_household_id' => $destino->id])->save();
        }
    }
}
