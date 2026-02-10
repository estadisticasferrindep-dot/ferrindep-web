<?php

// Adjust path to storage/logs/laravel.log
// Assumes public/read_log.php is in public_html
// and laravel is in ../ferrindep or ../
// Log path: ../storage/logs/laravel.log OR ../ferrindep/storage/logs/laravel.log

$paths = [
    __DIR__ . '/../storage/logs/laravel.log',
    __DIR__ . '/../ferrindep/storage/logs/laravel.log',
    __DIR__ . '/../app/storage/logs/laravel.log',
];

$logFile = null;
foreach ($paths as $p) {
    if (file_exists($p)) {
        $logFile = $p;
        break;
    }
}

if (!$logFile) {
    die("<h1>No encontré el log</h1>Rutas probadas:<br>" . implode("<br>", $paths));
}


echo "<h1>Log: $logFile (VERSION 2)</h1>";
echo "<h3>Server Time: " . date('Y-m-d H:i:s') . " (Timezone: " . date_default_timezone_get() . ")</h3>";
$modelPath = realpath(__DIR__ . '/../ferrindep/app/Models/Presentacion.php');
if ($modelPath && file_exists($modelPath)) {
    echo "<h3>Presentacion.php Mod Time: " . date('Y-m-d H:i:s', filemtime($modelPath)) . "</h3>";
    echo "<h3>Presentacion.php Path: $modelPath</h3>";
} else {
    echo "<h3>Presentacion.php NOT FOUND at ../ferrindep/app/Models/Presentacion.php</h3>";
}

echo "<pre>";

// Read last 100 lines
$lines = file($logFile);
$total = count($lines);
$start = max(0, $total - 200);

for ($i = $start; $i < $total; $i++) {
    echo htmlspecialchars($lines[$i]);
}

echo "</pre>";
