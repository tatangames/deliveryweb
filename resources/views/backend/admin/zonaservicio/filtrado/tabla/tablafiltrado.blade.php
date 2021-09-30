<!-- Aqui se filtran las posiciones de un servicio (snack, restaurante, etc) por zona -->

<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="table" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Identificador</th>
                            <th>Cerrado Emergencia</th>
                            <th>Activo</th>
                            <th>Nombre Servicio</th>
                            <th>Posición</th>
                        </tr>
                        </thead>
                        <tbody id="tablecontents">
                        @foreach($servicio as $dato)
                            <tr class="row1" data-id="{{ $dato->id }}">

                                <td>{{ $loop->iteration }}</td>
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

                                <td>{{ $dato->posicion }}</td>
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
                    id: $(this).attr('data-id'), // esto es el id: zona servicio
                    posicion: index+1
                });
            });

            openLoading();

            let formData = new FormData();
            formData.append('[order]', order);

            axios.post('/zonaservicios/ordenar',{
                'order': order
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastMensaje('success', 'Actualizado correctamente');
                        recargar();
                    }else{
                        toastMensaje('error', 'Error al actualizar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastMensaje('error', 'Error al actualizar');
                });
        }

    });
</script>
