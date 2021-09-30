<?php

namespace App\Http\Controllers\Backend\api\Monedero;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\InformacionAdmin;
use App\Models\Monedero;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DateTime;

class ApiMonederoController extends Controller
{

    public function informacionMonedero(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($infoCiente = Cliente::where('id', $request->clienteid)->first()){

            $infoAdmin = InformacionAdmin::where('id', 1)->first();

            if($infoAdmin->activo_tarjeta == 0){
                return ['success' => 1, 'mensaje' => $infoAdmin->mensaje_tarjeta];
            }

            $comision = intval($infoAdmin->comision);

            return ['success' => 2, 'comision' => $comision, 'monedero' => $infoCiente->monedero];
        }
    }


    public function realizarCompraMonedero(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required',
            'nombre' => 'required',
            'numero' => 'required',
            'mes' => 'required',
            'anio' => 'required',
            'cvv' => 'required',
            'correo' => 'required',
            'comprar' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){ return ['success' => 0];}

        $infoAdmin = InformacionAdmin::where('id', 1)->first();

        $time1 = Carbon::parse($infoAdmin->fecha_token);
        $horaEstimada = $time1->addMinute(50)->format('Y-m-d H:i:s'); // agregar 50 minutos
        $today = Carbon::now('America/El_Salvador')->format('Y-m-d H:i:s');

        $d1 = new DateTime($horaEstimada);
        $d2 = new DateTime($today);

        $comision = $infoAdmin->comision;

        $resultado = ($comision * $request->comprar) / 100;
        $pagara = $request->comprar + $resultado;


        $data = array (
            'tarjetaCreditoDebido' =>
                array (
                    'numeroTarjeta' => $request->numero,
                    'cvv' => $request->cvv,
                    'mesVencimiento' => $request->mes,
                    'anioVencimiento' => $request->anio,
                ),
            'monto' => $pagara,
            'emailCliente' => $request->correo,
            'nombreCliente' => $request->nombre,
            "formaPago" => "PagoNormal"
        );

        $convertido = json_encode($data);
        $tokenactual = $infoAdmin->token;

        DB::beginTransaction();

