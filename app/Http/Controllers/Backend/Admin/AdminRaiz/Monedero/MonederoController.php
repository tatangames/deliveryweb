<?php

namespace App\Http\Controllers\Backend\Admin\AdminRaiz\Monedero;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Monedero;
use App\Models\MonederoDevuelto;
use App\Models\Ordenes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MonederoController extends Controller
{
    public function index(){
        return view('backend.admin.monedero.index');
    }

    public function tablaIndex(){

        $lista = Monedero::orderBy('fecha')->get();

        foreach ($lista as $l){

            $l->fecha = date("d-m-Y h:i A", strtotime($l->fecha));

            $info = Cliente::where('id', $l->clientes_id)->first();

            $l->nombre = $info->nombre;
            $l->telefono = $info->telefono;
        }

        return view('backend.admin.monedero.tabla.tablamonedero', compact('lista'));

    }

    public function informacionMonedero(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($p = Monedero::where('id', $request->id)->first()){

            $fecha = "";
            if($p->fecha_revisada != null) {
                $fecha = date("d-m-Y h:i A", strtotime($p->fecha_revisada));
            }

            return ['success' => 1, 'lista' => $p, 'fecha' => $fecha];
        }else{
            return ['success' => 2];
        }
    }

    public function revisarMonedero(Request $request){

        $regla = array(
            'id' => 'required',
            'toggle' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Monedero::where('id', $request->id)->first()){

            $fecha = Carbon::now('America/El_Salvador');

            Monedero::where('id', $request->id)->update([
                'revisada' => $request->toggle,
                'nota' => $request->nota,
                'fecha_revisada' => $fecha
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


    public function indexMonederoDevuelto(){
        return view('backend.admin.monedero.devuelto.index');
    }

    public function tablaIndexMonederoDevuelto(){

        $lista = MonederoDevuelto::orderBy('fecha')->get();

        foreach ($lista as $l){
            $l->fecha = date("d-m-Y h:i A", strtotime($l->fecha));
        }

        return view('backend.admin.monedero.devuelto.tabladevuelto', compact('lista'));
    }







}
