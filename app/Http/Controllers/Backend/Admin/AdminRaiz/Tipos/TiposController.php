<?php

namespace App\Http\Controllers\Backend\Admin\AdminRaiz\Tipos;

use App\Http\Controllers\Controller;
use App\Models\Tipos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class TiposController extends Controller
{

    public function index(){
        return view('backend.admin.tipos.index');
    }

    public function tablaTipos(){
        $tipos = Tipos::orderBy('nombre')->get();

        return view('backend.admin.tipos.tabla.tablatipos', compact('tipos'));
    }

    public function nuevoTipo(Request $request){
        $regla = array(
            'nombre' => 'required',
            'descripcion' => 'required'
        );

        $validator = Validator::make($request->all(), $regla);

        if ( $validator->fails()){return ['success' => 0];}

        $tipo = new Tipos();
        $tipo->nombre = $request->nombre;
        $tipo->descripcion = $request->descripcion;

        if($tipo->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // informacion tipos
    public function informacionTipos(Request $request){
        if($request->isMethod('post')){
            $rules = array(
                'id' => 'required'
            );

            $validator = Validator::make($request->all(), $rules );

            if ( $validator->fails()){return ['success' => 0];}

            if($tipo = Tipos::where('id', $request->id)->first()){
                return['success' => 1, 'tipo' => $tipo];
            }else{
                return['success' => 2];
            }
        }
    }

    public function editarTipos(Request $request){
        if($request->isMethod('post')){
            $rules = array(
                'id' => 'required',
                'nombre' => 'required',
                'descripcion' => 'required'
            );

            $validator = Validator::make($request->all(), $rules);

            if ( $validator->fails()){return ['success' => 0];}

            if(Tipos::where('id', $request->id)->first()){

                Tipos::where('id', $request->id)->update([
                    'nombre' => $request->nombre,
                    'descripcion' => $request->descripcion]);

                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }


}
