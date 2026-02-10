<?php
require __DIR__ . '/../ferrindep/vendor/autoload.php';
$app = require_once __DIR__ . '/../ferrindep/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

echo "<h1>Shipping Bultos Verification</h1>";
echo "<style>table{border-collapse:collapse;margin:10px 0}td,th{border:1px solid #ccc;padding:8px}th{background:#f0f0f0}.pass{color:green;font-weight:bold}.fail{color:red;font-weight:bold}</style>";

// === TEST 1: Villa Ballester Flex lookup ===
echo "<h2>Test 1: Villa Ballester Flex Lookup</h2>";
$normalize = function ($str) {
    if (!$str)
        return '';
    $str = mb_strtolower($str, 'UTF-8');
    $str = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'], ['a', 'e', 'i', 'o', 'u', 'n', 'u'], $str);
    return trim($str);
};

$city = 'villa ballester';
$partido = 'san martin';
$normCity = $normalize($city);
$normPartido = $normalize($partido);

$mapeoFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', $normCity)->first();
if (!$mapeoFlex)
    $mapeoFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', $normCity)->first();
echo "<p>City '$normCity': " . ($mapeoFlex ? "✅ Found (tarifa_id: {$mapeoFlex->tarifa_id})" : "❌ Not found") . "</p>";

if (!$mapeoFlex && $normPartido) {
    $mapeoFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', $normPartido)->first();
    if (!$mapeoFlex)
        $mapeoFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', $normPartido)->first();
    echo "<p>Partido '$normPartido': " . ($mapeoFlex ? "✅ Found (tarifa_id: {$mapeoFlex->tarifa_id})" : "❌ Not found") . "</p>";
}

if (!$mapeoFlex && $normPartido && strpos($normPartido, 'martin') !== false) {
    $mapeoFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', '%san martin%')->first();
    echo "<p>Wildcard 'san martin': " . ($mapeoFlex ? "✅ Found (tarifa_id: {$mapeoFlex->tarifa_id})" : "❌ Not found") . "</p>";
}

$tarifaBase = 0;
$tarifaName = 'N/A';
if ($mapeoFlex) {
    $tarifa = $mapeoFlex->tarifa;
    if ($tarifa) {
        $tarifaBase = $tarifa->monto;
        $tarifaName = $tarifa->nombre;
    }
}
echo "<p><b>Tarifa Base: \${$tarifaBase} ({$tarifaName})</b></p>";

// === TEST 2: Simulate bultos calculation for different weights ===
echo "<h2>Test 2: Bultos Calculation Simulation</h2>";
echo "<table><tr><th>Items</th><th>Weight/item</th><th>Total Weight</th><th>Bultos</th><th>Tarifa Base</th><th>Expected Shipping</th></tr>";

$testCases = [
    ['qty' => 1, 'peso' => 26, 'desc' => '1 rollo'],
    ['qty' => 2, 'peso' => 26, 'desc' => '2 rollos'],
    ['qty' => 3, 'peso' => 26, 'desc' => '3 rollos'],
    ['qty' => 4, 'peso' => 26, 'desc' => '4 rollos'],
    ['qty' => 1, 'peso' => 10, 'desc' => '1 rollo liviano'],
    ['qty' => 3, 'peso' => 10, 'desc' => '3 rollos livianos'],
    ['qty' => 4, 'peso' => 10, 'desc' => '4 rollos livianos'],
];

foreach ($testCases as $tc) {
    $totalWeight = $tc['qty'] * $tc['peso'];
    $bultos = max(1, ceil($totalWeight / 30));
    $shipping = $tarifaBase * $bultos;
    echo "<tr><td>{$tc['desc']}</td><td>{$tc['peso']}kg</td><td>{$totalWeight}kg</td><td>{$bultos}</td><td>\${$tarifaBase}</td><td><b>\${$shipping}</b></td></tr>";
}
echo "</table>";

// === TEST 3: Check Presentacion weights in DB ===
echo "<h2>Test 3: Presentacion Weights (producto_id=6, 10x10mm 1mm)</h2>";
$pres = \App\Models\Presentacion::where('producto_id', 6)->get();
echo "<table><tr><th>ID</th><th>Nombre</th><th>Peso (kg)</th><th>Precio</th></tr>";
foreach ($pres as $p) {
    echo "<tr><td>{$p->id}</td><td>{$p->nombre}</td><td>{$p->peso}</td><td>\${$p->precio}</td></tr>";
}
echo "</table>";

