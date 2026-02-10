<?php
// public/optimize.php
// Script to run Laravel Production Optimizations
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Optimizando Laravel para Producción</h1>";

// 1. paths
$roots = [
    __DIR__ . '/../ferrindep',
    __DIR__ . '/..',
];

$found = false;
foreach ($roots as $root) {
    if (file_exists($root . '/vendor/autoload.php')) {
        echo "Found app at: $root <br>";
        require $root . '/vendor/autoload.php';
        $app = require_once $root . '/bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        $found = true;
        break;
    }
}

if (!$found) {
    die("❌ No pude encontrar la carpeta de la aplicación Laravel.");
}

try {
    echo "Ejecutando comandos artisan...<br>";

    // 1. Clear first to be sure
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    echo "✅ optimize:clear <br>";

    // 2. Cache Config
    \Illuminate\Support\Facades\Artisan::call('config:cache');
    echo "✅ config:cache <br>";

    // 3. Cache Routes
    \Illuminate\Support\Facades\Artisan::call('route:cache');
    echo "✅ route:cache <br>";

    // 4. Cache Views
    \Illuminate\Support\Facades\Artisan::call('view:cache');
    echo "✅ view:cache <br>";

    echo "<h2>¡Optimización Completa!</h2>";
    echo "<p>Tu sitio ahora debería volar 🚀. Recuerda borrar cookies si tienes problemas de sesión.</p>";

} catch (\Exception $e) {
    echo "❌ Error al ejecutar Artisan: " . $e->getMessage();
}
