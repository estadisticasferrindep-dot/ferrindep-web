<?php

$paths = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../ferrindep/vendor/autoload.php',
    __DIR__ . '/vendor/autoload.php',
];

$autoloadPath = null;
foreach ($paths as $path) {
    if (file_exists($path)) {
        $autoloadPath = $path;
        break;
    }
}

if (!$autoloadPath) {
    die("<h1>Error Fatal</h1>No se encontró vendor/autoload.php");
}

require $autoloadPath;
$basePath = dirname($autoloadPath, 2);
$appPath = $basePath . '/bootstrap/app.php';
$app = require_once $appPath;
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/html; charset=utf-8');
echo "<h1>Flex Zone Debug for Cañuelas</h1>";

$normalize = function ($str) {
    $str = mb_strtolower($str, 'UTF-8');
    return trim(str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'], ['a', 'e', 'i', 'o', 'u', 'n', 'u'], $str));
};

// ═══ CAÑUELAS ═══
$city = 'Cañuelas';
$normCity = $normalize($city);
echo "<p><b>City:</b> {$city} → normalized: <b>{$normCity}</b></p>";

// 1. MapeoZonaFlex
echo "<h2>1. MapeoZonaFlex lookup for '{$normCity}'</h2>";
$exact = \App\Models\MapeoZonaFlex::where('nombre_busqueda', $normCity)->first();
echo "<p>Exact: " . ($exact ? "✅ ID:{$exact->id}, nombre:{$exact->nombre_busqueda}, tarifa_id:{$exact->tarifa_id}" : "❌ NOT FOUND") . "</p>";

$like = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', '%' . $normCity . '%')->first();
echo "<p>LIKE: " . ($like ? "✅ ID:{$like->id}, nombre:{$like->nombre_busqueda}, tarifa_id:{$like->tarifa_id}" : "❌ NOT FOUND") . "</p>";

// All entries with 'canu'
$allCanu = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', '%canu%')->get();
echo "<p>All entries with 'canu': <b>" . $allCanu->count() . "</b></p>";
foreach ($allCanu as $m) {
    $t = $m->tarifa;
    echo "<p>&nbsp;&nbsp;→ ID:{$m->id}, nombre_busqueda:<b>{$m->nombre_busqueda}</b>, tarifa_id:{$m->tarifa_id}" . ($t ? " → monto:\${$t->monto}" : "") . "</p>";
}

// 2. Tarifa for match
$mapeo = $exact ?: $like;
if ($mapeo) {
    $tarifa = \App\Models\TarifaLogistica::find($mapeo->tarifa_id);
    echo "<h2>2. Tarifa</h2>";
    echo "<p>" . ($tarifa ? "✅ nombre:{$tarifa->nombre}, monto:<b>\${$tarifa->monto}</b>" : "❌ NOT FOUND") . "</p>";
} else {
    echo "<h2>2. ❌ No Flex match for Cañuelas</h2>";
}

// 3. Legacy
echo "<h2>3. Legacy Destino lookup</h2>";
$destinos = \App\Models\Destino::all();
$found = null;
foreach ($destinos as $d) {
    if ($normalize($d->nombre) === $normCity) {
        $found = $d;
        break;
    }
}
echo "<p>Legacy: " . ($found ? "✅ ID:{$found->id}, nombre:{$found->nombre}" : "❌ NOT FOUND") . "</p>";
if ($found) {
    $dz = \App\Models\Destinozona::where('destino_id', $found->id)->first();
    if ($dz) {
        $zone = \App\Models\Zona::find($dz->zona_id);
        echo "<p>Zone ID: {$dz->zona_id} → " . ($zone ? "precio:<b>\$" . ($zone->precio ?? $zone->costo ?? 'NULL') . "</b>" : "Zone not found") . "</p>";
    }
}

// 4. All TarifaLogistica
echo "<h2>4. All TarifaLogistica</h2>";
$tarifas = \App\Models\TarifaLogistica::all();
foreach ($tarifas as $t) {
    echo "<p>ID:{$t->id}, nombre:{$t->nombre}, monto:<b>\${$t->monto}</b></p>";
}

// 5. All Zonas (Legacy)
echo "<h2>5. All Zonas (Legacy)</h2>";
$zonas = \App\Models\Zona::all();
foreach ($zonas as $z) {
    $p = $z->precio ?? $z->costo ?? 'NULL';
    echo "<p>ID:{$z->id}, nombre:" . ($z->nombre ?? 'N/A') . ", precio:<b>\${$p}</b></p>";
}

// 6. General Rodriguez lookup (should work)
echo "<h2>6. General Rodriguez lookup (reference)</h2>";
$normGR = $normalize('General Rodríguez');
echo "<p>Normalized: <b>{$normGR}</b></p>";
$grFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', $normGR)->first();
if (!$grFlex)
    $grFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', '%' . $normGR . '%')->first();
echo "<p>Flex: " . ($grFlex ? "✅ tarifa_id:{$grFlex->tarifa_id}" : "❌ NOT FOUND") . "</p>";
if ($grFlex) {
    $t = \App\Models\TarifaLogistica::find($grFlex->tarifa_id);
    echo "<p>Tarifa: " . ($t ? "\${$t->monto}" : "NOT FOUND") . "</p>";
}
