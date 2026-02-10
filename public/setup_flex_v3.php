<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../ferrindep/vendor/autoload.php';
$app = require_once __DIR__ . '/../ferrindep/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<h1>Setup Flex Configuration V3</h1>";

try {
    // Disable FK checks
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    // 1. Create table 'tarifas_logistica'
    if (!Schema::hasTable('tarifas_logistica')) {
        Schema::create('tarifas_logistica', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->decimal('monto', 10, 2);
            $table->timestamps();
        });
        echo "Created table 'tarifas_logistica'.<br>";
    } else {
        echo "Table 'tarifas_logistica' already exists.<br>";
    }

    // 2. Create table 'mapeo_zonas_flex'
    if (!Schema::hasTable('mapeo_zonas_flex')) {
        Schema::create('mapeo_zonas_flex', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_busqueda')->index(); // Partido o Localidad
            $table->unsignedBigInteger('tarifa_id');
            $table->foreign('tarifa_id')->references('id')->on('tarifas_logistica')->onDelete('cascade');
            $table->timestamps();
        });
        echo "Created table 'mapeo_zonas_flex'.<br>";
    } else {
        echo "Table 'mapeo_zonas_flex' already exists.<br>";
    }

    // 3. Add column 'envio_gratis_flex' to 'presentaciones'
    if (Schema::hasTable('presentaciones') && !Schema::hasColumn('presentaciones', 'envio_gratis_flex')) {
        Schema::table('presentaciones', function (Blueprint $table) {
            $table->boolean('envio_gratis_flex')->default(false)->after('precio');
        });
        echo "Added column 'envio_gratis_flex' to 'presentaciones'.<br>";
    } else {
        echo "Column 'envio_gratis_flex' already exists or table missing.<br>";
    }

    // 4. Seed Data
    // Clear existing data to ensure clean state based on prompt
    DB::table('mapeo_zonas_flex')->truncate();
    DB::table('tarifas_logistica')->truncate();

    // Re-enable FK checks
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    $tarifas = [
        1 => ['nombre' => 'Tarifa 1', 'monto' => 5000],
        2 => ['nombre' => 'Tarifa 2', 'monto' => 6000],
        3 => ['nombre' => 'Tarifa 3', 'monto' => 7000],
        4 => ['nombre' => 'Tarifa 4', 'monto' => 9000],
    ];

    foreach ($tarifas as $id => $data) {
        DB::table('tarifas_logistica')->insert([
            'id' => $id,
            'nombre' => $data['nombre'],
            'monto' => $data['monto'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    echo "Seeded Tarifas.<br>";

    $zonas = [
        1 => ['CABA', 'VICENTE LOPEZ', 'SAN ISIDRO', 'SAN MARTIN', '3 DE FEBRERO', 'TRES DE FEBRERO', 'GENERAL SAN MARTIN'],
        2 => ['TIGRE', 'MALVINAS ARGENTINAS', 'HURLINGHAM', 'ITUZAINGO', 'SAN JUSTO', 'RAMOS MEJIA', 'HAEDO', 'LOMAS DE ZAMORA', 'LANUS', 'AVELLANEDA'],
        3 => ['ESCOBAR', 'JOSE C PAZ', 'MERLO', 'MORENO', 'GREGORIO DE LAFERRERE', 'VIRREY DEL PINO', 'ESTEBAN ECHEVERRIA', 'ALMIRANTE BROWN', 'QUILMES', 'FLORENCIO VARELA', 'BERAZATEGUI', 'EZEIZA'],
        4 => ['ZARATE', 'CAMPANA', 'PILAR', 'LUJAN', 'GENERAL RODRIGUEZ', 'MARCOS PAZ', 'CANUELAS', 'CAÑUELAS', 'SAN VICENTE', 'LA PLATA', 'BERISSO', 'ENSENADA'],
    ];

    foreach ($zonas as $tarifaId => $lugares) {
        foreach ($lugares as $lugar) {
            DB::table('mapeo_zonas_flex')->insert([
                'nombre_busqueda' => mb_strtolower($lugar, 'UTF-8'), // Store normalized
                'tarifa_id' => $tarifaId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    echo "Seeded Mapeo Zonas.<br>";

    echo "<h3>Setup Completed Successfully! (V3)</h3>";

} catch (\Exception $e) {
    echo "<h3>Error: " . $e->getMessage() . "</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
