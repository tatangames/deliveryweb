<?php

namespace App\Http\Controllers\Backend\ApiAfiliado\Perfil;

use App\Http\Controllers\Controller;
use App\Models\HorarioServicio;
use App\Models\Propietarios;
use App\Models\Servicios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ApiPerfilAfiliadoController extends Controller
{

    public function informacionCuenta(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if($p = Propietarios::where('id', $request->id)->first()){

            $infoServicio = Servicios::where('id', $p->servicios_id)->first();

            return ['success'=> 1,
                'nombre' => $p->nombre,
                'correo'=> $p->correo,
                'servicio' => $infoServicio->nombre];
        }else{
            return ['success'=> 2];
        }
    }

    public function informacionDisponibilidad(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if($p = Propietarios::where('id', $request->id)->first()){

            $infoServicio = Servicios::where('id', $p->servicios_id)->first();

            return ['success'=> 1,
                'disponibilidad' => $p->disponibilidad,
                'cerrado'=> $infoServicio->cerrado_emergencia,
                'msj1' => $infoServicio->mensaje_cerrado];
        }else{
            return ['success'=> 2];
        }
    }

    public function listadoHorarios(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if($p = Propietarios::where('id', $request->id)->first()){

            $servicios = HorarioServicio::where('servicios_id', $p->servicios_id)->get();

            foreach($servicios as $s){
                $s->hora1 = date("h:i A", strtotime($s->hora1));
                $s->hora2 = date("h:i A", strtotime($s->hora2));
                $s->hora3 = date("h:i A", strtotime($s->hora3));
                $s->hora4 = date("h:i A", strtotime($s->hora4));
            }

            return ['success' => 1, 'horario' => $servicios];

        }else{
            return ['success'=> 2];
        }
    }

    public function guardarEstados(Request $request){

        $rules = array(
            'id' => 'required',
            'cerrado' => 'required',
            'disponibilidad' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if($p = Propietarios::where('id', $request->id)->first()){

            Propietarios::where('id', $request->id)->update(['disponibilidad' => $request->disponibilidad]);
            Servicios::where('id', $p->servicios_id)->update(['cerrado_emergencia' => $request->cerrado,
                'mensaje_cerrado' => $request->mensaje]);

            return ['success'=> 1];
        }else{
            return ['success'=> 2];
        }
    }

    public function informacionTiempoOrden(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if($p = Propietarios::where('id', $request->id)->first()){

            $infoServicio = Servicios::where('id', $p->servicios_id)->first();


            $runtime = $infoServicio->tiempo;
            $hh = $runtime / 60;
            $minutos = $runtime % 60;

            $hora = intval($hh);

            $estado = "";

            if($infoServicio->orden_automatica == 1){
                if($hora == 0){
                    $estado = $minutos . " Minutos";
                }else{
                    if($minutos == 0){
                        $estado = $hora . " Horas";
                    }else{
                        $estado = $hora . " Horas Y " . $minutos . " Minutos";
                    }
                }

            }else{
                if($hora == 0){
                    $estado = $minutos . " Minutos";
                }else{
                    if($minutos == 0){
                        $estado = $hora . " Horas";
                    }else{
                        $estado = $hora . " Horas Y " . $minutos . " Minutos";
                    }
                }
            }

            if($infoServicio->orden_automatica == 1){
                $mensaje = "Se iniciara la orden y se tendra un tiempo de " . $estado . " Para completar la orden";
            }else{
                $mensaje = "Se preguntara al cliente si desea esperar " . $estado;
            }


            return ['success'=> 1,
                'automatica' => $infoServicio->orden_automatica,
                'msj1' => $mensaje,
                'tiempo' => $infoServicio->tiempo];
        }else{
            return ['success'=> 2];
        }
    }


    public function guardarTiempoOrden(Request $request){

        $rules = array(
            'id' => 'required',
            'estado' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){return ['success' => 0]; }

        if($p = Propietarios::where('id', $request->id)->first()){

            if(Servicios::where('id', $p->servicios_id)->first()){

                if($request->estado == 1){
                    Servicios::where('id', $p->servicios_id)->update(['orden_automatica' => 1, 'tiempo' => $request->minuto]);
                }else{
                    Servicios::where('id', $p->servicios_id)->update(['orden_automatica' => 0, 'tiempo' => $request->minuto]);
                }

                return ['success'=> 1];
            }else{
                return ['success'=> 2]; // servicio no encontrado
            }
        }else{
            return ['success'=> 2]; // propietario no encontrado
        }

    }

}
