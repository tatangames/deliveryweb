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
                                <th>Identificador</th>
                                <th>Cerrado Emergencia</th>
                                <th>Activo</th>
                                <th>Nombre Servicio</th>
                                <th>Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($servicio as $dato)
                                <tr>
                                    <td>{{ $dato->nombre }}</td>
                                    <td>{{ $dato->identificador }}</td>
                                    <td>
                                        @if($dato->cerrado_emergencia == 0)
                                            <span class="badge bg-danger">Desactivado</span>
                                        @else
                                            <span class="badge bg-success">Activado</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($dato->activo == 0)
                                            <span class="badge bg-danger">Desactivado</span>
                                        @else
                                            <span class="badge bg-success">Activado</span>
                                        @endif
                                    </td>
                                    <td>{{ $dato->nombreServicio }}</td>

                                    <td>
                                        <button type="button" class="btn btn-primary btn-xs" onclick="verTodaOpciones({{ $dato->id }})">
                                            <i class="fas fa-eye" title="Opciones"></i>&nbsp; Opciones
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
                "sEmptyTable": "Ning??n dato disponible en esta tabla",
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
                    "sLast": "??ltimo",
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
