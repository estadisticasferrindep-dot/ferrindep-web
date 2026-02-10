<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // <-- agregado
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;


use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Configuracion;
use App\Models\Empresa;
use App\Models\Home;
use App\Models\Imagen;
use App\Models\Red;
use App\Models\Servicio;
use App\Models\Pregunta;
use App\Models\Trabajo;
use App\Models\Galeria;
use App\Models\Video;
use App\Models\Configpedido;
use App\Models\ColorProducto;
use App\Models\Presentacion;
use App\Models\Pedido;
use App\Models\Medida;
use App\Models\Espesor;

use App\Models\Foto;
use App\Models\Familia;
use App\Models\Envio;

use App\Models\Destino;
use App\Models\Zona;
use App\Models\Destinozona;
use App\Models\Pesozona;

use Illuminate\Http\Request;
use App\Mail\ExpertoMailable;
use App\Mail\ContactanosMailable;
use App\Mail\PedidoMailable;

use App\Mail\PostVentaMailable;
use App\Models\Color;
use App\Models\Metadato;
use App\Models\Producto;
use Illuminate\Support\Facades\Mail;

class WebController extends Controller
{
    public function index()
    {
        $breadcrumb = [
            ['title' => 'home', 'link' => 'web.home', 'cat' => ''],
        ];

        $homes = Home::get();
        $home = $homes->first();

        $familia_1 = Familia::where('show', 1)->get()->first();

        $keywords = '';
        $description = '';

        $configuraciones = Configuracion::get();
        $configuracion = $configuraciones->first();

        $metaHome = Metadato::where('seccion', 'home')->first();
        $keywords = optional($metaHome)->keywords ?? '';
        $description = optional($metaHome)->description ?? '';

        $redes = Red::where('show', 1)->orderBy('orden')->get();

        // Categorías generales
        $categorias = Categoria::where('show', 1)->orderBy('orden')->get();

        $productos = Producto::with(['galerias', 'presentaciones'])->where('destacado', 1)->where('show', 1)->orderBy('orden')->get();
        $clientes = Cliente::where('show', 1)->orderBy('orden')->get();
        $imagenes = Imagen::where('show', 1)->where('ubicacion', 'Home')->orderBy('orden')->get();

        $configuracionesPedidos = Configpedido::get();
        $configuracionPedidos = $configuracionesPedidos->first();

        // === NUEVO: DATOS CON FAMILIA INCLUIDA (PARA EL BUSCADOR INTELIGENTE) ===
        // Quitamos el filtro de "familia_id = 1" para traer TODO (Mallas y Metal)
        // Y agregamos "productos.familia_id" al select para que el HTML sepa de quién es cada opción.

        // 1. Anchos (Categorías)
        $anchos = Categoria::join('productos', 'productos.categoria_id', '=', 'categorias.id')
            ->where('productos.show', 1)
            ->where('categorias.show', 1)
            ->select('categorias.*', 'productos.familia_id') // <--- CLAVE: Traemos la familia
            ->distinct()
            ->orderBy('categorias.orden')
            ->get();

        // 2. Medidas
        $medidas = Medida::join('productos', 'productos.medida_id', '=', 'medidas.id')
            ->where('productos.show', 1)
            ->select('medidas.*', 'productos.familia_id') // <--- CLAVE: Traemos la familia
            ->distinct()
            ->orderBy('medidas.medidas')
            ->get();

        // 3. Espesores
        $espesores = Espesor::join('productos', 'productos.espesor_id', '=', 'espesores.id')
            ->where('productos.show', 1)
            ->select('espesores.*', 'productos.familia_id') // <--- CLAVE: Traemos la familia
            ->distinct()
            ->orderBy('espesores.espesor')
            ->get();

        // Retornamos todo a la vista
        return view('web.index', compact(
            'breadcrumb',
            'redes',
            'keywords',
            'description',
            'home',
            'configuracion',
            'familia_1',
            'categorias',
            'clientes',
            'imagenes',
            'productos',
            'configuracionPedidos',
            'anchos',
            'medidas',
            'espesores'
        ));
    }

    public function index_buscar(Request $request)
    {
        $breadcrumb = [
            ['title' => 'home', 'link' => 'web.home', 'cat' => ''],
        ];

        $homes = Home::get();
        $home = $homes->first();

        $familia_1 = Familia::where('show', 1)->get()->first();

        $keywords = '';
        $description = '';

        $configuraciones = Configuracion::get();
        $configuracion = $configuraciones->first();

        $redes = Red::where('show', 1)->orderBy('orden')->get();
        $categorias = Categoria::where('show', 1)->orderBy('orden')->get();
        $productos = Producto::where('destacado', 1)->orderBy('orden')->get();
        $clientes = Cliente::where('show', 1)->orderBy('orden')->get();

        $imagenes = Imagen::where('ubicacion', 'Home')->where('show', 1)->orderBy('orden')->get();
        $marcas = DB::select('SELECT marca FROM `productos` WHERE marca IS NOT NULL GROUP BY marca');
        $modelos = DB::select('SELECT modelo FROM `productos` WHERE modelo IS NOT NULL GROUP BY modelo');
        $codigos = DB::select('SELECT id FROM `productos` WHERE id IS NOT NULL GROUP BY id');

        if ($request->codigo) {
            $productosFiltro = Producto::where('id', $request->codigo)->where('show', 1)->get();
        } else {
            $productosFiltro = Producto::where('marca', 'like', '%' . $request->marca . '%')
                ->where('modelo', 'like', '%' . $request->modelo . '%')
                ->where('show', 1)
                ->get();
        }

        return view('web.index', compact('breadcrumb', 'redes', 'keywords', 'description', 'home', 'configuracion', 'familia_1', 'categorias', 'clientes', 'imagenes', 'productos', 'marcas', 'modelos', 'codigos', 'productosFiltro'));
    }

