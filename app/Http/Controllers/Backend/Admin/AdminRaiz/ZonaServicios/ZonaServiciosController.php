<?php

namespace App\Http\Controllers\Backend\Admin\AdminRaiz\ZonaServicios;

use App\Http\Controllers\Controller;
use App\Models\Servicios;
use App\Models\TiposServicio;
use App\Models\Zona;
use App\Models\ZonasServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class ZonaServiciosController extends Controller
{

    // lista de zonas servicios
    public function index(){

        $zonas = Zona::orderBy('nombre')->get();
        $servicios = Servicios::orderBy('nombre')->get();
        $serviciostipo = TiposServicio::orderBy('nombre')->get();

        return view('backend.admin.zonaservicio.index', compact('zonas', 'servicios', 'serviciostipo'));
    }

    // tabla
    public function tablaZonaServicios(){

        $servicio = DB::table('zonas_servicio AS zs')
            ->join('zonas AS z', 'z.id', '=', 'zs.zonas_id')
            ->join('servicios AS s', 's.id', '=', 'zs.servicios_id')
            ->select('zs.id', 'z.identificador', 's.identificador AS idenServicio',
                's.nombre', 'zs.activo', 'zs.precio_envio', 'zs.ganancia_motorista',
                'zs.zona_envio_gratis', 'zs.min_envio_gratis', 'zs.costo_envio_gratis',
                'zs.saturacion', 'zs.mensaje_bloqueo')
            ->orderBy('s.id', 'ASC')
            ->get();

        return view('backend.admin.zonaservicio.tabla.tablazonaservicio', compact('servicio'));
    }


    // agregar zona servicio
    public function nuevo(Request $request){

            $regla = array(
                'selectzona' => 'required',
                'selectservicio' => 'required',
                'cbactivo' => 'required',
                'precioenvio' => 'required',
                'ganancia' => 'required',
            );

            $validar = Validator::make($request->all(), $regla);

            if ($validar->fails()){ return ['success' => 0];}

            // ya existe
            if(ZonasServicio::where('zonas_id', $request->selectzona)->where('servicios_id', $request->selectservicio)->first()){
                return ['success' => 1];
            }

            $zona = new ZonasServicio();
            $zona->zonas_id = $request->selectzona;
            $zona->servicios_id = $request->selectservicio;
            $zona->precio_envio = $request->precioenvio;
            $zona->activo = $request->cbactivo;
            $zona->ganancia_motorista = $request->ganancia;
            $zona->posicion = 1;
            $zona->zona_envio_gratis = 0;
            $zona->costo_envio_gratis = 0;
            $zona->min_envio_gratis = 0;
            $zona->saturacion = 0;

            if($zona->save()){
                return ['success' => 2];
            }else{
                return ['success' => 3];
            }
    }

    // informacion

    public function informacion(Request $request){

            $regla = array(
                'id' => 'required',
            );

            $validar = Validator::make($request->all(), $regla );

        if ($validar->fails()){ return ['success' => 0];}

            if(ZonasServicio::where('id', $request->id)->first()){

                $zonaservicio = DB::table('zonas_servicio AS zs')
                    ->join('zonas AS z', 'z.id', '=', 'zs.zonas_id')
                    ->join('servicios AS s', 's.id', '=', 'zs.servicios_id')
                    ->select('zs.id', 'z.identificador AS idenZona', 's.identificador AS idenServicio',
                       'zs.activo', 'zs.precio_envio', 'zs.ganancia_motorista',
                        'zs.min_envio_gratis', 'zs.costo_envio_gratis', 'zs.zona_envio_gratis',
                        'zs.saturacion', 'zs.mensaje_bloqueo')
                    ->where('zs.id', $request->id)
                    ->first();

                return ['success' => 1, 'zonaservicio' => $zonaservicio];
            }else{
                return ['success' => 2];
            }
    }

    // editar servicios
    public function editarServicio(Request $request){

            $rules = array(
                'id' => 'required',
                'toggle' => 'required',
                'precioenvio' => 'required',
                'ganancia' => 'required',
                'cbmingratis' => 'required',
                'minenvio' => 'required',
                'togglebloqueo' => 'required'
            );

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()){ return ['success' => 0];}

            if(ZonasServicio::where('id', $request->id)->first()){

                ZonasServicio::where('id', $request->id)->update([
                    'precio_envio' => $request->precioenvio,
                    'activo' => $request->toggle,
                    'ganancia_motorista' => $request->ganancia,
                    'min_envio_gratis' => $request->cbmingratis,
                    'costo_envio_gratis' => $request->minenvio,
                    'zona_envio_gratis' => $request->cbzonagratis,
                    'saturacion' => $request->togglebloqueo,
                    'mensaje_bloqueo' => $request->mensajebloqueo,
                ]);

                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
    }



    // seteara el campo de envio gratis, solo a servicios PUBLICOS
    public function setearEnvioGratis(Request $request){
        // $myString = $request->idzonas;
        // $myArray = explode(',', $myString);
        if(is_array( $request->idzonas)){

            // obtener todas las zonas servicios, con los servicios publicos
            $publicos = ZonasServicio::whereIn('zonas_id', $request->idzonas)->get();

            $pilaArray = array();
            foreach($publicos as $p){
                array_push($pilaArray, $p->id);
            }

            // recorrer cada uno para actualizar
            foreach($pilaArray as $p){
                ZonasServicio::where('id', $p)->update([
                    'zona_envio_gratis' => $request->cbzonapublico
                ]);
            }

            return ['success' => 1];
        }

        return ['success' => 2];
    }



    // filtrado
    public function filtrado($idzona, $idtipo){
        return view('backend.admin.zonaservicio.filtrado.index', compact('idzona', 'idtipo'));
    }

    // tabla filtrado
    public function tablaFiltrado($idzona, $idtipo){

        $servicio = DB::table('servicios AS s')
            ->join('tipos_servicio AS ts', 'ts.id', '=', 's.tipos_servicio_id')
            ->join('zonas_servicio AS z', 'z.servicios_id', '=', 's.id')
            ->select('z.id','s.nombre', 's.descripcion', 's.imagen',
                's.cerrado_emergencia', 'z.zonas_id', 'z.posicion', 's.tipos_servicio_id', 's.activo', 's.identificador', 'ts.nombre AS nombreServicio')
            ->where('z.zonas_id', $idzona)
            ->where('s.tipos_servicio_id', $idtipo)
            ->orderBy('z.posicion', 'ASC')
            ->get();

        return view('backend.admin.zonaservicio.filtrado.tabla.tablafiltrado', compact('servicio', 'idzona', 'idtipo'));
    }

    // ordenar producto
    public function ordenar(Request $request){

        // actualizar posicion por id de zona servicio y set posicion
        foreach ($request->order as $order) {

            ZonasServicio::where('id', $order['id'])
                ->update(['posicion' => $order['posicion']]);
        }

        return ['success' => 1];
    }

    // cambiar precio envio por zona
    public function precioEnvioPorZona(Request $request){
            $rules = array(
                'zonaid' => 'required',
                'preciozona' => 'required'
            );

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()){ return ['success' => 0];}

            if(ZonasServicio::where('zonas_id', $request->zonaid)->first()){

                ZonasServicio::where('zonas_id', $request->zonaid)->update([
                    'precio_envio' => $request->preciozona
                ]);

                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
    }

    public function precioGananciaPorZona(Request $request){
        $rules = array(
            'zonaid' => 'required',
            'preciozona' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){ return ['success' => 0];}

        if(ZonasServicio::where('zonas_id', $request->zonaid)->first()){

            ZonasServicio::where('zonas_id', $request->zonaid)->update([
                'ganancia_motorista' => $request->preciozona
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


    // aplicar nuevo cargo de envio por zona y servicio
    public function aplicarNuevoCargoZonaServicio(Request $request){

        if(is_array( $request->idzonas)){

            $publicos = ZonasServicio::whereIn('zonas_id', $request->idzonas)
                ->whereIn('servicios_id', $request->idservicios)
                ->get();


            $pilaArray = array();
            foreach($publicos as $p){
                array_push($pilaArray, $p->id);
            }

            // recorrer cada uno para actualizar
            foreach($pilaArray as $p){
                ZonasServicio::where('id', $p)->update([
                    'min_envio_gratis' => $request->cbzonapublico,
                    'costo_envio_gratis' => $request->mincompra,
                ]);
            }

            return ['success' => 1];
        }

        return ['success' => 2];
    }

    public function activarOCerrarServicioZona(Request $request){

        if(is_array( $request->idzonas)){

            $publicos = ZonasServicio::whereIn('zonas_id', $request->idzonas)
                ->whereIn('servicios_id', $request->idservicios)
                ->get();

            $pilaArray = array();
            foreach($publicos as $p){
                array_push($pilaArray, $p->id);
            }

            // recorrer cada uno para actualizar
            foreach($pilaArray as $p){
                ZonasServicio::where('id', $p)->update([
                    'activo' => $request->check,
                ]);
            }

            return ['success' => 1];
        }

        return ['success' => 2];
    }

}
