<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use App\Models\Destino;
use App\Models\MapeoUbicacion;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        // 1. Forzar HTTPS en Producción (Critical para Geolocalización móvil)
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // 2. Registrar View Composer para Ubicacion
        // Se ejecuta cada vez que una vista se renderiza, asegurando que Session esté disponible
        View::composer('*', \App\Http\ViewComposers\LocationComposer::class);
    }
}
