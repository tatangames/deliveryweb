<?php

namespace App\Http\Controllers\Backend\Admin\AdminRaiz\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Etiquetas;
use App\Models\EtiquetasServicio;
use App\Models\HorarioServicio;
use App\Models\Servicios;
use App\Models\TiposServicio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ServiciosController extends Controller
{
    public function index(){
        //$tiposervicio = TiposServicio::whereNotIn('id', [3,4,19])->get(['id','nombre']);
        $tiposervicio = TiposServicio::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.servicios.index', compact('tiposervicio'));
    }

    // tabla para ver codigo temporales
    public function tablaServicios(){

        $servicio = DB::table('servicios AS s')
            ->join('tipos_servicio AS ts', 'ts.id', '=', 's.tipos_servicio_id')
            ->select('s.id','s.nombre', 's.descripcion', 's.imagen',
                's.cerrado_emergencia', 's.activo', 's.identificador', 'ts.nombre AS nombreServicio')
            ->get();

        return view('backend.admin.servicios.tabla.tablaservicios', compact('servicio'));
    }

    //nuevo servicio
    public function nuevoServicio(Request $request){

            $regla = array(

                'comision' => 'required',
                'nombre' => 'required',
                'identificador' => 'required',
                'descripcion' => 'required',
                'tiposervicio' => 'required',
                'telefono' => 'required',
                'latitud' => 'required',
                'longitud' => 'required',
                'direccion' => 'required',
                'tipovista' => 'required',

                'horalunes1' => 'required',
                'horalunes2' => 'required',
                'horalunes3' => 'required',
                'horalunes4' => 'required',
                'cblunessegunda' => 'required',
                'cbcerradolunes' => 'required',

                'horamartes1' => 'required',
                'horamartes2' => 'required',
                'horamartes3' => 'required',
                'horamartes4' => 'required',
                'cbmartessegunda' => 'required',
                'cbcerradomartes' => 'required',

                'horamiercoles1' => 'required',
                'horamiercoles2' => 'required',
                'horamiercoles3' => 'required',
                'horamiercoles4' => 'required',
                'cbmiercolessegunda' => 'required',
                'cbcerradomiercoles' => 'required',

                'horajueves1' => 'required',
                'horajueves2' => 'required',
                'horajueves3' => 'required',
                'horajueves4' => 'required',
                'cbjuevessegunda' => 'required',
                'cbcerradojueves' => 'required',

                'horaviernes1' => 'required',
                'horaviernes2' => 'required',
                'horaviernes3' => 'required',
                'horaviernes4' => 'required',
                'cbviernessegunda' => 'required',
                'cbcerradoviernes' => 'required',

                'horasabado1' => 'required',
                'horasabado2' => 'required',
                'horasabado3' => 'required',
                'horasabado4' => 'required',
                'cbsabadosegunda' => 'required',
                'cbcerradosabado' => 'required',

                'horadomingo1' => 'required',
                'horadomingo2' => 'required',
                'horadomingo3' => 'required',
                'horadomingo4' => 'required',
                'cbdomingosegunda' => 'required',
                'cbcerradodomingo' => 'required',
            );

            $validar = Validator::make($request->all(), $regla);

            if ( $validar->fails()){return ['success' => 0];}

            if(Servicios::where('identificador', $request->identificador)->first()){
                return ['success' => 1];
            }

            DB::beginTransaction();

            try {

                $cadena = Str::random(15);
                $tiempo = microtime();
                $union = $cadena.$tiempo;
                $nombre = str_replace(' ', '_', $union);

                $extension = '.'.$request->logo->getClientOriginalExtension();
                $nombreFoto = $nombre.strtolower($extension);
                $avatar = $request->file('logo');
                $upload = Storage::disk('imagenes')->put($nombreFoto, \File::get($avatar));

                $cadena2 = Str::random(15);
                $tiempo2 = microtime();
                $union2 = $cadena2.$tiempo2;
                $nombre2 = str_replace(' ', '_', $union2);

                $extension2 = '.'.$request->imagen->getClientOriginalExtension();
                $nombreFoto2 = $nombre2.strtolower($extension2);
                $avatar2 = $request->file('imagen');
                $upload2 = Storage::disk('imagenes')->put($nombreFoto2, \File::get($avatar2));

                if($upload && $upload2){

                    $fecha = Carbon::now('America/El_Salvador');

                    $tipo = new Servicios();

                    $tipo->tipos_servicio_id = $request->tiposervicio;
                    $tipo->nombre = $request->nombre;
                    $tipo->identificador = $request->identificador;
                    $tipo->descripcion = $request->descripcion;
                    $tipo->logo = $nombreFoto;
                    $tipo->imagen = $nombreFoto2;
                    $tipo->cerrado_emergencia = 0;
                    $tipo->fecha = $fecha;
                    $tipo->activo = 0;
                    $tipo->telefono = $request->telefono;
                    $tipo->latitud = $request->latitud;
                    $tipo->longitud = $request->longitud;
                    $tipo->direccion = $request->direccion;
                    $tipo->tipo_vista = $request->tipovista;
                    $tipo->orden_automatica = 0;
                    $tipo->tiempo = 10;
                    $tipo->comision = $request->comision;
                    $tipo->privado = 0;
                    $tipo->save();

                    $idservicio = $tipo->id;

                    $hora1 = new HorarioServicio();
                    $hora1->hora1 = $request->horalunes1;
                    $hora1->hora2 = $request->horalunes2;
                    $hora1->hora3 = $request->horalunes3;
                    $hora1->hora4 = $request->horalunes4;
                    $hora1->dia = 2;
                    $hora1->servicios_id = $idservicio;
                    $hora1->segunda_hora = $request->cblunessegunda;
                    $hora1->cerrado = $request->cbcerradolunes;
                    $hora1->save();

                    $hora2 = new HorarioServicio();
                    $hora2->hora1 = $request->horamartes1;
                    $hora2->hora2 = $request->horamartes2;
                    $hora2->hora3 = $request->horamartes3;
                    $hora2->hora4 = $request->horamartes4;
                    $hora2->dia = 3;
                    $hora2->servicios_id = $idservicio;
                    $hora2->segunda_hora = $request->cbmartessegunda;
                    $hora2->cerrado = $request->cbcerradomartes;
                    $hora2->save();

                    $hora3 = new HorarioServicio();
                    $hora3->hora1 = $request->horamiercoles1;
                    $hora3->hora2 = $request->horamiercoles2;
                    $hora3->hora3 = $request->horamiercoles3;
                    $hora3->hora4 = $request->horamiercoles4;
                    $hora3->dia = 4;
                    $hora3->servicios_id = $idservicio;
                    $hora3->segunda_hora = $request->cbmiercolessegunda;
                    $hora3->cerrado = $request->cbcerradomiercoles;
                    $hora3->save();

                    $hora4 = new HorarioServicio();
                    $hora4->hora1 = $request->horajueves1;
                    $hora4->hora2 = $request->horajueves2;
                    $hora4->hora3 = $request->horajueves3;
                    $hora4->hora4 = $request->horajueves4;
                    $hora4->dia = 5;
                    $hora4->servicios_id = $idservicio;
                    $hora4->segunda_hora = $request->cbjuevessegunda;
                    $hora4->cerrado = $request->cbcerradojueves;
                    $hora4->save();

                    $hora5 = new HorarioServicio();
                    $hora5->hora1 = $request->horaviernes1;
                    $hora5->hora2 = $request->horaviernes2;
                    $hora5->hora3 = $request->horaviernes3;
                    $hora5->hora4 = $request->horaviernes4;
                    $hora5->dia = 6;
                    $hora5->servicios_id = $idservicio;
                    $hora5->segunda_hora = $request->cbviernessegunda;
                    $hora5->cerrado = $request->cbcerradoviernes;
                    $hora5->save();

                    $hora6 = new HorarioServicio();
                    $hora6->hora1 = $request->horasabado1;
                    $hora6->hora2 = $request->horasabado2;
                    $hora6->hora3 = $request->horasabado3;
                    $hora6->hora4 = $request->horasabado4;
                    $hora6->dia = 7;
                    $hora6->servicios_id = $idservicio;
                    $hora6->segunda_hora = $request->cbsabadosegunda;
                    $hora6->cerrado = $request->cbcerradosabado;
                    $hora6->save();

                    $hora7 = new HorarioServicio();
                    $hora7->hora1 = $request->horadomingo1;
                    $hora7->hora2 = $request->horadomingo2;
                    $hora7->hora3 = $request->horadomingo3;
                    $hora7->hora4 = $request->horadomingo4;
                    $hora7->dia = 1;
                    $hora7->servicios_id = $idservicio;
                    $hora7->segunda_hora = $request->cbdomingosegunda;
                    $hora7->cerrado = $request->cbcerradodomingo;
                    $hora7->save();

                    DB::commit();

                    return ['success' => 2];
                }else{
                    return ['success' => 3];
                }

            } catch(\Throwable $e){
                DB::rollback();
                return ['success' => 3];
            }
    }

    // informacion del servicio
    public function informacionServicio(Request $request){
        $rules = array(
            'id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){return ['success' => 0];}

        if($servicio = Servicios::where('id', $request->id)->first()){

            $tipo = TiposServicio::orderBy('nombre')->get();

            return['success' => 1, 'servicio' => $servicio, 'tipo' => $tipo];
        }else{
            return['success' => 2];
        }
    }

    // informacion sobre horarios
    public function informacionHorario(Request $request){
        $rules = array(
            'id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0];}

        if(HorarioServicio::where('id', $request->id)->first()){

            $horario = HorarioServicio::where('servicios_id', $request->id)->get();

            return['success' => 1, 'horario' => $horario];
        }else{
            return['success' => 2];
        }
    }

    // editar servicio
    public function editarServicio(Request $request){

            $rules = array(
                'id' => 'required',
                'comision' => 'required',
                'nombre' => 'required',
                'identificador' => 'required',
                'descripcion' => 'required',
                'tiposervicio' => 'required',
                'telefono' => 'required',
                'latitud' => 'required',
                'longitud' => 'required',
                'direccion' => 'required',
                'tipovista' => 'required',
                'cbcerradoemergencia' => 'required',
                'cbactivo' => 'required',
                'cbprivado' => 'required'
            );

            $validator = Validator::make($request->all(), $rules);

            if ( $validator->fails()){return ['success' => 0];}

            if(Servicios::where('id', '!=', $request->id)->where('identificador', $request->identificador)->first()){
                return ['success' => 1];
            }

            if($serviDatos = Servicios::where('id', $request->id)->first()){

                DB::beginTransaction();

                try {

                    if($request->hasFile('imagen')){

                        $cadena = Str::random(15);
                        $tiempo = microtime();
                        $union = $cadena.$tiempo;
                        $nombre = str_replace(' ', '_', $union);

                        $extension = '.'.$request->imagen->getClientOriginalExtension();
                        $nombreFoto = $nombre.strtolower($extension);
                        $avatar = $request->file('imagen');
                        $upload = Storage::disk('imagenes')->put($nombreFoto, \File::get($avatar));

                        if($upload){
                            $imagenOld = $serviDatos->imagen;

                            Servicios::where('id', $request->id)->update(['imagen' => $nombreFoto]);

                            if(Storage::disk('imagenes')->exists($imagenOld)){
                                Storage::disk('imagenes')->delete($imagenOld);
                            }
                        }else {
                            return ['success' => 0];
                        }
                    }

                    if($request->hasFile('logo')){

                        $cadena2 = Str::random(15);
                        $tiempo2 = microtime();
                        $union2 = $cadena2.$tiempo2;
                        $nombre2 = str_replace(' ', '_', $union2);

                        $extension2 = '.'.$request->logo->getClientOriginalExtension();
                        $nombreFoto2 = $nombre2.strtolower($extension2);
                        $avatar2 = $request->file('logo');
                        $upload2 = Storage::disk('imagenes')->put($nombreFoto2, \File::get($avatar2));

                        if($upload2){
                            $imagenOld = $serviDatos->logo;

                            Servicios::where('id', $request->id)->update(['logo' => $nombreFoto2]);

                            if(Storage::disk('imagenes')->exists($imagenOld)){
                                Storage::disk('imagenes')->delete($imagenOld);
                            }
                        }else {
                            return ['success' => 0];
                        }
                    }

                    Servicios::where('id', $request->id)->update([
                        'nombre' => $request->nombre,
                        'comision' => $request->comision,
                        'identificador' => $request->identificador,
                        'descripcion' => $request->descripcion,
                        'cerrado_emergencia' => $request->cbcerradoemergencia,
                        'activo' => $request->cbactivo,
                        'tipos_servicio_id' => $request->tiposervicio,
                        'telefono' => $request->telefono,
                        'latitud' => $request->latitud,
                        'longitud' => $request->longitud,
                        'direccion' => $request->direccion,
                        'tipo_vista' => $request->tipovista,
                        'mensaje_cerrado' => $request->mensajecerrado,
                        'privado' => $request->cbprivado
                    ]);

                    DB::commit();

                    return ['success' => 2];

                } catch(\Throwable $e){
                    DB::rollback();
                    return ['success' => 3];
                }

            }else{
                return ['success' => 3];
            }
    }

    // editar solamente horas
    public function editarHoras(Request $request){
            $rules = array(
                'id' => 'required',
                'horalunes1' => 'required',
                'horalunes2' => 'required',
                'horalunes3' => 'required',
                'horalunes4' => 'required',
                'cblunessegunda' => 'required',
                'cbcerradolunes' => 'required',

                'horamartes1' => 'required',
                'horamartes2' => 'required',
                'horamartes3' => 'required',
                'horamartes4' => 'required',
                'cbmartessegunda' => 'required',
                'cbcerradomartes' => 'required',

                'horamiercoles1' => 'required',
                'horamiercoles2' => 'required',
                'horamiercoles3' => 'required',
                'horamiercoles4' => 'required',
                'cbmiercolessegunda' => 'required',
                'cbcerradomiercoles' => 'required',

                'horajueves1' => 'required',
                'horajueves2' => 'required',
                'horajueves3' => 'required',
                'horajueves4' => 'required',
                'cbjuevessegunda' => 'required',
                'cbcerradojueves' => 'required',

                'horaviernes1' => 'required',
                'horaviernes2' => 'required',
                'horaviernes3' => 'required',
                'horaviernes4' => 'required',
                'cbviernessegunda' => 'required',
                'cbcerradoviernes' => 'required',

                'horasabado1' => 'required',
                'horasabado2' => 'required',
                'horasabado3' => 'required',
                'horasabado4' => 'required',
                'cbsabadosegunda' => 'required',
                'cbcerradosabado' => 'required',

                'horadomingo1' => 'required',
                'horadomingo2' => 'required',
                'horadomingo3' => 'required',
                'horadomingo4' => 'required',
                'cbdomingosegunda' => 'required',
                'cbcerradodomingo' => 'required',

            );

            $validator = Validator::make($request->all(), $rules);

            if ( $validator->fails() ) {return ['success' => 0];}

            DB::beginTransaction();

            try {

                HorarioServicio::where('servicios_id', $request->id)->where('dia', 1)->update(['hora1' => $request->horadomingo1, 'hora2' => $request->horadomingo2, 'hora3' => $request->horadomingo3, 'hora4' => $request->horadomingo4, 'segunda_hora' => $request->cbdomingosegunda, 'cerrado' => $request->cbcerradodomingo]);
                HorarioServicio::where('servicios_id', $request->id)->where('dia', 2)->update(['hora1' => $request->horalunes1, 'hora2' => $request->horalunes2, 'hora3' => $request->horalunes3, 'hora4' => $request->horalunes4, 'segunda_hora' => $request->cblunessegunda, 'cerrado' => $request->cbcerradolunes]);
                HorarioServicio::where('servicios_id', $request->id)->where('dia', 3)->update(['hora1' => $request->horamartes1, 'hora2' => $request->horamartes2, 'hora3' => $request->horamartes3, 'hora4' => $request->horamartes4, 'segunda_hora' => $request->cbmartessegunda, 'cerrado' => $request->cbcerradomartes]);
                HorarioServicio::where('servicios_id', $request->id)->where('dia', 4)->update(['hora1' => $request->horamiercoles1, 'hora2' => $request->horamiercoles2, 'hora3' => $request->horamiercoles3, 'hora4' => $request->horamiercoles4, 'segunda_hora' => $request->cbmiercolessegunda, 'cerrado' => $request->cbcerradomiercoles]);
                HorarioServicio::where('servicios_id', $request->id)->where('dia', 5)->update(['hora1' => $request->horajueves1, 'hora2' => $request->horajueves2, 'hora3' => $request->horajueves3, 'hora4' => $request->horajueves4, 'segunda_hora' => $request->cbjuevessegunda, 'cerrado' => $request->cbcerradojueves]);
                HorarioServicio::where('servicios_id', $request->id)->where('dia', 6)->update(['hora1' => $request->horaviernes1, 'hora2' => $request->horaviernes2, 'hora3' => $request->horaviernes3, 'hora4' => $request->horaviernes4, 'segunda_hora' => $request->cbviernessegunda, 'cerrado' => $request->cbcerradoviernes]);
                HorarioServicio::where('servicios_id', $request->id)->where('dia', 7)->update(['hora1' => $request->horasabado1, 'hora2' => $request->horasabado2, 'hora3' => $request->horasabado3, 'hora4' => $request->horasabado4, 'segunda_hora' => $request->cbsabadosegunda, 'cerrado' => $request->cbcerradosabado]);

                DB::commit();

                return ['success' => 1];

            } catch(\Throwable $e){
                DB::rollback();
                return ['success' => 2];
            }
    }

    // ubicacion del servicio
    public function servicioUbicacion($id){
        $datos = Servicios::where('id', $id)->select('latitud', 'longitud')->first();
        $latitud = $datos->latitud;
        $longitud = $datos->longitud;
        $api = "AIzaSyB-Iz6I6GtO09PaXGSQxZCjIibU_Li7yOM";
        return view('backend.admin.servicios.mapa.index', compact('latitud', 'longitud', 'api'));
    }


    public function indexServicioEtiquetas($id){

        $lista = Etiquetas::orderBy('nombre')->get();

        return view('backend.admin.servicios.etiquetas.index', compact('lista', 'id'));
    }

    public function tablaIndexServicioEtiquetas($id){

        $lista = DB::table('etiquetas_servicio AS es')
            ->join('etiquetas AS e', 'e.id', '=', 'es.etiquetas_id')
            ->select( 'es.id', 'es.servicios_id', 'e.nombre')
            ->where('es.servicios_id', $id)
            ->orderBy('e.nombre')
            ->get();

        return view('backend.admin.servicios.etiquetas.tabla.tablaetiquetas', compact('lista'));
    }

    public function eliminarEtiqueta(Request $request){

        $rules = array(
            'id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){return ['success' => 0];}

        if(EtiquetasServicio::where('id', $request->id)->first()){

            EtiquetasServicio::where('id', $request->id)->delete();
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function guardarEtiqueta(Request $request){

        $regla = array(

            'id' => 'required', // servicio
            'id2' => 'required', // id etiqueta
        );

        $validar = Validator::make($request->all(), $regla);

        if ( $validar->fails()){return ['success' => 0];}

        // ver sino existe este registro aun
        if(EtiquetasServicio::where('servicios_id', $request->id)
        ->where('etiquetas_id', $request->id2)->first()) {
            return ['success' => 1];
        }

        $e = new EtiquetasServicio();
        $e->servicios_id = $request->id;
        $e->etiquetas_id = $request->id2;

        if($e->save()){
            return ['success' => 2];
        }else{
            return ['success' => 3];
        }
    }

}
