<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
        <img src="{{ asset('images/logologin.png') }}" alt="Logo" class="brand-image img-circle elevation-3" >
        <span class="brand-text font-weight-light">Panel Web</span>
    </a>

    <div class="sidebar">

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="{{ route('index.estadisticas') }}" target="frameprincipal" class="nav-link">
                        <i class="fas fa-edit nav-icon"></i>
                        <p>Estadísticas</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="far fa-edit"></i>
                        <p>
                            Reportes
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('index.lista.reportes') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Ordenes</p>
                            </a>
                        </li>
                    </ul>
                </li>


                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="far fa-edit"></i>
                        <p>
                            Mapa
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('index.zonas') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Zonas</p>
                            </a>
                        </li>
                    </ul>

                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="far fa-edit"></i>
                        <p>
                            Tipos de Zona
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('index.tipos') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tipos</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('index.tipos.servicio') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tipos Servicios</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('index.tipos.servicio.zona') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tipos Servicio Zona</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('index.tiposserviciozona.posicionglobal') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Posición Global</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="far fa-edit"></i>
                        <p>
                            Negocios
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('index.lista.servicios') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Lista de Negocios</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('index.zonas.servicios') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Zona Servicios</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="far fa-edit"></i>
                        <p>
                            Ordenes
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">

                        <li class="nav-item">
                            <a href="{{ route('index.listado.ordenes') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Lista de Ordenes</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('index.listado.ordenes.cupon') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cupón Ordenes</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('index.lista.ordenes.comentarios') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Ordenes Calificación</p>
                            </a>
                        </li>

                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="far fa-edit"></i>
                        <p>
                            Propietarios
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('index.lista.propietarios') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Listado</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="far fa-edit"></i>
                        <p>
                            Motoristas
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('index.lista.motoristas') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Listado</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('index.lista.motoristas.asignados') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Motorista asignados</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('index.lista.motoristas.ordenes') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Motorista Ordenes</p>
                            </a>
                        </li>

                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="far fa-edit"></i>
                        <p>
                            Clientes
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">

                        <li class="nav-item">
                            <a href="{{ route('index.clientes.listado') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Lista de Clientes</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('index.clientes.intentos.sms') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Intentos SMS</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('index.clientes.numero.registro') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Número Registro</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('index.clientes.registrados.hoy') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Registrados Hoy</p>
                            </a>
                        </li>

                    </ul>
                </li>


                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="far fa-edit"></i>
                        <p>
                            Extras
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">

                        <li class="nav-item">
                            <a href="{{ route('index.listado.opciones') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Lista de Opciones</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('index.listado.etiquetas') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Lista de Etiquetas</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('index.listado.etiquetas.cliente') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Etiquetas Cliente</p>
                            </a>
                        </li>

                    </ul>
                </li>


                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="far fa-edit"></i>
                        <p>
                            Cupones
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">

                        <li class="nav-item">
                            <a href="{{ route('index.listado.cupones') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cupones</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('index.listado.cupones.zonas') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cupón para Zonas</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('index.listado.cupones.servicios') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cupón para Servicios</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('index.listado.cupon.envio') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cupón Asignar Envío</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('index.listado.cupon.producto') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cupón Asignar Producto</p>
                            </a>
                        </li>


                        <li class="nav-item">
                            <a href="{{ route('index.listado.tipos.de.cupones') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tipos de Cupones</p>
                            </a>
                        </li>

                    </ul>
                </li>


                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="far fa-edit"></i>
                        <p>
                            Monedero
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">

                        <li class="nav-item">
                            <a href="{{ route('index.listado.monedero') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Listado</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('index.listado.monedero.devuelto') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Monedero Devuelto</p>
                            </a>
                        </li>

                    </ul>
                </li>


                <!-- fin del acordeon -->
            </ul>
        </nav>




    </div>
</aside>






