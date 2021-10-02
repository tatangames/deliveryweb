<?php

namespace App\Http\Controllers\Backend\ApiMotorista\Ordenes;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Motoristas;
use App\Models\MotoristasAsignados;
use App\Models\MotoristasOrdenes;
use App\Models\Ordenes;
use App\Models\OrdenesCupones;
use App\Models\OrdenesDirecciones;
use App\Models\Servicios;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use OneSignal;
use Exception;

class ApiOrdenesMotoristaController extends Controller
{

    public function verNuevasOrdenes(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if($m = Motoristas::where('id', $request->id)->first()){

            if($m->activo == 0){
                return ['success' => 1];
            }

            $moto = DB::table('motoristas_asignados AS ms')
                ->join('motoristas AS m', 'm.id', '=', 'ms.motoristas_id')
                ->select('ms.servicios_id')
                ->where('motoristas_id', $m->id)
                ->get();

            $noquiero = DB::table('motoristas_ordenes AS mo')->get();

            $pilaOrden = array();
            foreach($noquiero as $p){
                array_push($pilaOrden, $p->ordenes_id);
            }

            $pilaUsuarios = array();
            foreach($moto as $p){
                array_push($pilaUsuarios, $p->servicios_id);
            }

            $orden = DB::table('ordenes AS o')
                ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
                ->select('o.id', 'o.servicios_id', 's.nombre', 'o.estado_4',
                    'o.estado_8', 'o.precio_consumido', 'o.precio_envio', 'o.fecha_4',
                    'o.hora_2', 'o.estado_6', 'o.nota', 'o.tipo_cargo')
                ->where('o.estado_6', 0) // nadie a seteado este
                ->where('o.estado_4', 1) // inicia la orden
                ->where('o.estado_8', 0) // orden no cancelada
                ->whereIn('o.servicios_id', $pilaUsuarios)
                ->whereNotIn('o.id', $pilaOrden)
                ->get();

            foreach($orden as $o){

                $datadir = OrdenesDirecciones::where('ordenes_id', $o->id)->first();
                $o->direccion = $datadir->direccion;

                $time1 = Carbon::parse($o->fecha_4);
                $horaEstimada = $time1->addMinute($o->hora_2)->format('h:i A d-m-Y');
                $o->horaEntrega = $horaEstimada;

            } //end foreach


            return ['success' => 2, 'ordenes' => $orden];
        }else{
            return ['success' => 3];
        }
    }


    public function verOrdenPorID(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($or = Ordenes::where('id', $request->ordenid)->first()){

            //sacar direccion de la orden

            $orden = OrdenesDirecciones::where('ordenes_id', $request->ordenid)->get();

            $servicioid = $or->servicios_id;

            $servicio = DB::table('servicios AS s')
                ->select('s.nombre', 's.telefono', 's.direccion', 's.latitud', 's.longitud')
                ->where('s.id', $servicioid)
                ->get();

            $time1 = Carbon::parse($or->fecha_4);

            $horaEstimada = $time1->addMinute($or->hora_2)->format('h:i A d-m-Y');

            return ['success' => 1, 'cliente' => $orden, 'servicio' => $servicio, 'hora' => $horaEstimada];
        }else{
            return ['success' => 2];
        }
    }


