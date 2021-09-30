<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePalabrasBuscadorTable extends Migration
{
    /**
     * guardar palabras que la persona busca en el buscador general
     *
     * @return void
     */
    public function up()
    {
        Schema::create('palabras_buscador', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha');
            $table->bigInteger('clientes_id')->unsigned();
            $table->string('nombre', 300)->nullable();

            $table->foreign('clientes_id')->references('id')->on('clientes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('palabras_buscador');
    }
}
