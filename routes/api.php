<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// --- RUTA BOT WHATSAPP ---
Route::get('/bot/check-order', function (Request $request) {
    $phone = $request->input('phone'); // ej: 5491157741441

    // 1. Limpieza básica del teléfono (dejar solo 4 últimos dígitos para búsqueda laxa o match exacto sin prefijos)
    // Para mayor precisión, buscamos "LIKE %...%"
    if (!$phone || strlen($phone) < 8) {
        return response()->json(['success' => false, 'message' => 'Phone required min 8 chars']);
    }

    // Quitamos '549' o '15' si queremos ser específicos, pero un LIKE %...% suele bastar
    // Si viene "5491157741441", buscamos "1157741441" o "57741441"
    $search = substr($phone, -8);
    $orderId = $request->input('order_id'); // Nuevo parámetro

    $query = \App\Models\Pedido::query();

    if ($orderId) {
        // Búsqueda por ID directo (Prioridad 1)
        // Buscamos coincidencia exacta de ID o que el código contenga el número
        $query->where(function ($q) use ($orderId) {
            $q->where('id', $orderId)
                ->orWhere('id', 'LIKE', "%$orderId"); // A veces ponen solo el final
        });
    } else {
        // Búsqueda por Teléfono (Legacy)
        $query->where(function ($q) use ($search) {
            $q->where('usuario_telefono', 'LIKE', "%$search%")
                ->orWhere('celular', 'LIKE', "%$search%");
        });
    }

    $pedidos = $query->with('itemsPedidos') // Traer items
        ->orderBy('id', 'desc')
        ->take(3)
        ->get();

    if ($pedidos->count() == 0) {
        return response()->json(['success' => true, 'orders' => []]);
    }

    $data = $pedidos->map(function ($p) {
        $items = $p->itemsPedidos->map(function ($i) {
            return $i->nombre . ' x' . $i->cantidad;
        })->implode(', ');

        return [
            'id' => $p->id,
            'estado' => $p->estado_personalizado ?? 'Pendiente', // Usar el campo correcto
            'total' => $p->total,
            'fecha' => $p->created_at->format('d/m/Y'),
            'items' => $items,
            'nombre' => $p->usuario_nombre ?? $p->nombre
        ];
    });

    return response()->json(['success' => true, 'orders' => $data]);
});

// --- RUTA BOT: OBTENER IMAGEN DE PRODUCTO ---
Route::get('/bot/product-image/{id}', function ($id) {
    $producto = \App\Models\Producto::find($id);
    if (!$producto || !$producto->imagen) {
        return response()->json(['image_url' => null]);
    }
    return response()->json([
        'image_url' => asset('storage/' . $producto->imagen),
        'name' => $producto->nombre
    ]);
});
