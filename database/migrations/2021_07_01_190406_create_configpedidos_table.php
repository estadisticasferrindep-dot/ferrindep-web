<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigpedidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configpedidos', function (Blueprint $table) {
            $table->id();

            $table->string('email1')->nullable();
            $table->string('email2')->nullable();
            $table->string('email3')->nullable();

            $table->longText('parrafo_envio_fabrica')->nullable();
            $table->longText('parrafo_envio_interior')->nullable();
            $table->longText('parrafo_envio_caba')->nullable();
            $table->longText('parrafo_envio_expreso')->nullable();


            $table->integer('costo_envio_fabrica')->nullable();
            $table->integer('costo_envio_interior')->nullable();
            $table->integer('costo_envio_caba')->nullable();
            $table->integer('costo_envio_expreso')->nullable();


            $table->longText('parrafo_efectivo')->nullable();
            $table->longText('parrafo_transferencia')->nullable();
            $table->longText('parrafo_mp')->nullable();

            $table->integer('descuento_efectivo')->nullable();
            $table->integer('descuento_transferencia')->nullable();
            $table->integer('descuento_mp')->nullable();

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
        Schema::dropIfExists('configpedidos');
    }
}
