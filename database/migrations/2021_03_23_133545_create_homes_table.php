<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('homes', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable();
            $table->string('logo_footer')->nullable();
            $table->longText('frase_footer')->nullable();


            $table->string('seccion_foto1')->nullable();
            $table->longText('seccion_titulo1')->nullable();
            $table->longText('seccion_texto1')->nullable();

            $table->string('seccion_foto2')->nullable();
            $table->longText('seccion_titulo2')->nullable();
            $table->longText('seccion_texto2')->nullable();

            $table->string('seccion_foto3')->nullable();
            $table->longText('seccion_titulo3')->nullable();
            $table->longText('seccion_texto3')->nullable();



            $table->string('fogo_foto')->nullable();
            $table->string('fogo_frase')->nullable();
            $table->string('acc_foto')->nullable();
            $table->string('acc_frase')->nullable();
            $table->string('coc_foto')->nullable();
            $table->string('coc_frase')->nullable();


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
        Schema::dropIfExists('homes');
    }
}
