<?php

namespace Database\Seeders;

use App\Models\TipoCupon;
use Illuminate\Database\Seeder;

class TipoCuponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoCupon::create([
            'nombre' => 'Envío Gratis',
            'descripcion' => 'A cualquier servicio proporciona envío gratis',
        ]);

        TipoCupon::create([
            'nombre' => 'Producti Gratis',
            'descripcion' => 'Por mínimo de consumo damos un producto gratis',
        ]);
    }
}