    public function obtenerOrden(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'ordenid' => 'required',
            'id' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Motoristas::where('id', $request->id)->first()){

            if($or = Ordenes::where('id', $request->ordenid)->first()){

                // esta libre aun
                if($or->estado_6 == 0){

                    if($or->estado_8 == 1){
                        return ['success' => 1];
                    }

                    DB::beginTransaction();
                    try {
                        // evitar orden con motorista ya asignado
                        if(MotoristasOrdenes::where('ordenes_id', $request->ordenid)->first()){
                            return ['success' => 2];
                        }

                        // ACTUALIZAR
                        Ordenes::where('id', $request->ordenid)->update(['visible_m' => 1]);

                        $fecha = Carbon::now('America/El_Salvador');

                        $nueva = new MotoristasOrdenes();
                        $nueva->ordenes_id = $or->id;
                        $nueva->motoristas_id = $request->id;
                        $nueva->fecha_agarrada = $fecha;

                        $nueva->save();

                        DB::commit();

                        return ['success' => 3]; // guardado

                    } catch(\Throwable $e){
                        DB::rollback();
                        return ['success' => 5];
                    }
                }else{
                    return ['success' => 2]; // orden ya agarrada por otro motorista
                }
            }else{
                return ['success' => 2]; // orden no encontrada
            }
        }else{
            return ['success' => 2]; // motorista no encontrado
        }
    }

    public function verProcesoOrdenes(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'id' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

            if(Motoristas::where('id', $request->id)->first()){

                // mostrar si fue cancelada para despues setear visible_m

                $orden = DB::table('motoristas_ordenes AS mo')
                    ->join('ordenes AS o', 'o.id', '=', 'mo.ordenes_id')
                    ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
                    ->select('o.id', 'o.precio_consumido', 'o.fecha_4', 'o.hora_2',
                        'o.estado_5', 'o.estado_6', 'o.precio_envio', 's.nombre',
                        's.id AS servicioid', 'o.estado_8', 'o.visible_m',
                        'o.nota', 'o.servicios_id', 's.comision', 's.privado')
                    ->where('o.estado_7', 0) // aun sin entregar al cliente
                    ->where('o.visible_m', 1) // para ver si una orden fue cancelada a los 10 minutos, y el motorista la agarro, asi ver el estado
                    ->where('o.estado_6', 0) // aun no han salido a entregarse
                    ->where('mo.motoristas_id', $request->id)
                    ->get();

                // sumar mas envio
                foreach($orden as $o) {

                    // buscar metodo de pago
                    $infoOrdenes = OrdenesDirecciones::where('ordenes_id', $o->id)->first();

                    //1- efectivo
                    //2- monedero
                    $o->metodopago = $infoOrdenes->metodo_pago;

                    $fechaOrden = Carbon::parse($o->fecha_4);
                    $horaEstimadaEntrega = $fechaOrden->addMinute($o->hora_2)->format('h:i A d-m-Y');
                    $o->fecharecoger = $horaEstimadaEntrega;

                    // informacion para pagarle al propietario

                    if($o->privado == 1){
                        // este servicio es privado, utiliza sus motoristas
                        $o->total = "-";
                    }else{
                        $comision = ($o->precio_consumido * $o->comision) / 100;
                        $total = $o->precio_consumido - $comision;
                        $total = number_format((float)$total, 2, '.', '');
                        $o->total = $total;
                    }

                    $suma = $o->precio_consumido + $o->precio_envio;

                    // Monedero
                    if($infoOrdenes->metodo_pago == 2){
                        $suma = "0.00 (Monedero)";
                    }

                    $o->precio_consumido = $suma;


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

                return ['success' => 1, 'ordenes' => $orden];
            }else{
                return ['success' => 2];
            }
    }


    public function verProductosOrden(Request $request){
        // validaciones para los datos
        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos );

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Ordenes::where('id', $request->ordenid)->first()){

            $producto = DB::table('ordenes AS o')
                ->join('ordenes_descripcion AS od', 'od.ordenes_id', '=', 'o.id')
                ->join('producto AS p', 'p.id', '=', 'od.producto_id')
                ->select('od.id AS productoID', 'od.nombre', 'od.nota',
                    'p.imagen', 'p.utiliza_imagen', 'od.precio', 'od.cantidad')
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
            return ['success' => 3];
        }
    }

    public function verOrdenProcesoPorID(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($or = Ordenes::where('id', $request->ordenid)->first()){

            //sacar direccion de la orden

            $orden = DB::table('ordenes_direcciones AS o')
                ->select('o.nombre', 'o.direccion',
                    'o.numero_casa', 'o.punto_referencia',
                    'o.latitud', 'o.longitud')
                ->where('o.ordenes_id', $request->ordenid)
                ->get();

            $servicioid = $or->servicios_id;

            $servicio = DB::table('servicios AS s')
                ->select('s.nombre', 's.telefono', 's.direccion', 's.latitud', 's.longitud')
                ->where('s.id', $servicioid)
                ->get();

            $time1 = Carbon::parse($or->fecha_4);

            $horaEstimada = $time1->addMinute($or->hora_2)->format('h:i A');

            // titulo que dira la notificacion, cuando se alerte al cliente que esta llegando su pedido.
            $mensaje = "Su orden #" . $request->ordenid . " esta llegando";

            return ['success' => 1, 'cliente' => $orden,
                'servicio' => $servicio, 'hora' => $horaEstimada,
                'estado' => $or->estado_6, 'cancelado' => $or->estado_8, 'mensaje' => $mensaje];
        }else{
            return ['success' => 2];
        }
    }

    public function iniciarEntrega(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($or = Ordenes::where('id', $request->ordenid)->first()){

            if($or->estado_8 == 1){
                return ['success' => 1];
            }
            // orden ya fue preparada por el propietario
            if($or->estado_5 == 1 && $or->estado_6 == 0){

                $fecha = Carbon::now('America/El_Salvador');
                Ordenes::where('id', $request->ordenid)->update(['estado_6' => 1,
                    'fecha_6' => $fecha]);

                // notificacion al cliente
                $usuario = Cliente::where('id', $or->clientes_id)->first();

                $titulo = "Orden #". $or->id ." Preparada";
                $mensaje = "El motorista va encamino";

                if($usuario->token_fcm != null){ // evitar id malos
                    try {
                        $this->envioNoticacionCliente($titulo, $mensaje, $usuario->token_fcm);
                    } catch (Exception $e) {

                    }
                }


                return ['success' => 2]; //orden va en camino
            }else{
                return ['success' => 3]; // la orden aun no ha sido preparada
            }
        }else{
            return ['success' => 4];
        }
    }

    public function borrarOrdenCancelada(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        // ocultar visibilidad
        if(Ordenes::where('id', $request->ordenid)->first()){
            Ordenes::where('id', $request->ordenid)->update(['visible_m' => 0]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function verProcesoOrdenesEntrega(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'id' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Motoristas::where('id', $request->id)->first()){

            // mostrar si fue cancelada para despues setear visible_m

            $orden = DB::table('motoristas_ordenes AS mo')
                ->join('ordenes AS o', 'o.id', '=', 'mo.ordenes_id')
                ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
                ->select('o.id', 'o.precio_consumido', 'o.fecha_4', 'o.hora_2',
                    'o.estado_5', 'o.estado_6', 'o.precio_envio', 's.nombre',
                    's.id AS servicioid', 'o.estado_8', 'o.visible_m',
                    'o.nota', 'o.servicios_id', 's.comision', 's.privado')
                ->where('o.estado_7', 0) // aun sin entregar al cliente
                ->where('o.visible_m', 1) // para ver si una orden fue cancelada a los 10 minutos, y el motorista la agarro, asi ver el estado
                ->where('o.estado_6', 1) // van a entregarse
                ->where('mo.motoristas_id', $request->id)
                ->get();

            // sumar mas envio
            foreach($orden as $o){

                // Tiempo dado por propietario + tiempo de zona extra
                $infoOrdenes = OrdenesDirecciones::where('ordenes_id', $o->id)->first();

                $tiempoorden = $infoOrdenes->copia_tiempo_orden + $o->hora_2;
                $fechaOrden = Carbon::parse($o->fecha_4);
                $o->fecharecoger = $fechaOrden->addMinute($tiempoorden)->format('h:i A');

                // DATOS PARA PAGAR A PROPIETARIO

                $comision = ($o->precio_consumido * $o->comision) / 100;

                $total = $o->precio_consumido - $comision;
                $total = number_format((float)$total, 2, '.', '');

                // total que se pagara a propietario, sino es privado
                if($o->privado == 0) {
                    $o->total = $total;
                }else{
                    $o->total = "-";
                }

                // DATOS QUE SE COBRARA AL CLIENTE

                // monedero
                if($infoOrdenes->metodo_pago == 2){
                    $suma = "0.00 Monedero";
                }else{
                    $suma = number_format((float)$o->precio_consumido + $o->precio_envio, 2, '.', '');
                }

                $o->precio_consumido = $suma;

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

            return ['success' => 1, 'ordenes' => $orden];
        }else{
            return ['success' => 2];
        }
    }

    public function notificarClienteOrden(Request $request){
        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        // obtener usuario de la orden
        if($o = Ordenes::where('id', $request->ordenid)->first()){

            $datos = Cliente::where('id', $o->clientes_id)->first();

            if($datos->token_fcm != null){

                $titulo = "El motorista se encuentra cerca de tu ubicación";
                $mensaje = "Su orden esta cerca";

                try {
                    $this->envioNoticacionCliente($titulo, $mensaje, $datos->token_fcm);
                } catch (Exception $e) {

                }

                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }


    public function finalizarEntrega(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($or = Ordenes::where('id', $request->ordenid)->first()){

            if($or->estado_7 == 0){

                $fecha = Carbon::now('America/El_Salvador');
                Ordenes::where('id', $request->ordenid)->update(['estado_7' => 1,
                    'fecha_7' => $fecha, 'visible_m' => 0]);

                // notificacion al cliente
                $usuario = Cliente::where('id', $or->clientes_id)->first();

                $titulo = "Orden Completada";
                $mensaje = "Muchas gracias por su compra";

                if($usuario->token_fcm != null){
                    try {
                        $this->envioNoticacionCliente($titulo, $mensaje, $usuario->token_fcm);
                    } catch (Exception $e) {

                    }
                }


                return ['success' => 1]; // orden completada
            }else{
                return ['success' => 2]; // ya habia seteado el campo
            }
        }else{
            return ['success' => 3];
        }
    }

    public function informacionCuenta(Request $request){
        $rules = array(
            'id' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $rules);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($p = Motoristas::where('id', $request->id)->first()){

            return ['success'=> 1, 'nombre' => $p->nombre];
        }else{
            return ['success'=> 2];
        }
    }

    public function actualizarPassword(Request $request){
        $rules = array(
            'id' => 'required',
            'password' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $rules);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Motoristas::where('id', $request->id)->first()){

            Motoristas::where('id', $request->id)->update(['password' => Hash::make($request->password)]);

            return ['success'=> 1];
        }else{
            return ['success'=> 2];
        }
    }

    public function informacionDisponibilidad(Request $request){
        $rules = array(
            'id' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $rules);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($p = Motoristas::where('id', $request->id)->first()){

            return ['success'=> 1, 'disponibilidad' => $p->disponible];
        }else{
            return ['success'=> 2];
        }
    }


    public function modificarDisponibilidad(Request $request){
        $rules = array(
            'id' => 'required',
            'valor' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $rules);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Motoristas::where('id', $request->id)->first()){

            Motoristas::where('id', $request->id)->update(['disponible' => $request->valor]);

            return ['success'=> 1];
        }else{
            return ['success'=> 2]; // motorista no encontrado
        }
    }


    public function verHistorial(Request $request){
        $reglaDatos = array(
            'id' => 'required',
            'fecha1' => 'required',
            'fecha2' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos );

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Motoristas::where('id', $request->id)->first()){

            $start = Carbon::parse($request->fecha1)->startOfDay();
            $end = Carbon::parse($request->fecha2)->endOfDay();

            $orden = DB::table('motoristas_ordenes AS m')
                ->join('ordenes AS o', 'o.id', '=', 'm.ordenes_id')
                ->select('o.id', 'o.precio_consumido', 'o.precio_envio', 'o.fecha_orden',
                    'm.motoristas_id', 'o.ganancia_motorista', 'o.estado_7', 'o.servicios_id', 'o.nota')
                ->where('o.estado_7', 1) // solo completadas
                ->where('m.motoristas_id', $request->id) // del motorista
                ->whereBetween('o.fecha_orden', [$start, $end])
                ->orderBy('o.id', 'DESC')
                ->get();

            $totalOrdenes = 0;
            $sumadoGanancia = 0;
            foreach($orden as $o){
                $totalOrdenes++;

                $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));

                // nombre servicio
                $infoServicio = Servicios::where('id', $o->servicios_id)->first();
                $o->servicio = $infoServicio->nombre;

                // sacar direccion guardada de la orden
                $infoOrdenes = OrdenesDirecciones::where('ordenes_id', $o->id)->first();

                $o->direccion = $infoOrdenes->direccion;

                // ver si servicio es privado
                if($infoOrdenes->privado == 1){
                    // no sumar nada
                    $ganancia = 0;
                }else{
                    $ganancia = $o->ganancia_motorista;
                    $sumadoGanancia = $sumadoGanancia + $o->ganancia_motorista;
                }

                $ganancia = number_format((float)$ganancia, 2, '.', '');

                $o->ganancia = "$".$ganancia;

                $o->privado = $infoServicio->privado;

                if($o->privado == 1){
                    // este servicio es privado, utiliza sus motoristas
                    $o->total = "-";
                }else{
                    $comision = ($o->precio_consumido * $infoServicio->comision) / 100;
                    $total = $o->precio_consumido - $comision;
                    $total = number_format((float)$total, 2, '.', '');
                    $o->total = "$".$total;
                }

                // cobrado a clientes - Efectivo
                if($infoOrdenes->metodo_pago == 1){
                    $resultado = $o->precio_consumido - $o->precio_envio;
                }else{
                    $resultado = "$0.00 (Monedero)";
                }

                $resultado = number_format((float)$resultado, 2, '.', '');
                $o->resultado = $resultado;

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

            $sumadoGanancia = number_format((float)$sumadoGanancia, 2, '.', '');

            return ['success' => 1,
                'historial' => $orden,
                'ganancia' => $sumadoGanancia,
                'conteo' => $totalOrdenes
                ];

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
