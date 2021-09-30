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
            <h1>Lista de Etiquetas</h1>
        </div>
        <br>
        <button type="button" onclick="abrirModalAgregar()" class="btn btn-success btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Nueva Etiqueta
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

<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nueva Etiqueta</h4>
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
                                    <label style="color:#191818">Etiquetas</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="select-etiqueta" onchange="buscarServicios(this)">
                                            @foreach($lista as $item)
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
                <h4 class="modal-title">Editar</h4>
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
                                    <label>¿Borrar Etiqueta?</label>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger" onclick="editar()">Borrar</button>
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
            var id = {{ $id }};
            var ruta = "{{ url('admin/servicios/tabla/etiquetas') }}/"+id;
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            var id = {{ $id }};
            var ruta = "{{ url('admin/servicios/tabla/etiquetas') }}/"+id;
            $('#tablaDatatable').load(ruta);
        }

        // modal nuevo
        function abrirModalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function nuevo(){
            var id = {{ $id }};
            var id2 = document.getElementById('select-etiqueta').value;

            if(id2 === ''){
                toastMensaje('error', 'Etiqueta es requerida');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('id2', id2);

            axios.post('/servicios/etiqueta/guardar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if (response.data.success === 1) {
                        toastMensaje('error', 'La etiqueta ya esta Registrada');
                    }
                    else if (response.data.success === 2) {
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

        function modalBorrar(id){
            $('#id-editar').val(id);
            $('#modalEditar').modal('show');
        }

        function editar(){
            var id = document.getElementById('id-editar').value;

            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post('/servicios/etiqueta/borrar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if (response.data.success === 1) {
                        $('#modalEditar').modal('hide');
                        toastMensaje('success', 'Eliminado');
                        recargar();
                    }
                    else {
                        toastMensaje('error', 'Error al borrar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastMensaje('error', 'Error al borrar');
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
