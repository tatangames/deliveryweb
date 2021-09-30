<?php

namespace App\Http\Controllers\Backend\api\Perfil;

use App\Http\Controllers\Controller;
use App\Models\CarritoExtra;
use App\Models\CarritoTemporal;
use App\Models\Cliente;
use App\Models\DireccionCliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ApiPerfilController extends Controller
{

   public function nuevaDireccionCliente(Request $request){
       $reglaDatos = array(
           'clienteid' => 'required',
           'nombre' => 'required|max:100',
           'direccion' => 'required|max:400',
           'numero_casa' => 'max:30',
           'punto_referencia' => 'max:400',
           'zona_id' => 'required',
       );

       $validarDatos = Validator::make($request->all(), $reglaDatos);

       if ( $validarDatos->fails()){return ['success' => 0]; }

       if(Cliente::where('id', $request->clienteid)->first()){

           DB::beginTransaction();

           try {

               $di = new DireccionCliente();
               $di->zonas_id = $request->zona_id;
               $di->clientes_id = $request->clienteid;
               $di->nombre = $request->nombre;
               $di->direccion = $request->direccion;
               $di->numero_casa = $request->numero_casa;
               $di->punto_referencia = $request->punto_referencia;
               $di->seleccionado = 1;
               $di->latitud = $request->latitud;
               $di->longitud = $request->longitud;
               $di->latitudreal = $request->latitudreal;
               $di->longitudreal = $request->longitudreal;

               //direccion revisada por el administrador
               $di->revisado = 0;

               if($di->save()){

                   $id = $di->id;

                   try {
                       DireccionCliente::where('clientes_id', $request->clienteid)->where('id', '!=', $id)->update(['seleccionado' => 0]);

                       // BORRAR CARRITO DE COMPRAS, SI CAMBIO DE DIRECCION

                       if($tabla1 = CarritoTemporal::where('clientes_id', $request->clienteid)->first()){
                           CarritoExtra::where('carrito_temporal_id', $tabla1->id)->delete();
                           CarritoTemporal::where('clientes_id', $request->clienteid)->delete();
                       }

                       DB::commit();

                       return ['success' => 1];

                   }  catch (\Exception $ex) {
                       DB::rollback();

                       return ['success' => 2]; // error
                   }
               }else{
                   return ['success' => 2]; // error
               }

           } catch(\Throwable $e){
               DB::rollback();
               return ['success' => 2 ];
           }
       }else{
           return ['success' => 2];
       }
   }


   public function cambiarPasswordPerfil(Request $request){

       $rules = array(
           'id' => 'required',
           'password' => 'required|min:4|max:16',
       );

       $validator = Validator::make($request->all(), $rules);

       if ( $validator->fails()){return ['success' => 0]; }

       if(Cliente::where('id', $request->id)->first()){
           Cliente::where('id', $request->id)->update(['password' => Hash::make($request->password)]);
           return ['success' => 1];
       }else{
           return ['success' => 2];
       }
   }

   public function informacionPerfil(Request $request){

       $rules = array(
           'clienteid' => 'required'
       );

       $validator = Validator::make($request->all(), $rules);

       if ( $validator->fails()){return ['success' => 0]; }

       if($info = Cliente::where('id', $request->clienteid)->first()){

           return ['success' => 1, 'nombre' => $info->nombre,
               'correo' => $info->correo, 'imagen' => $info->imagen];
       }else{
           return ['success' => 2];
       }
   }

   public function editarPerfil(Request $request){
       $reglaDatos = array(
           'clienteid' => 'required',
           'nombre' => 'required',
       );

       $validator = Validator::make($request->all(), $reglaDatos);

       if ( $validator->fails()){return ['success' => 0]; }

       Log:info($request->all());

       if($request->correo != null){
           if(Cliente::where('correo', $request->correo)
               ->where('id', '!=', $request->clienteid)
               ->first()){
               return ['success' => 1];
           }
       }

       if($request->hasFile('image')){

           $cadena = Str::random(15);
           $tiempo = microtime();
           $union = $cadena . $tiempo;
           $nombre = str_replace(' ', '_', $union);

           // por defecto la extension sera .jpg
           $nombreFoto = $nombre . strtolower('.jpg');
           $avatar = $request->file('image');
           $upload = Storage::disk('imagenes')->put($nombreFoto, \File::get($avatar));

           if($upload){
               if($data = Cliente::where('id', $request->clienteid)->first()){
                   $imagenOld = $data->imagen;

                   Cliente::where('id', $request->clienteid)->update([
                       'imagen' => $nombreFoto,
                       'nombre' => $request->nombre,
                       'correo' => $request->correo]);

                   if(Storage::disk('imagenes')->exists($imagenOld)){
                       Storage::disk('imagenes')->delete($imagenOld);
                   }

                   return ['success' => 2];
               }else{
                   return ['success' => 3];
               }
           }else{
               return ['success' => 3];
           }
       }else{

           Cliente::where('id', $request->clienteid)->update([
               'nombre' => $request->nombre,
               'correo' => $request->correo]);

           return ['success' => 2];
       }
   }


    public function listadoDeDirecciones(Request $request){
        $rules = array(
            'clienteid' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){return ['success' => 0]; }

        if(Cliente::where('id', $request->clienteid)->first()){

            $direccion = DireccionCliente::where('clientes_id', $request->clienteid)->get();

            return ['success' => 1, 'direcciones' => $direccion];
        }else{
            return ['succcess'=> 2];
        }
    }

    public function eliminarDireccion(Request $request){

        $reglaDatos = array(
            'clienteid' => 'required',
            'dirid' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if ( $validarDatos->fails()){return ['success' => 0]; }

        if($infoDire = DireccionCliente::where('id', $request->dirid)
            ->where('clientes_id', $request->clienteid)->first()){

            DB::beginTransaction();

            try {

                $total = DireccionCliente::where('clientes_id', $request->clienteid)->count();

                if($total > 1){

                    // verificar si esta direccion era la que estaba seleccionada, para poner una aleatoria
                    $info = DireccionCliente::where('id', $infoDire->id)->first();

                    // borrar direccion
                    DireccionCliente::where('id', $infoDire->id)->delete();

                    // si era la seleccionada poner aleatoria, sino no hacer nada
                    if($info->seleccionado == 1){

                        // volver a buscar la primera linea y poner seleccionado
                        $datos = DireccionCliente::where('clientes_id', $request->clienteid)->first();
                        DireccionCliente::where('id', $datos->id)->update(['seleccionado' => 1]);
                    }

                    DB::commit();

                    return ['success' => 1];
                }else{
                    // no puede borrar la direccion
                    return ['success' => 2];
                }
            }catch(\Throwable $e){
                DB::rollback();
                return ['success' => 3];
            }
        }else{
            return ['success' => 3];
        }
    }

    public function seleccionarDireccion(Request $request){

        $reglaDatos = array(
            'dirid' => 'required',
            'clienteid' => 'required',
        );

        $validarDatos = Validator::make($request->all(), $reglaDatos);

        if ( $validarDatos->fails()){return ['success' => 0]; }

        if(Cliente::where('id', $request->clienteid)->first()){

            if(DireccionCliente::where('clientes_id', $request->clienteid)
                ->where('id', $request->dirid)->first()){

                DB::beginTransaction();

                try {

                    // setear a 0
                    DireccionCliente::where('clientes_id', $request->clienteid)->update(['seleccionado' => 0]);

                    // setear a 1 el id de la direccion que envia el usuario
                    DireccionCliente::where('id', $request->dirid)->update(['seleccionado' => 1]);

                    if($tabla1 = CarritoTemporal::where('clientes_id', $request->clienteid)->first()){
                        CarritoExtra::where('carrito_temporal_id', $tabla1->id)->delete();
                        CarritoTemporal::where('clientes_id', $request->clienteid)->delete();
                    }

                    DB::commit();

                    // direccion seleccionda
                    return ['success' => 1];

                }catch(\Throwable $e){
                    DB::rollback();
                    // error
                    return ['success' => 0];
                }

            }else{
                // cliente no encontrado
                return ['success' => 2];
            }
        }else{
            return ['success' => 2];
        }
    }


}
