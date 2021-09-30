<?php

namespace App\Http\Controllers\Backend\Admin\AdminRaiz\Clientes;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\ContadorSms;
use App\Models\DireccionCliente;
use App\Models\NumerosSms;
use App\Models\Ordenes;
use App\Models\OrdenesDescripcion;
use App\Models\Producto;
use App\Models\Servicios;
use App\Models\Zona;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClientesController extends Controller
{

    // lista de registros para envios sms
    public function indexIntentos(){
        return view('backend.admin.clientes.registrosms.index');
    }

    public function tablaindexIntentos(){

        $lista = ContadorSms::orderBy('fecha', 'DESC')->get();

        foreach ($lista as $ll){

            $ll->fecha = date("d-m-Y h:i A", strtotime($ll->fecha));

            $tiponombre = "";

            // modificar los tipos
            if($ll->tipo == 1){
                $tiponombre = "Pantalla inicio de sesión";
            }else if($ll->tipo == 2){
                $tiponombre = "Pantalla contraseña olvidada";
            }else if($ll->tipo == 3){
                $tiponombre = "Reenvio Sms para pantalla registro";
            }

            $ll->tiponombre = $tiponombre;
        }

        return view('backend.admin.clientes.registrosms.tabla.tablaregistrosms', compact('lista'));
    }

    public function indexRegistradosHoy(){

        $dataFecha = Carbon::now('America/El_Salvador');
        $fecha = date("d-m-Y", strtotime($dataFecha));
        return view('backend.admin.clientes.registradoshoy.index', compact('fecha'));
    }

    public function tablaRegistradosHoy(){

        $fecha = Carbon::now('America/El_Salvador');

        $cliente = Cliente::whereDate('fecha', $fecha)->get();

        foreach($cliente as $c){
            $c->fecha = date("d-m-Y h:i A", strtotime($c->fecha));
        }

        return view('backend.admin.clientes.registradoshoy.tabla.tablaregistradoshoy', compact('cliente'));
    }

    public function indexNumeroRegistro(){
        return view('backend.admin.clientes.numeroregistro.index');
    }

    public function tablaindexNumeroRegistro(){

        $lista = NumerosSms::orderBy('fecha', 'ASC')->get();

        foreach($lista as $c){
            $c->fecha = date("d-m-Y h:i A", strtotime($c->fecha));
        }

        return view('backend.admin.clientes.numeroregistro.tabla.tablanumeroregistro', compact('lista'));
    }

    public function indexListaClientes(){
        return view('backend.admin.clientes.listacliente.index');
    }

    public function tablaindexListaClientes(){

        $lista = Cliente::orderBy('nombre')->get();

        foreach($lista as $c){
            $c->fecha = date("d-m-Y h:i A", strtotime($c->fecha));
        }

        return view('backend.admin.clientes.listacliente.tabla.tablalistacliente', compact('lista'));
    }

    // informacion cliente
    public function informacionCliente(Request $request){

        $rules = array(
            'id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if($cliente = Cliente::where('id', $request->id)->first()){
            return ['success' => 1, 'cliente' => $cliente];
        }else{
            return ['success' => 2];
        }
    }

    public function actualizarCliente(Request $request){

        $rules = array(
            'id' => 'required',
            'toggle' => 'required',
            'togglepass' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if(Cliente::where('id', $request->id)->first()){

            Cliente::where('id', $request->id)->update(['activo' => $request->toggle]);

            if($request->togglepass == 1){
                Cliente::where('id', $request->id)->update(['password' => bcrypt('12345678')]);
            }

            return ['success'=>1];
        }else{
            return ['success'=>2];
        }
    }

    public function indexListaDirecciones($id){
        return view('backend.admin.clientes.listacliente.direcciones.index', compact('id'));
    }

    public function tablaIndexListaDirecciones($id){

        $lista = DireccionCliente::where('clientes_id', $id)
            ->orderBy('nombre')
            ->get();

        foreach ($lista as $ll){
            $infoZona = Zona::where('id', $ll->zonas_id)->first();

            $ll->zona = $infoZona->nombre;
        }

        return view('backend.admin.clientes.listacliente.direcciones.tabla.tabladirecciones', compact('id', 'lista'));
    }

    public function informacionClienteDireccion(Request $request){

        $rules = array(
            'id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if($cliente = DireccionCliente::where('id', $request->id)->first()){
            return ['success' => 1, 'cliente' => $cliente];
        }else{
            return ['success' => 2];
        }
    }

    public function actualizarClienteDireccion(Request $request){

        $rules = array(
            'id' => 'required',
            'toggle' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if(DireccionCliente::where('id', $request->id)->first()){

            DireccionCliente::where('id', $request->id)->update(['revisado' => $request->toggle]);

            return ['success'=>1];
        }else{
            return ['success'=>2];
        }
    }

    public function indexMapaPin($id){

        $infoDireccion = DireccionCliente::where('id', $id)->first();

        $latitud = $infoDireccion->latitud;
        $longitud = $infoDireccion->longitud;

        $api = config('googlekey.Google_Key');

        $nombre = "Coordenada PIN";

        return view('backend.admin.clientes.listacliente.direcciones.mapa.index', compact('latitud', 'longitud', 'api', 'nombre'));
    }

    public function indexMapaReal($id){

        $infoDireccion = DireccionCliente::where('id', $id)->first();

        if($infoDireccion->latitudreal != null){
            $latitud = $infoDireccion->latitudreal;
            $longitud = $infoDireccion->longitudreal;

            $api = config('googlekey.Google_Key');

            $nombre = "Coordenada Real";

            return view('backend.admin.clientes.listacliente.direcciones.mapa.index', compact('latitud', 'longitud', 'api', 'nombre'));
        }else{
            return view('errors.mapa');
        }
    }

    public function indexHistorial($id){
        return view('backend.admin.clientes.historial.index', compact('id'));
    }

    public function tablaHistorial($id){

        $lista = Ordenes::where('clientes_id', $id)->orderBy('id', 'DESC')->get();

        foreach ($lista as $l){

            $l->fecha_orden = date("d-m-Y h:i A", strtotime($l->fecha_orden));
            $infoNegocio = Servicios::where('id', $l->servicios_id)->first();

            $l->negocio = $infoNegocio->nombre;
        }
        return view('backend.admin.clientes.historial.tablahistorial', compact('lista'));
    }


    public function indexHistorialProducto($id){
        return view('backend.admin.clientes.historial.producto.index', compact('id'));
    }

    public function tablaHistorialProducto($id){
        $lista = OrdenesDescripcion::where('ordenes_id', $id)->get();

        foreach ($lista as $l){

            $infoProducto = Producto::where('id', $l->producto_id)->first();

            $l->imagen = $infoProducto->imagen;

            $multi = $l->cantidad * $l->precio;
            $l->multiplicado = $multi;
        }

        return view('backend.admin.clientes.historial.producto.tablaindex', compact('lista'));
    }





















    // todos los registros de credi puntos
    public function tablaRegistroCredito(){

        $cliente = DB::table('users AS u')
            ->join('usuarios_credipuntos AS c', 'c.usuario_id', '=', 'u.id')
            ->select('c.id', 'u.name', 'u.phone', 'c.fecha', 'c.credi_puntos', 'c.pago_total',
                'c.comision', 'c.idtransaccion', 'c.codigo', 'c.esreal', 'c.esaprobada', 'c.nota', 'c.fecha_revisada')
            ->where('c.revisada', 1)
            ->get();

        foreach($cliente as $c){
            $c->fecha = date("d-m-Y h:i A", strtotime($c->fecha));
            if($c->fecha_revisada != null){
                $c->fecha_revisada = date("d-m-Y h:i A", strtotime($c->fecha_revisada));
            }
        }

        return view('backend.paginas.credipuntos.tablas.tablacredipuntosverificados', compact('cliente'));
    }



    // obtener todas las direcciones del usuario extranjero,
    // aqui se vera cual falta por verificar
    public function todasLasDirecciones($id){ // id del usuario
        return view('backend.paginas.cliente.listadireccionextranjero', compact('id'));
    }

    // todas las direccion de un cliente
    public function tablaTodasLasDirecciones($id){ // id del usuario

        $datos = Direccion::where('user_id', $id)->where('estado', 0)->get(); // aun no verificado

        return view('backend.paginas.cliente.tablas.tablalistadirecciones', compact('datos'));
    }


}