    public function busqueda(Request $request)
    {
        $breadcrumb = [
            ['title' => 'Resultados', 'link' => 'web.home', 'cat' => ''],
        ];

        $homes = Home::get();
        $home = $homes->first();

        $familia_1 = Familia::where('show', 1)->get()->first();

        $keywords = '';
        $description = '';

        $configuraciones = Configuracion::get();
        $configuracion = $configuraciones->first();

        $redes = Red::where('show', 1)->orderBy('orden')->get();

        $productos = Producto::where('nombre', 'like', '%' . $request->busqueda . '%')->where('show', 1)->get();

        return view('web.busqueda', compact('breadcrumb', 'redes', 'keywords', 'description', 'home', 'configuracion', 'familia_1', 'productos'));
    }

    public function login()
    {
        $breadcrumb = [
            ['title' => 'empresa', 'link' => 'web.empresa', 'cat' => ''],
        ];

        $homes = Home::get();
        $home = $homes->first();

        $familia_1 = Familia::where('show', 1)->get()->first();

        $keywords = '';
        $description = '';

        $configuraciones = Configuracion::get();
        $configuracion = $configuraciones->first();

        $redes = Red::where('show', 1)->orderBy('orden')->get();

        return view('web.login', compact('breadcrumb', 'redes', 'keywords', 'description', 'home', 'configuracion', 'familia_1'));
    }

    public function empresa()
    {
        $breadcrumb = [
            ['title' => 'empresa', 'link' => 'web.empresa', 'cat' => ''],
        ];

        $homes = Home::get();
        $home = $homes->first();

        $familia_1 = Familia::where('show', 1)->get()->first();

        $keywords = '';
        $description = '';

        $configuraciones = Configuracion::get();
        $configuracion = $configuraciones->first();

        $metaEmpresa = Metadato::where('seccion', 'empresa')->first();
        $keywords = optional($metaEmpresa)->keywords ?? '';
        $description = optional($metaEmpresa)->description ?? '';

        $redes = Red::where('show', 1)->orderBy('orden')->get();

        $empresas = Empresa::get();
        $empresa = $empresas->first();

        $imagenes = Imagen::where('ubicacion', 'Empresa')->where('show', 1)->orderBy('orden')->get();

        return view('web.empresa', compact('breadcrumb', 'redes', 'keywords', 'description', 'home', 'empresa', 'configuracion', 'imagenes'));
    }

    public function carrito()
    {
        $breadcrumb = [
            ['title' => 'orden de compra', 'link' => 'web.carrito', 'cat' => ''],
        ];

        $homes = Home::get();
        $home = $homes->first();

        $familia_1 = Familia::where('show', 1)->get()->first();

        $familia_id = Familia::where('show', 1)->get()->first();

        $keywords = '';
        $description = '';

        $configuraciones = Configuracion::get();
        $configuracion = $configuraciones->first();

        $configpedidos = Configpedido::get();
        $configpedido = $configpedidos->first();

        $redes = Red::where('show', 1)->orderBy('orden')->get();

        $envios = Envio::get();

        $destinos = Destino::get();
        $zonas = Zona::get();
        $destinozonas = Destinozona::get();
        $pesozonas = Pesozona::get();

        // FIX: Inject Cart Data for Blade
        $cartService = app(\App\Services\CartSessionService::class);
        $sessionCartItems = $cartService->getCart();

        return view('web.carrito', compact('breadcrumb', 'redes', 'keywords', 'description', 'home', 'configuracion', 'familia_1', 'configpedido', 'envios', 'pesozonas', 'destinozonas', 'zonas', 'destinos', 'sessionCartItems'));
    }

    public function carritoData()
    {
        return [
            'destinos' => Destino::get(),
            'zonas' => Zona::get(),
            // 'destinozona' => Destinozona::get(),
            'pesozonas' => Pesozona::orderBy('peso', 'desc')->get()
        ];
    }

    public function fin()
    {
        $breadcrumb = [
            ['title' => 'orden de compra', 'link' => 'web.carrito', 'cat' => ''],
        ];

        $homes = Home::get();
        $home = $homes->first();

        $familia_1 = Familia::where('show', 1)->get()->first();

        $keywords = '';
        $description = '';

        $configuraciones = Configuracion::get();
        $configuracion = $configuraciones->first();

        $configpedidos = Configpedido::get();
        $configpedido = $configpedidos->first();

        $redes = Red::where('show', 1)->orderBy('orden')->get();

        return view('web.finalizar_compra', compact('breadcrumb', 'redes', 'keywords', 'description', 'home', 'configuracion', 'familia_1', 'configpedido'));
    }

    public function finalizar_compra(Request $request)
    {
        return ['status' => 'success', 'redirect' => url('/') . '/fin?total=' . $request->get('total') . '&envio=' . $request->envio];
        /*
        ... (comentado por ustedes)
        */
    }

