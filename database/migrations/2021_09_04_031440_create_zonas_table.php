<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zonas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('descripcion', 300)->nullable();

            // ubicar punto central del mapa
            $table->string('latitud', 50);
            $table->string('longitud', 50);

            // si tenemos problemas de envio a esta zona
            $table->boolean('saturacion');

            $table->string('mensaje_bloqueo',200)->nullable();

            // horario domicilio a esta zona
            $table->time('hora_abierto_delivery');
            $table->time('hora_cerrado_delivery');

            // fecha cuando se creo la zona
            $table->date('fecha');

            // visibilidad de la zona en el mapa
            $table->boolean('activo');

            // identificador de la zona para ver un nombre corto
            $table->string('identificador', 100)->unique();

            // aumentar el tiempo extra a esta zona, es decir el propietario dice que
            // estara en 20 minutos pero se le agrega x tiempo mas para llegar su orden.
            $table->integer('tiempo_extra');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zonas');
    }
}
