<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePresentacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('presentaciones', function (Blueprint $table) {
            $table->id();

            $table->boolean('show')->nullable()->default(false);
            
            $table->integer('stock')->nullable();
            $table->integer('producto_id')->nullable();
            $table->integer('limite')->nullable();
            $table->integer('metros')->nullable();


            $table->string('nombre')->nullable();

            // $table->boolean('oferta')->nullable()->default(false);
            $table->integer('precio')->nullable();
            $table->integer('precio_anterior')->nullable();

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
        Schema::dropIfExists('presentaciones');
    }
}
