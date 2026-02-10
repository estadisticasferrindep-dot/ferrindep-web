<?php
// seeder_temp.php

$ferrindepPath = __DIR__ . '/../ferrindep';

if (file_exists($ferrindepPath . '/vendor/autoload.php')) {
    require $ferrindepPath . '/vendor/autoload.php';
} else {
    die("Error: No se encuentra autoload.php en $ferrindepPath/vendor/");
}

$app = require_once $ferrindepPath . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h1>Running Seeder (Update)...</h1>";

try {
    \Illuminate\Support\Facades\Artisan::call('db:seed', [
        '--class' => 'Database\Seeders\MapeoUbicacionesSeeder',
        '--force' => true
    ]);

    echo "<pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";
    echo "<h2 style='color:green'>Seeding Completed!</h2>";

} catch (\Throwable $e) {
    echo "<h2 style='color:red'>Error: " . $e->getMessage() . "</h2>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
