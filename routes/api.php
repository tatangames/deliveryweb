<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Cliente
use App\Http\Controllers\Backend\Api\Cliente\ApiClienteController;
use App\Http\Controllers\Backend\Api\Cliente\ApiRegistroController;
use App\Http\Controllers\Backend\Api\Servicios\ApiZonasServiciosController;
use App\Http\Controllers\Backend\Api\Mapa\ApiMapaController;
use App\Http\Controllers\Backend\Api\Perfil\ApiPerfilController;
use App\Http\Controllers\Backend\Api\Servicios\ApiServiciosController;
use App\Http\Controllers\Backend\Api\Productos\ApiProductosController;
use App\Http\Controllers\Backend\Api\Carrito\ApiCarritoController;
use App\Http\Controllers\Backend\Api\MetodoPago\ApiMetodoPagoController;
use App\Http\Controllers\Backend\Api\Procesar\ProcesarOrdenClienteController;
use App\Http\Controllers\Backend\Api\Buscador\ApiBuscadorController;
use App\Http\Controllers\Backend\Api\Monedero\ApiMonederoController;
use App\Http\Controllers\Backend\Api\Ordenes\ApiOrdenesActivasController;

// Afiliados
use App\Http\Controllers\Backend\ApiAfiliado\Login\ApiLoginAfiliadoController;
use App\Http\Controllers\Backend\ApiAfiliado\Perfil\ApiPerfilAfiliadoController;
use App\Http\Controllers\Backend\ApiAfiliado\Categorias\ApiCategoriaAfiliadoController;
use App\Http\Controllers\Backend\ApiAfiliado\Ordenes\ApiOrdenesAfiliadoController;

// Motoristas
use App\Http\Controllers\Backend\ApiMotorista\Login\ApiLoginMotoristaController;
use App\Http\Controllers\Backend\ApiMotorista\Ordenes\ApiOrdenesMotoristaController;



// ** Api Login Controller
// verificar los numeros si es cliente nuevo o no
Route::post('verificar/telefono/cliente', [ApiClienteController::class, 'verificarNumero']);
// verifica el codigo para poder registrarse
Route::post('cliente/codigo/temporal-area', [ApiClienteController::class, 'verificarCodigoTemporal']);
// registro de cliente nuevo
Route::post('cliente/registro', [ApiRegistroController::class, 'registroCliente']);
// inicio de sesion
Route::post('cliente/login', [ApiClienteController::class, 'loginCliente']);
// enviar codigo sms para recuperar contraseña
Route::post('cliente/enviar/codigo-sms', [ApiClienteController::class, 'enviarCodigoSms']);
// verificar el codigo sms para cambiar la contraseña
Route::post('cliente/verificar/codigo-sms-password', [ApiClienteController::class, 'verificarCodigoSmsPassword']);
// actualizar la contraseña
Route::post('cliente/actualizar/password', [ApiClienteController::class, 'actualizarPasswordCliente']);
// reenvio de codigo sms
Route::post('cliente/enviar/codigo-sms-registro', [ApiClienteController::class, 'enviarCodigoSmsRegistro']);

// ** Api Zonas Servicios
// listado de servicios
Route::post('cliente/lista/zona-servicios', [ApiZonasServiciosController::class, 'listado']);



Route::get('listado/zonas/poligonos', [ApiMapaController::class, 'puntosZonaPoligonos']);

Route::post('cliente/nueva/direccion', [ApiPerfilController::class, 'nuevaDireccionCliente']);
Route::post('cliente/perfil/cambiar-password', [ApiPerfilController::class, 'cambiarPasswordPerfil']);
Route::post('cliente/informacion', [ApiPerfilController::class, 'informacionPerfil']);
Route::post('cliente/editar-perfil', [ApiPerfilController::class, 'editarPerfil']);
Route::post('cliente/listado/direcciones', [ApiPerfilController::class, 'listadoDeDirecciones']);
Route::post('cliente/eliminar/direccion',  [ApiPerfilController::class, 'eliminarDireccion']);
Route::post('cliente/seleccionar/direccion', [ApiPerfilController::class, 'seleccionarDireccion']);

Route::post('cliente/listado/tipo/servicio', [ApiServiciosController::class, 'listaServiciosPorTipo']);
Route::post('cliente/servicios/listado/menu', [ApiServiciosController::class, 'listadoMenuVertical']);

Route::post('cliente/servicios/horizontal/producto', [ApiServiciosController::class, 'listadoMenuHorizontal']);

Route::post('cliente/informacion/producto', [ApiProductosController::class, 'infoProductoIndividual']);

Route::post('cliente/carrito/producto/agregar', [ApiCarritoController::class, 'agregarProductoCarritoTemporal']);
Route::post('cliente/carrito/ver/orden', [ApiCarritoController::class, 'verCarritoDeCompras']);

Route::post('cliente/carrito/borrar/orden', [ApiCarritoController::class, 'borrarCarritoDeCompras']);
Route::post('cliente/carrito/eliminar/producto', [ApiCarritoController::class, 'borrarProductoDelCarrito']);

