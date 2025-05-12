@extends('layout.master')

@section('title', 'Gestión de Proyectos')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0 d-inline">Gestión de Proyectos&nbsp;&nbsp;</h4>
            <small class="d-inline">{{ $proyecto->nombre }}</small>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div style="width:100%; margin-top:24px;"></div>
                                <div class="row col-md-12">
                                    <div class="col-md-3">
                                        <div class="d-flex justify-content-between align-items-baseline mb-3">
                                            <h4 class="card-title mb-0">DETALLES DEL PROYECTO</h4>
                                            <small><a href="#" id="editText" onclick="editProyecto()">Editar</a></small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nombre:</label>
                                            <input type="text" name="nombre" id="nombre" class="form-control"
                                                placeholder="Ingrese el nombre del proyecto" value="{{ $proyecto->nombre }}"
                                                disabled>
                                        </div>
                                        <div class="mb-3">
                                            @php
                                                $texto = '';
                                                $color = '';
                                                switch($proyecto->estado){
                                                    case 0:
                                                        $texto = 'EN CURSO';
                                                        $color = 'bg-success';
                                                        break;
                                                    case 1:
                                                        $texto = 'CERRADO';
                                                        $color = 'bg-danger';
                                                    break;
                                                }
                                            @endphp
                                            <label class="form-label mb-0">Estado del Proyecto:</label><br>
                                            <div id="estado_proyecto"><span id="estado" proyecto_id="{{$proyecto->estado}}" class="badge {{$color}}">{{ $texto }}</span></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Monto del Proyecto:</label>
                                            <input type="text" name="monto_proyecto" id="monto_proyecto" class="form-control"
                                                placeholder="Ingrese el monto del proyecto" value="{{ number_format($proyecto->monto_proyecto, 0, ',', '.') }}"
                                                disabled>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Total Facturas:</label>
                                            <input type="text" name="total" id="total" class="form-control"
                                                value="$ {{ number_format($total, 0, ',', '.') }}" disabled>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Saldo Proyecto:</label>
                                            <input type="text" name="total" id="total" class="form-control"
                                                value="$ {{ number_format($proyecto->monto_proyecto-$total, 0, ',', '.') }}" disabled>
                                        </div>
                                        <div class="mb-3">
                                            <a href="javascript:void(0)" onclick="abrirAdjuntos()"><i class="mdi mdi-file-multiple"></i> Ver archivos adjuntos</a>
                                        </div>
                                    </div>
                                    <div class="col-md-9 border-start">
                                        <ul class="nav nav-tabs">
                                            <li class="nav-item">
                                                <button class="nav-link active" id="fact-tab" data-bs-toggle="tab"
                                                    data-bs-target="#facturas" type="button" role="tab"
                                                    aria-selected="true">Facturas</button>
                                            </li>
                                            <li class="nav-item">
                                                <button class="nav-link" id="fact-compra-tab" data-bs-toggle="tab"
                                                    data-bs-target="#facturascompra" type="button" role="tab"
                                                    aria-selected="true">Facturas Compra</button>
                                            </li>
                                            <li class="nav-item">
                                                <button class="nav-link" id="guias-tab" data-bs-toggle="tab"
                                                    data-bs-target="#guias" type="button" role="tab"
                                                    >Guias Despacho</button>
                                            </li>
                                            <li class="nav-item">
                                                <button class="nav-link" id="nc-tab" data-bs-toggle="tab"
                                                    data-bs-target="#notascredito" type="button" role="tab"
                                                    >Notas Credito</button>
                                            </li>
                                            <li class="nav-item">
                                                <button class="nav-link" id="oc-tab" data-bs-toggle="tab"
                                                    data-bs-target="#ordenescompra" type="button" role="tab"
                                                    >Ordenes Compra</button>
                                            </li>
                                        </ul>
                                        <div class="tab-content pb-2 border" id="myTabContent">
                                            <div class="tab-pane mx-4 fade show active" id="facturas" role="tabpanel"
                                                aria-labelledby="fact-tab">
                                                <div style="width:100%; margin-top:24px;"></div>
                                                <div class="d-flex justify-content-between align-items-baseline mb-3">
                                                    <h4 class="card-title mb-0">FACTURAS ASOCIADAS</h4>
                                                </div>
                                                <table id="tablaFact" class="ms-2 table w-100">
                                                    <thead>
                                                        <th>Folio</th>
                                                        <th>Fecha</th>
                                                        <th>Razon Social</th>
                                                        <th>Monto Total</th>
                                                        <th></th>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($facturas as $fact)
                                                            <tr>
                                                                <td>{{ $fact->folio }}</td>
                                                                <td>{{ date('d/m/Y', strtotime($fact->fecha_emision)) }}</td>
                                                                <td>{{ $fact->cliente->razon_social }}</td>
                                                                <td>{{ $fact->monto_total }}</td>
                                                                <td><button type="button" title="Ver Orden de Compra"
                                                                        onclick="vistaPreviaFacturas({{ $fact->folio }})"
                                                                        class="btn btn-outline-primary btnxs px-1 py-0"><i
                                                                            class="mdi mdi-18 mdi-magnify"></i></button></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="tab-pane mx-4 fade show" id="facturascompra" role="tabpanel"
                                                aria-labelledby="fact-compra-tab">
                                                <div style="width:100%; margin-top:24px;"></div>
                                                <div class="d-flex justify-content-between align-items-baseline mb-3">
                                                    <h4 class="card-title mb-0">FACTURAS COMPRA ASOCIADAS</h4>
                                                </div>
                                                <table id="tablaFactCompra" class="ms-2 table w-100">
                                                    <thead>
                                                        <th>Folio</th>
                                                        <th>Fecha</th>
                                                        <th>Razon Social</th>
                                                        <th>Monto Total</th>
                                                        <th></th>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($facturascompra as $fact)
                                                            <tr>
                                                                <td>{{ $fact->folio }}</td>
                                                                <td>{{ date('d/m/Y', strtotime($fact->fecha_emision)) }}</td>
                                                                <td>{{ $fact->razon_social_emisor }}</td>
                                                                <td>{{ $fact->monto_total }}</td>
                                                                <td><button type="button" title="Ver Factura"
                                                                        onclick="vistaPreviaFacturasCompra('{{$fact->rut_emisor}}',{{ $fact->folio }})"
                                                                        class="btn btn-outline-primary btnxs px-1 py-0"><i
                                                                            class="mdi mdi-18 mdi-magnify"></i></button></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="tab-pane mx-4 fade" id="guias" role="tabpanel"
                                                aria-labelledby="fact-tab">
                                                <div style="width:100%; margin-top:24px;"></div>
                                                <div class="d-flex justify-content-between align-items-baseline mb-3">
                                                    <h4 class="card-title mb-0">GUIAS DE DESPACHO ASOCIADAS</h4>
                                                </div>
                                                <table id="tablaGuias" class="ms-2 table w-100">
                                                    <thead>
                                                        <th>Folio</th>
                                                        <th>Fecha</th>
                                                        <th>Razon Social</th>
                                                        <th>Monto Total</th>
                                                        <th></th>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($guias as $guia)
                                                            <tr>
                                                                <td>{{ $guia->folio }}</td>
                                                                <td>{{ date('d/m/Y', strtotime($guia->fecha_emision)) }}</td>
                                                                <td>{{ $guia->cliente->razon_social }}</td>
                                                                <td>{{ $guia->monto_total }}</td>
                                                                <td><button type="button" title="Ver Orden de Compra"
                                                                        onclick="vistaPreviaGuias({{ $guia->folio }}, {{$guia->rev}})"
                                                                        class="btn btn-outline-primary btnxs px-1 py-0"><i
                                                                            class="mdi mdi-18 mdi-magnify"></i></button></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="tab-pane mx-4 fade" id="notascredito" role="tabpanel"
                                                aria-labelledby="fact-tab">
                                                <div style="width:100%; margin-top:24px;"></div>
                                                <div class="d-flex justify-content-between align-items-baseline mb-3">
                                                    <h4 class="card-title mb-0">NOTAS DE CREDITO ASOCIADAS</h4>
                                                </div>
                                                <table id="tablaNC" class="ms-2 table w-100">
                                                    <thead>
                                                        <th>Folio</th>
                                                        <th>Fecha</th>
                                                        <th>Razon Social</th>
                                                        <th>Monto Total</th>
                                                        <th></th>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($notascredito as $nc)
                                                            <tr>
                                                                <td>{{ $nc->folio }}</td>
                                                                <td>{{ date('d/m/Y', strtotime($nc->fecha_emision)) }}</td>
                                                                <td>{{ $nc->cliente->razon_social }}</td>
                                                                <td>{{ $nc->monto_total }}</td>
                                                                <td><button type="button" title="Ver Orden de Compra"
                                                                        onclick="vistaPreviaNC({{ $nc->folio }}, {{$nc->rev}})"
                                                                        class="btn btn-outline-primary btnxs px-1 py-0"><i
                                                                            class="mdi mdi-18 mdi-magnify"></i></button></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="tab-pane mx-4 fade" id="ordenescompra" role="tabpanel"
                                                aria-labelledby="oc-tab">
                                                <div style="width:100%; margin-top:24px;"></div>
                                                <div class="d-flex justify-content-between align-items-baseline mb-3">
                                                    <h4 class="card-title mb-0">ORDENES DE COMPRA ASOCIADAS</h4>
                                                </div>
                                                <table id="tablaOC" class="ms-2 table w-100">
                                                    <thead>
                                                        <th>Folio</th>
                                                        <th>Fecha</th>
                                                        <th>Razon Social</th>
                                                        <th>Monto Total</th>
                                                        <th></th>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($ocs as $oc)
                                                            <tr>
                                                                <td>{{ $oc->folio }}</td>
                                                                <td>{{ date('d/m/Y', strtotime($oc->fecha_emision)) }}</td>
                                                                <td>{{ $oc->proveedor->razon_social }}</td>
                                                                <td>{{ $oc->monto_total }}</td>
                                                                <td><button type="button" title="Ver Orden de Compra"
                                                                        onclick="vistaPreviaOC({{ $oc->folio }}, {{$oc->rev}})"
                                                                        class="btn btn-outline-primary btnxs px-1 py-0"><i
                                                                            class="mdi mdi-18 mdi-magnify"></i></button></td>
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
        </div>
    </div>
