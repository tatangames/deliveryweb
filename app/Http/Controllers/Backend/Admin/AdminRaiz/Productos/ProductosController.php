<?php

namespace App\Http\Controllers\Backend\Admin\AdminRaiz\Productos;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\ServiciosTipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductosController extends Controller
{
    // lista de productos
    public function index($id){
        // recibo id del servicio

        $dato = DB::table('servicios_tipo AS st')
            ->join('servicios AS s', 's.id', '=', 'st.servicios_id')
            ->select('s.nombre')
            ->where('st.id', $id)
            ->first();

        $nombre = $dato->nombre;

        return view('backend.admin.servicios.categorias.producto.index', compact('id', 'nombre'));
    }

    // tabla de productos
    public function tablaProductos($id){

        $producto = DB::table('producto AS p')
            ->join('servicios_tipo AS s', 's.id', '=', 'p.servicios_tipo_id')
            ->select('p.id', 'p.nombre', 'p.descripcion', 'p.posicion', 'p.precio', 'p.disponibilidad', 'p.activo')
            ->where('s.id', $id)
            ->orderBy('p.posicion', 'ASC')
            ->get();

        return view('backend.admin.servicios.categorias.producto.tabla.tablaproducto', compact('producto'));
    }

    // ver todos los productos
    public function indexTodos($id){

        // recibimos el id del servicio
        $dato = DB::table('servicios_tipo AS st')
            ->join('servicios AS s', 's.id', '=', 'st.servicios_id')
            ->select('s.nombre')
            ->where('st.servicios_id', $id)
            ->first();

        $nombre = $dato->nombre;

        return view('backend.admin.servicios.categorias.producto.todos.index', compact('id', 'nombre'));
    }

    public function tablaTodosLosProductos($id){
        $producto = DB::table('producto AS p')
            ->join('servicios_tipo AS s', 's.id', '=', 'p.servicios_tipo_id')
            ->select('p.id', 'p.nombre', 'p.descripcion', 'p.posicion', 'p.precio',
                'p.disponibilidad', 'p.activo', 'p.imagen', 'p.utiliza_imagen',  's.nombre AS categoria')
            ->where('s.servicios_id', $id)
            ->orderBy('p.nombre', 'ASC')
            ->get();

        return view('backend.admin.servicios.categorias.producto.todos.tabla.tablatodos', compact('producto'));
    }

