@extends('layout.master')

@section('title', 'Visor de Facturas Electrónicas - Compra')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Factura Electrónica #{{ $documento['Encabezado']['IdDoc']['Folio'] }} - Compra</h4>
        </div>
        <div class="align-end">
            <button type="button" class="btn btn-success" onclick="window.open('/api/compras/facturas/vistaprevia/{{ $documento['Encabezado']['Emisor']['RUTEmisor'].'/33/'.intval($documento['Encabezado']['IdDoc']['Folio'])}}', '_blank')">
                <i class="mdi mdi-magnify"></i>
                Visualizar PDF
            </button>
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
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h4 class="card-title mb-0">VISOR DE FACTURAS ELECTRÓNICAS - COMPRA</h4>
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
                                            <h4 class="card-title mb-0">CATEGORIZACIÓN DE DOCUMENTO</h4>
                                        </div>
                                        <div class="row my-2">
                                            <label class="col-sm-4 col-form-label col-form-label-sm">Categoria</label>
                                            <div class="col-sm-8">
                                                <select onchange="categorizarDocumento()" id="categoriaDoc" class="form-control form-control-sm">
                                                    <option>Sin categorizar</option>
                                                    @foreach($categorias as $cat)
                                                    <option value="{{$cat->id}}">{{$cat->nombre}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
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
                                                        {{ number_format($documento['Encabezado']['Totales']['MntNeto'], 0, ',', '.') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <p>IVA </p>
                                                </div>
                                                <div class="col-md-5 text-end">
                                                    <p id="lbliva">$
                                                        {{ number_format($documento['Encabezado']['Totales']['IVA'], 0, ',', '.') }}
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
                                                        {{ number_format($documento['Encabezado']['Totales']['MntTotal'], 0, ',', '.') }}
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
                                        </div>
                                        <table class="table">
                                            <thead>
                                                <th>Tipo</th>
                                                <th>Fecha</th>
                                                <th>Monto</th>
                                            </thead>
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
@endsection

@push('plugin-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment-with-locales.min.js"
        integrity="sha512-4F1cxYdMiAW98oomSLaygEwmCnIP38pb4Kx70yQYqRwLVCs3DbRumfBq82T08g/4LJ/smbFGFpmeFlQgoDccgg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush

@push('custom-scripts')
    <script>
        var currentUserId = {{ auth()->user()->id }};

        function categorizarDocumento(){
            var data = {
                categoria: $('#categoriaDoc option:selected').val()
            };

            $.ajax({
                type: "POST",
                url: "/api/compras/categorizar/factura/{{$documento['Encabezado']['Emisor']['RUTEmisor']}}/{{intval($documento['Encabezado']['IdDoc']['Folio'])}}",
                data: data, // serializes the form's elements.
                success: function(data){
                    $.toast({
                        type: 'success',
                        title: 'Categorización de documento',
                        subtitle: 'ahora',
                        position: 'top-right',
                        content: 'Documento categorizado correctamente.',
                        delay: 3000
                });
                }
            });
        }
    </script>
@endpush
