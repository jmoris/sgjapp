@extends('layout.master')

@section('title', 'Visor de Facturas de Compra')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Factura de Compra #{{ $documento['Encabezado']['IdDoc']['Folio'] }}</h4>
        </div>
        <div class="align-end">
            <button type="button" class="btn btn-danger" onclick="location.href = '/compras/facturas'">
                <i class="mdi mdi-arrow-left"></i>
                Volver
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-8 grid-margin stretch-card">
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
                                                                    <option value="1">Operación Constituye Venta
                                                                    </option>
                                                                    <option value="2">Venta Por efectuar</option>
                                                                    <option value="3">Consigación</option>
                                                                    <option value="4">Donación</option>
                                                                    <option value="5">Traslado Interno</option>
                                                                    <option value="6">No Constituye Venta</option>
                                                                    <option value="7">Devolución</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-2 border-bottom">
                                                        <h5>Información Traslado</h5>
                                                    </div>
                                                    <div class="row mx-1">

                                                        <div class="row mb-2">
                                                            <label
                                                                class="col-sm-4 col-form-label col-form-label-sm">Comuna
                                                                de Destino</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" name="comuna_destino"
                                                                            id="comuna_destino"
                                                                            class="form-control form-control-sm"
                                                                            placeholder="Ingrese dirección o deje en blanco">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <label
                                                                class="col-sm-4 col-form-label col-form-label-sm">Dirección
                                                                de Destino</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" name="direccion_destino"
                                                                            id="direccion_destino"
                                                                            class="form-control form-control-sm"
                                                                            placeholder="Ingrese dirección o deje en blanco">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <label
                                                                class="col-sm-4 col-form-label col-form-label-sm">Patente</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" name="patente"
                                                                            id="patente"
                                                                            class="form-control form-control-sm"
                                                                            maxlength="6"
                                                                            placeholder="Ingrese Patente del vehiculo">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <label
                                                                class="col-sm-4 col-form-label col-form-label-sm">RUT Chofer</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" name="rut_chofer"
                                                                            id="rut_chofer"
                                                                            class="form-control form-control-sm"
                                                                            placeholder="Ingrese RUT del chofer">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <label
                                                                class="col-sm-4 col-form-label col-form-label-sm">Nombre Chofer</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" name="nombre_chofer"
                                                                            id="nombre_chofer"
                                                                            class="form-control form-control-sm"
                                                                            placeholder="Ingrese Nombre del chofer">
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
                                                <table id="tablaReferencia" class="table table-sm mb-3">
                                                    <thead>
                                                        <th>Tipo Documento</th>
                                                        <th>Folio</th>
                                                        <th>Fecha</th>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $referencias = isset($documento['Referencia'])
                                                                ? $documento['Referencia']
                                                                : [];
                                                            if (!is_array($referencias)) {
                                                                if (!isset($referencias[0])) {
                                                                    $referencias = [$referencias];
                                                                }
                                                            }
                                                        @endphp
                                                        @foreach ($referencias as $ref)
                                                            <tr>
                                                                <td>{{ $ref['TpoDocRef'] }}</td>
                                                                <td>{{ $ref['FolioRef'] }}</td>
                                                                <td>{{ $ref['FchRef'] }}</td>
                                                            </tr>
                                                        @endforeach

                                                    </tbody>
                                                </table>
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
                                                            if (!isset($detalles[0])) {
                                                                $detalles = [$detalles];
                                                            }
                                                        @endphp
                                                        @foreach ($detalles as $det)
                                                            <tr>
                                                                <td>-</td>
                                                                <td>{{ $det['NmbItem'] }}</td>
                                                                <td>{{ $det['QtyItem'] }}</td>
                                                                <td>$ {{ number_format($det['PrcItem'], 0, ',', '.') }}
                                                                </td>
                                                                <td>$ {{ number_format($det['MontoItem'], 0, ',', '.') }}
                                                                </td>
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
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="row align-items-start">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <h4 class="card-title mb-0">INFORMACION DOCUMENTO Y MONTOS</h4>
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
    </script>
@endpush
