<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tipos_servicio_id')->unsigned();

            $table->string('nombre', 150);
            $table->string('identificador', 100)->unique();
            $table->string('descripcion', 300)->nullable();
            $table->string('logo', 100);
            $table->string('imagen', 100);
            $table->boolean('cerrado_emergencia');
            $table->string('mensaje_cerrado', 200)->nullable();
            $table->date('fecha');
            $table->boolean('activo');
            $table->string('telefono', 20);
            $table->string('latitud', 50);
            $table->string('longitud', 50);
            $table->string('direccion', 300);

            // si es vertical o horizontal
            $table->boolean('tipo_vista');

            // minimo de compra
            $table->decimal('minimo', 10,2);
            $table->boolean('utiliza_minimo');

            // cuando el usuario no debe contestar si debe esperar la orden
            $table->boolean('orden_automatica');
            // tiempo espera para contestacion automatica
            $table->integer('tiempo');

            // comision que se le cobrara al negocio
            $table->integer('comision');

            // si es privado, da su propio domicilio, unicamente no le afecta el horario
            // de la zona.
            $table->boolean('privado');

            $table->foreign('tipos_servicio_id')->references('id')->on('tipos_servicio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('servicios');
    }
}
