<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Debug Log Reader v2</h1>";

$paths = [
    '../ferrindep/storage/logs/laravel.log',
    '../storage/logs/laravel.log',
    './storage/logs/laravel.log',
    '/home/ferrindep/storage/logs/laravel.log',
    '../../ferrindep/storage/logs/laravel.log',
    __DIR__ . '/../ferrindep/storage/logs/laravel.log',
    __DIR__ . '/../storage/logs/laravel.log',
];

$found = false;
foreach ($paths as $path) {
    if (file_exists($path)) {
        echo "<h2>Found Log: $path</h2>";
        $lines = file($path);
        // Get last 200 lines
        $last = array_slice($lines, -200);
        echo "<pre style='background:#eee; padding:10px; overflow:auto;'>";
        foreach ($last as $line) {
            echo htmlspecialchars($line);
        }
        echo "</pre>";
        $found = true;
        break; // Stop after finding one
    }
}

if (!$found) {
    echo "<h2>Log file not found. Checked:</h2><ul>";
    foreach ($paths as $path) {
        echo "<li>" . $path . " (Abs: " . realpath($path) . ")</li>";
    }
    echo "</ul>";
    echo "<h3>Current Directory: " . __DIR__ . "</h3>";
    echo "<h3>Directory Listing:</h3><pre>";
    print_r(scandir(__DIR__));
    echo "</pre>";
}