    public function trabajos()
    {
        $breadcrumb = [
            ['title' => 'trabajos realizados', 'link' => 'web.trabajos.trabajos', 'cat' => ''],
        ];

        $homes = Home::get();
        $home = $homes->first();

        $familia_1 = Familia::where('show', 1)->get()->first();

        $keywords = '';
        $description = '';

        $configuraciones = Configuracion::get();
        $configuracion = $configuraciones->first();

        $redes = Red::where('show', 1)->orderBy('orden')->get();

        $trabajos = Trabajo::where('show', 1)->orderBy('orden')->get();

        return view('web.trabajos.trabajos', compact('breadcrumb', 'redes', 'keywords', 'description', 'home', 'configuracion', 'familia_1', 'trabajos'));
    }

    public function trabajos_trabajo(Trabajo $trabajo)
    {
        $homes = Home::get();
        $home = $homes->first();

        $familia_1 = Familia::where('show', 1)->get()->first();

        $keywords = '';
        $description = '';

        $configuraciones = Configuracion::get();
        $configuracion = $configuraciones->first();

        $redes = Red::where('show', 1)->orderBy('orden')->get();

        $trabajos = Trabajo::where('show', 1)->orderBy('orden')->get();

        $relacionado_1 = Trabajo::find($trabajo->relacionado_1);
        $relacionado_2 = Trabajo::find($trabajo->relacionado_2);
        $relacionado_3 = Trabajo::find($trabajo->relacionado_3);

        $galerias = Galeria::where('trabajo_id', $trabajo->id)->orderBy('orden')->get();

        $breadcrumb = [
            ['title' => 'trabajos realizados ', 'link' => 'web.trabajos.trabajos', 'cat' => ''],
            ['title' => $trabajo->nombre, 'link' => 'web.trabajos.trabajo', 'cat' => $trabajo->id],
        ];

        return view('web.trabajos.trabajo', compact('breadcrumb', 'redes', 'keywords', 'description', 'home', 'configuracion', 'familia_1', 'trabajo', 'trabajos', 'galerias', 'relacionado_1', 'relacionado_2', 'relacionado_3'));
    }

