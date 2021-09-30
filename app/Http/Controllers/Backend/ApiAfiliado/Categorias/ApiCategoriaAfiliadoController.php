<?php

namespace App\Http\Controllers\Backend\ApiAfiliado\Categorias;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Propietarios;
use App\Models\ServiciosTipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiCategoriaAfiliadoController extends Controller
{
    public function informacionCategoriasPosiciones(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if($p = Propietarios::where('id', $request->id)->first()){

            $categorias = ServiciosTipo::where('servicios_id', $p->servicios_id)
                ->orderBy('posicion', 'ASC')->get();

            return ['success'=> 1, 'categorias'=> $categorias];
        }else{
            return ['success'=> 2];
        }
    }


    public function guardarPosicionCategorias(Request $request){

        $rules = array(
            'id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if(Propietarios::where('id', $request->id)->first()){

            foreach($request->categoria as $key => $value){

                $posicion = $value['posicion'];

                ServiciosTipo::where('id', $key)->update(['posicion' => $posicion]);
            }

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function actualizarDatosCategoria(Request $request){

        $rules = array(
            'id' => 'required',
            'idcategoria' => 'required',
            'nombre' => 'required',
            'valor' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if(Propietarios::where('id', $request->id)->first()){

            if($request->valor == 1){
                // obtener todos los productos de esa categoria
                $pL = Producto::where('servicios_tipo_id', $request->idcategoria)->get();

                $bloqueo = true;

                foreach($pL as $lista){
                    if($lista->disponibilidad == 1){ // si hay al menos 1 producto activo, no se desactiva categoria
                        $bloqueo = false;
                    }
                }

                if($bloqueo){
                    $mensaje = "Para activar la categoría, se necesita un producto disponible";
                    return ['success' => 1, 'msj1' => $mensaje];
                }
            }

            // actualizar
            ServiciosTipo::where('id', $request->idcategoria)->update(['activo' => $request->valor, 'nombre' => $request->nombre]);

            return ['success'=> 2];
        }else{
            return ['success'=> 0];
        }
    }


    public function listadoProductoPosicion(Request $request){

        $rules = array(
            'id' => 'required',
            'idcategoria' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if(Propietarios::where('id', $request->id)->first()){

            // buscar lista de productos
            $categorias = DB::table('producto AS p')
                ->join('servicios_tipo AS st', 'st.id', '=', 'p.servicios_tipo_id')
                ->select('p.id', 'p.nombre')
                ->where('st.id', $request->idcategoria)
                ->orderBy('p.posicion', 'ASC')
                ->where('p.activo', 1) // activo producto por admin
                ->get();

            return ['success'=> 1, 'categorias'=> $categorias];
        }else{
            return ['success'=> 2];
        }
    }


    public function actualizarProductosPosicion(Request $request){

        $rules = array(
            'id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if(Propietarios::where('id', $request->id)->first()){
            foreach($request->categoria as $key => $value){

                $posicion = $value['posicion'];

                Producto::where('id', $key)->update(['posicion' => $posicion]);
            }
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }

    }



}
