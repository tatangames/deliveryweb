<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonederoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monedero', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('clientes_id')->unsigned();
            $table->decimal('monedas', 10,2);
            $table->decimal('pago_total', 10, 2);
            $table->dateTime('fecha');
            $table->integer('comision');

            // se puede agregar una nota para registros
            $table->string('nota', 300)->nullable();

            $table->string('idtransaccion', 200)->nullable();
            $table->string('codigo', 200)->nullable();

            $table->boolean('esreal');
            $table->boolean('esaprobada');

            $table->string('correo', 100)->nullable();

            // el credito sera agregado de una vez, pero el admin verificara cuando tenga el monto
            // agregado en su cuenta bancaria
            $table->boolean('revisada');
            $table->dateTime('fecha_revisada')->nullable();
            $table->foreign('clientes_id')->references('id')->on('clientes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monedero');
    }
}
