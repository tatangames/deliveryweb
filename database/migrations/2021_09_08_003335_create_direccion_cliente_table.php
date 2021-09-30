<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDireccionClienteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('direccion_cliente', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('zonas_id')->unsigned();
            $table->bigInteger('clientes_id')->unsigned();

            $table->string('nombre', 100);
            $table->string('direccion', 400);
            $table->string('numero_casa', 30)->nullable();
            $table->string('punto_referencia', 400)->nullable();
            $table->boolean('seleccionado');
            $table->string('latitud', 50);
            $table->string('longitud', 50);
            $table->string('latitudreal', 50)->nullable();
            $table->string('longitudreal', 50)->nullable();
            $table->boolean('revisado');

            $table->foreign('zonas_id')->references('id')->on('zonas');
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
        Schema::dropIfExists('direccion_cliente');
    }
}
