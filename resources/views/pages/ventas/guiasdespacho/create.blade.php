@extends('layout.master')

@section('title', 'Emisión de Guia de Despacho Electrónica')

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
            <h4 class="mb-3 mb-md-0">Emisión de Guia de Despacho Electrónica</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h4 class="card-title mb-0">FORMULARIO DE NUEVA GUIA DE DESPACHO ELECTRÓNICA</h4>
                            </div>
                            <div class="row mx-3">
                                <div style="width:100%; margin-top:24px;"></div>
                                <div class="col-md-12">
                                    <form class="form" id="storeForm" method="post"
                                        onsubmit="confirmarGuiaDespacho(event)">
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
                                                            <h5>Información del cliente</h5>
                                                        </div>
                                                        <div class="row mx-1">
                                                            <div class="row mb-2">
                                                                <label
                                                                    class="col-sm-4 col-form-label col-form-label-sm">Razón
                                                                    Social</label>
                                                                <div class="col-sm-8">
                                                                    <select name="razon_social" id="razon_social"
                                                                        class="form-control form-control-sm"
                                                                        onchange="seleccionarCliente()">
                                                                        <option value="">Seleccione un cliente
                                                                        </option>
                                                                        @foreach ($clientes as $cliente)
                                                                            <option value="{{ $cliente->id }}">
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
                                                                        value="Guia de Despacho Electrónica (52)"
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
                                                                    Despacho</label>
                                                                <div class="col-sm-8">
                                                                    <select name="tipo_despacho" id="tipo_despacho"
                                                                        class="form-control form-control-sm">
                                                                        <option value="1">Comprador</option>
                                                                        <option value="2">Emisor al Comprador</option>
                                                                        <option value="3">Emisor a Otro</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <label
                                                                    class="col-sm-4 col-form-label col-form-label-sm">Tipo
                                                                    Traslado</label>
                                                                <div class="col-sm-8">
                                                                    <select name="ind_traslado" id="ind_traslado"
                                                                        class="form-control form-control-sm">
                                                                        <option value="1">Operación Constituye Venta</option>
                                                                        <option value="2">Venta Por efectuar</option>
                                                                        <option value="3">Consigación</option>
                                                                        <option value="4">Donación</option>
                                                                        <option value="5">Traslado Interno</option>
                                                                        <option value="6">No Constituye Venta</option>
                                                                        <option value="7">Devolución</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <label
                                                                    class="col-sm-4 col-form-label col-form-label-sm">Comuna de Destino</label>
                                                                <div class="col-sm-8">
                                                                    <select name="comuna_destino" id="comuna_destino"
                                                                        class="form-control form-control-sm">
                                                                        <option value="">Seleccione Comuna</option>
                                                                        @foreach($comunas as $comuna)
                                                                            <option value="{{ $comuna->id }}">{{ $comuna->nombre }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-2 border-bottom">
                                                            <h5>Información Comercial</h5>
                                                        </div>
                                                        <div class="row mx-1">
                                                            <div class="row mb-2">
                                                                <label
                                                                    class="col-sm-4 col-form-label col-form-label-sm">Lista
                                                                    de Precios</label>
                                                                <div class="col-sm-8" id="inputProyecto">
                                                                    <select class="form-control form-control-sm"
                                                                        id="lista_precio" name="lista_precio"
                                                                        onchange="seleccionarLista()">
                                                                        <option value="">Seleccione una lista
                                                                        </option>
                                                                        @foreach ($listas as $lista)
                                                                            <option value="{{ $lista->id }}">
                                                                                {{ $lista->nombre }}</option>
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
                                                                <td style="width:12%;padding:4px;">
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
                                                                <td style="width:18%;padding:4px;">
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
                                                                <td style="width:15%;padding:4px;"><span
                                                                        style="vertical-align: bottom; text-align:right;"
                                                                        id="lblSubtotal">$ 0</span></td>
                                                                <td style="width:10%;padding:4px;">
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
                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                <div class="mb-2 border-bottom">
                                                    <h5>Referencias del documento</h5>
                                                </div>
                                                <div class="col-md-12">
                                                    <table class="table table-sm mb-3">
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <select class="form-control form-control-sm">
                                                                        <option>Tipo de Documento</option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text" placeholder="Folio" />
                                                                </td>
                                                                <td>
                                                                    <input type="date"
                                                                        class="form-control form-control-sm" />
                                                                </td>
                                                                <td>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text" placeholder="Razón" />
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <select class="form-control form-control-sm">
                                                                        <option>Seleccione tipo documento</option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text" placeholder="Folio" />
                                                                </td>
                                                                <td>
                                                                    <input type="date"
                                                                        class="form-control form-control-sm" />
                                                                </td>
                                                                <td>
                                                                    <input class="form-control form-control-sm"
                                                                        type="text" placeholder="Razón" />
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="mb-2 border-bottom">
                                                    <h5>Glosa documento</h5>
                                                </div>
                                                <div class="col-md-12">
                                                    <textarea id="glosaTxt" maxlength="250" class="form-control mt-3" rows="3"></textarea>
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
                                                        <div class="col-md-6 pr-0">
                                                            <p>Descuento global </p>
                                                        </div>
                                                        <div class="col-md-3 pl-0">
                                                            <input value="0" class="form-control form-control-sm"
                                                                type="text" name="descuentoglobal"
                                                                id="descuentoglobal" />

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
    <div class="modal fade" id="loadingModal" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <img class="text-center mx-auto" src="/loading.gif" style="width: 48px; height: 48px;">
                <span id="statusTxt" class="text-center fw-bold">Enviando información al SII...</span>
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
            $("#descuentoglobal").inputmask('percentage', {});
            $('#razon_social').select2();
            $('#comuna_destino').select2();
            $('#productosTable').on('click', 'tbody tr', function(event) {
                $(this).addClass('highlight').siblings().removeClass('highlight');
            });

            $('#productosTable').on('dblclick', 'tbody tr', function(event) {
                selectDetalle();
            });
        });

        function selectDetalle() {
            var item = $('#productosTable tbody tr.highlight');
            var sku = $(item).find('td:eq(0)').text();
            var nombre = $(item).find('td:eq(1)').text();
            var descripcion = $(item).find('td:eq(3)').text();
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

        function confirmarGuiaDespacho(e) {
            e.preventDefault();

            Swal.fire({
                title: "¿Quieres confirmar esta Guia de Despacho?",
                text: "Una vez confirmada la Guia de Despacho no se podran hacer cambios sobre ella, recomendamos revisar el detalle del documento.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Confirmar",
                cancelButtonText: "Cancelar"
            }).then((modalResult) => {
                if (modalResult.isConfirmed) {
                    procesarGuiaDespacho(e);
                }
            });
        }

        function procesarGuiaDespacho(e) {
            $('#loadingModal').modal('show');
            $('#statusTxt').text('Validando información del formulario...');
            // Verificamos que se haya seleccionado un proveedor
            var clienteId = $('#razon_social').val();
            if (clienteId == '') {
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
                cliente: $('#razon_social').val(),
                fecha_emision: $('#fecha_emision').val(),
                ind_traslado: $('#ind_traslado').find(":selected").val(),
                tipo_despacho: $('#tipo_despacho').find(":selected").val(),
                comuna_destino: $('#comuna_destino').val(),
                items: detalles,
                glosa: $('#glosaTxt').val(),
                _token: $('meta[name="_token"]').attr('content')
            };
            $('#statusTxt').text('Enviando información del documento...');
            $.post("/api/ventas/guiasdespacho", doc)
                .done(function(data) {
                    $('#statusTxt').text('Recibiendo información de respuesta...');
                    console.log(data);
                    $('#loadingModal').modal('hide');
                    location.href = '/ventas/guiasdespacho';
                });
        }

        function seleccionarCliente() {
            var clienteId = $('#razon_social').val();
            if (clienteId != '') {
                $.get('/api/clientes/' + clienteId, function(data) {
                    console.log(data);
                    $('#rut').val(data.rut);
                    $('#giro').val(data.giro);
                    $('#direccion').val(data.direccion);
                    $('#comuna').val(data.comuna.nombre);

                });
            } else {
                $('#rut').val('');
                $('#giro').val('');
                $('#direccion').val('');
                $('#comuna').val('');
            }
        }

        function seleccionarLista() {
            var listaId = $('#lista_precio').val();
            // Se vacia el contenido actual de la tabla buscador de productos
            $("#productosTable tbody").empty();
            // Se obtienen todos los productos del proveedor y se vuelve a llenar
            $.get('/api/listaprecios/' + listaId, function(resp) {
                productos = resp.productos;
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
        }

        function agregarGuardarDetalle() {
            var clienteId = $('#razon_social').val();
            if (clienteId == '') {
                $.toast({
                    type: 'error',
                    title: 'Error en formulario',
                    subtitle: 'ahora',
                    position: 'top-right',
                    content: 'Debe seleccionar un cliente para agregar items al documento.',
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
                    if (data.success == true) {
                        var dataJson = {
                            lista_precio_id: $('#lista_precio').val(),
                            producto_id: data.data.id,
                            precio: $('#precioTxt').inputmask('unmaskedvalue')
                        };
                        $('#skuTxt').val(data.data.sku);
                        console.log("Precio proveedor : " + dataJson.precio);
                        // Creamos peticion psot para agregar el precio de venta
                        $.ajax({
                            type: "POST",
                            url: '/api/productos/listaprecio',
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
                    } else {
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
            var clienteId = $('#razon_social').val();
            if (clienteId == '') {
                $.toast({
                    type: 'error',
                    title: 'Error en formulario',
                    subtitle: 'ahora',
                    position: 'top-right',
                    content: 'Debe seleccionar un cliente para agregar items al documento.',
                    delay: 15000
                });
                return;
            }

            // Se verifica si se escribio un SKU, de no haber uno, se rellena con el timestamp
            if($('#skuTxt').val() == ''){
                $('#skuTxt').val('AG'+$.now());
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
            detalles.splice(index, 1);
            $('#tablaDetalle tr[detindex="' + index + '"]').remove();
            // Se calculan los totales y se aumenta el indice
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
