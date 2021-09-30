<?php

namespace App\Http\Controllers\backend\Api\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\ContadorSms;
use App\Models\NumerosSms;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;

class ApiClienteController extends Controller
{

    // verificacion de número télefonico
    public function verificarNumero(Request $request){

        $rules = array(
            'telefono' => 'required',
            'area' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){return ['success' => 0]; }

        $unido = $request->area . $request->telefono;

        if(Cliente::where('telefono', $unido)->first()){
            return ['success' => 1 ];
        }

        // generador de codigo aleatorio
        $codigo = '';
        for($i = 0; $i < 6; $i++) {
            $codigo .= mt_rand(0, 9);
        }
        DB::beginTransaction();

        $fecha = Carbon::now('America/El_Salvador');
        // si no encuentra el registro se enviara el codigo sms, verificar su contador
        if($ns = NumerosSms::where('telefono', $unido)->first()){

            NumerosSms::where('id', $ns->id)->update(['codigo' => $codigo]);

        }else{
            // numero no registrado, guardar registro y enviar sms

            // para inicio de sesion un contador
            $n = new NumerosSMS();
            $n->area = $request->area;
            $n->telefono = $unido;
            $n->codigo = $codigo;
            $n->codigo_fijo = $codigo;
            $n->fecha = $fecha;
            $n->save();
        }

        $dato = new ContadorSms();
        $dato->telefono = $unido;
        $dato->tipo = 1; // en pantalla login
        $dato->fecha = $fecha;
        $dato->save();

        // envio del mensaje
        $sid = config('twiliokey.Twilio_SID');
        $token = config('twiliokey.Twilio_TOKEN');
        $twilioNumber = config('twiliokey.Twilio_NUMBER');
        $client = new Client($sid, $token);
        $numero = $unido;

        DB::commit();
        return ['success' => 2];

        try {
            $client->account->messages->create(
                $numero,
                array(
                    'from' =>  $twilioNumber,
                    'body' =>'Tu código de acceso es: '.$codigo
                )
            );

            DB::commit();
            return ['success' => 2];
        } catch (Exception  $e) {
            // por cualquier error, notificar a la app
            return ['success' => 3];
        }

    }

    public function verificarCodigoTemporal(Request $request){
        $rules = array(
            'telefono' => 'required',
            'area' => 'required',
            'codigo' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){return ['success' => 0]; }

        $unido = $request->area . $request->telefono;

        // verificar codigo, donde coincida codigo de area + numero
        if($ns = NumerosSMS::where('telefono', $unido)->first()){

            if($request->codigo == $ns->codigo || $request->codigo == $ns->codigo_fijo){

                return ['success' => 1];
            }else{
                return ['success' => 2];
            }

        }else{
            return ['success' => 3];
        }
    }

    public function loginCliente(Request $request){
        $rules = array(
            'telefono' => 'required',
            'password' => 'required|max:16',
            'area' => 'required'
        );

        $validator = Validator::make($request->all(), $rules );

        if ( $validator->fails()){
            return ['success' => 0];
        }

        $unido = $request->area . $request->telefono;

        if($u = Cliente::where('telefono', $unido)->first()){

            if($u->activo == 0){
                $msj1 = "El número teléfonico ha sido bloqueado, contactar con administración";
                return ['success' => 1, 'msj1' => $msj1];
            }

            if (Hash::check($request->password, $u->password)) {

                Cliente::where('id', $u->id)->update(['token_fcm' => $request->token_fcm]);

                return ['success' => 2, 'id' => $u->id];

            }else{
                return ['success' => 3];
            }

        } else {
            return ['success' => 3];
        }
    }

