<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonederoDevueltoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monedero_devuelto', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ordenes_id')->unsigned();

            // monedero devuelto
            $table->decimal('dinero', 10, 2);
            $table->dateTime('fecha');

            $table->foreign('ordenes_id')->references('id')->on('ordenes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monedero_devuelto');
    }
}
