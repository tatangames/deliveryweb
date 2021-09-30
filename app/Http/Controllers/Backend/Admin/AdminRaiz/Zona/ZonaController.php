<?php

namespace App\Http\Controllers\Backend\Admin\AdminRaiz\Zona;

use App\Http\Controllers\Controller;
use App\Models\Zona;
use App\Models\ZonaPoligono;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ZonaController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        return view('backend.admin.zonas.index');
    }

    // tabla para ver zonas
    public function tablaZonas(){
        $zonas = Zona::orderBy('id', 'ASC')->get();

        foreach($zonas as $z){
            $z->hora_abierto_delivery = date("h:i A", strtotime($z->hora_abierto_delivery));
            $z->hora_cerrado_delivery = date("h:i A", strtotime($z->hora_cerrado_delivery));
        }

        return view('backend.admin.zonas.tabla.tablazonas', compact('zonas'));
    }

    // crear zona
    public function nuevaZona(Request $request){

            $rules = array(
                'nombre' => 'required',
                'horaabierto' => 'required',
                'horacerrado' => 'required',
                'identificador' => 'required',
                'tiempoextra' => 'required',
                'latitud' => 'required',
                'longitud' => 'required'
            );

            $validator = Validator::make($request->all(), $rules);

            if ( $validator->fails()){return ['success' => 0];}

            if(Zona::where('identificador', $request->identificador)->first()){
                return ['success'=> 1];
            }

            $fecha = Carbon::now('America/El_Salvador');

            $zona = new Zona();
            $zona->nombre = $request->nombre;
            $zona->descripcion = $request->descripcion;
            $zona->identificador = $request->identificador;
            $zona->latitud = $request->latitud;
            $zona->longitud = $request->longitud;
            $zona->saturacion = 0;
            $zona->hora_abierto_delivery = $request->horaabierto;
            $zona->hora_cerrado_delivery = $request->horacerrado;
            $zona->fecha = $fecha;
            $zona->activo = 1;
            $zona->tiempo_extra = $request->tiempoextra;
            $zona->mensaje_bloqueo = $request->mensaje;

            if($zona->save()){
                return ['success'=>2];
            }else{
                return ['success'=>3];
            }

    }

    // informacion de la zona
    public function informacionZona(Request $request){
            $rules = array(
                'id' => 'required'
            );

            $validator = Validator::make($request->all(), $rules);

            if ( $validator->fails()){return ['success' => 0];}

            if($zona = Zona::where('id', $request->id)->first()){
                return['success' => 1, 'zona' => $zona];
            }else{
                return['success' => 2];
            }

    }

    // editar la zona
    public function editarZona(Request $request){
            $rules = array(
                'id' => 'required',
                'nombre' => 'required',
                'descripcion' => 'required',
                'horaabierto' => 'required',
                'horacerrado' => 'required',
                'tiempoextra' => 'required',
                'togglep' => 'required',
                'togglea' => 'required',
                'identificador' => 'required',
                'latitud' => 'required',
                'longitud' => 'required',
                'mensaje' => 'required',
            );

            $validator = Validator::make($request->all(), $rules);

            if ( $validator->fails()){return ['success' => 0];}

            if(Zona::where('identificador', $request->identificador)
                ->where('id', '!=', $request->id)
                ->first()){
                return ['success'=> 1];
            }

            if(Zona::where('id', $request->id)->first()){

                Zona::where('id', $request->id)->update([
                    'nombre' => $request->nombre,
                    'descripcion'=> $request->descripcion,
                    'hora_abierto_delivery' => $request->horaabierto,
                    'hora_cerrado_delivery' => $request->horacerrado,
                    'tiempo_extra' => $request->tiempoextra,
                    'identificador' => $request->identificador,
                    'saturacion' => $request->togglep,
                    'activo' => $request->togglea,
                    'latitud' => $request->latitud,
                    'longitud' => $request->longitud,
                    'mensaje_bloqueo' => $request->mensaje]);

                return ['success' => 2];
            }else{
                return ['success' => 3];
            }

    }

    public function indexPoligono($id){
        $nombre = Zona::where('id', $id)->pluck('nombre')->first();
        return view('backend.admin.zonas.poligono.index', compact('nombre', 'id'));
    }

    public function nuevoPoligono(Request $request){

        $regla = array(
            'id' => 'required',
            'latitud' => 'required|array',
            'longitud' => 'required|array',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        for ($i = 0; $i < count($request->latitud); $i++) {

            $ingreso = new ZonaPoligono();
            $ingreso->zonas_id = $request->id;
            $ingreso->latitud = $request->latitud[$i];
            $ingreso->longitud = $request->longitud[$i];
            $ingreso->save();
        }

        return ['success' => 1];
    }

    public function borrarPoligonos(Request $request){

        $rules = array(
            'id' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){ return ['success' => 0]; }

        ZonaPoligono::where('zonas_id', $request->id)->delete();

        return ['success'=> 1];
    }

    public function verMapa($id){
        $poligono = ZonaPoligono::where('zonas_id', $id)->get();
        return view('backend.admin.zonas.mapa.index', compact('poligono'));
    }

    public function actualizarGlobalmente(Request $request){

        $rules = array(
            'toggle' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){ return ['success' => 0]; }

        Zona::where('id', '!=' , 0)->update([
            'mensaje_bloqueo' => $request->mensaje,
            'saturacion' => $request->toggle]);

        return ['success'=> 1];
    }



}
