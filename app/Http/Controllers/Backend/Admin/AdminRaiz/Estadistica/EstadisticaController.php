<?php

namespace App\Http\Controllers\Backend\Admin\AdminRaiz\Estadistica;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Motoristas;
use App\Models\Ordenes;
use App\Models\Propietarios;
use App\Models\Servicios;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EstadisticaController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        $fecha = Carbon::now('America/El_Salvador');
        // total ordenes hoy

        $num1 = Ordenes::where('fecha_orden', $fecha)->count();

       // total ordenes completadas

        $num2 = Ordenes::where('estado_7', 1)
            ->where('estado_8', 0)->count();

        // total de servicios

        $num3 = Servicios::count();

        // total de propietarios

        $num4 = Propietarios::count();

        // total de motoristas

        $num5 = Motoristas::count();

        // total cliente registrados hoy

        $num6 = Cliente::whereDate('fecha', $fecha)->count();

        // total de clientes

        $num7 = Cliente::count();

        return view('backend.admin.estadisticas.index', compact('num1', 'num2',
        'num3', 'num4', 'num5', 'num6', 'num7'));
    }
}
