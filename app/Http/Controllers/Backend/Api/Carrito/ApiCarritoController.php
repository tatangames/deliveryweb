<?php

namespace App\Http\Controllers\Backend\api\Carrito;

use App\Http\Controllers\Controller;
use App\Models\CarritoExtra;
use App\Models\CarritoTemporal;
use App\Models\Cliente;
use App\Models\DireccionCliente;
use App\Models\HorarioServicio;
use App\Models\Producto;
use App\Models\Servicios;
use App\Models\Zona;
use App\Models\ZonasServicio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiCarritoController extends Controller
{

    public function agregarProductoCarritoTemporal(Request $request){

        $reglaDatos = array(
            'productoid' => 'required',
            'clienteid' => 'required',
            'mismoservicio' => 'required', // para preguntar si borra contenido anterior y crear nuevo carrito
            'cantidad' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos );

        if($validarDatos->fails()){return ['success' => 0]; }

        DB::beginTransaction();

        try {
            // sacar id del servicio por el producto
            $datos = DB::table('servicios AS s')
                ->join('servicios_tipo AS st', 'st.servicios_id', '=', 's.id')
                ->join('producto AS p', 'p.servicios_tipo_id', '=', 'st.id')
                ->select('s.id AS idServicio', 's.cerrado_emergencia', 's.mensaje_cerrado', 's.activo')
                ->where('p.id', $request->productoid)
                ->first();

            $idservicio = $datos->idServicio; //id servcio

            $di = DireccionCliente::where('clientes_id', $request->clienteid)
                ->where('seleccionado', 1)
                ->first();

            $idzona = $di->zonas_id; // id zona

            $infoZonaServicio = ZonasServicio::where('zonas_id', $idzona)
                ->where('servicios_id', $idservicio)
                ->first();


            //**** VALIDACIONES

            // validacion de horarios para este servicio
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
                ->where('h.servicios_id', $idservicio) // id servicio
                ->where('h.dia', $diaSemana) // dia
                ->get();

            // si verificar con la segunda hora
            if(count($dato) >= 1){

                $horario = DB::table('horario_servicio AS h')
                    ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                    ->where('h.segunda_hora', '1') // segunda hora habilitada
                    ->where('h.servicios_id', $idservicio) // id servicio
                    ->where('h.dia', $diaSemana) // dia
                    ->where(function ($query) use ($hora) {
                        $query->where('h.hora1', '<=' , $hora)
                            ->where('h.hora2', '>=' , $hora)
                            ->orWhere('h.hora3', '<=', $hora)
                            ->where('h.hora4', '>=' , $hora);
                    })
                    ->get();

                if(count($horario) >= 1){
                    // abierto
                }else{
                    // cerrado horario normal del servicio (4 horarios)
                    return ['success' => 1];
                }

            }else{
                // verificar sin la segunda hora
                $horario = DB::table('horario_servicio AS h')
                    ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                    ->where('h.segunda_hora', 0) // segunda hora habilitada
                    ->where('h.servicios_id', $idservicio) // id servicio
                    ->where('h.dia', $diaSemana)
                    ->where('h.hora1', '<=', $hora)
                    ->where('h.hora2', '>=', $hora)
                    ->get();

                if(count($horario) >= 1){
                    // abierto
                }else{
                    // cerrado horario normal del servicio (2 horarios)
                    return ['success' => 1];
                }
            }

            // preguntar si este dia esta cerrado
            $cerradoHoy = HorarioServicio::where('servicios_id', $idservicio)->where('dia', $diaSemana)->first();

            if($cerradoHoy->cerrado == 1){
                // cerrado este dia el negocio
                return ['success' => 2];
            }

            $infoZona = Zona::where('id', $idzona)->first();

            if($infoZona->saturacion == 1){
                return ['success' => 3, 'msj1' => $infoZona->mensaje_bloqueo];
            }

            // cerrado emergencia del servicio
            if($datos->cerrado_emergencia == 1){
                return ['success' => 4, 'msj1' => $datos->mensaje_cerrado];
            }

            // servicio no activo actualmente
            if($datos->activo == 0){
                return ['success' => 5];
            }

            // servicio saturacion para esta zona agregado
            if($infoZonaServicio->saturacion){
                return ['success' => 6, 'msj1' => $infoZonaServicio->mensaje_bloqueo];
            }

            // horario delivery para esa zona
            $horarioDeliveryZona = Zona::where('id', $idzona)
                ->where('hora_abierto_delivery', '<=', $hora)
                ->where('hora_cerrado_delivery', '>=', $hora)
                ->get();

            if(count($horarioDeliveryZona) >= 1){
                // abierto
            }else{
                // cerrado horario de zona
                return ['success' => 7];
            }


            // verificar si el usuario va a borrar la tabla de carrito de compras
            if($request->mismoservicio == 1){ // borrar tablas
                $tabla1 = CarritoTemporal::where('clientes_id', $request->clienteid)->first();
                CarritoExtra::where('carrito_temporal_id', $tabla1->id)->delete();
                CarritoTemporal::where('clientes_id', $request->clienteid)->delete();
                DB::commit();
            }
            // preguntar si usuario ya tiene un carrito de compras
            if($cart = CarritoTemporal::where('clientes_id', $request->clienteid)->first()){

                // ver limite de unidades del producto que quiere agregar y comparar si esta el mismo producto en carrito
                // no esta agregando del mismo servicio
                if($cart->servicios_id != $idservicio){

                    $nombreServicio = Servicios::where('id', $cart->servicios_id)->pluck('nombre')->first();

                    return [
                        'success' => 8, // no agregando del mismo servicio
                        'nombre' => $nombreServicio // nombre del servicio que tengho el carrito de compras
                    ];
                }

                // si esta agregando del mismo servicio
                $extra = new CarritoExtra();
                $extra->carrito_temporal_id = $cart->id;
                $extra->producto_id = $request->productoid;
                $extra->cantidad = $request->cantidad; // siempre sera 1 el minimo
                $extra->nota_producto = $request->notaproducto;
                $extra->save();
                DB::commit();

                return [ //producto guardado
                    'success' => 9
                ];
            }else{

                $carrito = new CarritoTemporal();
                $carrito->clientes_id = $request->clienteid;
                $carrito->servicios_id = $idservicio;
                $carrito->zonas_id = $di->zonas_id;
                $carrito->save();

                // guardar producto
                $idcarrito = $carrito->id;
                $extra = new CarritoExtra();
                $extra->carrito_temporal_id = $idcarrito;
                $extra->producto_id = $request->productoid;
                $extra->cantidad = $request->cantidad;
                $extra->nota_producto = $request->notaproducto;
                $extra->save();
                DB::commit();

                return [
                    'success' => 9 // producto agregado
                ];
            }

        }catch(\Error $e){
            DB::rollback();

            return [
                'success' => 100
            ];
        }
    }

    public function verCarritoDecompras(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required',
        );


        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Cliente::where('id', $request->clienteid)->first()){

            try {

                $estadoProductoGlobal = 0; // saver si producto esta activo
                $estadoCerradoNormal = 0; // horarios (2 y hora horas)
                $estadoCerradoNormalDia = 0; // este dia
                $estadoSaturacionZona = 0;
                $msjEstadoSaturacionZona = "";
                $estadoCerradoEmergencia = 0;
                $estadoServicioNoActivo = 0;
                $estadoSaturacionZonaServicio = 0;
                $msjEstadoSaturacionZonaServicio = "";
                $estadoHorarioDezona = 0;

                // preguntar si usuario ya tiene un carrito de compras
                if($cart = CarritoTemporal::where('clientes_id', $request->clienteid)->first()){
                    $producto = DB::table('producto AS p')
                        ->join('carrito_extra AS c', 'c.producto_id', '=', 'p.id')
                        ->select('p.id AS productoID', 'p.nombre', 'c.cantidad',
                            'p.imagen', 'p.precio', 'p.activo', 'p.disponibilidad',
                            'c.id AS carritoid', 'p.utiliza_imagen')
                        ->where('c.carrito_temporal_id', $cart->id)
                        ->get();

                    $servicioidC = $cart->servicios_id; // id del servicio que esta en el carrito

                    $infoServicio = Servicios::where('id', $servicioidC)->first();
                    $infoZonaServicio = ZonasServicio::where('zonas_id', $cart->zonas_id)
                        ->where('servicios_id', $servicioidC)->first();

                    // verificar unidades de cada producto
                    foreach ($producto as $pro) {

                        // verificar si un producto no esta disponible o activo
                        $infop = Producto::where('id', $pro->productoID)->first();

                        // saver si al menos un producto no esta activo o disponible
                        if($pro->activo == 0 || $pro->disponibilidad == 0){
                            $estadoProductoGlobal = 1; // producto no disponible global
                        }

                        // multiplicar cantidad por el precio de cada producto
                        $precio = $pro->cantidad * $pro->precio;

                        // convertir
                        $valor = number_format((float)$precio, 2, '.', '');

                        $pro->precio = $valor;
                    }

                    // sub total de la orden
                    $subTotal = collect($producto)->sum('precio'); // sumar todos el precio

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
                        ->where('h.servicios_id', $servicioidC) // id servicio
                        ->where('h.dia', $diaSemana) // dia
                        ->get();

                    // si verificar con la segunda hora
                    if(count($dato) >= 1){

                        $horario = DB::table('horario_servicio AS h')
                            ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                            ->where('h.segunda_hora', '1') // segunda hora habilitada
                            ->where('h.servicios_id', $servicioidC) // id servicio
                            ->where('h.dia', $diaSemana) // dia
                            ->where(function ($query) use ($hora) {
                                $query->where('h.hora1', '<=' , $hora)
                                    ->where('h.hora2', '>=' , $hora)
                                    ->orWhere('h.hora3', '<=', $hora)
                                    ->where('h.hora4', '>=' , $hora);
                            })
                            ->get();

                        if(count($horario) >= 1){
                            // abierto
                        }else{
                            // cerrado horario normal del servicio (4 horarios)
                            $estadoCerradoNormal = 1;
                        }

                    }else{
                        // verificar sin la segunda hora
                        $horario = DB::table('horario_servicio AS h')
                            ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                            ->where('h.segunda_hora', 0) // segunda hora habilitada
                            ->where('h.servicios_id', $servicioidC) // id servicio
                            ->where('h.dia', $diaSemana)
                            ->where('h.hora1', '<=', $hora)
                            ->where('h.hora2', '>=', $hora)
                            ->get();

                        if(count($horario) >= 1){
                            // abierto
                        }else{
                            // cerrado horario normal del servicio (2 horarios)
                            $estadoCerradoNormal = 1;
                        }
                    }

                    // preguntar si este dia esta cerrado
                    $cerradoHoy = HorarioServicio::where('servicios_id', $servicioidC)->where('dia', $diaSemana)->first();

                    if($cerradoHoy->cerrado == 1){
                        // cerrado este dia el negocio
                        $estadoCerradoNormalDia = 1;
                    }

                    $infoZona = Zona::where('id', $cart->zonas_id)->first();

                    if($infoZona->saturacion == 1){
                        $estadoSaturacionZona = 1;
                        $msjEstadoSaturacionZona = $infoZona->mensaje_bloqueo;
                    }

                    // cerrado emergencia del servicio
                    if($infoServicio->cerrado_emergencia == 1){
                        $estadoCerradoEmergencia = 1;
                    }

                    // servicio no activo actualmente
                    if($infoServicio->activo == 0){
                        $estadoServicioNoActivo = 1;
                    }

                    // servicio saturacion para esta zona servicio agregado
                    if($infoZonaServicio->saturacion){
                        $estadoSaturacionZonaServicio = 1;
                        $msjEstadoSaturacionZonaServicio = $infoZonaServicio->mensaje_bloqueo;
                    }

                    // los negocios privados que dan su domicilio, no les afecta
                    // horario de zona
                    if($infoServicio->privado == 0) {
                        // horario delivery para esa zona
                        $horarioDeliveryZona = Zona::where('id', $cart->zonas_id)
                            ->where('hora_abierto_delivery', '<=', $hora)
                            ->where('hora_cerrado_delivery', '>=', $hora)
                            ->get();

                        if (count($horarioDeliveryZona) >= 1) {
                            // abierto
                        } else {
                            // cerrado horario de zona
                            $estadoHorarioDezona = 1;
                        }
                    }

                    // horario domicilio de la zona
                    $horazona1 = date("h:i A", strtotime($infoZona->hora_abierto_delivery));
                    $horazona2 = date("h:i A", strtotime($infoZona->hora_cerrado_delivery));

                    return [
                        'success' => 1,
                        'subtotal' => number_format((float)$subTotal, 2, '.', ''), // subtotal
                        'estadoProductoGlobal' => $estadoProductoGlobal, // saver si producto esta activo
                        'estadoCerradoNormal' => $estadoCerradoNormal, // horarios (2 y hora horas)
                        'estadoCerradoNormalDia' => $estadoCerradoNormalDia, // cerrado este dia
                        'estadoSaturazionZona' => $estadoSaturacionZona, // saturacion de zona
                        'msjEstadoSaturacionZona' => $msjEstadoSaturacionZona, // mensaje saturacion de zona
                        'estadoCerradoEmergencia' => $estadoCerradoEmergencia, // cerrado emergencia
                        'estadoServicioNoActivo' => $estadoServicioNoActivo, // servicio no activo
                        'estadoSaturazionZonaServicio' => $estadoSaturacionZonaServicio, // zona servicio para esta zona con saturacion
                        'msjEstadoSaturacionZonaServicio' => $msjEstadoSaturacionZonaServicio, // mensaje zona servicio saturacion
                        'estadoHorarioDezona' => $estadoHorarioDezona, // cerrado por horario de zona
                        'horazona1' => $horazona1, // horario de la zona de su direccion actual
                        'horazona2' => $horazona2, // horario de la zona de su direccion actual
                        'producto' => $producto, //todos los productos
                    ];

                }else{
                    return [
                        'success' => 2  // no tiene carrito de compras
                    ];
                }
            }catch(\Error $e){
                return [
                    'success' => 3, // error
                ];
            }
        }
        else{
            return ['success' => 3]; // usuario no encontrado
        }
    }


    public function borrarCarritoDeCompras(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos );

        if($validarDatos->fails()){return ['success' => 0]; }

        if($carrito = CarritoTemporal::where('clientes_id', $request->clienteid)->first()){
            CarritoExtra::where('carrito_temporal_id', $carrito->id)->delete();
            CarritoTemporal::where('clientes_id', $request->clienteid)->delete();

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function borrarProductoDelCarrito(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required',
            'carritoid' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        // verificar si tenemos carrito
        if($ctm = CarritoTemporal::where('clientes_id', $request->clienteid)->first()){

            // encontrar el producto a borrar
            if(CarritoExtra::where('id', $request->carritoid)->first()){
                CarritoExtra::where('id', $request->carritoid)->delete();

                // saver si tenemos mas productos aun
                $dato = CarritoExtra::where('carrito_temporal_id', $ctm->id)->get();

                if(count($dato) == 0){
                    CarritoTemporal::where('id', $ctm->id)->delete();
                    return ['success' => 1]; // carrito de compras borrado
                }

                return ['success' => 2]; // producto eliminado
            }else{
                // producto a borrar no encontrado
                return ['success' => 3];
            }
        }else{
            // carrito de compras borrado
            return ['success' => 1 ];
        }
    }

    public function verProductoCarritoEditar(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required',
            'carritoid' => 'required' //es id del producto
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(CarritoTemporal::where('clientes_id', $request->clienteid)->first()){

            if(CarritoExtra::where('id', $request->carritoid)->first()){

                // informacion del producto + cantidad elegida
                $producto = DB::table('producto AS p')
                    ->join('carrito_extra AS c', 'c.producto_id', '=', 'p.id')
                    ->join('servicios_tipo AS s', 's.id', '=', 'p.servicios_tipo_id')
                    ->select('p.id AS productoID', 'p.nombre', 'p.descripcion', 'c.cantidad', 'c.nota_producto',
                       'p.imagen', 'p.precio', 'p.utiliza_nota', 'p.nota', 'p.utiliza_imagen')
                    ->where('c.id', $request->carritoid)
                    ->first();

                return [
                    'success' => 1,
                    'producto' => $producto,
                ];

            }else{
                // producto no encontrado
                return ['success' => 2];
            }
        }else{
            // no tiene carrito
            return ['success' => 3];
        }
    }

    public function editarCantidadProducto(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required',
            'cantidad' => 'required',
            'carritoid' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        // buscar carrito de compras a quien pertenece el producto
        // verificar si existe el carrito
        if(CarritoTemporal::where('clientes_id', $request->clienteid)->first()){
            // verificar si existe el carrito extra id que manda el usuario
            if(CarritoExtra::where('id', $request->carritoid)->first()){

                CarritoExtra::where('id', $request->carritoid)->update(['cantidad' => $request->cantidad,
                    'nota_producto' => $request->nota]);

                return [
                    'success' => 1 // cantidad actualizada
                ];

            }else{
                // producto no encontrado
                return ['success' => 2];
            }
        }else{
            return ['success' => 2];
        }
    }





}
