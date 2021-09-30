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
            <h1>Lista de Clientes</h1>
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
                                        <button type="button" class="btn btn-info" onclick="verDirecciones()">Direcciones</button>
                                    </div>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-info" onclick="verInformacion()">Editar</button>
                                    </div>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-info" onclick="verHistorial()">Historial</button>
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
                <h4 class="modal-title">Información cliente</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="formulario-modalInformacion">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label style="color:#191818">Código dispositivo</label>
                                    <br>
                                    <input id="codigo-dispositivo" disabled class="form-control">
                                </div>

                                <div class="form-group" style="margin-left:20px">
                                    <label>Activo</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="toggle-activo">
                                        <div class="slider round">
                                            <span class="on">Activar</span>
                                            <span class="off">Desactivar</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-group" style="margin-left:20px">
                                    <label>Modificar Contraseña a "12345678"</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="toggle-pass">
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
            var ruta = "{{ URL::to('/admin/cliente/tabla/listado') }}";
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ URL::to('/admin/cliente/tabla/listado') }}";
            $('#tablaDatatable').load(ruta);
        }

        function verTodaOpciones(id){
            $('#modalOpciones').modal('show');
            $('#id-global').val(id);
        }

        function verDirecciones(){
            var id = document.getElementById('id-global').value;
            window.location.href="{{ url('/admin/cliente/lista/direcciones') }}/"+id;
        }

        function verInformacion(){
            openLoading();
            var id = document.getElementById('id-global').value;
            document.getElementById("formulario-modalInformacion").reset();

            axios.post('/cliente/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalInformacion').modal('show');

                        $('#codigo-dispositivo').val(response.data.cliente.token_fcm);

                        if(response.data.cliente.activo === 0){
                            $("#toggle-activo").prop("checked", false);
                        }else{
                            $("#toggle-activo").prop("checked", true);
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
            var ta = document.getElementById('toggle-activo').checked;
            var tp = document.getElementById('toggle-pass').checked;

            var toggleActivo = ta ? 1 : 0;
            var togglePass = tp ? 1 : 0;

            var formData = new FormData();
            formData.append('id', id);
            formData.append('toggle', toggleActivo);
            formData.append('togglepass', togglePass);

            openLoading();

            axios.post('/cliente/actualizar/informacion', formData, {
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


        function verHistorial(){
            var id = document.getElementById('id-global').value;
            window.location.href="{{ url('/admin/cliente/historial') }}/"+id;
        }


    </script>



@endsection
