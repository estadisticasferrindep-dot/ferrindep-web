<?php
// public/check_session.php
// Diagnostic Script V2

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../ferrindep/vendor/autoload.php';
$app = require_once __DIR__ . '/../ferrindep/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Session;

echo "<style>body{font-family:sans-serif; padding: 20px; line-height:1.5}</style>";
echo "<h1>Diagnóstico Avanzado de Sesiones</h1>";

// 1. Cookies Recibidas
echo "<h3>1. Cookies que envía tu navegador:</h3>";
if (empty($_COOKIE)) {
    echo "<p style='color:red; font-weight:bold'>❌ TU NAVEGADOR NO ESTÁ ENVIANDO COOKIES. (O están bloqueadas)</p>";
} else {
    echo "<div style='background:#eee; padding:10px; border-radius:5px'><pre>" . print_r($_COOKIE, true) . "</pre></div>";
}

// 2. Configuración Actual
echo "<h3>2. Configuración del Servidor:</h3>";
$config = config('session');
echo "<ul>";
echo "<li><b>Driver:</b> " . $config['driver'] . "</li>";
echo "<li><b>Cookie Name:</b> " . $config['cookie'] . "</li>";
echo "<li><b>Domain:</b> " . ($config['domain'] ?? '<span style="color:gray">NULL (Automático)</span>') . "</li>";
echo "<li><b>Secure:</b> " . ($config['secure'] ? 'TRUE' : 'FALSE') . "</li>";
echo "<li><b>SameSite:</b> " . ($config['same_site'] ?? 'null') . "</li>";
echo "</ul>";

// 3. Inicio de Sesión
Session::start();
$oldId = Session::getId();
echo "<h3>3. ID de Sesión (Server):</h3>";
echo "<code style='font-size:1.2em; background:#ffefc0; padding:2px 5px'>$oldId</code>";

// 4. Prueba de Escritura/Lectura
echo "<h3>4. Estado de Persistencia:</h3>";

if (Session::has('test_key')) {
    echo "<div style='background:#dff0d8; color:#3c763d; padding:15px; border:1px solid #d6e9c6; border-radius:4px'>";
    echo "<h2>✅ ¡ÉXITO! SESIÓN RECUPERADA</h2>";
    echo "<p>Valor guardado: " . Session::get('test_key') . "</p>";
    echo "</div>";
} else {
    Session::put('test_key', 'Guardado a las ' . date('H:i:s'));
    Session::save(); // Force save
    echo "<div style='background:#fcf8e3; color:#8a6d3b; padding:15px; border:1px solid #faebcc; border-radius:4px'>";
    echo "<h2>⚠️ PRIMERA VEZ (O PERDIDA)</h2>";
    echo "<p>He guardado un dato nuevo. <b>RECARGA LA PÁGINA AHORA</b>.</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><i>Si al recargar el ID de sesión (Punto 3) CAMBIA, es que tu navegador no acepta la cookie.</i></p>";
