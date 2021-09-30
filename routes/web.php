<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\Login\LoginController;
use App\Http\Controllers\Backend\Admin\AdminRaiz\Permisos\PermisosController;
use App\Http\Controllers\Backend\Admin\AdminRaiz\Roles\ControlController;
use App\Http\Controllers\Backend\Admin\AdminRaiz\Roles\RolesController;
use App\Http\Controllers\Backend\Admin\AdminRaiz\Perfil\PerfilController;
use App\Http\Controllers\Backend\Admin\AdminRaiz\Estadistica\EstadisticaController;
use App\Http\Controllers\Backend\Admin\AdminRaiz\Zona\ZonaController;
use App\Http\Controllers\Backend\Admin\AdminRaiz\Tipos\TiposController;
use App\Http\Controllers\Backend\Admin\AdminRaiz\Tipos\TiposServicioController;
use App\Http\Controllers\Backend\Admin\AdminRaiz\Tipos\TiposServicioZonaController;
use App\Http\Controllers\Backend\Admin\AdminRaiz\Servicios\ServiciosController;
use App\Http\Controllers\Backend\Admin\AdminRaiz\Categorias\CategoriasController;
use App\Http\Controllers\Backend\Admin\AdminRaiz\Productos\ProductosController;
use App\Http\Controllers\Backend\Admin\AdminRaiz\ZonaServicios\ZonaServiciosController;
use App\Http\Controllers\Backend\Admin\AdminRaiz\Propietarios\PropietariosController;
use App\Http\Controllers\Backend\Admin\AdminRaiz\Motoristas\MotoristasController;
use App\Http\Controllers\Backend\Admin\AdminRaiz\Clientes\ClientesController;
use App\Http\Controllers\Backend\Admin\AdminRaiz\Extras\ExtrasController;
use App\Http\Controllers\Backend\Admin\AdminRaiz\Cupones\CuponesController;
use App\Http\Controllers\Backend\Admin\AdminRaiz\Monedero\MonederoController;
use App\Http\Controllers\Backend\Admin\AdminRaiz\Ordenes\OrdenesController;
use App\Http\Controllers\Backend\Admin\AdminRaiz\Reportes\ReportesController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [LoginController::class,'index'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');

// --- CONTROL WEB ---
Route::get('/panel', [ControlController::class,'indexRedireccionamiento'])->name('admin.panel');

    // **** --- ADMINISTRADOR --- ****

    // --- ROLES ---
    Route::get('/admin/roles/index', [RolesController::class,'index'])->name('admin.roles.index');
    Route::get('/admin/roles/tabla', [RolesController::class,'tablaRoles']);
    Route::get('/admin/roles/lista/permisos/{id}', [RolesController::class,'vistaPermisos']);
    Route::get('/admin/roles/permisos/tabla/{id}', [RolesController::class,'tablaRolesPermisos']);
    Route::post('/admin/roles/permiso/borrar', [RolesController::class, 'borrarPermiso']);
    Route::post('/admin/roles/permiso/agregar', [RolesController::class, 'agregarPermiso']);
    Route::get('/admin/roles/permisos/lista', [RolesController::class,'listaTodosPermisos']);
    Route::get('/admin/roles/permisos-todos/tabla', [RolesController::class,'tablaTodosPermisos']);
    Route::post('/admin/roles/borrar-global', [RolesController::class, 'borrarRolGlobal']);

    // --- PERMISOS ---
    Route::get('/admin/permisos/index', [PermisosController::class,'index'])->name('admin.permisos.index');
    Route::get('/admin/permisos/tabla', [PermisosController::class,'tablaUsuarios']);
    Route::post('/admin/permisos/nuevo-usuario', [PermisosController::class, 'nuevoUsuario']);
    Route::post('/admin/permisos/info-usuario', [PermisosController::class, 'infoUsuario']);
    Route::post('/admin/permisos/editar-usuario', [PermisosController::class, 'editarUsuario']);
    Route::post('/admin/permisos/nuevo-rol', [PermisosController::class, 'nuevoRol']);
    Route::post('/admin/permisos/extra-nuevo', [PermisosController::class, 'nuevoPermisoExtra']);
    Route::post('/admin/permisos/extra-borrar', [PermisosController::class, 'borrarPermisoGlobal']);

    // --- ESTADISTICAS ---
    Route::get('/admin/estadisticas/index', [EstadisticaController::class,'index'])->name('index.estadisticas');

    // --- PERFIL ---
    Route::get('/admin/editar-perfil/index', [PerfilController::class,'index'])->name('admin.perfil');
    Route::post('/admin/editar-perfil/actualizar', [PerfilController::class, 'editarUsuario']);

    // --- ZONAS ---
    Route::get('/admin/zona/mapa/zona', [ZonaController::class,'index'])->name('index.zonas');
    Route::get('/admin/zona/tablas/zona', [ZonaController::class,'tablaZonas']);
    Route::post('/zona/nueva-zona', [ZonaController::class,'nuevaZona']);
    Route::post('/zona/informacion-zona', [ZonaController::class,'informacionZona']);
    Route::post('/zona/editar-zona', [ZonaController::class,'editarZona']);
    Route::get('admin/zona/ver-mapa/{id}', [ZonaController::class,'verMapa']);
    Route::post('/zona/actualizar-marcados',[ZonaController::class,'actualizarGlobalmente']);

    // --- POLIGONO ---
    Route::get('/admin/zona/poligono/{id}', [ZonaController::class,'indexPoligono']);
    Route::post('/zona/poligono/listado-nuevo', [ZonaController::class,'nuevoPoligono']);
    Route::post('/zona/poligono/borrar', [ZonaController::class,'borrarPoligonos']);

    // --- TIPOS ---
    Route::get('/admin/tipos/lista-tipos', [TiposController::class,'index'])->name('index.tipos');
    Route::get('/admin/tipos/tablas/lista-tipos', [TiposController::class,'tablaTipos']);
    Route::post('/tipos/nuevo', [TiposController::class,'nuevoTipo']);
    Route::post('/tipos/informacion', [TiposController::class,'informacionTipos']);
    Route::post('/tipos/editar-tipos', [TiposController::class,'editarTipos']);

    // --- TIPOS SERVICIO ---
    Route::get('/admin/tiposervicio/lista-tipo-servicio', [TiposServicioController::class,'index'])->name('index.tipos.servicio');
    Route::get('/admin/tiposervicio/tablas/lista-tipo-servicio', [TiposServicioController::class,'tablaTiposServicio']);
    Route::post('/tiposervicio/nuevo',[TiposServicioController::class,'nuevoTipoServicio']);
    Route::post('/tiposervicio/informacion',[TiposServicioController::class,'informacionTipoServicio']);
    Route::post('/tiposervicio/editar-tipo',[TiposServicioController::class,'editarTipoServicio']);

    // --- TIPOS SERVICIO ZONA ---
    Route::get('/admin/tiposerviciozona/lista-tipo-servicio-zona', [TiposServicioZonaController::class,'index'])->name('index.tipos.servicio.zona');
    Route::get('/admin/tiposerviciozona/tablas/lista-tipo-servicio-zona', [TiposServicioZonaController::class,'tablaTipoServicioZona']);
    Route::post('/tiposerviciozona/buscar/servicio', [TiposServicioZonaController::class,'buscarServicio']);
    Route::post('/tiposerviciozona/nuevo', [TiposServicioZonaController::class,'nuevoTipoServicioZona']);
    Route::post('/tiposerviciozona/informacion', [TiposServicioZonaController::class,'informacionTipoZona']);
    Route::post('/tiposerviciozona/editar-tipo', [TiposServicioZonaController::class,'editarTipo']);
    Route::post('/tiposerviciozona/actidesactivar/bloque-global', [TiposServicioZonaController::class,'activarDesactivarTipoServicio']);
    Route::get('/tiposerviciozona/bloqueposicion/{id}',  [TiposServicioZonaController::class,'indexBloqueFiltrado']);
    Route::get('/tiposerviciozona/bloqueposicion/tabla/{id}', [TiposServicioZonaController::class,'tablaBloqueFiltrado']);
    Route::post('/tiposerviciozona/ordenar/bloques', [TiposServicioZonaController::class,'ordenarBloques']);

    Route::get('/admin/tiposerviciozona/posiciones-globales', [TiposServicioZonaController::class,'indexglobal'])->name('index.tiposserviciozona.posicionglobal');
    Route::get('/admin/tiposerviciozona/tablas/tablatiposervicioglobal', [TiposServicioZonaController::class,'tablaGlobalTipos']);
    Route::post('/tiposerviciozona/ordenar-globalmente', [TiposServicioZonaController::class,'orderTipoServicioGlobalmente']);

    // --- LISTA DE SERVICIOS ---
    Route::get('/admin/servicios/lista', [ServiciosController::class,'index'])->name('index.lista.servicios');
    Route::get('/admin/servicios/tabla/lista', [ServiciosController::class,'tablaServicios']);
    Route::post('/servicios/nuevo', [ServiciosController::class,'nuevoServicio']);
    Route::post('/servicios/informacion/servicio', [ServiciosController::class,'informacionServicio']);
    Route::post('/servicios/editar-servicio', [ServiciosController::class,'editarServicio']);
    Route::post('/servicios/informacion-horario/servicio', [ServiciosController::class,'informacionHorario']);
    Route::post('/servicios/editar-horas', [ServiciosController::class,'editarHoras']);
    Route::get('admin/servicios/mapa/ubicacion/{id}', [ServiciosController::class,'servicioUbicacion']);

    // --- LISTA DE SERVICIOS - CATEGORIAS ---
    Route::get('/admin/categorias/{id}', [CategoriasController::class,'index']);
    Route::get('/admin/categorias/tablas/{id}', [CategoriasController::class,'tablaCategorias']);
    Route::post('/categorias/nuevo', [CategoriasController::class,'nuevaCategoria']);
    Route::post('/categorias/informacion', [CategoriasController::class,'informacion']);
    Route::post('/categorias/editar', [CategoriasController::class,'editar']);
    Route::post('/categorias/ordenar', [CategoriasController::class,'ordenar']);

    // --- LISTA DE SERVICIOS - PRODUCTOS ---
    Route::get('/admin/productos/{id}',  [ProductosController::class,'index']);
    Route::get('/admin/productos/tablas/{id}',  [ProductosController::class,'tablaProductos']);
    Route::post('/productos/nuevo', [ProductosController::class,'nuevo']);
    Route::post('/productos/informacion', [ProductosController::class,'informacion']);
    Route::post('/productos/editar', [ProductosController::class,'editar']);
    Route::post('/productos/ordenar', [ProductosController::class,'ordenar']);
    Route::get('/admin/ver/todos/productos/{id}', [ProductosController::class,'indexTodos']);
    Route::get('/admin/ver/tabla/todos/productos/{id}', [ProductosController::class,'tablaTodosLosProductos']);

    // --- ZONA SERVICIOS ---
    Route::get('/admin/zonaservicios/lista', [ZonaServiciosController::class,'index'])->name('index.zonas.servicios');
    Route::get('/admin/zonaservicios/tabla/lista', [ZonaServiciosController::class,'tablaZonaServicios']);
    Route::post('/zonaservicios/nuevo',  [ZonaServiciosController::class,'nuevo']);
    Route::post('/zonaservicios/informacion',[ZonaServiciosController::class,'informacion']);
    Route::post('/zonaservicios/editar', [ZonaServiciosController::class,'editarServicio']);
    Route::get('/admin/zonaservicios/filtrado/{id}/{id1}', [ZonaServiciosController::class,'filtrado']);
    Route::get('/admin/zonaservicios/tabla/{id}/{id1}', [ZonaServiciosController::class,'tablaFiltrado']);
    Route::post('/zonaservicios/ordenar', [ZonaServiciosController::class,'ordenar']);
    Route::post('/zonaservicios/enviogratis', [ZonaServiciosController::class,'setearEnvioGratis']);

    Route::post('/activar/desactivar/zonaservicio', [ZonaServiciosController::class,'activarOCerrarServicioZona']);

    // cambiar precio de envio a todos los servicios por zona
    Route::post('/zonaservicios/nuevo-precio-varios', [ZonaServiciosController::class,'precioEnvioPorZona']);
    // aplicar nuevo cargo de envio por zona y servicios
    Route::post('/zonaservicios/modificar-min-gratis', [ZonaServiciosController::class,'aplicarNuevoCargoZonaServicio']);
    // cambiar precio ganancia motorista a todos los servicios por zona
    Route::post('/zonaservicios/nuevo-precio-ganancia', [ZonaServiciosController::class,'precioGananciaPorZona']);

    // --- PROPIETARIOS ---
    Route::get('/admin/propietarios/lista', [PropietariosController::class,'index'])->name('index.lista.propietarios');
    Route::get('/admin/propietarios/tabla/lista', [PropietariosController::class,'tablaPropietarios']);
    Route::post('/propietarios/nuevo', [PropietariosController::class,'nuevo']);
    Route::post('/propietarios/informacion', [PropietariosController::class,'informacion']);
    Route::post('/propietarios/editar', [PropietariosController::class,'editar']);

    // --- MOTORISTAS ---
    Route::get('/admin/motoristas/lista', [MotoristasController::class,'index'])->name('index.lista.motoristas');
    Route::get('/admin/motoristas/tabla/lista', [MotoristasController::class, 'tablaMotorista']);
    Route::post('/motoristas/nuevo', [MotoristasController::class, 'nuevo']);
    Route::post('/motoristas/informacion', [MotoristasController::class, 'informacion']);
    Route::post('/motoristas/editar', [MotoristasController::class, 'editar']);
    Route::post('/motoristas/promedio', [MotoristasController::class, 'promedio']);

    // --- MOTORISTAS ASIGNACIONES ---
    Route::get('/admin/motoristasservicio/lista', [MotoristasController::class, 'index2'])->name('index.lista.motoristas.asignados');
    Route::get('/admin/motoristasservicio/tabla/lista', [MotoristasController::class, 'tablaAsignacionMotorista']);
    Route::post('/motoristasservicio/borrar', [MotoristasController::class, 'borrar']);
    Route::post('/motoristasservicio/borrartodo', [MotoristasController::class, 'borrarTodo']);

    Route::post('/motoristasservicio/nuevo', [MotoristasController::class, 'nuevomotoservicio']);
    Route::post('/motoristasservicio/nuevo-global', [MotoristasController::class, 'nuevoGlobal']);

    // lista de motoristas ordenes
    Route::get('/admin/motoristas/ordenes/lista', [MotoristasController::class, 'indexMotoristaOrdenes'])->name('index.lista.motoristas.ordenes');
    Route::get('/admin/motoristas/ordenes/tabla/lista', [MotoristasController::class, 'tablaMotoristaOrdenes']);
    Route::post('/admin/motoristas/ordenes/editar', [MotoristasController::class, 'editarMotoristaOrden']);
    Route::post('/admin/motoristas/ordenes/info', [MotoristasController::class, 'informacionMotoristaOrden']);


    // --- CLIENTES ---
    // lista de cliente registrados hoy
    Route::get('/admin/cliente/lista-clientes-hoy', [ClientesController::class, 'indexRegistradosHoy'])->name('index.clientes.registrados.hoy');
    Route::get('/admin/cliente/tablas/cliente-hoy', [ClientesController::class, 'tablaRegistradosHoy']);

    // lista de intentos sms
    Route::get('/admin/cliente/intentos-sms', [ClientesController::class, 'indexIntentos'])->name('index.clientes.intentos.sms');
    Route::get('/admin/cliente/tabla/intentos-sms', [ClientesController::class, 'tablaindexIntentos']);

    // lista de numeros registrados en la app
    Route::get('/admin/cliente/numero/registrado', [ClientesController::class, 'indexNumeroRegistro'])->name('index.clientes.numero.registro');
    Route::get('/admin/cliente/tabla/numero-registrado', [ClientesController::class, 'tablaindexNumeroRegistro']);

    // lista de clientes
    Route::get('/admin/cliente/listado', [ClientesController::class, 'indexListaClientes'])->name('index.clientes.listado');
    Route::get('/admin/cliente/tabla/listado', [ClientesController::class, 'tablaindexListaClientes']);
    Route::post('/cliente/informacion', [ClientesController::class, 'informacionCliente']);
    Route::post('/cliente/actualizar/informacion', [ClientesController::class, 'actualizarCliente']);

    // lista direccion cliente
    Route::get('/admin/cliente/lista/direcciones/{id}', [ClientesController::class, 'indexListaDirecciones']);
    Route::get('/admin/cliente/lista/tabla-direcciones/{id}', [ClientesController::class, 'tablaIndexListaDirecciones']);
    Route::post('/cliente/informacion/direccion', [ClientesController::class, 'informacionClienteDireccion']);
    Route::post('/cliente/actualizar/direccion', [ClientesController::class, 'actualizarClienteDireccion']);

    // historial cliente
    Route::get('/admin/cliente/historial/{id}', [ClientesController::class, 'indexHistorial']);
    Route::get('/admin/cliente/historial/tabla/{id}', [ClientesController::class, 'tablaHistorial']);

    Route::get('/admin/cliente/historial-pro/{id}', [ClientesController::class, 'indexHistorialProducto']);
    Route::get('/admin/cliente/historial-pro/tabla/{id}', [ClientesController::class, 'tablaHistorialProducto']);




    // mapa cliente
    Route::get('/admin/cliente/mapa/pin/{id}', [ClientesController::class, 'indexMapaPin']);
    Route::get('/admin/cliente/mapa/real/{id}', [ClientesController::class, 'indexMapaReal']);

    // --- EXTRAS ----
    // listado de opciones
    Route::get('/admin/extras/listado', [ExtrasController::class, 'indexExtras'])->name('index.listado.opciones');
    Route::get('/admin/extras/tabla/listado', [ExtrasController::class, 'tablaIndexExtras']);
    Route::post('/extras/informacion', [ExtrasController::class, 'informacion']);
    Route::post('/extras/actualizar', [ExtrasController::class, 'actualizarInformacion']);

    // listado de etiquetas
    Route::get('/admin/etiquetas/lista', [ExtrasController::class,'indexEtiquetas'])->name('index.listado.etiquetas');
    Route::get('/admin/etiquetas/tabla/lista', [ExtrasController::class, 'tablaEtiquetas']);
    Route::post('/etiquetas/nuevo', [ExtrasController::class, 'nuevaEtiqueta']);
    Route::post('/etiquetas/informacion', [ExtrasController::class, 'informacionEtiqueta']);
    Route::post('/etiquetas/editar', [ExtrasController::class, 'editarEtiqueta']);

    // lista de etiquetas servicio
    Route::get('/admin/servicios/etiquetas/{id}', [ServiciosController::class,'indexServicioEtiquetas']);
    Route::get('/admin/servicios/tabla/etiquetas/{id}', [ServiciosController::class,'tablaIndexServicioEtiquetas']);
    Route::post('/servicios/etiqueta/borrar', [ServiciosController::class, 'eliminarEtiqueta']);
    Route::post('/servicios/etiqueta/guardar', [ServiciosController::class, 'guardarEtiqueta']);

    // lista de etiquetas ingresadas por el cliente al buscar
    Route::get('/admin/etiquetas/lista/cliente', [ExtrasController::class,'indexEtiquetasCliente'])->name('index.listado.etiquetas.cliente');
    Route::get('/admin/etiquetas/tabla/lista/cliente', [ExtrasController::class, 'tablaEtiquetasCliente']);




        // --- CUPONES ----
    Route::get('/admin/cliente/lista/tipo-cupones', [CuponesController::class, 'indexTipoCupon'])->name('index.listado.tipos.de.cupones');
    Route::get('/admin/cliente/lista/tabla/tipo-cupones', [CuponesController::class, 'tablaIndexTipoCupon']);

    // listado de cupones
    Route::get('/admin/cliente/lista/cupones', [CuponesController::class, 'indexCupones'])->name('index.listado.cupones');
    Route::get('/admin/cliente/lista/tabla/cupones', [CuponesController::class, 'tablaIndexCupones']);
    Route::post('/cupones/nuevo', [CuponesController::class, 'nuevoCupon']);
    Route::post('/cupones/informacion', [CuponesController::class, 'informacionCupon']);
    Route::post('/cupones/editar', [CuponesController::class, 'editarCupones']);

    // asignacion de cupones a zonas
    Route::get('/admin/cupones/lista/zonas', [CuponesController::class, 'indexCuponZonas'])->name('index.listado.cupones.zonas');
    Route::get('/admin/cupones/lista/tabla/zonas', [CuponesController::class, 'tablaCuponZonas']);
    Route::post('/admin/cupones/zona/borrar', [CuponesController::class, 'borrarCuponZona']);
    Route::post('/admin/cupones/zona/borrar-global', [CuponesController::class, 'borrarCuponZonaGlobal']);
    Route::post('/admin/cupones/zona/agregar', [CuponesController::class, 'nuevaZonaCupon']);

    // asignacion de cupones a servicios
    Route::get('/admin/cupones/lista/servicios', [CuponesController::class, 'indexCuponServicios'])->name('index.listado.cupones.servicios');
    Route::get('/admin/cupones/lista/tabla/servicios', [CuponesController::class, 'tablaCuponServicios']);
    Route::post('/admin/cupones/servicio/borrar', [CuponesController::class, 'borrarCuponServicio']);
    Route::post('/admin/cupones/servicio/borrar-global', [CuponesController::class, 'borrarCuponServicioGlobal']);
    Route::post('/admin/cupones/servicio/agregar', [CuponesController::class, 'nuevaServicioCupon']);

    // lista de cupon asignacion envio
    Route::get('/admin/cupon/lista/envio', [CuponesController::class, 'indexCuponEnvio'])->name('index.listado.cupon.envio');
    Route::get('/admin/cupon/lista/tabla/envio', [CuponesController::class, 'tablaCuponEnvio']);
    Route::post('/admin/cupon/lista/envio-nuevo', [CuponesController::class, 'registrarCuponEnvio']);
    Route::post('/admin/cupon/lista/envio-informacion', [CuponesController::class, 'informacionCuponEnvio']);
    Route::post('/admin/cupon/lista/envio-editar', [CuponesController::class, 'editarCuponEnvio']);
    Route::post('/admin/cupon/lista/envio-borrar', [CuponesController::class, 'borrarCuponEnvio']);

    // lista de cupon asignacion producto
    Route::get('/admin/cupon/lista/producto', [CuponesController::class, 'indexCuponProducto'])->name('index.listado.cupon.producto');
    Route::get('/admin/cupon/lista/tabla/producto', [CuponesController::class, 'tablaCuponProducto']);
    Route::post('/admin/cupon/lista/producto-nuevo', [CuponesController::class, 'registrarCuponProducto']);
    Route::post('/admin/cupon/lista/producto-informacion', [CuponesController::class, 'informacionCuponProducto']);
    Route::post('/admin/cupon/lista/producto-editar', [CuponesController::class, 'editarCuponProducto']);
    Route::post('/admin/cupon/lista/producto-borrar', [CuponesController::class, 'borrarCuponProducto']);

    // --- MONEDERO ---
    Route::get('/admin/monedero/lista/index', [MonederoController::class, 'index'])->name('index.listado.monedero');
    Route::get('/admin/monedero/lista/tabla', [MonederoController::class, 'tablaIndex']);
    Route::post('/admin/monedero/informacion', [MonederoController::class, 'informacionMonedero']);
    Route::post('/admin/monedero/revisar', [MonederoController::class, 'revisarMonedero']);

    // lista de monedero devuelto
    Route::get('/admin/monedero/devuelto/lista/index', [MonederoController::class, 'indexMonederoDevuelto'])->name('index.listado.monedero.devuelto');
    Route::get('/admin/monedero/devuelto/lista/tabla', [MonederoController::class, 'tablaIndexMonederoDevuelto']);

    // --- ORDENES ---
    Route::get('/admin/ordenes/lista/index', [OrdenesController::class, 'indexOrdenes'])->name('index.listado.ordenes');
    Route::get('/admin/ordenes/lista/tabla', [OrdenesController::class, 'tablaIndexOrdenes']);
    Route::post('/admin/ordenes/lista/informacion', [OrdenesController::class, 'informacionOrdenes']);
    Route::post('/admin/ordenes/lista/informacion-cliente', [OrdenesController::class, 'informacionOrdenesCliente']);
    Route::get('/admin/orden/mapa/pin/{id}', [OrdenesController::class, 'indexMapaPin']);
    Route::get('/admin/orden/mapa/real/{id}', [OrdenesController::class, 'indexMapaReal']);

    // ordenes cupones
    Route::get('/admin/ordenes/cupon/lista/index', [OrdenesController::class, 'indexOrdenesCupon'])->name('index.listado.ordenes.cupon');
    Route::get('/admin/ordenes/cupon/lista/tabla', [OrdenesController::class, 'tablaIndexOrdenesCupon']);

    // lista de comentarios a las ordenes
    Route::get('/admin/ordenes/comentarios/index', [OrdenesController::class, 'indexComentarios'])->name('index.lista.ordenes.comentarios');
    Route::get('/admin/ordenes/comentarios/tabla', [OrdenesController::class, 'tablaComentarios']);


    // ---- REPORTES ----
    Route::get('/admin/lista/reportes', [ReportesController::class, 'index'])->name('index.lista.reportes');

    Route::get('/lista/reportes/lista-ordenes/{fecha1}/{fecha2}/{estado}', [ReportesController::class, 'reporteListaOrdenes']);
    Route::get('/lista/reportes/lista-ordenes-servicio/{fecha1}/{fecha2}/{id}', [ReportesController::class, 'reporteListaOrdenesServicio']);



