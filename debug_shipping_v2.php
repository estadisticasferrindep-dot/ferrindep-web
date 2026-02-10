<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Debug Shipping V2</h1>";

// Bootstrap
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

try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle($request = Illuminate\Http\Request::capture());
} catch (\Throwable $e) {
    echo "Bootstrap Error: " . $e->getMessage();
    exit;
}

try {
    $pid = 59;
    echo "<h2>Product $pid (Raw DB)</h2>";
    $p = \Illuminate\Support\Facades\DB::table('productos')->where('id', $pid)->first();
    if ($p) {
        echo "Product: {$p->nombre}<br>";

        $pres = \Illuminate\Support\Facades\DB::table('presentaciones')->where('producto_id', $pid)->get();
        foreach ($pres as $pr) {
            echo "Presentation ID: {$pr->id} | Name: {$pr->nombre} | Weight: {$pr->peso_unitario}<br>";

            // Calc
            $w = (float) $pr->peso_unitario;
            $qty = 1;
            $totalW = $w * $qty;
            $bultos = ceil($totalW / 30);
            if ($bultos < 1)
                $bultos = 1;
            echo "-> Bultos: $bultos <br>";
        }
    } else {
        echo "Product not found.<br>";
    }

    echo "<h2>Zone 4 (Raw DB)</h2>";
    $zone = \Illuminate\Support\Facades\DB::table('mapeo_zona_flexes')->where('id', 4)->first();
    if ($zone) {
        echo "<pre>";
        print_r($zone);
        echo "</pre>";

        // Try to interpret columns
        // Assuming 'cp' or 'precio' or something.
    } else {
        echo "Zone 4 not found.<br>";
    }

    // Check parameters table specifically
    echo "<h2>Variables Globales</h2>";
    $vars = \Illuminate\Support\Facades\DB::table('variables_globales')->first();
    if ($vars) {
        echo "<pre>";
        print_r($vars);
        echo "</pre>";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
