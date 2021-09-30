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
            <h1>Lista de Propietarios</h1>
        </div>
        <br>
        <button type="button" onclick="modalAgregar()" class="btn btn-success btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Nuevo propietario
        </button>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Motoristas</h3>
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
                <h4 class="modal-title">Nuevo Motorista</h4>
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
                                    <label>Identificador</label>
                                    <input type="text" maxlength="100" class="form-control" id="identificador" placeholder="Identificador unico">
                                </div>

                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" maxlength="50" class="form-control" id="nombre-nuevo" placeholder="Nombre">
                                </div>

                                <div class="form-group">
                                    <label>Telefono</label>
                                    <input type="text" maxlength="20" class="form-control" id="telefono-nuevo" placeholder="Telefono">
                                </div>

                                <div class="form-group">
                                    <label>Contraseña</label>
                                    <input type="text" maxlength="16" class="form-control" id="pass-nuevo" placeholder="12345678">
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
                <h4 class="modal-title">Editar Motorista</h4>
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
                                    <label>Fecha Registro</label>
                                    <input type="text" disabled class="form-control" id="fecha">
                                </div>

                                <div class="form-group">
                                    <label>Identificador</label>
                                    <input type="text" maxlength="100" class="form-control" id="identificador-editar" placeholder="Identificador único">
                                </div>

                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="hidden" id="id-editar">
                                    <input type="text" maxlength="50" class="form-control" id="nombre-editar" placeholder="Nombre">
                                </div>
                                <div class="form-group">
                                    <label>Telefono</label>
                                    <input type="text" maxlength="20" class="form-control" id="telefono-editar" placeholder="Telefono">
                                </div>

                                <div class="form-group" style="margin-left:0px">
                                    <label>Activo</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="activo-editar">
                                        <div class="slider round">
                                            <span class="on">Si</span>
                                            <span class="off">No</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-group" style="margin-left:0px">
                                    <label>Si se activa, se resetea password a '12345678'</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="password-editar">
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

<!-- modal promedio -->
<div class="modal fade" id="modalPromedio">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Promedio de calificacion</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-promedio">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Promedio global</label>
                                    <input type="text" disabled class="form-control" id="promedio">
                                </div>

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
            var ruta = "{{ URL::to('/admin/motoristas/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/motoristas/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function nuevo(){
            var identi = document.getElementById('identificador').value;
            var nombre = document.getElementById('nombre-nuevo').value;
            var telefono = document.getElementById('telefono-nuevo').value;
            var pass = document.getElementById('pass-nuevo').value;

            if(identi === ''){
                toastMensaje('error', 'Identificador es requerido');
                return;
            }

            if(identi.length > 100){
                toastMensaje('error', 'Identificador máximo 100 caracteres');
                return;
            }

            if(nombre === ''){
                toastMensaje('error', 'Nombre es requerido');
                return;
            }

            if(nombre.length > 50){
                toastMensaje('error', 'Nombre máximo 50 caracteres');
                return;
            }

            if(telefono === ''){
                toastMensaje('error', 'Télefono es requerido');
                return;
            }

            if(telefono.length > 20){
                toastMensaje('error', 'Télefono máximo 20 caracteres');
                return;
            }

            if(pass === ''){
                toastMensaje('error', 'Contraseña es requerido');
                return;
            }

            if(pass.length < 4) {
                toastMensaje('error', 'Mínimo 4 caracteres para Contraseña');
                return;
            }

            if(pass.length > 16) {
                toastMensaje('error', 'Máximo 16 caracteres para Contraseña');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('identi', identi);
            formData.append('nombre', nombre);
            formData.append('telefono', telefono);
            formData.append('password', pass);

            axios.post('/motoristas/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastMensaje('error', 'Identificador ya existe');
                    }
                    else if(response.data.success === 2){
                        toastMensaje('error', 'Télefono ya registrado');
                    }
                    else if(response.data.success === 3){
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

        function informacion(id){
           openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post('/motoristas/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(response.data.motorista.id);
                        $('#nombre-editar').val(response.data.motorista.nombre);
                        $('#telefono-editar').val(response.data.motorista.telefono);
                        $('#identificador-editar').val(response.data.motorista.identificador);

                        $('#fecha').val(response.data.fecha);

                        if(response.data.motorista.activo === 0){
                            $("#activo-editar").prop("checked", false);
                        }else{
                            $("#activo-editar").prop("checked", true);
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
            var telefono = document.getElementById('telefono-editar').value;
            var identificador = document.getElementById('identificador-editar').value;

            var ta = document.getElementById('activo-editar').checked;
            var tp = document.getElementById('password-editar').checked;

            var toggleActivo = ta ? 1 : 0;
            var togglePassword = tp ? 1 : 0;

            if(identificador === ''){
                toastMensaje('error', 'Identificador es requerido');
                return;
            }

            if(identificador.length > 100){
                toastMensaje('error', 'Identificador máximo 100 caracteres');
                return;
            }

            if(nombre === ''){
                toastMensaje('error', 'Nombre es requerido');
                return;
            }

            if(nombre.length > 50){
                toastMensaje('error', 'Nombre máximo 50 caracteres');
                return;
            }

            if(telefono === ''){
                toastMensaje('error', 'Télefono es requerido');
                return;
            }

            if(telefono.length > 20){
                toastMensaje('error', 'Télefono máximo 20 caracteres');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('telefono', telefono);
            formData.append('cbactivo', toggleActivo);
            formData.append('identificador', identificador);
            formData.append('checkpassword', togglePassword);

            axios.post('/motoristas/editar', formData, {
            })
                .then((response) => {
                    closeLoading();

                     if(response.data.success === 1){
                        toastMensaje('error', 'Teléfono ya esta registrado');
                    }
                    else if(response.data.success === 2){
                        toastMensaje('error', 'Identificador ya esta registrado');
                    }

                    else if(response.data.success === 3){
                        toastMensaje('success', 'Actualizado correctamente');
                        $('#modalEditar').modal('hide');
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


        function modalPromedio(id){
            document.getElementById("formulario-promedio").reset();

            axios.post('/motoristas/promedio',{
                'id': id
            })
                .then((response) => {

                    if(response.data.success === 1){
                        $('#modalPromedio').modal('show');
                        $('#promedio').val(response.data.promedio);

                    } else if(response.data.success === 2){
                        toastMensaje('success', 'El motorista no tiene calificación aun');
                    }
                    else{
                        toastMensaje('error', 'Error la buscar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastMensaje('error', 'Error la buscar');
                });
        }


    </script>


@endsection
