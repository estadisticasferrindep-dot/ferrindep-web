<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Debug Log Reader v4</h1>";

$path = __DIR__ . '/../ferrindep/storage/logs/laravel.log';

if (!file_exists($path)) {
    // Try fallback
    $path = __DIR__ . '/../storage/logs/laravel.log';
}

if (file_exists($path)) {
    echo "<h2>LOG ENCONTRADO: " . $path . "</h2>";
    echo "<h3>Last 10KB of Log:</h3>";

    $fp = fopen($path, 'r');
    if ($fp) {
        $size = filesize($path);
        $readSize = 50000; // Increased to 50KB
        $offset = max(0, $size - $readSize);

        fseek($fp, $offset);
        $content = fread($fp, $readSize);
        fclose($fp);

        echo "<pre style='background: #222; color: #eee; padding: 15px; border-radius: 5px; font-family: monospace; white-space: pre-wrap;'>";
        echo htmlspecialchars($content);
        echo "</pre>";
    } else {
        echo "Could not open file.";
    }
} else {
    echo "<h2 style='color:red'>NO SE ENCONTRO EL LOG</h2>";
    echo "<h3>Directorio Actual: " . __DIR__ . "</h3>";
}
?>