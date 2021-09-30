<?php

namespace App\Http\Controllers\Backend\Admin\AdminRaiz\Cupones;

use App\Http\Controllers\Controller;
use App\Models\CuponEnvio;
use App\Models\Cupones;
use App\Models\CuponProducto;
use App\Models\CuponServicios;
use App\Models\CuponZonas;
use App\Models\Servicios;
use App\Models\TipoCupon;
use App\Models\Zona;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class CuponesController extends Controller
{

    public function indexTipoCupon(){
        return view('backend.admin.cupones.tipocupon.index');
    }

    public function tablaIndexTipoCupon(){
        $lista = TipoCupon::orderBy('nombre')->get();
        return view('backend.admin.cupones.tipocupon.tabla.tablatipocupon', compact('lista'));
    }

    public function indexCupones(){

        $tipocupon = TipoCupon::orderBy('nombre')->get();

        return view('backend.admin.cupones.cupon.index', compact('tipocupon'));
    }

    public function tablaIndexCupones(){
        $lista = Cupones::orderBy('cupon')->get();

        foreach ($lista as $ll){

            $ll->fecha = date("d-m-Y h:i A", strtotime($ll->fecha));

            $info = TipoCupon::where('id', $ll->tipo_cupon_id)->first();

            $ll->tipocupon = $info->nombre;
        }

        return view('backend.admin.cupones.cupon.tabla.tablacupon', compact('lista'));
    }

    public function nuevoCupon(Request $request){

        $regla = array(
            'tipocupon' => 'required',
            'nombre' => 'required',
            'limite' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $fecha = Carbon::now('America/El_Salvador');

        if(Cupones::where('cupon', $request->nombre)->first()){
            return ['success' => 1];
        }

        $m = new Cupones();
        $m->tipo_cupon_id = $request->tipocupon;
        $m->cupon = $request->nombre;
        $m->uso_limite = $request->limite;
        $m->contador = 0;
        $m->fecha = $fecha;
        $m->activo = 0;

        if($m->save()){
            return ['success' => 2];
        }else{
            return ['success' => 3];
        }
    }

    public function informacionCupon(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($p = Cupones::where('id', $request->id)->first()){

            $lista = TipoCupon::orderBy('nombre')->get();

            return ['success' => 1, 'lista' => $p, 'tipocupon' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarCupones(Request $request){

        $regla = array(
            'id' => 'required',
            'tipocupon' => 'required',
            'nombre' => 'required',
            'limite' => 'required',
            'toggle' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Cupones::where('id', '!=', $request->id)
            ->where('cupon', $request->nombre)
            ->first()){
            return ['success' => 1];
        }

        if(Cupones::where('id', $request->id)->first()){

            Cupones::where('id', $request->id)->update([
                'tipo_cupon_id' => $request->tipocupon,
                'cupon' => $request->nombre,
                'uso_limite' => $request->limite,
                'activo' => $request->toggle,
            ]);

            return ['success' => 2];
        }else{
            return ['success' => 3];
        }
    }


    public function indexCuponZonas(){
        $cupon = Cupones::orderBy('cupon')->get();
        $zona = Zona::orderBy('nombre')->get();

        return view('backend.admin.cupones.cuponzona.index', compact('cupon', 'zona'));
    }


    public function tablaCuponZonas(){
        $cuponzona = CuponZonas::orderBy('id')->get();

        foreach ($cuponzona as $ll){
            $info = Cupones::where('id', $ll->cupones_id)->first();
            $infoZona = Zona::where('id', $ll->zonas_id)->first();

            $ll->nombrecupon = $info->cupon;
            $ll->zona = $infoZona->nombre;
        }

        return view('backend.admin.cupones.cuponzona.tablacuponzona', compact('cuponzona'));
    }


    public function borrarCuponZona(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        CuponZonas::where('id', $request->id)->delete();

        return ['success' => 1];
    }

    public function borrarCuponZonaGlobal(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        CuponZonas::truncate();

        return ['success' => 1];
    }

    public function nuevaZonaCupon(Request $request){

        $regla = array(
            'zona' => 'required',
            'cupon' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Cupones::where('cupon', $request->nombre)->first()){
            return ['success' => 1];
        }

        if(CuponZonas::where('cupones_id', $request->cupon)
            ->where('zonas_id', $request->zona)
            ->first()){
            return ['success' => 1];
        }

        $m = new CuponZonas();
        $m->cupones_id = $request->cupon;
        $m->zonas_id = $request->zona;

        if($m->save()){
            return ['success' => 2];
        }else{
            return ['success' => 3];
        }

    }

    // ***

    public function indexCuponServicios(){
        $cupon = Cupones::orderBy('cupon')->get();
        $servicio = Servicios::orderBy('nombre')->get();

        return view('backend.admin.cupones.cuponservicio.index', compact('cupon', 'servicio'));
    }


    public function tablaCuponServicios(){
        $cuponservicio = CuponServicios::orderBy('id')->get();

        foreach ($cuponservicio as $ll){
            $info = Cupones::where('id', $ll->cupones_id)->first();
            $infoServicio = Servicios::where('id', $ll->servicios_id)->first();

            $ll->nombrecupon = $info->cupon;
            $ll->servicio = $infoServicio->nombre;
        }

        return view('backend.admin.cupones.cuponservicio.tablacuponservicio', compact('cuponservicio'));
    }


    public function borrarCuponServicio(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        CuponServicios::where('id', $request->id)->delete();

        return ['success' => 1];
    }

    public function borrarCuponServicioGlobal(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        CuponServicios::truncate();

        return ['success' => 1];
    }

    public function nuevaServicioCupon(Request $request){

        $regla = array(
            'servicio' => 'required',
            'cupon' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Cupones::where('cupon', $request->nombre)->first()){
            return ['success' => 1];
        }

        if(CuponServicios::where('cupones_id', $request->cupon)
            ->where('servicios_id', $request->servicio)
            ->first()){
            return ['success' => 1];
        }

        $m = new CuponServicios();
        $m->cupones_id = $request->cupon;
        $m->servicios_id = $request->servicio;

        if($m->save()){
            return ['success' => 2];
        }else{
            return ['success' => 3];
        }
    }

    public function indexCuponEnvio(){
        $cupon = Cupones::where('tipo_cupon_id', 1)
            ->orderBy('cupon')
            ->get();

        return view('backend.admin.cupones.envio.index', compact('cupon'));
    }

    public function tablaCuponEnvio(){

        $lista = DB::table('cupon_envio AS c')
            ->join('cupones AS cc', 'cc.id', '=', 'c.cupones_id')
            ->select('c.id', 'cc.cupon', 'c.dinero')
            ->get();

        return view('backend.admin.cupones.envio.tablaenvio', compact('lista'));
    }

    public function registrarCuponEnvio(Request $request){

        $regla = array(
            'cupon' => 'required',
            'dinero' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(CuponEnvio::where('cupones_id', $request->cupon)->first()){
            return ['success' => 1];
        }

        $ca = new CuponEnvio();
        $ca->cupones_id = $request->cupon;
        $ca->dinero = $request->dinero;

        if($ca->save()){
            return ['success' => 2];
        }else{
            return ['success' => 3];
        }
    }

    public function informacionCuponEnvio(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($p = CuponEnvio::where('id', $request->id)->first()){

            return ['success' => 1, 'lista' => $p];
        }else{
            return ['success' => 2];
        }
    }


    public function editarCuponEnvio(Request $request){

        $regla = array(
            'id' => 'required',
            'dinero' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(CuponEnvio::where('id', $request->id)->first()){

            CuponEnvio::where('id', $request->id)->update([
                'dinero' => $request->dinero
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function borrarCuponEnvio(Request $request){

        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(CuponEnvio::where('id', $request->id)->first()){

            CuponEnvio::where('id', $request->id)->delete();

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


    //*****


    public function indexCuponProducto(){
        $cupon = Cupones::where('tipo_cupon_id', 2)
            ->orderBy('cupon')
            ->get();

        return view('backend.admin.cupones.producto.index', compact('cupon'));
    }

    public function tablaCuponProducto(){

        $lista = DB::table('cupon_producto AS c')
            ->join('cupones AS cc', 'cc.id', '=', 'c.cupones_id')
            ->select('c.id', 'cc.cupon', 'c.dinero', 'c.nombre')
            ->get();

        return view('backend.admin.cupones.producto.tablaproducto', compact('lista'));
    }

    public function registrarCuponProducto(Request $request){

        $regla = array(
            'cupon' => 'required',
            'dinero' => 'required',
            'producto' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(CuponEnvio::where('cupones_id', $request->cupon)->first()){
            return ['success' => 1];
        }

        $ca = new CuponProducto();
        $ca->cupones_id = $request->cupon;
        $ca->dinero = $request->dinero;
        $ca->nombre = $request->producto;

        if($ca->save()){
            return ['success' => 2];
        }else{
            return ['success' => 3];
        }
    }

    public function informacionCuponProducto(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($p = CuponProducto::where('id', $request->id)->first()){

            return ['success' => 1, 'lista' => $p];
        }else{
            return ['success' => 2];
        }
    }


    public function editarCuponProducto(Request $request){

        $regla = array(
            'id' => 'required',
            'dinero' => 'required',
            'producto' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(CuponProducto::where('id', $request->id)->first()){

            CuponProducto::where('id', $request->id)->update([
                'dinero' => $request->dinero,
                'nombre' => $request->producto
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function borrarCuponProducto(Request $request){

        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(CuponProducto::where('id', $request->id)->first()){

            CuponProducto::where('id', $request->id)->delete();

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


}
