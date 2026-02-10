<?php
// Check what app.js is on the server
$path = __DIR__ . '/js/app.js';
echo "Path: $path\n";
echo "Exists: " . (file_exists($path) ? 'YES' : 'NO') . "\n";
echo "Size: " . filesize($path) . " bytes\n";
echo "Modified: " . date('Y-m-d H:i:s', filemtime($path)) . "\n";

// Check for our markers
$content = file_get_contents($path);
echo "Contains 'flexBaseTariff': " . (strpos($content, 'flexBaseTariff') !== false ? 'YES' : 'NO') . "\n";
echo "Contains 'cart-updated': " . (strpos($content, 'cart-updated') !== false ? 'YES' : 'NO') . "\n";
echo "Contains 'onCartUpdated': " . (strpos($content, 'onCartUpdated') !== false ? 'YES' : 'NO') . "\n";
echo "Contains '_locationResolved': " . (strpos($content, '_locationResolved') !== false ? 'YES' : 'NO') . "\n";

// List all js files with sizes and dates
echo "\n\nAll files in /js/:\n";
$files = glob(__DIR__ . '/js/*');
foreach ($files as $f) {
    echo basename($f) . " | " . filesize($f) . " bytes | " . date('Y-m-d H:i:s', filemtime($f)) . "\n";
}
