@extends('layout.master')

@section('title', 'Visor de Guias de Despacho Electrónica - Compra')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Guia de Despacho Electrónica #{{ $documento['Encabezado']['IdDoc']['Folio'] }} - Compra</h4>
        </div>
        <div class="align-end">
            <button type="button" class="btn btn-danger" onclick="location.href = '/compras/facturas'">
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
                            <h4 class="card-title mb-4">VISOR DE GUIAS DE DESPACHO ELECTRÓNICA - COMPRA</h4>

                            {{-- Información del emisor --}}
                            <div class="mb-4">
                                <h5 class="pb-2 mb-3 border-bottom">Información del emisor</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm text-muted mb-1">Razón Social</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $documento['Encabezado']['Emisor']['RznSoc'] }}" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm text-muted mb-1">Dirección</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $documento['Encabezado']['Emisor']['DirOrigen'] }}" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm text-muted mb-1">R.U.T.</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $documento['Encabezado']['Emisor']['RUTEmisor'] }}" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm text-muted mb-1">Comuna</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $documento['Encabezado']['Emisor']['CmnaOrigen'] }}" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm text-muted mb-1">Actividad Económica</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $documento['Encabezado']['Emisor']['GiroEmis'] }}" disabled>
                                    </div>
                                </div>
                            </div>

                            {{-- Información del cliente --}}
                            <div class="mb-4">
                                <h5 class="pb-2 mb-3 border-bottom">Información del cliente</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm text-muted mb-1">Razón Social</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $documento['Encabezado']['Receptor']['RznSocRecep'] }}" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm text-muted mb-1">Dirección</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $documento['Encabezado']['Receptor']['DirRecep'] }}" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm text-muted mb-1">R.U.T.</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $documento['Encabezado']['Receptor']['RUTRecep'] }}" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm text-muted mb-1">Comuna</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $documento['Encabezado']['Receptor']['CmnaRecep'] }}" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-sm text-muted mb-1">Actividad Económica</label>
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $documento['Encabezado']['Receptor']['GiroRecep'] }}" disabled>
                                    </div>
                                </div>
                            </div>

                            {{-- Información del documento / traslado --}}
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <h5 class="pb-2 mb-3 border-bottom">Información del documento</h5>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label form-label-sm text-muted mb-1">Tipo documento</label>
                                            <input type="text" class="form-control form-control-sm"
                                                value="Guia de Despacho Electrónica (52)" disabled>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label form-label-sm text-muted mb-1">Fecha emisión</label>
                                            <input type="date" class="form-control form-control-sm"
                                                value="{{ date('Y-m-d', strtotime($documento['Encabezado']['IdDoc']['FchEmis'])) }}" disabled>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label form-label-sm text-muted mb-1">Tipo Despacho</label>
                                            @php $despacho = isset($documento['Encabezado']['IdDoc']['TipoDespacho'])?$documento['Encabezado']['IdDoc']['TipoDespacho']:null; @endphp
                                            <select class="form-control form-control-sm" disabled>
                                                <option value="">Sin Tipo de Despacho</option>
                                                <option @if($despacho==1) selected @endif value="1">Comprador</option>
                                                <option @if($despacho==2) selected @endif value="2">Emisor al Comprador</option>
                                                <option @if($despacho==3) selected @endif value="3">Emisor a Otro</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label form-label-sm text-muted mb-1">Tipo Traslado</label>
                                            @php $traslado = isset($documento['Encabezado']['IdDoc']['IndTraslado'])?$documento['Encabezado']['IdDoc']['IndTraslado']:null; @endphp
                                            <select class="form-control form-control-sm" disabled>
                                                <option value="">Sin Tipo de Traslado</option>
                                                <option @if($traslado==1) selected @endif value="1">Operación Constituye Venta</option>
                                                <option @if($traslado==2) selected @endif value="2">Venta Por efectuar</option>
                                                <option @if($traslado==3) selected @endif value="3">Consigación</option>
                                                <option @if($traslado==4) selected @endif value="4">Donación</option>
                                                <option @if($traslado==5) selected @endif value="5">Traslado Interno</option>
                                                <option @if($traslado==6) selected @endif value="6">No Constituye Venta</option>
                                                <option @if($traslado==7) selected @endif value="7">Devolución</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="pb-2 mb-3 border-bottom">Información Traslado</h5>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label form-label-sm text-muted mb-1">Comuna de Destino</label>
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ isset($documento['Encabezado']['Transporte']['CmnaDest'])?$documento['Encabezado']['Transporte']['CmnaDest']:'' }}" disabled>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label form-label-sm text-muted mb-1">Dirección de Destino</label>
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ isset($documento['Encabezado']['Transporte']['DirDest'])?$documento['Encabezado']['Transporte']['DirDest']:'' }}" disabled>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label form-label-sm text-muted mb-1">Patente</label>
                                            <input type="text" class="form-control form-control-sm" maxlength="6"
                                                value="{{ isset($documento['Encabezado']['Transporte']['Patente'])?$documento['Encabezado']['Transporte']['Patente']:'' }}" disabled>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label form-label-sm text-muted mb-1">RUT Chofer</label>
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ isset($documento['Encabezado']['Transporte']['Chofer']['RUTChofer'])?$documento['Encabezado']['Transporte']['Chofer']['RUTChofer']:'' }}" disabled>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label form-label-sm text-muted mb-1">Nombre Chofer</label>
                                            <input type="text" class="form-control form-control-sm"
                                                value="{{ isset($documento['Encabezado']['Transporte']['Chofer']['NombreChofer'])?$documento['Encabezado']['Transporte']['Chofer']['NombreChofer']:'' }}" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Referencias del documento --}}
                            <div class="mb-4">
                                <h5 class="pb-2 mb-3 border-bottom">Referencias del documento</h5>
                                <div class="table-responsive">
                                    <table id="tablaReferencia" class="table table-sm mb-0">
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
                                            @else
                                                <tr>
                                                    <td colspan="3" class="text-muted">Sin referencias</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Detalle del documento --}}
                            <div>
                                <h5 class="pb-2 mb-3 border-bottom">Detalle del documento</h5>
                                <div class="table-responsive">
                                    <table id="tablaDetalle" class="table table-sm mb-0">
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
                                                    <td>{{ isset($det['CdgItem']['VlrCodigo'])?$det['CdgItem']['VlrCodigo']:'-' }}</td>
                                                    <td>{{ $det['NmbItem'] }}</td>
                                                    <td>{{ isset($det['QtyItem'])?$det['QtyItem']:1 }}</td>
                                                    @php
                                                        $precio = 0;
                                                        if(isset($det['PrcItem'])){
                                                            $precio = $det['PrcItem'];
                                                        }
                                                    @endphp
                                                    <td>$ {{ number_format($precio, 0, ',', '.') }}
                                                    </td>
                                                    <td>$ {{ number_format($det['MontoItem'], 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                                @if(isset($det['DscItem']))
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
                <div class="col-md-4 grid-margin">
                    <div class="align-items-start">
                        <div class="col-md-12 mb-2">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-3">INFORMACIÓN DE MONTOS TOTALES</h4>
                                    @php
                                        $neto = isset($documento['Encabezado']['Totales']['MntNeto']) ? $documento['Encabezado']['Totales']['MntNeto'] : 0;
                                        $exento = isset($documento['Encabezado']['Totales']['MntExe']) ? $documento['Encabezado']['Totales']['MntExe'] : 0;
                                        $iva = isset($documento['Encabezado']['Totales']['IVA']) ? $documento['Encabezado']['Totales']['IVA'] : 0;
                                        $total = isset($documento['Encabezado']['Totales']['MntTotal']) ? $documento['Encabezado']['Totales']['MntTotal'] : 0;
                                        $impadicional = 0;
                                        if (isset($documento['Encabezado']['Totales']['ImptoReten'])) {
                                            $impadicionales = $documento['Encabezado']['Totales']['ImptoReten'];
                                            if (!isset($impadicionales[0])) {
                                                $impadicionales = [$impadicionales];
                                            }
                                            foreach ($impadicionales as $impuesto) {
                                                if (isset($impuesto['MontoImp']) && $impuesto['MontoImp'] != false) {
                                                    $impadicional += $impuesto['MontoImp'];
                                                }
                                            }
                                        }
                                    @endphp
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal</span>
                                        <span id="lblSubtotalDoc">$ {{ number_format($neto + $exento, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Monto exento</span>
                                        <span id="lblexento">$ {{ number_format($exento, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Monto neto</span>
                                        <span id="lblneto">$ {{ number_format($neto, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>IVA</span>
                                        <span id="lbliva">$ {{ number_format($iva, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Impuestos adicionales</span>
                                        <span id="lblimpad">$ {{ number_format($impadicional, 0, ',', '.') }}</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between fw-bold">
                                        <span>Total</span>
                                        <span id="lbltotal">$ {{ number_format($total, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
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
