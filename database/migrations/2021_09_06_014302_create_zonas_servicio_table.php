<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZonasServicioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zonas_servicio', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('zonas_id')->unsigned();
            $table->bigInteger('servicios_id')->unsigned();

            // precio de envio a esta zona
            $table->decimal('precio_envio', 10,2);

            // activo en esta zona
            $table->boolean('activo')->default(1);

            // ganancia motorista a esta zona
            $table->decimal('ganancia_motorista', 10,2);

            // ubicacion
            $table->integer('posicion');

            // si supera el minimo de compra, su envio es gratis
            $table->boolean('min_envio_gratis')->default(0);
            // minimo de compra para envio gratis
            $table->decimal('costo_envio_gratis', 10,2);

            // si el servicio dara envio gratis a esta zona, sin tocar el precio envio
            $table->boolean('zona_envio_gratis')->default(0);

            // no hay envio de este servicio temporalmente a esta zona agregado
            $table->boolean('saturacion');
            $table->string('mensaje_bloqueo', 200)->nullable();

            $table->foreign('zonas_id')->references('id')->on('zonas');
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
        Schema::dropIfExists('zonas_servicio');
    }
}
