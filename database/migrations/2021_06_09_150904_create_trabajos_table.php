<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrabajosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trabajos', function (Blueprint $table) {
            $table->id();
            $table->string('orden')->nullable();
            $table->boolean('show')->nullable()->default(false);
            $table->boolean('home')->nullable()->default(false);
            
            $table->string('nombre')->nullable();
            $table->string('imagen')->nullable();
            $table->longText('tabla')->nullable();

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
        Schema::dropIfExists('trabajos');
    }
}
