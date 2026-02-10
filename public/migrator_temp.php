<?php
// Script temporal para correr migraciones en servidor donde no hay SSH
// Se debe borrar después de usar.

// Definir constante para que Laravel sepa cuándo arrancó
define('LARAVEL_START', microtime(true));

// Habilitar visualización de errores rápida
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Migration Runner</h1>";
echo "<pre>";

// Intentar cargar autoload desde diferentes ubicaciones posibles
$autoloadPaths = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../ferrindep/vendor/autoload.php', // Check sibling folder
    $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php',
    $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php',
    $_SERVER['DOCUMENT_ROOT'] . '/../ferrindep/vendor/autoload.php'
];

$autoloadFound = false;
foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        echo "Found autoload at: $path\n";
        require $path;
        $autoloadFound = true;
        break;
    }
}

if (!$autoloadFound) {
    echo "Checked paths:\n";
    print_r($autoloadPaths);
    die("Error: Could not find vendor/autoload.php in any expected location.");
}

// Intentar cargar app desde diferentes ubicaciones
$appPaths = [
    __DIR__ . '/bootstrap/app.php',
    __DIR__ . '/../bootstrap/app.php',
    __DIR__ . '/../ferrindep/bootstrap/app.php', // Check sibling folder
    $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/app.php',
    $_SERVER['DOCUMENT_ROOT'] . '/../bootstrap/app.php',
    $_SERVER['DOCUMENT_ROOT'] . '/../ferrindep/bootstrap/app.php'
];

$appFound = false;
foreach ($appPaths as $path) {
    if (file_exists($path)) {
        echo "Found app bootstrap at: $path\n";
        $app = require_once $path;
        $appFound = true;
        break;
    }
}

if (!$appFound) {
    die("Error: Could not find bootstrap/app.php in any expected location.");
}

// Run Kernel
try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
} catch (\Throwable $e) {
    echo "Kernel Boot Error: " . $e->getMessage() . "\n";
}

try {
    echo "<h2>Debug Schema: Destinos</h2>";
    $columns = \Illuminate\Support\Facades\DB::select('DESCRIBE destinos');
    print_r($columns);
    echo "\n";
} catch (\Throwable $e) {
    echo "Debug Error: " . $e->getMessage() . "\n";
}

try {
    echo "Running migrate...\n";
    // Drop table to ensure clean state if it exists partially
    \Illuminate\Support\Facades\Schema::dropIfExists('mapeo_ubicaciones');
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    echo \Illuminate\Support\Facades\Artisan::output();
    echo "\nDone.";
} catch (\Throwable $e) {
    echo "Migration Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

echo "</pre>";
