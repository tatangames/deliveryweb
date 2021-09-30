<?php

namespace App\Http\Controllers\Backend\api\Mapa;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiMapaController extends Controller
{
    public function puntosZonaPoligonos(){

        $rr = DB::table('zonas AS z')
            ->join('zona_poligono AS p', 'p.zonas_id', '=', 'z.id')
            ->select('z.id')
            ->where('z.activo', 1)
            ->groupBy('id')
            ->get();

        // meter zonas que si tienen poligonos
        $pila = array();
        foreach($rr as $p){
            array_push($pila, $p->id);
        }

        $tablas = DB::table('zonas')
            ->select('id AS idZona', 'nombre AS nombreZona')
            ->whereIn('id', $pila)
            ->get();

        $resultsBloque = array();
        $index = 0;

        foreach($tablas  as $secciones){
            array_push($resultsBloque,$secciones);

            $subSecciones = DB::table('zona_poligono AS pol')
                ->select('pol.latitud AS latitudPoligono', 'pol.longitud AS longitudPoligono')
                ->where('pol.zonas_id', $secciones->idZona)
                ->get();

            $resultsBloque[$index]->poligonos = $subSecciones;
            $index++;
        }

        return [
            'success' => 1,
            'poligono' => $tablas
        ];
    }

}
