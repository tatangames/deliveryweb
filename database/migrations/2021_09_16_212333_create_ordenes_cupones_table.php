<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenesCuponesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes_cupones', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ordenes_id')->unsigned();
            $table->bigInteger('cupones_id')->unsigned();

            // por si cambian tipo de cupon, aqui quedara registrado
            $table->bigInteger('tipocupon_id')->unsigned();

            // copia del nombre del cupon
            $table->string('nombre_cupon', 100);

            $table->decimal('dinero', 10, 2);

            $table->string('nombre_producto', 100)->nullable();


            $table->foreign('ordenes_id')->references('id')->on('ordenes');
            $table->foreign('cupones_id')->references('id')->on('cupones');
            $table->foreign('tipocupon_id')->references('id')->on('tipo_cupon');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordenes_cupones');
    }
}
