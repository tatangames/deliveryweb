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
            <h1>Lista de Tipos Servicio</h1>
        </div>
        <button type="button" onclick="abrirModalAgregar()" class="btn btn-info btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Nuevo Tipo Servicio
        </button>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Tipos Servicio</h3>
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
                                    <label>Nombre</label>
                                    <input type="text" maxlength="50" class="form-control" id="nombre-nuevo">
                                </div>
                                <div class="form-group">
                                    <label>Descripción</label>
                                    <input type="text" maxlength="100" class="form-control" id="descripcion-nuevo">
                                </div>
                                <div class="form-group">
                                    <label style="color:#191818">Tipo</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="select-tipos">
                                            @foreach($tipos as $item)
                                                <option value="{{$item->id}}">{{$item->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label>Imagen</label>
                                        <p>Tamaño recomendado de: 128 x 128</p>
                                    </div>
                                    <br>
                                    <div class="col-md-10">
                                        <input type="file" style="color:#191818" id="imagen-nuevo" accept="image/jpeg, image/jpg, image/png"/>
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
                <form id="formulario-editar">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="hidden" id="id-editar">
                                    <input type="text" maxlength="50" class="form-control" id="nombre-editar" placeholder="Nombre tipo servicio">
                                </div>
                                <div class="form-group">
                                    <label>Descripción</label>
                                    <input type="text" maxlength="100" class="form-control" id="descripcion-editar" placeholder="Descripción tipo servicio">
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label>Imagen</label>
                                        <p>Tamaño recomendado de: 128 x 128</p>
                                    </div>
                                    <br>
                                    <div class="col-md-10">
                                        <input type="file" style="color:#191818" id="imagen-editar" accept="image/jpeg, image/jpg, image/png"/>
                                    </div>
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
            var ruta = "{{ URL::to('/admin/tiposervicio/tablas/lista-tipo-servicio') }}";
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/tiposervicio/tablas/lista-tipo-servicio') }}";
            $('#tablaDatatable').load(ruta);
        }

        function abrirModalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function nuevo() {
            var nombre = document.getElementById('nombre-nuevo').value;
            var descripcion = document.getElementById('descripcion-nuevo').value;
            var imagen = document.getElementById('imagen-nuevo');
            var tipos = document.getElementById("select-tipos").value;

            if (nombre === '') {
                toastMensaje('error', 'Nombre es requerido');
                return;
            }

            if(nombre.length > 50){
                toastMensaje('error', 'Nombre máximo 50 caracteres');
                return;
            }

            if (descripcion === '') {
                toastMensaje('error', 'Nombre es requerido');
                return;
            }

            if(descripcion.length > 200){
                toastMensaje('error', 'Descripción máximo 200 caracteres');
                return;
            }

            if(imagen.files && imagen.files[0]){}else{
                toastMensaje('error', 'Imagen es requerida');
                return;
            }

            if(imagen.files && imagen.files[0]){ // si trae imagen
                if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){
                    toastMensaje('error', 'Formato de imagen permitido: .png .jpg .jpeg');
                    return;
                }
            }

            openLoading();

            let formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);
            formData.append('imagen', imagen.files[0]);
            formData.append('tipos', tipos);

            axios.post('/tiposervicio/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if (response.data.success === 1) {
                        toastMensaje('success', 'Registrado correctamente');
                        $('#modalAgregar').modal('hide');
                        recargar();
                    } else {
                        toastMensaje('error', 'Error al registrar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastMensaje('error', 'Error al registrar');
                });
        }

        function verInformacion(id){

            document.getElementById("formulario-editar").reset();
            openLoading();

            axios.post('/tiposervicio/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        $('#modalEditar').modal('show');
                        $('#id-editar').val(response.data.tipo.id);
                        $('#nombre-editar').val(response.data.tipo.nombre);
                        $('#descripcion-editar').val(response.data.tipo.descripcion);

                    }else{
                        toastMensaje('error', 'Información no encontrado');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastMensaje('error', 'Información no encontrado');
                });
        }

        function editar(){

            var id = document.getElementById('id-editar').value;
            var nombre = document.getElementById('nombre-editar').value;
            var descripcion = document.getElementById('descripcion-editar').value;
            var imagen = document.getElementById('imagen-editar');

            if (nombre === '') {
                toastMensaje('error', "Nombre es requerido");
                return;
            }

            if(nombre.length > 50){
                toastMensaje('error', "Nombre máximo 50 caracteres");
                return;
            }

            if (descripcion === '') {
                toastMensaje('error', "Descripción es requerido");
                return;
            }

            if(descripcion.length > 100){
                toastMensaje('error', 'Descripción máximo 100 caracteres');
                return;
            }

            if(imagen.files && imagen.files[0]){ // si trae imagen
                if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){
                    toastMensaje('error', 'Formato de imagen permitido: .png .jpg .jpeg');
                    return;
                }
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);
            formData.append('imagen', imagen.files[0]);

            axios.post('/tiposervicio/editar-tipo', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1) {
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
