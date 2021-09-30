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
            <h1>Lista de Etiquetas</h1>
        </div>
        <br>
        <button type="button" onclick="modalAgregar()" class="btn btn-success btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Nueva Etiqueta
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

<div class="modal fade" id="modalOpciones" style="z-index:100000">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Opciones</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-global">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <input type="hidden" id="id-global">
                                </div>

                                <center>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-info" onclick="modal1()">Información Orden</button>
                                    </div>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-info" onclick="modal2()">Información cliente</button>
                                    </div>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-info" onclick="verMapaPin()">Ubicación Cliente</button>
                                    </div>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-info" onclick="verMapaReal()">Ubicación Real cliente</button>
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



<!-- modal editar -->
<div class="modal fade" id="modalEditar" style="z-index:1000000000">
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
                                    <label>Fecha Estado 2 (negocio envío tiempo de espera)</label>
                                    <input type="text" disabled class="form-control" id="fecha2">
                                </div>

                                <div class="form-group">
                                    <label>Tiempo dado por propietario</label>
                                    <input type="text" disabled class="form-control" id="tiempo">
                                </div>

                                <div class="form-group">
                                    <label>Tiempo de Zona</label>
                                    <input type="text" disabled class="form-control" id="tiempozona">
                                </div>

                                <div class="form-group">
                                    <label>Fecha Estado 3 (cliente acepto esperar)</label>
                                    <input type="text" disabled class="form-control" id="fecha3">
                                </div>

                                <div class="form-group">
                                    <label>Fecha Estado 4 (negocio inicio preparación)</label>
                                    <input type="text" disabled class="form-control" id="fecha4">
                                </div>

                                <div class="form-group">
                                    <label>Fecha Estado 5 (negocio termino de preparar)</label>
                                    <input type="text" disabled class="form-control" id="fecha5">
                                </div>

                                <div class="form-group">
                                    <label>Fecha Estado 6 (motorista inicio entrega)</label>
                                    <input type="text" disabled class="form-control" id="fecha6">
                                </div>

                                <div class="form-group">
                                    <label>Fecha Estado 7 (motorista completo la entrega)</label>
                                    <input type="text" disabled class="form-control" id="fecha7">
                                </div>

                                <div class="form-group">
                                    <label>Fecha Estado 8 (orden cancelada)</label>
                                    <input type="text" disabled class="form-control" id="fecha8">
                                </div>

                                <div class="form-group">
                                    <label>Ganancia Motorista</label>
                                    <input type="text" disabled class="form-control" id="ganancia">
                                </div>

                                <div class="form-group">
                                    <label>Versión App</label>
                                    <input type="text" disabled class="form-control" id="version">
                                </div>

                                <div class="form-group">
                                    <label>Dirección revisada</label>
                                    <input type="text" disabled class="form-control" id="revisado">
                                </div>

                                <div class="form-group">
                                    <label>Orden para Negocio Privado</label>
                                    <input type="text" disabled class="form-control" id="privado">
                                </div>

                                <div class="form-group">
                                    <label>Comisión aplicada</label>
                                    <input type="text" disabled class="form-control" id="comision">
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


<div class="modal fade" id="modalCliente" style="z-index:1000000000">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cliente</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-cliente">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Zona</label>
                                    <input type="text" disabled class="form-control" id="zona-1">
                                </div>

                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" disabled class="form-control" id="nombre-1">
                                </div>

                                <div class="form-group">
                                    <label>Dirección</label>
                                    <input type="text" disabled class="form-control" id="direccion-1">
                                </div>

                                <div class="form-group">
                                    <label>Número de Casa</label>
                                    <input type="text" disabled class="form-control" id="numero-1">
                                </div>

                                <div class="form-group">
                                    <label>Punto de Referencia</label>
                                    <input type="text" disabled class="form-control" id="punto-1">
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
            var ruta = "{{ URL::to('/admin/ordenes/lista/tabla') }}";
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/ordenes/lista/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalglobal(id){
            $('#id-global').val(id);
            $('#modalOpciones').modal('show');
        }

        function modal1(){

            var id = document.getElementById('id-global').value;

            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post('/admin/ordenes/lista/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');

                        $.each(response.data.lista, function( key, val ){

                            $('#fecha2').val(val.fecha_2);
                            $('#tiempo').val(val.hora_2);
                            $('#tiempozona').val(val.copiatiempo);

                            $('#fecha3').val(val.fecha_3)
                            $('#fecha4').val(val.fecha_4)
                            $('#fecha5').val(val.fecha_5)
                            $('#fecha6').val(val.fecha_6)
                            $('#fecha7').val(val.fecha_7)
                            $('#fecha8').val(val.fecha_8)
                            $('#ganancia').val(val.ganancia_motorista)
                            $('#version').val(val.version)
                            $('#revisado').val(val.revisado)
                            $('#privado').val(val.privado)
                            $('#comision').val(val.comision)
                        });

                    }else{
                        toastMensaje('error', 'Información no encontrada');
                    }

                })
                .catch((error) => {
                    closeLoading();
                    toastMensaje('error', 'Información no encontrada');
                });
        }

        function modal2(){

            var id = document.getElementById('id-global').value;

            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post('/admin/ordenes/lista/informacion-cliente',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalCliente').modal('show');

                        $.each(response.data.lista, function( key, val ){

                            $('#zona-1').val(val.zona);
                            $('#nombre-1').val(val.nombre);
                            $('#direccion-1').val(val.direccion);

                            $('#numero-1').val(val.numero_casa)
                            $('#punto-1').val(val.punto_referencia)
                        });

                    }else{
                        toastMensaje('error', 'Información no encontrada');
                    }

                })
                .catch((error) => {
                    closeLoading();
                    toastMensaje('error', 'Información no encontrada');
                });
        }

        function verMapaPin(){
            var id = document.getElementById('id-global').value;
            $('#modalOpciones').modal('hide');
            window.location.href="{{ url('/admin/orden/mapa/pin') }}/"+id;
        }

        function verMapaReal(){
            var id = document.getElementById('id-global').value;
            $('#modalOpciones').modal('hide');
            window.location.href="{{ url('/admin/orden/mapa/real') }}/"+id;
        }

    </script>


@endsection
