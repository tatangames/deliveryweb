<?php

namespace App\Http\Controllers\Backend\Admin\AdminRaiz\Tipos;

use App\Http\Controllers\Controller;
use App\Models\Tipos;
use App\Models\TiposServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class TiposServicioController extends Controller
{
    public function index(){

        $tipos = Tipos::orderBy('nombre')->get();
        return view('backend.admin.tiposServicio.index', compact('tipos'));
    }

    public function tablaTiposServicio(){

        $tipo = DB::table('tipos AS t')
            ->join('tipos_servicio AS ts', 'ts.tipos_id', '=', 't.id')
            ->select('ts.id', 'ts.nombre', 'ts.descripcion', 'ts.imagen', 't.nombre AS nombretipo' )
            ->get();

        return view('backend.admin.tiposServicio.tabla.tablaTiposServicio', compact('tipo'));
    }

    public function nuevoTipoServicio(Request $request){

        $regla = array(
            'nombre' => 'required',
            'descripcion' => 'required',
            'tipos' => 'required'
        );

        $validator = Validator::make($request->all(), $regla);

        if ( $validator->fails()){return ['success' => 0];}

        $cadena = Str::random(15);
        $tiempo = microtime();
        $union = $cadena.$tiempo;
        $nombre = str_replace(' ', '_', $union);

        $extension = '.'.$request->imagen->getClientOriginalExtension();
        $nombreFoto = $nombre.strtolower($extension);
        $avatar = $request->file('imagen');
        $upload = Storage::disk('imagenes')->put($nombreFoto, \File::get($avatar));

        if($upload){

            $tipo = new TiposServicio();
            $tipo->nombre = $request->nombre;
            $tipo->descripcion = $request->descripcion;
            $tipo->imagen = $nombreFoto;
            $tipo->tipos_id = $request->tipos;

            if($tipo->save()){
                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }else{
            return ['success' => 3];
        }
    }

    public function informacionTipoServicio(Request $request){
        if($request->isMethod('post')){
            $rules = array(
                'id' => 'required'
            );

            $validator = Validator::make($request->all(), $rules);

            if ( $validator->fails()){return ['success' => 0];}

            if($tipo = TiposServicio::where('id', $request->id)->first()){
                return['success' => 1, 'tipo' => $tipo];
            }else{
                return['success' => 2];
            }
        }
    }

    public function editarTipoServicio(Request $request){
        if($request->isMethod('post')){
            $rules = array(
                'id' => 'required',
                'nombre' => 'required',
                'descripcion' => 'required',
            );

            $validator = Validator::make($request->all(), $rules);

            if ( $validator->fails()){return ['success' => 0];}

            // validar solamente si mando la imagen
            if($request->hasFile('imagen')){

                // validaciones para los datos
                $regla2 = array(
                    'imagen' => 'required|image',
                );

                $validar2 = Validator::make($request->all(), $regla2);

                if ( $validator->fails()){return ['success' => 0];}
            }

            if($tipo = TiposServicio::where('id', $request->id)->first()){

                if($request->hasFile('imagen')){

                    $cadena = Str::random(15);
                    $tiempo = microtime();
                    $union = $cadena.$tiempo;
                    $nombre = str_replace(' ', '_', $union);

                    $extension = '.'.$request->imagen->getClientOriginalExtension();
                    $nombreFoto = $nombre.strtolower($extension);
                    $avatar = $request->file('imagen');
                    $upload = Storage::disk('imagenes')->put($nombreFoto, \File::get($avatar));

                    if($upload){
                        $imagenOld = $tipo->imagen; //nombre de imagen a borrar

                        TiposServicio::where('id', $request->id)->update([
                            'nombre' => $request->nombre,
                            'descripcion' => $request->descripcion,
                            'imagen' => $nombreFoto]);

                        if(Storage::disk('imagenes')->exists($imagenOld)){
                            Storage::disk('imagenes')->delete($imagenOld);
                        }

                        return ['success' => 1];
                    }else{
                        return ['success' => 2];
                    }

                }else{
                    TiposServicio::where('id', $request->id)->update([
                        'nombre' => $request->nombre,
                        'descripcion' => $request->descripcion]);

                    return ['success' => 1];
                }
            }else{
                return ['success' => 3];
            }
        }
    }

}
