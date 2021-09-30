<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuponServiciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cupon_servicios', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cupones_id')->unsigned();
            $table->bigInteger('servicios_id')->unsigned();

            $table->foreign('cupones_id')->references('id')->on('cupones');
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
        Schema::dropIfExists('cupon_servicios');
    }
}
