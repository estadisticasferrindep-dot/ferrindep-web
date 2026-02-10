<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('orden')->nullable();
            $table->boolean('show')->nullable()->default(false);
            $table->boolean('destacado')->nullable()->default(false);
            
            $table->integer('categoria_id')->nullable();
            $table->integer('medida_id')->nullable();
            $table->integer('espesor_id')->nullable();

            // $table->string('nombre')->nullable();
            $table->string('imagen')->nullable();
            $table->longText('descripcion')->nullable();
            $table->longText('caracteristicas')->nullable();
            $table->longText('usos')->nullable();


            $table->boolean('oferta')->nullable()->default(false);
            // $table->integer('precio')->nullable();
            // $table->integer('precio_anterior')->nullable();

            // $table->integer('precio_gremio')->nullable();
            // $table->integer('precio_anterior_gremio')->nullable();

            // $table->integer('precio_especial')->nullable();
            // $table->integer('precio_anterior_especial')->nullable();

            // $table->integer('precio_mayorista')->nullable();
            // $table->integer('precio_anterior_mayorista')->nullable();

            $table->integer('relacionado_1')->nullable();
            $table->integer('relacionado_2')->nullable();
            $table->integer('relacionado_3')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productos');
    }
}