<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Bloqueo</th>
                                <th>Mensaje</th>
                                <th>Estado</th>
                                <th>Identificador</th>
                                <th>Hora Abierto</th>
                                <th>Hora Cerrado</th>
                                <th>Tiempo Extra</th>
                                <th>Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($zonas as $dato)
                                <tr>
                                    <td>{{ $dato->nombre }}</td>
                                    <td>{{ $dato->descripcion }}</td>
                                    <td>
                                        @if($dato->saturacion == 0)
                                            <span class="badge bg-danger">Desactivado</span>
                                        @else
                                            <span class="badge bg-success">Activado</span>
                                        @endif
                                    </td>
                                    <td>{{ $dato->mensaje_bloqueo }}</td>
                                    <td>
                                        @if($dato->activo == 0)
                                            <span class="badge bg-danger">Inactivo</span>
                                        @else
                                            <span class="badge bg-success">Activo</span>
                                        @endif
                                    </td>
                                    <td>{{ $dato->identificador }}</td>
                                    <td>{{ $dato->hora_abierto_delivery }}</td>
                                    <td>{{ $dato->hora_cerrado_delivery }}</td>
                                    <td>{{ $dato->tiempo_extra }}</td>

                                    <td>
                                        <button type="button" class="btn btn-primary btn-xs" onclick="verInformacion({{ $dato->id }})">
                                            <i class="fas fa-eye" title="Editar"></i>&nbsp; Editar
                                        </button>
                                        <br><br>
                                        <button type="button" class="btn btn-success btn-xs" onclick="vistaPoligonos({{ $dato->id }})">
                                            <i class="fa fa-location-arrow" title="Agregar Poligonos"></i>&nbsp; Poligonos
                                        </button>
                                        <br><br>
                                        <button type="button" class="btn btn-warning btn-xs" onclick="verMapa({{ $dato->id }})">
                                            <i class="fa fa-location-arrow" title="Mapa"></i>&nbsp; Mapa
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    $(function () {
        $("#tabla").DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,

            "language": {

                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }

            },
            "responsive": true, "lengthChange": false, "autoWidth": false,
        });
    });


</script>
