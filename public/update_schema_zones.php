<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/../ferrindep/vendor/autoload.php';
$app = require_once __DIR__ . '/../ferrindep/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// --- SCRIPT START ---

echo "<h1>Updating Schema: Presentaciones Zones</h1>";

$table = 'presentaciones';
$columns = [
    'envio_gratis_zona_1',
    'envio_gratis_zona_2',
    'envio_gratis_zona_3',
    'envio_gratis_zona_4'
];

if (Schema::hasTable($table)) {
    Schema::table($table, function (Blueprint $table) use ($columns) {
        foreach ($columns as $col) {
            if (!Schema::hasColumn('presentaciones', $col)) {
                $table->boolean($col)->default(0);
                echo "<div>[CREATED] Column '$col' created.</div>";
            } else {
                echo "<div>[SKIPPED] Column '$col' already exists.</div>";
            }
        }
    });
} else {
    echo "<div>[ERROR] Table '$table' not found.</div>";
}

echo "<h3>Done.</h3>";
