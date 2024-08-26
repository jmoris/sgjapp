<nav class="sidebar">
    <div class="sidebar-header">
        <img style="margin-left: 10%; width:60%; height:auto;" class="sidebar-brand" src="/logo_joremet.png" />
        <div class="sidebar-toggler not-active">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="sidebar-body">
        <ul class="nav">
            <li class="nav-item nav-category">PRINCIPAL</li>
            <li class="nav-item {{ active_class(['dashboard']) }}">
                <a href="{{ url('/dashboard') }}" class="nav-link">
                    <i class="link-icon" data-feather="box"></i>
                    <span class="link-title">Dashboard</span>
                </a>
            </li>
            @if (has_permission('ver-administracion') || has_permission('ver-usuario'))
                <li class="nav-item nav-category">ADMINISTRACIÓN</li>
            @endif
            @if (has_permission('ver-usuario'))
                <li class="nav-item {{ active_class(['usuarios*']) }}">
                    <a href="{{ url('/usuarios') }}" class="nav-link">
                        <i class="mdi mdi-folder-account mdi-18"></i>
                        <span class="link-title" style="margin-left: 12px;">Usuarios</span>
                    </a>
                </li>
            @endif
            @if (has_permission('ver-administracion'))
                <li class="nav-item {{ active_class(['permisos*']) }}">
                    <a href="{{ url('/permisos') }}" class="nav-link">
                        <i class="mdi mdi-shield-account mdi-18"></i>
                        <span class="link-title" style="margin-left: 12px;">Roles y Permisos</span>
                    </a>
                </li>
                <li class="nav-item {{ active_class(['ajustes*']) }}">
                    <a href="{{ url('/ajustes') }}" class="nav-link">
                        <i class="mdi mdi-cog mdi-18"></i>
                        <span class="link-title" style="margin-left: 12px;">Ajustes Generales</span>
                    </a>
                </li>
            @endif
            <li class="nav-item nav-category">MAESTROS</li>
            @if (has_permission('ver-proveedor'))
                <li class="nav-item {{ active_class(['proveedores*']) }}">
                    <a href="{{ url('/proveedores') }}" class="nav-link">
                        <i class="mdi mdi-truck-delivery-outline mdi-18"></i>
                        <span class="link-title" style="margin-left: 12px;">Proveedores</span>
                    </a>
                </li>
            @endif
            @if (has_permission('ver-cliente'))
                <li class="nav-item {{ active_class(['clientes*']) }}">
                    <a href="{{ url('/clientes') }}" class="nav-link">
                        <i class="mdi mdi-account-group mdi-18"></i>
                        <span class="link-title" style="margin-left: 12px;">Clientes</span>
                    </a>
                </li>
            @endif
            @if (has_permission('ver-producto'))
                <li class="nav-item {{ active_class(['productos*']) }}">
                    <a href="{{ url('/productos') }}" class="nav-link">
                        <i class="mdi mdi-package-variant mdi-18"></i>
                        <span class="link-title" style="margin-left: 12px;">Productos</span>
                    </a>
                </li>
            @endif
            @if (has_permission('ver-bodega'))
                <li class="nav-item {{ active_class(['bodegas']) }}">
                    <a href="#" class="nav-link">
                        <i class="mdi mdi-warehouse mdi-18"></i>
                        <span class="link-title" style="margin-left: 12px;">Bodegas</span>
                    </a>
                </li>
            @endif
            @if (has_permission('ver-centro-costo'))
                <li class="nav-item {{ active_class(['centrocosto']) }}">
                    <a href="#" class="nav-link">
                        <i class="mdi mdi-finance mdi-18"></i>
                        <span class="link-title" style="margin-left: 12px;">Centros de Costos</span>
                    </a>
                </li>
            @endif
            <li class="nav-item nav-category">VENTAS</li>
            @if (has_permission('ver-proyecto'))
                <li class="nav-item {{ active_class(['ventas/proyectos*']) }}">
                    <a href="{{ url('ventas/proyectos') }}" class="nav-link">
                        <i class="mdi mdi-file-document-arrow-right mdi-18"></i>
                        <span class="link-title" style="margin-left: 12px;">Proyectos</span>
                    </a>
                </li>
            @endif
            @if (has_permission('ver-factura')||has_permission('ver-guia-despacho')||has_permission('ver-nota-credito')||has_permission('ver-nota-debito'))
            <li class="nav-item {{ active_class(['ventas/*']) }}">
                <a class="nav-link" data-bs-toggle="collapse" href="#ventas" role="button"
                    aria-expanded="{{ is_active_route(['ventas/*']) }}" aria-controls="ventas">
                    <i class="mdi mdi-file-document-multiple-outline mdi-18"></i>
                    <span class="link-title" style="margin-left: 12px;">Documentos</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse {{ show_class(['ventas/*']) }}" id="ventas">
                    <ul class="nav sub-menu">
                        @if (has_permission('ver-factura'))
                            <li class="nav-item">
                                <a href="{{ url('/ventas/facturas') }}"
                                    class="nav-link {{ active_class(['ventas/facturas*']) }}">
                                    Facturas
                                </a>
                            </li>
                        @endif
                        @if (has_permission('ver-guia-despacho'))
                        <li class="nav-item">
                            <a href="{{ url('/ventas/guiasdespacho') }}"
                                class="nav-link {{ active_class(['ventas/guiasdespacho*']) }}">
                                Guias de Despacho
                            </a>
                        </li>
                        @endif
                        @if (has_permission('ver-nota-credito'))
                        <li class="nav-item">
                            <a href="{{ url('/ventas/notascredito') }}"
                                class="nav-link {{ active_class(['ventas/notascredito*']) }}">
                                Notas de Credito
                            </a>
                        </li>
                        @endif
                        @if (has_permission('ver-nota-debito'))
                        <li class="nav-item">
                            <a href="{{ url('/ventas/notasdebito') }}"
                                class="nav-link {{ active_class(['ventas/notasdebito*']) }}">
                                Notas de Debito
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
            @if (has_permission('ver-presupuesto'))
                <li class="nav-item {{ active_class(['apps/presupuestos']) }}">
                    <a href="#" class="nav-link">
                        <i class="link-icon" data-feather="message-square"></i>
                        <span class="link-title">Presupuestos</span>
                    </a>
                </li>
            @endif
            @if (has_permission('ver-pago'))
                <li class="nav-item {{ active_class(['apps/pagos']) }}">
                    <a href="#" class="nav-link">
                        <i class="link-icon" data-feather="message-square"></i>
                        <span class="link-title">Pagos</span>
                    </a>
                </li>
            @endif
            <li class="nav-item nav-category">COMPRAS</li>
            @if (has_permission('ver-orden-compra'))
                <li class="nav-item {{ active_class(['compras/ordenescompra*']) }}">
                    <a href="{{ url('compras/ordenescompra') }}" class="nav-link">
                        <i class="mdi mdi-file-document-arrow-right-outline mdi-18"></i>
                        <span class="link-title" style="margin-left: 12px;">Ordenes de Compra</span>
                    </a>
                </li>
            @endif

            @if (has_permission('ver-pedido-material'))
            <li class="nav-item {{ active_class(['compras/pedidosmateriales*']) }}">
                <a href="{{ url('/compras/pedidosmateriales') }}" class="nav-link">
                    <i class="mdi mdi-archive-search-outline mdi-18"></i>
                    <span class="link-title" style="margin-left: 12px;">Pedido Materiales</span>
                </a>
            </li>
                <li class="nav-item {{ active_class(['apps/inventario']) }}">
                    <a href="#" class="nav-link">
                        <i class="link-icon" data-feather="message-square"></i>
                        <span class="link-title">Inventario</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</nav>
