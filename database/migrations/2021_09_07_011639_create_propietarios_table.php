<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropietariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('propietarios', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('servicios_id')->unsigned();

            $table->string('nombre', 100);
            $table->string('telefono', 20)->unique();
            $table->string('password', 255);
            $table->date('fecha');
            $table->boolean('disponibilidad');
            $table->string('correo', 100)->unique()->nullable();
            $table->string('codigo', 10)->nullable();
            $table->string('token_fcm',100)->nullable();
            $table->boolean('activo');
            $table->boolean('bloqueado'); // propietario no pueda editar productos

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
        Schema::dropIfExists('propietarios');
    }
}
