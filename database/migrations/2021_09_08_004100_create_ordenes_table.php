<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('clientes_id')->unsigned();
            $table->bigInteger('servicios_id')->unsigned();

            $table->string('nota', 600)->nullable();
            $table->decimal('precio_consumido', 10,2); // total de la orden

            // precio del envio (aqui viene afectado por cualquier otras opciones)
            $table->decimal('precio_envio', 10,2);
            $table->dateTime('fecha_orden');
            $table->string('cambio', 20)->nullable();

            $table->boolean('estado_2');
            $table->dateTime('fecha_2')->nullable();
            $table->integer('hora_2')->default(0);

            $table->boolean('estado_3');
            $table->dateTime('fecha_3')->nullable();

            $table->boolean('estado_4');
            $table->dateTime('fecha_4')->nullable();

            $table->boolean('estado_5');
            $table->dateTime('fecha_5')->nullable();

            $table->boolean('estado_6');
            $table->dateTime('fecha_6')->nullable();

            $table->boolean('estado_7');
            $table->dateTime('fecha_7')->nullable();

            $table->boolean('estado_8');
            $table->dateTime('fecha_8')->nullable();
            $table->string('mensaje_8', 600)->nullable(); // porque fue cancelada

            $table->boolean('visible');
            $table->boolean('visible_p');
            $table->boolean('visible_p2');
            $table->boolean('visible_p3');

            $table->integer('cancelado'); // 0: nadie, 1: cliente, 2: propietario

            $table->decimal('ganancia_motorista', 10,2);

            $table->boolean('visible_m');

            /* tipo de cargo de envio que se aplica
            1- cargo de envio tomado de precio de zona servicio
            3- cargo de envio se aplico entrega gratis tomado de zona servicio
            4- cargo de envio si supero o igualo min de compra
            */

            $table->integer('tipo_cargo');

            $table->foreign('clientes_id')->references('id')->on('clientes');
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
        Schema::dropIfExists('ordenes');
    }
}
