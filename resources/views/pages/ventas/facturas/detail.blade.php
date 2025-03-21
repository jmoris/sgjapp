@extends('layout.master')

@section('title', 'Visor de Facturas Electrónicas - Compra')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Factura Electrónica #{{ $documento['Encabezado']['IdDoc']['Folio'] }} - Venta</h4>
        </div>
        <div class="align-end">
            <button type="button" class="btn btn-success" onclick="window.open('/api/ventas/facturas/vistaprevia/{{ intval($documento['Encabezado']['IdDoc']['Folio'])}}', '_blank')">
                <i class="mdi mdi-magnify"></i>
                Visualizar PDF
            </button>
            <button type="button" class="btn btn-danger" onclick="location.href = '/ventas/facturas'">
                <i class="mdi mdi-arrow-left"></i>
                Volver
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-xl-12">
            <div class="row flex-grow-1">
                <div class="col-md-8 grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h4 class="card-title mb-0">VISOR DE FACTURAS ELECTRÓNICAS - VENTA</h4>
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
                                                            <label class="col-sm-4 col-form-label col-form-label-sm">Razón
                                                                Social</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" name="razon_social_emisor"
                                                                    id="razon_social_emisor"
                                                                    class="form-control form-control-sm"
                                                                    value="{{ $documento['Encabezado']['Emisor']['RznSoc'] }}"
                                                                    disabled>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <label
                                                                class="col-sm-4 col-form-label col-form-label-sm">R.U.T.</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" name="rut_emisor" id="rut_emisor"
                                                                    class="form-control form-control-sm"
                                                                    value="{{ $documento['Encabezado']['Emisor']['RUTEmisor'] }}"
                                                                    disabled>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <label
                                                                class="col-sm-4 col-form-label col-form-label-sm">Actividad
                                                                Económica</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" name="giro_emisor" id="giro_emisor"
                                                                    class="form-control form-control-sm"
                                                                    value="{{ $documento['Encabezado']['Emisor']['GiroEmis'] }}"
                                                                    disabled>
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
                                                                    id="communa_emisor" class="form-control form-control-sm"
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
                                                                            value="{{ $documento['Encabezado']['Receptor']['RznSocRecep'] }}"
                                                                            disabled>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-2">
                                                                    <label
                                                                        class="col-sm-4 col-form-label col-form-label-sm">R.U.T.</label>
                                                                    <div class="col-sm-8">
                                                                        <input type="text" name="rut" id="rut"
                                                                            class="form-control form-control-sm"
                                                                            value="{{ $documento['Encabezado']['Receptor']['RUTRecep'] }}"
                                                                            disabled>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-2">
                                                                    <label
                                                                        class="col-sm-4 col-form-label col-form-label-sm">Actividad
                                                                        Económica</label>
                                                                    <div class="col-sm-8">
                                                                        <input type="text" name="giro" id="giro"
                                                                            class="form-control form-control-sm"
                                                                            value="{{ $documento['Encabezado']['Receptor']['GiroRecep'] }}"
                                                                            disabled>
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
                                                                            value="{{ $documento['Encabezado']['Receptor']['DirRecep'] }}"
                                                                            disabled>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-2">
                                                                    <label
                                                                        class="col-sm-4 col-form-label col-form-label-sm">Comuna</label>
                                                                    <div class="col-sm-8">
                                                                        <input type="text" name="comuna"
                                                                            id="comuna"
                                                                            class="form-control form-control-sm"
                                                                            value="{{ $documento['Encabezado']['Receptor']['CmnaRecep'] }}"
                                                                            disabled>
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
                                                            <label class="col-sm-4 col-form-label col-form-label-sm">Tipo
                                                                documento</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" name="tipo_doc" id="tipo_doc"
                                                                    value="Factura Electrónica (33)"
                                                                    class="form-control form-control-sm" disabled>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <label class="col-sm-4 col-form-label col-form-label-sm">Fecha
                                                                emisión</label>
                                                            <div class="col-sm-8">
                                                                <input type="date" name="fecha_emision"
                                                                    id="fecha_emision"
                                                                    class="form-control form-control-sm"
                                                                    value="{{ $documento['Encabezado']['IdDoc']['FchEmis'] }}"
                                                                    disabled>
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
                                                            <label class="col-sm-4 col-form-label col-form-label-sm">Tipo
                                                                de
                                                                Pago</label>
                                                            <div class="col-sm-8">
                                                                <select name="tipo_pago" id="tipo_pago"
                                                                    class="form-control form-control-sm" disabled>
                                                                    @php
                                                                        $fma_pago = isset(
                                                                            $documento['Encabezado']['IdDoc'][
                                                                                'FmaPago'
                                                                            ],
                                                                        )
                                                                            ? $documento['Encabezado']['IdDoc'][
                                                                                'FmaPago'
                                                                            ]
                                                                            : null;
                                                                    @endphp
                                                                    <option
                                                                        @if ($fma_pago == 1) selected @endif
                                                                        value="1">Contado</option>
                                                                    <option
                                                                        @if ($fma_pago == 2) selected @endif
                                                                        value="2">Credito</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <label class="col-sm-4 col-form-label col-form-label-sm">Fecha
                                                                Vencimiento</label>
                                                            <div class="col-sm-8">
                                                                <input type="date" name="fecha_vencimiento"
                                                                    id="fecha_vencimiento"
                                                                    class="form-control form-control-sm"
                                                                    value="{{ isset($documento['Encabezado']['IdDoc']['FchVenc']) ? $documento['Encabezado']['IdDoc']['FchVenc'] : $documento['Encabezado']['IdDoc']['FchEmis'] }}"
                                                                    disabled>
                                                            </div>
                                                        </div>

                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <div class="mb-2 border-bottom">
                                                <h5>Referencias del documento</h5>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="row mx-1">
                                                    <table id="tablaReferencia" class="table table-sm mb-3">
                                                        <thead>
                                                            <th>Tipo Documento</th>
                                                            <th>Folio</th>
                                                            <th>Fecha</th>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $referencias = array_key_exists('Referencia', $documento) ? $documento['Referencia']:null;
                                                                if($referencias != null){
                                                                    if (!isset($referencias[0])){
                                                                        $referencias = [$referencias];
                                                                    }
                                                                }
                                                            @endphp
                                                            @if($referencias != null)
                                                            @foreach ($referencias as $ref)
                                                                <tr>
                                                                    @php
                                                                    $tipoDoc = isset($ref['TpoDocRef']) ? $ref['TpoDocRef'] : '-';
                                                                    @endphp
                                                                    <td>{{ \App\Helpers\Herramientas::getTipoDocumento($tipoDoc) }}</td>
                                                                    <td>{{ $ref['FolioRef'] }}</td>
                                                                    <td>{{ date('d/m/Y', strtotime($ref['FchRef'])) }}</td>
                                                                </tr>
                                                            @endforeach
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <div class="col-md-12 mb-2 border-bottom">
                                                <h5 class="d-inline">Detalle del documento</h5>
                                            </div>
                                            <div class="row mx-1">
                                                <div class="table-responsive">
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
                                                                if (!isset($detalles[0])) {
                                                                    $detalles = [$detalles];
                                                                }
                                                            @endphp
                                                            @foreach ($detalles as $det)
                                                                <tr>
                                                                    <td>{{ isset($det['CdgItem']['VlrCodigo']) ? $det['CdgItem']['VlrCodigo'] : '-' }}
                                                                    </td>
                                                                    <td>{{ $det['NmbItem'] }}</td>
                                                                    <td>{{ isset($det['QtyItem']) ? $det['QtyItem'] : 1 }}</td>
                                                                    @php
                                                                        $precio = 0;
                                                                        if (isset($det['PrcItem'])) {
                                                                            $precio = $det['PrcItem'];
                                                                        }
                                                                    @endphp
                                                                    <td>$ {{ number_format($precio, 0, ',', '.') }}
                                                                    </td>
                                                                    <td>$
                                                                        {{ number_format($det['MontoItem'], 0, ',', '.') }}
                                                                    </td>
                                                                </tr>
                                                                @if (isset($det['DscItem']))
                                                                    <tr>
                                                                        <td></td>
                                                                        <td colspan="4">{!! chunk_split($det['DscItem'], 100, '<br>') !!}</td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 grid-margin">
                    <div class="align-items-start">
                        <div class="col-md-12 mb-2">
                            <div class="card">
                                <div class="card-body">
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <h4 class="card-title mb-0">INFORMACIÓN DE MONTOS TOTALES</h4>
                                        </div>
                                        <div class="col-md-12 mx-2 my-2">
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <p>Subtotal </p>
                                                </div>
                                                <div class="col-md-5 text-end">
                                                    <p id="lblSubtotalDoc">
                                                        @php
                                                            $neto = $documento['Encabezado']['Totales']['MntNeto'];
                                                            $exento = isset(
                                                                $documento['Encabezado']['Totales']['MntExe'],
                                                            )
                                                                ? $documento['Encabezado']['Totales']['MntExe']
                                                                : 0;
                                                        @endphp
                                                        $ {{ number_format($neto + $exento, 0, ',', '.') }}</p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <p>Monto exento </p>
                                                </div>
                                                <div class="col-md-5 text-end">
                                                    <p id="lblexento">$ {{ number_format($exento, 0, ',', '.') }}</p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <p>Monto neto </p>
                                                </div>
                                                <div class="col-md-5 text-end">
                                                    <p id="lblneto">$
                                                        @php
                                                            $neto = (isset($documento['Encabezado']['Totales']['MntNeto'])?$documento['Encabezado']['Totales']['MntNeto']:0);
                                                        @endphp
                                                        {{ number_format($neto, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <p>IVA </p>
                                                </div>
                                                <div class="col-md-5 text-end">
                                                    <p id="lbliva">$
                                                        @php
                                                            $iva = (isset($documento['Encabezado']['Totales']['IVA'])?$documento['Encabezado']['Totales']['IVA']:0);
                                                        @endphp
                                                        {{ number_format($iva, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <p>Impuestos adicionales </p>
                                                </div>
                                                <div class="col-md-5 text-end">
                                                    @php
                                                        $impadicional = 0;
                                                        if (isset($documento['Encabezado']['Totales']['ImptoReten'])) {
                                                            $impadicionales =
                                                                $documento['Encabezado']['Totales']['ImptoReten'];
                                                            if (!isset($impadicionales[0])) {
                                                                $impadicionales = [$impadicionales];
                                                            }
                                                            foreach ($impadicionales as $impuesto) {
                                                                if (isset($impuesto['MontoImp'])) {
                                                                    if ($impuesto != false || $impuesto != null) {
                                                                        if ($impuesto['MontoImp'] != false) {
                                                                            $impadicional += $impuesto['MontoImp'];
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    <p id="lblimpad">$ {{ number_format($impadicional, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <p><b>Total </b></p>
                                                </div>
                                                <div class="col-md-5 text-end">
                                                    <p id="lbltotal">$
                                                        @php
                                                            $total = (isset($documento['Encabezado']['Totales']['MntTotal'])?$documento['Encabezado']['Totales']['MntTotal']:0);
                                                        @endphp
                                                        {{ number_format($total, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <h4 class="card-title mb-0">PAGOS ASOCIADOS AL DOCUMENTO</h4>
                                            <button onclick="abrirModalPago()" class="btn btn-primary btn-small float-end" title="Agregar pago">
                                                <i class="mdi mdi-plus"></i>
                                            </button>
                                        </div>
                                        <table class="table">
                                            <thead>
                                                <th>Tipo</th>
                                                <th>Fecha</th>
                                                <th>Monto</th>
                                            </thead>
                                            <tbody>
                                                @if(count($pagos) == 0)
                                                <tr>
                                                    <td colspan="3">No existen pagos asociados</td>
                                                </tr>
                                                @endif
                                                @foreach($pagos as $pago)
                                                <tr>
                                                    @php
                                                        $tipo_pago = '';
                                                        if($pago->tipo_pago == 1){
                                                            $tipo_pago = 'Efectivo';
                                                        }else if($pago->tipo_pago == 2){
                                                            $tipo_pago = 'Transferencia';
                                                        }else if($pago->tipo_pago == 3){
                                                            $tipo_pago = 'Cheque';
                                                        }else if($pago->tipo_pago == 4){
                                                            $tipo_pago = 'Otro';
                                                        }

                                                    @endphp
                                                    <td>{{$tipo_pago}}</td>
                                                    <td>{{$pago->fecha_pago}}</td>
                                                    <td>$ {{ number_format($pago->monto_pago, 0, ',', '.') }}</td>
                                                    <td><button class="btn btn-sm btn-outline-danger" style="padding:.25em .5em; float:left;" onclick="eliminarPago({{ $pago->id }})"><i class="mdi mdi-delete"></i></button></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="modalPagos" class="modal" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">ASOCIAR PAGO A FACTURA</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mx-2">
                    <div class="mb-2">
                        <label class="form-label">Tipo de pago</label>
                        <select class="form-control" id="tipo_pago">
                            <option>Seleccione un tipo de pago</option>
                            <option value="1">Efectivo</option>
                            <option value="2">Transferencia Electrónica</option>
                            <option value="3">Cheque</option>
                            <option value="4">Otro</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Fecha de pago</label>
                        <input type="date" name="fecha_pago"
                            id="fecha_pago"
                            class="form-control"
                            value="{{ date('Y-m-d') }}"
                            max="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Monto de Pago</label>
                        <input type="text" id="monto_pago" value="0" min="0" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Glosa</label>
                        <textarea id="glosa_pago" rows="3" class="form-control form-control-sm"
                                    placeholder="GLOSA CORRESPONDIENTE AL PAGO"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-danger" onclick="cerrarModalPago()">Cerrar</button>
              <button type="button" class="btn btn-primary" onclick="procesarPago()">Agregar</button>
            </div>
          </div>
        </div>
      </div>
@endsection

@push('plugin-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment-with-locales.min.js"
        integrity="sha512-4F1cxYdMiAW98oomSLaygEwmCnIP38pb4Kx70yQYqRwLVCs3DbRumfBq82T08g/4LJ/smbFGFpmeFlQgoDccgg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"
        integrity="sha512-efAcjYoYT0sXxQRtxGY37CKYmqsFVOIwMApaEbrxJr4RwqVVGw8o+Lfh/+59TU07+suZn1BWq4fDl5fdgyCNkw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush

@push('custom-scripts')
    <script>
        var currentUserId = {{ auth()->user()->id }};

        $(document).ready(function() {
            $("#monto_pago").inputmask('numeric', {
                prefix: '$ ',
                min: 0,
                radixPoint: ',',
                groupSeparator: '.',
                rightAlign: false
            });
        });

        function abrirModalPago(){
            $('#modalPagos').modal('show');
        }

        function limpiarModal(){
            $('#tipo_pago').val(0);
            $('#fecha_pago').val('{{date("Y-m-d")}}');
            $('#monto_pago').val(0);
            $('#glosa_pago').val('');
        }

        function cerrarModalPago(){
            limpiarModal();
            $('#modalPagos').modal('hide');
        }

        function eliminarPago(id){
            Swal.fire({
                title: "Confirmar eliminación del pago",
                text: "La acción que desea realizar es irreversible, ¿desea continuar con la operación?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#6571FF",
                cancelButtonColor: "#FF3366",
                confirmButtonText: "Confirmar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "/api/compras/facturas/pagos/eliminar/" + id,
                        data: {}, // serializes the form's elements.
                        success: function(data){
                            location.reload();
                        }
                    });
                }
            });
        }

        function procesarPago(){
            $.ajax({
                type: "POST",
                url: "/api/compras/facturas/pagos/{{$factura->id}}",
                data: {
                    tipo_pago: $('#tipo_pago').val(),
                    fecha_pago: $('#fecha_pago').val(),
                    monto_pago: $('#monto_pago').inputmask('unmaskedvalue'),
                    glosa: $('#glosa_pago').val()
                }, // serializes the form's elements.
                success: function(data){
                    if(data.success){
                        location.reload();
                    }else{
                        console.log(data);
                    }
                }
            });
        }

    </script>
@endpush
