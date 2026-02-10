<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemspedidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itemspedidos', function (Blueprint $table) {
            $table->id();
            $table->string('pedido_id')->nullable();

            $table->integer('producto_id')->nullable();
            $table->string('nombre')->nullable();
            $table->string('color')->nullable();

            $table->integer('cantidad')->nullable();
            $table->decimal('precio')->nullable();

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
        Schema::dropIfExists('itemspedidos');
    }
}
