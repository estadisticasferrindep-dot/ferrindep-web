<?php
// fix_db_decimals.php
// Script para ampliar la capacidad de dígitos en la base de datos (Precio > 1 millón)

// Cargar Laravel
// fix_db_decimals.php
// Script para ampliar la capacidad de dígitos en la base de datos (Precio > 1 millón)

// Intentar encontrar el autoload en varias ubicaciones posibles
$paths = [
    __DIR__ . '/vendor/autoload.php',           // Standard
    __DIR__ . '/../vendor/autoload.php',        // cPanel standard (root/public_html)
    __DIR__ . '/../ferrindep/vendor/autoload.php', // Custom folder structure
    $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php',
    $_SERVER['DOCUMENT_ROOT'] . '/ferrindep/vendor/autoload.php',
];

$autoloadFound = false;
foreach ($paths as $path) {
    if (file_exists($path)) {
        require $path;
        $autoloadFound = true;
        // Asumimos que bootstrap/app.php está relativo al vendor
        $appPath = dirname($path, 2) . '/bootstrap/app.php';
        if (file_exists($appPath)) {
            $app = require_once $appPath;
        } else {
            die("Encontre autoload pero no bootstrap/app.php en: $appPath");
        }
        break;
    }
}

if (!$autoloadFound) {
    die("<h1>Error: No pude encontrar 'vendor/autoload.php'</h1><p>Probé en:</p><ul>" . implode('', array_map(fn($p) => "<li>$p</li>", $paths)) . "</ul>");
}

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "<html><body style='font-family:sans-serif; padding:20px;'>";
echo "<h1>Mantenimiento de Base de Datos</h1>";
echo "<p>Iniciando proceso de actualización de columnas numéricas...</p>";

try {
    // 1. Tabla PEDIDOS
    echo "<h3>1. Tabla 'pedidos'</h3>";

    $updatesPedidos = [
        'total',
        'subtotal',
        'descuento_total',
        'iva' // A veces existe
    ];

    foreach ($updatesPedidos as $col) {
        if (Schema::hasColumn('pedidos', $col)) {
            echo "Ampliar columna <b>$col</b>... ";
            DB::statement("ALTER TABLE pedidos MODIFY COLUMN $col DECIMAL(15,2) NULL");
            echo "<span style='color:green'>OK</span><br>";
        }
    }

    // Chequear recargo_mp que a veces se agrega manual
    if (Schema::hasColumn('pedidos', 'recargo_mp')) {
        echo "Ampliar columna <b>recargo_mp</b>... ";
        DB::statement("ALTER TABLE pedidos MODIFY COLUMN recargo_mp DECIMAL(15,2) NULL");
        echo "<span style='color:green'>OK</span><br>";
    }

    // 2. Tabla ITEMSPEDIDOS
    echo "<h3>2. Tabla 'itemspedidos'</h3>";
    if (Schema::hasColumn('itemspedidos', 'precio')) {
        echo "Ampliar columna <b>precio</b>... ";
        DB::statement("ALTER TABLE itemspedidos MODIFY COLUMN precio DECIMAL(15,2) NULL");
        echo "<span style='color:green'>OK</span><br>";
    }

    // 3. Tabla PRESENTACIONES (Stock/Precio producto)
    echo "<h3>3. Tabla 'presentaciones'</h3>";
    if (Schema::hasColumn('presentaciones', 'precio')) {
        echo "Ampliar columna <b>precio</b>... ";
        DB::statement("ALTER TABLE presentaciones MODIFY COLUMN precio DECIMAL(15,2) NULL");
        echo "<span style='color:green'>OK</span><br>";
    }

    echo "<hr>";
    echo "<h2 style='color:green'>¡LISTO! La base de datos ahora soporta montos superiores a $1.000.000</h2>";
    echo "<p>Intenta realizar el pedido nuevamente.</p>";

} catch (\Exception $e) {
    echo "<hr>";
    echo "<h2 style='color:red'>Error Crítico</h2>";
    echo "<p>Detalle: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
