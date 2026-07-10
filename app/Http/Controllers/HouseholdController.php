<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Household;

class HouseholdController extends Controller
{
    public function show()
    {
        $household = auth()->user()->currentHousehold();

        return Inertia::render('Household/Show', [
            'household' => $household,
            'members' => $household->users()->get(['users.id', 'users.name', 'users.email']),
            'role' => auth()->user()->households()->first()->pivot->role,
        ]);
    }

    public function join(Request $request)
    {
        $validated = $request->validate([
            'invite_code' => 'required|string',
        ]);

        $household = Household::where('invite_code', strtoupper($validated['invite_code']))->first();

        if (! $household) {
            return back()->withErrors(['invite_code' => 'Código inválido.']);
        }

        if ($household->users()->where('user_id', auth()->id())->exists()) {
            return back()->withErrors(['invite_code' => 'Ya pertenecés a este household.']);
        }

        auth()->user()->households()->detach();

        $household->users()->attach(auth()->id(), ['role' => 'viewer']);

        return redirect()->route('household.show');
    }
}