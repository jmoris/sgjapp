@extends('layout.master')

@section('title', 'Emisión de Orden de Compra')

@push('style')
    <style>
        tr.noBorder td {
            border: 0;
        }

        th,
        td {
            padding: 0px 0px;
        }
    </style>
@endpush

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Emisión de Orden de Compra</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h4 class="card-title mb-0">FORMULARIO DE NUEVA ORDEN DE COMPRA</h4>
                            </div>
                            <div class="row mx-3">
                                <div style="width:100%; margin-top:24px;"></div>
                                <div class="col-md-12">
                                    <form class="form" id="storeForm" method="post" onsubmit="procesarOrden(event)">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <div class="mb-2 border-bottom">
                                                    <h5>Información del emisor</h5>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="row mx-1">
                                                            <div class="row mb-2">
                                                                <label
                                                                    class="col-sm-4 col-form-label col-form-label-sm">Razón
                                                                    Social</label>
                                                                <div class="col-sm-8">
                                                                    <input type="text" name="razon_social_emisor"
                                                                        id="razon_social_emisor"
                                                                        class="form-control form-control-sm"
                                                                        value="{{ $emisor['razon_social'] }}" disabled>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <label
                                                                    class="col-sm-4 col-form-label col-form-label-sm">R.U.T.</label>
                                                                <div class="col-sm-8">
                                                                    <input type="text" name="rut_emisor" id="rut_emisor"
                                                                        class="form-control form-control-sm"
                                                                        value="{{ $emisor['rut'] }}" disabled>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <label
                                                                    class="col-sm-4 col-form-label col-form-label-sm">Actividad
                                                                    Económica</label>
                                                                <div class="col-sm-8">
                                                                    <input type="text" name="giro_emisor"
                                                                        id="giro_emisor"
                                                                        class="form-control form-control-sm"
                                                                        value="{{ $emisor['giro'] }}" disabled>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="row mx-1">
                                                            <div class="row mb-2">
                                                                <label
                                                                    class="col-sm-4 col-form-label col-form-label-sm">Dirección</label>
                                                                <div class="col-sm-8">
                                                                    <input type="text" name="direccion_emisor"
                                                                        id="direccion_emisor"
                                                                        class="form-control form-control-sm" disabled
                                                                        value="{{ $emisor['direccion'] }}">
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <label
                                                                    class="col-sm-4 col-form-label col-form-label-sm">Comuna</label>
                                                                <div class="col-sm-8">
                                                                    <input type="text" name="comuna_emisor"
                                                                        id="communa_emisor"
                                                                        class="form-control form-control-sm"
                                                                        value="{{ App\Comuna::find($emisor['comuna'])->nombre }}"
                                                                        disabled>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-2 border-bottom">
                                                            <h5>Información del proveedor</h5>
                                                        </div>
                                                        <div class="row mx-1">
                                                            <div class="row mb-2">
                                                                <label
                                                                    class="col-sm-4 col-form-label col-form-label-sm">Razón
                                                                    Social</label>
                                                                <div class="col-sm-8">
                                                                    <select name="razon_social" id="razon_social"
                                                                        class="form-control form-control-sm"
                                                                        onchange="seleccionarProveedor()">
                                                                        <option value="">Seleccione un proveedor
                                                                        </option>
                                                                        @foreach ($proveedores as $proveedor)
                                                                            <option value="{{ $proveedor->id }}">
                                                                                {{ $proveedor->razon_social }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <label
                                                                    class="col-sm-4 col-form-label col-form-label-sm">R.U.T.</label>
                                                                <div class="col-sm-8">
                                                                    <input type="text" name="rut" id="rut"
                                                                        class="form-control form-control-sm" disabled>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <label
                                                                    class="col-sm-4 col-form-label col-form-label-sm">Actividad
                                                                    Económica</label>
                                                                <div class="col-sm-8">
                                                                    <input type="text" name="giro" id="giro"
                                                                        class="form-control form-control-sm" disabled>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <label
                                                                    class="col-sm-4 col-form-label col-form-label-sm">Dirección</label>
                                                                <div class="col-sm-8">
                                                                    <input type="text" name="direccion" id="direccion"
                                                                        class="form-control form-control-sm" disabled>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <label
                                                                    class="col-sm-4 col-form-label col-form-label-sm">Comuna</label>
                                                                <div class="col-sm-8">
                                                                    <input type="text" name="comuna" id="comuna"
                                                                        class="form-control form-control-sm" disabled>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-2 border-bottom">
                                                            <h5>Información del documento</h5>
                                                        </div>
                                                        <div class="row mx-1">
                                                            <div class="row mb-2">
                                                                <label
                                                                    class="col-sm-4 col-form-label col-form-label-sm">Tipo
                                                                    documento</label>
                                                                <div class="col-sm-8">
                                                                    <input type="text" name="tipo_doc" id="tipo_doc"
                                                                        value="Orden de Compra (801)"
                                                                        class="form-control form-control-sm" disabled>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <label
                                                                    class="col-sm-4 col-form-label col-form-label-sm">Fecha
                                                                    emisión</label>
                                                                <div class="col-sm-8">
                                                                    <input type="date" name="fecha_emision"
                                                                        id="fecha_emision"
                                                                        class="form-control form-control-sm"
                                                                        value="{{ date('Y-m-d') }}"
                                                                        max="{{ date('Y-m-d', strtotime('+1 days')) }}">
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <label
                                                                    class="col-sm-4 col-form-label col-form-label-sm">Tipo
                                                                    de pago</label>
                                                                <div class="col-sm-8 align-bottom">
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="tipo_pago" id="tipo_pago"
                                                                            value="1" checked>
                                                                        <label class="form-check-label"
                                                                            for="tipo_pago">Credito</label>
                                                                    </div>
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="tipo_pago" id="tipo_pago"
                                                                            value="2">
                                                                        <label class="form-check-label"
                                                                            for="tipo_pago">Contado</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-2 border-bottom">
                                                            <h5>Información de obra</h5>
                                                        </div>
                                                        <div class="row mx-1">
                                                            <div class="row mb-2">
                                                                <label
                                                                    class="col-sm-4 col-form-label col-form-label-sm">Nombre
                                                                    Obra/Proyecto</label>
                                                                <div class="col-sm-8" id="inputProyecto">
                                                                    <select class="form-control form-control-sm"
                                                                        id="nombre_proyecto" name="nombre_proyecto">
                                                                        <option>Seleccione proyecto</option>
                                                                        @foreach ($proyectos as $proyecto)
                                                                            <option value="{{ $proyecto->id }}">
                                                                                {{ $proyecto->nombre }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <div class="col-md-12 mb-2 border-bottom">
                                                    <h5 class="d-inline">Detalle del documento</h5>
                                                    <div class="d-inline float-end"><a href="javascript:void(0)"
                                                            id="modoSimple" onclick="modoDetalle(0)">Simple</a> | <a
                                                            id="modoCompleto" href="javascript:void(0)"
                                                            onclick="modoDetalle(1)">Avanzado</a></div>
                                                </div>
                                                <div class="row mx-1">
                                                    <table id="tablaDetalle" class="table mb-4">
                                                        <thead>
                                                            <th>SKU</th>
                                                            <th>Item</th>
                                                            <th>Cantidad</th>
                                                            <th>Precio</th>
                                                            <th>Subtotal</th>
                                                            <th></th>
                                                        </thead>
                                                        <tbody>
                                                            <tr class="noBorder" id="rowDetalle">
                                                                <td style="width:15%;padding:4px;">
                                                                    <div class="input-group">
                                                                        <input id="skuTxt" type="text"
                                                                            class="form-control form-control-sm p-1"
                                                                            placeholder="SKU" />
                                                                        <button
                                                                            class="btn btn-sm btn-outline-secondary p-1"
                                                                            type="button" id="inputGroupFileAddon04"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#staticBackdrop">
                                                                            <i class="mdi mdi-magnify"></i>
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                                <td style="width:30%;padding:4px;">
                                                                    <input id="nombreTxt" type="text"
                                                                        class="form-control form-control-sm"
                                                                        placeholder="NOMBRE ITEM" />
                                                                </td>
                                                                <td style="width:15%;padding:4px;">
                                                                    <div class="input-group">
                                                                        <input id="cantidadTxt"
                                                                            onchange="calcSubtotalFila()" type="number"
                                                                            min="1" value="1"
                                                                            class="form-control form-control-sm"
                                                                            placeholder="CANT" />
                                                                        <select id="unidadTxt" class="form-control-sm"
                                                                            style="max-width: 100px;">
                                                                            @foreach ($unidades as $unidad)
                                                                                <option value="{{ $unidad->id }}">
                                                                                    {{ $unidad->abreviacion }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                                <td style="width:15%;padding:4px;"><input
                                                                        onchange="calcSubtotalFila()" id="precioTxt"
                                                                        value="0" type="text"
                                                                        class="form-control" placeholder="PRECIO" /></td>
                                                                <td style="width:18%;padding:4px;"><span
                                                                        style="vertical-align: bottom; text-align:right;"
                                                                        id="lblSubtotal">$ 0</span></td>
                                                                <td style="width:7%;padding:4px;">
                                                                    <button type="button" onclick="agregarDetalle()"
                                                                        title="Agregar detalle a la lista"
                                                                        class="btn btn-sm btn-outline-primary"
                                                                        style="padding:.25em .5em; float:right;">
                                                                        <span class="mdi mdi-plus"></span></button>
                                                                    <button type="button"
                                                                        onclick="agregarGuardarDetalle()"
                                                                        class="btn btn-sm btn-outline-dark"
                                                                        title="Guardar producto"
                                                                        style="padding:.25em .5em; margin-right: 0.25em; float:right;">
                                                                        <span
                                                                            class="mdi mdi-content-save-plus"></span></button>
                                                                </td>
                                                            </tr>
                                                            <tr id="rowDetalleAvanzado" class="noBorder"
                                                                style="display: none;">
                                                                <td colspan="2" style="padding:4px;">
                                                                    <textarea id="descripcionTxt" rows="2" class="form-control form-control-sm"
                                                                        placeholder="DESCRIPCION DEL ITEM"></textarea>
                                                                </td>
                                                                <td colspan="2" class="align-top"
                                                                    style="padding:4px;">
                                                                    <select id="exencion"
                                                                        class="form-control form-control-sm">
                                                                        <option>Indicador Exención</option>
                                                                        <option value="1">No afecto o exento de IVA
                                                                        </option>
                                                                        <option value="2">Producto o servicio no
                                                                            facturable</option>
                                                                        <option value="3">Item no venta</option>
                                                                        <option value="4">Producto o servicio no
                                                                            facturable negativo</option>
                                                                    </select>
                                                                </td>
                                                                <td colspan="2" class="align-top"
                                                                    style="padding:4px;">
                                                                    <select id="exencion"
                                                                        class="form-control form-control-sm">
                                                                        <option>Impuesto adicional</option>
                                                                        <option value="1">Impuesto bebidas</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <div class="col-md-7">
                                                        <div class="mb-2 border-bottom">
                                                            <h5>Glosa documento</h5>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <textarea id="glosaTxt" maxlength="250" class="form-control mt-3" rows="5"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <div class="mb-2 border-bottom">
                                                            <h5>Resumen de montos</h5>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-7">
                                                                    <p>Subtotal </p>
                                                                </div>
                                                                <div class="col-md-5 text-end">
                                                                    <p id="lblSubtotalDoc">$0</p>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-5 pr-0">
                                                                    <p>Descuento global </p>
                                                                </div>
                                                                <div class="col-md-4 pl-0">
                                                                    <div class="input-group">
                                                                        <input step="any" min="0"
                                                                            max="100" value="0"
                                                                            class="form-control form-control-sm"
                                                                            type="number" name="descuentoglobal"
                                                                            id="descuentoglobal" />
                                                                        <span class="input-group-text">%</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 pl-0 my-0 text-end">
                                                                    <p class="my-0" id="lbldescuentoglobal">$0</p>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-7">
                                                                    <p>Monto exento </p>
                                                                </div>
                                                                <div class="col-md-5 text-end">
                                                                    <p id="lblexento">$0</p>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-7">
                                                                    <p>Monto neto </p>
                                                                </div>
                                                                <div class="col-md-5 text-end">
                                                                    <p id="lblneto">$0</p>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-7">
                                                                    <p>IVA </p>
                                                                </div>
                                                                <div class="col-md-5 text-end">
                                                                    <p id="lbliva">$0</p>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-7">
                                                                    <p>Impuestos adicionales </p>
                                                                </div>
                                                                <div class="col-md-5 text-end">
                                                                    <p id="lblimpad">$0</p>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-7">
                                                                    <p><b>Total </b></p>
                                                                </div>
                                                                <div class="col-md-5 text-end">
                                                                    <p id="lbltotal">$0</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="float-end">
                                            <button type="submit" class="btn btn-primary submit"><i
                                                    class="mdi mdi-content-save"></i> Guardar</button>
                                        </div>
                                        <button type="button" class="btn btn-danger"
                                            onclick="location.href = '/compras/ordenescompra'">
                                            <i class="mdi mdi-cancel"></i>
                                            Cancelar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Buscador de productos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="text" placeholder="NOMBRE DEL ITEM" class="form-control form-control-sm" />
                            <table id="productosTable" class="mt-3 table">
                                <thead>
                                    <th>SKU</th>
                                    <th>Nombre</th>
                                    <th>Precio</th>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="selectDetalle()">Seleccionar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        p {
            margin-top: 0;
            margin-bottom: 1em;
        }

        td {
            vertical-align: middle;
        }

        tr {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .table tbody tr.highlight td {
            background-color: gainsboro;
        }
    </style>
@endpush
@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"
        integrity="sha512-efAcjYoYT0sXxQRtxGY37CKYmqsFVOIwMApaEbrxJr4RwqVVGw8o+Lfh/+59TU07+suZn1BWq4fDl5fdgyCNkw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush

@push('custom-scripts')
    <script>
        var index = 0;
        var detalles = [];
        var subtotalDoc = 0;
        var unidades = {!! json_encode($unidades) !!};
        var selectedProducto = null;
        var productos = [];
        // Aqui se inicializan las librerias
        $(document).ready(function() {
            $("#precioTxt").inputmask('numeric', {
                prefix: '$ ',
                radixPoint: ',',
                groupSeparator: '.',
                rightAlign: false
            });
            $('#razon_social').select2();
            $('#productosTable').on('click', 'tbody tr', function(event) {
                $(this).addClass('highlight').siblings().removeClass('highlight');
            });

            $('#productosTable').on('dblclick', 'tbody tr', function(event) {
                selectDetalle();
            });
        });

        function selectDetalle(){
            var item = $('#productosTable tbody tr.highlight');
            var sku = $(item).find('td:eq(0)').text();
            var nombre = $(item).find('td:eq(1)').text();
            var descripcion = $(item).find('td:eq(3)').text();
            var precio = parseInt($(item).find('td:eq(2)').text().replace(/[^0-9]/gi, ''));
            if(sku == '' && nombre == '' && precio == ''){
                $.toast({
                    type: 'error',
                    title: 'Error en formulario',
                    subtitle: 'ahora',
                    position: 'top-right',
                    content: 'Debe seleccionar un producto para agregar al documento.',
                    delay: 15000
                });
                return;
            }
            selectedProducto = {
                sku,
                nombre,
                descripcion,
                precio
            };
            $('#skuTxt').val(sku);
            $('#nombreTxt').val(nombre);
            $('#descripcionTxt').val(descripcion);
            $('#precioTxt').val(precio);
            calcSubtotalFila();
            $('#cantidadTxt').focus();
            $("#staticBackdrop").modal('hide');
        }

        function cambiarInputProyecto() {
            var input = $('#manualProyecto').is(':checked');
            if (input) {
                $('#nombre_proyecto').hide();
                $('#nombre_proyectoTxt').show();
            } else {
                $('#nombre_proyecto').show();
                $('#nombre_proyectoTxt').hide();
            }
        }

        function modoDetalle(modo) {
            if (modo == 0) {
                $('#rowDetalleAvanzado').fadeOut(200);
            } else if (modo == 1) {
                $('#rowDetalleAvanzado').fadeIn(200);
            }
            return false;
        }

        function procesarOrden(e) {
            e.preventDefault();

            // Verificamos que se haya seleccionado un proveedor
            var proveedorId = $('#razon_social').val();
            if (proveedorId == '') {
                $.toast({
                    type: 'error',
                    title: 'Error en formulario',
                    subtitle: 'ahora',
                    position: 'top-right',
                    content: 'Debe seleccionar un proveedor para agregar items al documento.',
                    delay: 15000
                });
                return;
            }

            var switchProyecto = $('#manualProyecto').is(':checked');
            var nombreProyecto = (switchProyecto) ? $('#nombre_proyectoTxt').val() : $('#nombre_proyecto option:selected')
                .text();
            if (nombreProyecto == 'Seleccione proyecto') {
                $.toast({
                    type: 'error',
                    title: 'Error en formulario',
                    subtitle: 'ahora',
                    position: 'top-right',
                    content: 'Debe seleccionar/añadir un proyecto para generar el documento.',
                    delay: 15000
                });
                return;
            }

            if (detalles.length  == 0) {
                $.toast({
                    type: 'error',
                    title: 'Error en formulario',
                    subtitle: 'ahora',
                    position: 'top-right',
                    content: 'Debe agregar productos al documento para poder procesarlo.',
                    delay: 15000
                });
                return;
            }
            var doc = {
                proveedor: $('#razon_social').val(),
                fecha_emision: $('#fecha_emision').val(),
                tipo_pago: $('#tipo_pago').val(),
                items: detalles,
                proyecto: nombreProyecto,
                glosa: $('#glosaTxt').val(),
                _token: $('meta[name="_token"]').attr('content')
            };
            console.log(doc);
            $.post("/api/compras/ordenescompra", doc)
                .done(function(data) {
                    console.log(data);
                    location.href = '/compras/ordenescompra';
                });
        }

        function seleccionarProveedor() {
            var proveedorId = $('#razon_social').val();
            if (proveedorId != '') {
                $.get('/api/proveedores/' + proveedorId, function(data) {
                    console.log(data);
                    $('#rut').val(data.rut);
                    $('#giro').val(data.giro);
                    $('#direccion').val(data.direccion);
                    $('#comuna').val(data.comuna.nombre);

                    // Se vacia el contenido actual de la tabla buscador de productos
                    $("#productosTable tbody").empty();
                    // Se obtienen todos los productos del proveedor y se vuelve a llenar
                    $.get('/api/proveedores/productos/' + proveedorId, function(resp) {
                        productos = resp;
                        productos.forEach(function(producto, index) {
                            var row = `<tr>
                                <td>${producto.sku}</td>
                                <td>${producto.nombre}</td>
                                <td>${producto.pivot.precio.toFixed().replace(/(\d)(?=(\d{3})+(,|$))/g, '$1.')}</td>
                                <td style="display:none;"></td>
                                </tr>`;
                            $("#productosTable tbody").append(row);
                        });
                    });

                });
            }else{
                $('#rut').val('');
                    $('#giro').val('');
                    $('#direccion').val('');
                    $('#comuna').val('');
            }
        }

        function agregarGuardarDetalle() {
            var proveedorId = $('#razon_social').val();
            if (proveedorId == '') {
                $.toast({
                    type: 'error',
                    title: 'Error en formulario',
                    subtitle: 'ahora',
                    position: 'top-right',
                    content: 'Debe seleccionar un proveedor para agregar items al documento.',
                    delay: 15000
                });
                return;
            }
            var data = {
                sku: $('#skuTxt').val(),
                nombre: $('#nombreTxt').val(),
                descripcion: $('#descripcionTxt').val(),
                categoria: 1,
                unidad: $('#unidadTxt').val(),
                es_afecto: 1,
                se_vende: 1,
                se_compra: 1,
            };
            $.ajax({
                type: "POST",
                url: '/api/productos',
                data: data, // serializes the form's elements.
                success: function(data) {
                    if(data.success == true){
                        var dataJson = {
                            proveedor_id: $('#razon_social').val(),
                            producto_id: data.data.id,
                            precio: $('#precioTxt').inputmask('unmaskedvalue')
                        };
                        $('#skuTxt').val(data.data.sku);
                        console.log("Precio proveedor : " + dataJson.precio);
                        // Creamos peticion psot para agregar el precio de venta
                        $.ajax({
                            type: "POST",
                            url: '/api/productos/precioproveedor',
                            data: dataJson, // serializes the form's elements.
                            success: function(data2) {
                                if (data2.success == true) {
                                    console.log("precio guardado");
                                } else {
                                    console.log("error al guardar el precio");
                                }

                            }
                        });
                        Swal.fire({
                            title: "Producto guardado exitosamente",
                            text: "La información ingresada es correcta y fue procesada exitosamente.",
                            icon: "success"
                        })
                        .then((result) => {
                            agregarDetalle();
                            seleccionarProveedor();
                        });
                    }else{
                        Swal.fire({
                            title: "Producto no pudo ser guardado",
                            text: "La información ingresada no es correcta, verifiquela y vuelva a intentarlo.",
                            icon: "error"
                        })
                        .then((result) => {
                            agregarDetalle();
                        });
                    }

                }
            });
        }

        function agregarDetalle() {
            // Verificamos que se haya seleccionado un proveedor
            var proveedorId = $('#razon_social').val();
            if (proveedorId == '') {
                $.toast({
                    type: 'error',
                    title: 'Error en formulario',
                    subtitle: 'ahora',
                    position: 'top-right',
                    content: 'Debe seleccionar un proveedor para agregar items al documento.',
                    delay: 15000
                });
                return;
            }

            // Se recopila toda la información del producto
            var producto = {
                'sku': $('#skuTxt').val(),
                'nombre': $('#nombreTxt').val(),
                'descripcion': $('#descripcionTxt').val(),
                'cantidad': $('#cantidadTxt').val(),
                'unidad': $('#unidadTxt').val(),
                'precio': $('#precioTxt').inputmask('unmaskedvalue')
            };
            // Mapeamos las unidades y seleccionamos la información
            var unidad = $.map(unidades, (item) => {
                if (item.id == producto.unidad) {
                    return item;
                }
            })[0];
            // Se verifica si el producto existe en la tabla
            var checked = detalles.find(item => item.sku == producto.sku);
            if (checked != null) {
                var latestSubtotal = producto.precio * producto.cantidad;
                var total = parseInt(checked.cantidad) + parseInt(producto.cantidad);
                checked.cantidad = total;
                var itemIndex = detalles.indexOf(checked);
                var subtotal = total * producto.precio;
                var tdCant = $('#tablaDetalle tr[detindex="' + itemIndex + '"]').find('td:eq(2)');
                var tdSubtotal = $('#tablaDetalle tr[detindex="' + itemIndex + '"]').find('td:eq(4)');
                tdCant.text(total + ' ' + unidad.abreviacion);
                tdSubtotal.text('$ ' + subtotal.toFixed().replace(/(\d)(?=(\d{3})+(,|$))/g, '$1.'));
                console.log(detalles);
                // Se limpian los inputs
                $('#skuTxt').val('');
                $('#nombreTxt').val('');
                $('#descripcionTxt').val('');
                $('#cantidadTxt').val(1);
                $('#unidadTxt option:first').attr('selected');
                $('#precioTxt').val('0');
                calcSubtotalFila();
                calcularTotales();
            } else {
                // Se calcula el subtotal del producto
                var subtotal = producto.precio * producto.cantidad;
                subtotalDoc += subtotal;
                // Verificamos que el producto contenga información valida
                if (producto.nombre == '' || subtotal == 0) {
                    return;
                }
                // Se crea una fila con toda la información necesaria
                var row = `
                <tr detIndex="${index}">
                    <td>${producto.sku}</td>
                    <td>${producto.nombre}</td>
                    <td>${producto.cantidad} ${unidad.abreviacion}</td>
                    <td>${'$ ' + producto.precio.replace(/(\d)(?=(\d{3})+(,|$))/g, '$1.')}</td>
                    <td>${'$ ' + subtotal.toFixed().replace(/(\d)(?=(\d{3})+(,|$))/g, '$1.')}</td>
                    <td>
                        <button type="button" onclick="eliminarDetalle(${index})" class="btn btn-sm btn-outline-danger" style="padding:.25em .25em;">
                        <span class="mdi mdi-delete"></span></button>
                    </td>
                </tr>`;
                // Se limpian los inputs
                $('#skuTxt').val('');
                $('#nombreTxt').val('');
                $('#descripcionTxt').val('');
                $('#cantidadTxt').val(1);
                $('#unidadTxt option:first').attr('selected');
                $('#precioTxt').val('0');

                // Se inserta antes del rowDetalle que es nuestro formulario estatico
                $(row).insertBefore($('#rowDetalle'));
                // Se inserta el producto en nuestra lista
                detalles.push(producto);
                // Se calculan los totales y se aumenta el indice
                calcSubtotalFila();
                calcularTotales();
                index++;
            }
        }

        function eliminarDetalle(index) {
            var subtotal = detalles[index].precio * detalles[index].cantidad;
            subtotalDoc -= subtotal;
            $('#tablaDetalle tr[detindex="' + index + '"]').remove();
            calcularTotales();
        }

        function calcularTotales() {
            subtotalDoc = 0;
            detalles.forEach(element => {
                subtotalDoc += element.precio * element.cantidad;
            });
            var iva = subtotalDoc * 0.19;
            var total = subtotalDoc + iva;
            $('#lblSubtotalDoc').text('$ ' + subtotalDoc.toFixed().replace(/(\d)(?=(\d{3})+(,|$))/g, '$1.'));
            $('#lblneto').text('$ ' + subtotalDoc.toFixed().replace(/(\d)(?=(\d{3})+(,|$))/g, '$1.'));
            $('#lbliva').text('$ ' + iva.toFixed().replace(/(\d)(?=(\d{3})+(,|$))/g, '$1.'));
            $('#lbltotal').text('$ ' + total.toFixed().replace(/(\d)(?=(\d{3})+(,|$))/g, '$1.'));
        }

        function calcSubtotalFila() {
            var cantidad = $('#cantidadTxt').val();
            var precio = $('#precioTxt').inputmask('unmaskedvalue');

            var subtotal = cantidad * precio;

            $('#lblSubtotal').text('$ ' + subtotal.toFixed().replace(/(\d)(?=(\d{3})+(,|$))/g, '$1.'));
        }

        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });
    </script>
    <script>
        (function(a) {
            function f(c) {
                if (!a("#toast-container").length) {
                    var d = ["top-right", "top-left", "bottom-right", "bottom-left"].includes(a.toastDefaults
                        .position) ? a.toastDefaults.position : "top-right";
                    a("body").prepend(
                        '<div id="toast-container" class="toast-container" aria-live="polite" aria-atomic="true"></div>'
                    );
                    a("#toast-container").addClass(d)
                }
                d = a("#toast-container");
                var b = "",
                    e = b = "",
                    g = "toast-" + l,
                    f = c.type,
                    t = c.title,
                    m = c.subtitle,
                    n = c.content,
                    h = c.img,
                    p = c.delay ? 'data-delay="' + c.delay + '"' : 'data-autohide="false"',
                    q = "",
                    r = a.toastDefaults.dismissible,
                    u = a.toastDefaults.style.toast,
                    k = !1;
                "undefined" !== typeof c.dismissible && (r = c.dismissible);
                switch (f) {
                    case "info":
                        e = a.toastDefaults.style.info || "bg-info";
                        b = a.toastDefaults.style.info || "text-white";
                        break;
                    case "success":
                        e = a.toastDefaults.style.success || "bg-success";
                        b = a.toastDefaults.style.info || "text-white";
                        break;
                    case "warning":
                        e = a.toastDefaults.style.warning || "bg-warning";
                        b = a.toastDefaults.style.warning || "text-white";
                        break;
                    case "error":
                        e = a.toastDefaults.style.error ||
                            "bg-danger", b = a.toastDefaults.style.error || "text-white"
                }
                a.toastDefaults.pauseDelayOnHover && c.delay && (p = 'data-autohide="false"', q = 'data-hide-after="' +
                    (Math.floor(Date.now() / 1E3) + c.delay / 1E3) + '"');
                b = '<div id="' + g + '" class="toast ' + u +
                    '" role="alert" aria-live="assertive" aria-atomic="true" ' + p + " " + q +
                    '><div class="toast-header ' + (e + " " + b + '">');
                h && (b += '<img src="' + h.src + '" class="mr-2 ' + (h["class"] || "") + '" alt="' + (h.alt ||
                    "Image") + '">');
                b += '<strong class="mr-auto">' + t + "</strong>";
                m && (b += '<div class="toast-btnclose"><small class="text-white">' +
                    m + "</small>");
                r && (b +=
                    '<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button></div>'
                );
                b += "</div>";
                n && (b += '<div class="toast-body">\n                        ' + n + "\n                    </div>");
                b += "</div>";
                a.toastDefaults.stackable || d.find(".toast").each(function() {
                    a(this).remove()
                });
                d.append(b);
                d.find(".toast:last").toast("show");
                a.toastDefaults.pauseDelayOnHover &&
                    (setTimeout(function() {
                        k || a("#" + g).toast("hide")
                    }, c.delay), a("body").on("mouseover", "#" + g, function() {
                        k = !0
                    }), a(document).on("mouseleave", "#" + g, function() {
                        var b = Math.floor(Date.now() / 1E3),
                            c = parseInt(a(this).data("hideAfter"));
                        k = !1;
                        b >= c && a(this).toast("hide")
                    }));
                l++
            }
            a.toastDefaults = {
                position: "top-right",
                dismissible: !0,
                stackable: !0,
                pauseDelayOnHover: !0,
                style: {
                    toast: "",
                    info: "",
                    success: "",
                    warning: "",
                    error: ""
                }
            };
            a("body").on("hidden.bs.toast", ".toast", function() {
                a(this).remove()
            });
            var l = 1;
            a.snack = function(a,
                d, b) {
                return f({
                    type: a,
                    title: d,
                    delay: b
                })
            };
            a.toast = function(a) {
                return f(a)
            }
        })(jQuery);
        const TYPES = ['info', 'warning', 'success', 'error'],
            POSITION = ['top-right', 'top-left', 'bottom-right', 'bottom-left'];

        $.toastDefaults.position = POSITION[0];
        $.toastDefaults.dismissible = true;
        $.toastDefaults.stackable = true;
        $.toastDefaults.pauseDelayOnHover = true;
    </script>
@endpush
