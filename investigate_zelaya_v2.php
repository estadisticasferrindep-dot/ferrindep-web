<?php
// investigate_zelaya.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$autoloadPath = __DIR__ . '/../ferrindep/vendor/autoload.php';

if (!file_exists($autoloadPath)) {
    die("Autoload not found at: $autoloadPath");
}

require $autoloadPath;
$app = require_once __DIR__ . '/../ferrindep/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "--- INVESTIGATION START ---\n";

// 1. Check Destino ID 76
$destino76 = \App\Models\Destino::find(76);
if ($destino76) {
    echo "Destino ID 76: " . $destino76->nombre . "\n";
    $zona = \App\Models\Destinozona::where('destino_id', 76)->first();
    echo "Zona Asignada: " . ($zona ? $zona->zona_id : 'Ninguna') . "\n";
    if ($zona) {
        $precio = \App\Models\Pesozona::where('zona_id', $zona->zona_id)->first(); // Just get first weight
        echo "Precio Base Legacy Zone {$zona->zona_id}: " . ($precio ? $precio->costo : 'N/A') . "\n";
    }
} else {
    echo "Destino ID 76 NOT FOUND.\n";
}

// 2. Check Zelaya in MapeoZonaFlex
echo "\n--- SEARCHING ZELAYA IN FLEX (mapeo_zona_flexes) ---\n";
try {
    $zelayaFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', '%zelaya%')->get();
    if ($zelayaFlex->count() > 0) {
        foreach ($zelayaFlex as $m) {
            echo "- Found: {$m->nombre_busqueda} -> Tarifa ID: " . ($m->tarifa_id ?? 'NULL') . "\n";
            if ($m->tarifa) {
                echo "  Tarifa Name: {$m->tarifa->nombre} | Cost: {$m->tarifa->monto}\n";
            }
        }
    } else {
        echo "Zelaya NOT FOUND in Flex Mappings.\n";
    }
} catch (\Exception $e) {
    echo "Error querying MapeoZonaFlex: " . $e->getMessage() . "\n";
}

// 3. Check Pilar in MapeoZonaFlex (for comparison)
echo "\n--- SEARCHING PILAR IN FLEX (mapeo_zona_flexes) ---\n";
try {
    $pilarFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', '%pilar%')->get();
    foreach ($pilarFlex as $m) {
        echo "- Found: {$m->nombre_busqueda} -> Tarifa ID: " . ($m->tarifa_id ?? 'NULL') . "\n";
        if ($m->tarifa) {
            echo "  Tarifa Name: {$m->tarifa->nombre} | Cost: {$m->tarifa->monto}\n";
        }
    }
} catch (\Exception $e) {
    echo "Error querying MapeoZonaFlex for Pilar: " . $e->getMessage() . "\n";
}

// 4. Check 'MapeoUbicacion' (Geo Detection)
echo "\n--- SEARCHING ZELAYA IN MapeoUbicacion ---\n";
try {
    $zelayaGeo = \App\Models\MapeoUbicacion::where('ciudad_detectada', 'LIKE', '%zelaya%')->with('destino')->get();
    foreach ($zelayaGeo as $m) {
        echo "- Found Geo: {$m->ciudad_detectada} -> Destino ID: " . ($m->destino->id ?? 'NULL') . " ({$m->destino->nombre})\n";
    }
} catch (\Exception $e) {
    echo "Error querying MapeoUbicacion: " . $e->getMessage() . "\n";
}

echo "--- INVESTIGATION END ---\n";
