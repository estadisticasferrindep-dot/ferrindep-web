<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Check Pricing V1</h1>";

// Bootstrap
$paths = [
    __DIR__ . '/../ferrindep/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
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
} catch (\Throwable $e) { /* Ignore bootstrap errors if partly loaded */
}

try {
    echo "<h2>Listing Tables</h2>";
    $patterns = ['mapeo%', 'zona%', 'tarif%', 'precio%', 'costo%', 'variable%'];
    $foundTables = [];

    foreach ($patterns as $pat) {
        $res = \Illuminate\Support\Facades\DB::select("SHOW TABLES LIKE ?", [$pat]);
        foreach ($res as $r) {
            $vals = array_values((array) $r);
            $foundTables[] = $vals[0];
        }
    }

    $foundTables = array_unique($foundTables);
    if (empty($foundTables)) {
        echo "No relevant tables found.<br>";
    } else {
        foreach ($foundTables as $t) {
            echo "<h3>Table: $t</h3>";
            try {
                $rows = \Illuminate\Support\Facades\DB::select("SELECT * FROM $t LIMIT 2");
                if (empty($rows))
                    echo "Empty.<br>";
                else {
                    echo "<pre>";
                    print_r($rows);
                    echo "</pre>";
                }
            } catch (\Exception $e) {
                echo "Error: " . $e->getMessage() . "<br>";
            }
        }
    }

} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage();
}
