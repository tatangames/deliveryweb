<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Usuario::create([
            'nombre' => 'Jonathan Moran',
            'usuario' => 'tatan',
            'password' => bcrypt('admin'),
            'activo' => '1'
        ])->assignRole('Super-Admin');
    }
}
