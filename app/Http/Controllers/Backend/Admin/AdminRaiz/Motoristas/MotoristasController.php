<?php

namespace App\Http\Controllers\Backend\Admin\AdminRaiz\Motoristas;

use App\Http\Controllers\Controller;
use App\Models\MotoristaExperiencia;
use App\Models\Motoristas;
use App\Models\MotoristasAsignados;
use App\Models\MotoristasOrdenes;
use App\Models\Servicios;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MotoristasController extends Controller
{

    public function index(){
        return view('backend.admin.motoristas.index');
    }

    public function tablaMotorista(){
        $motorista = Motoristas::orderBy('nombre')->get();
        return view('backend.admin.motoristas.tabla.tablamotoristas', compact('motorista'));
    }

    // nuevo motorista
    public function nuevo(Request $request){

        $regla = array(
            'identi' => 'required',
            'nombre' => 'required',
            'telefono' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Motoristas::where('identificador', $request->identi)->first()){
            return ['success' => 1];
        }

        if(Motoristas::where('telefono', $request->telefono)->first()){
            return ['success' => 2];
        }

        $fecha = Carbon::now('America/El_Salvador');

            $m = new Motoristas();
            $m->nombre = $request->nombre;
            $m->telefono = $request->telefono;
            $m->password = bcrypt($request->password);
            $m->activo = 1;
            $m->identificador = $request->identi;
            $m->disponible = 0;
            $m->fecha = $fecha;
            $m->token_fcm = null;
            $m->codigo = null;

            if($m->save()){
                return ['success' => 3];
            }else{
                return ['success' => 4];
            }
    }

    // informacion del motorista
    public function informacion(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($p = Motoristas::where('id', $request->id)->first()){

            $fecha = date("d-m-Y", strtotime($p->fecha));

            return ['success' => 1, 'motorista' => $p, 'fecha' => $fecha];
        }else{
            return ['success' => 2];
        }
    }

    // editar motorista
    public function editar(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'telefono' => 'required',
            'cbactivo' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Motoristas::where('id', $request->id)->first()){


            if(Motoristas::where('telefono', $request->telefono)->where('id', '!=', $request->id)->first()){
                return ['success' => 1];
            }

            if(Motoristas::where('identificador', $request->identificador)->where('id', '!=', $request->id)->first()){
                return ['success' => 2];
            }

                Motoristas::where('id', $request->id)->update([
                    'nombre' => $request->nombre,
                    'telefono' => $request->telefono,
                    'activo' => $request->cbactivo,
                    'identificador' => $request->identificador,
                ]);

                // actualizar password
                if($request->checkpassword == 1){
                    Motoristas::where('id', $request->id)->update([
                        'password' => bcrypt('12345678')
                    ]);
                }

                return ['success' => 3];
        }else{
            return ['success' => 5];
        }
    }


    //** motoristas asignados */

    public function index2(){

        $servicios = Servicios::all();
        $motoristas = Motoristas::all();

        return view('backend.admin.motoristas.asignados.index', compact('servicios', 'motoristas'));
    }

    // tabla motorista asignados
    public function tablaAsignacionMotorista(){

        $motorista = DB::table('motoristas_asignados AS ms')
            ->join('servicios AS s', 's.id', '=', 'ms.servicios_id')
            ->join('motoristas AS m', 'm.id', '=', 'ms.motoristas_id')
            ->select('ms.id','s.identificador AS identi', 's.nombre', 'm.nombre AS nombreMotorista', 'm.identificador')
            ->get();

        return view('backend.admin.motoristas.asignados.tabla.tablaasignados', compact('motorista'));
    }

    // borrar motorista asignado
    public function borrar(Request $request){

            $regla = array(
                'id' => 'required',
            );

            $validar = Validator::make($request->all(), $regla);

            if ($validar->fails()){ return ['success' => 0];}

            MotoristasAsignados::where('id', $request->id)->delete();

            return ['success' => 1];

    }

    // borrar TODAS LAS ASIGNACIONES
    public function borrarTodo(Request $request){
        MotoristasAsignados::truncate();
        return ['success' => 1];
    }

    // agregar motorista a servicio
    public function nuevomotoservicio(Request $request){
        if($request->isMethod('post')){

            $regla = array(
                'motorista' => 'required',
                'servicio' => 'required',
            );


            $validar = Validator::make($request->all(), $regla);

            if ($validar->fails()){ return ['success' => 0];}

            if(MotoristasAsignados::where('servicios_id', $request->servicio)->where('motoristas_id', $request->motorista)->first()){
                return ['success' => 1];
            }

            $m = new MotoristasAsignados();
            $m->servicios_id = $request->servicio;
            $m->motoristas_id = $request->motorista;
            if($m->save()){
                return ['success' => 2];
            }else{
                return ['success' => 3];
            }
        }
    }

    // agregar motorista global
    public function nuevoGlobal(Request $request){
        if($request->isMethod('post')){

            $regla = array(
                'motorista' => 'required'
            );

            $validar = Validator::make($request->all(), $regla);

            if ($validar->fails()){ return ['success' => 0];}

            // obener todos los servicios
            $servicios = Servicios::all();
            foreach($servicios as $s){

                if(MotoristasAsignados::where('motoristas_id', $request->motorista)
                    ->where('servicios_id', $s->id)->first()){
                    // ya existe el registro
                }else{
                    // guardar registro
                    $m = new MotoristasAsignados();
                    $m->servicios_id = $s->id;
                    $m->motoristas_id = $request->motorista;
                    $m->save();
                }
            }

            // Completado
            return ['success' => 1];
        }
    }

    // sacar promedio completo
    public function promedio(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Motoristas::where('id', $request->id)->first()){

            $datos = MotoristaExperiencia::where('motoristas_id', $request->id)->get();

            $conteo = MotoristaExperiencia::where('motoristas_id', $request->id)->count();

            if($conteo > 0){

                $sumado=0;
                foreach ($datos as $valor){

                    $sumado = $sumado + $valor->experiencia;
                }

                $resultado = $sumado / $conteo;

                return ['success' => 1, 'promedio' => $resultado];
            }else{
                return ['success' => 2];
            }
        }else{
            return ['success' => 2];
        }
    }


    public function indexMotoristaOrdenes(){

        $motoristas = Motoristas::orderBy('identificador')->get();

        return view('backend.admin.motoristas.ordenes.index', compact('motoristas'));
    }

    public function tablaMotoristaOrdenes(){

        $lista = MotoristasOrdenes::orderBy('id', 'DESC')->get();

        foreach ($lista as $l){
            $infoMotorista = Motoristas::where('id', $l->motoristas_id)->first();
            $l->motorista = $infoMotorista->identificador;

            $l->fecha_agarrada  = date("d-m-Y h:i A", strtotime($l->fecha_agarrada));
        }

        return view('backend.admin.motoristas.ordenes.tablaordenes', compact('lista'));
    }

    public function editarMotoristaOrden(Request $request){
        $regla = array(
            'id' => 'required',
            'idmoto' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(MotoristasOrdenes::where('id', $request->id)->first()){

            MotoristasOrdenes::where('id', $request->id)->update([
                'motoristas_id' => $request->idmoto
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionMotoristaOrden(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($p = MotoristasOrdenes::where('id', $request->id)->first()){

            return ['success' => 1, 'motorista' => $p];
        }else{
            return ['success' => 2];
        }
    }





}
