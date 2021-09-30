<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiciosTipoTable extends Migration
{
    /**
     * Estas son las categorias
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servicios_tipo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->bigInteger('servicios_id')->unsigned();
            $table->integer('posicion');
            $table->boolean('activo');

            // visible al cliente y propietario la categoria
            $table->boolean('visible');

            $table->foreign('servicios_id')->references('id')->on('servicios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('servicios_tipo');
    }
}