        try {
            $infoCliente = Cliente::where('id', $request->clienteid)->first();
            if ($d1 > $d2){
                // hay tiempo de token, generar compra

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.wompi.sv/TransaccionCompra",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_POSTFIELDS => $convertido,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_HTTPHEADER => array(
                        "authorization: Bearer $tokenactual",
                        "content-type: application/json"
                    ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);
                $code = curl_getinfo($curl);
                curl_close($curl);

                if ($err) {
                    return ['success' => 1]; // problema de peticion
                }else {
                    if(empty($response)){
                        return ['success' => 2, 'info' => $curl]; // problemas internos
                    }

                    if($code["http_code"] == 200){ // peticion correcta
                        $arrayjson = json_decode($response,true);

                        $idtransaccion = $arrayjson["idTransaccion"]; // guardar, string
                        $esreal = $arrayjson["esReal"]; // guardar, bool
                        $esaprobada = $arrayjson["esAprobada"]; // guardar, bool
                        //$monto = $arrayjson["monto"]; // decimal
                        $codigo = $arrayjson["codigoAutorizacion"];

                        if($esaprobada == false){
                            return ['success' => 5]; // reprobada, no pudo ser efectuada
                        }

                        $fechahoy = Carbon::now('America/El_Salvador');

                        // guardar datos
                        $reg = new Monedero();
                        $reg->clientes_id = $request->clienteid;
                        $reg->monedas = $request->comprar;
                        $reg->pago_total = $pagara; // lo que pago al final
                        $reg->fecha = $fechahoy;
                        $reg->comision = $infoAdmin->comision;
                        $reg->nota = null;
                        $reg->idtransaccion = $idtransaccion;
                        $reg->codigo = $codigo;
                        $reg->esreal = (int)$esreal;
                        $reg->esaprobada = (int)$esaprobada;
                        $reg->revisada = 0;
                        $reg->fecha_revisada = null;
                        $reg->correo = $request->correo;
                        $reg->save();

                        // actualizar monedero cliente
                        $sumatoria = $request->comprar + $infoCliente->monedero;
                        Cliente::where('id', $request->clienteid)->update(['monedero' => $sumatoria]);
                        DB::commit();

                        $infoCliente = Cliente::where('id', $sumatoria)->first();

                        $msj1 = "Su monedero actual es: " . $infoCliente->monedero;

                        return ['success' => 3, 'msj1' => $msj1]; // compra exitosa
                    }else{
                        // revisar los datos de su tarjeta
                        return ['success' => 4];
                    }
                }

            }else{

                // supero tiempo
                // generar token nuevo
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://id.wompi.sv/connect/token",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "grant_type=client_credentials&client_id=2881492c-24eb-4849-876d-a3068e2a8563&client_secret=21200088-b201-4903-8c07-e34bf90eba08&audience=wompi_api",
                    CURLOPT_HTTPHEADER => array(
                        "content-type: application/x-www-form-urlencoded"
                    ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);

                if ($err) {
                    return ['success' => 1]; // error al obtener token
                } else {
                    $jsonArray = json_decode($response,true);
                    $key = "access_token";
                    $tokennuevo = $jsonArray[$key];
                    $fechahoy = Carbon::now('America/El_Salvador');

                    // GUARDAR TOKEN NUEVO
                    InformacionAdmin::where('id', 1)->update(['token' => $tokennuevo, 'fecha_token' => $fechahoy]);

                    DB::commit();

                    // GENERAR COMPRA

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://api.wompi.sv/TransaccionCompra",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_POSTFIELDS => $convertido,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_HTTPHEADER => array(
                            "authorization: Bearer $tokennuevo",
                            "content-type: application/json"
                        ),
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);
                    $code = curl_getinfo($curl);
                    curl_close($curl);
                    if ($err) {
                        return ['success' => 1]; // problema realizar cobro
                    }else {
                        if(empty($response)){
                            return ['success' => 2]; // problemas
                        }

                        if($code["http_code"] == 200){ // peticion correcta
                            $arrayjson = json_decode($response,true);

                            $idtransaccion = $arrayjson["idTransaccion"]; // guardar, string
                            $codigo = $arrayjson["codigoAutorizacion"];
                            $esreal = $arrayjson["esReal"]; // guardar, bool
                            $esaprobada = $arrayjson["esAprobada"]; // guardar, bool
                            //$monto = $arrayjson["monto"]; // decimal

                            if($esaprobada == false){
                                return ['success' => 5]; // reprobada, no pudo ser efectuada
                            }

                            $fechahoy = Carbon::now('America/El_Salvador');

                            // guardar datos
                            $reg = new Monedero();
                            $reg->clientes_id = $request->clienteid;
                            $reg->monedas = $request->comprar;
                            $reg->pago_total = $pagara; // lo que pago al final
                            $reg->fecha = $fechahoy;
                            $reg->comision = $infoAdmin->comision;
                            $reg->nota = null;
                            $reg->idtransaccion = $idtransaccion;
                            $reg->codigo = $codigo;
                            $reg->esreal = (int)$esreal;
                            $reg->esaprobada = (int)$esaprobada;
                            $reg->revisada = 0;
                            $reg->fecha_revisada = null;
                            $reg->correo = $request->correo;
                            $reg->save();

                            // actualizar monedero cliente

                            $sumatoria = $request->comprar + $infoCliente->monedero;
                            Cliente::where('id', $request->clienteid)->update(['monedero' => $sumatoria]);

                            DB::commit();


                            $msj1 = "Su monedero actual es: " . $sumatoria;

                            return ['success' => 3, 'msj1' => $msj1]; // compra exitosa
                        }else{
                            // revisar los datos de su tarjeta
                            return ['success' => 4];
                        }
                    }
                }
            }

        } catch(\Throwable $e){
            DB::rollback();
            // error
            return [
                'success' => 5
            ];
        }
    }
}
