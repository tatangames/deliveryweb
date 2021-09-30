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
            <h1>Opciones para App</h1>
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
                                    <input type="hidden" id="id-editar">
                                </div>

                                <div class="form-group" style="margin-left:0px">
                                    <label>Estado de Cupones</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="toggle-cupon">
                                        <div class="slider round">
                                            <span class="on">Activo</span>
                                            <span class="off">Inactivo</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label>Comisión Wompi</label>
                                    <input type="number" maxlength="2"  class="form-control" id="comision-editar">
                                </div>

                                <div class="form-group" style="margin-left:0px">
                                    <label>Estado de Tarjeta Monedero</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="toggle-monedero">
                                        <div class="slider round">
                                            <span class="on">Activo</span>
                                            <span class="off">Inactivo</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label>Mensaje Bloqueo Monedero</label>
                                    <input type="text" maxlength="200" class="form-control" id="mensaje-editar">
                                </div>

                                <div class="form-group" style="margin-left:0px">
                                    <label>Borrar Carrito de Compras al realizar una orden</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="toggle-carrito">
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
            var ruta = "{{ URL::to('/admin/extras/tabla/listado') }}";
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/extras/tabla/listado') }}";
            $('#tablaDatatable').load(ruta);
        }

        function informacion(id){
            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post('/extras/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(response.data.info.id);

                        if(response.data.info.estado_cupon === 0){
                            $("#toggle-cupon").prop("checked", false);
                        }else{
                            $("#toggle-cupon").prop("checked", true);
                        }

                        $('#comision-editar').val(response.data.info.comision);

                        if(response.data.info.activo_tarjeta === 0){
                            $("#toggle-monedero").prop("checked", false);
                        }else{
                            $("#toggle-monedero").prop("checked", true);
                        }

                        $('#mensaje-editar').val(response.data.info.mensaje_tarjeta);

                        if(response.data.info.borrar_carrito === 0){
                            $("#toggle-carrito").prop("checked", false);
                        }else{
                            $("#toggle-carrito").prop("checked", true);
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
            var comision = document.getElementById('comision-editar').value;
            var mensaje = document.getElementById('mensaje-editar').value;

            var tcu = document.getElementById('toggle-cupon').checked;
            var tm = document.getElementById('toggle-monedero').checked;
            var tca = document.getElementById('toggle-carrito').checked;

            var toggleCupon = tcu ? 1 : 0;
            var toggleMonedero = tm ? 1 : 0;
            var toggleCarrito = tca ? 1 : 0;

            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(comision === ''){
                toastMensaje('error', 'Comisión es requerido');
                return;
            }

            if(!comision.match(reglaNumeroEntero)) {
                toastMensaje('error', 'Comisión debe ser número entero');
                return;
            }

            if(comision < 0){
                toastMensaje('error', 'Comisión no debe ser negativo');
                return;
            }

            if(comision > 100){
                toastMensaje('error', 'Comisión no puede superar 100');
                return;
            }

            if(mensaje === ''){
                toastMensaje('error', 'Mensaje de bloqueo es requerido');
                return;
            }

            if(mensaje.length > 200){
                toastMensaje('error', 'Mensaje máximo 200 caracteres');
                return;
            }


            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('comision', comision);
            formData.append('mensaje', mensaje);
            formData.append('toggle_cupon', toggleCupon);
            formData.append('toggle_monedero', toggleMonedero);
            formData.append('toggle_carrito', toggleCarrito);

            axios.post('/extras/actualizar', formData, {
            })
                .then((response) => {
                    closeLoading();
                     if(response.data.success === 1){
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
