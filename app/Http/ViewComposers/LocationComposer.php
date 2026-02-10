<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use App\Models\Destino;
use App\Models\MapeoUbicacion;

class LocationComposer
{
    public function compose(View $view)
    {
        try {
            $destinoFinal = null;

            // 1. REGLA DE ORO: SIEMPRE PRIORIZAR ID EN SESION
            if (session()->has('gps_location') && !empty(session('gps_location')['destino_id'])) {
                $gps = session('gps_location');
                $destinoFinal = Destino::find($gps['destino_id']);

                if ($destinoFinal) {
                    // Si se encontró, compartimos.
                    // CRITICAL FIX: Asegurar que cityName y regionName existan para la vista
                    $destinoFinal->cityName = $gps['cityName'] ?? $destinoFinal->nombre;
                    $destinoFinal->regionName = $gps['regionName'] ?? 'Argentina';
                    $destinoFinal->partido = $gps['partido'] ?? null; // Add Partido

                    $view->with('ubicacionCliente', $destinoFinal);
                    return;
                }
            }

            // 1.b) Si hay datos manuales en sesión pero SIN ID (caso raro si falló el mapeo en controller)
            if (session()->has('gps_location')) {
                $gps = session('gps_location');
                $destinoFinal = new Destino();
                $destinoFinal->cityName = $gps['cityName'];
                $destinoFinal->regionName = $gps['regionName'];

                // Intento final de mapeo por nombre
                $mapeo = MapeoUbicacion::with('destino')->where('ciudad_detectada', $gps['cityName'])->first();
                if ($mapeo && $mapeo->destino) {
                    $destinoFinal->id = $mapeo->destino->id;
                    $destinoFinal->nombre = $mapeo->destino->nombre;
                }

                $destinoFinal->partido = $gps['partido'] ?? null; // FIX: Ensure partido is passed even without ID

                $view->with('ubicacionCliente', $destinoFinal);
                return;
            }

            // 2. DETECCION POR IP (Solo si no hay sesión)
            $ip = request()->ip();
            $dataLocation = null;

            if ($ip == '127.0.0.1' || $ip == '::1') {
                $dataLocation = (object) ['cityName' => 'Buenos Aires', 'regionName' => 'Distrito Federal'];
            } else {
                try {
                    $response = Http::timeout(2)->get("http://ip-api.com/json/{$ip}");
                    if ($response->successful()) {
                        $data = $response->json();
                        if ($data && ($data['status'] ?? '') === 'success') {
                            $dataLocation = (object) ['cityName' => $data['city'], 'regionName' => $data['regionName']];
                        }
                    }
                } catch (\Exception $e) { /* Silencio */
                }
            }

            if (!$dataLocation) {
                $dataLocation = (object) ['cityName' => 'Consultar Envío', 'regionName' => 'Argentina'];
            }

            // 3. Mapeo de IP a Destino
            if ($dataLocation->cityName !== 'Consultar Envío') {
                $mapeo = MapeoUbicacion::with('destino')->where('ciudad_detectada', $dataLocation->cityName)->first();

                if ($mapeo && $mapeo->destino) {
                    $destinoFinal = $mapeo->destino;
                } else {
                    // Fallbacks - Solo buscar match por nombre de REGION si es muy obvio, 
                    // pero EVITAR forzar ID 11 para todo Buenos Aires.
                    // $destinoFinal = Destino::where('nombre', 'LIKE', '%' . $dataLocation->regionName . '%')->first();
                }
            }

            if (!$destinoFinal) {
                $destinoFinal = new Destino();
            }
            // Adjuntar datos detectados siempre
            $destinoFinal->cityName = $dataLocation->cityName;
            $destinoFinal->regionName = $dataLocation->regionName;

            $view->with('ubicacionCliente', $destinoFinal);

        } catch (\Throwable $e) {
            // Fail safe
            $view->with('ubicacionCliente', null);
        }
    }
}
