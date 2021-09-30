<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarritoTemporalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrito_temporal', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('clientes_id')->unsigned();
            $table->bigInteger('servicios_id')->unsigned();
            $table->bigInteger('zonas_id')->unsigned();

            $table->foreign('clientes_id')->references('id')->on('clientes');
            $table->foreign('servicios_id')->references('id')->on('servicios');
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
        Schema::dropIfExists('carrito_temporal');
    }
}
