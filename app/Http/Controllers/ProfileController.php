<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Transaction;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Edición y eliminación del perfil del usuario.
 */
class ProfileController extends Controller
{
    /**
     * Muestra el formulario de edición del perfil con el estado de verificación del correo.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Guarda los datos del perfil y desmarca la verificación si se cambió el correo.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Valida la contraseña, desvincula movimientos, cede hogares propios, cierra la sesión y borra la cuenta.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Transaction::where('user_id', $user->id)->update(['user_id' => null]);

        // Si el dueño se va y quedan integrantes, alguien tiene que poder seguir administrando.
        foreach ($user->households()->wherePivot('role', 'owner')->get() as $household) {
            $sucesor = $household->users()->whereKeyNot($user->id)->orderBy('users.id')->first();

            if ($sucesor) {
                $household->users()->updateExistingPivot($sucesor->id, ['role' => 'owner']);
            }
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
