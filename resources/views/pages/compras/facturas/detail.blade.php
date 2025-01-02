@extends('layout.master')

@section('title', 'Visor de Facturas de Compra')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Factura de Compra #{{ $documento['Encabezado']['IdDoc']['Folio'] }}</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h4 class="card-title mb-0">VISOR DE FACTURAS DE COMPRA ELECTRONICA</h4>
                            </div>
                            <div class="row mx-3">
                                <div style="width:100%; margin-top:24px;"></div>
                                <div class="col-md-12">
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
                                                                        value="{{ $documento['Encabezado']['Emisor']['RznSoc'] }}" disabled>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <label
                                                                    class="col-sm-4 col-form-label col-form-label-sm">R.U.T.</label>
                                                                <div class="col-sm-8">
                                                                    <input type="text" name="rut_emisor" id="rut_emisor"
                                                                        class="form-control form-control-sm"
                                                                        value="{{ $documento['Encabezado']['Emisor']['RUTEmisor'] }}" disabled>
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
                                                                        value="{{ $documento['Encabezado']['Emisor']['GiroEmis'] }}" disabled>
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
                                                                        value="{{ $documento['Encabezado']['Emisor']['DirOrigen'] }}">
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <label
                                                                    class="col-sm-4 col-form-label col-form-label-sm">Comuna</label>
                                                                <div class="col-sm-8">
                                                                    <input type="text" name="comuna_emisor"
                                                                        id="communa_emisor"
                                                                        class="form-control form-control-sm"
                                                                        value="{{ $documento['Encabezado']['Emisor']['CmnaOrigen'] }}"
                                                                        disabled>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <div class="row">
                                                    <div class="col-md-12 mb-3">
                                                        <div class="mb-2 border-bottom">
                                                            <h5>Información del cliente</h5>
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
                                                                        value="{{ $documento['Encabezado']['Receptor']['RznSocRecep'] }}" disabled>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mb-2">
                                                                        <label
                                                                            class="col-sm-4 col-form-label col-form-label-sm">R.U.T.</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" name="rut"
                                                                                id="rut"
                                                                                class="form-control form-control-sm"
                                                                                value="{{ $documento['Encabezado']['Receptor']['RUTRecep'] }}" disabled>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mb-2">
                                                                        <label
                                                                            class="col-sm-4 col-form-label col-form-label-sm">Actividad
                                                                            Económica</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" name="giro"
                                                                                id="giro"
                                                                                class="form-control form-control-sm"
                                                                                value="{{ $documento['Encabezado']['Receptor']['GiroRecep'] }}" disabled>
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
                                                                            <input type="text" name="direccion"
                                                                                id="direccion"
                                                                                class="form-control form-control-sm"
                                                                                value="{{ $documento['Encabezado']['Receptor']['DirRecep'] }}" disabled>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mb-2">
                                                                        <label
                                                                            class="col-sm-4 col-form-label col-form-label-sm">Comuna</label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" name="comuna"
                                                                                id="comuna"
                                                                                class="form-control form-control-sm"
                                                                                value="{{ $documento['Encabezado']['Receptor']['CmnaRecep'] }}" disabled>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 ">
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
                                                                        value="Factura Electrónica (33)"
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
                                                                        value="{{ $documento['Encabezado']['IdDoc']['FchEmis'] }}" disabled>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-2 border-bottom">
                                                            <h5>Información Comercial</h5>
                                                        </div>
                                                        <div class="row mx-1">
                                                            <div class="row mb-2">
                                                                <label
                                                                    class="col-sm-4 col-form-label col-form-label-sm">Tipo
                                                                    de
                                                                    Pago</label>
                                                                <div class="col-sm-8">
                                                                    <select name="tipo_pago" id="tipo_pago"
                                                                        class="form-control form-control-sm" disabled>
                                                                        <option @if($documento['Encabezado']['IdDoc']['FmaPago'] == 1) selected @endif value="1">Contado</option>
                                                                        <option @if($documento['Encabezado']['IdDoc']['FmaPago'] == 2) selected @endif value="2">Credito</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <label
                                                                    class="col-sm-4 col-form-label col-form-label-sm">Fecha
                                                                    Vencimiento</label>
                                                                <div class="col-sm-8">
                                                                    <input type="date" name="fecha_vencimiento"
                                                                        id="fecha_vencimiento"
                                                                        class="form-control form-control-sm"
                                                                        value="{{ (isset($documento['Encabezado']['IdDoc']['FchVenc']))?$documento['Encabezado']['IdDoc']['FchVenc']:$documento['Encabezado']['IdDoc']['FchEmis'] }}" disabled>
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
                                                            <th>SKU</th>
                                                            <th>Item</th>
                                                            <th>Cantidad</th>
                                                            <th>Precio</th>
                                                            <th>Subtotal</th>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                            $detalles = $documento['Detalle'];
                                                            if (!isset($detalles[0])){
                                                                $detalles = [$detalles];
                                                            }
                                                            @endphp
                                                            @foreach($detalles as $det)
                                                            <tr>
                                                                <td>-</td>
                                                                <td>{{ $det['NmbItem'] }}</td>
                                                                <td>{{ $det['QtyItem'] }}</td>
                                                                <td>{{ $det['PrcItem'] }}</td>
                                                                <td>{{ $det['MontoItem'] }}</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="mb-2 border-bottom">
                                                    <h5>Referencias del documento</h5>
                                                </div>
                                                <div class="col-md-12">
                                                    <table id="tablaReferencia" class="table table-sm mb-3">
                                                        <thead>
                                                            <th>Tipo Documento</th>
                                                            <th>Folio</th>
                                                            <th>Fecha</th>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                                <div class="mb-2 border-bottom">
                                                    <h5>Glosa documento</h5>
                                                </div>
                                                <div class="col-md-12">
                                                    <textarea id="glosaTxt" maxlength="250" class="form-control mt-3" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-2 border-bottom">
                                                    <h5>Resumen de montos</h5>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-7">
                                                            <p>Subtotal </p>
                                                        </div>
                                                        <div class="col-md-5 text-end">
                                                            <p id="lblSubtotalDoc">{{ $documento['Encabezado']['Totales']['MntNeto'] }}</p>
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
                                                            <p id="lblneto">{{ $documento['Encabezado']['Totales']['MntNeto'] }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-7">
                                                            <p>IVA </p>
                                                        </div>
                                                        <div class="col-md-5 text-end">
                                                            <p id="lbliva">{{ $documento['Encabezado']['Totales']['IVA'] }}</p>
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
                                                            <p id="lbltotal">{{ $documento['Encabezado']['Totales']['MntTotal'] }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-danger"
                                onclick="location.href = '/compras/ordenescompra'">
                                <i class="mdi mdi-cancel"></i>
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('plugin-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment-with-locales.min.js"
        integrity="sha512-4F1cxYdMiAW98oomSLaygEwmCnIP38pb4Kx70yQYqRwLVCs3DbRumfBq82T08g/4LJ/smbFGFpmeFlQgoDccgg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush

@push('custom-scripts')
    <script>
        var currentUserId = {{ auth()->user()->id }};
    </script>
@endpush