Route::post('cliente/carrito/ver/producto', [ApiCarritoController::class, 'verProductoCarritoEditar']);
Route::post('cliente/carrito/cambiar/cantidad', [ApiCarritoController::class, 'editarCantidadProducto']);
Route::post('cliente/metodos/de/pago', [ApiMetodoPagoController::class, 'verMetodoPago']);

Route::post('cliente/carrito/ver/proceso-orden', [ProcesarOrdenClienteController::class, 'verOrdenAProcesarCliente']);

Route::post('cliente/productos/ver/seccion', [ApiBuscadorController::class, 'buscarProductoSeccion']);

Route::post('cliente/ver/info/monedero', [ApiMonederoController::class, 'informacionMonedero']);
Route::post('cliente/comprar/monedero', [ApiMonederoController::class, 'realizarCompraMonedero']);


Route::post('cliente/proceso/orden/estado-1', [ProcesarOrdenClienteController::class, 'procesarOrdenEstado1']);

Route::post('cliente/verificar/cupon', [ProcesarOrdenClienteController::class, 'verificarCupon']);

Route::post('cliente/ver/ordenes-activas',  [ApiOrdenesActivasController::class, 'ordenesActivas']);

Route::post('cliente/ver/estado-orden',  [ApiOrdenesActivasController::class, 'estadoOrdenesActivas']);

Route::post('cliente/listado/productos/ordenes',  [ApiOrdenesActivasController::class, 'listadoProductosOrdenes']);

Route::post('cliente/listado/productos/ordenes-individual',  [ApiOrdenesActivasController::class, 'listadoProductosOrdenesIndividual']);

Route::post('cliente/proceso/orden/cancelar',  [ApiOrdenesActivasController::class, 'cancelarOrdenCliente']);

Route::post('cliente/proceso/borrar/orden',  [ApiOrdenesActivasController::class, 'borrarOrdenCliente']);


Route::post('cliente/buscador/general',  [ApiBuscadorController::class, 'buscarNegocioGeneral']);

Route::post('cliente/listado/etiquetas-validas',  [ApiBuscadorController::class, 'listaEtiquetasValidas']);


Route::post('cliente/informacion/servicio',  [ApiServiciosController::class, 'informacionServicio']);

Route::post('cliente/buscador/producto/servicio',  [ApiBuscadorController::class, 'buscadorProductoServicio']);

Route::post('cliente/proceso/calificar/entrega',  [ApiOrdenesActivasController::class, 'calificarEntrega']);

Route::post('cliente/proceso/orden/estado-3', [ApiOrdenesAfiliadoController::class, 'procesarOrdenEstado3']);


Route::post('cliente/prueba',  [ApiBuscadorController::class, 'prueba']);




 // ****--------------  AFILIADOS  ---------------- **** //

Route::post('afiliado/login', [ApiLoginAfiliadoController::class, 'loginAfiliado']);
Route::post('afiliado/telefono/password', [ApiLoginAfiliadoController::class, 'enviarCodigoSms']);
Route::post('afiliado/verificar/codigo-login', [ApiLoginAfiliadoController::class, 'verificarCodigo']);
Route::post('afiliado/actualizar/password', [ApiLoginAfiliadoController::class, 'actualizarPasswordAfiliado']);


Route::post('afiliado/informacion/cuenta', [ApiPerfilAfiliadoController::class, 'informacionCuenta']);
Route::post('afiliado/listado/horarios', [ApiPerfilAfiliadoController::class, 'listadoHorarios']);
Route::post('afiliado/informacion/disponibilidad', [ApiPerfilAfiliadoController::class, 'informacionDisponibilidad']);
Route::post('afiliado/guardar/disponibilidad', [ApiPerfilAfiliadoController::class, 'guardarEstados']);

Route::post('afiliado/informacion/tiempo-orden', [ApiPerfilAfiliadoController::class, 'informacionTiempoOrden']);
Route::post('afiliado/guardar/tiempo-orden', [ApiPerfilAfiliadoController::class, 'guardarTiempoOrden']);

Route::post('afiliado/guardar/tiempo-orden', [ApiPerfilAfiliadoController::class, 'guardarTiempoOrden']);

Route::post('afiliado/categorias/ver-posiciones', [ApiCategoriaAfiliadoController::class, 'informacionCategoriasPosiciones']);

Route::post('afiliado/posiciones/actualizar-categorias', [ApiCategoriaAfiliadoController::class, 'guardarPosicionCategorias']);

Route::post('afiliado/categorias/actualizar-datos', [ApiCategoriaAfiliadoController::class, 'actualizarDatosCategoria']);

Route::post('afiliado/listado/productos-posicion-lista', [ApiCategoriaAfiliadoController::class, 'listadoProductoPosicion']);

Route::post('afiliado/actualizar/productos-posicion', [ApiCategoriaAfiliadoController::class, 'actualizarProductosPosicion']);


