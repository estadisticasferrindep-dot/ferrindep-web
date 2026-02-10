<?php

use App\Models\Destino;
use App\Models\Destinozona;
use App\Models\Pesozona;
use App\Models\MapeoZonaFlex;
use App\Models\TarifaLogistica;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<h1>Debug Precios Pilar</h1>";

// 1. LEGACY SYSTEM
echo "<h2>1. Sistema Legacy (Destinos / Zonas / Pesos)</h2>";
$destino = Destino::where('nombre', 'LIKE', '%Pilar%')->first(); // Adivinando nombre en DB 'Pilar' o similar
if ($destino) {
    echo "Destino encontrado: {$destino->nombre} (ID: {$destino->id})<br>";
    $dz = Destinozona::where('destino_id', $destino->id)->first();
    if ($dz) {
        echo "Zona asociada ID: {$dz->zona_id}<br>";
        $precios = Pesozona::where('zona_id', $dz->zona_id)->orderBy('peso')->get();
        echo "<ul>";
        foreach ($precios as $p) {
            echo "<li>Peso {$p->peso}kg: $ {$p->costo}</li>";
        }
        echo "</ul>";
    } else {
        echo "No tiene zona asociada.<br>";
    }
} else {
    echo "No se encontró destino 'Pilar' en tabla destinos.<br>";
}

// 2. FLEX SYSTEM
echo "<h2>2. Sistema Flex (MapeoZonaFlex / Tarifas)</h2>";
$flex = MapeoZonaFlex::where('nombre_busqueda', 'LIKE', '%pilar%')->get();
if ($flex->count() > 0) {
    foreach ($flex as $f) {
        echo "Mapeo Flex: '{$f->nombre_busqueda}' (ID: {$f->id})<br>";
        if ($f->tarifa) {
            echo "Tarifa: {$f->tarifa->nombre} - Costo: $ {$f->tarifa->monto}<br>";
        } else {
            echo "Sin tarifa asignada.<br>";
        }
        echo "<hr>";
    }
} else {
    echo "No se encontró 'pilar' en MapeoZonaFlex.<br>";
}
