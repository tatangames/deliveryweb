<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTiposServicioZonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipos_servicio_zonas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tipos_servicio_id')->unsigned();
            $table->bigInteger('zonas_id')->unsigned();
            $table->boolean('activo')->default(1);
            $table->integer('posicion');

            $table->foreign('tipos_servicio_id')->references('id')->on('tipos_servicio');
            $table->foreign('zonas_id')->references('id')->on('zonas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipos_servicio_zonas');
    }
}
