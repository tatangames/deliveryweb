<?php

namespace App\Http\Controllers\Backend\api\Productos;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiProductosController extends Controller
{

    public function infoProductoIndividual(Request $request){

        // validaciones para los datos
        $reglaDatos = array(
            'productoid' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if($validarDatos->fails()){return ['success' => 0]; }

        if(Producto::where('id', $request->productoid)->first()){

            $producto = DB::table('servicios AS s')
                ->join('servicios_tipo AS st', 'st.servicios_id', '=', 's.id')
                ->join('producto AS p', 'p.servicios_tipo_id', '=', 'st.id')
                ->select('p.id', 'p.nombre', 'p.descripcion', 'p.precio',
                   'p.imagen', 'p.activo', 'p.disponibilidad', 'p.utiliza_imagen',  'p.utiliza_nota', 'p.nota')
                ->where('p.id', $request->productoid)
                ->get();

            return ['success' => 1, 'producto' => $producto];

        }else{
            return ['success' => 2];
        }
    }

}