    // para recuperación de contraseña, se enviara un código sms
    // al area mas numero
    public function enviarCodigoSms(Request $request){
        $rules = array(
            'telefono' => 'required',
            'area' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){return ['success' => 0]; }

        // codigo aleaotorio
        $codigo = '';
        for($i = 0; $i < 6; $i++) {
            $codigo .= mt_rand(0, 9);
        }

        DB::beginTransaction();

        $fecha = Carbon::now('America/El_Salvador');

        $unido = $request->area . $request->telefono;

        if($ns = NumerosSms::where('telefono', $unido)->first()){

            NumerosSms::where('id', $ns->id)->update(['codigo' => $codigo]);

            // cambio de contraseña si la olvido

            $dato = new ContadorSms();
            $dato->telefono = $unido;
            $dato->tipo = 2; // recuperacion de contraseña
            $dato->fecha = $fecha;
            $dato->save();

            DB::commit();
            return ['success' => 1];

            // envio del mensaje
            /* $sid = config('twiliokey.Twilio_SID');
            $token = config('twiliokey.Twilio_TOKEN');
            $twilioNumber = config('twiliokey.Twilio_NUMBER');
            $client = new Client($sid, $token);
            $numero = $unido;

            try {
                $client->account->messages->create(
                    $numero,
                    array(
                        'from' =>  $twilioNumber,
                        'body' =>'Tu código de acceso es: '.$codigo
                    )
                );

                DB::commit();
                return ['success' => 1];
            } catch (Exception  $e) {
                // por cualquier error, notificar a la app
                return ['success' => 2];
            }*/

        }else{
             return ['success' => 3];
        }
    }

    public function verificarCodigoSmsPassword(Request $request){
        $rules = array(
            'telefono' => 'required',
            'area' => 'required',
            'codigo' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){return ['success' => 0]; }

        $unido = $request->area . $request->telefono;

        // verificar codigo, donde coincida codigo de area + numero
        if($ns = NumerosSMS::where('telefono', $unido)->first()){

            if($request->codigo == $ns->codigo){

                return ['success' => 1];
            }else{
                return ['success' => 2];
            }

        }else{
            return ['success' => 3];
        }
    }

    public function actualizarPasswordCliente(Request $request){

        $rules = array(
            'area' => 'required',
            'telefono' => 'required',
            'password' => 'required|min:4|max:16'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        $unido = $request->area . $request->telefono;

        if($datos = Cliente::where('telefono', $unido)->first()){

            Cliente::where('id', $datos->id)->update(['password' => Hash::make($request->password)]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // reenviar codigo sms cuando se esta registrando el cliente
    public function enviarCodigoSmsRegistro(Request $request){

        $rules = array(
            'area' => 'required',
            'telefono' => 'required'

        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){return ['success' => 0]; }

        $codigo = '';
        for($i = 0; $i < 6; $i++) {
            $codigo .= mt_rand(0, 9);
        }
        DB::beginTransaction();

        $fecha = Carbon::now('America/El_Salvador');

        $unido = $request->area . $request->telefono;

        if($ns = NumerosSms::where('telefono', $unido)->first()){

            NumerosSms::where('id', $ns->id)->update(['codigo' => $codigo]);

            $dato = new ContadorSms();
            $dato->telefono = $unido;
            $dato->tipo = 3; // reenviar codigo por contador al registrarse
            $dato->fecha = $fecha;
            $dato->save();

            DB::commit();
            return ['success' => 1];

            // envio del mensaje
            /*$sid = config('twiliokey.Twilio_SID');
            $token = config('twiliokey.Twilio_TOKEN');
            $twilioNumber = config('twiliokey.Twilio_NUMBER');
            $client = new Client($sid, $token);
            $numero = $unido;

            try {
                $client->account->messages->create(
                    $numero,
                    array(
                        'from' =>  $twilioNumber,
                        'body' =>'Tu código de acceso es: '.$codigo
                    )
                );

                DB::commit();
                return ['success' => 1];
            } catch (Exception  $e) {
                // por cualquier error, notificar a la app
                return ['success' => 2];
            }*/

        }else{
            return ['success' => 3];
        }

    }


}
