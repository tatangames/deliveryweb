<?php

namespace App\Http\Controllers\Backend\Admin\AdminRaiz\Roles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ControlController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function indexRedireccionamiento(){

        $user = Auth::user();

       // $permiso = $user->getAllPermissions()->pluck('name');

        // Rol: Super-Admin
        if($user->hasPermissionTo('rol.superadmin.inicio')){
           // $ruta = 'admin.roles.index';
            $ruta = 'index.estadisticas';
        }

        // Rol: Admin-Informativo
        // vista informatico -> redirigir a nuevas solicitudes
        else  if($user->hasPermissionTo('rol.informativo.nuevas-solicitudes')){
            $ruta = 'admin2.nuevas.solicitudes.index';
        }

        // Rol: Admin-Electrico
        // vista informatico -> redirigir a nuevas solicitudes
        //*** Por cada departamento de denuncia (electrico, agua, baches, etc)
        // se debera crear su propia vista
        // aqui se revisan denuncias tipo Electricas
        else  if($user->hasPermissionTo('rol.denuncia.nuevas-solicitudes')){
            $ruta = 'admin2.nuevas.solicitudes.denuncias.index';
        }

        // Rol: Admin-Partidas
        // vista partida -> redirigir a nuevas solicitudes
        // aqui se revisan solicitude para partida de nacimiento
        else  if($user->hasPermissionTo('rol.partida.nuevas-solicitudes')){
            $ruta = 'admin2.nuevas.solicitudes.partida.index';
        }

        else{
            // no tiene ningun permiso de vista, redirigir a pantalla sin permisos
            $ruta = 'no.permisos.index';
        }

        return view('backend.index', compact('user', 'ruta'));
    }

    public function indexSinPermiso(){
        return view('errors.403');
    }
}
