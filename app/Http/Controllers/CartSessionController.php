<?php

namespace App\Http\Controllers;

use App\Services\CartSessionService;
use Illuminate\Http\Request;

class CartSessionController extends Controller
{
    protected $cartService;

    public function __construct(CartSessionService $cartService)
    {
        $this->cartService = $cartService;
    }

    // Helper para normalizar strings (copiado de ShippingCalculatorController)
    private function normalizeStr($str)
    {
        if (!$str)
            return '';
        $str = mb_strtolower($str, 'UTF-8');
        return trim(str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'],
            ['a', 'e', 'i', 'o', 'u', 'n', 'u'],
            $str
        ));
    }

    protected function checkEnabled()
    {
        // Feature flag bypassed for immediate enabling
        // if (!config('cart.session_enabled')) {
        //    abort(404);
        // }
    }

    public function setQty(Request $request)
    {
        $this->checkEnabled();

        $request->validate([
            'presentacion_id' => 'required|integer',
            'qty' => 'required|integer|min:0'
        ]);

        $cart = $this->cartService->setQty(
            $request->input('presentacion_id'),
            $request->input('qty')
        );

        return response()->json([
            'success' => true,
            'summary' => $this->cartService->summary()
        ]);
    }

    public function summary()
    {
        $this->checkEnabled();

        return response()->json([
            'success' => true,
            'summary' => $this->cartService->summary()
        ]);
    }

    public function clear()
    {
        $this->checkEnabled();

        $this->cartService->clear();

        return response()->json([
            'success' => true,
            'summary' => $this->cartService->summary()
        ]);
    }

    public function calculateShipping(Request $request)
    {
        $this->checkEnabled();
        $request->validate(['destino_id' => 'required|integer']);

        $destinoId = $request->input('destino_id');

        // 1. Get Destination Data
        $destino = \App\Models\Destino::find($destinoId);

        // --- LOGICA FLEX (PRIORIDAD) ---
        // Intentamos mapear via Flex primero, similar a ShippingCalculatorController
        $gpsData = session('gps_location');
        $userCity = $gpsData['cityName'] ?? ($destino ? $destino->nombre : null);
        $userPartido = $gpsData['partido'] ?? null;

        // 0. Calculate Total Weight first (Used for BOTH Flex and Legacy)
        $cart = $this->cartService->getCart();
        $totalWeight = 0;
        foreach ($cart as $item) {
            $totalWeight += ($item['qty'] * ($item['peso'] ?? 0));
        }

        // Logic "BULTOS" (30kg Rule) - Applies to both now
        $bultos = max(1, ceil($totalWeight / 30));

        if ($userCity || $userPartido) {
            // Helper simple para normalizar (lowercase + quitar acentos)
            $normalize = function ($str) {
                if (!$str)
                    return '';
                $str = mb_strtolower($str, 'UTF-8');
                $str = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'], ['a', 'e', 'i', 'o', 'u', 'n', 'u'], $str);
                return trim($str);
            };

            $normCity = $normalize($userCity);
            $normPartido = $normalize($userPartido);

            $mapeoFlex = null;

            // Strategy from ShippingCalculatorController
            \Illuminate\Support\Facades\Log::info("CartSession calc: City=[$normCity], Partido=[$normPartido]");

            if ($normCity) {
                $mapeoFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', $normCity)->first();
                if (!$mapeoFlex) {
                    $mapeoFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', $normCity)->first();
                }
            }
            if (!$mapeoFlex && $normPartido) {
                \Illuminate\Support\Facades\Log::info("CartSession checking Partido: [$normPartido]");
                $mapeoFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', $normPartido)->first();
                if (!$mapeoFlex) {
                    $mapeoFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', $normPartido)->first();
                }
            }
            if (!$mapeoFlex && $normPartido && strpos($normPartido, 'martin') !== false) {
                $mapeoFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', '%san martin%')->first();
            }

            // Si encontramos zona Flex
            if ($mapeoFlex) {
                \Illuminate\Support\Facades\Log::info("CartSession MATCH Flex: " . $mapeoFlex->nombre_busqueda);
                // Verificar Envío Gratis Flex
                // NOTA: Si es gratis, no multiplicamos por bultos (o sí? Gralmente gratis es gratis).
                // Asumimos gratis total por ahora.

                // Cobrar Tarifa Flex Normal
                $tarifa = $mapeoFlex->tarifa;
                if ($tarifa) {
                    \Illuminate\Support\Facades\Log::info("CartSession Flex Tarifa Found: " . $tarifa->nombre . " ($ " . $tarifa->monto . ")");
                    // APPLY BULTOS MULTIPLIER
                    $finalCost = $tarifa->monto * $bultos;

                    return response()->json([
                        'costo_envio' => $finalCost,
                        'tarifa_base' => $tarifa->monto,
                        'zona' => "Flex ({$tarifa->nombre})",
                        'peso_calculado' => $totalWeight,
                        'bultos' => $bultos,
                        'tipo' => 'flex_paid'
                    ]);
                }
            }
        }

        // --- FALLBACK LEGACY ---

        if (!$destino)
            return response()->json(['error' => 'Destino inválido'], 404);

        $zonaId = null;
        $nombreDestino = strtoupper($destino->nombre);

        // CABA Override logic 
        if (str_contains($nombreDestino, 'CABA') || str_contains($nombreDestino, 'CAPITAL FEDERAL')) {
            $zonaId = 5;
        } else {
            $dz = \App\Models\Destinozona::where('destino_id', $destinoId)->first();
            if ($dz)
                $zonaId = $dz->zona_id;
        }

        if (!$zonaId)
            return response()->json(['error' => 'El destino no tiene zona asignada'], 404);

        // 2. Calculate Total Weight (Already calculated at step 0)
        // $cart = $this->cartService->getCart(); 
        // $totalWeight... defined above.

        if ($totalWeight <= 0) {
            if (count($cart) > 0)
                $totalWeight = 1;
            else
                return response()->json(['costo_envio' => 0, 'peso_calculado' => 0]);
        }

        // 3. Logic "BULTOS" (30kg Rule) for Legacy Zones
        // Logic: Calculate how many 30kg packages.
        // Price matches the price of 1 package (Base) * Bultos.
        // We find the 'Base Price' by looking for a weight ~30kg or the Max weight available.

        $bultos = max(1, ceil($totalWeight / 30));

        // Find price for a single bulto (approx 30kg)
        // Ideally we check weight=30, or the highest bracket.

        $precioBase = 0;

        // Look for price of a ~25-30kg package (Standard max for 1 bulto)
        $precioEnvio = \App\Models\Pesozona::where('zona_id', $zonaId)
            ->where('peso', '>=', 29) // Look for ~30kg
            ->orderBy('peso', 'asc')
            ->first();

        if ($precioEnvio) {
            $precioBase = $precioEnvio->costo;
        } else {
            // Fallback: Get max available in table
            $maximo = \App\Models\Pesozona::where('zona_id', $zonaId)->orderBy('peso', 'desc')->first();
            if ($maximo) {
                $precioBase = $maximo->costo;
            }
        }

        if ($precioBase <= 0) {
            return response()->json(['error' => 'No hay tarifa disponible para esta zona.'], 400);
        }

        $finalCost = $precioBase * $bultos;

        return response()->json([
            'costo_envio' => $finalCost,
            'tarifa_base' => $precioBase,
            'peso_calculado' => $totalWeight,
            'bultos' => $bultos,
            'zona' => $zonaId
        ]);
    }
}
