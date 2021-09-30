<?php

namespace App\Http\Controllers\Backend\api\Servicios;

use App\Http\Controllers\Controller;
use App\Models\CarritoExtra;
use App\Models\CarritoTemporal;
use App\Models\Cliente;
use App\Models\DireccionCliente;
use App\Models\HorarioServicio;
use App\Models\Producto;
use App\Models\Servicios;
use App\Models\TiposServicio;
use App\Models\Zona;
use App\Models\ZonasServicio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class ApiServiciosController extends Controller
{

    public function listaServiciosPorTipo(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required',
            'tipo' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Cliente::where('id', $request->clienteid)->first()){

            // zona del cliente
            $di = DireccionCliente::where('clientes_id', $request->clienteid)->first();

            // dia
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

            // servicios para la zona
            $servicios = DB::table('zonas_servicio AS z')
                ->join('servicios AS s', 's.id', '=', 'z.servicios_id')
                ->select('s.id AS idServicio', 's.nombre AS nombreServicio',
                    's.descripcion', 's.imagen', 'z.id AS zonaservicioid',
                    's.logo', 's.tipo_vista', 's.cerrado_emergencia', 's.privado', 's.mensaje_cerrado', 'z.posicion')
                ->where('z.zonas_id', $di->zonas_id)
                ->where('s.tipos_servicio_id', $request->tipo) // tipo restaurante por ejemplo
                ->where('z.activo', 1) // activo de zona servicios para esta zona
                ->where('s.activo', 1) // activo el servicio globalmente
                ->orderBy('z.posicion', 'ASC')
                ->get();

            $horaDelivery = Zona::where('id', $di->zonas_id)
                ->where('hora_abierto_delivery', '<=', $hora)
                ->where('hora_cerrado_delivery', '>=', $hora)
                ->get();

            foreach ($servicios as $user) {

                $infoZonaServicio = ZonasServicio::where('zonas_id', $di->zonas_id)
                    ->where('servicios_id', $user->idServicio)
                    ->first();

                $saturacionZonaServicio = 0;
                $msjBloqueoZonaServicio = "";
                if($infoZonaServicio->saturacion == 1){
                    $saturacionZonaServicio = 1;
                    $msjBloqueoZonaServicio = $infoZonaServicio->mensaje_bloqueo;
                }

                $user->saturacionZonaServicio = $saturacionZonaServicio;
                $user->msjBloqueoZonaServicio = $msjBloqueoZonaServicio;

                $horaZona = 0;

                // horario de la zona
                if($user->privado == 0){

                    if(count($horaDelivery) >= 1){
                        //$horaZona = 0; // abierto
                    }else{
                        $horaZona = 1; // cerrado
                    }
                }

                $user->horazona = $horaZona;


                // verificar si usara la segunda hora
                $dato = DB::table('horario_servicio AS h')
                    ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                    ->where('h.segunda_hora', 1) // segunda hora habilitada
                    ->where('h.servicios_id', $user->idServicio) // id servicio   1
                    ->where('h.dia', $diaSemana) // dia   2
                    ->get();

                // si verificar con la segunda hora
                if(count($dato) >= 1){

                    $horario = DB::table('horario_servicio AS h')
                        ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                        ->where('h.segunda_hora', 1) // segunda hora habilitada
                        ->where('h.servicios_id', $user->idServicio) // id servicio
                        ->where('h.dia', $diaSemana) // dia
                        ->where(function ($query) use ($hora) {
                            $query->where('h.hora1', '<=' , $hora)
                                ->where('h.hora2', '>=' , $hora)
                                ->orWhere('h.hora3', '<=', $hora)
                                ->where('h.hora4', '>=' , $hora);
                        })
                        ->get();

                    if(count($horario) >= 1){ // abierto
                        $user->horarioLocal = 0;
                    }else{
                        $user->horarioLocal = 1; //cerrado
                    }

                }else{
                    // verificar sin la segunda hora
                    $horario = DB::table('horario_servicio AS h')
                        ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                        ->where('h.segunda_hora', 0) // segunda hora habilitada
                        ->where('h.servicios_id', $user->idServicio) // id servicio
                        ->where('h.dia', $diaSemana) // dia
                        ->where(function ($query) use ($hora) {
                            $query->where('h.hora1', '<=' , $hora)
                                ->where('h.hora2', '>=' , $hora);
                        })
                        ->get();

                    if(count($horario) >= 1){
                        $user->horarioLocal = 0;
                    }else{
                        $user->horarioLocal = 1; //cerrado
                    }
                }

                // preguntar si este dia esta cerrado
                $cerradoHoy = HorarioServicio::where('servicios_id', $user->idServicio)->where('dia', $diaSemana)->first();

                if($cerradoHoy->cerrado == 1){
                    $user->cerrado = 1;
                }else{
                    $user->cerrado = 0;
                }
            }

            // problema para enviar a esta zona, ejemplo motoristas sin disponibilidad
            $zonaSa = Zona::where('id', $di->zonas_id)->first();
            $zonaSaturacion = $zonaSa->saturacion;
            $zonaMensaje = $zonaSa->mensaje_bloqueo;

            $horazona1 = date("h:i A", strtotime($zonaSa->hora_abierto_delivery));
            $horazona2 = date("h:i A", strtotime($zonaSa->hora_cerrado_delivery));

            $tengoCarrito = 0; // para saver si tengo carrito de compras
            $resultado = 0;
            // verificar si tengo algun carrito de compras con productos
            if($car = CarritoTemporal::where('clientes_id', $request->clienteid)->first()){
                // ver si tiene al menos 1 producto agregado
                if(CarritoExtra::where('carrito_temporal_id', $car->id)->first()){
                    $tengoCarrito = 1;
                    $producto = DB::table('carrito_extra AS c')
                        ->join('producto AS p', 'p.id', '=', 'c.producto_id')
                        ->where('c.carrito_temporal_id', $car->id)
                        ->select('p.precio', 'c.cantidad')
                        ->get();

                    $pila = array();

                    foreach($producto as $p){
                        $cantidad = $p->cantidad;
                        $precio = $p->precio;
                        $multi = $cantidad * $precio;
                        array_push($pila, $multi);
                    }

                    foreach ($pila as $valor){
                        $resultado=$resultado+$valor; //sumar que sera el sub total
                    }
                }
            }

            return [
                'success' => 1,
                'zonasaturacion' => $zonaSaturacion,
                'msj1' => $zonaMensaje, // mensaje por saturacion
               // 'horadelivery' => $horaEntrega, // 0: abierto zona 1: cerrado zona
                'hayorden' => $tengoCarrito, // saver si tenemos carrito
                'total' => number_format((float)$resultado, 2, '.', ''), //subtotal
                'horazona1' => $horazona1, // hora abre la zona
                'horazona2' => $horazona2, // hora cierra la zona
                'servicios' => $servicios // lista de servicios
            ];

        }else{
            return ['success' => 2];
        }
    }

    public function listadoMenuVertical(Request $request){

        $reglaDatos = array(
            'servicioid' => 'required',
            'idcliente' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($infoServicio = Servicios::where('id', $request->servicioid)->first()){

            if($infoDireccion = DireccionCliente::where('clientes_id', $request->idcliente)
                ->where('seleccionado', 1)
                ->first()) {

                $cerrado = "";

                // informacion de la zona servicio
                $infoZonaServicio = ZonasServicio::where('zonas_id', $infoDireccion->zonas_id)
                    ->where('servicios_id', $request->servicioid)
                    ->first();

                $datos = "Envío $" . $infoZonaServicio->precio_envio;

                if ($infoZonaServicio->min_envio_gratis == 1) {
                    $datos = "Envío $" . $infoZonaServicio->precio_envio . " - Por mínimo de consumo de $"
                        . $infoZonaServicio->costo_envio_gratis . " El envío es Gratis";
                }

                if ($infoZonaServicio->zona_envio_gratis == 1) {
                    $datos = "Envío Gratis";
                }

                $tipo = DB::table('servicios_tipo AS st')
                    ->join('servicios AS s', 's.id', '=', 'st.servicios_id')
                    ->select('st.id AS tipoId', 'st.nombre AS nombreSeccion')
                    ->where('st.servicios_id', $request->servicioid)
                    ->where('st.activo', 1) // categoria activa
                    ->where('st.visible', 1) // visible al propietario
                    ->orderBy('st.posicion', 'ASC')
                    ->get();

                $resultsBloque = array();
                $index = 0;

                foreach($tipo as $secciones){
                    array_push($resultsBloque,$secciones);

                    $subSecciones = Producto::where('servicios_tipo_id', $secciones->tipoId)
                        ->where('activo', 1) // para inactivarlo solo para administrador
                        ->where('disponibilidad', 1) // para inactivarlo pero el propietario
                        ->orderBy('posicion', 'ASC')
                        ->get();

                    $resultsBloque[$index]->productos = $subSecciones; //agregar los productos en la sub seccion
                    $index++;
                }

                // informacion del local
                $servicio = Servicios::where('id', $request->servicioid)->get();

                // Validacion de cerrados

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

                // horario de la zona
                $horaDelivery = Zona::where('id', $infoDireccion->zonas_id)
                    ->where('hora_abierto_delivery', '<=', $hora)
                    ->where('hora_cerrado_delivery', '>=', $hora)
                    ->get();

                $infoZona = Zona::where('id', $infoDireccion->zonas_id)->first();

                if($infoServicio->privado == 0){

                    if(count($horaDelivery) >= 1){
                        // abierto
                    }else{
                        $horazona1 = date("h:i A", strtotime($infoZona->hora_abierto_delivery));
                        $horazona2 = date("h:i A", strtotime($infoZona->hora_cerrado_delivery));

                        $cerrado = "Cerrado. Horario domicilio es (" . $horazona1 . ")" . " - " . "(" . $horazona2 . ")";
                    }
                }

                // saruracion de la zona servicio
                if($infoZonaServicio->saturacion == 1){
                    $cerrado = $infoZonaServicio->mensaje_bloqueo;
                }

                // verificar horario del negocio
                // verificar si usara la segunda hora
                $dato = DB::table('horario_servicio AS h')
                    ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                    ->where('h.segunda_hora', 1) // segunda hora habilitada
                    ->where('h.servicios_id', $request->servicioid) // id servicio   1
                    ->where('h.dia', $diaSemana) // dia   2
                    ->get();

                // si verificar con la segunda hora
                if(count($dato) >= 1){

                    $horario = DB::table('horario_servicio AS h')
                        ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                        ->where('h.segunda_hora', 1) // segunda hora habilitada
                        ->where('h.servicios_id', $request->servicioid) // id servicio
                        ->where('h.dia', $diaSemana) // dia
                        ->where(function ($query) use ($hora) {
                            $query->where('h.hora1', '<=' , $hora)
                                ->where('h.hora2', '>=' , $hora)
                                ->orWhere('h.hora3', '<=', $hora)
                                ->where('h.hora4', '>=' , $hora);
                        })
                        ->get();

                    if(count($horario) >= 1){ // abierto

                    }else{
                        $cerrado = "Cerrado Temporalmente";
                    }

                }else{
                    // verificar sin la segunda hora
                    $horario = DB::table('horario_servicio AS h')
                        ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                        ->where('h.segunda_hora', 0) // segunda hora habilitada
                        ->where('h.servicios_id', $request->servicioid) // id servicio
                        ->where('h.dia', $diaSemana) // dia
                        ->where(function ($query) use ($hora) {
                            $query->where('h.hora1', '<=' , $hora)
                                ->where('h.hora2', '>=' , $hora);
                        })
                        ->get();

                    if(count($horario) >= 1){

                    }else{
                        $cerrado = "Cerrado Temporalmente";
                    }
                }

                // preguntar si este dia esta cerrado
                $cerradoHoy = HorarioServicio::where('servicios_id', $request->servicioid)->where('dia', $diaSemana)->first();

                if($cerradoHoy->cerrado == 1){
                    $cerrado = "Cerrado Temporalmente";
                }

                // problema para enviar a esta zona, ejemplo motoristas sin disponibilidad

                if($infoZona->saturacion == 1){
                    $cerrado = $infoZona->mensaje_bloqueo;
                }

                if($infoServicio->cerrado_emergencia == 1){
                    $cerrado = $infoServicio->mensaje_cerrado;
                }

                return [
                    'success' => 1,
                    'msj1' => $datos,
                    'cerrado' => $cerrado,
                    'servicio' => $servicio,
                    'productos' => $tipo,
                ];
            }
            else{
                return ['success' => 2];
            }
        }else{
            return ['success' => 2];
        }
    }

    public function listadoMenuHorizontal(Request $request){

        $reglaDatos = array(
            'id' => 'required',
            'servicioid' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($infoServicio = Servicios::where('id', $request->servicioid)->first()){

            if(Cliente::where('id', $request->id)->first()){

                $infoDireccion = DireccionCliente::where('clientes_id', $request->id)
                    ->where('seleccionado', 1)->first();
                $cerrado = "";

                // obtener secciones
                $tipo = DB::table('servicios_tipo AS st')
                    ->join('servicios AS s', 's.id', '=', 'st.servicios_id')
                    ->select('st.id AS tipoId', 'st.nombre AS nombreSeccion')
                    ->where('st.servicios_id', $request->servicioid)
                    ->where('st.activo', 1)
                    ->where('st.visible', 1) // categoria visible al afiliado
                    ->orderBy('st.posicion', 'ASC')
                    ->get();

                // obtener total de productos por seccion
                foreach ($tipo as $user){

                    // contar cada seccion
                    $producto = DB::table('servicios_tipo AS st')
                        ->join('producto AS p', 'p.servicios_tipo_id', '=', 'st.id')
                        ->select('st.id')
                        ->where('p.activo', 1)
                        ->where('p.disponibilidad', 1)
                        ->where('st.id', $user->tipoId)
                        ->get();

                    $contador = count($producto);
                    $user->total = $contador;
                }

                $resultsBloque = array();
                $index = 0;

                foreach($tipo  as $secciones){
                    array_push($resultsBloque,$secciones);

                    $subSecciones = Producto::where('servicios_tipo_id', $secciones->tipoId)
                        ->where('activo', 1)
                        ->where('disponibilidad', 1)
                        ->take(5) //maximo 5 productos por seccion
                        ->orderBy('posicion', 'ASC') // ordenados
                        ->get();

                    $resultsBloque[$index]->productos = $subSecciones;
                    $index++;
                }

                $servicio = Servicios::where('id', $request->servicioid)->get();

                $infoZonaServicio = ZonasServicio::where('zonas_id', $infoDireccion->zonas_id)
                    ->where('servicios_id', $request->servicioid)
                    ->first();

                $datos = "Envío $" . $infoZonaServicio->precio_envio;

                if ($infoZonaServicio->min_envio_gratis == 1) {
                    $datos = "Envío $" . $infoZonaServicio->precio_envio . " - Por mínimo de consumo de $"
                        . $infoZonaServicio->costo_envio_gratis . " El envío es Gratis";
                }

                if ($infoZonaServicio->zona_envio_gratis == 1) {
                    $datos = "Envío Gratis";
                }


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

                // horario de la zona
                $horaDelivery = Zona::where('id', $infoDireccion->zonas_id)
                    ->where('hora_abierto_delivery', '<=', $hora)
                    ->where('hora_cerrado_delivery', '>=', $hora)
                    ->get();

                $infoZona = Zona::where('id', $infoDireccion->zonas_id)->first();

                if($infoServicio->privado == 0){

                    if(count($horaDelivery) >= 1){
                        // abierto
                    }else{
                        $horazona1 = date("h:i A", strtotime($infoZona->hora_abierto_delivery));
                        $horazona2 = date("h:i A", strtotime($infoZona->hora_cerrado_delivery));

                        $cerrado = "Cerrado. Horario domicilio es (" . $horazona1 . ")" . " - " . "(" . $horazona2 . ")";
                    }
                }

                // saruracion de la zona servicio
                if($infoZonaServicio->saturacion == 1){
                    $cerrado = $infoZonaServicio->mensaje_bloqueo;
                }

                // verificar horario del negocio
                // verificar si usara la segunda hora
                $dato = DB::table('horario_servicio AS h')
                    ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                    ->where('h.segunda_hora', 1) // segunda hora habilitada
                    ->where('h.servicios_id', $request->servicioid) // id servicio   1
                    ->where('h.dia', $diaSemana) // dia   2
                    ->get();

                // si verificar con la segunda hora
                if(count($dato) >= 1){

                    $horario = DB::table('horario_servicio AS h')
                        ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                        ->where('h.segunda_hora', 1) // segunda hora habilitada
                        ->where('h.servicios_id', $request->servicioid) // id servicio
                        ->where('h.dia', $diaSemana) // dia
                        ->where(function ($query) use ($hora) {
                            $query->where('h.hora1', '<=' , $hora)
                                ->where('h.hora2', '>=' , $hora)
                                ->orWhere('h.hora3', '<=', $hora)
                                ->where('h.hora4', '>=' , $hora);
                        })
                        ->get();

                    if(count($horario) >= 1){ // abierto

                    }else{
                        $cerrado = "Cerrado Temporalmente";
                    }

                }else{
                    // verificar sin la segunda hora
                    $horario = DB::table('horario_servicio AS h')
                        ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                        ->where('h.segunda_hora', 0) // segunda hora habilitada
                        ->where('h.servicios_id', $request->servicioid) // id servicio
                        ->where('h.dia', $diaSemana) // dia
                        ->where(function ($query) use ($hora) {
                            $query->where('h.hora1', '<=' , $hora)
                                ->where('h.hora2', '>=' , $hora);
                        })
                        ->get();

                    if(count($horario) >= 1){

                    }else{
                        $cerrado = "Cerrado Temporalmente";
                    }
                }

                // preguntar si este dia esta cerrado
                $cerradoHoy = HorarioServicio::where('servicios_id', $request->servicioid)->where('dia', $diaSemana)->first();

                if($cerradoHoy->cerrado == 1){
                    $cerrado = "Cerrado Temporalmente";
                }

                // problema para enviar a esta zona, ejemplo motoristas sin disponibilidad

                if($infoZona->saturacion == 1){
                    $cerrado = $infoZona->mensaje_bloqueo;
                }

                if($infoServicio->cerrado_emergencia == 1){
                    $cerrado = $infoServicio->mensaje_cerrado;
                }

                return [
                    'success' => 1,
                    'msj1' => $datos,
                    'cerrado' => $cerrado,
                    'servicio' => $servicio,
                    'productos' => $tipo
                ];

            }else{
                return ['success' => 2];
            }

        }else{
            return ['success'=> 2];
        }
    }

    public function informacionServicio(Request $request){

        $reglaDatos = array(
            'idcliente' => 'required',
            'idservicio' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if($info = Servicios::where('id', $request->idservicio)->first()){

            if($infoDireccion = DireccionCliente::where('clientes_id', $request->idcliente)
                ->where('seleccionado', 1)
                ->first()){

                // informacion de la zona servicio
                $infoZonaSercicio = ZonasServicio::where('zonas_id', $infoDireccion->zonas_id)
                    ->where('servicios_id', $info->id)
                    ->first();

                $datos = "Envío $" . $infoZonaSercicio->precio_envio;

                if($infoZonaSercicio->min_envio_gratis == 1){
                    $datos = "Envío $" . $infoZonaSercicio->precio_envio . " - Por mínimo de consumo de $"
                        . $infoZonaSercicio->costo_envio_gratis . " El envío es Gratis";
                }

                if($infoZonaSercicio->zona_envio_gratis == 1){
                    $datos = "Envío Gratis";
                }

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

                $horario = HorarioServicio::where('servicios_id', $request->idservicio)->get();

                foreach ($horario as $h){

                    $h->hora1 = date("h:i a", strtotime($h->hora1));
                    $h->hora2 = date("h:i a", strtotime($h->hora2));
                    $h->hora3 = date("h:i a", strtotime($h->hora3));
                    $h->hora4 = date("h:i a", strtotime($h->hora4));

                    if($h->dia == $diaSemana){
                        $h->hoy = 1;
                    }else{
                        $h->hoy = 0;
                    }
                }

                return ['success' => 1,
                    'latitud' => $info->latitud,
                    'longitud' => $info->longitud,
                    'direccion' => $info->direccion,
                    'msj1' => $datos,
                    'horario' => $horario];

            }else{
                return ['success' => 2];
            }
        }else{
            return ['success' => 2];
        }
    }


}
