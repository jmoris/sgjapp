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
                            <h4 class="card-title mb-4">VISOR DE FACTURAS ELECTRÓNICAS - COMPRA</h4>

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

                            {{-- Información del documento / comercial --}}
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <h5 class="pb-2 mb-3 border-bottom">Información del documento</h5>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label form-label-sm text-muted mb-1">Tipo documento</label>
                                            <input type="text" class="form-control form-control-sm"
                                                value="Factura Electrónica (33)" disabled>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label form-label-sm text-muted mb-1">Fecha emisión</label>
                                            <input type="date" class="form-control form-control-sm"
                                                value="{{ $documento['Encabezado']['IdDoc']['FchEmis'] }}" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="pb-2 mb-3 border-bottom">Información Comercial</h5>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label form-label-sm text-muted mb-1">Tipo de Pago</label>
                                            @php
                                                $fma_pago = isset($documento['Encabezado']['IdDoc']['FmaPago'])
                                                    ? $documento['Encabezado']['IdDoc']['FmaPago']
                                                    : null;
                                            @endphp
                                            <select class="form-control form-control-sm" disabled>
                                                <option @if ($fma_pago == 1) selected @endif value="1">Contado</option>
                                                <option @if ($fma_pago == 2) selected @endif value="2">Credito</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label form-label-sm text-muted mb-1">Fecha Vencimiento</label>
                                            <input type="date" class="form-control form-control-sm"
                                                value="{{ isset($documento['Encabezado']['IdDoc']['FchVenc']) ? $documento['Encabezado']['IdDoc']['FchVenc'] : $documento['Encabezado']['IdDoc']['FchEmis'] }}"
                                                disabled>
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
                                                    <td>{{ isset($det['CdgItem']['VlrCodigo']) ? $det['CdgItem']['VlrCodigo'] : '-' }}
                                                    </td>
                                                    <td>{!! $det['NmbItem'] !!}</td>
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
                <div class="col-md-4 grid-margin">
                    <div class="align-items-start">
                        <div class="col-md-12 mb-2">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-3">CATEGORIZACIÓN DE DOCUMENTO</h4>
                                    <div class="mb-4">
                                        <label class="form-label form-label-sm text-muted mb-1">Categoria</label>
                                        <select onchange="categorizarDocumento()" id="categoriaDoc" class="form-control form-control-sm">
                                            <option>Sin categorizar</option>
                                            @foreach($categorias as $cat)
                                            <option value="{{$cat->id}}" @if($factura->categoria_documento_id == $cat->id) selected @endif>{{$cat->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <h4 class="card-title mb-3">ASIGNACIÓN PROYECTO/OBRA DE DOCUMENTO</h4>
                                    <div>
                                        <label class="form-label form-label-sm text-muted mb-1">Proyecto/Obra</label>
                                        <select onchange="asignarProyectoDocumento()" id="proyectoDoc" class="form-control form-control-sm">
                                            <option>Sin proyecto</option>
                                            @foreach($proyectos as $proy)
                                            <option value="{{$proy->id}}" @if($factura->proyecto_id == $proy->id) selected @endif>{{$proy->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="card-title mb-0">PAGOS ASOCIADOS AL DOCUMENTO</h4>
                                        <button onclick="abrirModalPago()" class="btn btn-primary btn-sm" title="Agregar pago">
                                            <i class="mdi mdi-plus"></i>
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0">
                                            <thead>
                                                <th>Tipo</th>
                                                <th>Fecha</th>
                                                <th>Monto</th>
                                                <th></th>
                                            </thead>
                                            <tbody>
                                                @if(count($pagos) == 0)
                                                <tr>
                                                    <td colspan="4" class="text-muted">No existen pagos asociados</td>
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
                                                    <td class="text-end"><button class="btn btn-sm btn-outline-danger" style="padding:.25em .5em;" onclick="eliminarPago({{ $pago->id }})"><i class="mdi mdi-delete"></i></button></td>
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

        function asignarProyectoDocumento(){
            var data = {
                proyecto: $('#proyectoDoc option:selected').val()
            };

            $.ajax({
                type: "POST",
                url: "/api/compras/proyectos/factura/{{$documento['Encabezado']['Emisor']['RUTEmisor']}}/{{intval($documento['Encabezado']['IdDoc']['Folio'])}}",
                data: data, // serializes the form's elements.
                success: function(data){
                    $.toast({
                        type: 'success',
                        title: 'Asignación de proyecto de documento',
                        subtitle: 'ahora',
                        position: 'top-right',
                        content: 'Documento asignado correctamente.',
                        delay: 3000
                });
                }
            });
        }
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