<!-- Modal -->
<div class="modal fade" id="modalAdjuntos" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
aria-labelledby="modalAdjuntosLabel" aria-hidden="true">
<div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title" id="modalAdjuntosLabel">DOCUMENTOS ADJUNTOS PROYECTO</h6>
            <button id="btnAdjuntar" class="btn btn-primary ms-auto" onclick="adjuntarDocumentos()" style="padding: 0.1rem 0.5rem; font-size:0.75rem;"><i class="mdi mdi-file-upload-outline"></i> Adjuntar documentos</button>
        </div>
        <div class="modal-body" style="overflow-y: scroll; height: 50vh;">
            <div class="row">
                <div class="col-md-12" id="datadiv">
                    <div class="box">
                        <ul class="directory-list">
                            <li class="folder">Adjuntos {{ $proyecto->nombre }}
                                <ul id="files">
                                    @foreach($adjuntos as $adjunto)
                                        <li><a href="/api/ventas/proyectos/adjuntos/{{$adjunto->id}}">{{ $adjunto->nombre }}</a> <a href="javascript:void(0)" onclick="eliminarAdjunto({{$adjunto->id}})"><i class="ms-1 mdi mdi-delete text-danger"></i></a></li>
                                    @endforeach
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-12 d-none" id="dropdiv">
                    <div class="dropzone-box">
                        <form action="/api/ventas/proyectos/{{$proyecto->id}}/adjuntos" class="dropzone" id="uploadarea">
                            @csrf
                            <div class="dz-message" style="margin:4em auto; " data-dz-message><i style="font-size: 3rem;" class="mdi mdi-upload"></i></br><span>Arrastre los archivos al cuadro o haga click para abrir el explorador</span></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
    </div>
