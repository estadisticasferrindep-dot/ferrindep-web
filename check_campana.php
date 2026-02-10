<?php
use App\Models\MapeoZonaFlex;

// Adjust paths for server structure: /public_html/ is parallel to /ferrindep/
require __DIR__ . '/../ferrindep/vendor/autoload.php';
$app = require_once __DIR__ . '/../ferrindep/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<h1>Campana & Zarate Debug</h1>";

$candidates = ['Campana', 'campana', 'Zarate', 'Zárate', 'zarate', 'ZARATE', 'CAMPANA'];

echo "<table border=1 cellpadding=5 style='border-collapse:collapse; width:100%'>";
echo "<tr><th>Busqueda (Raw)</th><th>Busqueda (Norm)</th><th>DB Match Result</th><th>Tarifa Info</th></tr>";

$normalize = function ($str) {
    if (!$str)
        return '';
    $str = mb_strtolower($str, 'UTF-8');
    $str = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $str);
    return trim($str);
};

foreach ($candidates as $c) {
    $norm = $normalize($c);

    // Try Exact
    $match = MapeoZonaFlex::where('nombre_busqueda', $norm)->first();

    // Try LIKE
    $matchLike = MapeoZonaFlex::where('nombre_busqueda', 'LIKE', "%$norm%")->first();

    $res = "<span style='color:red'>Not Found</span>";
    $tid = "-";

    if ($match) {
        $res = "<span style='color:green'>EXACT MATCH</span>: " . htmlspecialchars($match->nombre_ciudad_partido) . " (Stored Busqueda: '{$match->nombre_busqueda}')";
        $tarifa = $match->tarifa;
        $tid = "Zona Flex ID: {$match->zona_flex_id} <br> Tarifa: " . ($tarifa ? "{$tarifa->nombre} (\${$tarifa->monto})" : 'NULL');
    } elseif ($matchLike) {
        $res = "<span style='color:orange'>LIKE MATCH</span>: " . htmlspecialchars($matchLike->nombre_ciudad_partido) . " (Stored Busqueda: '{$matchLike->nombre_busqueda}')";
        $tarifa = $matchLike->tarifa;
        $tid = "Zona Flex ID: {$matchLike->zona_flex_id} <br> Tarifa: " . ($tarifa ? "{$tarifa->nombre} (\${$tarifa->monto})" : 'NULL');
    }

    echo "<tr><td>$c</td><td>$norm</td><td>$res</td><td>$tid</td></tr>";
}
echo "</table>";

echo "<h2>All Mapeo Rows (Dump)</h2>";
echo "<div style='height:400px; overflow:auto; border:1px solid #ccc; padding:10px;'><pre>";
$all = MapeoZonaFlex::all();
foreach ($all as $m) {
    $tId = $m->zona_flex_id;
    // Manual join check
    $tName = $m->tarifa ? $m->tarifa->nombre : "No Tarifa";
    echo "ID: " . str_pad($m->id, 4) . " | Busqueda: [" . str_pad($m->nombre_busqueda, 20) . "] | Display: [" . str_pad($m->nombre_ciudad_partido, 20) . "] | FlexID: $tId ($tName)\n";
}
echo "</pre></div>";
