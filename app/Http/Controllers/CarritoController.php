<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Itemspedido;
use App\Mail\PedidoMailable;
use App\Models\Configpedido;
use App\Models\Presentacion;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CarritoController extends Controller
{
    /**
     * Recibe el pedido, crea Pedido + Items y devuelve JSON {status, redirect}.
     * No env��a mails ac�� para MP (se env��an en retorno()). Para Transferencia/Efectivo
     * dejamos el flujo tal como ya te funcionaba (por tu WebController/Success).
     */
    public function enviar(Request $request)
    {
        Log::info('envio_pedido payload', [
            'ct' => $request->header('content-type'),
            'all' => $request->all(),
            'raw' => $request->getContent(),
        ]);

        // ---- Leer carrito ANTES de crear Pedido
        // FIX: Priorizar datos del request (frescos del form) sobre sesión (puede estar desactualizada)
        $ct = strtolower($request->header('content-type', ''));

        $cart = $this->parseCartFromRequest($request);

        if (!is_array($cart) || !count($cart)) {
            // Fallback: intentar sesión PHP
            if (config('cart.session_enabled')) {
                $sessionService = app(\App\Services\CartSessionService::class);
                $sessionCart = $sessionService->getCart();
                if (count($sessionCart) > 0) {
                    $cart = $sessionCart;
                }
            }
        }

        // Si NO hay carrito y parece ser el "segundo POST" del form, lo ignoramos silenciosamente
        if (!is_array($cart) || !count($cart)) {
            if (strpos($ct, 'application/x-www-form-urlencoded') !== false) {
                return response()->noContent(); // 204 sin ruido
            }
            return response()->json(['ok' => false, 'msg' => 'No se recibieron productos del carrito'], 422);
        }

        $pedido = new Pedido;
        $user = auth()->guard('usuario')->user();
        $pedido->esta_pago = false;

        // ===== Datos del comprador =====
        if ($user) {
            $pedido->usuario_id = $user->id;
            $pedido->usuario_email = $user->email ?? $request->input('usuario_email', $request->input('email'));
            $pedido->usuario_nombre = $user->nombre ?? $request->input('usuario_nombre', $request->input('nombre'));
            $pedido->usuario_empresa = $user->empresa ?? $request->input('usuario_empresa');
            $pedido->usuario_telefono = $user->telefono ?? $request->input('usuario_celular', $request->input('celular'));
            $pedido->usuario_cuit = $user->cuit ?? $request->input('usuario_dni', $request->input('dni'));
            $pedido->usuario_direccion = $user->direccion ?? $request->input('usuario_domicilio', $request->input('direccion'));
        } else {
            $pedido->usuario_id = null;
            $pedido->usuario_email = $request->input('usuario_email', $request->input('email'));
            $pedido->usuario_nombre = $request->input('usuario_nombre', $request->input('nombre'));
            $pedido->usuario_empresa = $request->input('usuario_empresa'); // opcional
            $pedido->usuario_telefono = $request->input('usuario_celular', $request->input('celular'));
            $pedido->usuario_cuit = $request->input('usuario_dni', $request->input('dni'));
            $pedido->usuario_direccion = $request->input('usuario_domicilio', $request->input('direccion'));
        }

        // Duplico en campos legacy usados por vistas/emails
        $pedido->provincia = $request->input('usuario_provincia', $request->input('provincia'));
        $pedido->direccion = $request->input('usuario_domicilio', $request->input('direccion'));
        $pedido->localidad = $request->input('usuario_localidad', $request->input('localidad'));
        $pedido->celular = $request->input('usuario_celular', $request->input('celular'));
        $pedido->cp = $request->input('usuario_cp', $request->input('cp'));
        $pedido->email = $request->input('usuario_email', $request->input('email'));
        $pedido->dni = $request->input('usuario_dni', $request->input('dni'));
        $pedido->nombre = $request->input('usuario_nombre', $request->input('nombre'));

        $pedido->localidad_envio = $request->input('localidad_envio');
        $pedido->mensaje = $request->input('mensaje');

        // ===== SECURITY FIX: Recalcular totales server-side =====
        $envioTexto = $request->input('envio', $request->input('envio_calculado'));
        $pedido->pago = $request->input('pago');
        $pedido->envio = $envioTexto; // Guardamos el texto "caba"/"fabrica" intacto

        // 1. Calcular subtotal REAL desde precios de la DB
        $subtotal_real = 0;
        foreach ($cart as $value) {
            $p = is_array($value) ? $value : (array) $value;
            $presId = $p['presentacionId'] ?? $p['presentacion_id'] ?? null;
            $qty = (int) ($p['cantidad'] ?? $p['qty'] ?? $p['quantity'] ?? 1);
            if ($presId) {
                $pres = Presentacion::find($presId);
                if ($pres) {
                    $subtotal_real += $pres->precio * $qty;
                }
            }
        }
        $pedido->subtotal = $subtotal_real;

        // 2. Costo de envío (del frontend, no se puede recalcular sin la lógica completa de zonas)
        $envio_costo = (float) $request->input('envio_costo', 0);

        // 3. Calcular descuento/recargo según medio de pago usando config de la DB
        $config = Configpedido::first();
        $base_para_descuento = $subtotal_real + $envio_costo;
        $descuento = 0;

        if ($pedido->pago === 'efectivo' && $config) {
            $descuento = $base_para_descuento * ($config->descuento_efectivo / 100);
        } elseif ($pedido->pago === 'transferencia' && $config) {
            $descuento = $base_para_descuento * ($config->descuento_transferencia / 100);
        } elseif ($pedido->pago === 'mp' && $config) {
            $descuento = -($base_para_descuento * ($config->descuento_mp / 100)); // recargo (negativo)
        }

        $pedido->descuento_total = round($descuento, 2);

        // 4. Total final = subtotal + envío - descuento (o + recargo para MP)
        $total_real = $base_para_descuento - $descuento;
        $pedido->total = round($total_real, 2);

        if ($pedido->pago === 'mp') {
            $pedido->recargo_mp = round(abs($descuento), 2);
        } else {
            $pedido->recargo_mp = 0;
        }

        $pedido->save();

        // ===== Items del pedido =====
        $guardados = 0;
        foreach ($cart as $value) {
            $p = is_array($value) ? $value : (array) $value;

            $itemPedido = new Itemspedido;
            $itemPedido->pedido_id = $pedido->id;

            $presId = $p['presentacionId'] ?? $p['presentacion_id'] ?? null;
            $itemPedido->presentacion_id = $presId;
            $itemPedido->con_nombre = isset($p['conNombre']) ? (int) $p['conNombre'] : 0;

            $itemPedido->producto_id = $p['id'] ?? $p['producto_id'] ?? null;
            $itemPedido->nombre = $p['nombre'] ?? '';
            $itemPedido->cantidad = $p['cantidad'] ?? $p['qty'] ?? $p['quantity'] ?? 1;

            // SECURITY FIX: Re-fetch price from DB
            $dbPrice = 0;
            if ($presId) {
                $presentacion = \App\Models\Presentacion::find($presId);
                if ($presentacion) {
                    $dbPrice = $presentacion->precio;
                }
            }
            // Fallback to session price only if DB fetch fails (should not happen usually)
            $itemPedido->precio = ($dbPrice > 0) ? $dbPrice : ($p['precio'] ?? $p['price'] ?? 0);

            $itemPedido->metros = $p['metros'] ?? null;
            $itemPedido->medidas = $p['medidas'] ?? null;
            $itemPedido->espesor = $p['espesor'] ?? null;
            $itemPedido->ancho = $p['ancho'] ?? null;
            $itemPedido->familia = $p['familia'] ?? null;

            $itemPedido->save();
            $guardados++;
        }

        Log::info('[envio_pedido] items guardados', [
            'pedido' => $pedido->id,
            'count' => $guardados
        ]);

        // ===== Limpiar sesión de carrito (para TODOS los métodos de pago) =====
        if (config('cart.session_enabled')) {
            app(\App\Services\CartSessionService::class)->clear();
        }

        // ===== Redirección según medio de pago =====
        if ($pedido->pago === 'mp') {
            // MP: vamos a crear preferencia en pagar()
            return response()->json([
                'status' => 'success',
                'redirect' => route('mp.pagar', $pedido->id),
            ]);
        }

        // Transferencia / Efectivo: dejamos el flujo tal cual (tu success actual)
        return response()->json([
            'status' => 'success',
            'redirect' => route('web.success', $pedido->id),
        ]);
    }

    /**
     * COMPATIBILIDAD: si alg��n lugar llama a /pago/{id}, lo derivamos al nuevo flujo mp.pagar
     */
    public function confirm($id)
    {
        return redirect()->route('mp.pagar', $id);
    }

    /**
     * Crear preferencia en MercadoPago v��a API HTTP y redirigir al init_point (Checkout Pro).
     */
    public function pagar($pedidoId, Request $request)
    {
        $pedido = Pedido::findOrFail($pedidoId);

        try {
            $monto = (float) $pedido->total;
            if ($monto <= 0) {
                throw new \RuntimeException('Monto inv��lido para preferencia');
            }

            $pref = [
                'items' => [
                    [
                        'title' => 'Pedido #' . $pedido->id,
                        'quantity' => 1,
                        'currency_id' => 'ARS',
                        'unit_price' => round($monto, 2),
                    ]
                ],
                'payer' => [
                    'name' => $pedido->usuario_nombre ?: $pedido->nombre,
                    'email' => $pedido->usuario_email ?: $pedido->email,
                ],
                'back_urls' => [
                    'success' => route('mp.retorno'),
                    'failure' => route('mp.retorno'),
                    'pending' => route('mp.retorno'),
                ],
                'auto_return' => 'approved',
                'external_reference' => (string) $pedido->id,
            ];

            $token = env('MP_ACCESS_TOKEN');
            if (!$token) {
                throw new \RuntimeException('MP_ACCESS_TOKEN vac��o');
            }

            $res = Http::withToken($token)
                ->acceptJson()
                ->post('https://api.mercadopago.com/checkout/preferences', $pref);

            if (!$res->successful()) {
                Log::error('MP preferencia error HTTP', ['status' => $res->status(), 'body' => $res->body()]);
                return redirect()->route('web.failure');
            }

            $data = $res->json();
            $url = $data['init_point'] ?? ($data['sandbox_init_point'] ?? null);

            if (!$url) {
                Log::error('MP preferencia sin init_point', ['data' => $data]);
                return redirect()->route('web.failure');
            }

            return redirect()->away($url);

        } catch (\Throwable $e) {
            Log::error('MP preferencia error', ['msg' => $e->getMessage()]);
            return redirect()->route('web.failure');
        }
    }

    /**
     * Retorno desde MP (success/failure/pending). Enviamos mails SOLO si status=approved.
     */
    public function retorno(Request $request)
    {
        // MP puede mandar 'status' o 'collection_status'
        $status = $request->input('status', $request->input('collection_status', ''));
        $extRef = $request->input('external_reference'); // nuestro pedido->id
        $paymentId = $request->input('payment_id');

        Log::info('MP retorno', $request->all());

        if (!$extRef) {
            return redirect()->route('web.failure');
        }

        $pedido = Pedido::find($extRef);
        if (!$pedido) {
            return redirect()->route('web.failure');
        }

        if ($status === 'approved') {
            $yaEstabaPagado = (bool) $pedido->esta_pago;

            $pedido->esta_pago = true;
            $pedido->pago = 'mp';
            // Si tu tabla tiene campos para guardar info de pago, pod��s setearlos ac��:
            // $pedido->mp_payment_id = $paymentId;
            $pedido->save();

            // Enviar mails SOLO la primera vez que queda approved
            if (!$yaEstabaPagado) {
                $this->enviarMailsDePedido($pedido);
            }

            return redirect()->route('web.success', $pedido->id);
        }

        if ($status === 'pending') {
            return redirect()->route('web.pending');
        }

        return redirect()->route('web.failure');
    }

    /**
     * Normaliza datos del carrito contra stock/precio de Presentación.
     */
    public function cartdata(Request $request)
    {
        $cart = json_decode($request->cart);
        $newCart = [];

        if (!is_array($cart)) {
            return $newCart;
        }

        // Also sync to server session cart for accurate shipping calculations
        $cartService = app(\App\Services\CartSessionService::class);
        $cartService->clear(); // Clear old session data, rebuild from localStorage

        foreach ($cart as $car) {
            $presentacion = Presentacion::where('producto_id', $car->id ?? null)
                ->where('id', $car->presentacionId ?? null)
                ->first();
            $producto = Producto::where('id', $car->id ?? null)->first();

            if ($presentacion && $producto && ($presentacion->stock > 0)) {
                $car->precio = $presentacion->precio;
                $car->stock = $presentacion->stock;
                $car->free = $presentacion->free;
                $car->peso = $presentacion->peso ?? 0; // Authoritative weight from DB

                $car->anulaEnvio = $producto->anular_envio;
                $car->conNombre = $producto->con_nombre;
                $car->nombre = $producto->nombre;

                $newCart[] = $car;

                // Sync each item to session cart
                $qty = (int) ($car->cantidad ?? 1);
                if ($qty > 0) {
                    $cartService->setQty($presentacion->id, $qty);
                }
            }
        }

        return $newCart;
    }

    /* ======================== Helpers privados ======================== */

    /**
     * Parsear carrito desde request admitiendo varios formatos/campos.
     */
    private function parseCartFromRequest(Request $request): ?array
    {
        $cartPayload = $request->input('stringCart', $request->input('cart', $request->input('items')));
        $cart = null;

        if (is_string($cartPayload) && strlen($cartPayload)) {
            $decoded = json_decode($cartPayload, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $cart = $decoded;
            }
        } elseif (is_array($cartPayload)) {
            $cart = $cartPayload;
        } else {
            $raw = $request->getContent();
            if ($raw) {
                $decoded = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $cart = $decoded['stringCart'] ?? $decoded['cart'] ?? $decoded['items'] ?? $decoded;
                }
            }
        }

        if (is_object($cart))
            $cart = [$cart];
        return $cart;
    }

    /**
     * Enviar emails del pedido (admin + cliente). Se usa en MP retorno (approved).
     * No se llama desde enviar() para no romper tu flujo actual de Transferencia/Efectivo.
     */
    private function enviarMailsDePedido(Pedido $pedido): void
    {
        try {
            $admin = config('mail.from.address') ?: 'consultas@ferrindep.com.ar';
            $cliente = $pedido->usuario_email ?: $pedido->email;

            // Envi�� al admin
            Mail::to($admin)->send(new PedidoMailable($pedido));

            // Y opcional al cliente (si lo quer��s)
            if ($cliente) {
                Mail::to($cliente)->send(new PedidoMailable($pedido));
            }

            Log::info('Pedido: mails enviados', [
                'pedido' => $pedido->id,
                'admin' => $admin,
                'cliente' => $cliente,
            ]);
        } catch (\Throwable $e) {
            Log::error('Pedido: error enviando mails', [
                'pedido' => $pedido->id,
                'err' => $e->getMessage()
            ]);
        }
    }
}
