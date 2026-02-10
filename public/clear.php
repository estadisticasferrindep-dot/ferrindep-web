<?php
// public/clear.php - Force cache clear
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Forzando Limpieza de Opcaché y Laravel</h1>";

// 1. paths
$roots = [
    __DIR__ . '/../ferrindep', // Lo más probable según tu estructura
    __DIR__ . '/..',           // Estructura standard
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
    die("❌ No pude encontrar la carpeta de la aplicación Laravel. Contacta al soporte.");
}

try {
    echo "Ejecutando comandos artisan...<br>";

    \Illuminate\Support\Facades\Artisan::call('route:clear');
    echo "✅ route:clear <br>";

    \Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "✅ config:clear <br>";

    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "✅ cache:clear <br>";

    \Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "✅ view:clear <br>";

    // También re-optimizar por si acaso
    // \Illuminate\Support\Facades\Artisan::call('optimize'); 
    // echo "✅ optimize <br>";

    echo "<h2>¡Exito! La caché se ha limpiado.</h2>";
    echo "<p>Ahora puedes volver a intentar el link de setup: <a href='/setup-order-history'>/setup-order-history</a></p>";

} catch (\Exception $e) {
    echo "❌ Error al ejecutar Artisan: " . $e->getMessage();
}
