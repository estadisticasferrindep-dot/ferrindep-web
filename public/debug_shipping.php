<?php
// debug_shipping.php

// Helper to find file
function findFile($filename)
{
    $paths = [
        __DIR__ . '/../ferrindep/' . $filename, // Production structure
        __DIR__ . '/' . $filename,              // Local testing
        __DIR__ . '/../' . $filename,
    ];
    foreach ($paths as $path) {
        if (file_exists($path))
            return $path;
    }
    die("Could not find $filename in any of the expected paths.");
}

require findFile('vendor/autoload.php');
$app = require_once findFile('bootstrap/app.php');
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<pre>";
echo "DEBUGGING SHIPPING CALCULATION\n";
echo "==============================\n";

$zonaId = 5;
$peso = 2.30;

echo "Target Zone: $zonaId\n";
echo "Target Weight: $peso\n";

echo "\n--- Searching for Price 4999.00 in All Zones (Weight ~2-3kg) ---\n";
$zones = \App\Models\Pesozona::select('zona_id')->distinct()->orderBy('zona_id')->get();
foreach ($zones as $z) {
    $r = \App\Models\Pesozona::where('zona_id', $z->zona_id)
        ->whereBetween('peso', [1.5, 3.5]) // Look around 2-3kg
        ->get();

    if ($r->count() > 0) {
        echo ">> Zone ID: {$z->zona_id} <<\n";
        foreach ($r as $rItem) {
            echo "   Peso: {$rItem->peso} | Costo: {$rItem->costo} \n";
        }
    }
}

echo "\n--- Query Test ---\n";
$precioEnvio = \App\Models\Pesozona::where('zona_id', $zonaId)
    ->where('peso', '>=', $peso)
    ->orderBy('peso', 'asc')
    ->first();

if ($precioEnvio) {
    echo "MATCH FOUND: Peso {$precioEnvio->peso} -> Costo {$precioEnvio->costo}\n";
} else {
    echo "NO MATCH FOUND for query: where('peso', '>=', $peso)\n";
}

echo "</pre>";
