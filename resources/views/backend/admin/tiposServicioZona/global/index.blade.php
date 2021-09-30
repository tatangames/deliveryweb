
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
            <h1>Tipo de Servicios para cambio de posición para todas las zonas</h1>
        </div>
        <div style="margin-top:15px; margin-left:25px">
            <div class="form-group" style="margin-left:20px">
                <label>Si esta activo, se ordenaran posiciones</label><br>
                <label class="switch" style="margin-top:10px">
                    <input type="checkbox" id="check-posicion">
                    <div class="slider round">
                        <span class="on">Activar</span>
                        <span class="off">Desactivar</span>
                    </div>
                </label>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Tipos Servicio Zona</h3>
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

@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery-ui-drag.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/datatables-drag.min.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            var ruta = "{{ URL::to('/admin/tiposerviciozona/tablas/tablatiposervicioglobal') }}";
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function ordenarPosiciones(order){
            var check = document.getElementById('check-posicion').checked;

            if(check){
                openLoading();

                let formData = new FormData();
                formData.append('[order]', order);

                axios.post('/tiposerviciozona/ordenar-globalmente',{
                    'order': order,
                })
                    .then((response) => {
                        closeLoading();
                        toastMensaje('success', 'Actualizado correctamente');
                    })
                    .catch((error) => {
                        closeLoading();
                        toastMensaje('error', 'Error al Actualizar');
                    });
            }
        }

    </script>


@endsection
