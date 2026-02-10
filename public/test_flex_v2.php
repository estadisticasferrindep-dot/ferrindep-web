<?php
require __DIR__ . '/../ferrindep/vendor/autoload.php';
$app = require_once __DIR__ . '/../ferrindep/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\MapeoZonaFlex;
use App\Models\TarifaLogistica;

echo "<h1>Flex Logic Test V2</h1>";

$tests = [
    'Ramos Mejia' => 6000,
    'Virrey del Pino' => 7000,
    'Canuelas' => 9000,
    'Cordoba' => null // Should be null
];

foreach ($tests as $term => $expectedMonto) {
    $norm = mb_strtolower($term, 'UTF-8');
    echo "Testing: '$term' (norm: '$norm')... ";
    $map = MapeoZonaFlex::where('nombre_busqueda', $norm)->with('tarifa')->first();
    if ($map) {
        echo "FOUND! Tarifa: " . $map->tarifa->nombre . " ($" . $map->tarifa->monto . ")";
        if ($expectedMonto && $map->tarifa->monto == $expectedMonto) {
            echo " [OK]";
        } else {
            echo " [MISMATCH - Expected $expectedMonto]";
        }
    } else {
        echo "NOT FOUND.";
        if ($expectedMonto === null)
            echo " [OK]";
        else
            echo " [MISSING]";
    }
    echo "<br>";
}