    // nuevo producto
    public function nuevo(Request $request){

        $regla = array(
            'idcategoria' => 'required',
            'nombre' => 'required',
            'precio' => 'required',
            'cbdisponibilidad' => 'required',
            'cbactivo' => 'required',
            'cbnota' => 'required',
            'cbimagen' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

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

                $ca = new Producto();
                $ca->servicios_tipo_id = $request->idcategoria;
                $ca->nombre = $request->nombre;
                $ca->imagen = $nombreFoto;
                $ca->descripcion = $request->descripcion;
                $ca->precio = $request->precio;
                $ca->disponibilidad = $request->cbdisponibilidad;
                $ca->activo = $request->cbactivo;
                $ca->posicion = 1;
                $ca->utiliza_nota = $request->cbnota;
                $ca->nota = $request->nota;
                $ca->utiliza_imagen = $request->cbimagen;

                if($ca->save()){
                    return ['success' => 1];
                }else{
                    return ['success' => 2];
                }
            }else{
                return ['success' => 2];
            }

        }else{

            $ca = new Producto();
            $ca->servicios_tipo_id = $request->idcategoria;
            $ca->nombre = $request->nombre;
            $ca->imagen = null;
            $ca->descripcion = $request->descripcion;
            $ca->precio = $request->precio;
            $ca->disponibilidad = $request->cbdisponibilidad;
            $ca->activo = $request->cbactivo;
            $ca->posicion = 1;
            $ca->utiliza_nota = $request->cbnota;
            $ca->nota = $request->nota;
            $ca->utiliza_imagen = $request->cbimagen;

            if($ca->save()){
                return ['success' => 1];
            }else{
                return ['success' => 2];
            }

        }
    }

    // informacion del producto
    public function informacion(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        if($p = Producto::where('id', $request->id)->first()){

            // sacar todas las categorias, para poder cambiar el producto
            $idcategoria = $p->servicios_tipo_id;

            $idservicio = ServiciosTipo::where('id', $idcategoria)->pluck('servicios_id')->first();

            $categorias = ServiciosTipo::where('servicios_id', $idservicio)->get();

            return ['success' => 1, 'categoria' => $categorias, 'producto' => $p];
        }else{
            return ['success' => 2];
        }
    }

    // editar producto
    public function editar(Request $request){

            $rules = array(
                'id' => 'required',
                'nombre' => 'required',
                'selectcategoria' => 'required',
                'precio' => 'required',
                'cbdisponibilidad' => 'required',
                'cbactivo' => 'required',
                'cbutilizanota' => 'required',
                'cbimagen' => 'required',
            );

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()){return ['success' => 0]; }

            if($po = Producto::where('id', $request->id)->first()){

                if(empty($po->imagen)) {
                    if($request->cbimagen == 1) {
                        return ['success' => 1];
                    }
                }

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
                        $imagenOld = $po->imagen;

                        Producto::where('id', $request->id)->update([
                            'servicios_tipo_id' => $request->selectcategoria,
                            'nombre' => $request->nombre,
                            'imagen' => $nombreFoto,
                            'descripcion' => $request->descripcion,
                            'precio' => $request->precio,
                            'disponibilidad' => $request->cbdisponibilidad,
                            'activo' => $request->cbactivo,
                            'utiliza_nota' => $request->cbutilizanota,
                            'nota' => $request->nota,
                            'utiliza_imagen' => $request->cbimagen,
                        ]);

                        if(Storage::disk('imagenes')->exists($imagenOld)){
                            Storage::disk('imagenes')->delete($imagenOld);
                        }

                        return ['success' => 2];

                    }else{
                        return ['success' => 3];
                    }
                }else{
                    // solo guardar datos

                    Producto::where('id', $request->id)->update([
                        'servicios_tipo_id' => $request->selectcategoria,
                        'nombre' => $request->nombre,
                        'descripcion' => $request->descripcion,
                        'precio' => $request->precio,
                        'disponibilidad' => $request->cbdisponibilidad,
                        'activo' => $request->cbactivo,
                        'utiliza_nota' => $request->cbutilizanota,
                        'nota' => $request->nota,
                        'utiliza_imagen' => $request->cbimagen,
                    ]);

                    return ['success' => 2];
                }

            }else{
                return ['success' => 3];
            }
    }

    // ordenar producto
    public function ordenar(Request $request){

        $idproducto = 0;
        // sacar id del primer producto
        foreach ($request->order as $order) {

            $idproducto = $order['id'];
            break;
        }

        $idcategoria = Producto::where('id', $idproducto)->pluck('servicios_tipo_id')->first();

        // todos los productos que pertenecen a esta categoria
        $tasks = Producto::where('servicios_tipo_id', $idcategoria)->get();

        foreach ($tasks as $task) {
            $id = $task->id;

            foreach ($request->order as $order) {
                if ($order['id'] == $id) {
                    $task->update(['posicion' => $order['posicion']]);
                }
            }
        }
        return ['success' => 1];
    }


    public function indexMasFotos($id){

        $nombre = Producto::where('id', $id)->pluck('nombre')->first();

        return view('backend.paginas.servicios.listamultiplesfotos', compact('nombre', 'id'));
    }


    public function indexMasFotosTabla($id){
        $foto = MultiplesImagenes::where('producto_id', $id)->get();

        return view('backend.paginas.servicios.tablas.tablaproductoimagenes', compact('foto'));
    }

    public function indexMasVideo($id){

        $data = Producto::where('id', $id)->first();

        $nombre = $data->nombre;
        $video = $data->video_url;

        return view('backend.paginas.servicios.listaproductovideo', compact('nombre', 'video', 'id'));
    }


    // nueva foto extra de producto
    public function nuevaFotoExtra(Request $request){
        if($request->isMethod('post')){

            $regla = array(
                'id' => 'required'
            );

            $mensaje = array(
                'id.required' => 'id producto es requerido'

            );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails())
            {
                return [
                    'success' => 0,
                    'message' => $validar->errors()->all()
                ];
            }

            // validar imagen
            if($request->hasFile('imagen')){

                // validaciones para los datos
                $regla2 = array(
                    'imagen' => 'required|image',
                );

                $mensaje2 = array(
                    'imagen.required' => 'La imagen es requerida',
                    'imagen.image' => 'El archivo debe ser una imagen',
                );

                $validar2 = Validator::make($request->all(), $regla2, $mensaje2 );

                if ( $validar2->fails())
                {
                    return ['success' => 1]; // imagen no valida
                }
            }

            $cadena = Str::random(15);
            $tiempo = microtime();
            $union = $cadena.$tiempo;
            $nombre = str_replace(' ', '_', $union);

            $extension = '.'.$request->imagen->getClientOriginalExtension();
            $nombreFoto = $nombre.strtolower($extension);
            $avatar = $request->file('imagen');

            if(MultiplesImagenes::where('imagen_extra', $nombreFoto)->first()){
                // este nombre de imagen ya existe, reintentar subir
                return ['success' => 3];
            }

            $upload = Storage::disk('productos')->put($nombreFoto, \File::get($avatar));



            if($upload){

                $ca = new MultiplesImagenes();
                $ca->producto_id = $request->id;
                $ca->imagen_extra = $nombreFoto;
                $ca->posicion = 1;

                if($ca->save()){
                    return ['success' => 2]; // guardado
                }else{
                    return ['success' => 3]; // error la guardar
                }
            }else{
                return ['success' => 4]; // error al guardar imagen
            }
        }
    }


    // borrar imagen de producto extra
    public function borrarImagenExtra(Request $request){

        if($request->isMethod('post')){

            $regla = array(
                'id' => 'required'
            );

            $mensaje = array(
                'id.required' => 'id producto es requerido'

            );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails())
            {
                return [
                    'success' => 0,
                    'message' => $validar->errors()->all()
                ];
            }

            if($mi = MultiplesImagenes::where('id', $request->id)->first()){

                if(Storage::disk('productos')->exists($mi->imagen_extra)){
                    Storage::disk('productos')->delete($mi->imagen_extra);
                }

                $idpro = $mi->producto_id;

                MultiplesImagenes::where('id', $request->id)->delete();

                // buscar si hay imagenes extra, sino desactivar
                if(MultiplesImagenes::where('producto_id', $request->id)->first()){
                    // si hay aun
                }else{
                    Producto::where('id', $idpro)->update([
                        'utiliza_imagen_extra' => 0
                    ]);
                }

                return ['success' => 1];
            }
        }
    }

    public function editarProductoImagenExtra(Request $request){

        if($request->isMethod('post')){
            $rules = array(
                'id' => 'required'
            );

            $messages = array(
                'id.required' => 'El id es requerido'
            );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() )
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            // por lo menos hay 1 foto extra
            if(MultiplesImagenes::where('producto_id', $request->id)->first()){
                Producto::where('id', $request->id)->update([
                    'utiliza_imagen_extra' => $request->check
                ]);

                return ['success' => 1];

            }else{
                return ['success' => 2];
            }
        }
    }


    public function ordenarImagenesExtra(Request $request){

        foreach ($request->order as $order) {

            $tipoid = $order['id'];

            DB::table('zonas_publicidad')
                ->where('publicidad_id', $tipoid)
                ->update(['posicion' => $order['posicion']]); // actualizar posicion
        }

        return ['success' => 1];
    }


    public function agregarVideoProducto(Request $request){

        if($request->isMethod('post')){

            $regla = array(
                'id' => 'required'
            );

            $mensaje = array(
                'id.required' => 'id producto es requerido'

            );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails())
            {
                return [
                    'success' => 0,
                    'message' => $validar->errors()->all()
                ];
            }

            $cadena = Str::random(15);
            $tiempo = microtime();
            $union = $cadena.$tiempo;
            $nombre = str_replace(' ', '_', $union);

            $extension = '.'.$request->video->getClientOriginalExtension();
            $nombreVideo = $nombre.strtolower($extension);
            $avatar = $request->file('video');

            if(Producto::where('video_url', $nombreVideo)->first()){
                // este nombre de video ya existe, reintentar subir
                return ['success' => 1];
            }

            $upload = Storage::disk('productos')->put($nombreVideo, \File::get($avatar));

            if($upload){

                // obtener url anterior, sino habia nada, pues no eliminara nada
                $dd = Producto::where('id', $request->id)->pluck('video_url')->first();

                if(Storage::disk('productos')->exists($dd)){
                    Storage::disk('productos')->delete($dd);
                }

                // agregar nueva url
                Producto::where('id', $request->id)->update([
                    'video_url' => $nombreVideo,
                ]);

                return ['success' => 2];

            }else{
                return ['success' => 3]; // error al guardar
            }
        }
    }


    public function borrarVideoProducto(Request $request){

        if($request->isMethod('post')){

            $regla = array(
                'id' => 'required'
            );

            $mensaje = array(
                'id.required' => 'id producto es requerido'

            );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails())
            {
                return [
                    'success' => 0,
                    'message' => $validar->errors()->all()
                ];
            }

            if($p = Producto::where('id', $request->id)->first()){

                if(Storage::disk('productos')->exists($p->video_url)){
                    Storage::disk('productos')->delete($p->video_url);
                }

                Producto::where('id', $p->id)->update([
                    'utiliza_video' => 0,
                    'video_url' => ""
                ]);

                return ['success' => 1];
            }
        }
    }


    public function editarProductoVideo(Request $request){

        if($request->isMethod('post')){
            $rules = array(
                'id' => 'required'
            );

            $messages = array(
                'id.required' => 'El id es requerido'
            );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() )
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            // por lo menos hay 1 foto extra
            if($p = Producto::where('id', $request->id)->first()){

                if($p->video_url == null || $p->video_url == ""){
                    return ['success' => 1];
                }

                Producto::where('id', $request->id)->update([
                    'utiliza_video' => $request->check
                ]);

                return ['success' => 2];

            }else{
                return ['success' => 3];
            }
        }

    }
}
