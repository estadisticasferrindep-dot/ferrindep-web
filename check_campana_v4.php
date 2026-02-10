<?php
use App\Models\MapeoZonaFlex;
use App\Models\Destino;
use App\Models\Destinozona;

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

echo "LEGACY LINK CHECK:\n";

$checkList = ['Campana' => 39, 'Zarate' => 83];

foreach ($checkList as $name => $id) {
    if ($id <= 0) { // Look it up
        $d = Destino::where('nombre', $name)->first();
        if ($d)
            $id = $d->id;
    }

    echo "Checking Destino '$name' (ID: $id): ";

    $d = Destino::find($id);
    if (!$d) {
        echo " NOT FOUND in 'destinos' table.\n";
        continue;
    }

    $dz = Destinozona::where('destino_id', $id)->first();
    if ($dz) {
        echo " HAS LINK -> Zona ID: {$dz->zona_id}\n";
    } else {
        echo " NO LINK in 'destino_zonas' table. (MISSING ZONA)\n";
    }
}
