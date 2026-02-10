<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

echo "<h1>Fix Campana Zone (ID 39 -> Zone 4) V3</h1>";

try {
    echo "Step 1: Making Kernel...<br>";
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    echo "Step 2: Handling Request...<br>";
    $response = $kernel->handle($request = Illuminate\Http\Request::capture());

    echo "Step 3: Bootstrapped OK. Defining vars...<br>";
    $campanaId = 39;
    $campanaZona = 4; // Matches Zarate

    echo "Querying Campana...<br>";
    $dz = Destinozona::where('destino_id', $campanaId)->first();

    if ($dz) {
        echo "Entry ALREADY EXISTS for Campana (ID 39). Zona ID: " . $dz->zona_id . "<br>";
        if ($dz->zona_id != $campanaZona) {
            echo "UPDATING to Zone $campanaZona...<br>";
            $dz->zona_id = $campanaZona;
            $dz->save();
            echo "UPDATED.<br>";
        } else {
            echo "Zone match confirmed. No action needed.<br>";
        }
    } else {
        echo "Creating NEW LINK for Campana (39) -> Zona $campanaZona...<br>";
        $dz = new Destinozona();
        $dz->destino_id = $campanaId;
        $dz->zona_id = $campanaZona;
        $dz->save();
        echo "CREATED SUCCESSFULLY.<br>";
    }
} catch (\Throwable $e) {
    echo "<h3 style='color:red'>FATAL ERROR: " . $e->getMessage() . "</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "Done.";
