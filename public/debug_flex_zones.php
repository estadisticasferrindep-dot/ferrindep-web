<?php
require __DIR__ . '/../ferrindep/vendor/autoload.php';
$app = require_once __DIR__ . '/../ferrindep/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking MapeoZonaFlex structure:\n";
$first = \App\Models\MapeoZonaFlex::first();
if ($first) {
    print_r($first->toArray());
} else {
    echo "No records in MapeoZonaFlex.\n";
}

echo "\nChecking Tarifas:\n";
$tarifas = \App\Models\Tarifa::all();
foreach ($tarifas as $t) {
    echo "ID: " . $t->id . " - Nombre: " . $t->nombre . "\n";
}

echo "\nChecking Villa Ballester Search:\n";
$search = 'villa ballester';
$mapeo = \App\Models\MapeoZonaFlex::where('nombre_busqueda', $search)->first();
if ($mapeo) {
    echo "Found Villa Ballester! Mapped to Tarifa ID: " . $mapeo->tarifa_id . "\n";
} else {
    echo "Villa Ballester not found exact.\n";
    $mapeoLike = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', "%$search%")->first();
    if ($mapeoLike) {
        echo "Found Villa Ballester via LIKE! Mapped to Tarifa ID: " . $mapeoLike->tarifa_id . "\n";
    } else {
        echo "Villa Ballester NOT FOUND at all.\n";
    }
}
