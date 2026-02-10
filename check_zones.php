<?php
// check_zones.php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

function check($term)
{
    echo "\nSearching for '$term':\n";

    // Check Destino (Legacy)
    $destinos = \App\Models\Destino::where('nombre', 'LIKE', "%$term%")->get();
    echo "  [Destino] Matches: " . $destinos->count() . "\n";
    foreach ($destinos as $d) {
        $extra = "";
        $dz = \App\Models\Destinozona::where('destino_id', $d->id)->first();
        if ($dz)
            $extra = " -> Zona " . $dz->zona_id;
        echo "    - ID: {$d->id}, Nombre: {$d->nombre}$extra\n";
    }

    // Check MapeoZonaFlex (Flex)
    $flex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', "%$term%")->get();
    echo "  [Flex] Matches: " . $flex->count() . "\n";
    foreach ($flex as $f) {
        echo "    - ID: {$f->id}, Nombre: {$f->nombre_busqueda}, TarifaID: {$f->tarifa_id}\n";
    }
}

check("San Miguel");
check("Lomas");
check("Matanza");
check("Buenos Aires");
check("Capital");
check("CABA");
check("Palermo");
check("Cordoba");
