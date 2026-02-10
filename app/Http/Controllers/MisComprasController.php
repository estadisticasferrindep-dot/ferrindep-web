<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Models\Familia;
use App\Models\Configuracion;
use App\Models\Red;
use App\Models\Home;
use App\Models\Producto;

class MisComprasController extends Controller
{
    private function getSharedData()
    {
        $homes = Home::get();
        $home = $homes->first();

        $familia_1 = Familia::where('show', 1)->get()->first();

        $configuraciones = Configuracion::get();
        $configuracion = $configuraciones->first();

        $redes = Red::where('show', 1)->orderBy('orden')->get();

        // Productos para el carrusel (Mallas y Metal Desplegado mezclados)
        $productosCarousel = Producto::where('show', 1)->inRandomOrder()->take(24)->get();

        return compact('home', 'familia_1', 'configuracion', 'redes', 'productosCarousel');
    }

    public function login()
    {
        $title = 'Mis Compras - Acceso';
        $description = 'Consulta el estado de tus pedidos ingresando tu celular.';
        $keywords = 'mis compras, pedidos, historial, estado';

        $shared = $this->getSharedData();

        // Si esta vista no existe, te va a dar 500.
        return view('web.cliente.login', array_merge($shared, compact('title', 'description', 'keywords')));
    }

    public function historial(Request $request)
    {
        $request->validate([
            'celular' => 'required|string|min:6',
        ]);

        $celularInput = trim((string) $request->input('celular'));

        // Normalizamos un poco (dejamos solo números para buscar también)
        $celularDigits = preg_replace('/\D+/', '', $celularInput) ?: $celularInput;

        // Chequeamos columnas reales en DB para NO romper con "Unknown column"
        $tabla = (new Pedido)->getTable(); // normalmente "pedidos"
        $hasCelular = Schema::hasColumn($tabla, 'celular');
        $hasUsuarioTelefono = Schema::hasColumn($tabla, 'usuario_telefono');
        $hasUsuarioNombre = Schema::hasColumn($tabla, 'usuario_nombre');
        $hasNombreEnvio = Schema::hasColumn($tabla, 'nombre');

        // Variables compartidas
        $shared = $this->getSharedData();

        if (!$hasCelular && !$hasUsuarioTelefono) {
            // No podemos buscar sin columnas
            Log::error("MisCompras: faltan columnas 'celular' y 'usuario_telefono' en la tabla {$tabla}.");
            $pedidos = collect();
            $nombreCliente = 'Cliente';

            $title = 'Mis Compras - Historial';
            $description = 'Historial de tus compras realizadas.';
            $keywords = 'mis compras, historial, pedidos';

            // Podés mostrar este error en la vista si querés
            $error = "No se puede buscar porque la tabla '{$tabla}' no tiene columnas 'celular' ni 'usuario_telefono'.";
            return view('web.cliente.historial', array_merge($shared, compact('pedidos', 'nombreCliente', 'celularInput', 'title', 'description', 'keywords', 'error')));
        }

        // Armamos candidatos: solo dígitos, con y sin 0 inicial
        $variantes = [$celularDigits];
        if (Str::startsWith($celularDigits, '0')) {
            $variantes[] = substr($celularDigits, 1);
        } else {
            $variantes[] = '0' . $celularDigits;
        }
        $variantes = array_values(array_unique($variantes));

        $query = Pedido::query();

        $query->where(function ($q) use ($hasCelular, $hasUsuarioTelefono, $variantes) {
            $total = count($variantes);
            $placeholders = implode(',', array_fill(0, $total, '?'));

            $first = true;

            if ($hasCelular) {
                // Limpiamos la columna de DB para que "11-4537" sea "114537" y coincida con nuestra variante
                $q->whereRaw("REPLACE(REPLACE(celular, '-', ''), ' ', '') IN ($placeholders)", $variantes);
                $first = false;
            }

            if ($hasUsuarioTelefono) {
                // Lo mismo para usuario_telefono
                $sql = "REPLACE(REPLACE(usuario_telefono, '-', ''), ' ', '') IN ($placeholders)";
                if ($first) {
                    $q->whereRaw($sql, $variantes);
                } else {
                    $q->orWhereRaw($sql, $variantes);
                }
            }
        });

        $pedidos = $query->orderBy('created_at', 'desc')
            ->with('itemsPedidos.producto') // Eager loading con producto para fallback de nombre
            ->get();

        // Nombre (solo primer nombre, y sin romper si columnas faltan)
        $nombreCliente = 'Cliente';
        if ($pedidos->isNotEmpty()) {
            $p = $pedidos->first();

            $nombreCompleto = null;
            if ($hasUsuarioNombre && !empty($p->usuario_nombre))
                $nombreCompleto = $p->usuario_nombre;
            if (!$nombreCompleto && $hasNombreEnvio && !empty($p->nombre))
                $nombreCompleto = $p->nombre;

            if (is_string($nombreCompleto) && trim($nombreCompleto) !== '') {
                $primer = explode(' ', trim($nombreCompleto))[0] ?? 'Cliente';
                $nombreCliente = Str::title($primer);
            }

            // --- ALERTA DE SEGUIMIENTO ---
            try {
                $mainRecipient = config('mail.from.address') ?: 'consultas@ferrindep.com.ar';
                $ccRecipients = ['estadisticasferrindep@gmail.com', 'ferrindepventas@gmail.com'];

                Log::info("INTENTO ENVIO ALERTA: Main={$mainRecipient}, CC=" . implode(',', $ccRecipients));

                \Illuminate\Support\Facades\Mail::to($mainRecipient)
                    ->cc($ccRecipients)
                    ->send(new \App\Mail\AlertaSeguimientoMailable($celularInput, $pedidos, $nombreCliente));

                Log::info("ALERTA ENVIADA EXITOSAMENTE a {$mainRecipient} + CCs.");
            } catch (\Exception $e) {
                Log::error("ERROR CRITICO ENVIO ALERTA: " . $e->getMessage());
            }
        }

        $title = 'Mis Compras - Historial';
        $description = 'Historial de tus compras realizadas.';
        $keywords = 'mis compras, historial, pedidos';

        return view('web.cliente.historial', array_merge($shared, [
            'pedidos' => $pedidos,
            'nombreCliente' => $nombreCliente,
            'celular' => $celularInput,
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
        ]));
    }
}
