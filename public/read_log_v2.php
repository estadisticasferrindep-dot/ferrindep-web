<?php
echo "<h1>Diagnostic Tool V2</h1>";
echo "<p>Server Time: " . date('Y-m-d H:i:s') . " (" . date_default_timezone_get() . ")</p>";
echo "<p>Script Path: " . __FILE__ . "</p>";

$filesToCheck = [
    '../app/Models/Presentacion.php',
    '../ferrindep/app/Models/Presentacion.php',
    '../../ferrindep/app/Models/Presentacion.php',
    '/home/ferrinde/ferrindep/app/Models/Presentacion.php'
];

echo "<h2>Checking Presentacion.php</h2>";
foreach ($filesToCheck as $path) {
    echo "<p>Checking $path: ";
    if (file_exists($path)) {
        echo "<b>FOUND</b> (Modified: " . date('Y-m-d H:i:s', filemtime($path)) . ")";
        $content = file_get_contents($path);
        if (strpos($content, 'function producto') !== false) {
            echo " [CONTAINS function producto]";
        } else {
            echo " [MISSING function producto]";
        }
    } else {
        echo "Not found";
    }
    echo "</p>";
}

echo "<h2>Checking Logs</h2>";
$logPaths = [
    '../storage/logs/laravel.log',
    '../ferrindep/storage/logs/laravel.log',
    '/home/ferrinde/ferrindep/storage/logs/laravel.log',
    '../../ferrindep/storage/logs/laravel.log'
];

foreach ($logPaths as $path) {
    if (file_exists($path)) {
        echo "<h3>Log found at: $path</h3>";
        echo "<p>Size: " . filesize($path) . " bytes</p>";
        $lines = file($path);
        $lastLines = array_slice($lines, -50);
        echo "<pre style='background:#eee; padding:10px; font-size:11px'>" . htmlspecialchars(implode("", $lastLines)) . "</pre>";
    }
}
