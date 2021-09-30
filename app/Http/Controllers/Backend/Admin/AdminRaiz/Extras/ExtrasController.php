<?php

namespace App\Http\Controllers\Backend\Admin\AdminRaiz\Extras;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Etiquetas;
use App\Models\EtiquetasServicio;
use App\Models\InformacionAdmin;
use App\Models\PalabrasBuscador;
use App\Models\Servicios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExtrasController extends Controller
{
    public function indexExtras(){
        return view('backend.admin.extras.index');
    }

    public function tablaIndexExtras(){

        $lista = InformacionAdmin::all();

        return view('backend.admin.extras.tabla.tablaextras', compact('lista'));
    }

    public function informacion(Request $request){

        $rules = array(
            'id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if($info = InformacionAdmin::where('id', $request->id)->first()){
            return ['success' => 1, 'info' => $info];
        }else{
            return ['success' => 2];
        }
    }

    public function actualizarInformacion(Request $request){

        $rules = array(
            'id' => 'required',
            'comision' => 'required',
            'toggle_cupon' => 'required',
            'toggle_monedero' => 'required',
            'toggle_carrito' => 'required',
            'mensaje' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if(InformacionAdmin::where('id', $request->id)->first()){

            InformacionAdmin::where('id', $request->id)->update([
                'estado_cupon' => $request->toggle_cupon,
                'comision' => $request->comision,
                'activo_tarjeta' => $request->toggle_monedero,
                'mensaje_tarjeta' => $request->mensaje,
                'borrar_carrito' => $request->toggle_carrito]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


    //** Listado de etiquetas */


    public function indexEtiquetas(){
        return view('backend.admin.etiquetas.index');
    }

    public function tablaEtiquetas(){
        $lista = Etiquetas::orderBy('nombre')->get();
        return view('backend.admin.etiquetas.tabla.tablaetiquetas', compact('lista'));
    }

    public function nuevaEtiqueta(Request $request){

        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $m = new Etiquetas();
        $m->nombre = $request->nombre;

        if($m->save()){
            return ['success' => 1];
        } else{
            return ['success' => 2];
        }
    }

    public function informacionEtiqueta(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($p = Etiquetas::where('id', $request->id)->first()){

            return ['success' => 1, 'etiqueta' => $p];
        }else{
            return ['success' => 2];
        }
    }

    // editar motorista
    public function editarEtiqueta(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Etiquetas::where('id', $request->id)->first()){

            Etiquetas::where('id', $request->id)->update([
                'nombre' => $request->nombre
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function indexEtiquetasCliente(){
        return view('backend.admin.etiquetas.cliente.index');
    }

    public function tablaEtiquetasCliente(){
        $lista = PalabrasBuscador::orderBy('fecha')->get();

        foreach ($lista as $ll){

            $ll->fecha = date("d-m-Y h:i A", strtotime($ll->fecha));

            $info = Cliente::where('id', $ll->clientes_id)->first();

            $ll->nombrecliente = $info->nombre;
            $ll->telefono = $info->telefono;
        }

        return view('backend.admin.etiquetas.cliente.tabla.tablaetiquetacliente', compact('lista'));
    }

}
