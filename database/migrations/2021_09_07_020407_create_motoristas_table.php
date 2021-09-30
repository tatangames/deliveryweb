<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMotoristasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motoristas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->string('identificador', 100);
            $table->string('telefono', 20)->unique();
            $table->string('password', 255);
            $table->boolean('activo')->default(1);
            $table->boolean('disponible')->default(0);
            $table->date('fecha');
            $table->string('token_fcm', 100)->nullable();
            $table->string('codigo', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('motoristas');
    }
}
