<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenesDireccionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes_direcciones', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('clientes_id')->unsigned();
            $table->bigInteger('ordenes_id')->unsigned();
            $table->bigInteger('zonas_id')->unsigned();

            $table->string('nombre', 100);
            $table->string('direccion', 400);
            $table->string('numero_casa', 30)->nullable();
            $table->string('punto_referencia', 400)->nullable();
            $table->string('latitud', 50)->nullable();
            $table->string('longitud', 50)->nullable();
            $table->string('latitudreal', 50)->nullable();
            $table->string('longitudreal', 50)->nullable();

            // una copia del precio de zona que habia en ese momento
            $table->decimal('copia_envio', 10,2);

            // es el tiempo extra de cada zona para la orden
            $table->integer('copia_tiempo_orden');


            // version de la app
            $table->string('version', 100)->nullable();
            $table->boolean('revisado');

            // metodo pago
            // 1: efectivo, 2: monedero
            $table->integer('metodo_pago');

            // copia de la comision del negocio
            $table->integer('copia_comision');

            // copia si el servicio dio su propio dimicilio
            $table->boolean('privado');

            $table->foreign('clientes_id')->references('id')->on('clientes');
            $table->foreign('ordenes_id')->references('id')->on('ordenes');
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
        Schema::dropIfExists('ordenes_direcciones');
    }
}
