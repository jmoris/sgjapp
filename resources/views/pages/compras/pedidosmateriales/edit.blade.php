@extends('layout.master')

@section('title', 'Edición de Pedido de Materiales')

@push('style')
    <style>
        tr.noBorder td {
            border: 0;
        }

        th,
        td {
            padding: 0px 0px;
        }

        body tr {
            -webkit-user-select: initial !important;
            -moz-user-select: initial !important;
            -ms-user-select: initial !important;
            -o-user-select: initial !important;
            user-select: initial !important;
        }
    </style>
@endpush

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Edición de Pedido de Materiales <small
                style="font-size:.75em;color: grey;">#{{ str_pad($pedido->folio, 5, '0', STR_PAD_LEFT) }}</small></h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h4 class="card-title mb-0">FORMULARIO DE EDICIÓN DE PEDIDO DE MATERIALES</h4>
                            </div>
                            <div class="row mx-3">
                                <div style="width:100%; margin-top:24px;"></div>
                                <div class="col-md-12">
                                    <form class="form" id="storeForm" method="post" onsubmit="procesarPedido(event)">
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
                                                            <h5>Información del mandante</h5>
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
                                                                        <option value="">Seleccione un cliente
                                                                        </option>
                                                                        @foreach ($clientes as $cliente)
                                                                            <option
                                                                                @if ($cliente->id == $pedido->cliente_id) selected @endif
                                                                                value="{{ $cliente->id }}">
                                                                                {{ $cliente->razon_social }}</option>
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
                                                                        value="Pedido de Material"
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
                                                                    class="col-sm-4 col-form-label col-form-label-sm">Materia</label>
                                                                <div class="col-sm-8">
                                                                    <input type="text" name="materia" id="materia"
                                                                        value="{{ $pedido->materia }}"
                                                                        class="form-control form-control-sm" required>
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
                                                                            <option
                                                                                @if ($proyecto->id == $pedido->proyecto_id) selected @endif
                                                                                value="{{ $proyecto->id }}">
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

                                                </div>
                                                <div class="row mx-1">
                                                    <table id="tablaDetalle" class="table mb-4">
                                                        <thead>
                                                            <!--<th>SKU</th>-->
                                                            <th>Item</th>
                                                            <th>Largo</th>
                                                            <th>Stock</th>
                                                            <th>Cantidad</th>
                                                            <th>Recibidos</th>
                                                            <th>Total Kgs</th>
                                                            <th class="text-end"><i class="mdi mdi-dots-vertical"></i>
                                                            </th>
                                                        </thead>
                                                        <tbody>
                                                            <tr class="noBorder" id="rowDetalle">
                                                                <!--<td style="width:12%;padding:4px;">
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
                                                                    </td>-->
                                                                <td style="width:35%;padding:4px;">
                                                                    <!--<input id="nombreTxt" type="text"
                                                                            class="form-control form-control-sm"
                                                                            placeholder="NOMBRE ITEM" />-->
                                                                    <select class="form-control form-control-sm"
                                                                        id="selectProductos"></select>
                                                                </td>
                                                                <td style="width:10%;padding:4px;">
                                                                    <input id="largoTxt" type="text"
                                                                        class="form-control form-control-sm"
                                                                        value="1" placeholder="LARGO"
                                                                        onchange="calcTotalKg()"/>
                                                                </td>
                                                                <td style="width:10%;padding:4px;">
                                                                    <input id="stockTxt" type="text"
                                                                        class="form-control form-control-sm"
                                                                        value="0" placeholder="STOCK" />
                                                                </td>
                                                                <td style="width:10%;padding:4px;">
                                                                    <input id="cantidadTxt" type="text"
                                                                        class="form-control form-control-sm"
                                                                        value="1" placeholder="CANTIDAD"
                                                                        onchange="calcTotalKg()" />
                                                                </td>
                                                                <td style="width:10%;padding:4px;">
                                                                    <input id="recibidoTxt" type="text"
                                                                        class="form-control form-control-sm"
                                                                        value="0" placeholder="RECIBIDOS" />
                                                                </td>
                                                                <td style="width:10%;padding:4px;text-align:center;">
                                                                    <span id="totalTxt"></span>
                                                                </td>
                                                                <td style="width:10%;padding:4px;">
                                                                    <button type="button" onclick="agregarDetalle()"
                                                                        title="Agregar detalle a la lista"
                                                                        class="btn btn-sm btn-outline-primary"
                                                                        style="padding:.25em .5em; float:right;">
                                                                        <span class="mdi mdi-plus"></span></button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                <div class="mb-2 border-bottom">
                                                    <h5>Glosa documento</h5>
                                                </div>
                                                <div class="col-md-12">
                                                    <textarea id="glosaTxt" maxlength="250" class="form-control mt-3" rows="5">{{ $pedido->glosa }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="mb-2 border-bottom">
                                                    <h5>Resumen de pedido</h5>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-7">
                                                            <p><b>Total de Kg </b></p>
                                                        </div>
                                                        <div class="col-md-5 text-end">
                                                            <p id="lbltotal">0 Kg</p>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <p><b>Total de Kg Recibidos </b></p>
                                                        </div>
                                                        <div class="col-md-5 text-end">
                                                            <p id="lblrecibido">0 Kg</p>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <p><b>Total de Kg Faltantes </b></p>
                                                        </div>
                                                        <div class="col-md-5 text-end">
                                                            <p id="lblfaltante">0 Kg</p>
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
        var editIndex = null;
        var editHtml = null;
        var index = 0;
        var detalles = {!! json_encode(
            $pedido->lineas()->select('sku', 'nombre', 'descripcion', 'cantidad', 'stock', 'recibido', 'unidad', 'largo', 'peso')->get(),
        ) !!};
        var subtotalDoc = 0;
        var unidades = {!! json_encode($unidades) !!};
        var selectedProducto = null;
        var productos = [];
        var dataProductos = [];
        // Aqui se inicializan las librerias
        $(document).ready(function() {
            $("#precioTxt").inputmask('numeric', {
                prefix: '$ ',
                radixPoint: ',',
                groupSeparator: '.',
                rightAlign: false
            });
            $("#descuentoglobal").inputmask('percentage', {});
            $('#razon_social').select2();
            $('#productosTable').on('click', 'tbody tr', function(event) {
                $(this).addClass('highlight').siblings().removeClass('highlight');
            });

            $('#productosTable').on('dblclick', 'tbody tr', function(event) {
                selectDetalle();
            });

            $('#selectProductos').change(function() {
                var id = $(this).val();
                $.get('/api/productos/' + id, function(data) {
                    selectedProducto = data;
                    $('#totalTxt').text(parseFloat(data.largo * data.peso).toFixed().replace(
                        /(\d)(?=(\d{3})+(,|$))/g, '$1.') + ' Kg');
                    $('#cantidadTxt').select();
                    $('#cantidadTxt').focus();
                });
            });

            $('#selectProductos').select2({
                placeholder: 'Sin productos en la lista',
                ajax: {
                    url: '/api/productos/lista/materiaprima',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(response) {
                        console.log(response);
                        var data = $.map(response, function(obj) {
                            obj.text = obj.text || obj
                            .nombre; // replace name with the property used for the text
                            return obj;
                        });
                        return {
                            results: data
                        };
                    },
                },

            });
            renderDetalles();
            seleccionarProveedor();
        });

        function renderDetalles() {
            for (var i = 0; i < detalles.length; i++) {
                unidad = $.map(unidades, (item) => {
                    if (item.abreviacion == detalles[i].unidad) {
                        return item;
                    }
                })[0];
                detalles[i].unidad = unidad.id;
            }


            detalles.forEach(function(producto, index) {
                // Mapeamos las unidades y seleccionamos la información
                var unidad = $.map(unidades, (item) => {
                    if (item.id == producto.unidad) {
                        return item;
                    }
                })[0];
                console.log(producto);
                //producto[index].unidad = unidad[1];
                // Se calcula el subtotal del producto
                var subtotal = producto.precio * producto.cantidad;
                subtotalDoc += subtotal;

                var row = `
                <tr detIndex="${index}">
                    <td>${producto.nombre} ${unidad.nombre}</td>
                    <td>${producto.largo}</td>
                    <td>${producto.stock}</td>
                    <td>${producto.cantidad}</td>
                    <td>${producto.recibido}</td>
                    <td><b>${parseFloat((parseInt(producto.stock)+parseInt(producto.recibido)) * producto.largo * producto.peso).toFixed(2)+'</b>/'+parseFloat(producto.cantidad * producto.largo * producto.peso).toFixed(2) + ' Kgs'}</td>
                    <td>
                        <button type="button" onclick="editarProducto(${index}, 0)" class="btn btn-sm btn-outline-danger" style="padding:.25em .5em; float:left;">
                            <span class="mdi mdi-pencil"></span>
                        </button>
                        <button type="button" onclick="eliminarDetalle(${index})" class="ms-1 btn btn-sm btn-outline-danger" style="padding:.25em .5em; float:left;">
                            <span class="mdi mdi-delete"></span>
                        </button>
                    </td>
                </tr>`;
                // Se inserta antes del rowDetalle que es nuestro formulario estatico
                $(row).insertBefore($('#rowDetalle'));
                index++;
            });
            index++;
            // Se calculan los totales y se aumenta el indice
            calcTotalKg();
            calcularTotales();
        }

        function selectDetalle() {
            var item = $('#productosTable tbody tr.highlight');

            var index = $(item).attr('itemindex');

            var sku = $(item).find('td:eq(0)').text();
            var nombre = $(item).find('td:eq(1)').text();
            var unidad = $(item).find('td:eq(3)').text();
            var precio = parseInt($(item).find('td:eq(2)').text().replace(/[^0-9]/gi, ''));
            if (sku == '' && nombre == '' && precio == '') {
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

            selectedProducto = dataProductos[index];

            $('#nombreTxt').val(nombre);
            $('#totalTxt').text(selectedProducto.peso + ' Kgs');
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

        function procesarPedido(e) {
            e.preventDefault();

            // Verificamos que se haya seleccionado un proveedor
            var proveedorId = $('#razon_social').val();
            if (proveedorId == '') {
                $.toast({
                    type: 'error',
                    title: 'Error en formulario',
                    subtitle: 'ahora',
                    position: 'top-right',
                    content: 'Debe seleccionar un mandante para agregar items al documento.',
                    delay: 15000
                });
                return;
            }

            var nombreProyecto = $('#nombre_proyecto option:selected').text();
            var idProyecto = $('#nombre_proyecto option:selected').val();

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

            if (detalles.length == 0) {
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
                mandante: $('#razon_social').val(),
                fecha_emision: $('#fecha_emision').val(),
                materia: $('#materia').val(),
                items: detalles,
                proyecto: idProyecto,
                glosa: $('#glosaTxt').val(),
                _token: $('meta[name="_token"]').attr('content')
            };
            console.log(doc);
            $.post("/api/compras/pedidosmateriales/editar/{{$pedido->id}}", doc)
                .done(function(data) {
                    console.log(data);
                    location.href = '/compras/pedidosmateriales';
            });
        }

        function seleccionarProveedor() {
            var proveedorId = $('#razon_social').val();
            if (proveedorId != '') {
                $.get('/api/clientes/' + proveedorId, function(data) {
                    console.log(data);
                    $('#rut').val(data.rut);
                    $('#giro').val(data.giro);
                    $('#direccion').val(data.direccion);
                    $('#comuna').val(data.comuna.nombre);

                    // Se vacia el contenido actual de la tabla buscador de productos
                    $("#productosTable tbody").empty();
                    // Se obtienen todos los productos del proveedor y se vuelve a llenar
                    $.get('/api/listaprecios/1', function(resp) {
                        productos = resp.productos;
                        dataProductos = productos;
                        console.log(resp);
                        productos.forEach(function(producto, index) {
                            var row = `<tr itemindex="${index}">
                                <td>${producto.sku}</td>
                                <td>${producto.nombre}</td>
                                <td>${producto.pivot.precio.toFixed().replace(/(\d)(?=(\d{3})+(,|$))/g, '$1.')}</td>
                                <td style="display:none;">${producto.unidad_id}</td>
                                </tr>`;
                            $("#productosTable tbody").append(row);
                        });
                    });

                });
            } else {
                $('#rut').val('');
                $('#giro').val('');
                $('#direccion').val('');
                $('#comuna').val('');
            }
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
                    content: 'Debe seleccionar un mandante para agregar items al documento.',
                    delay: 15000
                });
                return;
            }


            // Se recopila toda la información del producto
            var producto = {
                'sku': selectedProducto.sku,
                'nombre': selectedProducto.nombre,
                'unidad': selectedProducto.unidad_id,
                'largo': selectedProducto.largo,
                'peso': selectedProducto.peso,
                'stock': $('#stockTxt').val(),
                'cantidad': $('#cantidadTxt').val(),
                'recibido': $('#recibidoTxt').val(),
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



                var itemIndex = detalles.indexOf(checked);
                var tdStock = $('#tablaDetalle tr[detindex="' + itemIndex + '"]').find('td:eq(1)');
                var tdCant = $('#tablaDetalle tr[detindex="' + itemIndex + '"]').find('td:eq(2)');
                var tdRec = $('#tablaDetalle tr[detindex="' + itemIndex + '"]').find('td:eq(3)');
                var tdTotal = $('#tablaDetalle tr[detindex="' + itemIndex + '"]').find('td:eq(4)');

                var stock = parseInt(checked.stock) + parseInt(producto.stock);
                var cantidad = parseInt(checked.cantidad) + parseInt(producto.cantidad);
                var recibido = parseInt(checked.recibido) + parseInt(producto.recibido);
                // Se verifica la actual cantidad, recibidos y stock
                checked.stock = stock;
                checked.cantidad = cantidad;
                checked.recibido = recibido;

                tdStock.text(stock);
                tdCant.text(cantidad);
                tdRec.text(recibido);

                var totalKg = checked.cantidad * checked.largo * checked.peso

                var recibidoKg = (checked.recibido+checked.stock) * checked.largo * checked.peso
                tdTotal.html('<b>' + recibidoKg.toFixed() + '</b>/' + totalKg.toFixed() + ' Kgs');

                // Se limpian los inputs
                $('#nombreTxt').val('');
                $('#stockTxt').val(0);
                $('#cantidadTxt').val(1);
                $('#unidadTxt option:first').attr('selected');
                $('#recibidoTxt').val(0);
                $('#selectProductos').val(null).trigger('change');
                $('#totalTxt').text('0 Kg');
                calcularTotales();
            } else {
                // Verificamos que el producto contenga información valida
                if (producto.nombre == '') {
                    return;
                }
                var unidad = $.map(unidades, (item) => {
                    if (item.id == producto.unidad) {
                        return item;
                    }
                })[0];
                // Se crea una fila con toda la información necesaria
                var row = `
                <tr detIndex="${index}">
                    <td>${producto.nombre} ${unidad.nombre}</td>
                    <td>${producto.largo}</td>
                    <td>${producto.stock}</td>
                    <td>${producto.cantidad}</td>
                    <td>${producto.recibido}</td>
                    <td><b>${parseFloat((parseInt(producto.stock)+parseInt(producto.recibido)) * producto.largo * producto.peso).toFixed(2)+'</b>/'+parseFloat(producto.cantidad * producto.largo * producto.peso).toFixed(2) + ' Kgs'}</td>
                    <td>
                        <button type="button" onclick="eliminarDetalle(${index})" class="btn btn-sm btn-outline-danger" style="padding:.25em .25em;">
                        <span class="mdi mdi-delete"></span></button>
                    </td>
                </tr>`;
                // Se limpian los inputs
                $('#nombreTxt').val('');
                $('#stockTxt').val(0);
                $('#cantidadTxt').val(1);
                $('#unidadTxt option:first').attr('selected');
                $('#recibidoTxt').val(0);
                $('#totalTxt').text('0 Kg');
                $('#selectProductos').val(null).trigger('change');
                // Se inserta antes del rowDetalle que es nuestro formulario estatico
                $(row).insertBefore($('#rowDetalle'));
                // Se inserta el producto en nuestra lista
                detalles.push(producto);
                // Se calculan los totales y se aumenta el indice
                calcularTotales();
                index++;
            }
            selectedProducto = null;
            calcTotalKg();
        }

        function editarProducto(index, action) {
            if (editIndex != null && !action) {
                alert("Ya esta modificando un producto");
                return;
            }
            if (editIndex != null && action) {
                var row = $('#tablaDetalle tr[detindex="' + index + '"]');
                var item = detalles[index];
                var largo = $('#largoEditTxt').val();
                var cantidad = $('#cantidadEditTxt').val();
                var stock = $('#stockEditTxt').val();
                var recibido = $('#recibidoEditTxt').val();

                if((parseFloat(stock)+parseFloat(recibido)) > cantidad){
                    alert("La cantidad de productos recibidos supera la cantidad necesitada");
                    return;
                }

                detalles[index].largo = largo;
                detalles[index].stock = stock;
                detalles[index].cantidad = cantidad;
                detalles[index].recibido = recibido;

                calcularTotales();

                row.find('td:eq(1)').html(`${largo}`);
                row.find('td:eq(2)').html(`${stock}`);
                row.find('td:eq(3)').html(`${cantidad}`);
                row.find('td:eq(4)').html(`${recibido}`);
                row.find('td:eq(5)').html(
                `<b>${parseFloat((parseInt(item.stock)+parseInt(item.recibido)) * item.largo * item.peso).toFixed(2)+'</b>/'+parseFloat(item.cantidad * item.largo * item.peso).toFixed(2) + ' Kgs'}`
                );
                row.find('td:eq(6)').html(`
                        <button type="button" onclick="editarProducto(${index}, 1)" class="btn btn-sm btn-outline-danger" style="padding:.25em .5em;">
                            <span class="mdi mdi-pencil"></span>
                        </button>
                        <button type="button" onclick="eliminarDetalle(${index})" class="btn btn-sm btn-outline-danger" style="padding:.25em .5em;">
                            <span class="mdi mdi-delete"></span>
                        </button>
                `);
                editIndex = null;
                console.log(detalles);
                return;
            }
            editIndex = index;
            var row = $('#tablaDetalle tr[detindex="' + index + '"]');
            var item = detalles[index];

            row.find('td:eq(1)').html(`<input id="largoEditTxt" type="text"
                                                                        class="form-control form-control-sm"
                                                                        value="${item.largo}"
                                                                        placeholder="LARGO" />`);

            row.find('td:eq(2)').html(`<input id="stockEditTxt" type="text"
                                                                        class="form-control form-control-sm"
                                                                        value="${item.stock}"
                                                                        placeholder="STOCK" />`);
            row.find('td:eq(3)').html(`<input id="cantidadEditTxt"
                                                type="number"
                                                min="1" value="${item.cantidad}"
                                                class="form-control form-control-sm"
                                                placeholder="CANT" />`);
            row.find('td:eq(4)').html(`<input
                                                                        id="recibidoEditTxt" type="text"
                                                                        class="form-control form-control-sm"
                                                                        value="${item.recibido}"
                                                                        placeholder="RECIBIDOS" />`);
            row.find('td:eq(6)').html(` <button type="button" onclick="editarProducto(${index}, 1)" class="btn btn-sm btn-outline-primary" style="padding:.25em .5em;">
                                            <span class="mdi mdi-content-save-plus"></span>
                                        </button>
                                        <button type="button" onclick="eliminarDetalle(${index})" class="btn btn-sm btn-outline-danger" style="padding:.25em .5em;">
                                            <span class="mdi mdi-delete"></span>
                                        </button>`);
        }

        function eliminarDetalle(index) {
            detalles.splice(index, 1);
            $('#tablaDetalle tr[detindex="' + index + '"]').remove();
            // Se calculan los totales y se aumenta el indice
            calcularTotales();
        }

        function calcularTotales() {
            subtotalKg = 0;
            subtotalRecibido = 0;
            subtotalFaltante = 0;
            detalles.forEach(element => {
                subtotalKg += element.largo * element.cantidad * element.peso;
                subtotalRecibido += element.largo * (parseInt(element.recibido) + parseInt(element.stock)) * element
                    .peso;
            });
            subtotalFaltante = subtotalKg - subtotalRecibido;
            $('#lbltotal').text(subtotalKg.toFixed(2).replace(/(\d)(?=(\d{3})+(,|$))/g, '$1.') + ' Kg');
            $('#lblrecibido').text(subtotalRecibido.toFixed(2).replace(/(\d)(?=(\d{3})+(,|$))/g, '$1.') + ' Kg');
            $('#lblfaltante').text(subtotalFaltante.toFixed(2).replace(/(\d)(?=(\d{3})+(,|$))/g, '$1.') + ' Kg');

        }

        function calcTotalKg() {
            var cantidad = $('#cantidadTxt').val();
            console.log(cantidad);
            console.log(selectedProducto);
            if (selectedProducto == null) {
                console.log('cambio');
                $('#totalTxt').text('0 Kg');
            } else {
                console.log('no cambia');
                var totalKg = parseFloat(cantidad * selectedProducto.peso * selectedProducto.largo);
                if (totalKg == NaN || totalKg == null || totalKg == undefined) {
                    totalKg = 0;
                }
                $('#totalTxt').text(totalKg.toFixed(2).replace(/(\d)(?=(\d{3})+(,|$))/g, '$1.') + ' Kgs');
            }
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
