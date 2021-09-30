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
            <h1>Lista de Tipos Servicio Zona</h1>
        </div>
        <button type="button" onclick="abrirModalAgregar()" class="btn btn-success btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Nuevo Tipo Servicio
        </button>

        <button type="button" style="margin-left: 15px" onclick="abrirModalFiltro()" class="btn btn-info btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Filtro para posiciones
        </button>

        <button type="button" style="margin-left: 15px" onclick="modal()" class="btn btn-info btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Activar/Desactivar Servicio por Bloque
        </button>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Tipos Servicio Zona</h3>
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
                <h4 class="modal-title">Nuevo Tipo Servicio</h4>
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
                                        <select class="form-control" id="select-identificador" onchange="buscarServicios(this)">
                                            <option value="0" selected>Seleccionar</option>
                                            @foreach($zona as $item)
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="color:#191818">Servicio</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="select-servicio">
                                            <option value="0" selected>Vacío</option>
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
                <h4 class="modal-title">Editar Tipo Servicio</h4>
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
                                    <label>Mostrar/Ocultar este bloque en la App</label><br>
                                    <input type="hidden" id="id-editar">
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="toggle-activo">
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
                <button type="button" class="btn btn-primary" onclick="editar()">Actualizar</button>
            </div>
        </div>
    </div>
</div>

<!-- modal filtro para cambiar posiciones -->
<div class="modal fade" id="modalFiltro">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Filtro para cambiar posiciones por Bloque</h4>
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
                                    <label style="color:#191818">Zonas Identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="select-filtro">
                                            @foreach($zona as $item)
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
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

<!-- activar o desactivar servicios -->
<div class="modal fade" id="modalVista">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Activar/Desactivar Global por bloque</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-vista">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label style="color:#191818">Tipos de Servicios</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="select-tiposervicio">
                                            @foreach($tiposervicio as $item)
                                                <option value="{{$item->id}}">{{$item->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Disponibilidad</label><br>
                                    <input type="hidden" id="id-editar">
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="cbvista">
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
                <button type="button" class="btn btn-primary" onclick="modificarServicio()">Actualizar</button>
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
            var ruta = "{{ URL::to('/admin/tiposerviciozona/tablas/lista-tipo-servicio-zona') }}";
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function abrirModalFiltro(){
            $('#modalFiltro').modal('show');
        }

        function recargar(){
            var ruta = "{{ url('/admin/tiposerviciozona/tablas/lista-tipo-servicio-zona') }}";
            $('#tablaDatatable').load(ruta);
        }

        // ver todos los tipos de servicio para desactivar o activar alguno
        function modal(){
            $('#modalVista').modal('show');
        }

        // buscar servicios, segun cambio del select
        function buscarServicios(sel){

            if(sel.value !== '0'){
                openLoading();
                axios.post('/tiposerviciozona/buscar/servicio',{
                    'id': sel.value
                })
                    .then((response) => {
                        closeLoading();
                        if (response.data.success === 1) {

                            var tipo = document.getElementById("select-servicio");
                            document.getElementById("select-servicio").options.length = 0;

                            if(response.data.tiposervicio.length === 0){
                                tipo.options[0] = new Option('Ninguna disponible', 0);
                            }else{
                                $.each(response.data.tiposervicio, function( key, val ){
                                    tipo.options[key] = new Option(val.nombre, val.id);
                                });
                            }
                        }else{
                            toastMensaje('error', 'Error de busqueda');
                        }
                    })
                    .catch((error) => {
                        closeLoading();
                        toastMensaje('error', 'Error de busqueda');
                    });
            }
        }

        function modificarServicio(){
            var id = document.getElementById("select-tiposervicio").value;
            // toggle estado
            var te = document.getElementById("cbvista").checked;

            var toggleestado = te ? 1 : 0;

            let formData = new FormData();
            formData.append('id', id);
            formData.append('estado', toggleestado);

            openLoading();

            axios.post('/tiposerviciozona/actidesactivar/bloque-global', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalVista').modal('hide');
                        toastMensaje('success', 'Actualizado correctamente');
                        recargar();
                    }else{
                        toastMensaje('error', 'Error al editar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastMensaje('error', 'Error al editar');
                });
        }

        // modal nuevo tipo servicio zona
        function abrirModalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        // agregar nuevo tipo servicio zona
        function nuevo(){

            var identificador = document.getElementById("select-identificador").value; //zona
            var servicio = document.getElementById("select-servicio").value; // servicio

            if (identificador === '0') {
                toastMensaje('error', 'Seleccionar identificador de Zona');
                return;
            }

            if(servicio === '0'){
                toastMensaje('error', 'Seleccionar un servicio')
                return;
            }

            let formData = new FormData();
            formData.append('identificador', identificador);
            formData.append('servicio', servicio);

            openLoading();

            axios.post('/tiposerviciozona/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastMensaje('error', 'Este servicio ya esta agregado');
                    } else if (response.data.success === 2) {
                        $('#modalAgregar').modal('hide');
                        toastMensaje('success', 'Registrado correctamente');
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

        function filtrar(){
            var identificador = document.getElementById("select-filtro").value;

            window.location.href="{{ url('/tiposerviciozona/bloqueposicion') }}/"+identificador;
        }

        // informacion tipo servicios zona
        function verInformacion(id){
            openLoading();

            axios.post('/tiposerviciozona/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        $('#modalEditar').modal('show');
                        $('#id-editar').val(response.data.tipo.id);

                        if(response.data.tipo.activo === 0){
                            $("#toggle-activo").prop("checked", false);
                        }else{
                            $("#toggle-activo").prop("checked", true);
                        }
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
            var ta = document.getElementById('toggle-activo').checked;

            var toggleactivo = ta ? 1 : 0;

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('toggle', toggleactivo);

            axios.post('/tiposerviciozona/editar-tipo', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        $('#modalEditar').modal('hide');
                        toastMensaje('success', 'Actualizado correctamente');
                        recargar();
                    }
                    else {
                        toastMensaje('error', 'Error al Editar');
                    }
                })
                .catch((error) => {
                   closeLoading();
                   toastMensaje('error', 'Error al Editar');
                });
        }


    </script>


@endsection
