<?php

use App\Models\MapeoZonaFlex;
use Illuminate\Support\Facades\Session;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
// $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); // Console kernel might not have session access the same way web does, but for DB it's fine.
// For session debugging, we might need to run this via web route, but let's check DB normalization first.
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// We can't easily see the user's session from CLI without their cookie. 
// BUT we can check what the DB has for 'General San Martin'.

echo "--- DB NORMALIZATION CHECK ---\n";
$search = ['General San Martin', 'General San Martín', 'GENERAL SAN MARTIN', 'San Martin'];

foreach ($search as $s) {
    $found = MapeoZonaFlex::where('nombre_ciudad_partido', $s)->first();
    echo "Searching '{$s}': " . ($found ? "FOUND (ID: {$found->id})" : "NOT FOUND") . "\n";
}

echo "\n--- ALL FLEX ZONES ---\n";
$all = MapeoZonaFlex::all();
foreach ($all as $z) {
    echo "- '{$z->nombre_ciudad_partido}' (Zona Flex: {$z->zona_flex_id})\n";
}
