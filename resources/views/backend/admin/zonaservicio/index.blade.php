@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />

@stop

<style>
    table{
        /*Ajustar tablas*/
        table-layout:fixed;
    }
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="col-sm-12">
            <h1>Lista de Zonas Servicio</h1>
        </div>
        <br>
        <button type="button" onclick="abrirModalAgregar()" class="btn btn-success btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Nuevo Zona Servicio
        </button>

        <button type="button" style="margin-left: 15px" onclick="abrirModalFiltro()" class="btn btn-info btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Filtro para posiciones
        </button>

        <button type="button" style="margin-left: 15px" onclick="abrirModalFiltro2()" class="btn btn-info btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Filtro para envío gratis por zonas
        </button>

        </br>
        </br>

        <button type="button" style="margin-left: 15px" onclick="modalPrecio()" class="btn btn-info btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Modificar precios de envio por zona
        </button>

        <button type="button" style="margin-left: 15px" onclick="modalGanancia()" class="btn btn-info btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Modificar precios de ganancia motorista por zona
        </button>

        <button type="button" style="margin-left: 15px" onclick="modalMinGratis()" class="btn btn-info btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Filtro mínimo para envio gratis
        </button>

        <button type="button" style="margin-left: 15px" onclick="modal()" class="btn btn-info btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Activar/Desactivar Servicio
        </button>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Listado</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="tablaDatatable">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- modal nuevo -->
<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Zona Servicio</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-nuevo">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">

                                    <label style="color:#191818">Zonas identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="selectzona-identificador">
                                            @foreach($zonas as $item)
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label style="color:#191818">Servicios identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="selectservicio-identificador">
                                            @foreach($servicios as $item)
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group" style="margin-left:0px">
                                    <label>Activo</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="cbactivo">
                                        <div class="slider round">
                                            <span class="on">Si</span>
                                            <span class="off">No</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <br>
                                    <label>Precio Envío $</label>
                                    <input type="number" id="precioenvio">
                                </div>

                                <div class="form-group">
                                    <br>
                                    <label>Ganancia Motorista $</label>
                                    <input type="number" id="ganancia-nuevo">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="nuevo()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- modal editar -->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Zona Servicio</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-editar">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Zona identificador</label>
                                    <input type="hidden" id="id-editar">
                                    <input type="text" disabled class="form-control" id="zonaidentificador-editar">
                                </div>

                                <div class="form-group">
                                    <label>Servicio identificador</label>
                                    <input type="text" disabled class="form-control" id="servicioidentificador-editar">
                                </div>

                                <div class="form-group">
                                    <br>
                                    <label>Precio Envío $</label>
                                    <input type="number" step="any" id="precioenvio-editar">
                                </div>

                                <div class="form-group" style="margin-left:0px">
                                    <label>Activo</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="cbactivo-editar">
                                        <div class="slider round">
                                            <span class="on">Si</span>
                                            <span class="off">No</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <br>
                                    <label>Ganancia Motorista $</label>
                                    <input type="number" step="any" id="ganancia-editar">
                                </div>


                                <div class="form-group" style="margin-left:0px">
                                    <label>Activar Nuevo cargo si supera x cantidad de Dinero de la orden</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="cbmingratis-editar">
                                        <div class="slider round">
                                            <span class="on">Si</span>
                                            <span class="off">No</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <br>
                                    <label>Nuevo Precio a superar para Envío Gratis $</label>
                                    <input type="number" step="any" id="precioenviogratis-editar">
                                </div>

                                <div class="form-group" style="margin-left:0px">
                                    <label>Este Servicio para esta Zona tiene envío gratis</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="cbzonagratis-editar">
                                        <div class="slider round">
                                            <span class="on">Si</span>
                                            <span class="off">No</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-group" style="margin-left:0px">
                                    <label>Cerrar Servicio para esta Zona</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="toggle-cerrado">
                                        <div class="slider round">
                                            <span class="on">Si</span>
                                            <span class="off">No</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <br>
                                    <label>Mensaje de Cerrado</label>
                                    <input type="text" maxlength="200" id="mensajecerrado-editar">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editar()">Actualizar</button>
            </div>
        </div>
    </div>
</div>


<!-- modal modificar precio envio -->
<div class="modal fade" id="modalPrecio">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Modificar precio envio por zona</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-precio">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">

                                    <label style="color:#191818">Zonas identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="select-precio">
                                            @foreach($zonas as $item)
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <br>
                                    <label>Precio Envío $</label>
                                    <input type="number" step="any" id="precio-zona">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="modificarPrecio()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- modal ganancia -->
