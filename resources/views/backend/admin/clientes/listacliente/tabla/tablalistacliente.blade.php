<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="tabla" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Fecha de Registro</th>
                                <th>Teléfono</th>
                                <th>Area</th>
                                <th>Correo</th>
                                <th>Monedero</th>
                                <th>Activo</th>
                                <th>Imagen</th>
                                <th>Opciones</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($lista as $dato)
                                <tr>
                                    <td>{{ $dato->fecha }}</td>
                                    <td>{{ $dato->telefono }}</td>
                                    <td>{{ $dato->area }}</td>
                                    <td>{{ $dato->correo }}</td>
                                    <td>{{ $dato->monedero }}</td>
                                    <td>
                                        @if($dato->activo == 0)
                                            <span class="badge bg-danger">Inactivo</span>
                                        @else
                                            <span class="badge bg-success">Activo</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($dato->imagen != null)
                                        <center><img alt="Servicios" src="{{ url('storage/imagenes/'.$dato->imagen) }}" width="100px" height="100px" /></center>
                                        @else

                                        @endif
                                    </td>

                                    <td><button type="button" class="btn btn-success btn-xs" onclick="verTodaOpciones({{ $dato->id }})">
                                        <i class="fas fa-edit" title="Ver Opciones"></i> Ver Opciones
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
