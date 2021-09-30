<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuponZonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cupon_zonas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cupones_id')->unsigned();
            $table->bigInteger('zonas_id')->unsigned();

            $table->foreign('cupones_id')->references('id')->on('cupones');
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
        Schema::dropIfExists('cupon_zonas');
    }
}
