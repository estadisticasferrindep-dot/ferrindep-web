<?php
// DB Price Updater - Fix Empty Prices (RAW SQL EDITION)
// Sets correct prices for Tarifa 1-4

define('LARAVEL_START', microtime(true));
// FIX: Path for WNPower structure
require __DIR__ . '/../ferrindep/vendor/autoload.php';
$app = require_once __DIR__ . '/../ferrindep/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

header('Content-Type: text/plain; charset=utf-8');

echo "🚀 ACTUALIZANDO PRECIOS DE ZONAS (RAW SQL FIX)\n";
echo "=======================================\n\n";

$prices = [
    1 => 5000,
    2 => 5900,
    3 => 7000,
    4 => 9000
];

foreach ($prices as $id => $price) {
    try {
        echo "🔹 Zona ID $id: Estableciendo precio $price ... ";

        $affected = DB::table('zonas')
            ->where('id', $id)
            ->update(['precio' => $price, 'updated_at' => now()]);

        if ($affected) {
            echo "✅ ACTUALIZADO (Filas: $affected)\n";
        } else {
            // Check if it already has that value
            $current = DB::table('zonas')->where('id', $id)->first();
            if ($current && $current->precio == $price) {
                echo "✅ YA ESTABA ACTUALIZADO\n";
            } else {
                echo "⚠️ NO SE PUDO ACTUALIZAR (¿Existe el ID?)\n";
            }
        }
    } catch (\Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
    }
}

echo "\n=======================================\n";
echo "✅ ACTUALIZACION FINALIZADA\n";
