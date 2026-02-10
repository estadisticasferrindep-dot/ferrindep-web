<?php
// DB Zone Importer V2 - Safe Idempotent Script
// Loads Laravel Bootstrap and imports missing zones into DB.

define('LARAVEL_START', microtime(true));
// FIX: Path for WNPower structure (public_html vs ferrindep folder)
require __DIR__ . '/../ferrindep/vendor/autoload.php';
$app = require_once __DIR__ . '/../ferrindep/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Destino;
use App\Models\Destinozona;
use App\Models\Zona;

header('Content-Type: text/plain; charset=utf-8');

echo "🚀 INICIANDO IMPORTACION DE ZONAS (V2)\n";
echo "=======================================\n\n";

// LISTA DE EMERGENCIA (A IMPORTAR)
// Format: {n: "name", id: TARIFA_ID}
// Tarifa IDs in DB: 1=Tarifa1, 2=Tarifa2, 3=Tarifa3, 4=Tarifa4
// Note: Code used logical IDs like 10, 11 (Zone 1) -> We map to DB ID 1
// Note: Code used logical IDs like 20, 21 (Zone 2) -> We map to DB ID 2
// ...

$zonesToImport = [
    // --- TARIFA 1 (DB ID: 1) ---
    ['name' => 'General San Martin', 'zona_id' => 1],
    ['name' => 'San Martin', 'zona_id' => 1],
    ['name' => 'Tres de Febrero', 'zona_id' => 1],
    ['name' => '3 de Febrero', 'zona_id' => 1],
    ['name' => 'Vicente Lopez', 'zona_id' => 1],
    ['name' => 'San Isidro', 'zona_id' => 1],
    ['name' => 'Caba', 'zona_id' => 1],
    ['name' => 'Capital Federal', 'zona_id' => 1],
    ['name' => 'Ciudad Autonoma de Buenos Aires', 'zona_id' => 1],
    ['name' => 'Buenos Aires', 'zona_id' => 1],
    ['name' => 'Villa Ballester', 'zona_id' => 1],
    ['name' => 'Ballester', 'zona_id' => 1],

    // --- TARIFA 2 (DB ID: 2) ---
    ['name' => 'Avellaneda', 'zona_id' => 2],
    ['name' => 'Lanus', 'zona_id' => 2],
    ['name' => 'Lomas de Zamora', 'zona_id' => 2],
    ['name' => 'La Matanza', 'zona_id' => 2],
    ['name' => 'Moron', 'zona_id' => 2],
    ['name' => 'Hurlingham', 'zona_id' => 2],
    ['name' => 'Ituzaingo', 'zona_id' => 2],
    ['name' => 'Tigre', 'zona_id' => 2],
    ['name' => 'San Fernando', 'zona_id' => 2],
    ['name' => 'San Miguel', 'zona_id' => 2],
    ['name' => 'Malvinas Argentinas', 'zona_id' => 2],

    // --- TARIFA 3 (DB ID: 3) ---
    ['name' => 'Merlo', 'zona_id' => 3],
    ['name' => 'Moreno', 'zona_id' => 3],
    ['name' => 'Quilmes', 'zona_id' => 3],
    ['name' => 'Florencio Varela', 'zona_id' => 3],
    ['name' => 'Ezeiza', 'zona_id' => 3],
    ['name' => 'Jose C Paz', 'zona_id' => 3],
    ['name' => 'Gregorio de Laferrere', 'zona_id' => 3],
    ['name' => 'Virrey del Pino', 'zona_id' => 3],
    ['name' => 'Esteban Echeverria', 'zona_id' => 3],
    ['name' => 'Almirante Brown', 'zona_id' => 3],
    ['name' => 'Berazategui', 'zona_id' => 3],

    // --- TARIFA 4 (DB ID: 4) ---
    ['name' => 'General Rodriguez', 'zona_id' => 4],
    ['name' => 'Gral Rodriguez', 'zona_id' => 4],
    ['name' => 'Pilar', 'zona_id' => 4],
    ['name' => 'Escobar', 'zona_id' => 4],
    ['name' => 'Canuelas', 'zona_id' => 4],
    ['name' => 'La Plata', 'zona_id' => 4],
    ['name' => 'Berisso', 'zona_id' => 4],
    ['name' => 'Ensenada', 'zona_id' => 4],
    ['name' => 'Campana', 'zona_id' => 4],
    ['name' => 'Lujan', 'zona_id' => 4],
    ['name' => 'Zarate', 'zona_id' => 4],
    ['name' => 'Marcos Paz', 'zona_id' => 4],
];

// Verify Tarifas Exist
echo "🔎 Verificando Tarifas Base (IDs 1-4)...\n";
$tarifas = Zona::whereIn('id', [1, 2, 3, 4])->get()->keyBy('id');

foreach ([1, 2, 3, 4] as $tid) {
    if (isset($tarifas[$tid])) {
        echo "✅ Tarifa ID $tid encontrada: " . $tarifas[$tid]->nombre . " ($" . $tarifas[$tid]->precio . ")\n";
    } else {
        echo "⛔ ERROR CRITICO: Tarifa ID $tid NO existe en DB. Abortando.\n";
        exit;
    }
}
echo "\n---------------------------------------\n";

$createdCount = 0;
$skippedCount = 0;
$errorCount = 0;

foreach ($zonesToImport as $item) {
    $name = $item['name'];
    $zonaId = $item['zona_id'];

    try {
        // 1. Check if Destino exists (Case Insensitive)
        $existing = Destino::where('nombre', 'LIKE', $name)->first();

        if ($existing) {
            echo "⏭️  SKIPPED: '$name' ya existe (ID: " . $existing->id . ")\n";
            $skippedCount++;

            // Optional: Check if linked correctly?
            // For safety, we just rely on existing.
        } else {
            // 2. Create Destino
            echo "➕ CREANDO: '$name' ... ";
            $newDestino = Destino::create(['nombre' => $name]);

            if ($newDestino) {
                // 3. Link to Zona
                Destinozona::create([
                    'destino_id' => $newDestino->id,
                    'zona_id' => $zonaId
                ]);
                echo "OK -> Linkeado a Tarifa $zonaId\n";
                $createdCount++;
            } else {
                echo "❌ FALLO CREACION\n";
                $errorCount++;
            }
        }

    } catch (\Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
        $errorCount++;
    }
}

echo "\n=======================================\n";
echo "📊 RESUMEN FINAL:\n";
echo "   Nuevas Zonas Creadas: $createdCount\n";
echo "   Ya Existentes (Saltadas): $skippedCount\n";
echo "   Errores: $errorCount\n";
echo "=======================================\n";

if ($errorCount === 0) {
    echo "✅ IMPORTACION EXITOSA. Ahora es seguro limpiar Checkout.vue.\n";
} else {
    echo "⚠️ HUBO ERRORES. Revisar log antes de limpiar.\n";
}