    public function productos(Familia $familia)
    {
        $breadcrumb = [
            ['title' => 'productos', 'link' => 'web.productos.productos', 'cat' => ''],
        ];

        $homes = Home::get();
        $home = $homes->first();

        $familia_1 = Familia::where('show', 1)->get()->first();

        $familia_id = $familia->id;

        $keywords = '';
        $description = '';

        $configuraciones = Configuracion::get();
        $configuracion = $configuraciones->first();

        $redes = Red::where('show', 1)->orderBy('orden')->get();

        $categorias = Categoria::where('show', 1)->orderBy('orden')->get();
        $familias = Familia::where('show', 1)->orderBy('orden')->get();
        $productos = Producto::where('show', 1)->orderBy('orden')->get();

        $configuracionesPedidos = Configpedido::get();
        $configuracionPedidos = $configuracionesPedidos->first();

        $zonas = \App\Models\Zona::all();
        $destinos = \App\Models\Destino::all();
        $destinozonas = \App\Models\Destinozona::all();

        // ── Flex Zone Resolution for Listing Cards ──
        // Resolve once per page load so every card can show the correct price
        $flexZonePrice = 0;
        try {
            $ubicacionCliente = null;

            // 1. Session (priority)
            if (session()->has('gps_location')) {
                $gps = session('gps_location');
                $ubicacionCliente = (object) [
                    'cityName'   => $gps['cityName'] ?? null,
                    'regionName' => $gps['regionName'] ?? null,
                    'partido'    => $gps['partido'] ?? null,
                ];
            }

            // 2. IP fallback
            if (!$ubicacionCliente) {
                $ip = request()->ip();
                if ($ip != '127.0.0.1' && $ip != '::1') {
                    $resp = Http::timeout(1)->get("http://ip-api.com/json/{$ip}");
                    if ($resp->successful()) {
                        $d = $resp->json();
                        if (($d['status'] ?? '') === 'success') {
                            $ubicacionCliente = (object) [
                                'cityName'   => $d['city'],
                                'regionName' => $d['regionName'],
                                'partido'    => null,
                            ];
                        }
                    }
                }
            }

            if ($ubicacionCliente) {
                $normalize = function ($str) {
                    $str = mb_strtolower($str, 'UTF-8');
                    return trim(str_replace(['á','é','í','ó','ú','ñ','ü'], ['a','e','i','o','u','n','u'], $str));
                };

                $region = $normalize($ubicacionCliente->regionName ?? '');
                $isAMBA = (
                    strpos($region, 'buenos aires') !== false ||
                    strpos($region, 'capital federal') !== false ||
                    strpos($region, 'autonoma') !== false
                );

                if ($isAMBA) {
                    $normCity    = $normalize($ubicacionCliente->cityName ?? '');
                    $normPartido = $normalize($ubicacionCliente->partido ?? '');
                    $normRegion  = $normalize($ubicacionCliente->regionName ?? '');
                    $foundFlex   = null;

                    // City match
                    if ($normCity) {
                        $foundFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', $normCity)->first();
                        if (!$foundFlex) $foundFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', '%' . $normCity . '%')->first();
                    }
                    // Partido fallback
                    if (!$foundFlex && $normPartido) {
                        $foundFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', $normPartido)->first();
                        if (!$foundFlex) $foundFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', '%' . $normPartido . '%')->first();
                    }
                    // CABA fallback
                    if (!$foundFlex && (strpos($normRegion, 'ciudad autonoma') !== false || strpos($normRegion, 'capital federal') !== false)) {
                        $foundFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', '%capital federal%')->first();
                        if (!$foundFlex) $foundFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', '%caba%')->first();
                    }

                    if ($foundFlex) {
                        $tarifa = \App\Models\TarifaLogistica::find($foundFlex->tarifa_id);
                        if ($tarifa) {
                            $flexZonePrice = $tarifa->monto;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silencio – no romper la página
        }

        return view('web.productos.productos2', compact('breadcrumb', 'redes', 'keywords', 'description', 'home', 'configuracion', 'familia_1', 'categorias', 'productos', 'familias', 'configuracionPedidos', 'familia_id', 'zonas', 'destinos', 'destinozonas', 'flexZonePrice'));
    }

    public function productos_categoria(Categoria $categoria, Familia $familia)
    {
        $familias = Familia::where('show', 1)->orderBy('orden')->get();

        $familiaElegida = $familia->id;

        $breadcrumb = [
            ['title' => $categoria->nombre, 'link' => 'web.productos.categoria', 'cat' => $categoria->id],
        ];

        $homes = Home::get();
        $home = $homes->first();

        $familia_1 = Familia::where('show', 1)->get()->first();

        $keywords = '';
        $description = '';

        $configuraciones = Configuracion::get();
        $configuracion = $configuraciones->first();

        $redes = Red::where('show', 1)->orderBy('orden')->get();

        $categorias = Categoria::where('show', 1)->orderBy('orden')->get();
        $productos = Producto::where('categoria_id', $categoria->id)->orderBy('orden')->get();

        $categoria_id = $categoria->id;

        $configuracionesPedidos = Configpedido::get();
        $configuracionPedidos = $configuracionesPedidos->first();

        $zonas = \App\Models\Zona::all();
        $destinos = \App\Models\Destino::all();
        $destinozonas = \App\Models\Destinozona::all();

        return view('web.productos.categoria', compact('breadcrumb', 'redes', 'keywords', 'description', 'home', 'configuracion', 'familia_1', 'categorias', 'productos', 'categoria_id', 'categoria', 'configuracionPedidos', 'familias', 'familiaElegida', 'zonas', 'destinos', 'destinozonas'));
    }

    public function productos_producto($id)
    {
        $producto = Producto::find($id);

        $producto = Producto::where('id', $id)->where('show', 1)->first();

        if (!$producto) {
            abort(404);
        }

        // ======================================================
        // LOGICA DE ENVIO (Copy of LocationComposer Logic)
        // ======================================================
        $locationName = null;
        $zonaId = null;
        $realZonaId = null;
        $shippingRates = [];
        $ubicacionCliente = null;

        try {

            // 1. Intentar obtener desde Session (Prioridad 1)
            if (session()->has('gps_location')) {
                $gps = session('gps_location');
                // Reconstruir objeto simple para la vista
                $ubicacionCliente = new \stdClass();
                $ubicacionCliente->id = $gps['destino_id'] ?? null;
                $ubicacionCliente->cityName = $gps['cityName'] ?? null;
                $ubicacionCliente->regionName = $gps['regionName'] ?? null;
                $ubicacionCliente->partido = $gps['partido'] ?? null;
            }

            // 2. Si no hay session, intentar IP (Solo para rellenar, la session es mejor)
            if (!$ubicacionCliente) {
                $ip = request()->ip();
                if ($ip != '127.0.0.1' && $ip != '::1') {
                    $response = Http::timeout(1)->get("http://ip-api.com/json/{$ip}");
                    if ($response->successful()) {
                        $data = $response->json();
                        if (($data['status'] ?? '') === 'success') {
                            $ubicacionCliente = new \stdClass();
                            $ubicacionCliente->cityName = $data['city'];
                            $ubicacionCliente->regionName = $data['regionName'];
                            // IP no da Partido
                        }
                    }
                }
            }

            if ($ubicacionCliente) {
                $locationName = $ubicacionCliente->cityName ?? $ubicacionCliente->regionName;

                // A) Normalizar Texto
                $normalize = function ($str) {
                    $str = mb_strtolower($str, 'UTF-8');
                    return trim(str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'], ['a', 'e', 'i', 'o', 'u', 'n', 'u'], $str));
                };

                // B) Buscar en MapeoZonaFlex (Lógica Robusta v2 - Sincronizada con ShippingCalculator)
                // GUARD: Solo buscar Flex si es Buenos Aires o CABA (Evitar falsos positivos como San Luis)
                $region = $normalize($ubicacionCliente->regionName ?? '');
                $isAMBA = (
                    strpos($region, 'buenos aires') !== false ||
                    strpos($region, 'capital federal') !== false ||
                    strpos($region, 'autonoma') !== false
                );

                if ($locationName && $isAMBA) {
                    $normCity = $normalize($ubicacionCliente->cityName ?? '');
                    $normPartido = $normalize($ubicacionCliente->partido ?? '');
                    $normRegion = $normalize($ubicacionCliente->regionName ?? '');

                    // --- NUEVA LÓGICA ROBUSTA (Sincronizada con ShippingCalculator) ---

                    // 1. Match City
                    if ($normCity) {
                        // A) Exacto
                        $foundFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', $normCity)->first();
                        // B) Like (Sin wildcard, ej: "San Miguel" match "sanity miguel" NO, pero case insensitive DB)
                        if (!$foundFlex) {
                            $foundFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', $normCity)->first();
                        }
                        // C) Wildcard (Solo si es muy específico o falla lo anterior - WebController Original)
                        if (!$foundFlex) {
                            $foundFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', '%' . $normCity . '%')->first();
                        }
                    }

                    // 2. Fallback a Partido
                    if (!$foundFlex && $normPartido) {
                        // A) Exacto
                        $foundFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', $normPartido)->first();
                        // B) Like
                        if (!$foundFlex) {
                            $foundFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', $normPartido)->first();
                        }
                        // C) Wildcard
                        if (!$foundFlex) {
                            $foundFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', '%' . $normPartido . '%')->first();
                        }
                    }

                    // 3. Casos Especiales (Martin)
                    if (!$foundFlex && $normPartido && strpos($normPartido, 'martin') !== false) {
                        $foundFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', '%san martin%')->first();
                    }

                    // 4. Fallback CABA Explícito (Si la detección dice CABA/Capital pero no matcheo en Flex DB)
                    if (!$foundFlex && (strpos($normRegion, 'ciudad autonoma') !== false || strpos($normRegion, 'capital federal') !== false)) {
                        // Buscar "Capital Federal" o "CABA" en Flex DB, o forzar Zona 1 si no existe.
                        $foundFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', '%capital federal%')->first();
                        if (!$foundFlex) {
                            $foundFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', '%caba%')->first();
                        }
                    }
                } elseif (!$isAMBA && $locationName) {
                    // Debug (Opcional): Si no es AMBA, asegurar que no haya match Flex
                    $foundFlex = null;
                }


                if ($foundFlex) {
                    // Es una zona Flex (1, 2, 3, 4)
                    $realZonaId = $foundFlex->tarifa_id;
                    $zonaId = -1; // Flag para Vue

                    $tarifas = \App\Models\TarifaLogistica::find($realZonaId);
                    if ($tarifas) {
                        $costo = $tarifas->monto;
                        $shippingRates = [
                            ['peso' => 999999, 'costo' => $costo] // Flat rate logic
                        ];
                    }
                } elseif ($isAMBA) {
                    // STRICT MODE: If in AMBA/Buenos Aires but NOT in Flex List -> DO NOT FALLBACK.
                    // This prevents Mar del Plata / Bahia Blanca from grabbing legacy Zone 4 prices.
                    $shippingRates = [];
                    $zonaId = null;
                } else {
                    // --- 2. LOGICA LEGACY (PESOZONA) - Only for Interior ---
                    // Necesitamos ID de destino. Si viene de IP no tiene ID, hay que buscarlo.
                    $destinoId = $ubicacionCliente->id ?? null;

                    if (!$destinoId && !empty($ubicacionCliente->cityName)) {
                        // Intentar matchear ciudad con MapeoUbicacion
                        $mapeo = \App\Models\MapeoUbicacion::where('ciudad_detectada', $ubicacionCliente->cityName)->first();
                        if ($mapeo)
                            $destinoId = $mapeo->destino_id;
                    }

                    if ($destinoId) {
                        $destinoZona = \App\Models\Destinozona::where('destino_id', $destinoId)->first();
                        if ($destinoZona) {
                            $zonaId = $destinoZona->zona_id;
                            // Obtener TODAS las tarifas para esta zona
                            $shippingRates = \App\Models\Pesozona::where('zona_id', $zonaId)
                                ->orderBy('peso', 'asc')
                                ->get()
                                ->map(function ($rate) {
                                    return [
                                        'peso' => $rate->peso,
                                        'costo' => $rate->costo
                                    ];
                                });
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            // Silencio
        }


        $homes = Home::get();
        $home = $homes->first();

        $familia_1 = Familia::where('show', 1)->get()->first();

        $keywords = '';
        $description = '';

        $familias = Familia::where('show', 1)->orderBy('orden')->get();

        $familiaElegida = $producto->familia_id;

        $configuraciones = Configuracion::get();
        $configuracion = $configuraciones->first();

        $redes = Red::where('show', 1)->orderBy('orden')->get();

        $categoria = Categoria::find($producto->categoria_id);
        $categoria_id = $categoria->id;

        $colores = ColorProducto::with('color')->where('producto_id', $producto->id)->get();
        $categorias = Categoria::where('show', 1)->orderBy('orden')->get();
        $productos = Producto::where('categoria_id', $categoria->id)->orderBy('orden')->get();

        $relacionado_1 = Producto::find($producto->relacionado_1);
        $relacionado_2 = Producto::find($producto->relacionado_2);
        $relacionado_3 = Producto::find($producto->relacionado_3);

        $configuracionesPedidos = Configpedido::get();
        $configuracionPedidos = $configuracionesPedidos->first();

        $breadcrumb = [
            ['title' => 'productos', 'link' => 'web.productos.productos', 'cat' => ''],
            ['title' => $categoria->nombre, 'link' => 'web.productos.categoria', 'cat' => $categoria->id],
            ['title' => $producto->nombre, 'link' => 'web.productos.producto', 'cat' => $producto->id],
        ];

        $principalUrl = $producto->imagen ? asset('storage/' . $producto->imagen) : asset('img/no-image.png');
        // Corrected relationship usage: galerias (plural)
        $galeriaUrls = $producto->galerias->map(function ($img) {
            return asset('storage/' . $img->imagen);
        });

        $zonas = \App\Models\Zona::all();
        $destinos = \App\Models\Destino::all();
        $destinozonas = \App\Models\Destinozona::all();

        return view('web.productos.producto', compact('breadcrumb', 'redes', 'keywords', 'description', 'home', 'configuracion', 'familia_1', 'categoria', 'producto', 'categorias', 'productos', 'categoria_id', 'relacionado_1', 'relacionado_2', 'relacionado_3', 'colores', 'familias', 'familiaElegida', 'principalUrl', 'galeriaUrls', 'locationName', 'zonaId', 'realZonaId', 'shippingRates', 'ubicacionCliente', 'zonas', 'destinos', 'destinozonas'));
    }

    public function contactanos_experto(Request $request, $producto)
    {
        $correo = new ExpertoMailable($request->all());
        Mail::to('juulimarti@gmail.com')->send($correo);
        return redirect()->route('web.productos.producto', $producto)->with('info', 'Mensaje enviado');
    }

    public function clientes()
    {

        $breadcrumb = [
            ['title' => 'clientes', 'link' => 'web.clientes', 'cat' => ''],
        ];

        $homes = Home::get();
        $home = $homes->first();

        $familia_1 = Familia::where('show', 1)->get()->first();

        $keywords = '';
        $description = '';

        $configuraciones = Configuracion::get();
        $configuracion = $configuraciones->first();

        $redes = Red::where('show', 1)->orderBy('orden')->get();

        $clientes = Cliente::orderBy('orden')->get();

        return view('web.clientes', compact('breadcrumb', 'redes', 'keywords', 'description', 'home', 'configuracion', 'familia_1', 'clientes'));
    }

    public function videos()
    {
        $breadcrumb = [
            ['title' => 'videos', 'link' => 'web.videos', 'cat' => ''],
        ];

        $homes = Home::get();
        $home = $homes->first();

        $familia_1 = Familia::where('show', 1)->get()->first();

        $keywords = '';
        $description = '';

        $configuraciones = Configuracion::get();
        $configuracion = $configuraciones->first();
        $metaVideos = Metadato::where('seccion', 'videos')->first();
        $keywords = optional($metaVideos)->keywords ?? '';
        $description = optional($metaVideos)->description ?? '';

        $redes = Red::where('show', 1)->orderBy('orden')->get();

        $videos = Video::orderBy('orden')->get();

        return view('web.videos', compact('breadcrumb', 'redes', 'keywords', 'description', 'home', 'configuracion', 'familia_1', 'keywords', 'description', 'videos'));
    }

    public function servicios()
    {
        $breadcrumb = [
            ['title' => 'servicios', 'link' => 'web.servicios', 'cat' => ''],
        ];

        $homes = Home::get();
        $home = $homes->first();

        $familia_1 = Familia::where('show', 1)->get()->first();

        $keywords = '';
        $description = '';

        $configuraciones = Configuracion::get();
        $configuracion = $configuraciones->first();

        $redes = Red::where('show', 1)->orderBy('orden')->get();

        $servicios = Servicio::orderBy('orden')->get();

        return view('web.servicios', compact('breadcrumb', 'redes', 'keywords', 'description', 'home', 'configuracion', 'familia_1', 'servicios'));
    }

    public function preguntas_frecuentes()
    {
        $breadcrumb = [
            ['title' => 'preguntas frecuentes', 'link' => 'web.preguntas_frecuentes', 'cat' => ''],
        ];

        $homes = Home::get();
        $home = $homes->first();

        $familia_1 = Familia::where('show', 1)->get()->first();

        $keywords = '';
        $description = '';

        $configuraciones = Configuracion::get();
        $configuracion = $configuraciones->first();

        $redes = Red::where('show', 1)->orderBy('orden')->get();

        $preguntas = Pregunta::orderBy('orden')->get();

        return view('web.preguntas_frecuentes', compact('breadcrumb', 'redes', 'keywords', 'description', 'home', 'configuracion', 'familia_1', 'preguntas'));
    }

    public function solicitud_de_presupuesto()
    {
        $breadcrumb = [
            ['title' => 'presupuesto', 'link' => 'web.solicitud_de_presupuesto', 'cat' => ''],
        ];

        $homes = Home::get();
        $home = $homes->first();

        $familia_1 = Familia::where('show', 1)->get()->first();

        $keywords = '';
        $description = '';

        $configuraciones = Configuracion::get();
        $configuracion = $configuraciones->first();

        $redes = Red::where('show', 1)->orderBy('orden')->get();

        return view('web.solicitud_de_presupuesto', compact('breadcrumb', 'redes', 'keywords', 'description', 'home', 'configuracion', 'familia_1'));
    }

    public function galeria()
    {
        $breadcrumb = [
            ['title' => 'galeria', 'link' => 'web.galeria', 'cat' => ''],
        ];

        $homes = Home::get();
        $home = $homes->first();

        $familia_1 = Familia::where('show', 1)->get()->first();

        $keywords = '';
        $description = '';

        $configuraciones = Configuracion::get();
        $configuracion = $configuraciones->first();

        $redes = Red::where('show', 1)->orderBy('orden')->get();

        $fotos = Foto::where('show', 1)->orderBy('orden')->get();

        return view('web.galeria', compact('breadcrumb', 'redes', 'keywords', 'description', 'home', 'configuracion', 'familia_1', 'fotos'));
    }

    public function contacto()
    {
        $breadcrumb = [
            ['title' => 'contacto', 'link' => 'web.contacto', 'cat' => ''],
        ];

        $homes = Home::get();
        $home = $homes->first();

        $familia_1 = Familia::where('show', 1)->get()->first();

        $keywords = '';
        $description = '';

        $configuraciones = Configuracion::get();
        $configuracion = $configuraciones->first();
        $metaContacto = Metadato::where('seccion', 'contacto')->first();
        $keywords = optional($metaContacto)->keywords ?? '';
        $description = optional($metaContacto)->description ?? '';

        $redes = Red::where('show', 1)->orderBy('orden')->get();

        return view('web.contacto', compact('breadcrumb', 'redes', 'keywords', 'description', 'home', 'configuracion', 'familia_1', 'keywords', 'description'));
    }

    public function contactanos(Request $request)
    {
        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo')->store('mails');
            $correo = new ContactanosMailable($request->all(), $archivo);
        } else {
            $correo = new ContactanosMailable($request->all());
        }

        $configuraciones = Configuracion::get();
        $configuracion = $configuraciones->first();

        Mail::to($configuracion->email)->send($correo);

        return redirect()->route('web.contacto')->with('info', 'Mensaje enviado');
    }

    public function success($pedido_id)
    {
        $breadcrumb = [
            ['title' => 'contacto', 'link' => 'web.contacto', 'cat' => ''],
        ];
        $homes = Home::get();
        $home = $homes->first();

        $familia_1 = Familia::where('show', 1)->get()->first();

        $keywords = '';
        $description = '';

        $configuraciones = Configuracion::get();
        $configuracion = $configuraciones->first();

        $redes = Red::where('show', 1)->orderBy('orden')->get();

        // --- CORRECCIÓN: BUSCAR EL PEDIDO REAL ---
        $pedido = Pedido::find($pedido_id);

        // Si no existe, evitamos el error redirigiendo
        if (!$pedido) {
            return redirect()->route('web.home');
        }

        $configpedidos = Configpedido::get();
        $configpedido = $configpedidos->first();

        if ($pedido->pago == 'mp') {
            $pedido->esta_pago = 1;
            $pedido->save();
        }

        // Datos para el mail (legacy)
        $pedido->mail_efectivo = $configpedido->mail_efectivo;
        $pedido->mail_mp = $configpedido->mail_mp;
        $pedido->mail_transferencia = $configpedido->mail_transferencia;
        $pedido->mail_registro = $configpedido->mail_registro;
        $pedido->mail_envio = $configpedido->mail_envio;
        $pedido->mail_fabrica = $configpedido->mail_fabrica;

        $pedido->descFabrica = $configpedido->parrafo_envio_fabrica;
        $pedido->descCaba = $configpedido->parrafo_envio_caba;

        $cart = $pedido->itemsPedidos;

        // Envío de Emails (ENVUELTO EN TRY/CATCH PARA SEGURIDAD)
        try {
            $correo = new PedidoMailable($cart, $pedido);

            if ($configpedido->email1)
                Mail::to($configpedido->email1)->send($correo);
            if ($configpedido->email2)
                Mail::to($configpedido->email2)->send($correo);
            if ($configpedido->email3)
                Mail::to($configpedido->email3)->send($correo);

            Mail::to($pedido->usuario_email)->send($correo);
        } catch (\Exception $e) {
            // Si falla el mail, seguimos igual para mostrar la pantalla de éxito
        }

        // Descuento de stock
        foreach ($cart as $key => $value) {
            $presentacion = Presentacion::find($value->presentacion_id);
            if ($presentacion) {
                $presentacion->stock = $presentacion->stock - $value->cantidad;
                $presentacion->save();
            }
        }

        // --- CORRECCIÓN FINAL: PASAR LA VARIABLE $pedido A LA VISTA ---
        return view('web.success', compact('breadcrumb', 'redes', 'keywords', 'description', 'home', 'configuracion', 'familia_1', 'pedido'));
    }

    public function failure()
    {
        $breadcrumb = [
            ['title' => 'home', 'link' => 'web.home', 'cat' => ''],
        ];

        $homes = Home::get();
        $home = $homes->first();

        $familia_1 = Familia::where('show', 1)->get()->first();

        $keywords = '';
        $description = '';

        $configuraciones = Configuracion::get();
        $configuracion = $configuraciones->first();

        $keywords = Metadato::where('seccion', 'home')->get()->first()->keywords;
        $description = Metadato::where('seccion', 'home')->get()->first()->description;

        $redes = Red::where('show', 1)->orderBy('orden')->get();
        $categorias = Categoria::where('show', 1)->orderBy('orden')->get();
        $productos = Producto::where('destacado', 1)->orderBy('orden')->get();
        $clientes = Cliente::where('show', 1)->orderBy('orden')->get();
        $imagenes = Imagen::where('show', 1)->where('ubicacion', 'Home')->orderBy('orden')->get();

        return view('web.index', compact('breadcrumb', 'redes', 'keywords', 'description', 'home', 'configuracion', 'familia_1', 'keywords', 'description', 'categorias', 'clientes', 'imagenes', 'productos'));
    }

    public function pending()
    {
        $breadcrumb = [
            ['title' => 'contacto', 'link' => 'web.contacto', 'cat' => ''],
        ];
        $homes = Home::get();
        $home = $homes->first();

        $familia_1 = Familia::where('show', 1)->get()->first();

        $keywords = '';
        $description = '';
        $primerCategoria = null;

        $configuraciones = Configuracion::get();
        $configuracion = $configuraciones->first();

        $redes = Red::where('show', 1)->orderBy('orden')->get();

        return view('web.pending', compact('breadcrumb', 'redes', 'keywords', 'description', 'home', 'configuracion', 'familia_1', 'primerCategoria'));
    }

    public function enviar_mails(Request $request)
    {
        $correo = new PedidoMailable($request->all());
        Mail::to('estadisticasferrindep@gmail.com')->send($correo);
    }

    /* ===========================
     *  NUEVO: buscador por medidas
     * =========================== */

    // Normaliza textos como "30cm x 16 mts (rollo)" para que coincidan aunque var¨ªen espacios/acentos
    private function norm(string $s): string
    {
        $s = Str::of($s)->ascii()->lower();

        // unificar unidades y quitar "ruidos"
        $s = $s->replace(['centimetros', 'centímetros', 'centimetro', 'centímetro'], 'cm');
        $s = $s->replace(['metros', 'metro'], 'm');
        $s = $s->replace([' mts', ' mt', 'mts', 'mt', ' mtrs', ' mtr'], ' m');
        $s = $s->replace(['(rollo)', 'rollo', '.', ' ,', ','], ' ');

        // unificar "x" y espacios
        $s = $s->replace([' ¡Á ', ' x ', '¡Á', '×'], 'x');

        // espacios en blanco a 1 solo
        $s = Str::of(preg_replace('/\s+/', ' ', (string) $s))->trim();
        return (string) $s;
    }

    // /buscar?q=...
    public function buscar(Request $req)
    {
        $q = trim((string) $req->get('q', ''));
        $resultados = collect();

        if ($q !== '') {
            // hacemos el LIKE m¨¢s permisivo
            $like = '%' . str_replace(' ', '%', $q) . '%';

            // <<<--- FILTRAMOS (Quitamos restricción de familia 1 para buscar en todo)
            $rows = DB::table('presentaciones as p')
                ->join('productos as pr', 'pr.id', '=', 'p.producto_id')
                ->leftJoin('categorias as c', 'c.id', '=', 'pr.categoria_id')
                ->leftJoin('medidas as m', 'm.id', '=', 'pr.medida_id')
                ->leftJoin('espesores as e', 'e.id', '=', 'pr.espesor_id')
                ->where('pr.show', 1) // <-- Agregamos seguridad: solo activos
                ->where(function ($w) use ($like) {
                    $w->where('p.medidas', 'LIKE', $like)
                        ->orWhere('p.nombre', 'LIKE', $like)
                        ->orWhere('pr.nombre', 'LIKE', $like)
                        ->orWhere('c.nombre', 'LIKE', $like)
                        ->orWhere('m.medidas', 'LIKE', $like)
                        ->orWhere('e.espesor', 'LIKE', $like);
                })
                ->select([
                    'p.id',
                    'p.producto_id',
                    'p.medidas',
                    'p.nombre as nombre_variante',
                    'p.precio',
                    'p.stock',
                    'pr.nombre as producto_nombre',
                    'c.nombre as categoria_nombre',
                    'm.medidas as medidas_producto',
                    'e.espesor'
                ])
                ->orderBy('pr.nombre')
                ->limit(400)
                ->get();

            $qn = $this->norm($q);
            $qnC = str_replace(' ', '', $qn); // versi¨®n sin espacios

            $resultados = collect($rows)->filter(function ($r) use ($qn, $qnC) {
                $hay = $this->norm(
                    ($r->producto_nombre ?? '') . ' ' .
                    ($r->categoria_nombre ?? '') . ' ' .
                    ($r->medidas_producto ?? '') . ' ' .
                    ($r->espesor ?? '') . ' ' .
                    ($r->medidas ?? '') . ' ' .
                    ($r->nombre_variante ?? '')
                );
                $hayC = str_replace(' ', '', $hay);

                // match directo normalizado
                if (Str::contains($hay, $qn) || Str::contains($hayC, $qnC))
                    return true;

                // fallback: que TODOS los n¨²meros del query est¨¦n presentes (ej: "30 20", "10 10 30")
                preg_match_all('/\d+/', $qnC, $m);
                $nums = $m[0] ?? [];
                if (empty($nums))
                    return false;
                foreach ($nums as $n) {
                    if (!Str::contains($hayC, $n))
                        return false;
                }
                return true;
            })->values();
        }

        return view('web.buscar', ['q' => $q, 'resultados' => $resultados]);
    }


    // /api/sugerencias?q=...  (opcional para autocompletar)
    public function sugerencias(Request $req)
    {
        $q = trim($req->get('q', ''));
        if ($q === '')
            return response()->json([]);
        $like = '%' . $q . '%';
        $sugs = DB::table('presentaciones')
            ->select('medidas')->where('medidas', 'LIKE', $like)
            ->groupBy('medidas')
            ->orderByRaw('LENGTH(medidas) ASC')
            ->limit(8)
            ->pluck('medidas');
        return response()->json($sugs);
    }
    /* ==========================================
       EL CHISMOSO: Guardar historial de búsqueda
       ========================================== */
    public function guardarBusqueda(Request $request)
    {
        try {
            // Insertamos directamente en la tabla que creaste en phpMyAdmin
            \DB::table('historial_busquedas')->insert([
                'familia' => $request->input('familia'), // '1' (Malla) o '2' (Metal)
                'ancho' => $request->input('ancho_texto'), // Ej: "1 m"
                'medida' => $request->input('medida_texto'), // Ej: "10 x 10"
                'espesor' => $request->input('espesor_texto'), // Ej: "2 mm"
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            // Si falla, no rompemos nada, solo lo ignoramos
            return response()->json(['status' => 'error'], 500);
        }
    }
}
