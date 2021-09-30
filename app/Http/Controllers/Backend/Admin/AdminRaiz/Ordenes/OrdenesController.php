<?php

namespace App\Http\Controllers\Backend\Admin\AdminRaiz\Ordenes;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\MotoristaExperiencia;
use App\Models\Motoristas;
use App\Models\MotoristasOrdenes;
use App\Models\Ordenes;
use App\Models\OrdenesCupones;
use App\Models\OrdenesDirecciones;
use App\Models\Servicios;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class OrdenesController extends Controller
{
    public function indexOrdenes(){
        return view('backend.admin.ordenes.index');
    }

    public function tablaIndexOrdenes(){

        $lista = Ordenes::orderBy('fecha_orden')->get();

        foreach ($lista as $l){

            $l->fecha_orden = date("d-m-Y h:i A", strtotime($l->fecha_orden));

            $infoCliente = Cliente::where('id', $l->clientes_id)->first();
            $infoServicio = Servicios::where('id', $l->servicios_id)->first();
            $infoOrdenes = OrdenesDirecciones::where('ordenes_id', $l->id)->first();

            $l->cliente = $infoCliente->nombre;
            $l->telefono = $infoServicio->telefono;
            $l->negocio = $infoServicio->nombre;

            $moto = "No";
            if($infoMoto = MotoristasOrdenes::where('ordenes_id', $l->id)->first()){

                $infoNombre = Motoristas::where('id', $infoMoto->motoristas_id)->first();
                $moto = $infoNombre->identificador;
            }

            $l->motorista = $moto;

            if($infoOrdenes->metodo_pago == 1){
                $tipopago = "Efectivo";
            }else{
                $tipopago = "Monedero";
            }

            /* tipo de cargo de envio que se aplica
            1- cargo de envio tomado de precio de zona servicio
            2- cargo de envio se aplico entrega gratis tomado de zona servicio
            3- cargo de envio si supero o igualo min de compra
            */

            if($l->tipo_cargo == 1){
                $tipocargo = "Aplico envío de Zona Servicio";
            } else if($l->tipo_cargo == 2){
                $tipocargo = "Aplico envío gratis para zona servicio";
            } else if($l->tipo_cargo == 3){
                $tipocargo = "Aplico mínimo de compra para envío gratis";
            }else{
                $tipocargo = "";
            }

            $estado = "Nueva Orden";

            if($l->estado_2 == 1){
                $estado = "Propietario establecio tiempo de espera";
            }

            if($l->estado_3 == 1){
                $estado = "Cliente acepto esperar tiempo de espera, esperando servicio iniciar la orden";
            }

            if($l->estado_4 == 1){
                $estado = "Negocio inicio inicio la preparación";
            }

            if($l->estado_5 == 1){
                $estado = "Negocio termino de preparar la orden, esperando motorista";
            }

            if($l->estado_6 == 1){
                $estado = "Motorista inicio la entrega";
            }

            if($l->estado_7 == 1){
                $estado = "Orden completada";
            }

            if($l->estado_8 == 1){

                if($l->cancelado == 1){
                    $persona = "Cliente";
                }else{
                    $persona = "Propietario";
                }
                $estado = "Orden cancelada por " . $persona . ". Nota: " . $l->mensaje_8;
            }

            $l->tipopago = $tipopago;
            $l->tipocargo = $tipocargo;
            $l->estado = $estado;
        }

        return view('backend.admin.ordenes.tabla.tablaordenes', compact('lista'));
    }

    public function informacionOrdenes(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Ordenes::where('id', $request->id)->first()){

            $p = Ordenes::where('id', $request->id)->get();

            foreach ($p as $l){

                $infoOrdenes = OrdenesDirecciones::where('ordenes_id', $l->id)->first();


                $l->copiatiempo = $infoOrdenes->copia_tiempo_orden;

                if($infoOrdenes->revisado == 0){
                    $revi = "No";
                }else{
                    $revi = "Si";
                }

                if($infoOrdenes->privado == 0){
                    $pri = "No";
                }else{
                    $pri = "Si";
                }

                $l->privado = $pri;
                $l->revisado = $revi;
                $l->version = $infoOrdenes->version;
                $l->comision = $infoOrdenes->copia_comision . "%";

                if($l->fecha_2 != null){
                    $l->fecha_2 = date("d-m-Y h:i A", strtotime($l->fecha_2));
                }

                if($l->fecha_3 != null){
                    $l->fecha_3 = date("d-m-Y h:i A", strtotime($l->fecha_3));
                }

                if($l->fecha_4 != null){
                    $l->fecha_4 = date("d-m-Y h:i A", strtotime($l->fecha_4));
                }

                if($l->fecha_5 != null){
                    $l->fecha_5 = date("d-m-Y h:i A", strtotime($l->fecha_5));
                }

                if($l->fecha_6 != null){
                    $l->fecha_6 = date("d-m-Y h:i A", strtotime($l->fecha_6));
                }

                if($l->fecha_7 != null){
                    $l->fecha_7 = date("d-m-Y h:i A", strtotime($l->fecha_7));
                }

                if($l->fecha_8 != null){
                    $l->fecha_8 = date("d-m-Y h:i A", strtotime($l->fecha_8));
                }
            }

            return ['success' => 1, 'lista' => $p];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionOrdenesCliente(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(OrdenesDirecciones::where('ordenes_id', $request->id)->first()){

            $lista = OrdenesDirecciones::where('ordenes_id', $request->id)->get();

            foreach ($lista as $l){

                $infoZona = Zona::where('id', $l->zonas_id)->first();

                $l->zona = $infoZona->nombre;
            }

            return ['success' => 1, 'lista' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function indexMapaPin($id){

        $infoDireccion = OrdenesDirecciones::where('ordenes_id', $id)->first();

        $latitud = $infoDireccion->latitud;
        $longitud = $infoDireccion->longitud;

        $api = config('googlekey.Google_Key');

        $nombre = "Coordenada PIN";

        return view('backend.admin.clientes.listacliente.direcciones.mapa.index', compact('latitud', 'longitud', 'api', 'nombre'));
    }

    public function indexMapaReal($id){

        $infoDireccion = OrdenesDirecciones::where('ordenes_id', $id)->first();

        if($infoDireccion->latitudreal != null){
            $latitud = $infoDireccion->latitudreal;
            $longitud = $infoDireccion->longitudreal;

            $api = config('googlekey.Google_Key');

            $nombre = "Coordenada Real";

            return view('backend.admin.clientes.listacliente.direcciones.mapa.index', compact('latitud', 'longitud', 'api', 'nombre'));
        }else{
            return view('errors.mapa');
        }
    }


    public function indexOrdenesCupon(){
        return view('backend.admin.ordenes.cupon.index');
    }

    public function tablaIndexOrdenesCupon(){

        $lista = OrdenesCupones::orderBy('id', 'DESC')->get();

        foreach ($lista as $l){

            $infoOrden = Ordenes::where('id', $l->ordenes_id)->first();

            $infoServicio = Servicios::where('id', $infoOrden->servicios_id)->first();

            $l->fecha_orden = date("d-m-Y h:i A", strtotime($infoOrden->fecha_orden));
            $l->negocio = $infoServicio->nombre;
            $l->identificador = $infoServicio->identificador;
        }

        return view('backend.admin.ordenes.cupon.tablacupon', compact('lista'));

    }

    public function indexComentarios(){
        return view('backend.admin.ordenes.comentarios.index');
    }

    public function tablaComentarios(){

        $lista = MotoristaExperiencia::orderBy('ordenes_id', 'DESC')->get();

        foreach ($lista  as $l) {

            $infoMotorista = Motoristas::where('id', $l->motoristas_id)->first();

            $l->nombre = $infoMotorista->nombre;
            $l->identificador = $infoMotorista->identificador;

            $l->fecha = date("d-m-Y h:i A", strtotime($l->fecha));
        }

        return view('backend.admin.ordenes.comentarios.tablacomentarios', compact('lista'));
    }


}
