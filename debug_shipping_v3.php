<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Debug Shipping V3 (Discovery)</h1>";

try {
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

    // Explicitly confirm connection
    echo "<h2>DB Connection</h2>";
    try {
        $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
        echo "PDO OK.<br>";
    } catch (\Throwable $e) {
        echo "PDO Fail: " . $e->getMessage();
        exit;
    }

    echo "<h2>Table Search</h2>";
    $searches = ['mapeo%', 'zona%', 'tarif%', 'cost%', 'var%'];
    foreach ($searches as $pattern) {
        $tables = \Illuminate\Support\Facades\DB::select("SHOW TABLES LIKE ?", [$pattern]);
        echo "<h3>Pattern: $pattern</h3>";
        if (!empty($tables)) {
            echo "<pre>";
            print_r($tables);
            echo "</pre>";
        } else {
            echo "No matches.<br>";
        }
    }

    // Check specific tables if found
    echo "<h2>Key Table Contents</h2>";
    $candidates = ['mapeo_zona_flexes', 'mapeo_zonas', 'tarifas', 'costos_envio', 'variables_globales', 'param_tarifas'];
    foreach ($candidates as $t) {
        // Check exist
        $exists = \Illuminate\Support\Facades\DB::select("SHOW TABLES LIKE ?", [$t]);
        if (!empty($exists)) {
            echo "<h3>$t (First Row)</h3>";
            try {
                $row = \Illuminate\Support\Facades\DB::select("SELECT * FROM $t LIMIT 1");
                if (!empty($row)) {
                    echo "<pre>";
                    print_r($row[0]);
                    echo "</pre>";
                } else {
                    echo "Empty table.<br>";
                }
            } catch (\Throwable $e) {
                echo "Error reading $t: " . $e->getMessage() . "<br>";
            }
        }
    }

} catch (\Throwable $e) {
    echo "Runtime Throwable: " . $e->getMessage();
}
