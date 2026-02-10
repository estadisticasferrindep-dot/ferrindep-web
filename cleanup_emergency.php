<?php
// Script de Mantenimiento de Emergencia
// Sube este archivo a /public_html y ejecútalo desde el navegador

require __DIR__ . '/../ferrindep/vendor/autoload.php';
$app = require_once __DIR__ . '/../ferrindep/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<h1>LIMPIEZA DE EMERGENCIA INICIADA</h1>";

try {
    echo "1. Limpiando Vistas... ";
    Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "OK <br>";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "<br>";
}

try {
    echo "2. Limpiando Cache de Aplicación... ";
    Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "OK <br>";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "<br>";
}

try {
    echo "3. Limpiando Configuración... ";
    Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "OK <br>";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "<br>";
}

try {
    echo "4. Limpiando Sesiones (Esto desconectará usuarios)... ";
    // session:clear no siempre existe en todas las versiones, probamos borrar archivos si es file driver
    $sessionDriver = config('session.driver');
    if ($sessionDriver == 'file') {
        $files = glob(storage_path('framework/sessions/*'));
        foreach ($files as $file) {
            if (is_file($file))
                @unlink($file);
        }
        echo "OK (Archivos eliminados) <br>";
    } else {
        echo "Driver es $sessionDriver - Ejecutando comando... ";
        Illuminate\Support\Facades\Artisan::call('session:clear'); // Si existe el comando
        echo "OK <br>";
    }
} catch (\Exception $e) {
    echo "Warning: " . $e->getMessage() . "<br>";
}

echo "<h2>✅ PROCESO COMPLETADO. TU SITIO ESTÁ LIMPIO.</h2>";
echo "<p>Por favor, cierra esta pestaña y prueba en Incógnito.</p>";
