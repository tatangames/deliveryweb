<?php

namespace App\Http\Controllers\Backend\Admin\AdminRaiz\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Ordenes;
use App\Models\OrdenesDescripcion;
use App\Models\OrdenesDirecciones;
use App\Models\Servicios;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{

    public function index(){

        $servicios = Servicios::orderBy('nombre')->get();

        return view('backend.admin.reportes.index', compact('servicios'));
    }

    public function reporteListaOrdenes($fecha1, $fecha2, $estado){

        $date1 = Carbon::parse($fecha1)->format('Y-m-d');
        $date2 = Carbon::parse($fecha2)->addDays(1)->format('Y-m-d');

        $f1 = Carbon::parse($fecha1)->format('d-m-Y');
        $f2 = Carbon::parse($fecha2)->format('d-m-Y');

        //$orden = Ordenes::whereBetween('fecha_orden', array($date1, $date2))->get();
        $orden = DB::table('ordenes AS o')
            ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
            ->select('o.id', 'o.fecha_orden', 'o.precio_consumido', 'precio_envio', 'o.servicios_id',
            's.privado')
            ->where('s.privado', $estado)
            ->whereBetween('o.fecha_orden', array($date1, $date2))
            ->get();

        $contador = 0;
        $consumido = 0;
        $envio = 0;
        foreach ($orden as $l){
            $contador++;
            $l->contador = $contador;

            $infoOrden = OrdenesDirecciones::where('ordenes_id', $l->id)->first();
            $metodo = "Efectivo";
            if($infoOrden->metodo_pago == 2){
              $metodo = "Monedero";
            }

            $l->metodo = $metodo;

            $l->fecha_orden = date("d-m-Y", strtotime($l->fecha_orden));


            $infoServicio = Servicios::where('id', $l->servicios_id)->first();

            $l->negocio = $infoServicio->nombre;

            $consumido = $consumido + $l->precio_consumido;
            $envio = $envio + $l->precio_envio;
        }

        $consumido = number_format((float)$consumido, 2, '.', '');
        $envio = number_format((float)$envio, 2, '.', '');

        // generar vista y enviar datos
        $view =  \View::make('backend.admin.reportes.listaordenes.index', compact(['orden', 'f1', 'f2', 'consumido', 'envio']))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($view)->setPaper('carta', 'portrait');

        return $pdf->stream();
    }


    public function reporteListaOrdenesServicio($fecha1, $fecha2, $estado){

        $date1 = Carbon::parse($fecha1)->format('Y-m-d');
        $date2 = Carbon::parse($fecha2)->addDays(1)->format('Y-m-d');

        $f1 = Carbon::parse($fecha1)->format('d-m-Y');
        $f2 = Carbon::parse($fecha2)->format('d-m-Y');

        //$orden = Ordenes::whereBetween('fecha_orden', array($date1, $date2))->get();
        $orden = DB::table('ordenes AS o')
            ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
            ->select('o.id', 'o.fecha_orden', 'o.precio_consumido', 'precio_envio', 'o.servicios_id')
            ->where('s.id', $estado)
            ->whereBetween('o.fecha_orden', array($date1, $date2))
            ->get();

        $servicio = "";
        if($data = Servicios::where('id', $estado)->first()){
            $servicio = $data->nombre;
        }

        $contador = 0;
        $consumido = 0;
        $envio = 0;
        foreach ($orden as $l){
            $contador++;
            $l->contador = $contador;

            $infoOrden = OrdenesDirecciones::where('ordenes_id', $l->id)->first();
            $metodo = "Efectivo";
            if($infoOrden->metodo_pago == 2){
                $metodo = "Monedero";
            }

            $l->comi = $infoOrden->copia_comision;

            $l->metodo = $metodo;

            $l->fecha_orden = date("d-m-Y", strtotime($l->fecha_orden));

            $infoServicio = Servicios::where('id', $l->servicios_id)->first();

            $l->negocio = $infoServicio->nombre;

            $consumido = $consumido + $l->precio_consumido;
            $envio = $envio + $l->precio_envio;
        }

        $consumido = number_format((float)$consumido, 2, '.', '');
        $envio = number_format((float)$envio, 2, '.', '');

        // generar vista y enviar datos
        $view =  \View::make('backend.admin.reportes.listaordenesservicio.index', compact(['orden', 'f1', 'f2', 'consumido', 'envio', 'servicio']))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($view)->setPaper('carta', 'portrait');

        return $pdf->stream();
    }


}
