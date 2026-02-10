<?php
// Display errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Shipping Debugger - Product 59 - Zone 4</h1>";

// Bootstrap Laravel
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
    // 1. Get Product
    $productId = 59;
    echo "<h2>Product Details (ID: $productId)</h2>";
    $product = \App\Models\Producto::with(['presentaciones', 'marca', 'categoria'])->find($productId);

    if (!$product) {
        echo "Product not found.";
    } else {
        echo "Name: " . $product->nombre . "<br>";
        echo "<h3>Presentations:</h3>";
        foreach ($product->presentaciones as $pres) {
            echo "ID: " . $pres->id . " | Name: " . $pres->nombre . " | Weight: <b>" . $pres->peso_unitario . " kg</b><br>";

            // Calculate Logic for this Presentation
            $weight = (float) $pres->peso_unitario;
            $qty = 1; // Assuming 1 unit
            $totalWeight = $weight * $qty;
            $bultos = ceil($totalWeight / 30);
            if ($bultos < 1)
                $bultos = 1;

            echo "&nbsp;&nbsp;-> Bultos (per unit): $bultos <br>";
        }
    }

    // 2. Get Zone 4 Price
    echo "<h2>Zone 4 Pricing</h2>";
    $zoneId = 4;
    $zoneMapping = \App\Models\MapeoZonaFlex::where('id', $zoneId)->first();

    if ($zoneMapping) {
        // Assuming MapeoZonaFlex has columns like 'tarifa_1', 'tarifa_2', or just 'precio' mapped somehow, 
        // OR we check the 'shipping_table' logic.
        // Let's dump the row to see the structure
        echo "<pre>";
        print_r($zoneMapping->toArray());
        echo "</pre>";

        // Check if there is a 'valor_referencia' or if it maps to a tariff code
        // For Ferrindep, usually it's tarifa_1, tarifa_2, etc. in a 'CostosEnvio' model or similar?
        // Let's search for Costos or Tarifas

        $tarifas = \Illuminate\Support\Facades\DB::table('param_tarifas')->get();
        // Adjust table name if needed. 
        // Based on previous contexts, prices might be in 'mapeo_zona_flexes' directly or linked.

    } else {
        echo "Zone 4 not found in MapeoZonaFlex.<br>";
    }

    // Check Global Prices / Tarifas table if exists
    echo "<h2>Tarifas Params</h2>";
    try {
        $params = \Illuminate\Support\Facades\DB::table('param_tarifas')->get();
        echo "<pre>";
        print_r($params->toArray());
        echo "</pre>";
    } catch (\Exception $e) {
        echo "param_tarifas table error: " . $e->getMessage() . "<br>";

        // Try 'variables_globales' or simliar
        try {
            $vars = \Illuminate\Support\Facades\DB::table('variables_globales')->get();
            echo "Variables Globales:<pre>";
            print_r($vars->toArray());
            echo "</pre>";
        } catch (\Exception $ex) {
            echo "No variables_globales.<br>";
        }
    }


} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
