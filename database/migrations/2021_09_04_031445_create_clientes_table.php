<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientesTable extends Migration
{
    /**
     * Clientes moviles
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 100)->nullable();
            $table->string('telefono', 20)->unique();
            $table->string('correo', 100)->unique()->nullable();
            $table->string('password', 255);
            $table->string('codigo_correo',10)->nullable();
            $table->string('token_fcm', 300)->nullable();
            $table->dateTime('fecha');
            $table->boolean('activo')->default(1);
            $table->string('imagen', 100)->nullable();

            // monedero virtual
            $table->decimal('monedero', 10,2);

            // area de pais
            $table->string('area', 20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clientes');
    }
}
