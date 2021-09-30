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
            <h1>Lista de Asignaciones</h1>
        </div>
        <br>
        <button type="button" onclick="modalAgregar()" class="btn btn-success btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Nuevo Asignación
        </button>

        <button type="button" onclick="vistaBorrar()" class="btn btn-success btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Borrar Todo
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
                <h4 class="modal-title">Nueva Asignación</h4>
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
                                    <label style="color:#191818">Servicio identificador</label>
                                    <br>
                                    <div>

                                        <select class="form-control" id="servicio">
                                            @foreach($servicio as $item)
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label style="color:#191818">Cupón</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="cupones">
                                            @foreach($cupon as $item)
                                                <option value="{{$item->id}}">{{$item->cupon}}</option>
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

<!-- modal nuevo -->
<div class="modal fade" id="modalBorrar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Borrar asignacion</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-borrar">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <input type="hidden" id="id-editar">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger" onclick="borrar()">Borrar</button>
            </div>
        </div>
    </div>
</div>

<!-- borrar todos los registros -->
<div class="modal fade" id="modalVista">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Borrar todas las asignaciones</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-vista">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger" onclick="borrarTodo()">Borrar</button>
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
            var ruta = "{{ URL::to('/admin/cupones/lista/tabla/servicios') }}";
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/cupones/lista/tabla/servicios') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalBorrar(id){
            $('#id-editar').val(id);
            $('#modalBorrar').modal('show');
        }

        function modalAgregar(){
            $('#modalAgregar').modal('show');
        }

        function vistaBorrar(){
            $('#modalVista').modal('show');
        }

        function vistaGlobal(){
            $('#modalGlobal').modal('show');
        }

        function nuevo(){
            var servicio = document.getElementById('servicio').value;
            var cupones = document.getElementById('cupones').value;

            openLoading();
            var formData = new FormData();

            formData.append('servicio', servicio);
            formData.append('cupon', cupones);

            axios.post('/admin/cupones/servicio/agregar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if (response.data.success === 1) {
                        toastMensaje('error', 'Esta asignación ya existe');
                    } else if(response.data.success === 2){
                        toastMensaje('success', 'Registrado correctamente');
                        $('#modalAgregar').modal('hide');
                        recargar();
                    }
                    else {
                        toastMensaje('error', 'Error al registrar');
                    }
                })
                .catch((error) => {
                    toastMensaje('error', 'Error al registrar');
                    closeLoading();
                });
        }



        function borrar(){
            var id = document.getElementById('id-editar').value;

            openLoading();
            axios.post('/admin/cupones/servicio/borrar',{
                'id': id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastMensaje('success', 'Eliminado correctamente');
                        $('#modalBorrar').modal('hide');
                        recargar();
                    }else{
                        toastMensaje('error', 'Error al eliminar');
                    }
                })
                .catch((error) => {
                    toastMensaje('error', 'Error al eliminar');
                    closeLoading();
                });
        }

        function borrarTodo(){

            openLoading();
            axios.post('/admin/cupones/servicio/borrar-global',{
                'id': 0
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastMensaje('success', 'Eliminado correctamente');
                        $('#modalVista').modal('hide');
                        recargar();
                    }else{
                        toastMensaje('error', 'Error al eliminar');
                    }
                })
                .catch((error) => {
                    toastMensaje('error', 'Error al eliminar');
                    closeLoading();
                });
        }



    </script>


@endsection
