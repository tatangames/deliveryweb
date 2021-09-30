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
                <h3 class="card-title">Listados</h3>
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

<!-- modal nuevo-->
<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo propietario</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-agregar">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label style="color:#191818">Servicios identificador</label>
                                    <br>
                                    <div>
                                        <select id="selectservicio" class="form-control">
                                            @foreach($servicios as $item)
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" maxlength="100" class="form-control" id="nombre-nuevo" placeholder="Nombre">
                                </div>

                                <div class="form-group">
                                    <label>Teléfono</label>
                                    <input type="text" maxlength="20" class="form-control" id="telefono-nuevo" placeholder="Telefono">
                                </div>

                                <div class="form-group">
                                    <label>Contraseña</label>
                                    <input type="text" maxlength="16" class="form-control" id="pass-nuevo" placeholder="12345678">
                                </div>

                                <div class="form-group">
                                    <label>Correo</label>
                                    <input type="text" maxlength="100" class="form-control" id="correo-nuevo" placeholder="Correo">
                                </div>

                                <div class="form-group" style="margin-left:20px">
                                    <label>Bloquear propietario para que no pueda editar productos</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="bloqueado-nuevo">
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
                <button type="button" class="btn btn-primary" onclick="nuevo()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- modal editar-->
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
                                    <label style="color:#191818">Servicios identificador</label>
                                    <br>
                                    <div>
                                        <select id="selectservicio-editar" class="form-control">

                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="hidden" id="id-editar">
                                    <input type="text" maxlength="100" class="form-control" id="nombre-editar" placeholder="Nombre">
                                </div>

                                <div class="form-group">
                                    <label>Teléfono</label>
                                    <input type="text" maxlength="20" class="form-control" id="telefono-editar" placeholder="Telefono">
                                </div>

                                <div class="form-group">
                                    <label>Correo</label>
                                    <input type="text" maxlength="100" class="form-control" id="correo-editar" placeholder="Correo">
                                </div>

                                <div class="form-group" style="margin-left:20px">
                                    <label>Activo</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="activo-editar">
                                        <div class="slider round">
                                            <span class="on">Si</span>
                                            <span class="off">No</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-group" style="margin-left:20px">
                                    <label>Bloquear propietario para que no pueda editar productos</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="bloqueado">
                                        <div class="slider round">
                                            <span class="on">Si</span>
                                            <span class="off">No</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-group" style="margin-left:20px">
                                    <label>Si se activa, se resetea la contraseña a '12345678'</label><br>
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
            var ruta = "{{ URL::to('/admin/propietarios/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/propietarios/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar(){
            document.getElementById("formulario-agregar").reset();
            $('#modalAgregar').modal('show');
        }

        function nuevo(){
            var identificador = document.getElementById('selectservicio').value;
            var nombre = document.getElementById('nombre-nuevo').value;
            var telefono = document.getElementById('telefono-nuevo').value;
            var correo = document.getElementById('correo-nuevo').value;
            var pass = document.getElementById('pass-nuevo').value;
            var tb = document.getElementById('bloqueado-nuevo').checked;

            var toggle = tb ? 1 : 0;

            if(nombre === ''){
                toastMensaje('error', 'Nombre es requerido');
                return;
            }

            if(nombre.length > 100){
                toastMensaje('error', 'Nombre máximo 100 caracteres');
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

            if(correo === ''){
                toastMensaje('error', 'Correo eléctronico es requerido');
                return;
            }

            if(correo.length > 100){
                toastMensaje('error', 'Correo máximo 100 caracteres');
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
            formData.append('identificador', identificador);
            formData.append('nombre', nombre);
            formData.append('telefono', telefono);
            formData.append('correo', correo);
            formData.append('bloqueado', toggle);

            axios.post('/propietarios/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastMensaje('error', 'El correo ya se encuentra Registrado');
                    }

                    else if(response.data.success === 2){
                        toastMensaje('error', 'El Télefono ya se encuentra Registrado');
                    }

                    else if (response.data.success === 3) {
                        toastMensaje('success', 'Actualizado correctamente');
                        $('#modalAgregar').modal('hide');
                        recargar();
                    }
                    else {
                        toastMensaje('error', 'Error al Registrar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastMensaje('error', 'Error al Registrar');
                });
        }

        function informacion(id){
            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post('/propietarios/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){


                        document.getElementById("selectservicio-editar").options.length = 0;

                        $.each(response.data.servicios, function( key, val ){
                            if(response.data.propietario.servicios_id === val.id){
                                $('#selectservicio-editar').append('<option value="' +val.id +'" selected="selected">'+val.identificador+'</option>');
                            }else{
                                $('#selectservicio-editar').append('<option value="' +val.id +'">'+val.identificador+'</option>');
                            }
                        });

                        $('#id-editar').val(response.data.propietario.id);
                        $('#nombre-editar').val(response.data.propietario.nombre);
                        $('#correo-editar').val(response.data.propietario.correo);
                        $('#telefono-editar').val(response.data.propietario.telefono);

                        if(response.data.propietario.activo === 0){
                            $("#activo-editar").prop("checked", false);
                        }else{
                            $("#activo-editar").prop("checked", true);
                        }

                        if(response.data.propietario.bloqueado === 0){
                            $("#bloqueado").prop("checked", false);
                        }else{
                            $("#bloqueado").prop("checked", true);
                        }

                        $('#modalEditar').modal('show');

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
            var identificador = document.getElementById('selectservicio-editar').value;
            var nombre = document.getElementById('nombre-editar').value;
            var telefono = document.getElementById('telefono-editar').value;
            var correo = document.getElementById('correo-editar').value;
            var ta = document.getElementById('activo-editar').checked;
            var tb = document.getElementById('bloqueado').checked;
            var tp = document.getElementById('password-editar').checked;

            var toggleActivo = ta ? 1 : 0;
            var toggleBloqueado = tb ? 1 : 0;
            var togglePassword = tp ? 1 : 0;

            if(nombre === ''){
                toastMensaje('error', 'Nombre es requerido');
                return;
            }

            if(nombre.length > 100){
                toastMensaje('error', 'Nombre máximo 100 caracteres');
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

            if(correo === ''){
                toastMensaje('error', 'Correo eléctronico es requerido');
                return;
            }

            if(correo.length > 100){
                toastMensaje('error', 'Correo máximo 100 caracteres');
                return;
            }


            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('identificador', identificador);
            formData.append('nombre', nombre);
            formData.append('telefono', telefono);
            formData.append('correo', correo);
            formData.append('activo', toggleActivo);
            formData.append('bloqueado', toggleBloqueado);
            formData.append('passcheck', togglePassword);

            axios.post('/propietarios/editar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastMensaje('error', 'El correo ya esta registrado');
                    } else if(response.data.success === 2){
                        toastMensaje('error', 'El Télefono ya esta registrado');
                    }
                    else if(response.data.success === 3){
                        toastMensaje('success', 'Actualizado correctamente');
                        $('#modalEditar').modal('hide');
                        recargar();
                    }
                    else {
                        toastMensaje('error', 'Error al Actualizar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastMensaje('error', 'Error al Actualizar');
                });
        }


    </script>


@endsection
