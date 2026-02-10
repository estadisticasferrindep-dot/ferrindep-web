<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MapeoZonaFlex;

echo "Checking MapeoZonaFlex for 'San Luis'...\n";

$matches = MapeoZonaFlex::where('nombre_busqueda', 'LIKE', '%san luis%')->get();

if ($matches->count() > 0) {
    echo "FOUND " . $matches->count() . " matches:\n";
    foreach ($matches as $m) {
        echo "- ID: {$m->id} | Nombre: {$m->nombre_busqueda} | Tarifa ID: {$m->tarifa_id}\n";
    }
} else {
    echo "No matches found for 'San Luis'.\n";
}
