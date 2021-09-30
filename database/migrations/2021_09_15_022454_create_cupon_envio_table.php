<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuponEnvioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cupon_envio', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cupones_id')->unsigned();

            // minimo a consumir para aplicar envio gratis
            $table->decimal('dinero', 10, 2);

            $table->foreign('cupones_id')->references('id')->on('cupones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cupon_envio');
    }
}
