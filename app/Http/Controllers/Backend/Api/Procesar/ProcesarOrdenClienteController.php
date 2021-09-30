<?php

namespace App\Http\Controllers\Backend\api\Procesar;

use App\Http\Controllers\Controller;
use App\Models\CarritoExtra;
use App\Models\CarritoTemporal;
use App\Models\Cliente;
use App\Models\CuponDescuento;
use App\Models\CuponEnvio;
use App\Models\Cupones;
use App\Models\CuponPorcentaje;
use App\Models\CuponProducto;
use App\Models\CuponServicios;
use App\Models\CuponZonas;
use App\Models\DireccionCliente;
use App\Models\HorarioServicio;
use App\Models\InformacionAdmin;
use App\Models\Ordenes;
use App\Models\OrdenesCupones;
use App\Models\OrdenesDescripcion;
use App\Models\OrdenesDirecciones;
use App\Models\Producto;
use App\Models\Servicios;
use App\Models\Zona;
use App\Models\ZonasServicio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use OneSignal;
use Exception;

class ProcesarOrdenClienteController extends Controller
{

    public function verOrdenAProcesarCliente(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required',
            'metodo' => 'required' //1: efectivo  2: monedero
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        try {
            // preguntar si usuario ya tiene un carrito de compras
            if($cart = CarritoTemporal::where('clientes_id', $request->clienteid)->first()){

                $infoCliente = Cliente::where('id', $request->clienteid)->first();

                // sacar id del servicio del carrito
                $servicioidC = $cart->servicios_id;
                $zonaiduser = 0;

                $direccionCliente = "";

                // sacar id zona del usuario
                if($userDire = DireccionCliente::where('clientes_id', $request->clienteid)
                    ->where('seleccionado', 1)->first()){

                    $direccionCliente = $userDire->direccion;
                    $zonaiduser = $userDire->zonas_id; // zona id donde esta el usuario
                }

                $resultado = 0; // sub total del carrito de compras
                $faltacredito = 0; // si es 1, falta credito
                $estadoCupon = 0;

                // listado de productos del carrito
                $producto = DB::table('producto AS p')
                    ->join('carrito_extra AS c', 'c.producto_id', '=', 'p.id')
                    ->select('p.precio', 'c.cantidad')
                    ->where('c.carrito_temporal_id', $cart->id)
                    ->get();

                $pila = array();


                // multiplicar precio x cantidad
                foreach($producto as $p){

                    $cantidad = $p->cantidad;
                    $precio = $p->precio;
                    $multi = $cantidad * $precio;
                    array_push($pila, $multi);
                }

                // sumar listado de sub totales de cada producto multiplicado
                foreach ($pila as $valor){
                    $resultado = $resultado + $valor;
                }

                // precio de la zona servicio
                $infoZonaServicio = ZonasServicio::where('zonas_id', $zonaiduser)
                    ->where('servicios_id', $servicioidC)
                    ->first();

                // obtiene precio envio de la zona servicio
                // PRIORIDAD 1
                $envioPrecio = $infoZonaServicio->precio_envio;

                // PRIORIDAD 2
                // esta zona servicio tiene un minimo de $$ para aplicar nuevo tipo de cargo
                if($infoZonaServicio->min_envio_gratis == 1){
                    $costo = $infoZonaServicio->costo_envio_gratis;

                    // verificar resultado que es el sub total
                    if($resultado >= $costo){
                        //aplicar nuevo tipo cargo
                        $envioPrecio = $infoZonaServicio->nuevo_cargo;
                    }
                }

                // PRIORIDAD 3
                // envio gratis a esta zona servicio
                if($infoZonaServicio->zona_envio_gratis == 1){
                    $envioPrecio = 0;
                }


                $infoAdmin = InformacionAdmin::where('id', 1)->first();

                // PRIORIDAD 5
                // tu primera compra tiene envio gratis
                if($infoAdmin->primera_es_gratis == 1){
                    // verificar sino tenemos compras ya realizadas
                    if(Ordenes::where('clientes_id', $request->clienteid)->first()){
                        // no se puede ya
                    }else{
                        $envioPrecio = 0;
                    }
                }

                // sumar a total
                $total = $resultado + $envioPrecio;

                // ver si estara visible el boton cupones
                $infoAdmin = InformacionAdmin::where('id', 1)->first();

                if($infoAdmin->estado_cupon == 1){
                    $estadoCupon = 1;
                }

                // si utiliza monedero
                if($request->metodo == 2){
                    // lo que tiene el usuario - total (sub total + cargo de envio)
                    $credipuntosDescontado = $infoCliente->monedero - $total;

                    // los credi puntos son insuficientes
                    if($credipuntosDescontado < 0){
                        $faltacredito = 1;
                    }
                }

                $total = number_format((float)$total, 2, '.', '');
                $envioPrecio = number_format((float)$envioPrecio, 2, '.', '');
                $monedero = number_format((float)$infoCliente->monedero, 2, '.', '');

                return [
                    'success' => 1,
                    'total' => $total,
                    'subtotal' => number_format((float)$resultado, 2, '.', ''),
                    'envio' => $envioPrecio,
                    'direccion' => $direccionCliente,
                    'btncupon' => $estadoCupon,
                    'credipuntos' => $monedero, // lo que tiene el cliente
                    'faltacredito' => $faltacredito, // 0: no falta, 1: si falta
                ];

            }else{
                // no tiene carrito de compras
                return ['success' => 2];
            }
        }catch(\Error $e){
            return ['success' => 3, 'err' => $e];
        }
    }


