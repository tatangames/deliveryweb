<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * 200 caracteres para name
     * 500 caracteres para description
     *
     * @return void
     */
    public function run()
    {
        // administrador con todos los permisos
        $role1 = Role::create(['name' => 'Super-Admin']);

        // Vista de Ingreso
        Permission::create(['name' => 'rol.superadmin.inicio', 'description' => 'Cuando inicia el sistema, se redirigirá la vista al grupo Inicio'])->syncRoles($role1);

        // Lista de permisos
        Permission::create(['name' => 'grupo.superadmin.roles-y-permisos', 'description' => 'Contenedor para el grupo llamado: Roles y Permisos'])->syncRoles($role1);



    }
}
