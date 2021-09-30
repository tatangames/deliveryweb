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
                                <th>Fecha</th>
                                <th>Pago Total</th>
                                <th>Comisión</th>
                                <th>Monedero</th>
                                <th>Nota</th>
                                <th>Correo</th>
                                <th>Revisada</th>
                                <th>Cliente</th>
                                <th>Teléfono</th>
                                <th>Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($lista as $dato)
                                <tr>
                                    <td>{{ $dato->fecha }}</td>
                                    <td>{{ $dato->pago_total }}</td>
                                    <td>{{ $dato->comision }}</td>
                                    <td>{{ $dato->monedas }}</td>
                                    <td>{{ $dato->nota }}</td>
                                    <td>{{ $dato->correo }}</td>

                                    <td>
                                        @if($dato->revisada == 0)
                                            <span class="badge bg-danger">No</span>
                                        @else
                                            <span class="badge bg-primary">Sí</span>
                                        @endif
                                    </td>

                                    <td>{{ $dato->nombre }}</td>
                                    <td>{{ $dato->telefono }}</td>

                                    <td>
                                        <button type="button" class="btn btn-primary btn-xs" onclick="informacion({{ $dato->id }})">
                                            <i class="fas fa-eye" title="Información"></i>&nbsp; Información
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
