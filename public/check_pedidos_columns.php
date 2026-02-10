<?php
// DB Schema Inspector
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

echo "🔎 INSPECCIONANDO TABLA 'pedidos'\n";
echo "================================\n";

$columns = Schema::getColumnListing('pedidos');
echo "Columnas encontradas:\n";
print_r($columns);
