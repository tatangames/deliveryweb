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
            <h1>Lista de Cupones</h1>
        </div>
        <button type="button" onclick="modalAgregar()" class="btn btn-success btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Nuevo Cupón
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

<!-- modal nuevo-->
<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Cupón</h4>
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
                                    <label style="color:#191818">Tipo Cupón</label>
                                    <br>
                                    <div>
                                        <select id="select-cupon" class="form-control">
                                            @foreach($tipocupon as $item)
                                                <option value="{{$item->id}}">{{$item->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" maxlength="100" class="form-control" id="nombre-nuevo" placeholder="Nombre">
                                </div>

                                <div class="form-group">
                                    <label>Uso Límite</label>
                                    <input type="number" class="form-control" id="limite-nuevo" placeholder="Límite">
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
                                    <label style="color:#191818">Tipo Cupón</label>
                                    <br>
                                    <div>
                                        <select id="selectcupon-editar" class="form-control">

                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="hidden" id="id-editar">
                                    <input type="text" maxlength="100" class="form-control" id="nombre-editar" placeholder="Nombre">
                                </div>

                                <div class="form-group">
                                    <label>Uso Límite</label>
                                    <input type="number" class="form-control" id="limite-editar" placeholder="Límite">
                                </div>

                                <div class="form-group" style="margin-left:20px">
                                    <label>Disponible</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="activo-editar">
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
            var ruta = "{{ URL::to('/admin/cliente/lista/tabla/cupones') }}";
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/cliente/lista/tabla/cupones') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar(){
            document.getElementById("formulario-agregar").reset();
            $('#modalAgregar').modal('show');
        }

        function nuevo(){
            var tipocupon = document.getElementById('select-cupon').value;
            var nombre = document.getElementById('nombre-nuevo').value;
            var limite = document.getElementById('limite-nuevo').value;

            if(nombre === ''){
                toastMensaje('error', 'Nombre es requerido');
                return;
            }

            if(nombre.length > 100){
                toastMensaje('error', 'Nombre máximo 100 caracteres');
                return;
            }

            if(limite === ''){
                toastMensaje('error', 'Límite es requerido');
                return;
            }

            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(!limite.match(reglaNumeroEntero)) {
                toastMensaje('error', 'Límite debe ser número Entero')
                return;
            }

            if(limite > 1000000){
                toastMensaje('error', 'Máximo 1 millón de límite')
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('tipocupon', tipocupon);
            formData.append('nombre', nombre);
            formData.append('limite', limite);

            axios.post('/cupones/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastMensaje('error', 'Nombre Cupón ya existe');
                    }
                    else if(response.data.success === 2){
                        toastMensaje('success', 'Agregado correctamente');
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

            axios.post('/cupones/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        document.getElementById("selectcupon-editar").options.length = 0;

                        $.each(response.data.tipocupon, function( key, val ){
                            if(response.data.lista.tipo_cupon_id === val.id){
                                $('#selectcupon-editar').append('<option value="' +val.id +'" selected="selected">'+val.nombre+'</option>');
                            }else{
                                $('#selectcupon-editar').append('<option value="' +val.id +'">'+val.nombre+'</option>');
                            }
                        });

                        $('#id-editar').val(response.data.lista.id);
                        $('#nombre-editar').val(response.data.lista.cupon);
                        $('#limite-editar').val(response.data.lista.uso_limite);

                        if(response.data.lista.activo === 0){
                            $("#activo-editar").prop("checked", false);
                        }else{
                            $("#activo-editar").prop("checked", true);
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

            var tipocupon = document.getElementById('selectcupon-editar').value;
            var nombre = document.getElementById('nombre-editar').value;
            var limite = document.getElementById('limite-editar').value;
            var ta = document.getElementById('activo-editar').checked;

            var toggleActivo = ta ? 1 : 0;

            if(nombre === ''){
                toastMensaje('error', 'Nombre es requerido');
                return;
            }

            if(nombre.length > 100){
                toastMensaje('error', 'Nombre máximo 100 caracteres');
                return;
            }

            if(limite === ''){
                toastMensaje('error', 'Límite es requerido');
                return;
            }

            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(!limite.match(reglaNumeroEntero)) {
                toastMensaje('error', 'Límite debe ser número Entero')
                return;
            }

            if(limite > 1000000){
                toastMensaje('error', 'Máximo 1 millón de límite')
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('tipocupon', tipocupon);
            formData.append('nombre', nombre);
            formData.append('limite', limite);
            formData.append('toggle', toggleActivo);

            axios.post('/cupones/editar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastMensaje('error', 'Nombre Cupón ya existe');
                    }

                    else if(response.data.success === 2){
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
