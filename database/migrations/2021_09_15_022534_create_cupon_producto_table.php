<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuponProductoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cupon_producto', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cupones_id')->unsigned();

            // minimo a consumir para aplicar producto gratis
            $table->decimal('dinero', 10, 2);

            // producto gratis
            $table->string('nombre', 100);

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
        Schema::dropIfExists('cupon_producto');
    }
}
