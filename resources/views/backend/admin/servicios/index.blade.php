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
            <h1>Lista de Tipos</h1>
        </div>
        <br>
        <button type="button" onclick="modalNuevo()" class="btn btn-success btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Nuevo Servicio
        </button>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Lista de Servicios</h3>
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

<!-- modal agregar -->
<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Servicio</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-nuevo">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label>Comisión (NÚMERO ENTERO)</label>
                                        <input type="number" step="1" value="1" min="1" max="100" class="form-control" id="comision-nuevo">
                                    </div>

                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input type="text" maxlength="150" class="form-control" id="nombre-nuevo" placeholder="Nombre servicio">
                                    </div>

                                    <div class="form-group">
                                        <label>Identificador</label>
                                        <input type="text" maxlength="100" class="form-control" id="identificador-nuevo" placeholder="Identificador único">
                                    </div>

                                    <div class="form-group">
                                        <label>Descripción</label>
                                        <input type="text" maxlength="300" class="form-control" id="descripcion-nuevo" placeholder="Descripción">
                                    </div>

                                    <div class="form-group">
                                        <div>
                                            <label>Logo</label>
                                            <p>Tamaño recomendado de: 100 x 100</p>
                                        </div>
                                        <br>
                                        <div class="col-md-10">
                                            <input type="file" style="color:#191818" id="imagenlogo-nuevo" accept="image/jpeg, image/jpg, image/png"/>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div>
                                            <label>Imagen</label>
                                            <p>Tamaño recomendado de: 600 x -</p>
                                        </div>
                                        <br>
                                        <div class="col-md-10">
                                            <input type="file" style="color:#191818" id="imagen-nuevo" accept="image/jpeg, image/jpg, image/png"/>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label style="color:#191818">Tipo Servicio</label>
                                        <br>
                                        <div>
                                            <select class="form-control" id="select-servicio">
                                                <option value="0" selected>Seleccionar</option>
                                                @foreach($tiposervicio as $item)
                                                    <option value="{{$item->id}}">{{$item->nombre}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Teléfono</label>
                                        <input type="text" maxlength="20" class="form-control" id="telefono-nuevo" placeholder="Telefono">
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
                                        <label>Dirección</label>
                                        <input type="text" maxlength="300" class="form-control" id="direccion-nuevo" placeholder="Direccion del servicio">
                                    </div>

                                    <div class="form-group">
                                        <label style="color:#191818">Tipo Vista</label>
                                        <br>
                                        <div>
                                            <select class="form-control" id="select-vista">
                                                <option value="0" selected>Vertical</option>
                                                <option value="1">Horizontal</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="col-md-12">
                                    <p>Horarios</p>

                                    <!-- horario abre y cierre -->
                                    <div class="form-group">
                                        <label>Cerrado lunes</label>
                                        <input type="checkbox" id="cbcerradolunes">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario abre Lunes</label>
                                        <input type="time" class="form-control" id="horalunes1">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario cierra Lunes</label>
                                        <input type="time" class="form-control" id="horalunes2">
                                    </div>
                                    <div class="form-group">
                                        <label>Usa la segunda hora</label>
                                        <input type="checkbox" id="cblunessegunda">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario abre de nuevo Lunes</label>
                                        <input type="time" class="form-control" id="horalunes3">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario cierra de nuevo Lunes</label>
                                        <input type="time" class="form-control" id="horalunes4">
                                    </div>

                                    <div class="form-group">
                                        <label>Cerrado martes</label>
                                        <input type="checkbox" id="cbcerradomartes">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario abre Martes</label>
                                        <input type="time" class="form-control" id="horamartes1">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario cierra Martes</label>
                                        <input type="time" class="form-control" id="horamartes2">
                                    </div>
                                    <div class="form-group">
                                        <label>Usa la segunda hora</label>
                                        <input type="checkbox" id="cbmartessegunda">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario abre de nuevo Martes</label>
                                        <input type="time" class="form-control" id="horamartes3">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario cierra de nuevo Martes</label>
                                        <input type="time" class="form-control" id="horamartes4">
                                    </div>

                                    <div class="form-group">
                                        <label>Cerrado miercoles</label>
                                        <input type="checkbox" id="cbcerradomiercoles">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario abre Miercoles</label>
                                        <input type="time" class="form-control" id="horamiercoles1">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario cierra Miercoles</label>
                                        <input type="time" class="form-control" id="horamiercoles2">
                                    </div>
                                    <div class="form-group">
                                        <label>Usa la segunda hora</label>
                                        <input type="checkbox" id="cbmiercolessegunda">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario abre de nuevo Miercoles</label>
                                        <input type="time" class="form-control" id="horamiercoles3">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario cierra de nuevo Miercoles</label>
                                        <input type="time" class="form-control" id="horamiercoles4">
                                    </div>

                                    <div class="form-group">
                                        <label>Cerrado jueves</label>
                                        <input type="checkbox" id="cbcerradojueves">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario abre Jueves</label>
                                        <input type="time" class="form-control" id="horajueves1">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario cierra Jueves</label>
                                        <input type="time" class="form-control" id="horajueves2">
                                    </div>
                                    <div class="form-group">
                                        <label>Usa la segunda hora</label>
                                        <input type="checkbox" id="cbjuevessegunda">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario abre de nuevo Jueves</label>
                                        <input type="time" class="form-control" id="horajueves3">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario cierra de nuevo Jueves</label>
                                        <input type="time" class="form-control" id="horajueves4">
                                    </div>


                                    <div class="form-group">
                                        <label>Cerrado viernes</label>
                                        <input type="checkbox" id="cbcerradoviernes">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario abre Viernes</label>
                                        <input type="time" class="form-control" id="horaviernes1">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario cierra Viernes</label>
                                        <input type="time" class="form-control" id="horaviernes2">
                                    </div>
                                    <div class="form-group">
                                        <label>Usa la segunda hora</label>
                                        <input type="checkbox" id="cbviernessegunda">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario abre de nuevo Viernes</label>
                                        <input type="time" class="form-control" id="horaviernes3">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario cierra de nuevo Viernes</label>
                                        <input type="time" class="form-control" id="horaviernes4">
                                    </div>

                                    <div class="form-group">
                                        <label>Cerrado Sabado</label>
                                        <input type="checkbox" id="cbcerradosabado">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario abre Sabado</label>
                                        <input type="time" class="form-control" id="horasabado1">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario cierra Sabado</label>
                                        <input type="time" class="form-control" id="horasabado2">
                                    </div>
                                    <div class="form-group">
                                        <label>Usa la segunda hora</label>
                                        <input type="checkbox" id="cbsabadosegunda">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario abre de nuevo Sabado</label>
                                        <input type="time" class="form-control" id="horasabado3">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario cierra de nuevo Sabado</label>
                                        <input type="time" class="form-control" id="horasabado4">
                                    </div>

                                    <div class="form-group">
                                        <label>Cerrado Domingo</label>
                                        <input type="checkbox" id="cbcerradodomingo">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario abre Domingo</label>
                                        <input type="time" class="form-control" id="horadomingo1">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario cierra Domingo</label>
                                        <input type="time" class="form-control" id="horadomingo2">
                                    </div>
                                    <div class="form-group">
                                        <label>Usa la segunda hora</label>
                                        <input type="checkbox" id="cbdomingosegunda">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario abre de nuevo Domingo</label>
                                        <input type="time" class="form-control" id="horadomingo3">
                                    </div>
                                    <div class="form-group">
                                        <label>Horario cierra de nuevo Domingo</label>
                                        <input type="time" class="form-control" id="horadomingo4">
                                    </div>
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


<!-- modal editar servicio-->
<div class="modal fade" id="modalServicio" style="z-index:1000000000">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar servicio</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-servicio">
                    <div class="card-body">
                        <div class="col-md-12">

                            <div class="form-group">
                                <label>Comisión (NÚMERO ENTERO)</label>
                                <input type="number" step="0.01" class="form-control" id="comision-editar">
                            </div>

                            <div class="form-group">
                                <label>Nombre</label>
                                <input type="text" maxlength="150" class="form-control" id="nombre-editar" placeholder="Nombre servicio">
                            </div>

                            <div class="form-group">
                                <label>Identificador</label>
                                <input type="text" maxlength="100" class="form-control" id="identificador-editar" placeholder="Identificador único">
                            </div>

                            <div class="form-group">
                                <label>Descripción</label>
                                <input type="text" maxlength="300" class="form-control" id="descripcion-editar" placeholder="Descripción servicio">
                            </div>

                            <div class="form-group">
                                <div>
                                    <label>Logo</label>
                                    <p>Tamaño recomendado de: 100 x 100</p>
                                </div>
                                <br>
                                <div class="col-md-10">
                                    <input type="file" style="color:#191818" id="imagenlogo-editar" accept="image/jpeg, image/jpg, image/png"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <div>
                                    <label>Imagen</label>
                                    <p>Tamaño recomendado de: 600 x -</p>
                                </div>
                                <br>
                                <div class="col-md-10">
                                    <input type="file" style="color:#191818" id="imagen-editar" accept="image/jpeg, image/jpg, image/png"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="text" maxlength="20" class="form-control" id="telefono-editar" placeholder="Telefono">
                            </div>

                            <div class="form-group">
                                <label>Latitud</label>
                                <input type="text" maxlength="50" class="form-control" id="latitud-editar" placeholder="Latitud">
                            </div>

                            <div class="form-group">
                                <label>Longitud</label>
                                <input type="text" maxlength="50" class="form-control" id="longitud-editar" placeholder="Longitud">
                            </div>

                            <div class="form-group">
                                <label>Dirección</label>
                                <input type="text" maxlength="300" class="form-control" id="direccion-editar" placeholder="Dirección del servicio">
                            </div>

                            <div class="form-group">
                                <label style="color:#191818">Tipo Vista</label>
                                <br>
                                <div>
                                    <select class="form-control" id="select-vista-editar">
                                        <option value="0" selected>Vertical</option>
                                        <option value="1">Horizontal</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label style="color:#191818">Tipo Servicio</label>
                                <br>
                                <div>
                                    <select class="form-control" id="select-servicio-editar">
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" style="margin-left:0px">
                                <label>Cerrado emergencia</label><br>
                                <label class="switch" style="margin-top:10px">
                                    <input type="checkbox" id="cbcerradoemergencia-editar">
                                    <div class="slider round">
                                        <span class="on">Abrir</span>
                                        <span class="off">Cerrar</span>
                                    </div>
                                </label>
                            </div>

                            <div class="form-group">
                                <label>Mensaje del Cerrado Emergencia</label>
                                <input type="text" maxlength="200" class="form-control" id="mensajecerrado-editar" placeholder="Mensaje">
                            </div>

                            <div class="form-group" style="margin-left:0px">
                                <label>Estado</label><br>
                                <label class="switch" style="margin-top:10px">
                                    <input type="checkbox" id="cbactivo-editar">
                                    <div class="slider round">
                                        <span class="on">Activo</span>
                                        <span class="off">No Activo</span>
                                    </div>
                                </label>
                            </div>

                            <div class="form-group" style="margin-left:0px">
                                <label>El servicio da su propio envío</label><br>
                                <label class="switch" style="margin-top:10px">
                                    <input type="checkbox" id="cbprivado-editar">
                                    <div class="slider round">
                                        <span class="on">Abrir</span>
                                        <span class="off">Cerrar</span>
                                    </div>
                                </label>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editarservicio()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- modal editar horarios-->
<div class="modal fade" id="modalHorario" style="z-index:1000000000">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Horarios</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-horario">
                    <div class="card-body">
                        <div class="col-md-12">

                            <!-- horario abre y cierre -->
                            <div class="form-group">
                                <label>Cerrado lunes</label>
                                <input type="checkbox" id="cbcerradolunes-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre Lunes</label>
                                <input type="time" class="form-control" id="horalunes1-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra Lunes</label>
                                <input type="time" class="form-control" id="horalunes2-editar">
                            </div>
                            <div class="form-group">
                                <label>Usa la segunda hora</label>
                                <input type="checkbox" id="cblunessegunda-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre de nuevo Lunes</label>
                                <input type="time" class="form-control" id="horalunes3-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra de nuevo Lunes</label>
                                <input type="time" class="form-control" id="horalunes4-editar">
                            </div>

                            <div class="form-group">
                                <label>Cerrado martes</label>
                                <input type="checkbox" id="cbcerradomartes-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre Martes</label>
                                <input type="time" class="form-control" id="horamartes1-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra Martes</label>
                                <input type="time" class="form-control" id="horamartes2-editar">
                            </div>
                            <div class="form-group">
                                <label>Usa la segunda hora</label>
                                <input type="checkbox" id="cbmartessegunda-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre de nuevo Martes</label>
                                <input type="time" class="form-control" id="horamartes3-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra de nuevo Martes</label>
                                <input type="time" class="form-control" id="horamartes4-editar">
                            </div>

                            <div class="form-group">
                                <label>Cerrado miercoles</label>
                                <input type="checkbox" id="cbcerradomiercoles-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre Miercoles</label>
                                <input type="time" class="form-control" id="horamiercoles1-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra Miercoles</label>
                                <input type="time" class="form-control" id="horamiercoles2-editar">
                            </div>
                            <div class="form-group">
                                <label>Usa la segunda hora</label>
                                <input type="checkbox" id="cbmiercolessegunda-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre de nuevo Miercoles</label>
                                <input type="time" class="form-control" id="horamiercoles3-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra de nuevo Miercoles</label>
                                <input type="time" class="form-control" id="horamiercoles4-editar">
                            </div>

                            <div class="form-group">
                                <label>Cerrado jueves</label>
                                <input type="checkbox" id="cbcerradojueves-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre Jueves</label>
                                <input type="time" class="form-control" id="horajueves1-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra Jueves</label>
                                <input type="time" class="form-control" id="horajueves2-editar">
                            </div>
                            <div class="form-group">
                                <label>Usa la segunda hora</label>
                                <input type="checkbox" id="cbjuevessegunda-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre de nuevo Jueves</label>
                                <input type="time" class="form-control" id="horajueves3-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra de nuevo Jueves</label>
                                <input type="time" class="form-control" id="horajueves4-editar">
                            </div>


                            <div class="form-group">
                                <label>Cerrado viernes</label>
                                <input type="checkbox" id="cbcerradoviernes-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre Viernes</label>
                                <input type="time" class="form-control" id="horaviernes1-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra Viernes</label>
                                <input type="time" class="form-control" id="horaviernes2-editar">
                            </div>
                            <div class="form-group">
                                <label>Usa la segunda hora</label>
                                <input type="checkbox" id="cbviernessegunda-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre de nuevo Viernes</label>
                                <input type="time" class="form-control" id="horaviernes3-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra de nuevo Viernes</label>
                                <input type="time" class="form-control" id="horaviernes4-editar">
                            </div>

                            <div class="form-group">
                                <label>Cerrado Sabado</label>
                                <input type="checkbox" id="cbcerradosabado-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre Sabado</label>
                                <input type="time" class="form-control" id="horasabado1-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra Sabado</label>
                                <input type="time" class="form-control" id="horasabado2-editar">
                            </div>
                            <div class="form-group">
                                <label>Usa la segunda hora</label>
                                <input type="checkbox" id="cbsabadosegunda-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre de nuevo Sabado</label>
                                <input type="time" class="form-control" id="horasabado3-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra de nuevo Sabado</label>
                                <input type="time" class="form-control" id="horasabado4-editar">
                            </div>

                            <div class="form-group">
                                <label>Cerrado Domingo</label>
                                <input type="checkbox" id="cbcerradodomingo-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre Domingo</label>
                                <input type="time" class="form-control" id="horadomingo1-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra Domingo</label>
                                <input type="time" class="form-control" id="horadomingo2-editar">
                            </div>
                            <div class="form-group">
                                <label>Usa la segunda hora</label>
                                <input type="checkbox" id="cbdomingosegunda-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario abre de nuevo Lunes</label>
                                <input type="time" class="form-control" id="horadomingo3-editar">
                            </div>
                            <div class="form-group">
                                <label>Horario cierra de nuevo Domingo</label>
                                <input type="time" class="form-control" id="horadomingo4-editar">
                            </div>

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editarHoras()">Guardar</button>
            </div>
        </div>
    </div>
</div>

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
                <form id="formulario-nuevo">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <input type="hidden" id="id-global">
                                </div>

                                <center>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-info" onclick="modalServicio()">Editar Servicio</button>
                                    </div>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-info" onclick="modalHorario()">Editar Horario</button>
                                    </div>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-info" onclick="verCategorias()">Categorias</button>
                                    </div>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-info" onclick="verMapa()">Ubicación</button>
                                    </div>

                                    <div class="form-group">
                                        <button type="button" class="btn btn-info" onclick="verEtiquetas()">Etiquetas</button>
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
            var ruta = "{{ URL::to('/admin/servicios/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/servicios/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
        }

        function verTodaOpciones(id){
            $('#modalOpciones').modal('show');
            $('#id-global').val(id);
        }

        // abrir modal
        function modalNuevo(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function verificarNuevo(){
            Swal.fire({
                title: 'Guardar Nuevo Servicio?',
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

        //nuevo servicio
        function nuevo(){

            var comision = document.getElementById('comision-nuevo').value;

            var nombre = document.getElementById('nombre-nuevo').value;
            var identificador = document.getElementById('identificador-nuevo').value;
            var descripcion = document.getElementById('descripcion-nuevo').value;
            var logo = document.getElementById('imagenlogo-nuevo');
            var imagen = document.getElementById('imagen-nuevo');
            var tiposervicio = document.getElementById('select-servicio').value;
            var telefono = document.getElementById('telefono-nuevo').value;
            var latitud = document.getElementById('latitud-nuevo').value;
            var longitud = document.getElementById('longitud-nuevo').value;
            var direccion = document.getElementById('direccion-nuevo').value;
            var tipovista = document.getElementById('select-vista').value;

            var horalunes1 = document.getElementById('horalunes1').value;
            var horalunes2 = document.getElementById('horalunes2').value;
            var horalunes3 = document.getElementById('horalunes3').value;
            var horalunes4 = document.getElementById('horalunes4').value;
            var cblunessegunda = document.getElementById('cblunessegunda').checked;
            var cbcerradolunes = document.getElementById('cbcerradolunes').checked;

            var horamartes1 = document.getElementById('horamartes1').value;
            var horamartes2 = document.getElementById('horamartes2').value;
            var horamartes3 = document.getElementById('horamartes3').value;
            var horamartes4 = document.getElementById('horamartes4').value;
            var cbmartessegunda = document.getElementById('cbmartessegunda').checked;
            var cbcerradomartes = document.getElementById('cbcerradomartes').checked;

            var horamiercoles1 = document.getElementById('horamiercoles1').value;
            var horamiercoles2 = document.getElementById('horamiercoles2').value;
            var horamiercoles3 = document.getElementById('horamiercoles3').value;
            var horamiercoles4 = document.getElementById('horamiercoles4').value;
            var cbmiercolessegunda = document.getElementById('cbmiercolessegunda').checked;
            var cbcerradomiercoles = document.getElementById('cbcerradomiercoles').checked;

            var horajueves1 = document.getElementById('horajueves1').value;
            var horajueves2 = document.getElementById('horajueves2').value;
            var horajueves3 = document.getElementById('horajueves3').value;
            var horajueves4 = document.getElementById('horajueves4').value;
            var cbjuevessegunda = document.getElementById('cbjuevessegunda').checked;
            var cbcerradojueves = document.getElementById('cbcerradojueves').checked;

            var horaviernes1 = document.getElementById('horaviernes1').value;
            var horaviernes2 = document.getElementById('horaviernes2').value;
            var horaviernes3 = document.getElementById('horaviernes3').value;
            var horaviernes4 = document.getElementById('horaviernes4').value;
            var cbviernessegunda = document.getElementById('cbviernessegunda').checked;
            var cbcerradoviernes = document.getElementById('cbcerradoviernes').checked;

            var horasabado1 = document.getElementById('horasabado1').value;
            var horasabado2 = document.getElementById('horasabado2').value;
            var horasabado3 = document.getElementById('horasabado3').value;
            var horasabado4 = document.getElementById('horasabado4').value;
            var cbsabadosegunda = document.getElementById('cbsabadosegunda').checked;
            var cbcerradosabado = document.getElementById('cbcerradosabado').checked;

            var horadomingo1 = document.getElementById('horadomingo1').value;
            var horadomingo2 = document.getElementById('horadomingo2').value;
            var horadomingo3 = document.getElementById('horadomingo3').value;
            var horadomingo4 = document.getElementById('horadomingo4').value;
            var cbdomingosegunda = document.getElementById('cbdomingosegunda').checked;
            var cbcerradodomingo = document.getElementById('cbcerradodomingo').checked;

            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(comision === ''){
                toastMensaje('error', 'Comisión es requerida');
                return;
            }

            if(!comision.match(reglaNumeroEntero)) {
                toastMensaje('error', 'La comisión debe ser un número ENTERO y no negativo');
                return;
            }

            if(comision < 0){
                toastMensaje('error', 'Comisión no puede ser negativo');
                return;
            }

            if(comision > 100){
                toastMensaje('error', 'Comisión no puede ser mayor a 100');
                return;
            }

            if(nombre === '') {
                toastMensaje('error', 'Nombre es requerido');
                return;
            }

            if(nombre.length > 150){
                toastMensaje('error', 'Nombre máximo 150 caracteres');
                return;
            }

            if(identificador === '') {
                toastMensaje('error', 'Identificador es requerido');
                return;
            }

            if(identificador.length > 100){
                toastMensaje('error', 'Identificador máximo 150 caracteres');
                return;
            }

            if(descripcion.length > 300){
                toastMensaje('error', 'Descripción máximo 150 caracteres');
                return;
            }

            if(logo.files && logo.files[0]){ // si trae imagen
                if (!logo.files[0].type.match('image/jpeg|image/jpeg|image/png')){
                    toastMensaje('error', 'Formato de imagen permitido: .png .jpg .jpeg');
                    return;
                }
            }else{
                toastMensaje('error', 'Logo es requerido');
                return;
            }

            if(imagen.files && imagen.files[0]){ // si trae imagen
                if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){
                    toastMensaje('error', 'Formato de imagen permitido: .png .jpg .jpeg');
                    return;
                }
            }else{
                toastMensaje('error', 'Imagen es requerido');
                return;
            }

            if(telefono === '') {
                toastMensaje('error', 'Teléfono es requerido');
                return;
            }

            if(telefono.length > 20){
                toastMensaje('error', 'Teléfono máximo 20 caracteres');
                return;
            }

            if(latitud === '') {
                toastMensaje('error', 'Latitud es requerido');
                return;
            }

            if(latitud.length > 50){
                toastMensaje('error', 'Latitud máximo 50 caracteres');
                return;
            }

            if(longitud === '') {
                toastMensaje('error', 'Longitud es requerido');
                return;
            }

            if(longitud.length > 50){
                toastMensaje('error', 'Longitud máximo 50 caracteres');
                return;
            }

            if(direccion === '') {
                toastMensaje('error', 'Dirección es requerida');
                return;
            }

            if(direccion.length > 300){
                toastMensaje('error', 'Dirección máximo 300 caracteres');
                return;
            }

            // ** Horarios

            if (horalunes1 === '') {
                toastMensaje('error', 'Lunes horario 1 es requerido');
                return;
            }

            if (horalunes2 === '') {
                toastMensaje('error', 'Lunes horario 2 es requerido');
                return;
            }

            if (horalunes3 === '') {
                toastMensaje('error', 'Lunes horario 3 es requerido');
                return;
            }

            if (horalunes4 === '') {
                toastMensaje('error', 'Lunes horario 4 es requerido');
                return;
            }

            //------

            if (horamartes1 === '') {
                toastMensaje('error', 'Martes horario 1 es requerido');
                return;
            }

            if (horamartes2 === '') {
                toastMensaje('error', 'Martes horario 2 es requerido');
                return;
            }

            if (horamartes3 === '') {
                toastMensaje('error', 'Martes horario 3 es requerido');
                return;
            }

            if (horamartes4 === '') {
                toastMensaje('error', 'Martes horario 4 es requerido');
                return;
            }

            //---

            if (horamiercoles1 === '') {
                toastMensaje('error', 'Miercoles horario 1 es requerido');
                return;
            }

            if (horamiercoles2 === '') {
                toastMensaje('error', 'Miercoles horario 2 es requerido');
                return;
            }

            if (horamiercoles3 === '') {
                toastMensaje('error', 'Miercoles horario 3 es requerido');
                return;
            }

            if (horamiercoles4 === '') {
                toastMensaje('error', 'Miercoles horario 4 es requerido');
                return;
            }

            //----

            if (horajueves1 === '') {
                toastMensaje('error', 'Jueves horario 1 es requerido');
                return;
            }

            if (horajueves2 === '') {
                toastMensaje('error', 'Jueves horario 2 es requerido');
                return;
            }

            if (horajueves3 === '') {
                toastMensaje('error', 'Jueves horario 3 es requerido');
                return;
            }

            if (horajueves4 === '') {
                toastMensaje('error', 'Jueves horario 4 es requerido');
                return;
            }

            //---

            if (horaviernes1 === '') {
                toastMensaje('error', 'Viernes horario 1 es requerido');
                return;
            }

            if (horaviernes2 === '') {
                toastMensaje('error', 'Viernes horario 2 es requerido');
                return;
            }

            if (horaviernes3 === '') {
                toastMensaje('error', 'Viernes horario 3 es requerido');
                return;
            }

            if (horaviernes4 === '') {
                toastMensaje('error', 'Viernes horario 4 es requerido');
                return;
            }

            //---

            if (horasabado1 === '') {
                toastMensaje('error', 'Sabado horario 1 es requerido');
                return;
            }

            if (horasabado2 === '') {
                toastMensaje('error', 'Sabado horario 2 es requerido');
                return;
            }

            if (horasabado3 === '') {
                toastMensaje('error', 'Sabado horario 3 es requerido');
                return;
            }

            if (horasabado4 === '') {
                toastMensaje('error', 'Sabado horario 4 es requerido');
                return;
            }

            //---

            if (horadomingo1 === '') {
                toastMensaje('error', 'Domingo horario 1 es requerido');
                return;
            }

            if (horadomingo2 === '') {
                toastMensaje('error', 'Domingo horario 2 es requerido');
                return;
            }

            if (horadomingo3 === '') {
                toastMensaje('error', 'Domingo horario 3 es requerido');
                return;
            }

            if (horadomingo4 === '') {
                toastMensaje('error', 'Domingo horario 4 es requerido');
                return;
            }

            var check_lunes_segunda = cblunessegunda ? 1 : 0;
            var check_lunes_cerrado = cbcerradolunes ? 1 : 0;

            var check_martes_segunda = cbmartessegunda ? 1 : 0;
            var check_martes_cerrado = cbcerradomartes ? 1 : 0;

            var check_miercoles_segunda = cbmiercolessegunda ? 1 : 0;
            var check_miercoles_cerrado = cbcerradomiercoles ? 1 : 0;

            var check_jueves_segunda = cbjuevessegunda ? 1 : 0;
            var check_jueves_cerrado = cbcerradojueves ? 1 : 0;

            var check_viernes_segunda = cbviernessegunda ? 1 : 0;
            var check_viernes_cerrado = cbcerradoviernes ? 1 : 0;

            var check_sabado_segunda = cbsabadosegunda ? 1 : 0;
            var check_sabado_cerrado = cbcerradosabado ? 1 : 0;

            var check_domingo_segunda = cbdomingosegunda ? 1 : 0;
            var check_domingo_cerrado = cbcerradodomingo ? 1 : 0;

            openLoading();

            var formData = new FormData();
            formData.append('comision', comision);
            formData.append('nombre', nombre);
            formData.append('identificador', identificador);
            formData.append('descripcion', descripcion);
            formData.append('logo', logo.files[0]);
            formData.append('imagen', imagen.files[0]);
            formData.append('tiposervicio', tiposervicio);
            formData.append('telefono', telefono);
            formData.append('latitud', latitud);
            formData.append('longitud', longitud);
            formData.append('direccion', direccion);
            formData.append('tipovista', tipovista);

            formData.append('horalunes1', horalunes1);
            formData.append('horalunes2', horalunes2);
            formData.append('horalunes3', horalunes3);
            formData.append('horalunes4', horalunes4);
            formData.append('cblunessegunda', check_lunes_segunda);
            formData.append('cbcerradolunes', check_lunes_cerrado);

            formData.append('horamartes1', horamartes1);
            formData.append('horamartes2', horamartes2);
            formData.append('horamartes3', horamartes3);
            formData.append('horamartes4', horamartes4);
            formData.append('cbmartessegunda', check_martes_segunda);
            formData.append('cbcerradomartes', check_martes_cerrado);

            //---

            formData.append('horamiercoles1', horamiercoles1);
            formData.append('horamiercoles2', horamiercoles2);
            formData.append('horamiercoles3', horamiercoles3);
            formData.append('horamiercoles4', horamiercoles4);
            formData.append('cbmiercolessegunda', check_miercoles_segunda);
            formData.append('cbcerradomiercoles', check_miercoles_cerrado);

            //--

            formData.append('horajueves1', horajueves1);
            formData.append('horajueves2', horajueves2);
            formData.append('horajueves3', horajueves3);
            formData.append('horajueves4', horajueves4);
            formData.append('cbjuevessegunda', check_jueves_segunda);
            formData.append('cbcerradojueves', check_jueves_cerrado);

            formData.append('horaviernes1', horaviernes1);
            formData.append('horaviernes2', horaviernes2);
            formData.append('horaviernes3', horaviernes3);
            formData.append('horaviernes4', horaviernes4);
            formData.append('cbviernessegunda', check_viernes_segunda);
            formData.append('cbcerradoviernes', check_viernes_cerrado);

            //---

            formData.append('horasabado1', horasabado1);
            formData.append('horasabado2', horasabado2);
            formData.append('horasabado3', horasabado3);
            formData.append('horasabado4', horasabado4);
            formData.append('cbsabadosegunda', check_sabado_segunda);
            formData.append('cbcerradosabado', check_sabado_cerrado);

            formData.append('horadomingo1', horadomingo1);
            formData.append('horadomingo2', horadomingo2);
            formData.append('horadomingo3', horadomingo3);
            formData.append('horadomingo4', horadomingo4);
            formData.append('cbdomingosegunda', check_domingo_segunda);
            formData.append('cbcerradodomingo', check_domingo_cerrado);

            axios.post('/servicios/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();
                   if (response.data.success === 1) {
                       toastMensaje('error', 'Identificador ya existe');
                   } else if (response.data.success === 2) {
                       $('#modalAgregar').modal('hide');
                       toastMensaje('success', 'Registrado correctamente');
                       recargar();
                    }
                    else {
                       toastMensaje('error', 'Error al guardar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastMensaje('error', 'Error al guardar');
                });
        }

        // elegir modal para modificar
        function verModales(id){
            document.getElementById("formulario-opciones").reset();
            $('#id-editar').val(id);
            $('#modalOpcion').modal('show');
        }

        // vista editar servicio
        function modalServicio(){

            document.getElementById("formulario-servicio").reset();

            var id = document.getElementById('id-global').value;
            openLoading();

            axios.post('/servicios/informacion/servicio',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        $('#modalServicio').modal('show');
                        $('#comision-editar').val(response.data.servicio.comision);
                        $('#nombre-editar').val(response.data.servicio.nombre);
                        $('#descripcion-editar').val(response.data.servicio.descripcion);
                        $('#identificador-editar').val(response.data.servicio.identificador);
                        $('#telefono-editar').val(response.data.servicio.telefono);
                        $('#latitud-editar').val(response.data.servicio.latitud);
                        $('#longitud-editar').val(response.data.servicio.longitud);
                        $('#direccion-editar').val(response.data.servicio.direccion);
                        $('#mensajecerrado-editar').val(response.data.servicio.mensaje_cerrado);

                        document.getElementById("select-servicio-editar").options.length = 0;

                        $.each(response.data.tipo, function( key, val ){
                            if(response.data.servicio.tipos_servicio_id === val.id){
                                $('#select-servicio-editar').append('<option value="' +val.id +'" selected="selected">'+val.nombre+'</option>');
                            }else{
                                $('#select-servicio-editar').append('<option value="' +val.id +'">'+val.nombre+'</option>');
                            }
                        });

                        if(response.data.servicio.tipo_vista === 1){
                            $('#select-vista-editar option')[1].selected = true;
                        }

                        if(response.data.servicio.cerrado_emergencia === 1){
                            $('#cbcerradoemergencia-editar').prop('checked', true);
                        }

                        if(response.data.servicio.activo === 1){
                            $('#cbactivo-editar').prop('checked', true);
                        }

                        if(response.data.servicio.privado === 1){
                            $('#cbprivado-editar').prop('checked', true);
                        }

                    }else{
                        toastMensaje('error', 'Error al buscar');
                    }
                })
                .catch((error) => {
                   toastMensaje('error', 'Error al buscar');
                   closeLoading();
                });
        }

        // editar servicio
        function editarservicio(){

            var id = document.getElementById('id-global').value;
            var comision = document.getElementById('comision-editar').value;
            var nombre = document.getElementById('nombre-editar').value;
            var identificador = document.getElementById('identificador-editar').value;
            var descripcion = document.getElementById('descripcion-editar').value;
            var logo = document.getElementById('imagenlogo-editar');
            var imagen = document.getElementById('imagen-editar');
            var tiposervicio = document.getElementById('select-servicio-editar').value;
            var telefono = document.getElementById('telefono-editar').value;
            var latitud = document.getElementById('latitud-editar').value;
            var longitud = document.getElementById('longitud-editar').value;
            var direccion = document.getElementById('direccion-editar').value;
            var tipovista = document.getElementById('select-vista-editar').value;
            var mensajecerrado = document.getElementById('mensajecerrado-editar').value;

            var cbcerradoemergencia = document.getElementById('cbcerradoemergencia-editar').checked;
            var cbactivo = document.getElementById('cbactivo-editar').checked;
            var cbprivado = document.getElementById('cbprivado-editar').checked;

            var check_cerrado_emergencia = cbcerradoemergencia ? 1 : 0;
            var check_activo = cbactivo ? 1 : 0;
            var check_privado = cbprivado ? 1 : 0;

            var reglaNumeroEntero = /^[0-9]\d*$/;

            if(comision === ''){
                toastMensaje('error', 'Comisión es requerida');
                return;
            }

            if(!comision.match(reglaNumeroEntero)) {
                toastMensaje('error', 'La comisión debe ser un número ENTERO y no negativo');
                return;
            }

            if(comision < 0){
                toastMensaje('error', 'Comisión no puede ser negativo');
                return;
            }

            if(comision > 100){
                toastMensaje('error', 'Comisión no puede ser mayor a 100');
                return;
            }

            if(nombre === '') {
                toastMensaje('error', 'Nombre es requerido');
                return;
            }

            if(nombre.length > 150){
                toastMensaje('error', 'Nombre máximo 150 caracteres');
                return;
            }

            if(identificador === '') {
                toastMensaje('error', 'Identificador es requerido');
                return;
            }

            if(identificador.length > 100){
                toastMensaje('error', 'Identificador máximo 150 caracteres');
                return;
            }

            if(descripcion.length > 300){
                toastMensaje('error', 'Descripción máximo 150 caracteres');
                return;
            }

            if(logo.files && logo.files[0]){ // si trae imagen
                if (!logo.files[0].type.match('image/jpeg|image/jpeg|image/png')){
                    toastMensaje('error', 'Formato de imagen permitido: .png .jpg .jpeg');
                    return;
                }
            }

            if(imagen.files && imagen.files[0]){ // si trae imagen
                if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){
                    toastMensaje('error', 'Formato de imagen permitido: .png .jpg .jpeg');
                    return;
                }
            }

            if(telefono === '') {
                toastMensaje('error', 'Teléfono es requerido');
                return;
            }

            if(telefono.length > 20){
                toastMensaje('error', 'Teléfono máximo 20 caracteres');
                return;
            }

            if(latitud === '') {
                toastMensaje('error', 'Latitud es requerido');
                return;
            }

            if(latitud.length > 50){
                toastMensaje('error', 'Latitud máximo 50 caracteres');
                return;
            }

            if(longitud === '') {
                toastMensaje('error', 'Longitud es requerido');
                return;
            }

            if(longitud.length > 50){
                toastMensaje('error', 'Longitud máximo 50 caracteres');
                return;
            }

            if(direccion === '') {
                toastMensaje('error', 'Dirección es requerida');
                return;
            }

            if(direccion.length > 300){
                toastMensaje('error', 'Dirección máximo 300 caracteres');
                return;
            }

            if(check_cerrado_emergencia === 1){
                if(mensajecerrado === ''){
                    toastMensaje('error', 'Mensaje de Cerrado Emergencia es requerido');
                    return;
                }
            }

            if(mensajecerrado.length > 200){
                toastMensaje('error', 'Mensaje de Cerrado Máximo 200 caracteres');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('comision', comision);
            formData.append('nombre', nombre);
            formData.append('identificador', identificador);
            formData.append('descripcion', descripcion);
            formData.append('logo', logo.files[0]);
            formData.append('imagen', imagen.files[0]);
            formData.append('tiposervicio', tiposervicio);
            formData.append('telefono', telefono);
            formData.append('latitud', latitud);
            formData.append('longitud', longitud);
            formData.append('direccion', direccion);
            formData.append('tipovista', tipovista);
            formData.append('mensajecerrado', mensajecerrado);
            formData.append('cbcerradoemergencia', check_cerrado_emergencia);
            formData.append('cbactivo', check_activo);
            formData.append('cbprivado', check_privado);

            axios.post('/servicios/editar-servicio', formData, {
            })
                .then((response) => {
                    closeLoading();
                   if (response.data.success === 1) {
                        toastMensaje('error', 'Identificador ya existe');
                    } else if (response.data.success === 2) {
                       $('#modalOpciones').modal('hide');
                       $('#modalServicio').modal('hide');
                        toastMensaje('success', 'Actualizado correctamente');
                        recargar();
                    }
                    else {
                       toastMensaje('error', 'Error al Editar');
                    }
                })
                .catch((error) => {
                    toastMensaje('error', 'Error al Editar');
                    closeLoading();
                });
        }


        // vista editar horarios
        function modalHorario(){

            document.getElementById("formulario-horario").reset();

            var id = document.getElementById('id-global').value;
            openLoading();

            axios.post('/servicios/informacion-horario/servicio',{
                'id': id
            })
                .then((response) => {
                   closeLoading();

                    if(response.data.success === 1){

                        $('#modalHorario').modal('show');

                        $.each(response.data.horario, function( key, val ){
                            if(val.dia === 1){ //domingo
                                $('#horadomingo1-editar').val(val.hora1);
                                $('#horadomingo2-editar').val(val.hora2);
                                $('#horadomingo3-editar').val(val.hora3);
                                $('#horadomingo4-editar').val(val.hora4);

                                if(val.segunda_hora === 1){
                                    $('#cbdomingosegunda-editar').prop('checked', true);
                                }

                                if(val.cerrado === 1){
                                    $('#cbcerradodomingo-editar').prop('checked', true);
                                }

                            }else if(val.dia === 2){
                                $('#horalunes1-editar').val(val.hora1);
                                $('#horalunes2-editar').val(val.hora2);
                                $('#horalunes3-editar').val(val.hora3);
                                $('#horalunes4-editar').val(val.hora4);

                                if(val.segunda_hora === 1){
                                    $('#cblunessegunda-editar').prop('checked', true);
                                }

                                if(val.cerrado === 1){
                                    $('#cbcerradolunes-editar').prop('checked', true);
                                }
                            }else if(val.dia === 3){
                                $('#horamartes1-editar').val(val.hora1);
                                $('#horamartes2-editar').val(val.hora2);
                                $('#horamartes3-editar').val(val.hora3);
                                $('#horamartes4-editar').val(val.hora4);

                                if(val.segunda_hora === 1){
                                    $('#cbmartessegunda-editar').prop('checked', true);
                                }

                                if(val.cerrado === 1){
                                    $('#cbcerradomartes-editar').prop('checked', true);
                                }
                            }
                            else if(val.dia === 4){
                                $('#horamiercoles1-editar').val(val.hora1);
                                $('#horamiercoles2-editar').val(val.hora2);
                                $('#horamiercoles3-editar').val(val.hora3);
                                $('#horamiercoles4-editar').val(val.hora4);

                                if(val.segunda_hora === 1){
                                    $('#cbmiercolessegunda-editar').prop('checked', true);
                                }

                                if(val.cerrado === 1){
                                    $('#cbcerradomiercoles-editar').prop('checked', true);
                                }
                            }
                            else if(val.dia === 5){
                                $('#horajueves1-editar').val(val.hora1);
                                $('#horajueves2-editar').val(val.hora2);
                                $('#horajueves3-editar').val(val.hora3);
                                $('#horajueves4-editar').val(val.hora4);

                                if(val.segunda_hora === 1){
                                    $('#cbjuevessegunda-editar').prop('checked', true);
                                }

                                if(val.cerrado === 1){
                                    $('#cbcerradojueves-editar').prop('checked', true);
                                }
                            }
                            else if(val.dia === 6){
                                $('#horaviernes1-editar').val(val.hora1);
                                $('#horaviernes2-editar').val(val.hora2);
                                $('#horaviernes3-editar').val(val.hora3);
                                $('#horaviernes4-editar').val(val.hora4);

                                if(val.segunda_hora === 1){
                                    $('#cbviernessegunda-editar').prop('checked', true);
                                }

                                if(val.cerrado === 1){
                                    $('#cbcerradoviernes-editar').prop('checked', true);
                                }
                            }
                            else if(val.dia === 7){
                                $('#horasabado1-editar').val(val.hora1);
                                $('#horasabado2-editar').val(val.hora2);
                                $('#horasabado3-editar').val(val.hora3);
                                $('#horasabado4-editar').val(val.hora4);

                                if(val.segunda_hora === 1){
                                    $('#cbsabadosegunda-editar').prop('checked', true);
                                }

                                if(val.cerrado === 1){
                                    $('#cbcerradosabado-editar').prop('checked', true);
                                }
                            }

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

        // ditar las horas del servicio
        function editarHoras(){
            var id = document.getElementById('id-global').value;

            var horalunes1 = document.getElementById('horalunes1-editar').value;
            var horalunes2 = document.getElementById('horalunes2-editar').value;
            var horalunes3 = document.getElementById('horalunes3-editar').value;
            var horalunes4 = document.getElementById('horalunes4-editar').value;
            var cblunessegunda = document.getElementById('cblunessegunda-editar').checked;
            var cbcerradolunes = document.getElementById('cbcerradolunes-editar').checked;

            var horamartes1 = document.getElementById('horamartes1-editar').value;
            var horamartes2 = document.getElementById('horamartes2-editar').value;
            var horamartes3 = document.getElementById('horamartes3-editar').value;
            var horamartes4 = document.getElementById('horamartes4-editar').value;
            var cbmartessegunda = document.getElementById('cbmartessegunda-editar').checked;
            var cbcerradomartes = document.getElementById('cbcerradomartes-editar').checked;

            var horamiercoles1 = document.getElementById('horamiercoles1-editar').value;
            var horamiercoles2 = document.getElementById('horamiercoles2-editar').value;
            var horamiercoles3 = document.getElementById('horamiercoles3-editar').value;
            var horamiercoles4 = document.getElementById('horamiercoles4-editar').value;
            var cbmiercolessegunda = document.getElementById('cbmiercolessegunda-editar').checked;
            var cbcerradomiercoles = document.getElementById('cbcerradomiercoles-editar').checked;

            var horajueves1 = document.getElementById('horajueves1-editar').value;
            var horajueves2 = document.getElementById('horajueves2-editar').value;
            var horajueves3 = document.getElementById('horajueves3-editar').value;
            var horajueves4 = document.getElementById('horajueves4-editar').value;
            var cbjuevessegunda = document.getElementById('cbjuevessegunda-editar').checked;
            var cbcerradojueves = document.getElementById('cbcerradojueves-editar').checked;

            var horaviernes1 = document.getElementById('horaviernes1-editar').value;
            var horaviernes2 = document.getElementById('horaviernes2-editar').value;
            var horaviernes3 = document.getElementById('horaviernes3-editar').value;
            var horaviernes4 = document.getElementById('horaviernes4-editar').value;
            var cbviernessegunda = document.getElementById('cbviernessegunda-editar').checked;
            var cbcerradoviernes = document.getElementById('cbcerradoviernes-editar').checked;

            var horasabado1 = document.getElementById('horasabado1-editar').value;
            var horasabado2 = document.getElementById('horasabado2-editar').value;
            var horasabado3 = document.getElementById('horasabado3-editar').value;
            var horasabado4 = document.getElementById('horasabado4-editar').value;
            var cbsabadosegunda = document.getElementById('cbsabadosegunda-editar').checked;
            var cbcerradosabado = document.getElementById('cbcerradosabado-editar').checked;

            var horadomingo1 = document.getElementById('horadomingo1-editar').value;
            var horadomingo2 = document.getElementById('horadomingo2-editar').value;
            var horadomingo3 = document.getElementById('horadomingo3-editar').value;
            var horadomingo4 = document.getElementById('horadomingo4-editar').value;
            var cbdomingosegunda = document.getElementById('cbdomingosegunda-editar').checked;
            var cbcerradodomingo = document.getElementById('cbcerradodomingo-editar').checked;

            if (horalunes1 === '') {
                toastMensaje('error', 'Lunes horario 1 es requerido');
                return;
            }

            if (horalunes2 === '') {
                toastMensaje('error', 'Lunes horario 2 es requerido');
                return;
            }

            if (horalunes3 === '') {
                toastMensaje('error', 'Lunes horario 3 es requerido');
                return;
            }

            if (horalunes4 === '') {
                toastMensaje('error', 'Lunes horario 4 es requerido');
                return;
            }

            //------

            if (horamartes1 === '') {
                toastMensaje('error', 'Martes horario 1 es requerido');
                return;
            }

            if (horamartes2 === '') {
                toastMensaje('error', 'Martes horario 2 es requerido');
                return;
            }

            if (horamartes3 === '') {
                toastMensaje('error', 'Martes horario 3 es requerido');
                return;
            }

            if (horamartes4 === '') {
                toastMensaje('error', 'Martes horario 4 es requerido');
                return;
            }

            //---

            if (horamiercoles1 === '') {
                toastMensaje('error', 'Miercoles horario 1 es requerido');
                return;
            }

            if (horamiercoles2 === '') {
                toastMensaje('error', 'Miercoles horario 2 es requerido');
                return;
            }

            if (horamiercoles3 === '') {
                toastMensaje('error', 'Miercoles horario 3 es requerido');
                return;
            }

            if (horamiercoles4 === '') {
                toastMensaje('error', 'Miercoles horario 4 es requerido');
                return;
            }

            //----

            if (horajueves1 === '') {
                toastMensaje('error', 'Jueves horario 1 es requerido');
                return;
            }

            if (horajueves2 === '') {
                toastMensaje('error', 'Jueves horario 2 es requerido');
                return;
            }

            if (horajueves3 === '') {
                toastMensaje('error', 'Jueves horario 3 es requerido');
                return;
            }

            if (horajueves4 === '') {
                toastMensaje('error', 'Jueves horario 4 es requerido');
                return;
            }

            //---

            if (horaviernes1 === '') {
                toastMensaje('error', 'Viernes horario 1 es requerido');
                return;
            }

            if (horaviernes2 === '') {
                toastMensaje('error', 'Viernes horario 2 es requerido');
                return;
            }

            if (horaviernes3 === '') {
                toastMensaje('error', 'Viernes horario 3 es requerido');
                return;
            }

            if (horaviernes4 === '') {
                toastMensaje('error', 'Viernes horario 4 es requerido');
                return;
            }

            //---

            if (horasabado1 === '') {
                toastMensaje('error', 'Sabado horario 1 es requerido');
                return;
            }

            if (horasabado2 === '') {
                toastMensaje('error', 'Sabado horario 2 es requerido');
                return;
            }

            if (horasabado3 === '') {
                toastMensaje('error', 'Sabado horario 3 es requerido');
                return;
            }

            if (horasabado4 === '') {
                toastMensaje('error', 'Sabado horario 4 es requerido');
                return;
            }

            //---

            if (horadomingo1 === '') {
                toastMensaje('error', 'Domingo horario 1 es requerido');
                return;
            }

            if (horadomingo2 === '') {
                toastMensaje('error', 'Domingo horario 2 es requerido');
                return;
            }

            if (horadomingo3 === '') {
                toastMensaje('error', 'Domingo horario 3 es requerido');
                return;
            }

            if (horadomingo4 === '') {
                toastMensaje('error', 'Domingo horario 4 es requerido');
                return;
            }

            var check_lunes_segunda = cblunessegunda ? 1 : 0;
            var check_lunes_cerrado = cbcerradolunes ? 1 : 0;

            var check_martes_segunda = cbmartessegunda ? 1 : 0;
            var check_martes_cerrado = cbcerradomartes ? 1 : 0;

            var check_miercoles_segunda = cbmiercolessegunda ? 1 : 0;
            var check_miercoles_cerrado = cbcerradomiercoles ? 1 : 0;

            var check_jueves_segunda = cbjuevessegunda ? 1 : 0;
            var check_jueves_cerrado = cbcerradojueves ? 1 : 0;

            var check_viernes_segunda = cbviernessegunda ? 1 : 0;
            var check_viernes_cerrado = cbcerradoviernes ? 1 : 0;

            var check_sabado_segunda = cbsabadosegunda ? 1 : 0;
            var check_sabado_cerrado = cbcerradosabado ? 1 : 0;

            var check_domingo_segunda = cbdomingosegunda ? 1 : 0;
            var check_domingo_cerrado = cbcerradodomingo ? 1 : 0;

                openLoading();
                var formData = new FormData();

                formData.append('id', id);

                formData.append('horalunes1', horalunes1);
                formData.append('horalunes2', horalunes2);
                formData.append('horalunes3', horalunes3);
                formData.append('horalunes4', horalunes4);
                formData.append('cblunessegunda', check_lunes_segunda);
                formData.append('cbcerradolunes', check_lunes_cerrado);

                formData.append('horamartes1', horamartes1);
                formData.append('horamartes2', horamartes2);
                formData.append('horamartes3', horamartes3);
                formData.append('horamartes4', horamartes4);
                formData.append('cbmartessegunda', check_martes_segunda);
                formData.append('cbcerradomartes', check_martes_cerrado);

                //---

                formData.append('horamiercoles1', horamiercoles1);
                formData.append('horamiercoles2', horamiercoles2);
                formData.append('horamiercoles3', horamiercoles3);
                formData.append('horamiercoles4', horamiercoles4);
                formData.append('cbmiercolessegunda', check_miercoles_segunda);
                formData.append('cbcerradomiercoles', check_miercoles_cerrado);

                //--

                formData.append('horajueves1', horajueves1);
                formData.append('horajueves2', horajueves2);
                formData.append('horajueves3', horajueves3);
                formData.append('horajueves4', horajueves4);
                formData.append('cbjuevessegunda', check_jueves_segunda);
                formData.append('cbcerradojueves', check_jueves_cerrado);

                formData.append('horaviernes1', horaviernes1);
                formData.append('horaviernes2', horaviernes2);
                formData.append('horaviernes3', horaviernes3);
                formData.append('horaviernes4', horaviernes4);
                formData.append('cbviernessegunda', check_viernes_segunda);
                formData.append('cbcerradoviernes', check_viernes_cerrado);

                //---

                formData.append('horasabado1', horasabado1);
                formData.append('horasabado2', horasabado2);
                formData.append('horasabado3', horasabado3);
                formData.append('horasabado4', horasabado4);
                formData.append('cbsabadosegunda', check_sabado_segunda);
                formData.append('cbcerradosabado', check_sabado_cerrado);

                formData.append('horadomingo1', horadomingo1);
                formData.append('horadomingo2', horadomingo2);
                formData.append('horadomingo3', horadomingo3);
                formData.append('horadomingo4', horadomingo4);
                formData.append('cbdomingosegunda', check_domingo_segunda);
                formData.append('cbcerradodomingo', check_domingo_cerrado);

                axios.post('/servicios/editar-horas', formData, {
                })
                    .then((response) => {
                        closeLoading();
                      if (response.data.success === 1) {
                          toastMensaje('success', 'Actualizado correctamente');
                          $('#modalOpciones').modal('hide');
                          $('#modalHorario').modal('hide');
                        }
                        else {
                          toastMensaje('error', 'Error al editar Horario');
                        }
                    })
                    .catch((error) => {
                        closeLoading();
                        toastMensaje('error', 'Error al editar Horario');
                    });
        }

        function verMapa(){
            var id = document.getElementById('id-global').value;
            window.location.href="{{ url('/admin/servicios/mapa/ubicacion') }}/"+id;
        }

        function verCategorias(){
            var id = document.getElementById('id-global').value;
            window.location.href="{{ url('/admin/categorias/') }}/"+id;
        }

        function verEtiquetas(){
            var id = document.getElementById('id-global').value;
            window.location.href="{{ url('/admin/servicios/etiquetas') }}/"+id;
        }

    </script>


@endsection
