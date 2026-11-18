<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * Plantilla raíz que se carga en la primera visita a la app.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Devuelve la versión actual de los assets, para invalidar la caché al desplegar.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Propiedades que Inertia comparte con todas las páginas (datos del usuario autenticado).
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
        ];
    }
}
