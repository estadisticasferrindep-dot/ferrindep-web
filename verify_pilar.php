<?php

use App\Models\MapeoZonaFlex;
use App\Models\TarifaLogistica;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- CHECKING PILAR / LA LONJA ---\n";

$queries = ['La Lonja', 'Pilar', 'Partido de Pilar'];

foreach ($queries as $q) {
    $found = MapeoZonaFlex::where('nombre_ciudad_partido', 'LIKE', "%{$q}%")->get();
    echo "Query '{$q}': " . ($found->count() > 0 ? "FOUND {$found->count()} matches" : "NOT FOUND") . "\n";
    foreach ($found as $f) {
        echo " - Name: {$f->nombre_ciudad_partido} | Zona Flex ID: {$f->zona_flex_id}\n";

        $tarifa = TarifaLogistica::where('zona_id', $f->zona_flex_id)->first();
        if ($tarifa) {
            echo "   -> Tarifa: Base \${$tarifa->cobro_fijo} | Extra \${$tarifa->precio_kg_extra}\n";
        } else {
            echo "   -> NO TARIFF FOUND\n";
        }
    }
}
echo "--- END ---\n";
