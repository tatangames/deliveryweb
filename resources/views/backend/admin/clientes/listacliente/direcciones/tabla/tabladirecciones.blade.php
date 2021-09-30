<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Dirección</th>
                                <th>Num. Casa</th>
                                <th>Referencia</th>
                                <th>Seleccionado</th>
                                <th>Direc. Revisada</th>
                                <th>Zona</th>
                                <th>Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($lista as $dato)
                                <tr>
                                    <td>{{ $dato->nombre }}</td>
                                    <td>{{ $dato->direccion }}</td>
                                    <td>{{ $dato->numero_casa }}</td>
                                    <td>{{ $dato->punto_referencia }}</td>
                                    <td>
                                        @if($dato->seleccionado == 0)
                                            <span class="badge bg-danger">No</span>
                                        @else
                                            <span class="badge bg-success">Si</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($dato->revisado == 0)
                                            <span class="badge bg-danger">No</span>
                                        @else
                                            <span class="badge bg-success">Si</span>
                                        @endif
                                    </td>

                                    <td>{{ $dato->zona }}</td>

                                    <td><button type="button" class="btn btn-success btn-xs" onclick="opciones({{ $dato->id }})">
                                            <i class="fas fa-edit" title="Opciones"></i> Opciones
                                        </button></td>

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
