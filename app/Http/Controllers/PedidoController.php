<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\PedidoMailable;

class PedidoController extends Controller
{
    /**
     * Lista de pedidos:
     * - Eager-load de itemsPedidos para evitar N+1
     * - Orden descendente (más nuevos primero)
     * - Paginación (ajustá 50/100/200 a gusto)
     */
    public function index()
    {
        // Eager load itemsPedidos AND their related products (itemsPedidos.producto) if needed for images
        $pedidos = Pedido::with(['itemsPedidos.producto'])
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('pedidos.index', compact('pedidos'));
    }

    public function create()
    {
        return view('pedidos.create');
    }

    public function store(Request $request)
    {
        $pedidos = Pedido::create($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/pedidos');
            $pedidos->imagen = $imagen;
        }

        $pedidos->save();

        return redirect()->route('pedidos.index')->with('info', 'Pedido agregado con éxito');
    }

    public function edit(Pedido $pedido)
    {
        return view('pedidos.edit', compact('pedido'));
    }

    public function update(Request $request, Pedido $pedido)
    {
        $pedido->update($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/pedidos');
            $pedido->imagen = $imagen;
        }

        if (!$request->show) {
            $pedido->show = 0;
        }

        if (!$request->home) {
            $pedido->home = 0;
        }

        $pedido->save();

        return redirect()->route('pedidos.index')->with('info', 'Pedido actualizado con éxito');
    }

    public function destroy(Pedido $pedido)
    {
        $pedido->delete();
        return redirect()->route('pedidos.index');
    }

    public function updateStatus(Request $request)
    {
        $pedido = Pedido::find($request->id);
        if ($pedido) {
            // Check if status changed to 'Listo para retirar'
            if ($pedido->estado_personalizado != 'Listo para retirar' && $request->status == 'Listo para retirar') {
                try {
                    \App\Jobs\SendOrderReadyEmail::dispatch($pedido)->delay(now()->addMinutes(3));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Error dispatching job: ' . $e->getMessage());
                }
            }

            // HISTORY LOG
            $historia = $pedido->historial_estado ?? [];
            if (!is_array($historia)) {
                // If existing data is not array (safe fallback)
                $historia = [];
            }

            // Append change
            $historia[] = [
                'estado' => $request->status,
                'fecha' => now()->format('d/m/Y H:i'),
                'user' => auth()->user()->name ?? 'Admin' // Assuming auth admin
            ];

            $pedido->historial_estado = $historia;
            $pedido->estado_personalizado = $request->status;
            $pedido->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }
    public function reenviarEmail(Request $request)
    {
        $pedido = Pedido::find($request->id);

        if (!$pedido) {
            return response()->json(['success' => false, 'message' => 'Pedido no encontrado'], 404);
        }

        try {
            // Determinar destinatario: Usuario registrado o Email del pedido
            $emailDestino = $pedido->usuario_email ?: $pedido->email;

            if (!$emailDestino) {
                return response()->json(['success' => false, 'message' => 'El pedido no tiene email asociado'], 422);
            }

            // Enviar Email
            // Podríamos enviar copia al admin también si se quisiera, 
            // pero la función dice "Reenviar Email" (al cliente usualmente).
            // Para asegurar, mandamos al cliente.

            // Reconstruir $cart simulado desde los items guardados para que la vista emails.pedido no falle
            $cartSimulado = $pedido->itemsPedidos->map(function ($item) {
                $obj = new \stdClass();
                $obj->id = $item->producto_id;
                $obj->nombre = $item->nombre;
                $obj->familia = $item->familia;
                $obj->medidas = $item->medidas;
                $obj->ancho = $item->ancho;
                $obj->espesor = $item->espesor;
                $obj->metros = $item->metros;
                $obj->cantidad = $item->cantidad;
                $obj->precio = $item->precio;
                // Agregamos con_nombre por si la vista lo usa
                $obj->con_nombre = $item->con_nombre;
                return $obj;
            });

            Mail::to($emailDestino)->send(new PedidoMailable($cartSimulado, $pedido));

            // Log para registro interno
            Log::info("Email reenviado manualmente para Pedido #{$pedido->id} a {$emailDestino} por " . (auth()->user()->name ?? 'Admin'));

            return response()->json(['success' => true, 'message' => "Email enviado a {$emailDestino}"]);

        } catch (\Exception $e) {
            Log::error("Error reenviando email pedido #{$pedido->id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al enviar: ' . $e->getMessage()], 500);
        }
    }
    public function updateNota(Request $request)
    {
        $pedido = Pedido::find($request->id);
        if ($pedido) {
            $pedido->nota = $request->nota;
            $pedido->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }
}
