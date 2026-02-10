<?php
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/../ferrindep/vendor/autoload.php';
$app = require_once __DIR__ . '/../ferrindep/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$columns = Schema::getColumnListing('presentaciones');
echo "<h1>Columns in 'presentaciones'</h1>";
echo "<ul>";
foreach ($columns as $col) {
    if (strpos($col, 'envio_gratis_zona') !== false) {
        echo "<li style='color: green; font-weight: bold;'>$col (FOUND)</li>";
    } else {
        echo "<li>$col</li>";
    }
}
echo "</ul>";
