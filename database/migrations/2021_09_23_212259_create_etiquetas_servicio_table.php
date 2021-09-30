<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtiquetasServicioTable extends Migration
{
    /**
     * vincular servicio con sus etiquetas
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etiquetas_servicio', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('servicios_id')->unsigned();
            $table->bigInteger('etiquetas_id')->unsigned();

            $table->foreign('servicios_id')->references('id')->on('servicios');
            $table->foreign('etiquetas_id')->references('id')->on('etiquetas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('etiquetas_servicio');
    }
}
