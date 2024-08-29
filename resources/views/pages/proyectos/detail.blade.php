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
                                            <label class="form-label">Nombre</label>
                                            <input type="text" name="nombre" id="nombre" class="form-control"
                                                placeholder="Ingrese el nombre del proyecto" value="{{ $proyecto->nombre }}"
                                                disabled>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Total Acumulado OC:</label>
                                            <input type="text" name="total" id="total" class="form-control"
                                                value="$ {{ number_format($total, 0, ',', '.') }}" disabled>
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

@endsection

@push('plugin-scripts')
@endpush

@push('custom-scripts')
    <script>

        function editProyecto(){
            console.log($('#editText').text());
            if($('#editText').text() == 'Guardar'){
                // aqui post a guardar proyecto
                $.ajax({
                    type: "POST",
                    url: '/api/ventas/proyectos/editar/{{$proyecto->id}}',
                    data: {nombre:$('#nombre').val()}, // serializes the form's elements.
                    success: function(data){
                        if(!data.success){
                            console.log(data.msg);
                            alert(data.msg);
                        }else{
                            $('#editText').text('Editar');
                            $('#nombre').attr('disabled', true);
                        }
                    }
                });
            }else{
                $('#nombre').removeAttr('disabled');
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

        $('#tablaGuias').DataTable({
            layout: {
                topStart: {
                    buttons: [{
                        text: 'Excel Resumen',
                        action: function(e, dt, node, config) {
                            location.href = "/api/reportes/excel/proyecto/52/{{ $proyecto->id }}/0"
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
        function vistaPreviaGuias(id) {
            window.open('/api/ventas/guiasdespacho/vistaprevia/' + id);
        }
        function vistaPreviaNC(id) {
            window.open('/api/ventas/notascredito/vistaprevia/' + id);
        }
    </script>
@endpush
