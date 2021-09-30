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
            <h1>Lista de Categorias para: {{ $nombre }}</h1>
        </div>
        <br>
        <button type="button" onclick="abrirModalAgregar()" class="btn btn-success btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Nueva categoria
        </button>

        <button type="button" onclick="verTodoProducto()" class="btn btn-info btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Ver Todos los Productos
        </button>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Tipos</h3>
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

<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nueva categoria</h4>
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
                                    <label>Nombre</label>
                                    <input type="text" maxlength="100" class="form-control" id="nombre-nuevo" placeholder="Nombre categoria">
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
                <h4 class="modal-title">Editar categoria</h4>
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
                                    <input type="hidden" id="id-editar">
                                </div>

                                <div class="form-group">
                                    <label>Nombre categoria</label>
                                    <input type="text" maxlength="50" class="form-control" id="nombre-editar" placeholder="Nombre categoria">
                                </div>

                                <div class="form-group">
                                    <label>Disponibilidad</label> <br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="cbactivo-editar">
                                        <div class="slider round">
                                            <span class="on">Si</span>
                                            <span class="off">No</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label>Visible Propietario</label> <br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="cbvisible-editar">
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
                <button type="button" class="btn btn-primary" onclick="editar()">Guardar</button>
            </div>
        </div>
    </div>
</div>


@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery-ui-drag.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/datatables-drag.min.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            id = {{ $id }};
            $('#id-editar').val(id);
            var ruta = "{{ url('admin/categorias/tablas') }}/"+id;
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            id = {{ $id }};
            $('#id-editar').val(id);
            var ruta = "{{ url('admin/categorias/tablas') }}/"+id;
            $('#tablaDatatable').load(ruta);
        }

        // modal nuevo
        function abrirModalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function nuevo(){
            var id = {{ $id }};
            var nombre = document.getElementById('nombre-nuevo').value;

            if(nombre === ''){
                toastMensaje('error', 'Nombre es requerido');
                return;
            }

            if(nombre.length > 100){
                toastMensaje('error', 'Nombre máximo 100 caracteres');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);

            axios.post('/categorias/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastMensaje('success', 'Registrado correctamente');
                        $('#modalAgregar').modal('hide');
                        recargar();
                    }
                    else {
                        toastMensaje('error', 'Error al registrar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastMensaje('error', 'Error al registrar');
                });
        }

        function informacion(id){
            openLoading();

            axios.post('/categorias/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(response.data.categoria.id);
                        $('#nombre-editar').val(response.data.categoria.nombre);

                        if(response.data.categoria.activo === 0){
                            $("#cbactivo-editar").prop("checked", false);
                        }else{
                            $("#cbactivo-editar").prop("checked", true);
                        }

                        if(response.data.categoria.visible === 0){
                            $("#cbvisible-editar").prop("checked", false);
                        }else{
                            $("#cbvisible-editar").prop("checked", true);
                        }

                    }else{
                        toastMensaje('error', 'Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastMensaje('error', 'Información no encontrada');
                });
        }

        function editar(){
            var id = document.getElementById('id-editar').value;
            var nombre = document.getElementById('nombre-editar').value;
            var ta = document.getElementById('cbactivo-editar').checked;
            var tv = document.getElementById('cbvisible-editar').checked;

            var toggleActivo = ta ? 1 : 0;
            var toggleVisible = tv ? 1 : 0;

            if(nombre === ''){
                toastMensaje('error', 'Nombre es requerido');
                return;
            }

            if(nombre.length > 100){
                toastMensaje('error', 'Nombre máximo 100 caracteres');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('toggle', toggleActivo);
            formData.append('togglevisible', toggleVisible);
            formData.append('nombre', nombre);

            axios.post('/categorias/editar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if (response.data.success === 1) {
                        $('#modalEditar').modal('hide');
                        toastMensaje('success', 'Categoria actualizada');
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

        // mandamos id de categoria
        function producto(id){
            window.location.href="{{ url('/admin/productos/') }}/"+id;
        }

        function verTodoProducto(){
            // id del servicio
            var id = {{ $id }};
            window.location.href="{{ url('/admin/ver/todos/productos/') }}/"+id;
        }


    </script>


@endsection
