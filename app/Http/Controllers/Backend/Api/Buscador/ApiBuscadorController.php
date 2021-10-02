<?php

namespace App\Http\Controllers\Backend\api\Buscador;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\DireccionCliente;
use App\Models\Etiquetas;
use App\Models\EtiquetasServicio;
use App\Models\HorarioServicio;
use App\Models\PalabrasBuscador;
use App\Models\Producto;
use App\Models\Servicios;
use App\Models\ServiciosTipo;
use App\Models\TiposServicio;
use App\Models\TiposServicioZona;
use App\Models\Zona;
use App\Models\ZonasServicio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use OneSignal;


class ApiBuscadorController extends Controller
{
    public function buscarProductoSeccion(Request $request){

        $reglaDatos = array(
            'seccionid' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(ServiciosTipo::where('id', $request->seccionid)->first()){

            // solo mostrar producto activo, y el disponibilidad saldra en rojo

            $productos = Producto::where('servicios_tipo_id', $request->seccionid)
                ->where('activo', 1)
                ->orderBy('posicion', 'ASC')
                ->get();

            return ['success' => 1, 'productos' => $productos];
        }else{
            return ['success' => 2];
        }
    }

    public function listaEtiquetasValidas(Request $request){

        $reglaDatos = array(
            'id' => 'required', // id cliente
            'nombre' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Cliente::where('id', $request->id)->first()){

            // obtener zona
            $infoDireccion = DireccionCliente::where('clientes_id', $request->id)
                ->where('seleccionado', 1)->first();

            // todos los id de servicios validos
            $zonaServicio = ZonasServicio::where('zonas_id', $infoDireccion->zonas_id)->get();

            $pilaServicios = array();

            // obtener un x numero de etiquetas validas en todos los servicios que el cliente
            // puede ver

            // recorrer cada id servicio
            foreach ($zonaServicio as $s){

                // obtener todas las etiquetas vinculadas a un servicio
                $etiquetaServicio = EtiquetasServicio::where('servicios_id', $s->servicios_id)->get();

                // recorrer cada etiqueta con cada servicio
                foreach ($etiquetaServicio as $l){

                    if($info = Etiquetas::where('id', $l->etiquetas_id)->
                    where('nombre', 'like', '%' . $request->nombre . '%')->first()){
                        // ingresar el id de la etiqueta
                        array_push($pilaServicios, $info->id);
                    }
                }
            }

            $listado = Etiquetas::whereIn('id', $pilaServicios)
                ->orderBy('nombre')
                ->distinct()
                ->take(15)
                ->get();

            return ['success' => 1, 'listado' => $listado];

        }else{
            return ['success' => 2];
        }
    }


    // buscador general de negocios
    public function buscarNegocioGeneral(Request $request){

        $reglaDatos = array(
            'id' => 'required', // id cliente
            'nombre' => 'required'
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Cliente::where('id', $request->id)->first()){

            DB::beginTransaction();

            try {

                // buscar zona de direccion del cliente
                $infoDireccion = DireccionCliente::where('clientes_id', $request->id)
                    ->where('seleccionado', 1)->first();

                // lista de ID servicios que estan en mi zona
                $zonaServicio = ZonasServicio::where('zonas_id', $infoDireccion->zonas_id)->get();

                $pilaServicios = array();

                // saber si una etiqueta esta vinculada a un servicio permitido
                foreach ($zonaServicio as $idServicio) {

                    if (DB::table('etiquetas_servicio AS es')
                        ->join('etiquetas AS e', 'e.id', '=', 'es.etiquetas_id')
                        ->select('es.id', 'es.servicios_id', 'es.etiquetas_id', 'e.nombre')
                        ->where('e.nombre', 'like', '%' . $request->nombre . '%')
                        ->where('es.servicios_id', $idServicio->id)
                        ->first()) {
                        array_push($pilaServicios, $idServicio->id);
                    }
                }

                // Verificar horarios

                $numSemana = [
                    0 => 1, // domingo
                    1 => 2, // lunes
                    2 => 3, // martes
                    3 => 4, // miercoles
                    4 => 5, // jueves
                    5 => 6, // viernes
                    6 => 7, // sabado
                ];


               // return [$pilaServicios];

                // hora y fecha
                $getValores = Carbon::now('America/El_Salvador');
                $getDiaHora = $getValores->dayOfWeek;
                $diaSemana = $numSemana[$getDiaHora];
                $hora = $getValores->format('H:i:s');

                // servicios para la zona
                $servicios = DB::table('zonas_servicio AS z')
                    ->join('servicios AS s', 's.id', '=', 'z.servicios_id')
                    ->select('s.id', 's.nombre AS nombreServicio',
                        's.descripcion', 's.imagen', 'z.id AS zonaservicioid',
                        's.logo', 's.tipo_vista', 's.privado', 's.cerrado_emergencia', 's.mensaje_cerrado',
                        'z.posicion', 'z.zonas_id')
                    ->whereIn('s.id', $pilaServicios) // listado de ID servicio
                    ->where('z.activo', 1) // activo de zona servicios para esta zona
                    ->where('s.activo', 1) // activo el servicio globalmente
                        ->where('z.zonas_id', $infoDireccion->zonas_id)
                    ->orderBy('z.posicion', 'ASC')
                    ->get();

                // horario delivery para esa zona
                $horaDelivery = Zona::where('id', $infoDireccion->zonas_id)
                    ->where('hora_abierto_delivery', '<=', $hora)
                    ->where('hora_cerrado_delivery', '>=', $hora)
                    ->get();

                foreach ($servicios as $user) {

                    $infoZonaServicio = ZonasServicio::where('zonas_id', $user->zonas_id)
                        ->where('servicios_id', $user->id)
                        ->first();

                    $saturacionZonaServicio = 0;
                    $msjBloqueoZonaServicio = "";
                    if($infoZonaServicio->saturacion == 1){
                        $saturacionZonaServicio = 1;
                        $msjBloqueoZonaServicio = $infoZonaServicio->mensaje_bloqueo;
                    }

                    $horaZona = 0;

                    if(count($horaDelivery) >= 1){
                        //$horaZona = 0; // abierto
                    }else{
                        $horaZona = 1; // cerrado
                    }

                    $user->horazona = $horaZona;

                    $user->saturacionZonaServicio = $saturacionZonaServicio;
                    $user->msjBloqueoZonaServicio = $msjBloqueoZonaServicio;

                    // verificar si usara la segunda hora
                    $dato = DB::table('horario_servicio AS h')
                        ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                        ->where('h.segunda_hora', 1) // segunda hora habilitada
                        ->where('h.servicios_id', $user->id) // id servicio   1
                        ->where('h.dia', $diaSemana) // dia   2
                        ->get();

                    // si verificar con la segunda hora
                    if(count($dato) >= 1){

                        $horario = DB::table('horario_servicio AS h')
                            ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                            ->where('h.segunda_hora', 1) // segunda hora habilitada
                            ->where('h.servicios_id', $user->id) // id servicio
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
                            ->where('h.servicios_id', $user->id) // id servicio
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
                    $cerradoHoy = HorarioServicio::where('servicios_id', $user->id)->where('dia', $diaSemana)->first();

                    if($cerradoHoy->cerrado == 1){
                        $user->cerrado = 1;
                    }else{
                        $user->cerrado = 0;
                    }
                } // finaliza for

                // problema para enviar a esta zona, ejemplo motoristas sin disponibilidad
                $zonaSa = Zona::where('id', $infoDireccion->zonas_id)->first();

                /* validaciones
                1- cerrado horario
                2- este dia cerrado
                3- saturacion zona completa
                4- cerrado emergencia + msj1
                5- zona servicio cerrado + msj1
                6- horario de la zona

                */
                return [
                    'success' => 1,
                    'zonasaturacion' => $zonaSa->saturacion,
                    'servicios' => $servicios // lista de servicios
                ];

            } catch(\Throwable $e){
                DB::rollback();
                return ['success' => 2];
            }
        }else{
            return ['success' => 2];
        }
    }


    public function buscadorProductoServicio(Request $request){

        $reglaDatos = array(
            'servicioid' => 'required',
            'nombre' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Servicios::where('id', $request->servicioid)->first()){

            $a = $request->nombre;

            $productos = DB::table('servicios AS s')
                ->join('servicios_tipo AS st', 'st.servicios_id', '=', 's.id')
                ->join('producto AS p', 'p.servicios_tipo_id', '=', 'st.id')
                ->select('p.id', 'p.nombre', 'p.imagen', 'p.activo', 'p.precio', 'p.disponibilidad', 'p.utiliza_imagen')
                ->where('s.id', $request->servicioid)
                ->where('p.disponibilidad', 1) // producto disponible
                ->where('p.activo', 1) // producto activo
                ->where('st.activo', 1) // categoria activa
                ->where('st.visible', 1) // categoria visible
                ->where(function ($query) use ($a) {
                    $query->where('p.nombre', 'like', '%' . $a . '%')
                        ->orWhere('p.descripcion', 'like', '%' . $a . '%');
                })
                ->get();

            return ['success' => 1, 'productos' => $productos];
        }else{
            return ['success' => 2];
        }
    }



    public function prueba(Request $request){
        $tituloMM1 = "Ordegcdfgdfn 2";
        $mensajeMM1 = "Recarga 2";

        $usuario = Cliente::where('id', 1)->first();

        // mandar notificacion al cliente si quiere esperar

        if($usuario->token_fcm != null){
            try {

                $this->envioNoticacionCliente($tituloMM1, $mensajeMM1, $usuario->token_fcm);
            } catch (Exception $e) {
                return 'error' . $e;
            }
        }

        return "saliod";
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