// === TEST 4: Verify CartSessionController returns tarifa_base ===
echo "<h2>Test 4: CartSessionController Response Shape</h2>";
// Check that the controller file contains tarifa_base in both response paths
$controllerPath = base_path('app/Http/Controllers/CartSessionController.php');
$controllerContent = file_get_contents($controllerPath);
$flexHasTarifaBase = strpos($controllerContent, "'tarifa_base' => \$tarifa->monto") !== false;
$legacyHasTarifaBase = strpos($controllerContent, "'tarifa_base' => \$precioBase") !== false;
echo "<p>Flex response has tarifa_base: " . ($flexHasTarifaBase ? "<span class='pass'>✅ YES</span>" : "<span class='fail'>❌ NO</span>") . "</p>";
echo "<p>Legacy response has tarifa_base: " . ($legacyHasTarifaBase ? "<span class='pass'>✅ YES</span>" : "<span class='fail'>❌ NO</span>") . "</p>";

// === TEST 5: Check Checkout.vue has flexBaseTariff logic ===
echo "<h2>Test 5: Frontend Code Verification (app.js)</h2>";
$appJsPath = public_path('js/app.js');
if (file_exists($appJsPath)) {
    $appJs = file_get_contents($appJsPath);
    $hasFlexBaseTariff = strpos($appJs, 'flexBaseTariff') !== false;
    $hasCartUpdated = strpos($appJs, 'cart-updated') !== false;
    $hasOnCartUpdated = strpos($appJs, 'onCartUpdated') !== false;
    echo "<p>app.js contains 'flexBaseTariff': " . ($hasFlexBaseTariff ? "<span class='pass'>✅ YES</span>" : "<span class='fail'>❌ NO</span>") . "</p>";
    echo "<p>app.js contains 'cart-updated' event: " . ($hasCartUpdated ? "<span class='pass'>✅ YES</span>" : "<span class='fail'>❌ NO</span>") . "</p>";
    echo "<p>app.js contains 'onCartUpdated' method: " . ($hasOnCartUpdated ? "<span class='pass'>✅ YES</span>" : "<span class='fail'>❌ NO</span>") . "</p>";
    echo "<p>app.js size: " . number_format(strlen($appJs)) . " bytes</p>";
} else {
    echo "<p class='fail'>❌ app.js NOT FOUND at {$appJsPath}</p>";
}

// === TEST 6: Legacy path for Villa Ballester ===
echo "<h2>Test 6: Legacy Path Verification (Villa Ballester)</h2>";
$destino = \App\Models\Destino::where('nombre', 'LIKE', '%Ballester%')->first();
if ($destino) {
    echo "<p>Destino: ID={$destino->id}, nombre={$destino->nombre}</p>";
    $dz = \App\Models\Destinozona::where('destino_id', $destino->id)->first();
    if ($dz) {
        echo "<p>Zona ID: {$dz->zona_id}</p>";
        // Simulate legacy tarifa_base calculation
        $precioBase = 0;
        $precioEnvio = \App\Models\Pesozona::where('zona_id', $dz->zona_id)->where('peso', '>=', 29)->orderBy('peso', 'asc')->first();
        if ($precioEnvio) {
            $precioBase = $precioEnvio->costo;
            echo "<p>Base price (peso>=29): \${$precioBase} (peso bracket: {$precioEnvio->peso}kg)</p>";
        } else {
            $maximo = \App\Models\Pesozona::where('zona_id', $dz->zona_id)->orderBy('peso', 'desc')->first();
            if ($maximo) {
                $precioBase = $maximo->costo;
                echo "<p>Base price (max bracket): \${$precioBase} (peso bracket: {$maximo->peso}kg)</p>";
            }
        }

        echo "<p>Legacy bultos simulation:</p>";
        echo "<table><tr><th>Qty</th><th>Weight/item</th><th>Total</th><th>Bultos</th><th>Shipping</th></tr>";
        foreach ([1, 2, 3, 4] as $qty) {
            $w = 26 * $qty;
            $b = max(1, ceil($w / 30));
            $s = $precioBase * $b;
            echo "<tr><td>{$qty}</td><td>26kg</td><td>{$w}kg</td><td>{$b}</td><td>\${$s}</td></tr>";
        }
        echo "</table>";
    }
}

echo "<hr><p><i>Generated at " . date('Y-m-d H:i:s') . "</i></p>";
