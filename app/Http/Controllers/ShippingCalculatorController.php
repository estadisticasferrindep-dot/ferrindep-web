<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destino;
use App\Models\Destinozona;
use App\Models\Pesozona;
use Illuminate\Support\Facades\Http;
use App\Models\Presentacion;
use App\Models\Producto;
use App\Models\Familia;
use App\Models\Categoria;
use App\Models\Color;

class ShippingCalculatorController extends Controller
{
    /**
     * Devuelve lista de destinos para el dropdown.
     */
    public function getDestinations()
    {
        // Intentamos obtener destinos ordenados por nombre y sus zonas
        // Esto nos servirá para debuguear qué zona tiene asignada CABA
        $destinos = Destino::orderBy('nombre')->get();
        // Agregamos info de zona manualmente para no romper la respuesta simple json si el front espera algo específico
        // pero el front solo usa id y nombre.

        $data = $destinos->map(function ($d) {
            $dz = \App\Models\Destinozona::where('destino_id', $d->id)->first();
            return [
                'id' => $d->id,
                'nombre' => $d->nombre . ($dz ? " (Zona {$dz->zona_id})" : " (Sin Zona)"),
                'zona_id' => $dz ? $dz->zona_id : null
            ];
        });

        return response()->json($data);
    }

    /**
     * Calcula el costo de envío.
     */
    public function calculate(Request $request)
    {
        try {
            $request->validate([
                'destino_id' => 'nullable', // Changed from required to nullable
                'presentacion_id' => 'required',
            ]);

            $presentacionId = $request->presentacion_id;
            $presentacion = Presentacion::find($presentacionId);

            if (!$presentacion) {
                return response()->json(['error' => 'Presentación no encontrada.'], 404);
            }

            // --- NUEVA LÓGICA FLEX ---
            // 1. Obtener ubicación del usuario desde la sesión (GPS/Manual)
            $gpsData = session('gps_location');
            $userCity = $gpsData['cityName'] ?? null;
            $userPartido = $gpsData['partido'] ?? null; // Si se capturó en el paso anterior

            \Illuminate\Support\Facades\Log::info("ShippingCalc Debug:", [
                'gps_session' => $gpsData,
                'userCity' => $userCity,
                'userPartido' => $userPartido,
                'request_destino_id' => $request->destino_id
            ]);

            if ($userCity || $userPartido) {
                // Helper simple para normalizar (lowercase + quitar acentos)
                $normalize = function ($str) {
                    if (!$str)
                        return '';
                    $str = mb_strtolower($str, 'UTF-8');
                    $str = str_replace(
                        ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'],
                        ['a', 'e', 'i', 'o', 'u', 'n', 'u'],
                        $str
                    );
                    return trim($str);
                };

                $normCity = $normalize($userCity);
                $normPartido = $normalize($userPartido);

                // 2. Buscar en MapeoZonaFlex 
                // Buscamos exacto o unaccented
                $mapeoFlex = null;

                if ($normCity) {
                    // Intento 1: Match directo
                    $mapeoFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', $normCity)->first();

                    // Intento 2: Búsqueda flexible (LIKE) si falla el exacto
                    if (!$mapeoFlex) {
                        $mapeoFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', $normCity)->first();
                    }
                }

                // Intento 3: Fallback a Partido
                if (!$mapeoFlex && $normPartido) {
                    $mapeoFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', $normPartido)->first();
                    // Intento 4: Partido Flexible
                    if (!$mapeoFlex) {
                        $mapeoFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', $normPartido)->first();
                    }
                }

                // Intento 5: Partido "General San Martin" vs "San Martin"
                if (!$mapeoFlex && $normPartido && strpos($normPartido, 'martin') !== false) {
                    $mapeoFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', '%san martin%')->first();
                }

                // 3. Si encontramos zona Flex
                if ($mapeoFlex) {
                    // Verificar Envío Gratis Flex
                    if ($presentacion->envio_gratis_flex) {
                        $nombreTarifa = $mapeoFlex->tarifa ? $mapeoFlex->tarifa->nombre : 'Sin Tarifa';
                        return response()->json([
                            'costo_envio' => 0,
                            'zona' => "Flex Gratis ({$nombreTarifa})",
                            'peso_calculado' => $presentacion->peso,
                            'tipo' => 'flex_free'
                        ]);
                    }

                    // Cobrar Tarifa Flex Normal
                    $tarifa = $mapeoFlex->tarifa;
                    if ($tarifa) {
                        return response()->json([
                            'costo_envio' => $tarifa->monto,
                            'zona' => "Flex ({$tarifa->nombre})",
                            'peso_calculado' => $presentacion->peso,
                            'tipo' => 'flex_paid'
                        ]);
                    }
                }
            }

            // --- FALLBACK: LÓGICA LEGACY (Interior / Transporte) ---
            // Si no es zona Flex, usamos la lógica antigua de zonas y pesos.

            $destinoId = $request->destino_id;
            // Si destino_id es nulo (caso Smart Cart con sesión pero sin mapeo legacy), 
            // intentamos resolver un destino default "Buenos Aires" o similar para no romper lógica legado
            if (!$destinoId) {
                // Fallback básico para cálculo legado si todo lo demás falló
                // Asumimos Interior Bs As (11) si es BSAS, o Capital (1) si era CABA
                if (isset($gpsData['regionName']) && stripos($gpsData['regionName'], 'Buenos Aires') !== false) {
                    $destinoId = 11; // Interior Buenos Aires (Default legacy)
                }
            }

            $destino = Destino::find($destinoId);
            if (!$destino) {
                // Si falló Flex y falló Legacy Destino, no podemos calcular.
                return response()->json(['error' => 'Destino no válido para envío tradicional.'], 404);
            }

            $destinoZona = Destinozona::where('destino_id', $destinoId)->first();
            if (!$destinoZona) {
                return response()->json(['error' => 'El destino no tiene zona de envío asignada.'], 404);
            }
            $zonaId = $destinoZona->zona_id;

            // Force CABA to Zone 5 (Legacy logic preserved just in case, though CABA is likely Flex now)
            $nombreDestino = strtoupper($destino->nombre);
            if (str_contains($nombreDestino, 'CABA') || str_contains($nombreDestino, 'CAPITAL FEDERAL')) {
                $zonaId = 5;
            }

            $peso = $presentacion->peso;
            if (!$peso || $peso <= 0) {
                return response()->json(['error' => 'El producto no tiene peso registrado.'], 400);
            }

            $precioEnvio = Pesozona::where('zona_id', $zonaId)
                ->where('peso', '>=', $peso)
                ->orderBy('peso', 'asc')
                ->first();

            if (!$precioEnvio) {
                $maximo = Pesozona::where('zona_id', $zonaId)->orderBy('peso', 'desc')->first();
                if ($maximo && $peso > $maximo->peso) {
                    return response()->json([
                        'costo_envio' => $maximo->costo,
                        'zona' => "Zona $zonaId (Tope)",
                        'peso_calculado' => $peso
                    ]);
                }
                return response()->json(['error' => 'No hay tarifa disponible para este peso/zona.'], 404);
            }

            return response()->json([
                'costo_envio' => $precioEnvio->costo,
                'zona' => "Zona $zonaId",
                'peso_calculado' => $peso
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Shipping Calculation Error: " . $e->getMessage());
            return response()->json(['error' => 'Error calculando envío: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Vista de Prueba (Copia de productos_producto)
     */
    public function testView($id)
    {
        // Reutilizamos la lógica de WebController::productos_producto
        // para cargar los datos necesarios de la vista.

        $producto = Producto::where('id', $id)->with('medidas', 'espesor', 'categoria', 'familia', 'presentaciones', 'galerias')->first();
        if (!$producto)
            abort(404);

        $familiaElegida = $producto->familia_id;
        $familias = Familia::orderBy('orden')->get();
        // Cargar categorias y productos para el sidebar (misma logica del controller original)
        $categorias = Categoria::orderBy('orden')->get();
        // Nota: WebController carga productos con 'show' y orden. Hacemos algo similar.
        $productos = Producto::where('show', 1)->orderBy('orden')->get();

        $breadcrumb = [
            ['title' => 'Inicio', 'url' => route('web.home')],
            ['title' => 'Productos', 'url' => route('web.productos.productos2', $familiaElegida)],
            ['title' => $producto->nombre, 'url' => '#']
        ];

        // Variables extra que usa la plantilla base o vista
        // Colores y galerias se pasan a JS en la vista, lo manejamos igual.
        $colores = Color::all();

        // Variables extra que usa la plantilla base o vista
        // Necesitamos Empresa, Redes y Metadatos para que plantilla.blade.php no falle.
        $empresa = \App\Models\Empresa::first();
        $redes = \App\Models\Red::orderBy('orden')->get();

        // AGREGADO: Variables críticas para plantilla.blade.php
        $configuracion = \App\Models\Configuracion::first();
        $home = \App\Models\Home::first();
        $primeraFam = \App\Models\Familia::orderBy('orden')->first();
        $familia_1 = $primeraFam ? $primeraFam->id : 0;

        // Intentamos obtener metadatos genéricos o del producto
        $description = $producto->descripcion ? strip_tags($producto->descripcion) : 'Descripción del producto';
        $keywords = $producto->nombre;

        return view('web.productos.producto_test', compact('producto', 'familias', 'categorias', 'productos', 'familiaElegida', 'breadcrumb', 'colores', 'empresa', 'redes', 'description', 'keywords', 'configuracion', 'home', 'familia_1'));
    }

    /**
     * Obtener ciudad desde GPS (Reverse Geocoding)
     */
    /**
     * Obtener ciudad desde GPS (Reverse Geocoding)
     */
    public function obtenerCiudadGps(Request $request)
    {
        $lat = $request->input('lat');
        $lon = $request->input('lon');

        $manualCity = $request->input('manual_city');
        $manualRegion = $request->input('manual_region');
        $manualPartido = $request->input('manual_partido'); // Capture Partido

        // Logic 0: If manual data provided (e.g. from Google Autocomplete)
        if ($manualCity) {
            $dataToStore = [
                'cityName' => $manualCity,
                'regionName' => $manualRegion ?? 'Buenos Aires',
                'partido' => $manualPartido, // Store Partido in session
                'destino_id' => null, // Placeholder
            ];

            // --- JERRARQUÍA DE MAPEO ---
            // 1. Match Exacto por Ciudad (Normalizando)
            $mapeo = \App\Models\MapeoUbicacion::with('destino')
                ->where('ciudad_detectada', $manualCity)
                ->first();

            // 2. Si no hay match exacto, buscar por Partido (si existe)
            if ((!$mapeo || !$mapeo->destino) && $manualPartido) {
                // Intentamos buscar el partido en la tabla de mapeo (asumiendo que se cargaron partidos como 'ciudad_detectada')
                $mapeoPartido = \App\Models\MapeoUbicacion::with('destino')
                    ->where('ciudad_detectada', $manualPartido) // Ej: "General San Martín"
                    ->first();

                if ($mapeoPartido && $mapeoPartido->destino) {
                    $mapeo = $mapeoPartido;
                    // Opcional: Podríamos sobreescribir cityName para mostrar el partido, pero mejor mantenemos la ciudad específica del usuario visualmente
                }
            }

            if ($mapeo && $mapeo->destino) {
                $dataToStore['destino_id'] = $mapeo->destino->id;
                // Opcional: normalizar nombre con el del destino
                // $dataToStore['cityName'] = $mapeo->destino->cityName ?? $manualCity;
            } else {
                // 3. Fallback Regional (Provincia) si no hay match de Ciudad ni de Partido
                if (stripos($manualRegion ?? '', 'Buenos Aires') !== false) {
                    $d = \App\Models\Destino::find(11); // Interior BsAs
                    if ($d)
                        $dataToStore['destino_id'] = $d->id;
                }
            }

            session(['gps_location' => $dataToStore]);
            session()->save(); // Forzar guardado para persistencia inmediata

            return response()->json([
                'status' => 'success',
                'city' => $manualCity,
                'region' => $manualRegion ?? 'Buenos Aires',
                'partido' => $manualPartido ?? null,
                'mapped_id' => $dataToStore['destino_id'],
                'source' => 'manual',
                'debug_session' => session('gps_location')
            ]);
        }

        if (!$lat || !$lon) {
            return response()->json(['error' => 'Coordenadas inválidas'], 400);
        }

        // 1. Calling Nominatim (OSM)
        // User-Agent is mandatory for Nominatim
        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lon}";

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'LaravelApp/1.0'
            ])->get($url);

            if ($response->failed()) {
                return response()->json(['error' => 'Error al conectar con servicio de mapas'], 500);
            }

            $data = $response->json();
            $addr = $data['address'] ?? [];

            // Try to find the city in order of precision
            $city = $addr['city'] ?? $addr['town'] ?? $addr['village'] ?? $addr['municipality'] ?? null;
            $state = $addr['state'] ?? 'Buenos Aires';
            // Attempt to find partido from address components if possible (Nominatim varies)
            // Usually 'county' or 'municipality' might be partido in Arg
            $partido = $addr['county'] ?? $addr['municipality'] ?? null;

            if (!$city) {
                return response()->json(['error' => 'No se pudo determinar la ciudad'], 404);
            }

            // 2. Set Session for AppServiceProvider
            // This overrides the IP-based detection on next reload
            session([
                'gps_location' => [
                    'cityName' => $city,
                    'regionName' => $state,
                    'partido' => $partido
                ]
            ]);

            // Optional: Check mapping here just for feedback (AppServiceProvider will do the real work)
            $mapeo = \App\Models\MapeoUbicacion::where('ciudad_detectada', $city)->first();

            return response()->json([
                'status' => 'success',
                'city' => $city,
                'region' => $state,
                'partido' => $partido,
                'found_mapping' => $mapeo ? true : false
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
