<?php
use App\Models\MapeoUbicacion;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<h1>Verificación de Mapeos</h1>";

$searches = ['Ezeiza', 'San Martin', 'San Martín', 'General San Martin', 'General San Martín'];

foreach ($searches as $term) {
    echo "<h3>Buscando: '$term'</h3>";
    $results = MapeoUbicacion::where('ciudad_detectada', 'LIKE', "%$term%")->get();

    if ($results->count() > 0) {
        foreach ($results as $r) {
            echo "MATCH: [{$r->ciudad_detectada}] -> ID Destino: " . ($r->destino_id ?? 'NULL') . "<br>";
        }
    } else {
        echo "NO MATCH found.<br>";
    }
    echo "<hr>";
}
