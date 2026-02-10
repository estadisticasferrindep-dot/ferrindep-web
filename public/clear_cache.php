<?php

require __DIR__ . '/../ferrindep/vendor/autoload.php';
$app = require_once __DIR__ . '/../ferrindep/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h1>Limpiando Cache...</h1>";

try {
    Illuminate\Support\Facades\Artisan::call('optimize:clear');
    echo "<pre>" . Illuminate\Support\Facades\Artisan::output() . "</pre>";

    Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "<pre>" . Illuminate\Support\Facades\Artisan::output() . "</pre>";

    Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "<pre>" . Illuminate\Support\Facades\Artisan::output() . "</pre>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "<br><strong>OPCACHE RESET SUCCESSFULLY!</strong>";
} else {
    echo "<br>OPCACHE function not found.";
}
