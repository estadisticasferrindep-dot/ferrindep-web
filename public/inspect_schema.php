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

echo "🔎 INSPECCIONANDO TABLA 'zonas'\n";
echo "================================\n";

// 1. Column Listing
$columns = Schema::getColumnListing('zonas');
echo "Columnas encontradas:\n";
print_r($columns);

echo "\n--------------------------------\n";

// 2. Sample Data
$first = DB::table('zonas')->first();
echo "Primer registro (JSON):\n";
echo json_encode($first, JSON_PRETTY_PRINT);
