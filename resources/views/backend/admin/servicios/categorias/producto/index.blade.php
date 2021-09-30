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
            <h1>Lista de Productos</h1>
        </div>
        <br>
        <button type="button" onclick="abrirModalAgregar()" class="btn btn-success btn-sm">
            <i class="fas fa-pencil-alt"></i>
            Nuevo Producto
        </button>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Listado</h3>
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

<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Producto</h4>
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
                                    <label>Nombre</label>
                                    <input type="text" maxlength="150" class="form-control" id="nombre-nuevo" placeholder="Nombre producto">
                                </div>

                                <div class="form-group">
                                    <div>
                                        <label>Imagen</label>
                                        <p>Tamaño recomendado de: 250 x -</p>
                                    </div>
                                    <br>
                                    <div class="col-md-10">
                                        <input type="file" style="color:#191818" id="imagen-nuevo" accept="image/jpeg, image/jpg, image/png"/>
                                    </div>
                                </div>

                                <div class="form-group" style="margin-left:0px">
                                    <label>Utiliza imagen</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="cbimagen-nuevo">
                                        <div class="slider round">
                                            <span class="on">Si</span>
                                            <span class="off">No</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label>Descripcion</label>
                                    <textarea maxlength="2000" rows="2" class="form-control" id="descripcion-nuevo" placeholder="Descripción producto"></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Precio producto</label>
                                    <input type="number" step="any" class="form-control" id="precio-nuevo">
                                </div>

                                <div class="form-group" style="margin-left:0px">
                                    <label>Disponibilidad</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="cbdisponibilidad-nuevo">
                                        <div class="slider round">
                                            <span class="on">Si</span>
                                            <span class="off">No</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-group" style="margin-left:0px">
                                    <label>Activo</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="cbactivo-nuevo">
                                        <div class="slider round">
                                            <span class="on">Si</span>
                                            <span class="off">No</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-group" style="margin-left:0px">
                                    <label>Utiliza nota</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="cbnota-nuevo">
                                        <div class="slider round">
                                            <span class="on">Si</span>
                                            <span class="off">No</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label>Nota (ejemplo: si un producto necesita opciones a elegir)</label>
                                    <input type="text" maxlength="150" class="form-control" id="nota-nuevo">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="nuevo()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- modal editar -->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar producto</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-editar">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label style="color:#191818">Categoria</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="selectcategoria-editar">
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="hidden" id="idproducto-editar">
                                    <input type="text" maxlength="150" class="form-control" id="nombre-editar" placeholder="Nombre producto">
                                </div>

                                <div class="form-group">
                                    <label>Descripcion</label>
                                    <textarea maxlength="2000" rows="2" class="form-control" id="descripcion-editar" placeholder="Descripción producto"></textarea>
                                </div>

                                <div class="form-group">
                                    <label id="txtImagen">Imagen producto</label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <img id="img-producto" src="{{ asset('images/foto-default.png') }}" width="40%">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Precio producto</label>
                                    <input type="number" step="any" class="form-control" id="precio-editar">
                                </div>

                                <div class="form-group" style="margin-left:0px">
                                    <label>Activo</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="cbactivo-editar">
                                        <div class="slider round">
                                            <span class="on">Si</span>
                                            <span class="off">No</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-group" style="margin-left:0px">
                                    <label>disponibilidad</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="cbdisponibilidad-editar">
                                        <div class="slider round">
                                            <span class="on">Si</span>
                                            <span class="off">No</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-group" style="margin-left:0px">
                                    <label>Utiliza nota (ejemplo: cuando un producto necesita una descripción)</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="cbutilizanota-editar">
                                        <div class="slider round">
                                            <span class="on">Si</span>
                                            <span class="off">No</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label>Nota</label>
                                    <input type="text" maxlength="150" class="form-control" id="nota-editar">
                                </div>

                                <div class="form-group">
                                    <div>
                                        <label>Imagen</label>
                                        <p>Tamaño recomendado de: 250 x -</p>
                                    </div>
                                    <br>
                                    <div class="col-md-10">
                                        <input type="file" style="color:#191818" id="imagen-editar" accept="image/jpeg, image/jpg, image/png"/>
                                    </div>
                                </div>

                                <div class="form-group" style="margin-left:0px">
                                    <label>Utiliza imagen?</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="cbimagen-editar">
                                        <div class="slider round">
                                            <span class="on">Si</span>
                                            <span class="off">No</span>
                                        </div>
                                    </label>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editar()">Guardar</button>
            </div>
        </div>
    </div>
