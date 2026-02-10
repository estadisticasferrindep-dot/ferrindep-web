<?php
// DB Schema Fixer - Add 'precio' column
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../ferrindep/vendor/autoload.php';
$app = require_once __DIR__ . '/../ferrindep/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

header('Content-Type: text/plain; charset=utf-8');

echo "🛠️ AGREGANDO COLUMNA 'precio' A TABLA 'zonas'\n";
echo "=============================================\n";

if (Schema::hasColumn('zonas', 'precio')) {
    echo "⚠️ La columna 'precio' YA EXISTE.\n";
} else {
    try {
        Schema::table('zonas', function (Blueprint $table) {
            $table->decimal('precio', 10, 2)->default(0)->after('nombre');
        });
        echo "✅ Columna 'precio' AGREGADA EXITOSAMENTE.\n";
    } catch (\Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
    }
}

echo "\n---------------------------------------------\n";
$columns = Schema::getColumnListing('zonas');
print_r($columns);