<div class="modal fade" id="modalGanancia">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Modificar ganancia motorista por zona</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-ganancia">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="color:#191818">Zonas identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="select-ganancia">
                                            @foreach($zonas as $item)
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <br>
                                    <label>Precio Ganancia $</label>
                                    <input type="number" step="any" id="ganancia-zona">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="modificarGanancia()">Guardar</button>
            </div>
        </div>
    </div>
</div>


<!-- modal filtro -->
<div class="modal fade" id="modalFiltro">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Filtro para cambiar posiciones</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-filtro">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="color:#191818">Zonas identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="selectzona-filtro">
                                            @foreach($zonas as $item)
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="color:#191818">Servicios tipo identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="selectservicio-filtro">
                                            @foreach($serviciostipo as $item)
                                                <option value="{{$item->id}}">{{$item->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="filtrar()">Filtrar</button>
            </div>
        </div>
    </div>
</div>

<!-- modal filtro para servicios, cambiar estado envio gratis -->
<div class="modal fade" id="modalFiltro2">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Filtro para colocar envío gratis</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-filtro2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="color:#191818">Zonas identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="selectzona-filtro2" multiple="multiple" >
                                            @foreach($zonas as $item)
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group" style="margin-left:20px">
                                    <label>Envio gratis a todas las zonas</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="cbzonapublico">
                                        <div class="slider round">
                                            <span class="on">Si</span>
                                            <span class="off">No</span>
                                        </div>
                                    </label>
                                </div>


                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="filtrar2()">Filtrar</button>
            </div>
        </div>
    </div>
</div>



<!-- modal filtro minimo de envio gratis, por zona y servicios -->
<div class="modal fade" id="modalFiltro4">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Filtro mínimo para envio gratis</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-filtro4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">


                                <div class="form-group">
                                    <label style="color:#191818">Zonas identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="selectzona-filtro4" multiple="multiple" >
                                            @foreach($zonas as $item)
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label style="color:#191818">Servicios identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="servicios-filtro4" multiple="multiple" >
                                            @foreach($servicios as $item)
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label>Minimo de compra para aplicar nuevo tipo de cargo</label>
                                    <input type="number" step="0.01" id="minenvio-filtro4">
                                </div>

                                <div class="form-group" style="margin-left:20px">
                                    <label>Activar o Desactivar</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="check4">
                                        <div class="slider round">
                                            <span class="on">Activar</span>
                                            <span class="off">Desactivar</span>
                                        </div>
                                    </label>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="filtrar4()">Filtrar</button>
            </div>
        </div>
    </div>
</div>


<!-- Activar o desactivar servicio por zona -->
<div class="modal fade" id="modalVista">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Activar o Desactivar Servicio por Zona</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-zona">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">


                                <div class="form-group">
                                    <label style="color:#191818">Zonas identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="selector-zona" multiple="multiple" >
                                            @foreach($zonas as $item)
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label style="color:#191818">Servicios identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="selector-servicio" multiple="multiple" >
                                            @foreach($servicios as $item)
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group" style="margin-left:20px">
                                    <label>Activar o Desactivar</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="check-selector">
                                        <div class="slider round">
                                            <span class="on">Activar</span>
                                            <span class="off">Desactivar</span>
                                        </div>
                                    </label>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="modificarServicio()">Filtrar</button>
            </div>
        </div>
    </div>
</div>


@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery.dataTables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            var ruta = "{{ URL::to('/admin/zonaservicios/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/zonaservicios/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
        }

        // modal nuevo zona servicio
        function abrirModalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        // agregar nuevo zona servicio
        function nuevo(){

            var selectzona = document.getElementById('selectzona-identificador').value;
            var selectservicio = document.getElementById('selectservicio-identificador').value;
            var ta = document.getElementById('cbactivo').checked;
            var precioenvio = document.getElementById("precioenvio").value;
            var ganancia = document.getElementById("ganancia-nuevo").value;

            var toggleActivo = ta ? 1 : 0;

            if (precioenvio === '') {
                toastMensaje('error', 'Precio es requerido');
                return;
            }

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            if(!precioenvio.match(reglaNumeroDecimal)) {
                toastMensaje('error', 'Precio debe ser número decimal');
                return;
            }

            if(precioenvio < 0) {
                toastMensaje('error', 'Precio debe ser mayor a 0 o igual');
                return;
            }

            if(precioenvio > 1000) {
                toastMensaje('error', 'Precio no debe superar 1000');
                return;
            }

            if (ganancia === '') {
                toastMensaje('error', 'Ganancia motorista es requerida');
                return;
            }

            if(!ganancia.match(reglaNumeroDecimal)) {
                toastMensaje('error', 'Ganancia motorista debe ser número decimal');
                return;
            }

            if(ganancia < 0) {
                toastMensaje('error', 'Ganancia motorista debe ser mayor a 0 o igual');
                return;
            }

            if(ganancia > 1000) {
                toastMensaje('error', 'Ganancia motorista no debe superar 1000');
                return;
            }

            let formData = new FormData();
            formData.append('selectzona', selectzona);
            formData.append('selectservicio', selectservicio);
            formData.append('cbactivo', toggleActivo);
            formData.append('precioenvio', precioenvio);
            formData.append('ganancia', ganancia);

            openLoading();

            axios.post('/zonaservicios/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastMensaje('error', 'Esta zona Servicio ya esta agregada');
                    } else if (response.data.success === 2) {
                        toastMensaje('success', 'Agregado correctamente');
                        $('#modalAgregar').modal('hide');
                        recargar();
                    } else {
                        toastMensaje('error', 'Error al guardar');
                    }
                })
                .catch((error) => {
                    toastMensaje('error', 'Error al guardar');
                    closeLoading();
                });

        }


        // informacion tipo servicios zona
        function verInformacion(id){
           openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post('/zonaservicios/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(response.data.zonaservicio.id);
                        $('#zonaidentificador-editar').val(response.data.zonaservicio.idenZona);
                        $('#servicioidentificador-editar').val(response.data.zonaservicio.idenServicio);

                        if(response.data.zonaservicio.activo === 0){
                            $("#cbactivo-editar").prop("checked", false);
                        }else{
                            $("#cbactivo-editar").prop("checked", true);
                        }

                        $('#precioenvio-editar').val(response.data.zonaservicio.precio_envio);
                        $('#ganancia-editar').val(response.data.zonaservicio.ganancia_motorista);

                        if(response.data.zonaservicio.min_envio_gratis === 0){
                            $("#cbmingratis-editar").prop("checked", false);
                        }else{
                            $("#cbmingratis-editar").prop("checked", true);
                        }

                        $('#precioenviogratis-editar').val(response.data.zonaservicio.costo_envio_gratis);

                        document.getElementById("cbzonagratis-editar").disabled = false;
                        if(response.data.zonaservicio.zona_envio_gratis === 0){
                            $("#cbzonagratis-editar").prop("checked", false);
                        }else{
                            $("#cbzonagratis-editar").prop("checked", true);
                        }

                        if(response.data.zonaservicio.saturacion === 0){
                            $("#toggle-cerrado").prop("checked", false);
                        }else{
                            $("#toggle-cerrado").prop("checked", true);
                        }

                        $('#mensajecerrado-editar').val(response.data.zonaservicio.mensaje_bloqueo);

                    }else{
                        toastMensaje('error', 'Error al buscar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastMensaje('error', 'Error al buscar');
                });
        }

        // editar tipo servicio zona
        function editar(){

            var id = document.getElementById('id-editar').value;

            var precioenvio = document.getElementById('precioenvio-editar').value;
            var ta = document.getElementById('cbactivo-editar').checked;
            var ganancia = document.getElementById('ganancia-editar').value;
            var tg = document.getElementById('cbmingratis-editar').checked;
            var minenvio = document.getElementById('precioenviogratis-editar').value;
            var tz = document.getElementById('cbzonagratis-editar').checked;

            var tb = document.getElementById('toggle-cerrado').checked;
            var mensajebloqueo = document.getElementById('mensajecerrado-editar').value;

            var toggleActivo = ta ? 1 : 0;
            var toggleMinGratis = tg ? 1 : 0;
            var toggleZonaGratis = tz ? 1 : 0;
            var toggleBloqueo = tb ? 1 : 0;

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            if(precioenvio === ''){
                toastMensaje('error', 'Precio es requerido');
                return;
            }

            if(!precioenvio.match(reglaNumeroDecimal)) {
                toastMensaje('error', 'Precio envío debe ser número decimal');
                return;
            }

            if(precioenvio < 0){
                toastMensaje('error', 'Precio envío no números negativo');
                return;
            }

            if(precioenvio > 1000){
                toastMensaje('error', 'Precio envío máximo 1000');
                return;
            }

            if(ganancia === ''){
                toastMensaje('error', 'Ganancia Motorista es requerido');
                return;
            }

            if(!ganancia.match(reglaNumeroDecimal)) {
                toastMensaje('error', 'Ganancia Motorista envío debe ser número decimal');
                return;
            }

            if(ganancia < 0){
                toastMensaje('error', 'Ganancia Motorista envío no números negativo');
                return;
            }

            if(ganancia > 1000){
                toastMensaje('error', 'Ganancia Motorista envío máximo 1000');
                return;
            }


            if(minenvio === ''){
                toastMensaje('error', 'Nuevo Cargo es requerido');
                return;
            }

            if(!minenvio.match(reglaNumeroDecimal)) {
                toastMensaje('error', 'Nuevo Cargo envío debe ser número decimal');
                return;
            }

            if(minenvio < 0){
                toastMensaje('error', 'Nuevo Cargo envío no números negativo');
                return;
            }

            if(minenvio > 1000){
                toastMensaje('error', 'Nuevo Cargo envío máximo 1000');
                return;
            }

            if(toggleBloqueo === 1){
                if(mensajebloqueo === ''){
                    toastMensaje('error', 'Mensaje de Cerrado es requerido');
                    return;
                }

                if(mensajebloqueo.length > 200){
                    toastMensaje('error', 'Máximo 200 caracteres para mensaje bloqueo');
                    return;
                }
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('toggle', toggleActivo);
            formData.append('precioenvio', precioenvio);
            formData.append('ganancia', ganancia);
            formData.append('cbmingratis', toggleMinGratis);
            formData.append('minenvio', minenvio);
            formData.append('cbzonagratis', toggleZonaGratis);
            formData.append('togglebloqueo', toggleBloqueo);
            formData.append('mensajebloqueo', mensajebloqueo);

            axios.post('/zonaservicios/editar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastMensaje('success', 'Actualizado correctamente');
                        $('#modalEditar').modal('hide');
                        recargar();
                    }
                    else {
                        toastMensaje('error', 'Error al editar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastMensaje('error', 'Error al editar');
                });
        }


        // modificar precio por zona servicio
        function modificarPrecio(){
            var zonaid = document.getElementById('select-precio').value;
            var preciozona = document.getElementById('precio-zona').value;

            if(zonaid === ''){
                toastMensaje('error', 'ID zona es requerido');
                return;
            }

            if(preciozona === ''){
                toastMensaje('error', 'Precio es requerido');
                return;
            }

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            if(!preciozona.match(reglaNumeroDecimal)) {
                toastMensaje('error', 'Precio requiere número decimal');
                return;
            }

            if(preciozona < 0){
                toastMensaje('error', 'Precio no números negativos');
                return;
            }

            if(preciozona > 1000){
                toastMensaje('error', 'Precio no puede ser mayor a 1000');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('zonaid', zonaid);
            formData.append('preciozona', preciozona);

            axios.post('/zonaservicios/nuevo-precio-varios', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1) {
                        $('#modalPrecio').modal('hide');
                        toastMensaje('success', 'Actualizado correctamente');
                        recargar();
                    }
                    else {
                        toastMensaje('error', 'Error al actualizar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastMensaje('error', 'Error al actualizar');
                });
        }

        function modificarGanancia(){
            var zonaid = document.getElementById('select-ganancia').value;
            var preciozona = document.getElementById('ganancia-zona').value;

            if(zonaid === ''){
               toastMensaje('error', 'ID zona es requerido');
                return;
            }

            if(preciozona === ''){
                toastMensaje('error','Precio es requerido');
                return;
            }

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            if(!preciozona.match(reglaNumeroDecimal)) {
                toastMensaje('error', 'Precio debe ser decimal');
                return;
            }

            if(preciozona < 0){
                toastMensaje('error', 'Precio no debe ser Negativo');
                return;
            }

            if(preciozona > 1000){
                toastMensaje('error', 'Precio no debe superar 1000');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('zonaid', zonaid);
            formData.append('preciozona', preciozona);

            axios.post('/zonaservicios/nuevo-precio-ganancia', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1) {
                        toastMensaje('success', 'Actualizado correctamente');
                        $('#modalGanancia').modal('hide');
                        recargar();
                    }
                    else {
                        toastMensaje('error', 'Error al actualizar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastMensaje('error', 'Error al actualizar');
                });
        }

        // abrir modal precio
        function modalPrecio(){
            document.getElementById("formulario-precio").reset();
            $('#modalPrecio').modal('show');
        }

        function modalGanancia(){
            document.getElementById("formulario-ganancia").reset();
            $('#modalGanancia').modal('show');
        }

        // filtros
        function abrirModalFiltro(){
            $('#modalFiltro').modal('show');
        }

        function abrirModalFiltro2(){
            document.getElementById("formulario-filtro2").reset();
            $('#modalFiltro2').modal('show');
        }


        function modalMinGratis(){
            document.getElementById("formulario-filtro4").reset();
            $('#modalFiltro4').modal('show');
        }

        function filtrar(){
            var idzona = document.getElementById('selectzona-filtro').value;
            var idtipo = document.getElementById('selectservicio-filtro').value;


            window.location.href="{{ url('/admin/zonaservicios/filtrado') }}/"+idzona+'/'+idtipo;
        }

        function filtrar2(){
            var values = $('#selectzona-filtro2').val();
            var check = document.getElementById('cbzonapublico').checked;

            var toggle = check ? 1 : 0;

            if(values.length == null || values.length === 0){
                toastMensaje('error', 'Seleccionar mínimo 1 zona');
                return;
            }

            openLoading();
            var formData = new FormData();
            for (var i = 0; i < values.length; i++) {
                formData.append('idzonas[]', values[i]);
            }
            formData.append('cbzonapublico', toggle);

            axios.post('/zonaservicios/enviogratis', formData, {
            })
                .then((response) => {

                    closeLoading();
                    if(response.data.success === 1) {
                        $('#modalFiltro2').modal('hide');
                        toastMensaje('success', 'Actualizado correctamente');
                        recargar();
                    }
                    else {
                        toastMensaje('error', 'Error al actualizar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastMensaje('error', 'Error al actualizar');
                });
        }


        function filtrar4(){
            var values = $('#selectzona-filtro4').val();
            var servicios = $('#servicios-filtro4').val();
            var mincompra = document.getElementById("minenvio-filtro4").value;
            var check = document.getElementById('check4').checked;

            var toggleZona = check ? 1 : 0;
            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;
            if(values.length == null || values.length === 0){
                toastMensaje('error', 'Seleccionar mínimo 1 Zona');
                return;
            }

            if(servicios.length == null || servicios.length === 0){
                toastMensaje('error', 'Seleccionar mínimo 1 Servicio');
                return;
            }

            if(mincompra === ''){
                toastMensaje('error', 'Mínimo de compra es requerido');
                return;
            }

            if(!mincompra.match(reglaNumeroDecimal)) {
                toastMensaje('error', 'Mínimo de compra requiere número decimal');
                return;
            }

            if(mincompra < 0){
                toastMensaje('error', 'Mínimo de compra no debe ser Negativo');
                return;
            }

            if(mincompra > 1000000){
                toastMensaje('error', 'Mínimo de compra no debe ser mayor a 1 millón');
                return;
            }

            openLoading();
            var formData = new FormData();
            for (var i = 0; i < values.length; i++) {
                formData.append('idzonas[]', values[i]);
            }
            for (var i = 0; i < servicios.length; i++) {
                formData.append('idservicios[]', servicios[i]);
            }

            formData.append('cbzonapublico', toggleZona);
            formData.append('mincompra', mincompra);

            axios.post('/zonaservicios/modificar-min-gratis', formData, {
            })
                .then((response) => {

                    closeLoading();
                    if(response.data.success === 1) {
                        toastMensaje('success', 'Actualizado correctamente');
                        $('#modalFiltro4').modal('hide');
                        recargar();
                    }
                    else {
                        toastMensaje('error', 'Error al actualizar');
                    }
                })
                .catch((error) => {
                    toastMensaje('error', 'Error al actualizar');
                    closeLoading();
                });
        }

        function modal(){
            document.getElementById("formulario-zona").reset();
            $('#modalVista').modal('show');
        }

        function modificarServicio(){
            var zonas = $('#selector-zona').val();
            var servicios = $('#selector-servicio').val();

            var ts = document.getElementById("check-selector").checked;

            var toggleZona = ts ? 1 : 0;

            if(zonas.length == null || zonas.length === 0){
                toastMensaje('error', 'Seleccionar mínimo 1 Zona');
                return;
            }

            if(servicios.length == null || servicios.length === 0){
                toastMensaje('error', 'Seleccionar mínimo 1 Servicio');
                return;
            }

            openLoading();

            var formData = new FormData();
            for (var i = 0; i < zonas.length; i++) {
                formData.append('idzonas[]', zonas[i]);
            }
            for (var j = 0; j < servicios.length; j++) {
                formData.append('idservicios[]', servicios[j]);
            }

            formData.append('check', toggleZona);

            axios.post('/activar/desactivar/zonaservicio', formData, {
            })
                .then((response) => {

                    closeLoading();
                    if(response.data.success === 1) {
                        toastMensaje('success', 'Actualizado correctamente');
                        $('#modalVista').modal('hide');
                        recargar();
                    }
                    else {
                        toastMensaje('error', 'Error al actualizar');
                    }
                })
                .catch((error) => {
                    toastMensaje('error', 'Error al actualizar');
                    closeLoading();
                });
        }


    </script>


@endsection
