<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registro de servicios de la aplicación (sin servicios propios por ahora).
     */
    public function register(): void
    {
        //
    }

    /**
     * Inicialización de servicios: activa el prefetch limitado de assets con Vite.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
