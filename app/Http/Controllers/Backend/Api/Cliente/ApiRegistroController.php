<?php

namespace App\Http\Controllers\Backend\api\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use JWTAuth;

class ApiRegistroController extends Controller
{
    public function registroCliente(Request $request){

        $rules = array(
            'nombre' => 'required|max:100',
            'telefono' => 'required|max:20',
            'password' => 'required|min:4|max:16',
            'area' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){return ['success' => 0]; }

        $unido = $request->area . $request->telefono;

        // verificar si existe el telefono
        if(Cliente::where('telefono', $unido)->first()){
            return ['success' => 1];
        }

        $fecha = Carbon::now('America/El_Salvador');

        $usuario = new Cliente();
        $usuario->nombre = $request->nombre;
        $usuario->telefono = $unido;
        $usuario->correo = null;
        $usuario->password = Hash::make($request->password);
        $usuario->codigo_correo = null;
        $usuario->token_fcm = $request->token_fcm;
        $usuario->fecha = $fecha;
        $usuario->activo = 1;
        $usuario->monedero = 0;
        $usuario->area = $request->area;

        if($usuario->save()){

            $token = JWTAuth::fromUser($usuario);

            return ['success'=> 2, 'id'=> strval($usuario->id), 'token' => $token];

        }else{
            return ['success' => 3];
        }
    }









}
