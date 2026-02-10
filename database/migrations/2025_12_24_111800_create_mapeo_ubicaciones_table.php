<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMapeoUbicacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mapeo_ubicaciones', function (Blueprint $table) {
            $table->id();
            $table->string('ciudad_detectada'); // Ej: "Villa Ballester"
            $table->integer('destino_id');
            $table->timestamps();

            // Foreign key relation
            $table->foreign('destino_id')->references('id')->on('destinos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mapeo_ubicaciones');
    }
}
