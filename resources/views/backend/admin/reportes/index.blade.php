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
            <h1>Reportes Ordenes</h1>
        </div>
    </div>
</section>


<section class="content">
    <div class="container-fluid" style="margin-left: 15px">
        <div class="row">
            <div class="col-md-4">
                <div class="card card-green">
                    <div class="card-header">
                        <h3 class="card-title">Reporte Lista de Ordenes</h3>
                    </div>
                    <form>

                        <div class="form-group" style="width: 60%; margin-left: 15px; margin-top: 15px">
                            <label>Fecha desde</label>
                            <input type="date" class="form-control" id="fechadesde1">
                        </div>

                        <div class="form-group" style="width: 60%; margin-left: 15px">
                            <label>Fecha hasta</label>
                            <input type="date" class="form-control" id="fechahasta1">
                        </div>

                        <div class="form-group" style="margin-left:15px">
                            <label>Servicios (Privados o Publicos)</label><br>
                            <label class="switch" style="margin-top:10px">
                                <input type="checkbox" id=estado1">
                                <div class="slider round">
                                    <span class="on">Privados</span>
                                    <span class="off">Publicos</span>
                                </div>
                            </label>
                        </div>

                        <div class="card-footer">
                            <button type="button" style="float: right;" class="btn btn-success" onclick="buscar1()">Buscar</button>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
</section>


<section class="content">
    <div class="container-fluid" style="margin-left: 15px">
        <div class="row">
            <div class="col-md-4">
                <div class="card card-green">
                    <div class="card-header">
                        <h3 class="card-title">Reporte Lista de Ordenes por Servicio</h3>
                    </div>
                    <form>

                        <div class="form-group" style="width: 60%; margin-left: 15px; margin-top: 15px">
                            <label>Fecha desde</label>
                            <input type="date" class="form-control" id="fechadesde2">
                        </div>

                        <div class="form-group" style="width: 60%; margin-left: 15px">
                            <label>Fecha hasta</label>
                            <input type="date" class="form-control" id="fechahasta2">
                        </div>

                        <div class="form-group" style="width: 85%; margin-left: 15px">
                            <label style="color:#191818">Servicio identificador</label>
                            <br>
                            <div>

                                <select class="form-control" id="servicio2">
                                    @foreach($servicios as $item)
                                        <option value="{{$item->id}}">{{$item->identificador}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="button" style="float: right;" class="btn btn-success" onclick="buscar2()">Buscar</button>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
</section>



@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery.dataTables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>



    <script>

    function buscar1(){

        var fechadesde = document.getElementById('fechadesde1').value;
        var fechahasta = document.getElementById('fechahasta1').value;
        var tb = document.getElementById('estado1"').checked;
        var toggle = tb ? 1 : 0;

        if(fechadesde === ''){
            toastMensaje('error', 'Fecha desde es requerido');
            return;
        }

        if(fechahasta === ''){
            toastMensaje('error', 'Fecha hasta es requerido');
            return;
        }

        window.open("{{ URL::to('lista/reportes/lista-ordenes') }}/" + fechadesde + "/" + fechahasta + "/" + toggle);
    }

    function buscar2(){

        var fechadesde = document.getElementById('fechadesde2').value;
        var fechahasta = document.getElementById('fechahasta2').value;
        var servicio = document.getElementById('servicio2').value;

        if(fechadesde === ''){
            toastMensaje('error', 'Fecha desde es requerido');
            return;
        }

        if(fechahasta === ''){
            toastMensaje('error', 'Fecha hasta es requerido');
            return;
        }

        window.open("{{ URL::to('lista/reportes/lista-ordenes-servicio') }}/" + fechadesde + "/" + fechahasta + "/" + servicio);
    }

    </script>


@endsection
