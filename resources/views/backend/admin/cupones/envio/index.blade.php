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
            <h1>Lista de Cupones para Envío</h1>
        </div>
        <br>
        <button type="button" onclick="modalAgregar()" class="btn btn-success btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Nuevo Asignación
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
                                    <label style="color:#191818">Cupones Tipo Envío</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="select-envio">
                                            @foreach($cupon as $item)
                                                <option value="{{$item->id}}">{{$item->cupon}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Mínimo para aplicar</label>
                                    <input type="number" class="form-control" id="dinero">
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
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Información</h4>
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
                                    <label>Mínimo para aplicar</label>
                                    <input type="number" class="form-control" id="dinero-editar">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="editar()">Actualizar</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalBorrar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Borrar asignación</h4>
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
                                    <input type="hidden" id="id-global">
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
            var ruta = "{{ url('/admin/cupon/lista/tabla/envio') }}";
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/cupon/lista/tabla/envio') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalBorrar(id){
            $('#id-global').val(id);
            $('#modalBorrar').modal('show');
        }

        function modalAgregar(){
            $('#modalAgregar').modal('show');
        }

        function vistaBorrar(){
            $('#modalVista').modal('show');
        }

        function informacion(id){

            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post('/admin/cupon/lista/envio-informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');
                        $('#id-global').val(response.data.lista.id);
                        $('#dinero-editar').val(response.data.lista.dinero);
                    }else{
                        toastMensaje('error', 'Información no encontrada');
                    }

                })
                .catch((error) => {
                    closeLoading();
                    toastMensaje('error', 'Información no encontrada');
                });

        }

        function nuevo(){
            var cupon = document.getElementById('select-envio').value;
            var dinero = document.getElementById('dinero').value;

            if(dinero === ''){
                toastMensaje('error', 'Mínimo es requerido');
                return;
            }

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            if(!dinero.match(reglaNumeroDecimal)) {
                toastMensaje('error', 'Mínimo debe ser número decimal');
                return;
            }

            if(dinero < 0){
                toastMensaje('error', 'Mínimo no debe ser negativo');
                return;
            }

            if(dinero > 1000000){
                toastMensaje('error', 'Máximo 1 millón');
                return;
            }

            openLoading();
            var formData = new FormData();

            formData.append('cupon', cupon);
            formData.append('dinero', dinero);

            axios.post('/admin/cupon/lista/envio-nuevo', formData, {
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

        function editar(){
            var id = document.getElementById('id-global').value;
            var dinero = document.getElementById('dinero-editar').value;

            if(dinero === ''){
                toastMensaje('error', 'Mínimo es requerido');
                return;
            }

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            if(!dinero.match(reglaNumeroDecimal)) {
                toastMensaje('error', 'Mínimo debe ser número decimal');
                return;
            }

            if(dinero < 0){
                toastMensaje('error', 'Mínimo no debe ser negativo');
                return;
            }

            if(dinero > 1000000){
                toastMensaje('error', 'Máximo 1 millón');
                return;
            }

            openLoading();
            var formData = new FormData();

            formData.append('id', id);
            formData.append('dinero', dinero);

            axios.post('/admin/cupon/lista/envio-editar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastMensaje('success', 'Actualizado correctamente');
                        $('#modalEditar').modal('hide');
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
            var id = document.getElementById('id-global').value;

            openLoading();
            axios.post('/admin/cupon/lista/envio-borrar',{
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


    </script>


@endsection