Route::post('afiliado/nueva/ordenes', [ApiOrdenesAfiliadoController::class, 'nuevasOrdenes']);

Route::post('afiliado/informacion/estado/nueva-orden', [ApiOrdenesAfiliadoController::class, 'informacionEstadoNuevaOrden']);

Route::post('afiliado/listado/producto/orden', [ApiOrdenesAfiliadoController::class, 'listadoProductosOrden']);
Route::post('afiliado/listado/orden/producto/individual', [ApiOrdenesAfiliadoController::class, 'listaOrdenProductoIndividual']);

Route::post('afiliado/cancelar/orden', [ApiOrdenesAfiliadoController::class, 'cancelarOrden']);
Route::post('afiliado/borrar/orden', [ApiOrdenesAfiliadoController::class, 'borrarOrden']);

Route::post('afiliado/proceso/orden/estado-2', [ApiOrdenesAfiliadoController::class, 'procesarOrdenEstado2']);


Route::post('afiliado/proceso/orden/estado-4', [ApiOrdenesAfiliadoController::class, 'procesarOrdenEstado4']);


Route::post('afiliado/listado/preparando/ordenes', [ApiOrdenesAfiliadoController::class, 'listadoPreparandoOrdenes']);
Route::post('afiliado/informacion/orden/preparando', [ApiOrdenesAfiliadoController::class, 'informacionOrdenEnPreparacion']);

Route::post('afiliado/finalizar/orden', [ApiOrdenesAfiliadoController::class, 'finalizarOrden']); // estado 5

Route::post('afiliado/cancelar/orden/extra', [ApiOrdenesAfiliadoController::class, 'cancelarOrdenExtra']);

Route::post('afiliado/ordenes/completadas/hoy', [ApiOrdenesAfiliadoController::class, 'listadoOrdenesCompletadasHoy']);

Route::post('afiliado/listado/categorias', [ApiOrdenesAfiliadoController::class, 'listadoCategoriasProducto']);

Route::post('afiliado/listado/categorias/producto', [ApiOrdenesAfiliadoController::class, 'listadoCategoriasProductoListado']);

Route::post('afiliado/producto/info/individual', [ApiOrdenesAfiliadoController::class, 'informacionProductoIndividual']);

Route::post('afiliado/actualizar/producto/informacion', [ApiOrdenesAfiliadoController::class, 'actualizarProducto']);

Route::post('afiliado/historial/ordenes', [ApiOrdenesAfiliadoController::class, 'historialOrdenesCompletas']);




// ***--- MOTORISTAS **

Route::post('motorista/login', [ApiLoginMotoristaController::class, 'loginMotorista']);
Route::post('motorista/buscar/telefono', [ApiLoginMotoristaController::class, 'buscarTelefono']);
Route::post('motorista/verificar/codigo-login', [ApiLoginMotoristaController::class, 'verificarCodigo']);
Route::post('motorista/actualizar/password', [ApiLoginMotoristaController::class, 'actualizarPasswordMotorista']);

Route::post('motorista/ver/nueva/ordenes', [ApiOrdenesMotoristaController::class, 'verNuevasOrdenes']);
Route::post('motorista/ver/orden/id', [ApiOrdenesMotoristaController::class, 'verOrdenPorID']);
Route::post('motorista/obtener/orden', [ApiOrdenesMotoristaController::class, 'obtenerOrden']);

Route::post('motorista/orden/proceso',  [ApiOrdenesMotoristaController::class, 'verProcesoOrdenes']);

Route::post('motorista/ver/productos',  [ApiOrdenesMotoristaController::class, 'verProductosOrden']);

Route::post('motorista/ver/orden/proceso/id', [ApiOrdenesMotoristaController::class, 'verOrdenProcesoPorID']);

Route::post('motorista/iniciar/entrega', [ApiOrdenesMotoristaController::class, 'iniciarEntrega']);

Route::post('motorista/borrar/orden/cancelada', [ApiOrdenesMotoristaController::class, 'borrarOrdenCancelada']);


Route::post('motorista/orden/procesoentrega', [ApiOrdenesMotoristaController::class, 'verProcesoOrdenesEntrega']);

Route::post('motorista/notificar/cliente/orden', [ApiOrdenesMotoristaController::class, 'notificarClienteOrden']);

Route::post('motorista/finalizar/entrega', [ApiOrdenesMotoristaController::class, 'finalizarEntrega']);

Route::post('motorista/info/cuenta', [ApiOrdenesMotoristaController::class, 'informacionCuenta']);

Route::post('motorista/actualizar/password', [ApiOrdenesMotoristaController::class, 'actualizarPassword']);

Route::post('motorista/info/disponibilidad', [ApiOrdenesMotoristaController::class, 'informacionDisponibilidad']);

Route::post('motorista/guadar/configuracion', [ApiOrdenesMotoristaController::class, 'modificarDisponibilidad']);


Route::post('motorista/ver/historial', [ApiOrdenesMotoristaController::class, 'verHistorial']);