    public function verificarCupon(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'clienteid' => 'required',
            'cupon' => 'required',
            'metodo' => 'required', // 1: efectivo, 2: monedero
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

            // verificar si usuario tiene carrito de compras
            if($cart = CarritoTemporal::where('clientes_id', $request->clienteid)->first()){

                // verificar que tipo de cupon es y si aun es valido
                if($cupon = Cupones::where('cupon', $request->cupon)->first()){

                    $tipocupon = $cupon->tipo_cupon_id;
                    $usolimite = $cupon->uso_limite;
                    $contador = $cupon->contador;
                    $activo = $cupon->activo;

                    $zonacarrito = $cart->zonas_id;
                    $serviciocarrito = $cart->servicios_id;

                    $infoServicio = Servicios::where('id', $serviciocarrito)->first();

                    // verificar si aun es valido este cupon
                    if($contador >= $usolimite || $activo == 0){
                        // cupon no valido
                        return ['success' => 1];
                    }

                    // obtener el total del carrito de compras
                    $producto = DB::table('producto AS p')
                        ->join('carrito_extra AS c', 'c.producto_id', '=', 'p.id')
                        ->select('p.id AS productoID', 'c.cantidad', 'p.precio')
                        ->where('c.carrito_temporal_id', $cart->id)
                        ->get();

                    $pilaSub = array(); // para obtener el sub total


                    // recorrer cada producto
                    foreach ($producto as $pro) {

                        // saver el precio multiplicado por la cantidad
                        $cantidad = $pro->cantidad;
                        $precio = $pro->precio;
                        $multi = $cantidad * $precio;
                        array_push($pilaSub, $multi);
                    }

                    $consumido = 0;
                    foreach ($pilaSub as $valor){
                        $consumido = $consumido + $valor;
                    }

                    // primero verificar que este disponible para la zona y servicio

                    if(CuponZonas::where('cupones_id', $cupon->id)->where('zonas_id', $zonacarrito)->first()){
                        // si disponible
                    }else{
                        // no disponible
                        return ['success' => 2];
                    }

                    if(CuponServicios::where('cupones_id', $cupon->id)
                        ->where('servicios_id', $serviciocarrito)
                        ->first()){
                        // si disponible
                    }else{
                        $nombre = $infoServicio->nombre;
                        // no disponible para servicios
                        if(strlen($infoServicio->nombre) > 80){
                            $nombre = substr($infoServicio->nombre,0,80) . "...";
                        }

                        return ['success' => 3, 'msj1' => $nombre];
                    }

                    //** ver si el cargo de envio */

                    $infoZonaServicio = ZonasServicio::where('servicios_id', $cart->servicios_id)
                        ->where('zonas_id', $cart->zonas_id)->first();

                    // PRIORIDAD 1: precio normal de envio
                    $envioPrecio = $infoZonaServicio->precio_envio;

                    // PRIORIDAD 2:
                    if($infoZonaServicio->min_envio_gratis == 1){
                        if($consumido >= $infoZonaServicio->costo_envio_gratis){
                            $envioPrecio = 0;
                        }
                    }

                    // PRIORIDAD 3: esta zona servicio tiene envio gratis
                    if($infoZonaServicio->zona_envio_gratis == 1){
                        $envioPrecio = 0;
                    }

                    $infoAdmin = InformacionAdmin::where('id', 1)->first();

                    // PRIORIDAD 5: producto que tengo en carrito lleva envio gratis
                    if($infoAdmin->primera_es_gratis == 1){
                        // verificar sino tenemos compras ya realizadas
                        if(Ordenes::where('clientes_id', $request->clienteid)->first()){
                            // no se puede ya
                        }else{
                            $envioPrecio = 0;
                        }
                    }

                    // ** -- **//
                    $infoCliente = Cliente::where('id', $request->clienteid)->first();

                    if($tipocupon == 1){ // tipo: envio gratis

                        // verificar si esta agregado aqui
                        if($info = CuponEnvio::where('cupones_id', $cupon->id)->first()){

                            if($consumido >= $info->dinero){
                                // aplica envio gratis.

                                // envio $0.00
                                // consumido $xx.xx
                                // total $xx.xx
                                // por el cupon valido
                                $envioPrecio = number_format((float)0, 2, '.', '');

                                if($request->metodo == 2) { // metodo monedero

                                    // si es monedero (consumido + $0 de envio)
                                    if($infoCliente->monedero < $consumido){

                                        // cupon envio gratis, aplica pero monedero no alcanza
                                        $consumido = number_format((float)$consumido, 2, '.', '');

                                        return ['success' => 4, 'msj1' => $consumido,
                                            'msj2' => $envioPrecio, 'msj3' => $infoCliente->monedero];

                                    }else{
                                        $consumido = number_format((float)$consumido, 2, '.', '');

                                        // se pagara con monedero, si alcanza para pagarle
                                        return ['success' => 5, 'msj1' => $consumido,
                                            'msj2' => $envioPrecio, 'msj3' => $infoCliente->monedero];
                                    }
                                }

                                // pagara con efectivo
                                $consumido = number_format((float)$consumido, 2, '.', '');
                                return ['success' => 6, 'msj1' => $consumido, 'msj2' => $envioPrecio];

                            }else{
                                // el consumible no es igual o mayor
                                return ['success' => 7, 'msj1' => $info->dinero];
                            }
                        }else{
                            // no esta agregado
                            return ['success' => 8];
                        }
                    }
                    else if($tipocupon == 2){

                        if($info = CuponProducto::where('cupones_id', $cupon->id)->first()){

                            if($consumido >= $info->dinero){
                                // aplica para producto gratis
                                return ['success' => 17, 'msj1' => $info->nombre];

                            }else{
                                // no aplica producto gratis
                                return ['success' => 18, 'msj1' => $info->dinero];
                            }

                        }else{
                            // no esta agregado
                            return ['success' => 19];
                        }
                    }
                    else{
                        // tipo cupon desconocido
                        return ['success' => 100];
                    }
                }else{
                    // cupon no encontrado
                    return ['success' => 100];
                }
            }else{
                // carrito de compras no encontrado
                return ['success' => 101];
            }
    }


