<?php

namespace App\Http\Controllers\Backend\ApiAfiliado\Login;

use App\Http\Controllers\Controller;
use App\Models\Propietarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class ApiLoginAfiliadoController extends Controller
{
    public function loginAfiliado(Request $request){

        $rules = array(
            'telefono' => 'required',
            'password' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if($p = Propietarios::where('telefono', $request->telefono)->first()){

            if($p->activo == 0){
                // propietario inactivo
                return ['success' => 1];
            }

            if (Hash::check($request->password, $p->password)) {

                if($request->tokenfcm != null){
                    Propietarios::where('id', $p->id)->update(['token_fcm' => $request->tokenfcm]);
                }

                return ['success' => 2, 'id' => $p->id];
            }else{
                // contraseña incorrecta
                return ['success' => 3];
            }
        }else{
            // telefono no encontrado
            return ['success' => 4];
        }
    }


    public function enviarCodigoSms(Request $request){

        $rules = array(
            'telefono' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){return ['success' => 0]; }

        $codigo = '';
        for($i = 0; $i < 6; $i++) {
            $codigo .= mt_rand(0, 9);
        }

        if($infoAfiliado = Propietarios::where('telefono', $request->telefono)->first()) {

            if ($infoAfiliado->activo == 0) {
                // propietarios inactivo
                return ['success' => 1];
            }

            Propietarios::where('telefono', $request->telefono)->update(['codigo' => $codigo]);

            return ['success' => 2];

            // envio del mensaje
            /*$sid = "ACc68bf246c0d9be071f2367e81b686201";
            $token = "01990626f6e7fb813eb7317c06db6a47";
            $twilioNumber = "+12075012749";
            $client = new Client($sid, $token);
            $numero = $unido;

            try {
                $client->account->messages->create(
                    $numero,
                    array(
                        'from' =>  $twilioNumber,
                        'body' =>'Tu código Tatan Express es: '.$codigo
                    )
                );

                return ['success' => 2];
            } catch (Exception  $e) {
                // por cualquier error, notificar a la app
                return ['success' => 3];
            }*/

        }else{
            return ['success' => 4];
        }
    }


    public function verificarCodigo(Request $request){

        $rules = array(
            'telefono' => 'required',
            'codigo' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){return ['success' => 0]; }

        if(Propietarios::where('telefono', $request->telefono)
            ->where('codigo', $request->codigo)
            ->first()){

            return ['success' => 1];

        }else{
            return ['success' => 2];
        }
    }

    public function actualizarPasswordAfiliado(Request $request){

        $rules = array(
            'telefono' => 'required',
            'password' => 'required|min:4|max:16'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if($datos = Propietarios::where('telefono', $request->telefono)->first()){

            Propietarios::where('id', $datos->id)->update(['password' => Hash::make($request->password)]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }
}
