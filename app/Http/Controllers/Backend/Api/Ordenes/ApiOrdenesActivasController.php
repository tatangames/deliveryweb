<?php

namespace App\Http\Controllers\Backend\api\Ordenes;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\DireccionCliente;
use App\Models\MonederoDevuelto;
use App\Models\MotoristaExperiencia;
use App\Models\MotoristasOrdenes;
use App\Models\Ordenes;
use App\Models\OrdenesCupones;
use App\Models\OrdenesDescripcion;
use App\Models\OrdenesDirecciones;
use App\Models\Servicios;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class ApiOrdenesActivasController extends Controller
{
    public function ordenesActivas(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'clienteid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Cliente::where('id', $request->clienteid)->first()){
            $orden = Ordenes::where('clientes_id', $request->clienteid)
                ->where('visible', 1)
                ->orderBy('id', 'DESC')
                ->get();

            foreach($orden as $o){
                $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));

                $infoServicio = Servicios::where('id', $o->servicios_id)->first();
                $infoDireccion = OrdenesDirecciones::where('ordenes_id', $o->id)->first();

                $o->nombreservicio = $infoServicio->nombre;
                $o->direccion = $infoDireccion->direccion;

                $sumado = $o->precio_consumido + $o->precio_envio;
                $sumado = number_format((float)$sumado, 2, '.', '');
                $o->total = $sumado;

                $cupon = "";
                $aplicoCupon = 0;
                $metodoPago = "Efectivo";
                if($infoDireccion->metodo_pago == 1){
                   $metodoPago = "Efectivo";
                }
                else if($infoDireccion->metodo_pago == 2){
                    $metodoPago = "Monedero";
                }

                $o->metodopago = $metodoPago;

                // buscar si aplico cupon
                if($oc = OrdenesCupones::where('ordenes_id', $o->id)->first()) {
                    $cupon = $oc->nombre_cupon;

                    // total pagado

                    if($oc->tipocupon_id == 1){
                        // envio gratis
                        $cupon = "Envío gratis";
                        $aplicoCupon = 1;

                        // sera el precio_consumido ya que se cambia el cargo envio a $0.00
                    }
                    else if($oc->tipocupon_id == 2){
                        $aplicoCupon = 1;
                        $cupon = "Aplica para: " . $oc->nombre_producto;
                    }
                }

                $o->cupon = $cupon;
                $o->aplicacupon = $aplicoCupon;
            }

            return ['success' => 1, 'ordenes' => $orden];
        }else{
            return ['success' => 2];
        }
    }


    public function estadoOrdenesActivas(Request $request){

        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Ordenes::where('id', $request->ordenid)->first()){

            $orden = Ordenes::where('id', $request->ordenid)->get();

            // CLIENTE MIRA EL TIEMPO DEL PROPIETARIO MAS COPIA DEL TIEMPO DE ZONA
            $tiempo = OrdenesDirecciones::where('ordenes_id', $request->ordenid)->first();

            foreach($orden as $o){

                $horaEstimada = "";

                $sumado = $tiempo->copia_tiempo_orden + $o->hora_2;
                $o->hora_2 = $sumado;

                if($o->estado_2 == 1){ // propietario da el tiempo de espera
                    $o->fecha_2 = date("h:i A d-m-Y", strtotime($o->fecha_2));
                }

                if($o->estado_3 == 1){
                    $o->fecha_3 =date("h:i A d-m-Y", strtotime($o->fecha_3));
                }

                if($o->estado_4 == 1){ // orden en preparacion
                    $time1 = Carbon::parse($o->fecha_4);

                    // ya va sumado el tiempo extra de la zona, aqui arriba
                    $horaEstimada = $time1->addMinute($o->hora_2)->format('h:i A d-m-Y');
                }

                $o->horaEstimada = $horaEstimada;

                if($o->estado_5 == 1){
                    $o->fecha_5 = date("h:i A d-m-Y", strtotime($o->fecha_5));
                }

                if($o->estado_6 == 1){
                    $o->fecha_6 = date("h:i A d-m-Y", strtotime($o->fecha_6));
                }

                if($o->estado_7 == 1){
                    $o->fecha_7 = date("h:i A d-m-Y", strtotime($o->fecha_7));
                }

                if($o->estado_8 == 1){
                    $o->fecha_8 = date("h:i A d-m-Y", strtotime($o->fecha_8));
                }

                $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));
            }

            return ['success' => 1, 'ordenes' => $orden];
        }else{
            return ['success' => 2];
        }
    }


    public function listadoProductosOrdenes(Request $request){

        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Ordenes::where('id', $request->ordenid)->first()){
            $producto = DB::table('ordenes AS o')
                ->join('ordenes_descripcion AS od', 'od.ordenes_id', '=', 'o.id')
                ->join('producto AS p', 'p.id', '=', 'od.producto_id')
                ->select('od.id AS productoID', 'od.nombre', 'p.utiliza_imagen', 'p.imagen', 'od.precio', 'od.cantidad')
                ->where('o.id', $request->ordenid)
                ->get();

            foreach($producto as $p){
                $cantidad = $p->cantidad;
                $precio = $p->precio;
                $multi = $cantidad * $precio;
                $p->multiplicado = number_format((float)$multi, 2, '.', '');
            }

            return ['success' => 1, 'productos' => $producto];
        }else{
            return ['success' => 2];
        }
    }


    public function listadoProductosOrdenesIndividual(Request $request){

        $reglaDatos = array(
            'productoid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(OrdenesDescripcion::where('id', $request->productoid)->first()){

            $producto = DB::table('ordenes_descripcion AS o')
                ->join('producto AS p', 'p.id', '=', 'o.producto_id')
                ->select('p.imagen', 'o.nombre', 'p.descripcion', 'p.utiliza_imagen', 'o.precio', 'o.cantidad', 'o.nota')
                ->where('o.id', $request->productoid)
                ->get();

            foreach($producto as $p){
                $cantidad = $p->cantidad;
                $precio = $p->precio;
                $multi = $cantidad * $precio;
                $p->multiplicado = number_format((float)$multi, 2, '.', '');
            }

            return ['success' => 1, 'productos' => $producto];
        }else{
            return ['success' => 2];
        }
    }


    public function cancelarOrdenCliente(Request $request){

        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($orden = Ordenes::where('id', $request->ordenid)->first()){

            if($orden->estado_8 == 0){

                // seguro para evitar cancelar cuando servicio inicia a preparar orden
                if($orden->estado_4 == 1){
                    return ['success' => 1];
                }

                DB::beginTransaction();

                try {

                    $fecha = Carbon::now('America/El_Salvador');
                    Ordenes::where('id', $request->ordenid)->update(['estado_8' => 1,
                        'cancelado_cliente' => 1,
                        'visible' => 0,
                        'fecha_8' => $fecha]);


                    $infoOrdenesDireccion = OrdenesDirecciones::where('ordenes_id', $orden->id)->first();

                    // verificar si fue pagado con monedero
                    if($infoOrdenesDireccion->metodo_pago == 2){

                        if(MonederoDevuelto::where('ordenes_id', $orden->id)->first()){
                            // ya existe
                        }else{

                            $sumado = $orden->precio_consumido + $orden->precio_envio;

                            $reg = new MonederoDevuelto();
                            $reg->fecha = $fecha;
                            $reg->ordenes_id = $orden->id;
                            $reg->dinero = $sumado;
                            $reg->save();

                            // devolver credito al cliente
                            $infoCliente = Cliente::where('id', $orden->clientes_id)->first();

                            $moneda = $infoCliente->monedero + $sumado;

                            Cliente::where('id', $orden->clientes_id)->update(['monedero' => $moneda]);
                        }
                    }

                    DB::commit();
                    return ['success' => 2];

                } catch(\Throwable $e){
                    DB::rollback();
                    return ['success' => 3];
                }



                // notificar a los propietario de la orden cancelada
                /*$propietarios = DB::table('propietarios AS p')
                    ->select('p.device_id')
                    ->where('p.servicios_id', $o->servicios_id)
                    ->where('p.disponibilidad', 1)
                    ->get();

                $pilaUsuarios = array();
                foreach($propietarios as $m){
                    if(!empty($m->device_id)){
                        if($m->device_id != "0000"){
                            array_push($pilaUsuarios, $m->device_id);
                        }
                    }
                }

                // enviar notificaciones a todos los propietarios asignados
                $titulo = "Orden #".$o->id . " Cancelada";
                $mensaje = "Orden cancelada por el cliente.";

                if(!empty($pilaUsuarios)){
                    try {
                        $this->envioNoticacionPropietario($titulo, $mensaje, $pilaUsuarios);
                    } catch (Exception $e) {

                    }
                }*/

               /* if($data->tipo_pago == 1){

                    //*** NOTIFICAR ADMINISTRADOR SI ESTA ORDEN FUE PAGADA CON CREDI PUNTOS

                    $administradores = DB::table('administradores')
                        ->where('activo', 1)
                        ->where('disponible', 1)
                        ->get();

                    $pilaAdministradores = array();
                    foreach($administradores as $p){
                        if(!empty($p->device_id)){

                            if($p->device_id != "0000"){
                                array_push($pilaAdministradores, $p->device_id);
                            }
                        }
                    }

                    //si no esta vacio
                    if(!empty($pilaAdministradores)){
                        $tituloa = "ORDEN CANCELADA POR CLIENTE";
                        $mensajea = "Se pago con Credi Puntos";
                        try {
                            $this->envioNoticacionAdministrador2($tituloa, $mensajea, $pilaAdministradores);
                        } catch (Exception $e) {

                        }
                    }
                }*/

            }else{
                return ['success' => 2]; // ya cancelada
            }
        }else{
            return ['success' => 3]; // no encontrada
        }
    }


    public function borrarOrdenCliente(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        // oculta la orden al cliente
        if(Ordenes::where('id', $request->ordenid)->first()){

            Ordenes::where('id', $request->ordenid)->update(['visible' => 0]);

            return ['success' => 1];
        }else{
            return ['success' => 2]; // no encontrada
        }
    }


    public function listado(){

        $lista = DireccionCliente::where('clientes_id', 1)->paginate(15);


        return ['success' => 1, 'lista' => $lista];
    }

    public function calificarEntrega(Request $request){

        $reglaDatos = array(
            'ordenid' => 'required',
            'valor' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($or = Ordenes::where('id', $request->ordenid)->first()){

            if($or->estado_6 == 0){
                return ['success' => 1]; // motorista no asignado aun
            }

            if(MotoristaExperiencia::where('ordenes_id', $or->id)->first()){
                Ordenes::where('id', $or->id)->update(['visible' => 0]);
                return ['success' => 2]; // ya hay una valoracion
            }

            // sacar id del motorista de la orden
            $motoristaDato = MotoristasOrdenes::where('ordenes_id', $or->id)->first();

            $idMotorista = $motoristaDato->motoristas_id;
            $fecha = Carbon::now('America/El_Salvador');

            $nueva = new MotoristaExperiencia;
            $nueva->ordenes_id = $or->id;
            $nueva->motoristas_id = $idMotorista;
            $nueva->experiencia = $request->valor;
            $nueva->mensaje = $request->mensaje;;
            $nueva->fecha = $fecha;
            $nueva->save();

            // ocultar orden al usuario
            Ordenes::where('id', $or->id)->update(['visible' => 0]);

            return ['success' => 3];
        }else{
            return ['success' => 4];
        }
    }

}
