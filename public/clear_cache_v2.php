<?php
// clear_cache_v2.php

require __DIR__ . '/../ferrindep/vendor/autoload.php';
$app = require_once __DIR__ . '/../ferrindep/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h1>Limpiando Cache (V2)...</h1>";

try {
    echo "<h2>Artisan Commands</h2>";
    Illuminate\Support\Facades\Artisan::call('optimize:clear');
    echo "<pre>" . Illuminate\Support\Facades\Artisan::output() . "</pre>";

    Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "<pre>" . Illuminate\Support\Facades\Artisan::output() . "</pre>";

} catch (Exception $e) {
    echo "Error calling Artisan: " . $e->getMessage() . "<br>";
}

echo "<h2>Opcache Reset</h2>";
if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "<div style='background:green;color:white;padding:10px'><strong>OPCACHE RESET SUCCESSFULLY!</strong></div>";
    } else {
        echo "<div style='background:red;color:white;padding:10px'>OPCACHE RESET FAILED (Returned false)</div>";
    }
} else {
    echo "<div style='background:orange;padding:10px'>OPCACHE Function Not Found</div>";
}
