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
            <h1>Lista de Zonas</h1>
        </div>

        <button type="button" onclick="abrirModalAgregar()" class="btn btn-info btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Nueva Zona
        </button>

        <button type="button" onclick="modalOpcion()" class="btn btn-info btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Cerrar o Abrir Zonas
        </button>

    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Zonas</h3>
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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nueva Zona</h4>
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
                                    <input type="hidden" id="id-actualizar">
                                    <input type="text" maxlength="100" class="form-control" id="nombre-nuevo" placeholder="Nombre zona">
                                </div>
                                <div class="form-group">
                                    <label>Descripción</label>
                                    <input type="text" maxlength="300" class="form-control" id="descripcion-nuevo" placeholder="Descripción de la zona">
                                </div>
                                <div class="form-group">
                                    <label>Identificador</label>
                                    <input type="text" maxlength="100" class="form-control" id="identificador-nuevo" placeholder="Identificador">
                                </div>
                                <div class="form-group">
                                    <label>Hora abierto</label>
                                    <input type="time" class="form-control" id="horaabierto-nuevo">
                                </div>
                                <div class="form-group">
                                    <label>Hora cerrado</label>
                                    <input type="time" class="form-control" id="horacerrado-nuevo">
                                </div>
                                <div class="form-group">
                                    <label>Tiempo extra (tiempo que se agregara a una nueva orden por zona en Minutos)</label>
                                    <input type="number" value="0" min="0" class="form-control" id="tiempoextra-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Latitud</label>
                                    <input type="text" maxlength="50" class="form-control" id="latitud-nuevo" placeholder="Latitud">
                                </div>

                                <div class="form-group">
                                    <label>Longitud</label>
                                    <input type="text" maxlength="50" class="form-control" id="longitud-nuevo" placeholder="Longitud">
                                </div>

                                <div class="form-group">
                                    <label>Mensaje Bloqueo (cuando no se puede dar servicio a toda una zona)</label>
                                    <input type="text" maxlength="200" class="form-control" id="mensaje-nuevo" placeholder="Mensaje Bloqueo" value="Fuera de servicio temporalmente, volveremos Pronto!">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="verificarNuevo()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- modal editar -->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Zona</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-editar">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="hidden" id="id-editar">
                                    <input type="text" maxlength="100" class="form-control" id="nombre-editar" placeholder="Nombre zona">
                                </div>
                                <div class="form-group">
                                    <label>Descripción</label>
                                    <input type="text" maxlength="300" class="form-control" id="descripcion-editar" placeholder="Descripción de la zona">
                                </div>
                                <div class="form-group">
                                    <label>Identificador</label>
                                    <input type="text" maxlength="100" class="form-control" id="identificador-editar" placeholder="Identificador">
                                </div>
                                <div class="form-group">
                                    <label>Hora abierto</label>
                                    <input type="time" class="form-control" id="horaabierto-editar">
                                </div>
                                <div class="form-group">
                                    <label>Hora cerrado</label>
                                    <input type="time" class="form-control" id="horacerrado-editar">
                                </div>

                                <div class="form-group">
                                    <label>Tiempo extra (tiempo que se agregara a una nueva orden por zona en Minutos)</label>
                                    <input type="number" value="0" min="0" class="form-control" id="tiempoextra-editar">
                                </div>

                                <div class="form-group">
                                    <label>Latitud</label>
                                    <input type="text" maxlength="50" class="form-control" id="latitud-editar" placeholder="Latitud" required>
                                </div>

                                <div class="form-group">
                                    <label>Longitud</label>
                                    <input type="text" maxlength="50" class="form-control" id="longitud-editar" placeholder="Longitud" required>
                                </div>

                            </div>
                            <div class="col-md-6">

                                <div class="form-group" style="margin-left:0px">
                                    <label>Problema de zona</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="toggle-problema">
                                        <div class="slider round">
                                            <span class="on">Activar</span>
                                            <span class="off">Desactivar</span>
                                        </div>
                                    </label>
                                </div>

                                <br>

                                <div class="form-group">
                                    <label>Mensaje Bloqueo (cuando no se puede dar servicio a toda una zona)</label>
                                    <input type="text" maxlength="200" class="form-control" id="mensaje-editar" placeholder="Mensaje Bloqueo">
                                </div>

                                <br>

                                <div class="form-group" style="margin-left:0px">
                                    <label>Disponibilidad Zona (Mostrar/Ocultar en Aplicaciones)</label><br>
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
                <button type="button" class="btn btn-primary" id="btnGuardar" onclick="verificarEditar()">Guardar</button>
            </div>
        </div>
    </div>
