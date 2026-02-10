<?php

use App\Models\MapeoZonaFlex;
use App\Models\TarifaLogistica;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- VERIFICATION START ---\n";

$partido = 'General San Martin';
echo "Testing Partido: '{$partido}'\n";

$flexZone = MapeoZonaFlex::where('nombre_ciudad_partido', $partido)->first();

if ($flexZone) {
    echo "✔ Found MapeoZonaFlex: ID {$flexZone->id} -> Zona Flex ID: {$flexZone->zona_flex_id}\n";

    $tarifa = TarifaLogistica::where('zona_id', $flexZone->zona_flex_id)->first();

    if ($tarifa) {
        echo "✔ Found TarifaLogistica: Base \${$tarifa->cobro_fijo} | Kg Extra \${$tarifa->precio_kg_extra}\n";

        $expected = 5000; // As per user expectation
        if ($tarifa->cobro_fijo == $expected) {
            echo "SUCCESS: Cost matches expected \${$expected}\n";
        } else {
            echo "WARNING: Cost \${$tarifa->cobro_fijo} does not match expected \${$expected}\n";
        }
    } else {
        echo "❌ No TarifaLogistica found for Zona Flex ID {$flexZone->zona_flex_id}\n";
    }

} else {
    echo "❌ No MapeoZonaFlex found for '{$partido}'\n";

    // Check what is in the DB
    $all = MapeoZonaFlex::all();
    echo "Available mappings: " . $all->pluck('nombre_ciudad_partido')->implode(', ') . "\n";
}

echo "--- VERIFICATION END ---\n";
