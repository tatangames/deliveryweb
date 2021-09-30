<?php

namespace App\Http\Controllers\Backend\api\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\DireccionCliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiZonasServiciosController extends Controller
{
    public function listado(Request $request){

        $rules = array(
            'id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if($data = Cliente::where('id', $request->id)->first()){
            if($data->activo == 0){
                return ['success' => 1];
            }
        }

        if($info = DireccionCliente::where('clientes_id', $request->id)->first()){

            $servicios = DB::table('tipos_servicio_zonas AS tz')
                ->join('tipos_servicio AS t', 't.id', '=', 'tz.tipos_servicio_id')
                ->select('t.id AS tipoServicioID', 't.nombre', 't.imagen', 't.tipos_id', 't.descripcion')
                ->where('tz.zonas_id', $info->zonas_id)
                ->where('tz.activo', '1') //solo servicios disponibles
                ->orderBy('tz.posicion', 'ASC')
                ->get();

            return [
                'success' => 2,
                'servicios' => $servicios
            ];

        }else{
            // no hay direccion, se elegira una
            return ['success' => 3, 'msj1' => "Bienvenido", 'msj2' => "Agregar una nueva dirección"];
        }
    }



}
