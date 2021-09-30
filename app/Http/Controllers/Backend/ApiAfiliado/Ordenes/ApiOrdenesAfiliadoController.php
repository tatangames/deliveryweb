<?php

namespace App\Http\Controllers\Backend\ApiAfiliado\Ordenes;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\MonederoDevuelto;
use App\Models\Motoristas;
use App\Models\MotoristasOrdenes;
use App\Models\Ordenes;
use App\Models\OrdenesCupones;
use App\Models\OrdenesDescripcion;
use App\Models\OrdenesDirecciones;
use App\Models\Producto;
use App\Models\Propietarios;
use App\Models\Servicios;
use App\Models\ServiciosTipo;
use App\Models\Zona;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use OneSignal;
use Exception;

class ApiOrdenesAfiliadoController extends Controller
{

    public function nuevasOrdenes(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if($p = Propietarios::where('id', $request->id)->first()){

            if($p->activo == 0){
                return ['success'=> 1];
            }

            // obtener comision
            $infoServicio = Servicios::where('id', $p->servicios_id)->first();

            $orden = Ordenes::where('servicios_id', $p->servicios_id)
                ->where('visible_p', 1)
                ->get();

            foreach($orden as $o){

                $infoOrdenesDireccion = OrdenesDirecciones::where('ordenes_id', $o->id)->first();

                $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));

                $comision = ($o->precio_consumido * $infoOrdenesDireccion->copia_comision) / 100;

                $total = $o->precio_consumido - $comision;
                $total = number_format((float)$total, 2, '.', '');

                $o->comision = $infoOrdenesDireccion->copia_comision;
                $o->total_comision = $total;

                $cupon = "";
                $aplicoCupon = 0;
                // informacion de los cupones
                if($oc = OrdenesCupones::where('ordenes_id', $o->id)->first()) {
                    $cupon = $oc->nombre_cupon;
                    $o->tipocupon = $oc->tipocupon_id;
                    // total pagado

                    if($oc->tipocupon_id == 1){
                        // envio gratis
                        $cupon = "Aplico Envío gratis";
                        $aplicoCupon = 1;
                    }
                    else if($oc->tipocupon_id == 2){
                        $aplicoCupon = 1;
                        $cupon = "Aplico para: " . $oc->nombre_producto;
                    }
                }

                $o->cupon = $cupon;
                $o->aplicocupon = $aplicoCupon;
            }



