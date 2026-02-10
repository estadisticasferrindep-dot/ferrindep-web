<?php
// Load Laravel environment
require __DIR__ . '/../ferrindep/vendor/autoload.php';
$app = require_once __DIR__ . '/../ferrindep/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Http\Controllers\ShippingCalculatorController;
use App\Models\Presentacion;
use App\Models\MapeoZonaFlex;
use App\Models\TarifaLogistica;

echo "<h1>Prueba Integral Sistema Flex</h1>";
echo "<pre>";

$controller = new ShippingCalculatorController();

// --- Helper Function for Testing ---
function testScenario($title, $city, $partido, $province, $presentacionId, $expectedType)
{
    echo "\n---------------------------------------------------\n";
    echo "SCENARIO: $title\n";
    echo "Location: $city, $partido, $province\n";

    // Simulate Request
    $req = new \Illuminate\Http\Request([
        'city' => $city,
        'partido' => $partido,
        'province' => $province,
        'items' => [
            ['id' => $presentacionId, 'qty' => 1] // Assuming simple cart structure for test
        ]
    ]);

    // We can't easily call 'calculate' directly if it relies on session/cart state heavily without mocking.
    // However, we can test the LOGIC core if we extract it or simulate the data 'calculate' uses.
    // For this test, let's Verify the DATA MAPPING first which is the core of Flex.

    // 1. Normalize
    $normCity = 'Testing'; // Logic inside controller is protected/private usually, so we re-implement logic check here for validaiton
    // Let's rely on Database checks basically mimicking the controller logic

    $normalized_search = strtolower(trim($partido));
    $normalized_search = str_replace(
        ['á', 'é', 'í', 'ó', 'ú', 'ñ'],
        ['a', 'e', 'i', 'o', 'u', 'n'],
        $normalized_search
    );

    echo "Normalized Search Term: '$normalized_search'\n";

    $flexZone = MapeoZonaFlex::where('nombre_busqueda', $normalized_search)->first();

    if ($flexZone) {
        $tarifa = TarifaLogistica::find($flexZone->tarifa_id);
        echo "MATCH FOUND: Flex Zone '{$flexZone->nombre_busqueda}' -> Tarifa '{$tarifa->nombre}' ($" . number_format($tarifa->monto, 2) . ")\n";

        // Check Product Free Shipping
        if ($presentacionId) {
            $prod = Presentacion::find($presentacionId);
            if ($prod) {
                echo "Product '{$prod->nombre}': envio_gratis_flex = " . ($prod->envio_gratis_flex ? 'TRUE' : 'FALSE') . "\n";

                $finalCost = ($prod->envio_gratis_flex) ? 0 : $tarifa->monto;
                echo "CALCULATED COST: $" . number_format($finalCost, 2) . "\n";
            } else {
                echo "Product ID $presentacionId not found.\n";
            }
        }

    } else {
        echo "NO FLEX MATCH -> Fallback to Legacy Logic.\n";
    }
}

// ------------------------------------------
// EXECUTE TESTS
// ------------------------------------------

// 1. Setup Test Data (Temporary) if needed, or rely on existing seeded data.
// We seeded: Ramos Mejia (Tarifa 2), Ezeiza (Tarifa 3), Cañuelas (Tarifa 4)

// 2. Get a product to test
$p = Presentacion::first(); // Grab any product
if (!$p)
    die("No presentations found to test.");

echo "Using Test Product ID: {$p->id} ({$p->nombre})\n";

// TEST 1: Match Standard (Ramos Mejia - Tarifa 2 ~6000)
testScenario(
    "Standard Flex Match",
    "Ramos Mejía",
    "Ramos Mejía",
    "Buenos Aires",
    $p->id,
    "FLEX"
);

// TEST 2: Match with Free Shipping Manual Override (Simulated)
// We temporarily set the flag on this product object in memory (or db if we revert)
$p->envio_gratis_flex = 1;
$p->save(); // Save to DB to test real persistence

testScenario(
    "Free Flex Match",
    "Cañuelas",
    "Cañuelas",
    "Buenos Aires",
    $p->id,
    "FREE"
);

// Revert flag
$p->envio_gratis_flex = 0;
$p->save();


// TEST 3: No Match (Córdoba - Should Fallback)
testScenario(
    "Fallback to Legacy",
    "Córdoba",
    "Capital",
    "Córdoba",
    $p->id,
    "LEGACY"
);

echo "</pre>";
