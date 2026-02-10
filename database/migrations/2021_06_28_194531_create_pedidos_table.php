<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->boolean('esta_pago')->nullable();

            $table->integer('usuario_id')->nullable();
            $table->string('usuario_email')->nullable();
            $table->string('usuario_nombre')->nullable();
            $table->string('usuario_empresa')->nullable();
            $table->string('usuario_telefono')->nullable();
            $table->string('usuario_cuit')->nullable();
            $table->string('usuario_direccion')->nullable();
            $table->string('tipo_cliente')->nullable();
            
            $table->decimal('descuento_total')->nullable();
            // $table->decimal('iva')->nullable();
            $table->decimal('subtotal')->nullable();
            $table->decimal('total')->nullable();

            $table->longText('envio')->nullable();
            $table->longText('pago')->nullable();
                
            $table->longText('provincia')->nullable();
            $table->longText('direccion')->nullable();
            $table->longText('localidad')->nullable();
            $table->longText('celular')->nullable();
            $table->longText('cp')->nullable();
            $table->longText('email')->nullable();
            $table->longText('dni')->nullable();
            $table->longText('nombre')->nullable();
            $table->longText('mensaje')->nullable();

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
        Schema::dropIfExists('pedidos');
    }
}