    public function procesarOrdenEstado1(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'clienteid' => 'required',
            'aplicacupon' => 'required', // 0: no, 1: si
            'metodo' => 'required', // 1: efectivo  2: monedero
            'version' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        DB::beginTransaction();

        try {
            // verificar si tengo carrito
            if($cart = CarritoTemporal::where('clientes_id', $request->clienteid)->first()) {

                $infoCliente = Cliente::where('id', $request->clienteid)->first();

                // agarrar todos los productos del carrito
                $producto = DB::table('producto AS p')
                    ->join('carrito_extra AS c', 'c.producto_id', '=', 'p.id')
                    ->select('p.id AS productoID', 'c.cantidad', 'p.precio', 'p.activo',
                        'p.disponibilidad', 'c.id AS carritoid', 'c.nota_producto')
                    ->where('c.carrito_temporal_id', $cart->id)
                    ->get();

                if (count($producto) <= 0) {
                    // no hay productos en la cesta
                    return ['success' => 1];
                }

                $infoDireccionCliente = DireccionCliente::where('clientes_id', $request->clienteid)->where('seleccionado', 1)->first();

                // zona_id direccion seleccionada "no es igual" a la zona donde se agrego el carrito
                if ($infoDireccionCliente->zonas_id != $cart->zonas_id) {
                    // no coincide direccion zona cliente con la zona del carrito
                    return ['success' => 2];
                }

                $infoZonaServicio = ZonasServicio::where('zonas_id', $cart->zonas_id)
                    ->where('servicios_id', $cart->servicios_id)
                    ->first();

                $infoServicio = Servicios::where('id', $cart->servicios_id)->first();

                if ($infoZonaServicio->activo == 0) {
                    // zona servicio no esta activo
                    return ['success' => 3];
                }

                $pilaSub = array(); // para saver si subtotal supera el minimo consumible

                $tengoProductoGratis = 0;

                // recorrer cada producto
                foreach ($producto as $pro) {

                    // un producto no esta disponible o activo
                    if ($pro->activo == 0 || $pro->disponibilidad == 0) {
                        // producto no activo o no disponible
                        return ['success' => 4];
                    }


                    // saver el minimo consumible
                    $cantidad = $pro->cantidad;
                    $precio = $pro->precio;
                    $multi = $cantidad * $precio;
                    array_push($pilaSub, $multi);
                }

                $consumido = 0;
                foreach ($pilaSub as $valor) {
                    $consumido = $consumido + $valor;
                }

                // variable que toma el dato de lo consumido y sera modifica
                // segun se use un cupon
                $consumidoCupon = $consumido;

                //** validacion de horarios */

                $numSemana = [
                    0 => 1, // domingo
                    1 => 2, // lunes
                    2 => 3, // martes
                    3 => 4, // miercoles
                    4 => 5, // jueves
                    5 => 6, // viernes
                    6 => 7, // sabado
                ];

                // hora y fecha
                $getValores = Carbon::now('America/El_Salvador');
                $getDiaHora = $getValores->dayOfWeek;
                $diaSemana = $numSemana[$getDiaHora];
                $hora = $getValores->format('H:i:s');

                // verificar si usara la segunda hora
                $dato = DB::table('horario_servicio AS h')
                    ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                    ->where('h.segunda_hora', 1) // segunda hora habilitada
                    ->where('h.servicios_id', $infoServicio->id) // id servicio
                    ->where('h.dia', $diaSemana) // dia
                    ->get();

                // si verificar con la segunda hora
                if (count($dato) >= 1) {

                    $horario = DB::table('horario_servicio AS h')
                        ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                        ->where('h.segunda_hora', '1') // segunda hora habilitada
                        ->where('h.servicios_id', $infoServicio->id) // id servicio
                        ->where('h.dia', $diaSemana) // dia
                        ->where(function ($query) use ($hora) {
                            $query->where('h.hora1', '<=', $hora)
                                ->where('h.hora2', '>=', $hora)
                                ->orWhere('h.hora3', '<=', $hora)
                                ->where('h.hora4', '>=', $hora);
                        })
                        ->get();

                    if (count($horario) >= 1) {
                        // abierto
                    } else {
                        // cerrado horario normal del servicio (4 horarios)
                        return ['success' => 5];
                    }

                } else {
                    // verificar sin la segunda hora
                    $horario = DB::table('horario_servicio AS h')
                        ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                        ->where('h.segunda_hora', 0) // segunda hora habilitada
                        ->where('h.servicios_id', $infoServicio->id) // id servicio
                        ->where('h.dia', $diaSemana)
                        ->where('h.hora1', '<=', $hora)
                        ->where('h.hora2', '>=', $hora)
                        ->get();

                    if (count($horario) >= 1) {
                        // abierto
                    } else {
                        // cerrado horario normal del servicio (2 horarios)
                        return ['success' => 5];
                    }
                }

                // preguntar si este dia esta cerrado
                $cerradoHoy = HorarioServicio::where('servicios_id', $infoServicio->id)->where('dia', $diaSemana)->first();

                if ($cerradoHoy->cerrado == 1) {
                    // cerrado este dia el negocio
                    return ['success' => 6];
                }

                $infoZona = Zona::where('id', $cart->zonas_id)->first();

                if ($infoZona->saturacion == 1) {
                    // saturacion de zona completa
                    return ['success' => 7, 'msj1' => $infoZona->mensaje_bloqueo];
                }

                // cerrado emergencia del servicio
                if ($infoServicio->cerrado_emergencia == 1) {
                    return ['success' => 8, 'msj1' => $infoServicio->mensaje_cerrado];
                }

                // servicio no activo actualmente
                if ($infoServicio->activo == 0) {
                    return ['success' => 9];
                }

                // servicio saturacion para esta zona servicio agregado
                if ($infoZonaServicio->saturacion) {
                    return ['success' => 10, 'msj1' => $infoZonaServicio->mensaje_bloqueo];
                }

                // horario delivery para esa zona
                $horarioDeliveryZona = Zona::where('id', $cart->zonas_id)
                    ->where('hora_abierto_delivery', '<=', $hora)
                    ->where('hora_cerrado_delivery', '>=', $hora)
                    ->get();

                if (count($horarioDeliveryZona) >= 1) {
                    // abierto
                } else {
                    // horario domicilio de la zona
                    $horazona1 = date("h:i A", strtotime($infoZona->hora_abierto_delivery));
                    $horazona2 = date("h:i A", strtotime($infoZona->hora_cerrado_delivery));

                    $unido = "El horario para su dirección de envío es: " . $horazona1 . " A " . $horazona2;

                    // cerrado por horario de zona
                    return ['success' => 11, 'msj1' => $unido];
                }

                // verificar cargo de envio

                $tipoCargoEnvio = 1;

                // PRIORIDAD 1: precio normal de envio
                $envioPrecio = $infoZonaServicio->precio_envio;
                $copiaenvio = $infoZonaServicio->precio_envio;
                $gananciamotorista = $infoZonaServicio->ganancia_motorista;


                // PRIORIDAD 2:
                if ($infoZonaServicio->min_envio_gratis == 1) {
                    if ($consumido >= $infoZonaServicio->costo_envio_gratis) {
                        $envioPrecio = 0;
                        $tipoCargoEnvio = 2;
                    }
                }

                // PRIORIDAD 3: esta zona servicio tiene envio gratis
                if ($infoZonaServicio->zona_envio_gratis == 1) {
                    $envioPrecio = 0;
                    $tipoCargoEnvio = 3;
                }

                // PRIORIDAD 4: un producto que tengo tiene envio gratis
                if ($tengoProductoGratis == 1) {
                    $envioPrecio = 0;
                    $tipoCargoEnvio = 4;
                }

                $infoAdmin = InformacionAdmin::where('id', 1)->first();

                // PRIORIDAD 5: producto que tengo en carrito lleva envio gratis
                if ($infoAdmin->primera_es_gratis == 1) {
                    // verificar sino tenemos compras ya realizadas
                    if (Ordenes::where('clientes_id', $infoCliente->id)->first()) {
                        // ya tiene al menos una orden
                    } else {
                        // primera vez que pide
                        $tipoCargoEnvio = 5;
                    }
                }


                // datos para guardar si utiliza cupon
                $numeroCupon = 0; // 1- envio, 2- descuento, 3- porcentaje, 4- producto
                $idcupon = 0;
                $nombreCupon = "";
                $nombreProductoCupon = "";
                $dineroCupon = 0;

                //****** CUPONES *********/

                // Verificar validez del cupon
                if ($request->aplicacupon == 1) {
                    // verificar que exista

                    if ($cupon = Cupones::where('cupon', $request->cupon)->first()) {

                        // verificar validacion si es valido a un
                        $idcupon = $cupon->id;
                        $tipocupon = $cupon->tipo_cupon_id;
                        $numeroCupon = $cupon->tipo_cupon_id; // para guardar en la base
                        $nombreCupon = $cupon->cupon;

                        $usolimite = $cupon->uso_limite;
                        $contador = $cupon->contador;
                        $activo = $cupon->activo;

                        // verificar si aun es valido este cupon
                        if ($contador >= $usolimite || $activo == 0) {
                            // cupon ya no es valido
                            return ['success' => 12];
                        }

                        // primero verificar que este disponible para la zona y servicio

                        if (CuponZonas::where('cupones_id', $cupon->id)->where('zonas_id', $cart->zonas_id)->first()) {
                            // si disponible
                        } else {
                            // no disponible
                            return ['success' => 12];
                        }

                        if (CuponServicios::where('cupones_id', $cupon->id)
                            ->where('servicios_id', $cart->servicios_id)
                            ->first()) {
                            // si disponible
                        } else {

                            return ['success' => 12];
                        }

                        if ($tipocupon == 1) { // tipo: envio gratis

                            if ($info = CuponEnvio::where('cupones_id', $cupon->id)->first()) {

                                $envioPrecio = 0; // por cupon habilitado
                                $dineroCupon = $info->dinero; // registro de cual era minimo para envio gratis

                                if ($consumido >= $info->dinero) {

                                    if ($request->metodo == 2) { // metodo monedero

                                        // si es monedero (consumido + $0 de envio)
                                        if ($infoCliente->monedero < $consumido) {

                                            // cupon envio gratis, aplica pero monedero no alcanza *
                                            return ['success' => 13];
                                        }
                                    }

                                } else {
                                    // el consumible no es igual o mayor *
                                    return ['success' => 13];
                                }
                            } else {
                                // no esta agregado a la tabla de envios el cupon
                                return ['success' => 13];
                            }

                        }
                        else if ($tipocupon == 2) {
                            // producto gratis

                            if ($info = CuponProducto::where('cupones_id', $cupon->id)->first()) {

                                $nombreProductoCupon = $info->nombre;

                                if ($consumido < $info->dinero) {
                                    // no aplica producto gratis
                                    return ['success' => 16];
                                }

                            } else {
                                // no esta agregado
                                return ['success' => 16];
                            }

                        } else {
                            // tipo cupon desconocido
                            return ['success' => 17];
                        }
                    } else {
                        // cupon no encontrado
                        return ['success' => 17];
                    }

                } // -- fin - aplicaccupon


                $descontado = 0; // variable aqui, porque abajo solicito credito que va a quedar

                // comprobar si alcanzara el monedero
                if($request->metodo == 2){

                    // si consumidoCupon no utilizo cupon, siempre valdra lo mismo que consumido
                    $sumatoria = $consumidoCupon + $envioPrecio; // aqui ya viene aplicado cualquier cupon

                    if($infoCliente->monedero >= $sumatoria){

                        // nunca podra quedarme menor a 0
                        $descontado = $infoCliente->monedero - $sumatoria;

                        // actualizar monedero al cliente
                        Cliente::where('id', $request->clienteid)->update(['monedero' => $descontado]);
                    }else{
                        // no puedo comprar, monedero insuficiente
                        return ['success' => 18];
                    }
                }


                // comprobaciones finalizadas, proceder a guardar

                $fechahoy = Carbon::now('America/El_Salvador');

                // orden crear normalmente, saver el tiempo automatico o no, depende del estado_2
                // crear la orden
                $idOrden = DB::table('ordenes')->insertGetId(
                    [ 'clientes_id' => $request->clienteid,
                        'servicios_id' => $infoServicio->id,
                        'nota' => $request->nota,
                        'cambio' => $request->cambio,
                        'fecha_orden' => $fechahoy,
                        'precio_consumido' => $consumido,
                        'precio_envio' => $envioPrecio,

                        'estado_2' => 0,
                        'fecha_2' => null,
                        'hora_2' => 0,

                        'estado_3' => 0,
                        'fecha_3' => null,

                        'estado_4' => 0,
                        'fecha_4' => null,

                        'estado_5' => 0,
                        'fecha_5' => null,

                        'estado_6' => 0,
                        'fecha_6' => null,

                        'estado_7' => 0,
                        'fecha_7' => null,

                        'estado_8' => 0,
                        'fecha_8' => null,
                        'mensaje_8' => null,

                        'visible' => 1,
                        'visible_p' => 1,
                        'visible_p2' => 0,
                        'visible_p3' => 0,
                        'visible_m' => 0,
                        'cancelado' => 0, // 0: nadie, 1: cliente, 2 propietarios

                        'ganancia_motorista' => $gananciamotorista ,
                        'tipo_cargo' => $tipoCargoEnvio, // tipo de cargo envio que se aplico
                    ]
                );

                if($request->aplicacupon == 1){

                    // guardar registro, con esto veremos el resultado de cada accion de cupon,
                    // en las diferentes pantallas que se requiera
                    $reg = new OrdenesCupones();
                    $reg->ordenes_id = $idOrden;
                    $reg->cupones_id = $idcupon;
                    $reg->tipocupon_id =  $numeroCupon;
                    $reg->nombre_cupon = $nombreCupon;
                    $reg->nombre_producto = $nombreProductoCupon;
                    $reg->dinero = $dineroCupon;
                    $reg->save();

                    // actualizar contador
                    $infoCupon = Cupones::where('id', $idcupon)->first();

                    $sumarContador = $infoCupon->contador + 1;

                    // sumas +1 el contador
                    Cupones::where('id', $idcupon)->update(['contador' => $sumarContador]);
                }

                // guadar todos los productos de esa orden
                foreach($producto as $p){

                    $data = array('ordenes_id' => $idOrden,
                        'producto_id' => $p->productoID,
                        'cantidad' => $p->cantidad,
                        'precio' => $p->precio,
                        'nombre' => $p->nombre,
                        'nota' => $p->nota_producto);
                    OrdenesDescripcion::insert($data);
                }

                $nuevaDir = new OrdenesDirecciones();
                $nuevaDir->clientes_id = $infoCliente->id;
                $nuevaDir->ordenes_id = $idOrden;
                $nuevaDir->zonas_id = $infoZona->id;
                $nuevaDir->nombre = $infoDireccionCliente->nombre;
                $nuevaDir->direccion = $infoDireccionCliente->direccion;
                $nuevaDir->numero_casa = $infoDireccionCliente->numero_casa;
                $nuevaDir->punto_referencia = $infoDireccionCliente->punto_referencia;
                $nuevaDir->latitud = $infoDireccionCliente->latitud;
                $nuevaDir->longitud = $infoDireccionCliente->longitud;
                $nuevaDir->latitudreal = $infoDireccionCliente->latitudreal;
                $nuevaDir->longitudreal = $infoDireccionCliente->longitudreal;
                $nuevaDir->copia_envio = $copiaenvio; // el envio original de la zona servicio
                $nuevaDir->copia_tiempo_orden = $infoZona->tiempo_extra; // tiempo extra que se le da a una zona
                $nuevaDir->version = $request->version;
                $nuevaDir->revisado = $infoDireccionCliente->revisado;
                $nuevaDir->metodo_pago = $request->metodo;
                $nuevaDir->copia_comision = $infoServicio->comision;
                $nuevaDir->privado = $infoServicio->privado;
                $nuevaDir->save();

                // BORRAR CARRITO TEMPORAL DEL USUARIO

                if($infoAdmin->borrar_carrito == 1){
                    CarritoExtra::where('carrito_temporal_id', $cart->id)->delete();
                    CarritoTemporal::where('clientes_id', $request->clienteid)->delete();
                }

                DB::commit();
                // monedero descontado de la compra, se mostrara si se efectua la compra con monedero
                $mone = Cliente::where('id', $request->clienteid)->first();
                $datoMonedero = number_format((float)$mone->monedero, 2, '.', '');


                // NOTIFICACION

                $propietarios = DB::table('propietarios')
                    ->where('servicios_id', $cart->servicios_id)
                    ->where('disponibilidad', 1)
                    ->where('activo', 1)
                    ->get();

                $pilaPropietarios = array();

                foreach($propietarios as $m){
                    if($m->token_fcm != null){
                        array_push($pilaPropietarios, $m->token_fcm);
                    }
                }

                // NOTIFICACIONES A PROPIETARIOS, DISPONIBLES
                if(!empty($pilaPropietarios)) {
                    $tituloNoti = "Nueva Orden #" . $idOrden;
                    $mensajeNoti = "Ver orden nueva!";

                    if (!empty($pilaPropietarios)) {
                        try {
                            $this->envioNoticacionAfiliado($tituloNoti, $mensajeNoti, $pilaPropietarios);
                        } catch (Exception $e) {
                        }
                    }
                }

                return ['success' => 19, 'msj1' => $datoMonedero];

            }else{
                return [
                    'success' => 100 // carrito de compras no encontrado
                ];
            }

        } catch(\Throwable $e){
            DB::rollback();
            return [
                'success' => 101,
                'message' => "e".  $e
            ];
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
