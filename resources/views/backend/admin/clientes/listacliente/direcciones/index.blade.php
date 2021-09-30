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
            <h1>Lista de Direcciones</h1>
        </div>

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


<div class="modal fade" id="modalOpciones">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Opciones</h4>
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
                                    <input type="hidden" id="id-global">
                                </div>

                                <center>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-info" onclick="verInformacion()">Editar</button>
                                    </div>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-info" onclick="verMapaPin()">Ubicación PIN</button>
                                    </div>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-info" onclick="verMapaReal()">Ubicación Real</button>
                                    </div>

                                </center>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalInformacion">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Información</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="formulario-modalInformacion">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group" style="margin-left:20px">
                                    <label>Dirección Comprobada</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="toggle-direccion">
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

            <div class="modal-footer float-right">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editarCliente()">Actualizar</button>
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
            id = {{ $id }};
            var ruta = "{{ url('/admin/cliente/lista/tabla-direcciones') }}/"+id;
            $('#tablaDatatable').load(ruta);
        });
    </script>


    <script>

        function recargar(){
            id = {{ $id }};
            var ruta = "{{ url('/admin/cliente/lista/tabla-direcciones') }}/"+id;
            $('#tablaDatatable').load(ruta);
        }

        function opciones(id){
            $('#modalOpciones').modal('show');
            $('#id-global').val(id);
        }

        function verInformacion(){
            openLoading();
            var id = document.getElementById('id-global').value;
            document.getElementById("formulario-modalInformacion").reset();

            axios.post('/cliente/informacion/direccion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalInformacion').modal('show');

                        if(response.data.cliente.revisado === 0){
                            $("#toggle-direccion").prop("checked", false);
                        }else{
                            $("#toggle-direccion").prop("checked", true);
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

        function editarCliente(){
            var id = document.getElementById('id-global').value;
            var td = document.getElementById('toggle-direccion').checked;

            var toggleDireccion = td ? 1 : 0;

            var formData = new FormData();
            formData.append('id', id);
            formData.append('toggle', toggleDireccion);

            openLoading();

            axios.post('/cliente/actualizar/direccion', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalInformacion').modal('hide');
                        $('#modalOpciones').modal('hide');

                        toastMensaje('success', 'Actualizado correctamente');
                        recargar();

                    }else{
                        toastMensaje('error', 'Error al actualizar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastMensaje('error', 'Error al actualizar');
                });
        }

        function verMapaPin(){
            var id = document.getElementById('id-global').value;
            window.location.href="{{ url('/admin/cliente/mapa/pin') }}/"+id;
        }

        function verMapaReal(){
            var id = document.getElementById('id-global').value;
            window.location.href="{{ url('/admin/cliente/mapa/real') }}/"+id;
        }

    </script>



@endsection
