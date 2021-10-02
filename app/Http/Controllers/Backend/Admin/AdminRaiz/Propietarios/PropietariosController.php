<?php

namespace App\Http\Controllers\Backend\Admin\AdminRaiz\Propietarios;

use App\Http\Controllers\Controller;
use App\Models\Propietarios;
use App\Models\Servicios;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PropietariosController extends Controller
{
    public function index(){
        $servicios = Servicios::orderBy('nombre')->get();
        return view('backend.admin.propietarios.index', compact('servicios'));
    }

    // tabla
    public function tablaPropietarios(){
        $propi = DB::table('propietarios AS p')
            ->join('servicios AS s', 's.id', '=', 'p.servicios_id')
            ->select('p.id', 'p.bloqueado', 's.identificador', 'p.codigo', 'p.nombre AS nombrePropi',
                'p.disponibilidad', 'p.fecha', 'p.activo', 'p.telefono')
            ->get();

        return view('backend.admin.propietarios.tabla.tablapropietarios', compact('propi'));
    }

    public function nuevo(Request $request){

            $regla = array(
                'nombre' => 'required',
                'identificador' => 'required',
                'telefono' => 'required',
                'correo' => 'required',
            );

            $validar = Validator::make($request->all(), $regla);

            if ($validar->fails()){ return ['success' => 0];}

            if(Propietarios::where('correo', $request->correo)->first()){
                return ['success' => 1];
            }

            if(Propietarios::where('telefono', $request->telefono)->first()){
                return ['success' => 2];
            }

            $fecha = Carbon::now('America/El_Salvador');

            $p = new Propietarios();
            $p->nombre = $request->nombre;
            $p->telefono = $request->telefono;
            $p->password = bcrypt($request->password);
            $p->correo = $request->correo;
            $p->fecha = $fecha;
            $p->disponibilidad = 0;
            $p->token_fcm = null;
            $p->servicios_id = $request->identificador;
            $p->codigo = null;
            $p->activo = 1;
            $p->bloqueado = $request->bloqueado;

            if($p->save()){
                return ['success' => 3];
            }else{
                return ['success' => 2];
            }
    }

    public function informacion(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($p = Propietarios::where('id', $request->id)->first()){

            $servicios = Servicios::orderBy('nombre')->get();

            return ['success' => 1, 'servicios' => $servicios, 'propietario' => $p];
        }else{
            return ['success' => 2];
        }
    }

    public function editar(Request $request){
        $rules = array(
            'id' => 'required',
            'identificador' => 'required',
            'telefono' => 'required',
            'correo' => 'required',
            'activo' => 'required',
            'bloqueado' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){ return ['success' => 0];}

        if(Propietarios::where('id', $request->id)->first()){

            if(Propietarios::where('correo', $request->correo)->where('id', '!=', $request->id)->first()){
                return [
                    'success' => 1 //correo ya registrado
                ];
            }

            if(Propietarios::where('telefono', $request->telefono)->where('id', '!=', $request->id)->first()){
                return ['success' => 2];
            }

            Propietarios::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'telefono' => $request->telefono,
                'correo' => $request->correo,
                'servicios_id' => $request->identificador,
                'activo' => $request->activo,
                'bloqueado' => $request->bloqueado
            ]);

            // actualizar password
            if($request->passcheck == 1){
                Propietarios::where('id', $request->id)->update([
                    'password' => bcrypt('12345678')
                ]);
            }

            return ['success' => 3];
        }else{
            return ['success' => 4];
        }
    }
}
