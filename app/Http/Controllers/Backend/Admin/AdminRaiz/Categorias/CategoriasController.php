<?php

namespace App\Http\Controllers\Backend\Admin\AdminRaiz\Categorias;

use App\Http\Controllers\Controller;
use App\Models\Servicios;
use App\Models\ServiciosTipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoriasController extends Controller
{
    public function index($id){
        $nombre = Servicios::where('id',$id)->pluck('nombre')->first();
        return view('backend.admin.servicios.categorias.index', compact('id', 'nombre'));
    }

    // tabla lista categorias
    public function tablaCategorias($id){

        $servicio = DB::table('servicios_tipo AS st')
            ->join('servicios AS s', 's.id', '=', 'st.servicios_id')
            ->select('st.id', 's.identificador', 'st.visible', 'st.nombre', 'st.posicion', 'st.activo')
            ->where('st.servicios_id', $id)
            ->orderBy('st.posicion', 'ASC')
            ->get();

        return view('backend.admin.servicios.categorias.tabla.tablacategorias', compact('servicio'));
    }

    public function nuevaCategoria(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        // conteo de posicion
        $conteo = ServiciosTipo::count();
        $posicion = 1;

        if($conteo >= 1){
            // ya existe, obtener ultima posicion
            $registro = ServiciosTipo::orderBy('id', 'DESC')->first();
            $posicion = $registro->posicion;
            $posicion++;
        }

        $ca = new ServiciosTipo();
        $ca->nombre = $request->nombre;
        $ca->servicios_id = $request->id;
        $ca->posicion = $posicion;
        $ca->activo = 0;
        $ca->visible = 1;

        if($ca->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // informacion de la categoria
    public function informacion(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(ServiciosTipo::where('id', $request->id)->first()){

            $categoria = DB::table('servicios_tipo AS st')
                ->join('servicios AS s', 's.id', '=', 'st.servicios_id')
                ->select('st.id', 'st.nombre', 'st.visible', 'st.activo', 's.nombre AS nombreServicio')
                ->where('st.id', $request->id)
                ->first();

            return ['success' => 1, 'categoria' => $categoria];
        }else{
            return ['success' => 2];
        }
    }

    // editar la categoria
    function editar(Request $request){

        $rules = array(
            'id' => 'required',
            'toggle' => 'required',
            'togglevisible' => 'required',
            'nombre' => 'required',
        );

        $validator = Validator::make($request->all(), $rules );

        if ($validator->fails()){ return ['success' => 0];}

        if(ServiciosTipo::where('id', $request->id)->first()){

            ServiciosTipo::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'activo' => $request->toggle,
                'visible' => $request->togglevisible
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // ordenar posiciones
    public function ordenar(Request $request){

        $tasks = ServiciosTipo::all();

        foreach ($tasks as $task) {
            $id = $task->id;

            foreach ($request->order as $order) {
                if ($order['id'] == $id) {
                    $task->update(['posicion' => $order['posicion']]);
                }
            }
        }
        return ['success' => 1];
    }
}
