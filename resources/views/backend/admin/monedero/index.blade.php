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
            <h1>Lista de Compras Monedero</h1>
        </div>
        <br>

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
                                    <label>ID Transacción</label>
                                    <input type="hidden" id="id-editar">
                                    <input type="text" disabled class="form-control" id="transaccion">
                                </div>

                                <div class="form-group">
                                    <label>Código</label>
                                    <input type="text" disabled class="form-control" id="codigo">
                                </div>

                                <div class="form-group">
                                    <label>Es Real</label>
                                    <input type="text" disabled class="form-control" id="real">
                                </div>

                                <div class="form-group">
                                    <label>Esta Aprobada</label>
                                    <input type="text" disabled class="form-control" id="aprobada">
                                </div>

                                <div class="form-group">
                                    <label>Fecha Revisada</label>
                                    <input type="text" disabled class="form-control" id="fecha">
                                </div>

                                <div class="form-group">
                                    <label>Nota</label>
                                    <input type="text" maxlength="300" class="form-control" id="notas">
                                </div>

                                <div class="form-group" style="margin-left:0px">
                                    <label>Confirmar Deposito</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="toggle">
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
            var ruta = "{{ URL::to('/admin/monedero/lista/tabla') }}";
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/monedero/lista/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }


        function informacion(id){
            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post('/admin/monedero/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(response.data.lista.id);
                        $('#transaccion').val(response.data.lista.idtransaccion);
                        $('#codigo').val(response.data.lista.codigo);

                        $('#notas').val(response.data.lista.nota);

                        $('#fecha').val(response.data.fecha);

                        $('#real').val(response.data.fecha);

                        if(response.data.lista.esreal === 0){
                            $('#real').val('No');
                        }else{
                            $('#real').val('Si');
                        }

                        if(response.data.lista.esaprobada === 0){
                            $('#aprobada').val('No');
                        }else{
                            $('#aprobada').val('Si');
                        }

                        if(response.data.lista.revisada === 0){
                            $("#toggle").prop("checked", false);
                        }else{
                            $("#toggle").prop("checked", true);
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
            var to = document.getElementById('toggle').value;
            var nota = document.getElementById('notas').value;

            var toggle = to ? 1 : 0;

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nota', nota);
            formData.append('toggle', toggle);

            axios.post('/admin/monedero/revisar', formData, {
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



    </script>


@endsection
