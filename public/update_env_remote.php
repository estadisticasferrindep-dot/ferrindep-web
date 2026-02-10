<?php
// Script to update .env and clear config
require __DIR__ . '/../ferrindep/vendor/autoload.php';
$app = require_once __DIR__ . '/../ferrindep/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\Artisan;

$envPath = __DIR__ . '/../ferrindep/.env';
$key = 'GOOGLE_MAPS_KEY';
$newValue = 'AIzaSyDssmltsmUd-dlEzYjO5VZG72km7ZKtbdU';

if (file_exists($envPath)) {
    $content = file_get_contents($envPath);

    // Check if key exists
    if (strpos($content, $key) !== false) {
        // Replace existing
        $pattern = "/^{$key}=.*/m";
        $replacement = "{$key}={$newValue}";
        $newContent = preg_replace($pattern, $replacement, $content);
        file_put_contents($envPath, $newContent);
        echo "Updated existing {$key}.<br>";
    } else {
        // Append new
        file_put_contents($envPath, "\n{$key}={$newValue}\n", FILE_APPEND);
        echo "Appended new {$key}.<br>";
    }
} else {
    echo ".env file not found at {$envPath}<br>";
}

// Clear Config
try {
    Artisan::call('config:clear');
    echo "Config cleared.<br>";
} catch (\Exception $e) {
    echo "Error clearing config: " . $e->getMessage() . "<br>";
}

// Verify
$finalContent = file_get_contents($envPath);
if (strpos($finalContent, $newValue) !== false) {
    echo "VERIFICATION: Key found in .env (Success).";
} else {
    echo "VERIFICATION: Key NOT found (Failed).";
}
