<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="table" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Disponibilidad</th>
                            <th>Activo</th>
                            <th>Posicion</th>
                            <th>Opciones</th>
                        </tr>
                        </thead>
                        <tbody id="tablecontents">
                        @foreach($producto as $dato)
                            <tr class="row1" data-id="{{ $dato->id }}">

                                <td>{{ $dato->id }}</td>
                                <td>{{ $dato->nombre }}</td>
                                <td>{{ $dato->precio }}</td>
                                <td>
                                    @if($dato->disponibilidad == 0)
                                        <span class="badge bg-danger">No disponible</span>
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



                                <td>{{ $dato->posicion }}</td>

                                <td>
                                    <button type="button" class="btn btn-primary btn-xs" onclick="informacion({{ $dato->id }})">
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
</section>

<script type="text/javascript">
    $(document).ready(function() {

        $( "#tablecontents" ).sortable({
            items: "tr",
            cursor: 'move',
            opacity: 0.6,
            update: function() {
                sendOrderToServer();
            }
        });

        function sendOrderToServer() {

            var order = [];
            $('tr.row1').each(function(index,element) {
                order.push({
                    id: $(this).attr('data-id'),
                    posicion: index+1
                });
            });

            openLoading();

            axios.post('/productos/ordenar',  {
                'order': order
            })
                .then((response) => {
                    closeLoading();
                    toastMensaje('success', 'Actualizado correctamente');
                    recargar();
                })
                .catch((error) => {
                    closeLoading();
                    toastMensaje('error', 'Error al actualizar');
                });
        }
    });

</script>
