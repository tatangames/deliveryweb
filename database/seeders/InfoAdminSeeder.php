<?php

namespace Database\Seeders;

use App\Models\InformacionAdmin;
use Illuminate\Database\Seeder;

class InfoAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        InformacionAdmin::create([
            'estado_cupon' => 1,
            'comision' => 3,
            'activo_tarjeta' => 1,
            'mensaje_tarjeta' => '',
            'token' => null,
            'fecha_token' => null,
            'borrar_carrito' => 0
        ]);
    }
}
