<?php
use App\Models\MapeoZonaFlex;
use App\Models\Destino;

// Adjust paths for server structure: /public_html/ is parallel to /ferrindep/
// Try multiple common paths to be safe
$paths = [
    __DIR__ . '/../ferrindep/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/ferrindep/vendor/autoload.php',
    '/home/ferrinde/ferrindep/vendor/autoload.php'
];

$loaded = false;
foreach ($paths as $path) {
    if (file_exists($path)) {
        require $path;
        $loaded = true;
        // echo "Loaded autoload from: $path <br>";
        break;
    }
}

if (!$loaded) {
    die("Could not find autoload.php. Checked: " . implode(", ", $paths));
}

// Same for app.php
$appPaths = [
    __DIR__ . '/../ferrindep/bootstrap/app.php',
    __DIR__ . '/../bootstrap/app.php',
    __DIR__ . '/ferrindep/bootstrap/app.php',
    '/home/ferrinde/ferrindep/bootstrap/app.php'
];

$app = null;
foreach ($appPaths as $path) {
    if (file_exists($path)) {
        $app = require_once $path;
        break;
    }
}

if (!$app) {
    die("Could not bootstrap App.");
}

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<h1>Campana & Zarate Debug V3 (Legacy + Flex)</h1>";

$candidates = ['Campana', 'campana', 'Zarate', 'Zárate', 'zarate', 'ZARATE', 'CAMPANA'];

echo "<table border=1 cellpadding=5 style='border-collapse:collapse; width:100%'>";
echo "<tr><th>Busqueda (Raw)</th><th>Busqueda (Norm)</th><th>FLEX DB Match</th><th>LEGACY Destino Match</th></tr>";

$normalize = function ($str) {
    if (!$str)
        return '';
    $str = mb_strtolower($str, 'UTF-8');
    $str = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $str);
    return trim($str);
};

foreach ($candidates as $c) {
    $norm = $normalize($c);

    // --- FLEX ---
    $match = MapeoZonaFlex::where('nombre_busqueda', $norm)->first();
    $matchLike = MapeoZonaFlex::where('nombre_busqueda', 'LIKE', "%$norm%")->first();

    $flexRes = "<span style='color:red'>Not Found</span>";
    if ($match) {
        $flexRes = "<strong style='color:green'>EXACT</strong> (ID {$match->id})";
    } elseif ($matchLike) {
        $flexRes = "<span style='color:orange'>LIKE</span> (ID {$matchLike->id})";
    }

    // --- LEGACY ---
    $legacy = Destino::where('nombre', $c)->first(); // Exact raw
    if (!$legacy) {
        $legacy = Destino::where('nombre', 'LIKE', "%$c%")->first();
    }
    if (!$legacy) {
        $legacy = Destino::where('nombre', 'LIKE', "%$norm%")->first();
    }

    $legacyRes = "<span style='color:red'>Not Found</span>";
    if ($legacy) {
        $dz = \App\Models\Destinozona::where('destino_id', $legacy->id)->first();
        $zonaInfo = $dz ? "Zona {$dz->zona_id}" : "SIN ZONA";
        $legacyRes = "<strong style='color:green'>FOUND</strong>: {$legacy->nombre} (ID {$legacy->id}) -> $zonaInfo";
    }

    echo "<tr><td>$c</td><td>$norm</td><td>$flexRes</td><td>$legacyRes</td></tr>";
}
echo "</table>";

echo "<h2>Legacy Destinos Dump (First 100)</h2>";
echo "<div style='height:300px; overflow:auto; border:1px solid #ccc; padding:10px;'><pre>";
$destinos = Destino::take(100)->get();
foreach ($destinos as $d) {
    echo "ID: " . str_pad($d->id, 4) . " | Nombre: {$d->nombre}\n";
}
echo "</pre></div>";
