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
                                <th style="width: 15%">Zona identificador</th>
                                <th style="width: 20%">Servicio identificador</th>
                                <th style="width: 10%">Activo</th>
                                <th style="width: 15%">Precio envío</th>
                                <th style="width: 15%">Ganancia Motorista</th>
                                <th style="width: 10%">Envio Gratis</th>
                                <th style="width: 12%">Min envio gratis</th>
                                <th style="width: 12%">Min Compra para envío gratis</th>
                                <th style="width: 12%">Bloqueo</th>
                                <th style="width: 12%">Mensaje Bloqueo</th>
                                <th style="width: 20%">Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($servicio as $dato)
                                <tr>
                                    <td>{{ $dato->identificador }}</td>

                                    <td>{{ $dato->idenServicio }}</td>

                                    <td>
                                        @if($dato->activo == 0)
                                            <span class="badge bg-danger">Inactivo</span>
                                        @else
                                            <span class="badge bg-success">Activo</span>
                                        @endif
                                    </td>

                                    <td>{{ $dato->precio_envio }}</td>

                                    <td>{{ $dato->ganancia_motorista }}</td>

                                    <td>
                                        @if($dato->zona_envio_gratis == 0)
                                            <span class="badge bg-danger">NO</span>
                                        @else
                                            <span class="badge bg-success">SI</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($dato->min_envio_gratis == 0)
                                            <span class="badge bg-danger">NO</span>
                                        @else
                                            <span class="badge bg-success">SI</span>
                                        @endif
                                    </td>

                                    <td>{{ $dato->costo_envio_gratis }}</td>

                                    <td>
                                        @if($dato->saturacion == 0)
                                            <span class="badge bg-danger">NO</span>
                                        @else
                                            <span class="badge bg-success">SI</span>
                                        @endif
                                    </td>

                                    <td>{{ $dato->mensaje_bloqueo }}</td>

                                    <td>
                                        <button type="button" class="btn btn-primary btn-xs" onclick="verInformacion({{ $dato->id }})">
                                            <i class="fas fa-eye" title="Editar"></i>&nbsp; Editar
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
