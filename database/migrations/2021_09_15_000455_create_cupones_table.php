<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuponesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cupones', function (Blueprint $table) {
            $table->id();

            // 1- envio gratis
            // 2- producto gratis

            $table->bigInteger('tipo_cupon_id')->unsigned();

            $table->string('cupon', 100)->unique();
            $table->integer('uso_limite');
            $table->integer('contador');
            $table->date('fecha');
            $table->boolean('activo');

            $table->foreign('tipo_cupon_id')->references('id')->on('tipo_cupon');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cupones');
    }
}
