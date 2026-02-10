<?php

require __DIR__ . '/../ferrindep/vendor/autoload.php';

$app = require_once __DIR__ . '/../ferrindep/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h1>Chequeo de Modelo Presentacion</h1>";

if (method_exists(\App\Models\Presentacion::class, 'producto')) {
    echo "<h2 style='color:green'>METODO 'producto' EXISTE</h2>";
} else {
    echo "<h2 style='color:red'>METODO 'producto' NO EXISTE</h2>";
}

$p = new \App\Models\Presentacion();
echo "<pre>";
echo "Table: " . $p->getTable() . "\n";
echo "</pre>";

try {
    $ref = new ReflectionClass(\App\Models\Presentacion::class);
    $path = $ref->getFileName();
    echo "Arc: $path<br>";
    echo "Contenido:<br><pre>" . htmlspecialchars(file_get_contents($path)) . "</pre>";
} catch (Exception $e) {
    echo "Error reflection: " . $e->getMessage();
}
