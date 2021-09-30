<?php

namespace App\Http\Controllers\Backend\Admin\AdminRaiz\Tipos;

use App\Http\Controllers\Controller;
use App\Models\TiposServicio;
use App\Models\TiposServicioZona;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TiposServicioZonaController extends Controller
{
    public function index(){

        $zona = Zona::orderBy('nombre')->get();
        $tiposervicio = TiposServicio::orderBy('nombre')->get();

        return view('backend.admin.tiposServicioZona.index', compact('zona', 'tiposervicio'));
    }

    public function tablaTipoServicioZona(){

        $tipo = DB::table('tipos_servicio_zonas AS tz')
            ->join('tipos_servicio AS ts', 'ts.id', '=', 'tz.tipos_servicio_id')
            ->join('zonas AS z', 'z.id', '=', 'tz.zonas_id')
            ->select('tz.id', 'z.nombre', 'ts.descripcion', 'z.identificador', 'tz.activo', 'ts.nombre AS nombreServicio')
            ->get();

        return view('backend.admin.tiposServicioZona.tabla.tablaTipoServicioZona', compact('tipo'));
    }


    // posiciones globales
    public function indexGlobal(){

        return view('backend.admin.tiposServicioZona.global.index');
    }

    public function tablaGlobalTipos(){

        $tipos = TiposServicio::orderBy('nombre')->get();

        foreach($tipos as $t){

            $contador = TiposServicioZona::where('tipos_servicio_id', $t->id)
                ->count();

            $t->cuantas = $contador;

            $activos = TiposServicioZona::where('tipos_servicio_id', $t->id)
                ->where('activo', 1)
                ->count();

            $t->activos = $activos;
        }

        return view('backend.admin.tiposServicioZona.global.tabla.tablaglobal', compact('tipos'));
    }

    // buscar servicio segun select
    public function buscarServicio(Request $request){
            $rules = array(
                'id' => 'required'
            );

            $validator = Validator::make($request->all(), $rules);

            if ( $validator->fails()){return ['success' => 0];}

            $noquiero = TiposServicioZona::where('zonas_id', $request->id)->get();

            $pilaOrden = array();
            foreach($noquiero as $t){
                if(!empty($t->tipos_servicio_id)){
                    array_push($pilaOrden, $t->tipos_servicio_id);
                }
            }

            // obtener todos los servicios, menos los que ya tengo
            $tiposervicio = TiposServicio::whereNotIn('id', $pilaOrden)->get();

            return ['success' => 1, 'tiposervicio' => $tiposervicio];
    }

    // nuevo tipo servicio zona
    public function nuevoTipoServicioZona(Request $request){

        $regla = array(
            'identificador' => 'required', // id zona
            'servicio' => 'required', //id tipo servicio
        );

        $validar = Validator::make($request->all(), $regla );

        if ( $validar->fails()){return ['success' => 0];}

        if(TiposServicioZona::where('tipos_servicio_id', $request->servicio)->where('zonas_id', $request->identificador)->first()){
            return ['success' => 1];
        }

        // aqui ira revuelto, todos los servicios de la misma zona, sin importar el tipo, se agregara hasta posicion ultima
        $conteo = TiposServicioZona::where('zonas_id', $request->identificador)->count();
        $posicion = 1;

        if($conteo >= 1){
            // ya existe uno
            $registro = TiposServicioZona::where('zonas_id', $request->identificador)->orderBy('id', 'DESC')->first();
            $posicion = $registro->posicion;
            $posicion++;
        }

        $tipo = new TiposServicioZona();
        $tipo->tipos_servicio_id = $request->servicio;
        $tipo->zonas_id = $request->identificador;
        $tipo->activo = 0;
        $tipo->posicion = $posicion;

        if($tipo->save()){
            return ['success' => 2];
        }else{
            return ['success' => 3];
        }
    }

    // informacion tipo servicio zona
    public function informacionTipoZona(Request $request){

        $rules = array(
            'id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){return ['success' => 0];}

        if($tipo = TiposServicioZona::where('id', $request->id)->first()){
            return['success' => 1, 'tipo' => $tipo];
        }else{
            return['success' => 2];
        }
    }

    // editar tipo servicio zona
    public function editarTipo(Request $request){
            $rules = array(
                'id' => 'required',
                'toggle' => 'required',
            );

            $validator = Validator::make($request->all(), $rules);

            if ( $validator->fails()){return ['success' => 0];}

            if(TiposServicioZona::where('id', $request->id)->first()){
                TiposServicioZona::where('id', $request->id)->update([
                    'activo' => $request->toggle]);
                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
    }

    public function indexBloqueFiltrado($idzona){
        return view('backend.admin.tiposServicioZona.filtrado.index', compact('idzona'));
    }

    public function tablaBloqueFiltrado($idzona){

        $servicio = DB::table('tipos_servicio AS ts')
            ->join('tipos_servicio_zonas AS z', 'z.tipos_servicio_id', '=', 'ts.id')
            ->select('z.id', 'ts.nombre', 'ts.descripcion')
            ->where('z.zonas_id', $idzona)
            ->orderBy('z.posicion', 'ASC')
            ->get();

        return view('backend.admin.tiposServicioZona.filtrado.tabla.tablaIndexFiltrado', compact('servicio', 'idzona'));
    }

    public function ordenarBloques(Request $request){

        $idzona = $request->idzona;

        // dame todos los servicios de ese tipo, un array
        $mismotipo = TiposServicioZona::where('zonas_id', $idzona)->get();

        $pila = array();
        foreach($mismotipo as $p){
            array_push($pila, $p->id);
        }

        $tasks = DB::table('tipos_servicio_zonas')
            ->where('zonas_id', $idzona)
            ->whereIn('id', $pila)
            ->get();

        foreach ($tasks as $task) {
            $id = $task->id;

            foreach ($request->order as $order) {
                if ($order['id'] == $id) {

                    TiposServicioZona::where('id', $task->id)
                        ->update(['posicion' => $order['posicion']]);
                }
            }
        }

        return ['success' => 1];
    }


    // ordenar tipos de servicios para todas las zonas
    public function orderTipoServicioGlobalmente(Request $request){

        // recorrer cada tipo de servicio
        foreach ($request->order as $order) {

            $tipoid = $order['id'];

            TiposServicioZona::where('tipos_servicio_id', $tipoid) // restaurante por ejemplo
                ->update(['posicion' => $order['posicion']]); // actualizar posicion
        }

        return ['success' => 1];
    }



    public function activarDesactivarTipoServicio(Request $request){

            $rules = array(
                'id' => 'required'
            );

            $validator = Validator::make($request->all(), $rules);

            if ( $validator->fails()){return ['success' => 0];}

            TiposServicioZona::where('tipos_servicio_id', $request->id)->update(['activo' => $request->estado]);

            return['success' => 1];
    }


    // por zona servicio
    public function activarDesactivarZonaServicio(Request $request){

        if($request->isMethod('post')){
            $rules = array(
                'id' => 'required'
            );

            $messages = array(
                'id.required' => 'El ID tipo servicio es requerido.'
            );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() )
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            ZonasServicios::where('servicios_id', $request->id)->update(['activo' => $request->estado]);

            return['success' => 1];
        }
    }
}
