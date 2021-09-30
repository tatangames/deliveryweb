<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInformacionAdminTable extends Migration
{
    /**
     * campos de informacion
     *
     * @return void
     */
    public function up()
    {
        Schema::create('informacion_admin', function (Blueprint $table) {
            $table->id();

            // para activar o no el boton de cupones
            $table->boolean('estado_cupon');

            // comision de wompi
            $table->integer('comision');
            $table->boolean('activo_tarjeta');
            $table->string('mensaje_tarjeta', 200)->nullable();

            // al completar una orden
            $table->boolean('borrar_carrito');

            $table->string('token', 2500)->nullable();
            $table->dateTime('fecha_token')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('informacion_admin');
    }
}