</div>

@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery-ui-drag.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/datatables-drag.min.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            id = {{ $id }};
            $('#id-editar').val(id);
            var ruta = "{{ url('admin/productos/tablas') }}/"+id;
            $('#tablaDatatable').load(ruta);
        });
    </script>

    <script>

        function recargar(){
            id = {{ $id }};
            $('#id-editar').val(id);
            var ruta = "{{ url('admin/productos/tablas') }}/"+id;
            $('#tablaDatatable').load(ruta);
        }

        // modal nuevo
        function abrirModalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function nuevo(){

            // id categoria
            id = {{ $id }};
            var nombre = document.getElementById('nombre-nuevo').value;
            var imagen = document.getElementById('imagen-nuevo');
            var descripcion = document.getElementById('descripcion-nuevo').value;
            var precio = document.getElementById('precio-nuevo').value;
            var nota = document.getElementById('nota-nuevo').value;

            var td = document.getElementById('cbdisponibilidad-nuevo').checked;
            var ta = document.getElementById('cbactivo-nuevo').checked;
            var tn = document.getElementById('cbnota-nuevo').checked;
            var ti = document.getElementById('cbimagen-nuevo').checked;

            var toggleDisponibilidad = td ? 1 : 0;
            var toggleActivo = ta ? 1 : 0;
            var toggleNota = tn ? 1 : 0;
            var toggleImagen = ti ? 1 : 0;

            if(nombre === ''){
                toastMensaje('error', 'Imagen es requerido');
                return;
            }

            if(nombre.length > 150){
                toastMensaje('error', 'Nombre máximo 150 caracteres');
                return;
            }

            if(imagen.files && imagen.files[0]){ // si trae imagen
                if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){
                    toastMensaje('error', 'Formato de imagen permitido: .png .jpg .jpeg');
                    return;
                }
            }else{
                if(toggleImagen === 1){
                    toastMensaje('error', 'Se debe elegir una imagen');
                    return;
                }
            }

            if(descripcion.length > 2000){
                toastMensaje('error', 'Descripción máximo 2000 caracteres');
                return;
            }

            if(precio === ''){
                toastMensaje('error', 'Precio es requerido');
                return;
            }

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            if(!precio.match(reglaNumeroDecimal)) {
                toastMensaje('error', 'Precio debe ser número decimal');
                return;
            }

            if(precio < 0){
                toastMensaje('error', 'Precio no puede ser Negativo');
                return;
            }

            if(precio > 1000000){
                toastMensaje('error', 'Precio máximo 1 millón');
                return;
            }

            if(toggleNota === 1){
                if(nota === ''){
                    toastMensaje('error', 'Se debe ingresar una Nota');
                    return;
                }
            }

            openLoading();
            var formData = new FormData();

            formData.append('idcategoria', id);
            formData.append('nombre', nombre);
            formData.append('imagen', imagen.files[0]);
            formData.append('descripcion', descripcion);
            formData.append('precio', precio);
            formData.append('cbdisponibilidad', toggleDisponibilidad);
            formData.append('cbactivo', toggleActivo);
            formData.append('cbnota', toggleNota);
            formData.append('nota', nota);
            formData.append('cbimagen', toggleImagen);

            axios.post('/productos/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalAgregar').modal('hide');
                        toastMensaje('success', 'Registrado correctamente');
                        recargar();
                    } else{
                        toastMensaje('error', 'Error al Registrar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastMensaje('error', 'Error al Registrar');
                });

        }

        function informacion(id){
            document.getElementById("formulario-nuevo").reset();
            document.getElementById("formulario-editar").reset();

            openLoading();

            axios.post('/productos/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');

                        document.getElementById("selectcategoria-editar").options.length = 0;

                        $.each(response.data.categoria, function( key, val ){
                            if(response.data.producto.servicios_tipo_id === val.id){
                                $('#selectcategoria-editar').append('<option value="' +val.id +'" selected="selected">'+val.nombre+'</option>');
                            }else{
                                $('#selectcategoria-editar').append('<option value="' +val.id +'">'+val.nombre+'</option>');
                            }
                        });

                        $('#idproducto-editar').val(response.data.producto.id);
                        $('#nombre-editar').val(response.data.producto.nombre);
                        $('#descripcion-editar').val(response.data.producto.descripcion);

                        if(response.data.producto.utiliza_imagen === 1){
                            $('#img-producto').prop("src","{{ url('storage/imagenes') }}"+'/'+ response.data.producto.imagen);
                        }else{
                            if(response.data.producto.imagen != null){
                                $('#img-producto').prop("src","{{ url('storage/imagenes') }}"+'/'+ response.data.producto.imagen);
                            }else{
                                $('#img-producto').prop("src","{{ asset('images/foto-default.png') }}");
                            }
                        }

                        $('#precio-editar').val(response.data.producto.precio);

                        if(response.data.producto.disponibilidad === 0){
                            $("#cbdisponibilidad-editar").prop("checked", false);
                        }else{
                            $("#cbdisponibilidad-editar").prop("checked", true);
                        }

                        if(response.data.producto.activo === 0){
                            $("#cbactivo-editar").prop("checked", false);
                        }else{
                            $("#cbactivo-editar").prop("checked", true);
                        }

                        if(response.data.producto.utiliza_imagen === 0){
                            $("#cbimagen-editar").prop("checked", false);
                        }else{
                            $("#cbimagen-editar").prop("checked", true);
                        }

                        if(response.data.producto.utiliza_nota === 0){
                            $("#cbutilizanota-editar").prop("checked", false);
                        }else{
                            $("#cbutilizanota-editar").prop("checked", true);
                        }

                        $('#cantidadorden-editar').val(response.data.producto.cantidad_por_orden);

                        $('#nota-editar').val(response.data.producto.nota);
                    }else{
                        toastMensaje('error', 'Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastMensaje('error', 'Información no encontrada');
                });
        }

        function editar(){

            var id = document.getElementById('idproducto-editar').value; // producto id
            var selectcategoria = document.getElementById('selectcategoria-editar').value;
            var nombre = document.getElementById('nombre-editar').value;

            var descripcion = document.getElementById('descripcion-editar').value;
            var precio = document.getElementById('precio-editar').value;
            var td = document.getElementById('cbdisponibilidad-editar').checked;
            var ta = document.getElementById('cbactivo-editar').checked;

            var tn = document.getElementById('cbutilizanota-editar').checked;
            var nota = document.getElementById('nota-editar').value;
            var ti = document.getElementById('cbimagen-editar').checked;
            var imagen = document.getElementById('imagen-editar');

            var toggleDisponibilidad = td ? 1 : 0;
            var toggleActivo = ta ? 1 : 0;
            var toggleNota = tn ? 1 : 0;
            var toggleImagen = ti ? 1 : 0;

            if(nombre === ''){
                toastMensaje('error', 'Imagen es requerido');
                return;
            }

            if(nombre.length > 150){
                toastMensaje('error', 'Nombre máximo 150 caracteres');
                return;
            }

            if(imagen.files && imagen.files[0]){ // si trae imagen
                if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){
                    toastMensaje('error', 'Formato de imagen permitido: .png .jpg .jpeg');
                    return;
                }
            }

            if(descripcion.length > 2000){
                toastMensaje('error', 'Descripción máximo 2000 caracteres');
                return;
            }

            if(precio === ''){
                toastMensaje('error', 'Precio es requerido');
                return;
            }

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            if(!precio.match(reglaNumeroDecimal)) {
                toastMensaje('error', 'Precio debe ser número decimal');
                return;
            }

            if(precio < 0){
                toastMensaje('error', 'Precio no puede ser Negativo');
                return;
            }

            if(precio > 1000000){
                toastMensaje('error', 'Precio máximo 1 millón');
                return;
            }

            if(toggleNota === 1){
                if(nota === ''){
                    toastMensaje('error', 'Se debe ingresar una Nota');
                    return;
                }

                if(nota.length > 150){
                    toastMensaje('error', 'Nota máximo 150 caracteres');
                    return;
                }
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);
            formData.append('selectcategoria', selectcategoria);
            formData.append('precio', precio);
            formData.append('cbdisponibilidad', toggleDisponibilidad);
            formData.append('cbactivo', toggleActivo);
            formData.append('cbutilizanota', toggleNota);
            formData.append('cbimagen', toggleImagen);
            formData.append('nota', nota);
            formData.append('imagen', imagen.files[0]);

            axios.post('/productos/editar', formData, {
            })
                .then((response) => {

                    closeLoading();

                    if (response.data.success === 1) {
                        toastMensaje('error', 'Se Activara imagen pero el producto no Tiene');
                    } else if (response.data.success === 2) {
                        $('#modalEditar').modal('hide');
                        toastMensaje('success', 'Actualizado correctamente');
                        recargar();
                    } else{
                        toastMensaje('error', 'Error al actualizar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastMensaje('error', 'Error al actualizar');
                });
        }

    </script>


@endsection
