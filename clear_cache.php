<?php
// clear_cache.php
// This script clears Laravel caches using Artisan commands.

define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

try {
    echo "<h1>Clearing Caches...</h1>";

    echo "<p>Running: view:clear</p>";
    Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "<pre>" . Illuminate\Support\Facades\Artisan::output() . "</pre>";

    echo "<p>Running: cache:clear</p>";
    Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "<pre>" . Illuminate\Support\Facades\Artisan::output() . "</pre>";

    echo "<p>Running: config:clear</p>";
    Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "<pre>" . Illuminate\Support\Facades\Artisan::output() . "</pre>";

    echo "<h2>Done!</h2>";

} catch (\Exception $e) {
    echo "<h2 style='color:red;'>Error: " . $e->getMessage() . "</h2>";
}