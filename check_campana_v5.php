<?php
// Display errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Diagnostic V5 - " . date('Y-m-d H:i:s') . "</h1>";

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
}

// 1. Check Tables
echo "<h2>Table Search (destino%)</h2>";
try {
    $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES LIKE "destino%"');
    echo "<pre>";
    print_r($tables);
    echo "</pre>";

    // Extract table names
    $tableNames = [];
    foreach ($tables as $t) {
        $vars = get_object_vars($t);
        $tableNames[] = array_values($vars)[0];
    }

    // 2. Dump Content for ID 39
    echo "<h2>Content Check (ID: 39)</h2>";
    foreach ($tableNames as $table) {
        if ($table == 'destinos')
            continue; // Skip main table

        echo "<h3>Table: $table</h3>";
        $rows = \Illuminate\Support\Facades\DB::table($table)->where('destino_id', 39)->get();
        if ($rows->count() > 0) {
            echo "<pre>";
            print_r($rows->toArray());
            echo "</pre>";
        } else {
            echo "No rows for destino_id = 39.<br>";
        }

        echo "<h3>Table: $table (For Zarate ID 83)</h3>";
        $rowsZ = \Illuminate\Support\Facades\DB::table($table)->where('destino_id', 83)->get();
        if ($rowsZ->count() > 0) {
            echo "Zarate found: Count " . $rowsZ->count() . "<br>";
        } else {
            echo "Zarate NOT found.<br>";
        }
    }

} catch (\Exception $e) {
    echo "DB Error: " . $e->getMessage();
}