</div>
</div>
@endsection

@push('plugin-scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"
integrity="sha512-efAcjYoYT0sXxQRtxGY37CKYmqsFVOIwMApaEbrxJr4RwqVVGw8o+Lfh/+59TU07+suZn1BWq4fDl5fdgyCNkw=="
crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
@endpush

@push('custom-scripts')
    <script>
        let myDropzone = new Dropzone("#uploadarea", { /* options */ });

        myDropzone.on("queuecomplete", file => {
            renderAdjuntos();
        });

        $(document).ready(function(){
            $('#monto_proyecto').inputmask('numeric', {
                min: 0,
                prefix: '$ ',
                radixPoint: ',',
                groupSeparator: '.',
                rightAlign: false
            });
        });

        function abrirAdjuntos(){
            $('#modalAdjuntos').modal('show');
        }

        function eliminarAdjunto(id){
            Swal.fire({
                title: "¿Quieres confirmar la eliminacion de este archivo?",
                text: "Una vez confirmada la eliminación del archivo este sera eliminado permanentemente.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Confirmar",
                cancelButtonText: "Cancelar"
            }).then((modalResult) => {
                if (modalResult.isConfirmed) {

                    $.ajax({
                        type: "DELETE",
                        url: '/api/ventas/proyectos/adjuntos/'+id,
                        success: function(data) {
                            renderAdjuntos();
                        }
                    });

                }
            });
        }

        function renderAdjuntos(){
            $.ajax({
                type: "GET",
                url: '/api/ventas/proyectos/{{$proyecto->id}}/adjuntos',
                success: function(data) {
                    console.log(data);
                    $('#files').empty();
                    data.forEach(item => {
                        $('#files').append(`<li><a href="/api/ventas/proyectos/adjuntos/${item.id}">${item.nombre}</a> <a href="javascript:void(0)" onclick="eliminarAdjunto(${item.id})"><i class="ms-1 mdi mdi-delete text-danger"></i></a></li>`);
                    });
                }
            });
        }

        function adjuntarDocumentos(){
            if($('#datadiv').hasClass('d-none')){
                $('#datadiv').removeClass('d-none');
                $('#dropdiv').addClass('d-none');
                $('#btnAdjuntar').html('<i class="mdi mdi-file-upload-outline"></i> Adjuntar documentos');
            }else{
                $('#dropdiv').removeClass('d-none');
                $('#datadiv').addClass('d-none');
                $('#btnAdjuntar').html('<i class="mdi mdi-arrow-left"></i> Volver al listado');
            }
            renderAdjuntos();

        }

        function editProyecto(){
            console.log($('#editText').text());
            if($('#editText').text() == 'Guardar'){
                // aqui post a guardar proyecto
                $.ajax({
                    type: "POST",
                    url: '/api/ventas/proyectos/editar/{{$proyecto->id}}',
                    data: {
                        nombre:$('#nombre').val(),
                        monto_proyecto:$('#monto_proyecto').inputmask('unmaskedvalue'),
                        estado: $('#estado').val()
                    }, // serializes the form's elements.
                    success: function(data){
                        if(!data.success){
                            console.log(data.msg);
                            alert(data.msg);
                        }else{
                            $('#editText').text('Editar');
                            $('#nombre').attr('disabled', true);
                            $('#monto_proyecto').attr('disabled', true);
                            var color = '';
                            var texto = '';
                            console.log(data.data.estado);
                            switch(parseInt(data.data.estado)){
                                case 0:
                                    texto = 'EN CURSO';
                                    color = 'bg-success';
                                    break;
                                case 1:
                                    texto = 'CERRADO';
                                    color = 'bg-danger';
                                    break;
                            }
                            var html = '<span proyecto_id="'+data.data.estado+'" class="badge ' + color + '">'+texto+'</span>';
                            $('#estado_proyecto').html(html);
                        }
                    }
                });
            }else{
                $('#nombre').removeAttr('disabled');
                $('#monto_proyecto').removeAttr('disabled');
                var proyecto_id = $('#estado_proyecto span').attr('proyecto_id');
                $('#estado_proyecto').html(`
                    <select id="estado" class="form-control form-control-sm">
                        <option ${(proyecto_id==0)?'selected':''} value="0">EN CURSO</option>
                        <option ${(proyecto_id==1)?'selected':''} value="1">CERRADO</option>
                    </select>`);
                $('#editText').text('Guardar');
            }
        }

        $('#tablaOC').DataTable({
            layout: {
                topStart: {
                    buttons: [{
                        text: 'Excel Resumen',
                        action: function(e, dt, node, config) {
                            location.href = "/api/reportes/excel/proyecto/0/{{ $proyecto->id }}/0"
                        }
                    },
                    {
                        text: 'Excel Detallado',
                        action: function(e, dt, node, config) {
                            location.href = "/api/reportes/excel/proyecto/0/{{ $proyecto->id }}/1"
                        }
                    }]
                }
            },
            lengthMenu: [5, 10, 20, 50],
            responsive: true,
            search: {
                return: true
            },
            language: {
                url: '/assets/js/datatables/es-ES.json',
                thousands: '.'
            },
            columnDefs: [{
                target: 3,
                render: DataTable.render.number('.', ',', 0, '$')
            }],
            order: [
                [0, 'desc']
            ],
            fixedColumns: true,
        });

        $('#tablaFact').DataTable({
            layout: {
                topStart: {
                    buttons: [{
                        text: 'Excel Resumen',
                        action: function(e, dt, node, config) {
                            location.href = "/api/reportes/excel/proyecto/33/{{ $proyecto->id }}/0"
                        }
                    }]
                }
            },
            lengthMenu: [5, 10, 20, 50],
            responsive: true,
            search: {
                return: true
            },
            language: {
                url: '/assets/js/datatables/es-ES.json',
                thousands: '.'
            },
            columnDefs: [{
                target: 3,
                render: DataTable.render.number('.', ',', 0, '$')
            }],
            order: [
                [0, 'desc']
            ],
            fixedColumns: true,
        });

        $('#tablaFactCompra').DataTable({
            layout: {
                topStart: {
                    buttons: [/*{
                        text: 'Excel Resumen',
                        action: function(e, dt, node, config) {
                            location.href = "/api/reportes/excel/proyecto/33/{{ $proyecto->id }}/0"
                        }
                    }*/]
                }
            },
            lengthMenu: [5, 10, 20, 50],
            responsive: true,
            search: {
                return: true
            },
            language: {
                url: '/assets/js/datatables/es-ES.json',
                thousands: '.'
            },
            columnDefs: [{
                target: 3,
                render: DataTable.render.number('.', ',', 0, '$')
            }],
            order: [
                [0, 'desc']
            ],
            fixedColumns: true,
        });

        $('#tablaGuias').DataTable({
            layout: {
                topStart: {
                    buttons: [{
                        text: 'Excel Resumen',
                        action: function(e, dt, node, config) {
                            location.href = "/api/reportes/excel/proyecto/52/{{ $proyecto->id }}/0"
                        }
                    },
                    {
                        text: 'Excel Detallado',
                        action: function(e, dt, node, config) {
                            location.href = "/api/reportes/excel/proyecto/52/{{ $proyecto->id }}/1"
                        }
                    }]
                }
            },
            lengthMenu: [5, 10, 20, 50],
            responsive: true,
            search: {
                return: true
            },
            language: {
                url: '/assets/js/datatables/es-ES.json',
                thousands: '.'
            },
            columnDefs: [{
                target: 3,
                render: DataTable.render.number('.', ',', 0, '$')
            }],
            order: [
                [0, 'desc']
            ],
            fixedColumns: true,
        });

        $('#tablaNC').DataTable({
            layout: {
                topStart: {
                    buttons: [{
                        text: 'Excel Resumen',
                        action: function(e, dt, node, config) {
                            location.href = "/api/reportes/excel/proyecto/61/{{ $proyecto->id }}/0"
                        }
                    }]
                }
            },
            lengthMenu: [5, 10, 20, 50],
            responsive: true,
            search: {
                return: true
            },
            language: {
                url: '/assets/js/datatables/es-ES.json',
                thousands: '.'
            },
            columnDefs: [{
                target: 3,
                render: DataTable.render.number('.', ',', 0, '$')
            }],
            order: [
                [0, 'desc']
            ],
            fixedColumns: true,
        });

        function vistaPreviaOC(id, rev) {
            window.open('/api/compras/ordenescompra/vistaprevia/' + id + '/' + rev);
        }
        function vistaPreviaFacturas(id) {
            window.open('/api/ventas/facturas/vistaprevia/' + id);
        }
        function vistaPreviaFacturasCompra(emisor, id) {
            window.open('/api/compras/facturas/vistaprevia/' + emisor + '/33/' + id);
        }
        function vistaPreviaGuias(id) {
            window.open('/api/ventas/guiasdespacho/vistaprevia/' + id);
        }
        function vistaPreviaNC(id) {
            window.open('/api/ventas/notascredito/vistaprevia/' + id);
        }
    </script>
@endpush

@push('style')
    <style>
        .dropzone-box {
            background: white;
            border-radius: 5px;
            border: 2px dashed rgb(0, 135, 247);
            border-image: none;
            max-width: 600px;
            height: 40vh;
            margin-left: auto;
            margin-right: auto;
        }

        .dropzone-box .dropzone {
            max-height: 100%;
            height: 40vh;
            border: none;
        }

        .box {
            width: 100%;
            border-radius: 2px;
            max-height: 100%;
            overflow-y: scroll;
        }

        @media (min-width: 544px) {
        .box {
            width: 100%;
            max-height: 100%;
            overflow-y: scroll;
        }
        }


        /* The list style
        -------------------------------------------------------------- */

        .directory-list ul {
        margin-left: 10px;
        padding-left: 20px;
        border-left: 1px dashed #ddd;
        }

        .directory-list li {
        list-style: none;
        color: #000;
        font-size: 17px;
        font-weight: normal;
        }

        .directory-list a {
        border-bottom: 1px solid transparent;
        color: #000;
        text-decoration: none;
        transition: all 0.2s ease;
        }

        .directory-list a:hover {
        border-color: #eee;
        color: #000;
        }

        .directory-list .folder,
        .directory-list .folder > a {
        color: #000;
        font-weight: bold;
        }


        /* The icons
        -------------------------------------------------------------- */

        .directory-list li:before {
        margin-right: 10px;
        content: "";
        height: 20px;
        vertical-align: middle;
        width: 20px;
        background-repeat: no-repeat;
        display: inline-block;
        /* file icon by default */
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><path fill='lightgrey' d='M85.714,42.857V87.5c0,1.487-0.521,2.752-1.562,3.794c-1.042,1.041-2.308,1.562-3.795,1.562H19.643 c-1.488,0-2.753-0.521-3.794-1.562c-1.042-1.042-1.562-2.307-1.562-3.794v-75c0-1.487,0.521-2.752,1.562-3.794 c1.041-1.041,2.306-1.562,3.794-1.562H50V37.5c0,1.488,0.521,2.753,1.562,3.795s2.307,1.562,3.795,1.562H85.714z M85.546,35.714 H57.143V7.311c3.05,0.558,5.505,1.767,7.366,3.627l17.41,17.411C83.78,30.209,84.989,32.665,85.546,35.714z' /></svg>");
        background-position: center 2px;
        background-size: 60% auto;
        }

        .directory-list li.folder:before {
        /* folder icon if folder class is specified */
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><path fill='lightblue' d='M96.429,37.5v39.286c0,3.423-1.228,6.361-3.684,8.817c-2.455,2.455-5.395,3.683-8.816,3.683H16.071 c-3.423,0-6.362-1.228-8.817-3.683c-2.456-2.456-3.683-5.395-3.683-8.817V23.214c0-3.422,1.228-6.362,3.683-8.817 c2.455-2.456,5.394-3.683,8.817-3.683h17.857c3.422,0,6.362,1.228,8.817,3.683c2.455,2.455,3.683,5.395,3.683,8.817V25h37.5 c3.422,0,6.361,1.228,8.816,3.683C95.201,31.138,96.429,34.078,96.429,37.5z' /></svg>");
        background-position: center top;
        background-size: 75% auto;
        }
    </style>
@endpush
