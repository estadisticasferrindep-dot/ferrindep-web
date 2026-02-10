<?php
// test_shipping_logic.php

define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Helpers
$normalize = function ($str) {
    $str = mb_strtolower($str, 'UTF-8');
    return trim(str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'], ['a', 'e', 'i', 'o', 'u', 'n', 'u'], $str));
};

function testMatch($city, $partido, $region, $dbName = null)
{
    global $normalize;
    echo "\n--- Testing: City='$city', Partido='$partido', Region='$region' ---\n";
    $normCity = $normalize($city);
    $normPartido = $normalize($partido);

    // WebController Logic Simulation
    $foundFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', $normCity)->first();
    if (!$foundFlex) {
        $foundFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', '%' . $normCity . '%')->first();
    }

    // Fallback Partido
    if (!$foundFlex && $normPartido) {
        $foundFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', $normPartido)->first();
        if (!$foundFlex) {
            $foundFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', '%' . $normPartido . '%')->first();
        }
    }

    if ($foundFlex) {
        echo "[WebController] FOUND Flex: ID {$foundFlex->id} ({$foundFlex->nombre_busqueda}) - Tarifa: " . ($foundFlex->tarifa_id ?? 'N/A') . "\n";
    } else {
        echo "[WebController] NOT FOUND Flex.\n";
    }

    // ShippingCalculator Logic Simulation
    $scFlex = null;
    if ($normCity) {
        $scFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', $normCity)->first(); // Exact
        if (!$scFlex) {
            $scFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', $normCity)->first(); // Like (No wildcard)
        }
    }
    if (!$scFlex && $normPartido) {
        $scFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', $normPartido)->first();
        if (!$scFlex) {
            $scFlex = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', $normPartido)->first();
        }
    }

    if ($scFlex) {
        echo "[ShippingCalc ] FOUND Flex: ID {$scFlex->id} ({$scFlex->nombre_busqueda})\n";
    } else {
        echo "[ShippingCalc ] NOT FOUND Flex.\n";
    }

    // CHECK DB CONTENTS for sanity
    if ($dbName) {
        $check = \App\Models\MapeoZonaFlex::where('nombre_busqueda', 'LIKE', "%$dbName%")->get();
        echo "[DB Check] Entries matching '%$dbName%': " . $check->count() . "\n";
        foreach ($check as $c) {
            echo "   - ID: {$c->id}, Name: '{$c->nombre_busqueda}'\n";
        }
    }
}

// Test Cases based on User Report
testMatch("Lomas del Mirador", "La Matanza", "Buenos Aires", "matanza");
testMatch("San Miguel", "San Miguel", "Buenos Aires", "miguel");
testMatch("Ciudad Autónoma de Buenos Aires", "", "Ciudad Autónoma de Buenos Aires", "capital"); // CABA usually has empty party? or similar
testMatch("Palermo", "Comuna 14", "Ciudad Autónoma de Buenos Aires", "palermo");