            return ['success' => 2, 'ordenes' => $orden];
        }else{
            return ['success' => 3];
        }
    }

    public function informacionEstadoNuevaOrden(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Ordenes::where('id', $request->ordenid)->first()){

            $orden = DB::table('ordenes AS o')
                ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
                ->select('o.id', 's.nombre', 'o.precio_consumido',
                    'o.fecha_orden', 'o.precio_envio', 'o.estado_2', 'o.fecha_2',
                    'o.hora_2', 'o.estado_3', 'o.fecha_3', 'o.estado_8', 'o.fecha_8',
                    'o.mensaje_8', 's.orden_automatica', 's.tiempo')
                ->where('o.id', $request->ordenid)
                ->get();

            foreach($orden as $o){

                $runtime = $o->tiempo;
                $hh = $runtime / 60;
                $minutos = $runtime % 60;

                $hora = intval($hh);

                $tiempo = "";
                if($o->orden_automatica == 1){
                    if($hora == 0){
                        $estado = "Iniciar la orden con tiempo: " . $minutos . " Minutos";
                        $tiempo = $minutos . " Minutos";
                    }else{
                        if($minutos == 0){
                            $estado = "Iniciar la orden con tiempo: " . $hora . " Horas";
                            $tiempo = $hora . " Horas";
                        }else{
                            $estado = "Iniciar la orden con tiempo: " . $hora . " Horas Y " . $minutos . " Minutos";
                            $tiempo = $hora . " Horas Y " . $minutos . " Minutos";
                        }
                    }

                }else{
                    if($hora == 0){
                        $estado = "Se le preguntara al cliente si espera: " . $minutos . " Minutos";
                        $tiempo = $minutos . " Minutos";
                    }else{
                        if($minutos == 0){
                            $estado = "Se le preguntara al cliente si espera: ". $hora . " Horas";
                            $tiempo = $hora . " Horas";
                        }else{
                            $estado = "Se le preguntara al cliente si espera: ". $hora . " Horas Y " . $minutos . " Minutos";
                            $tiempo = $hora . " Horas Y " . $minutos . " Minutos";
                        }
                    }
                }

                $o->msj2 = $tiempo;
                $o->msj1 = $estado;

                if($o->estado_2 == 1){ // propietario da el tiempo de espera

                    $fechaE2 = $o->fecha_2;
                    $hora2 = date("h:i A", strtotime($fechaE2));
                    $fecha2 = date("d-m-Y", strtotime($fechaE2));

                    $o->fecha_2 = $hora2 . " " . $fecha2;
                }

                if($o->estado_3 == 1){
                    $fechaE3 = $o->fecha_3;
                    $hora3 = date("h:i A", strtotime($fechaE3));
                    $fecha3 = date("d-m-Y", strtotime($fechaE3));
                    $o->fecha_3 = $hora3 . " " . $fecha3;
                }

                if($o->estado_8 == 1){
                    $fechaE8 = $o->fecha_8;
                    $hora8 = date("h:i A", strtotime($fechaE8));
                    $fecha8 = date("d-m-Y", strtotime($fechaE8));
                    $o->fecha_8 = $hora8 . " " . $fecha8;
                }

                $fechaOrden = $o->fecha_orden;
                $hora = date("h:i A", strtotime($fechaOrden));
                $fecha = date("d-m-Y", strtotime($fechaOrden));
                $o->fecha_orden = $hora . " " . $fecha;
            }

            return ['success' => 1, 'ordenes' => $orden];
        }else{
            return ['success' => 2];
        }
    }


    public function listadoProductosOrden(Request $request){

        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        // buscar la orden
        if(Ordenes::where('id', $request->ordenid)->first()){
            $producto = DB::table('ordenes AS o')
                ->join('ordenes_descripcion AS od', 'od.ordenes_id', '=', 'o.id')
                ->join('producto AS p', 'p.id', '=', 'od.producto_id')
                ->select('od.id AS productoID', 'p.nombre', 'od.nota', 'p.utiliza_imagen', 'p.imagen', 'od.precio', 'od.cantidad')
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

    public function listaOrdenProductoIndividual(Request $request){

        $reglaDatos = array(
            'ordenesid' => 'required' // id tabla orden_descripcion
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        // producto descripcion
        if(OrdenesDescripcion::where('id', $request->ordenesid)->first()){

            $producto = DB::table('ordenes_descripcion AS o')
                ->join('producto AS p', 'p.id', '=', 'o.producto_id')
                ->select('p.imagen', 'p.nombre', 'p.descripcion', 'p.utiliza_imagen', 'o.precio', 'o.cantidad', 'o.nota')
                ->where('o.id', $request->ordenesid)
                ->get();

            foreach($producto as $p){
                $cantidad = $p->cantidad;
                $precio = $p->precio;
                $multi = $cantidad * $precio;
                $p->multiplicado = number_format((float)$multi, 2, '.', '');
                $p->descripcion = $p->descripcion;
            }

            return ['success' => 1, 'productos' => $producto];
        }else{
            return ['success' => 2];
        }
    }

    public function cancelarOrden(Request $request){
        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($o = Ordenes::where('id', $request->ordenid)->first()){

            DB::beginTransaction();

            try {

                // cancelar si aun no ha sido cancelada
                // esta orden aun no ha iniciado su preparacion
                if($o->estado_8 == 0 && $o->estado_4 == 0){

                    $fecha = Carbon::now('America/El_Salvador');

                    Ordenes::where('id', $request->ordenid)->update(['estado_8' => 1, 'visible_p' => 0,
                        'cancelado' => 2, 'fecha_8' => $fecha, 'mensaje_8' => $request->mensaje]);

                    $infoOrdenesDireccion = OrdenesDirecciones::where('ordenes_id', $o->id)->first();

                    // verificar si fue pagado con monedero
                    if($infoOrdenesDireccion->metodo_pago == 2){

                        if(MonederoDevuelto::where('ordenes_id', $o->id)->first()){
                            // ya existe
                        }else{
                            $sumado = $o->precio_consumido + $o->precio_envio;

                            $reg = new MonederoDevuelto();
                            $reg->fecha = $fecha;
                            $reg->ordenes_id = $o->id;
                            $reg->dinero = $sumado;
                            $reg->save();

                            // devolver credito al cliente
                            $infoCliente = Cliente::where('id', $o->clientes_id)->first();

                            $moneda = $infoCliente->monedero + $sumado;

                            Cliente::where('id', $o->clientes_id)->update(['monedero' => $moneda]);
                        }
                    }

                    DB::commit();

                    return ['success' => 1];

                }else{
                    return ['success' => 2]; // ya cancelada
                }
            } catch(\Throwable $e){
                DB::rollback();
                return ['success' => 3];
            }
        }else{
            return ['success' => 3]; // no encontrada
        }

    }


    public function borrarOrden(Request $request){

        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Ordenes::where('id', $request->ordenid)->first()){

            Ordenes::where('id', $request->ordenid)->update(['visible_p' => 0]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function procesarOrdenEstado2(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'ordenid' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($or = Ordenes::where('id', $request->ordenid)->first()){

            // aun no se ha establecido tiempo de espera
            if($or->estado_2 == 0){

                $fecha = Carbon::now('America/El_Salvador');

                // verificar si sera orden automatica, iniciar preparacion de orden
                $infoServicio = Servicios::where('id', $or->servicios_id)->first();

                // buscar tiempo extra que se sumara por cada zona
                $infoOrdenDireccion = OrdenesDirecciones::where('ordenes_id', $request->ordenid)->first();

                $usuario = Cliente::where('id', $or->clientes_id)->first();

                // contestacion hasta estado 4
                if($infoServicio->orden_automatica == 1){

                    // notificacion para el cliente
                    $tituloC1 = "Orden iniciada";
                    $mensajeC1 = "Seguir el estado de su orden";

                    $tituloC2 = "Solicitud Nueva";
                    $mensajeC2 = "Revisar lista de las ordenes";

                    // tiempo de la orden automatica

                    Ordenes::where('id', $request->ordenid)->update(['estado_2' => 1,
                        'fecha_2' => $fecha, 'hora_2' => $infoServicio->tiempo, 'estado_3' => 1, 'fecha_3' => $fecha,
                        'estado_4' => 1, 'fecha_4' => $fecha, 'visible_p' => 0, 'visible_p2' => 1, 'visible_p3' => 1]);

                    // mandar notificacion a los motoristas asignados al servicio
                    $moto = DB::table('motoristas_asignados AS ms')
                        ->join('motoristas AS m', 'm.id', '=', 'ms.motoristas_id')
                        ->select('m.activo', 'm.disponible', 'ms.servicios_id', 'm.device_id')
                        ->where('m.activo', 1)
                        ->where('m.disponible', 1)
                        ->where('ms.servicios_id', $or->servicios_id)
                        ->get();

                    $pilaMotorista = array();
                    foreach($moto as $p){
                        if($p->token_fcm != null){
                            array_push($pilaMotorista, $p->token_fcm);
                        }
                    }

                    if(!empty($pilaMotorista)) {
                        try {
                            $this->envioNoticacionMotorista($tituloC2, $mensajeC2, $pilaMotorista);
                        } catch (Exception $e) {

                        }
                    }

                    // notificacion cliente

                    if($usuario->token_fcm != null){
                        try {
                            $this->envioNoticacionCliente($tituloC1, $mensajeC2, $usuario->token_fcm);
                        } catch (Exception $e) {

                        }
                    }

                    return ['success' => 1];
                }

                $tituloMM1 = "Orden aceptada";
                $mensajeMM1 = "Revisar tiempo aproximado de entrega";

                Ordenes::where('id', $request->ordenid)->update(['estado_2' => 1,
                    'fecha_2' => $fecha, 'hora_2' => $infoServicio->tiempo]);

                // mandar notificacion al cliente si quiere esperar

                if($usuario->token_fcm != null){
                    try {
                        $this->envioNoticacionCliente($tituloMM1, $mensajeMM1, $usuario->token_fcm);
                    } catch (Exception $e) {

                    }
                }

                return ['success' => 2];
            }else{
                // tiempo ya habia sido establecido
                return ['success'=> 1];
            }
        }else{
            // numero de orden no encontrado
            return ['success'=> 2];
        }
    }


    public function procesarOrdenEstado3(Request $request){

        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($or = Ordenes::where('id', $request->ordenid)->first()){

            if($or->estado_3 == 0){

                $fecha = Carbon::now('America/El_Salvador');

                Ordenes::where('id', $request->ordenid)->update(['estado_3' => 1,
                    'fecha_3' => $fecha]);

                // mandar notificacion al propietario
                $propietarios = Propietarios::where('servicios_id', $or->servicios_id)
                    ->where('disponibilidad', 1)
                    ->where('activo', 1)
                    ->get();

                $pilaAfiliado = array();

                foreach($propietarios as $p){
                    if($p->token_fcm != null){
                        array_push($pilaAfiliado, $p->token_fcm);
                    }
                }

                $tituloP1 = "Cliente acepto tiempo";
                $mensajeP1 = "El cliente desea esperar la orden";

                if(!empty($pilaAfiliado)){
                    try {
                        $this->envioNoticacionAfiliado($tituloP1, $mensajeP1, $pilaAfiliado);
                    } catch (Exception $e) {

                    }
                }

                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }else{
            // id orden no encontrada
            return ['success' => 3];
        }
    }


    public function procesarOrdenEstado4(Request $request){

        $reglaDatos = array(
            'ordenid' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($or = Ordenes::where('id', $request->ordenid)->first()){

            if($or->estado_4 == 0 && $or->estado_8 == 0){

                $fecha = Carbon::now('America/El_Salvador');

                Ordenes::where('id', $request->ordenid)->update(['estado_4' => 1,
                    'fecha_4' => $fecha, 'visible_p' => 0, 'visible_p2' => 1, 'visible_p3' => 1]);

                // mandar notificacion al cliente
                $usuario = Cliente::where('id', $or->clientes_id)->first();

                $tituloC1 = "Orden iniciada";
                $mensajeC1 = "Su orden empieza a prepararse";

                if($usuario->token_fcm != null){
                    try {
                        $this->envioNoticacionCliente($tituloC1, $mensajeC1, $usuario->token_fcm);
                    } catch (Exception $e) {

                    }
                }

                // mandar notificacion a los motoristas asignados al servicio
                $moto = DB::table('motoristas_asignados AS ms')
                    ->join('motoristas AS m', 'm.id', '=', 'ms.motoristas_id')
                    ->select('m.activo', 'm.disponible', 'ms.servicios_id', 'm.token_fcm')
                    ->where('m.activo', 1)
                    ->where('m.disponible', 1)
                    ->where('ms.servicios_id', $or->servicios_id)
                    ->get();

                $pilaMoto = array();

                foreach($moto as $p){
                    if($p->token_fcm != null){
                        array_push($pilaMoto, $p->token_fcm);
                    }
                }

                $tituloP1 = "Solicitud Nueva";
                $mensajeP1 = "Se necesita motorista";

                // NOTIFICACION A LOS MOTORISTAS
                if(!empty($pilaMoto)) {
                    try {
                        $this->envioNoticacionMotorista($tituloP1, $mensajeP1, $pilaMoto);
                    } catch (Exception $e) {

                    }
                }

                return ['success' => 1];

            }else{
                return ['success'=> 2];
            }
        }else{
            return ['success'=> 3];
        }
    }

    public function listadoPreparandoOrdenes(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $rules);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($p = Propietarios::where('id', $request->id)->first()){

            // obtener comision

            $orden = Ordenes::where('estado_8', 0) // ordenes no canceladas
                ->where('servicios_id', $p->servicios_id)
                ->where('visible_p2', 1) // estan en preparacion
                ->where('visible_p3', 1) // aun sin terminar de preparar
                ->where('estado_4', 1) // orden estado 4 preparacion
                ->get();

            foreach($orden as $o){

                $infoOrden = OrdenesDirecciones::where('ordenes_id', $o->id)->first();

                $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_4));

                $time1 = Carbon::parse($o->fecha_4);
                $horaEstimada = $time1->addMinute($o->hora_2)->format('h:i A d-m-Y');
                $o->horaEstimada = $horaEstimada;

                $comision = ($o->precio_consumido * $infoOrden->copia_comision) / 100;

                $total = $o->precio_consumido - $comision;
                $total = number_format((float)$total, 2, '.', '');

                $o->comision = $infoOrden->copia_comision;
                $o->total_comision = $total;

                $cupon = "";
                $aplicoCupon = 0;
                // informacion de los cupones
                if($oc = OrdenesCupones::where('ordenes_id', $o->id)->first()) {
                    $cupon = $oc->nombre_cupon;
                    $o->tipocupon = $oc->tipocupon_id;
                    // total pagado

                    if($oc->tipocupon_id == 1){
                        // envio gratis
                        $cupon = "Aplico Envío gratis";
                        $aplicoCupon = 1;
                    }
                    else if($oc->tipocupon_id == 2){
                        $aplicoCupon = 1;
                        $cupon = "Aplico para: " . $oc->nombre_producto;
                    }
                }

                $o->cupon = $cupon;
                $o->aplicocupon = $aplicoCupon;
            }

            return ['success' => 1, 'ordenes' => $orden];
        }else{
            return ['success' => 2];
        }
    }


    public function informacionOrdenEnPreparacion(Request $request){

        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Ordenes::where('id', $request->ordenid)->first()){

            $orden = Ordenes::where('id', $request->ordenid)->get();

            foreach ($orden as $oo){
                $inicio = Carbon::parse($oo->fecha_4);
                $horaEstimada = $inicio->addMinute($oo->hora_2)->format('h:i A');
                $oo->horaEstimada = $horaEstimada;
            }

            return ['success' => 1, 'ordenes' => $orden];
        }else{
            return ['success' => 2];
        }
    }

    public function finalizarOrden(Request $request){

        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($o = Ordenes::where('id', $request->ordenid)->first()){

            $fechahoy = Carbon::now('America/El_Salvador');

            if($o->estado_5 == 0){
                Ordenes::where('id', $request->ordenid)->update(['visible_p2' => 0, 'visible_p3' => 0,
                    'estado_5' => 1, 'fecha_5' => $fechahoy]);
            }

            // buscar si esta orden aun no tiene motorista
            $hay = 0;
            if(MotoristasOrdenes::where('ordenes_id', $o->id)->first()){
                $hay = 1;
            }

            // SIGNIFICA QUE NO TIENE MOTORISTA ASIGNADO AUN LA ORDEN
            // MANDAR NOTIFICACION AL MOTORISTA QUE YA ESTA LA ORDEN
            if($hay == 0) {

                $moto = DB::table('motoristas_asignados AS ms')
                    ->join('motoristas AS m', 'm.id', '=', 'ms.motoristas_id')
                    ->where('m.activo', 1)
                    ->where('m.disponible', 1)
                    ->where('ms.servicios_id', $o->servicios_id)
                    ->get();

                $pilaMoto = array();

                $tituloCC1 = "Orden urgente";
                $mensajeCC1 = "Una nueva orden no tiene motorista";

                foreach ($moto as $p) {
                    if ($p->token_fcm != null) {
                        array_push($pilaMoto, $p->token_fcm);
                    }
                }

                if (!empty($pilaMoto)) {
                    try {
                        $this->envioNoticacionMotorista($tituloCC1, $mensajeCC1, $pilaMoto);
                    } catch (Exception $e) {
                    }
                }
            }

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


    public function cancelarOrdenExtra(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($orden = Ordenes::where('id', $request->ordenid)->first()){

            // evitar cancelar si ya dijo que estaba completada
            if($orden->estado_5 == 1){
                // orden no puede ser cancelada
                return ['success' => 1];
            }

            // aun no ha sido cancelada
            if($orden->estado_8 == 0){

                DB::beginTransaction();

                try {

                    $fechahoy = Carbon::now('America/El_Salvador');

                    Ordenes::where('id', $request->ordenid)->update(['estado_8' => 1, 'fecha_8' => $fechahoy,
                        'mensaje_8' => $request->mensaje, 'cancelado' => 2]);

                    $infoOrdenesDireccion = OrdenesDirecciones::where('ordenes_id', $orden->id)->first();

                    // verificar si fue pagado con monedero
                    if($infoOrdenesDireccion->metodo_pago == 2){

                        if(MonederoDevuelto::where('ordenes_id', $orden->id)->first()){
                            // ya existe
                        }else{

                            $sumado = $orden->precio_consumido + $orden->precio_envio;

                            $reg = new MonederoDevuelto();
                            $reg->fecha = $fechahoy;
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


                    // mandar notificacion al cliente

                    $titulo1 = "Orden cancelada";
                    $mensaje1 = "El Negocio cancelado su orden";

                    $usuario = Cliente::where('id', $orden->clientes_id)->first();

                        if($usuario->token_fcm != null){
                             try {
                                 $this->envioNoticacionCliente($titulo1, $mensaje1, $usuario->token_fcm);
                             } catch (Exception $e) {

                             }
                        }


                    // mandar notificacion al motorista si ya agarro la orden

                     if($moo = MotoristasOrdenes::where('ordenes_id', $request->ordenid)->first()){
                         $dato = Motoristas::where('id', $moo->motoristas_id)->first();
                         $titulo2 = "Orden cancelada";
                         $mensaje2 = "El servicio cancelo la orden";
                         if($dato->token_fcm != null){
                             try {
                                 $this->envioNoticacionMotorista($titulo2, $mensaje2, $dato->token_fcm);
                             } catch (Exception $e) {

                             }
                         }
                     }

                    return ['success' => 2];

                } catch(\Throwable $e){
                    DB::rollback();
                    return ['success' => 3];
                }

            }else{
                return ['success' => 1]; // cancelado anteriormente
            }

        }else{
            return ['success' => 3];
        }
    }

    public function listadoOrdenesCompletadasHoy(Request $request){

        $reglaDatos = array(
            'id' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($p = Propietarios::where('id', $request->id)->first()){

            $orden = Ordenes::where('estado_5', 1)
                ->where('servicios_id', $p->servicios_id)
                ->whereDate('fecha_orden', '=', Carbon::today('America/El_Salvador')->toDateString())
                ->get();

            foreach($orden as $o){

                $infoOrden = OrdenesDirecciones::where('ordenes_id', $o->id)->first();

                $o->fecha_orden = date("h:i A ", strtotime($o->fecha_orden));

                $o->horacompletada = date("h:i A ", strtotime($o->fecha_5));

                $comision = ($o->precio_consumido * $infoOrden->copia_comision) / 100;

                $total = $o->precio_consumido - $comision;
                $total = number_format((float)$total, 2, '.', '');

                $o->comision = $infoOrden->copia_comision;
                $o->total_comision = $total;

                $cupon = "";
                $aplicoCupon = 0;
                // informacion de los cupones
                if($oc = OrdenesCupones::where('ordenes_id', $o->id)->first()) {
                    $cupon = $oc->nombre_cupon;
                    $o->tipocupon = $oc->tipocupon_id;
                    // total pagado

                    if($oc->tipocupon_id == 1){
                        // envio gratis
                        $cupon = "Aplico Envío gratis";
                        $aplicoCupon = 1;
                    }
                    else if($oc->tipocupon_id == 2){
                        $aplicoCupon = 1;
                        $cupon = "Aplico para: " . $oc->nombre_producto;
                    }
                }

                $o->cupon = $cupon;
                $o->aplicocupon = $aplicoCupon;
            }

            return ['success' => 1, 'ordenes' => $orden];
        }else{
            return ['success' => 2];
        }
    }

    public function listadoCategoriasProducto(Request $request){

        $reglaDatos = array(
            'id' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($p = Propietarios::where('id', $request->id)->first()){

            $lista = ServiciosTipo::where('servicios_id', $p->servicios_id)
                ->where('visible', 1) // los ocultos por los admin
                ->orderBy('posicion', 'ASC')
                ->get();

            return ['success' => 1, 'categorias' => $lista];
        }else{
            return ['success' => 2];
        }
    }


    public function listadoCategoriasProductoListado(Request $request){

        $reglaDatos = array(
            'id' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(ServiciosTipo::where('id', $request->id)->first()){

            $lista = Producto::where('servicios_tipo_id', $request->id)
                ->orderBy('posicion', 'ASC')
                ->get();

            return ['success' => 1, 'productos' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionProductoIndividual(Request $request){

        $rules = array(
            'productoid' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $rules);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Producto::where('id', $request->productoid)->first()){

            $producto = Producto::where('id', $request->productoid)->get();

            return ['success'=> 1, 'productos' => $producto];

        }else{
            return ['success'=> 2];
        }
    }


    public function actualizarProducto(Request $request){
        $rules = array(
            'id' => 'required',
            'productoid' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $rules);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($pp = Propietarios::where('id', $request->id)->first()){

            // no puede editar los productos
            if($pp->bloqueado == 1){
                return ['success'=> 1];
            }

            if(Producto::where('id', $request->productoid)->first()){

                Producto::where('id', $request->productoid)->update(['nombre' => $request->nombre,
                    'descripcion' => $request->descripcion, 'precio' => $request->precio,
                    'nota' => $request->nota, 'activo' => $request->estadoactivo,
                    'disponibilidad' => $request->estadodisponible, 'utiliza_nota' => $request->estadonota]);

                return ['success'=> 2];

            }else{
                return ['success'=> 3];
            }
        }else{
            return ['success'=> 3];
        }
    }


    public function historialOrdenesCompletas(Request $request){

        $reglaDatos = array(
            'id' => 'required',
            'fecha1' => 'required',
            'fecha2' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($p = Propietarios::where('id', $request->id)->first()){

            $date1 = Carbon::parse($request->fecha1)->format('Y-m-d');
            $date2 = Carbon::parse($request->fecha2)->addDays(1)->format('Y-m-d');

            $orden = Ordenes::where('estado_5', 1) // orden completada
                ->where('servicios_id', $p->servicios_id)
                ->whereBetween('fecha_orden', array($date1, $date2))
                ->get();

            $conteoOrden = 0;
            $vendido = 0;
            foreach($orden as $o){
                $conteoOrden++;

                $o->fecha_orden = date("d-m-Y h:i A", strtotime($o->fecha_orden));

                $vendido = $vendido + $o->precio_consumido;

                $infoOrden = OrdenesDirecciones::where('ordenes_id', $o->id)->first();

                $comision = ($o->precio_consumido * $infoOrden->copia_comision) / 100;
                $total = $o->precio_consumido - $comision;
                $total = number_format((float)$total, 2, '.', '');
                $o->total_comision = $total;

                $o->comision = $infoOrden->copia_comision;

                $cancelado = "";
                if($o->estado_8 == 1){
                    if($o->cancelado == 1){
                        $cancelado = "Por el Cliente";
                    }else{
                        $cancelado = "Por el Negocio";
                    }
                }

                $o->cance = $cancelado;

                $cupon = "";
                $aplicoCupon = 0;
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
                        $cupon = "Aplica para " . $oc->nombre_producto;
                    }
                }

                $o->cupon = $cupon;
                $o->aplicacupon = $aplicoCupon;
            }

            $vendido = number_format((float)$vendido, 2, '.', '');

            return ['success' => 1, 'ordenes' => $orden,
                'conteo' => $conteoOrden,
                'total' => $vendido];
        }else{
            return ['success' => 2];
        }
    }

    public function envioNoticacionCliente($titulo, $mensaje, $pilaUsuarios){
        OneSignal::notificacionCliente($titulo, $mensaje, $pilaUsuarios);
    }

    public function envioNoticacionAfiliado($titulo, $mensaje, $pilaUsuarios){
        OneSignal::notificacionAfiliado($titulo, $mensaje, $pilaUsuarios);
    }

    public function envioNoticacionMotorista($titulo, $mensaje, $pilaUsuarios){
        OneSignal::notificacionMotorista($titulo, $mensaje, $pilaUsuarios);
    }

}
