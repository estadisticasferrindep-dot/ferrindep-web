<?php
use Illuminate\Support\Facades\DB;

// robust autoload
$paths = [
    __DIR__ . '/../ferrindep/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/ferrindep/vendor/autoload.php',
    '/home/ferrinde/ferrindep/vendor/autoload.php'
];
foreach ($paths as $path) {
    if (file_exists($path)) {
        require $path;
        break;
    }
}
// robust app bootstrap
$appPaths = [
    __DIR__ . '/../ferrindep/bootstrap/app.php',
    __DIR__ . '/../bootstrap/app.php',
    __DIR__ . '/ferrindep/bootstrap/app.php',
    '/home/ferrinde/ferrindep/bootstrap/app.php'
];
foreach ($appPaths as $path) {
    if (file_exists($path)) {
        $app = require_once $path;
        break;
    }
}
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "<h1>Fix Campana Zone V5 (Raw DB)</h1>";

$campanaId = 39;
$zonaId = 4;

$exists = DB::table('destino_zonas')->where('destino_id', $campanaId)->first();

if ($exists) {
    echo "EXISTS. Zona: {$exists->zona_id}<br>";
    if ($exists->zona_id != $zonaId) {
        echo "Updating...<br>";
        DB::table('destino_zonas')->where('destino_id', $campanaId)->update(['zona_id' => $zonaId]);
        echo "UPDATED.<br>";
    } else {
        echo "ALREADY CORRECT.<br>";
    }
} else {
    echo "INSERTING...<br>";
    DB::table('destino_zonas')->insert([
        'destino_id' => $campanaId,
        'zona_id' => $zonaId
    ]);
    echo "INSERTED.<br>";
}
echo "DONE.";