</div>


<!-- modal para abrir o cerrar zonas -->
<div class="modal fade" id="modalOpcion">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Abrir o cerrar todas las zonas</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-opcion">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Habilita o Deshabilita todas las zonas y el cliente no podrá ordenar</label>
                                </div>

                                <div class="form-group">
                                    <label>Mensaje que cambiara en todas las zonas</label>
                                    <input type="text" maxlength="200" class="form-control" id="mensaje-cerrado">
                                </div>

                                <div class="form-group" style="margin-left:20px">
                                    <label>Estado (Abierto o Cerrado)</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="toggle-cerrado-abierto">
                                        <div class="slider round">
                                            <span class="on">Cerrar Zona</span>
                                            <span class="off">Abrir Zona</span>
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
                <button type="button" class="btn btn-primary" onclick="verificarCerrado()">Guardar</button>
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
            var ruta = "{{ URL::to('/admin/zona/tablas/zona') }}";
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/zona/tablas/zona') }}";
            $('#tablaDatatable').load(ruta);
        }

        function abrirModalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function verificarNuevo(){

            Swal.fire({
                title: 'Guardar Nueva zona?',
                text: "",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    nuevo();
                }
            })
        }

        function nuevo() {
            var nombre = document.getElementById('nombre-nuevo').value;
            var descripcion = document.getElementById('descripcion-nuevo').value;
            var identificador = document.getElementById('identificador-nuevo').value;
            var horaabierto = document.getElementById('horaabierto-nuevo').value;
            var horacerrado = document.getElementById('horacerrado-nuevo').value;
            var tiempoextra = document.getElementById('tiempoextra-nuevo').value;
            var latitud = document.getElementById("latitud-nuevo").value;
            var longitud = document.getElementById("longitud-nuevo").value;
            var mensaje = document.getElementById("mensaje-nuevo").value;

            if(nombre === ''){
                toastMensaje('error', 'Nombre de zona es Requerido');
                return;
            }

            if(nombre > 100){
                toastMensaje('error', 'Nombre máximo 100 caracteres');
                return;
            }

            if(descripcion.length > 300){
                toastMensaje('error', 'Descripción máximo 300 caracteres');
                return;
            }

            if(identificador === ''){
                toastMensaje('error', 'Identificador de zona es Requerido');
                return;
            }

            if(identificador.length > 100){
                toastMensaje('error', 'Identificador máximo 100 caracteres');
                return;
            }

            if(horaabierto === ''){
                toastMensaje('error', 'Hora Abierto es Requerido');
                return;
            }

            if(horacerrado === ''){
                toastMensaje('error', 'Hora Cerrador es Requerido');
                return;
            }

            if(tiempoextra === ''){
                toastMensaje('error', 'Tiempo Extra es Requerido');
                return;
            }

            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(!tiempoextra.match(reglaNumeroEntero)) {
                toastMensaje('error', 'Tiempo Extra necesita número Entero');
                return;
            }

            if(tiempoextra < 0){
                toastMensaje('error', 'Tiempo Extra no puede ser Negativo');
                return;
            }

            if(tiempoextra > 300){
                toastMensaje('error', 'Tiempo Extra no puede superar 300 minutos');
                return;
            }

            if(latitud === '') {
                toastMensaje('error', 'Latitud es Requerido');
                return;
            }

            if(latitud.length > 50){
                toastMensaje('error', 'Latitud máximo 50 caracteres');
                return;
            }


            if(longitud === '') {
                toastMensaje('error', 'Longitud es Requerido');
                return;
            }

            if(longitud.length > 50){
                toastMensaje('error', 'Longitud máximo 50 caracteres');
                return;
            }

            if(mensaje === ''){
                toastMensaje('error', 'Mensaje bloqueo es Requerido');
                return;
            }

            if(mensaje.length > 200){
                toastMensaje('error', 'Mensaje Bloqueo máximo 200 caracteres');
                return;
            }

            let formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);
            formData.append('horaabierto', horaabierto);
            formData.append('horacerrado', horacerrado);
            formData.append('tiempoextra', tiempoextra);
            formData.append('identificador', identificador);
            formData.append('latitud', latitud);
            formData.append('longitud', longitud);
            formData.append('mensaje', mensaje);

            openLoading();

            axios.post('/zona/nueva-zona', formData, {
            })
                .then((response) => {
                    closeLoading()

                    if (response.data.success === 1) {
                        toastMensaje('error', 'El identificador ya existe');
                    } else if (response.data.success === 2) {
                        $('#modalAgregar').modal('hide');
                        toastMensaje('success', 'Registro Agregado');
                        recargar();
                    } else if (response.data.success === 3) {
                        toastMensaje('error', 'Error al Registrar');
                    }
                    else {
                        toastMensaje('error', 'Error al Registrar');
                    }
                })
                .catch((error) => {
                    toastMensaje('error', 'Error al Registrar');
                    closeLoading()
                });
        }

        function modalOpcion(){
            document.getElementById("formulario-opcion").reset();
            $('#modalOpcion').modal('show');
        }

        function verInformacion(id){

            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post('/zona/informacion-zona',{
                'id': id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        $('#modalEditar').modal('show');
                        $('#id-editar').val(response.data.zona.id);
                        $('#nombre-editar').val(response.data.zona.nombre);
                        $('#identificador-editar').val(response.data.zona.identificador);
                        $('#descripcion-editar').val(response.data.zona.descripcion);
                        $('#horaabierto-editar').val(response.data.zona.hora_abierto_delivery);
                        $('#horacerrado-editar').val(response.data.zona.hora_cerrado_delivery);
                        $('#tiempoextra-editar').val(response.data.zona.tiempo_extra)
                        $('#mensaje-editar').val(response.data.zona.mensaje_bloqueo)

                        $('#latitud-editar').val(response.data.zona.latitud);
                        $('#longitud-editar').val(response.data.zona.longitud);

                        if(response.data.zona.saturacion === 0){
                            $("#toggle-problema").prop("checked", false);
                        }else{
                            $("#toggle-problema").prop("checked", true);
                        }

                        if(response.data.zona.activo === 0){
                            $("#toggle-activo").prop("checked", false);
                        }else{
                            $("#toggle-activo").prop("checked", true);
                        }

                    }else{
                        toastMensaje('error', 'Error al Buscar');
                    }
                })
                .catch((error) => {
                    toastMensaje('error', 'Error al Buscar');
                    closeLoading();
                });
        }

        function verificarEditar(){

            Swal.fire({
                title: 'Editar Registro?',
                text: "",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    editar();
                }
            })
        }

        function editar() {
            var id = document.getElementById('id-editar').value;
            var nombre = document.getElementById('nombre-editar').value;
            var descripcion = document.getElementById('descripcion-editar').value;
            var identificador = document.getElementById('identificador-editar').value;
            var horaabierto = document.getElementById('horaabierto-editar').value;
            var horacerrado = document.getElementById('horacerrado-editar').value;
            var tiempoextra = document.getElementById('tiempoextra-editar').value;

            // toggle problema
            var tp = document.getElementById('toggle-problema').checked;
            // toggle activo
            var ta = document.getElementById('toggle-activo').checked;

            var latitud = document.getElementById("latitud-editar").value;
            var longitud = document.getElementById("longitud-editar").value;
            var mensaje = document.getElementById("mensaje-editar").value;

            var toggleProblema = tp ? 1 : 0;
            var toggleActivo = ta ? 1 : 0;

            if(nombre === ''){
                toastMensaje('error', 'Nombre de zona es Requerido');
                return;
            }

            if(nombre > 100){
                toastMensaje('error', 'Nombre máximo 100 caracteres');
                return;
            }

            if(descripcion.length > 300){
                toastMensaje('error', 'Descripción máximo 300 caracteres');
                return;
            }

            if(identificador === ''){
                toastMensaje('error', 'Identificador de zona es Requerido');
                return;
            }

            if(identificador.length > 100){
                toastMensaje('error', 'Identificador máximo 100 caracteres');
                return;
            }

            if(horaabierto === ''){
                toastMensaje('error', 'Hora Abierto es Requerido');
                return;
            }

            if(horacerrado === ''){
                toastMensaje('error', 'Hora Cerrador es Requerido');
                return;
            }

            if(tiempoextra === ''){
                toastMensaje('error', 'Tiempo Extra es Requerido');
                return;
            }

            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(!tiempoextra.match(reglaNumeroEntero)) {
                toastMensaje('error', 'Tiempo Extra necesita número Entero');
                return;
            }

            if(tiempoextra < 0){
                toastMensaje('error', 'Tiempo Extra no puede ser Negativo');
                return;
            }

            if(tiempoextra > 300){
                toastMensaje('error', 'Tiempo Extra no puede superar 300 minutos');
                return;
            }

            if(latitud === '') {
                toastMensaje('error', 'Latitud es Requerido');
                return;
            }

            if(latitud.length > 50){
                toastMensaje('error', 'Latitud máximo 50 caracteres');
                return;
            }


            if(longitud === '') {
                toastMensaje('error', 'Longitud es Requerido');
                return;
            }

            if(longitud.length > 50){
                toastMensaje('error', 'Longitud máximo 50 caracteres');
                return;
            }

            if(mensaje === ''){
                toastMensaje('error', 'Mensaje bloqueo es Requerido');
                return;
            }

            if(mensaje.length > 200){
                toastMensaje('error', 'Mensaje Bloqueo máximo 200 caracteres');
                return;
            }

            openLoading();

            let formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);
            formData.append('identificador', identificador);
            formData.append('horaabierto', horaabierto);
            formData.append('horacerrado', horacerrado);
            formData.append('tiempoextra', tiempoextra);
            formData.append('togglep', toggleProblema);
            formData.append('togglea', toggleActivo);
            formData.append('latitud', latitud);
            formData.append('longitud', longitud);
            formData.append('mensaje', mensaje);

            axios.post('/zona/editar-zona', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastMensaje('error', 'Identificador ya existe');
                    }

                    else if (response.data.success === 2) {
                        $('#modalEditar').modal('hide');
                        toastMensaje('success', 'Información Actualizada');
                        recargar();
                    } else {
                        toastMensaje('error', 'Error al Editar');
                    }
                })
                .catch((error) => {
                    toastMensaje('error', 'Error al Editar');
                    closeLoading();
                });
        }

        function vistaPoligonos(id){
            window.location.href="{{ url('/admin/zona/poligono') }}/"+id;
        }

        function verMapa(id){
            window.location.href="{{ url('/admin/zona/ver-mapa/') }}/"+id;
        }


        function verificarCerrado(){

            Swal.fire({
                title: 'Cerrar o Abrir zonas?',
                text: "",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    cerrarAbrir();
                }
            })
        }

        function cerrarAbrir(){
            var toggle = document.getElementById('toggle-cerrado-abierto').checked;
            var mensaje = document.getElementById("mensaje-cerrado").value;

            var tt = toggle ? 1 : 0;

            if(tt === 1){
                if (mensaje === '') {
                    toastMensaje('error', 'Mensaje es Requerido');
                    return;
                }
            }

            if(mensaje.length > 200){
                toastMensaje('error', 'Mensaje máximo 200 caracteres');
                return;
            }

            openLoading();
            let formData = new FormData();
            formData.append('toggle', tt);
            formData.append('mensaje', mensaje);

            axios.post('/zona/actualizar-marcados', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if (response.data.success === 1) {
                        $('#modalOpcion').modal('hide');
                        toastMensaje('success', 'Información Actualizada');
                        recargar();
                    } else {
                        toastMensaje('error', 'Error al Actualizar');
                    }
                })
                .catch((error) => {
                    toastMensaje('error', 'Error al Actualizar');
                    closeLoading();
                });
        }


    </script>


@endsection
