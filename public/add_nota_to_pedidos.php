<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../ferrindep/vendor/autoload.php';
$app = require_once __DIR__ . '/../ferrindep/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

header('Content-Type: text/plain; charset=utf-8');

echo "🛠️ INTENTANDO AGREGAR COLUMNA 'nota' A 'pedidos'...\n";

if (Schema::hasColumn('pedidos', 'nota')) {
    echo "⚠️ La columna 'nota' YA EXISTE. No se realizaron cambios.\n";
} else {
    try {
        DB::statement('ALTER TABLE pedidos ADD COLUMN nota TEXT NULL AFTER mensaje');
        echo "✅ Columna 'nota' agregada CORRECTAMENTE.\n";
    } catch (\Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
    }
}

// Verificar de nuevo
$columns = Schema::getColumnListing('pedidos');
if (in_array('nota', $columns)) {
    echo "VERIFICACIÓN: La columna 'nota' ESTÁ PRESENTE.\n";
} else {
    echo "VERIFICACIÓN: La columna 'nota' NO aparece.\n";
}
