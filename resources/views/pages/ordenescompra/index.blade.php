@extends('layout.master')

@section('title', 'Gestión de Ordenes de Compra')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Gestión de Ordenes de Compra</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            <button type="button" class="btn btn-sm btn-primary btn-icon-text mb-2 mb-md-0"
                onclick="location.href = '/compras/ordenescompra/nuevo';">
                <div>
                    <i class="mdi mdi-plus"></i> Nueva Orden de Compra
                </div>
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-3">LISTA DE ORDENES DE COMPRA</h6>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <table id="example" class="compact hover order-column row-border" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Folio</th>
                                                <th>Proveedor</th>
                                                <th>RUT</th>
                                                <th>Fecha</th>
                                                <th>Monto Total</th>
                                                <th>Estado</th>
                                                <th></th>
                                            </tr>
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
@endsection

@push('custom-scripts')
    <script>
        var ocsTable = null;
        var currentUserId = {{auth()->user()->id}};

        function editProveedor(id){
            location.href = '/proveedores/editar/' + id
        }

        function vistaPreviaOC(id){
            location.href = '/api/compras/ordenescompra/vistaprevia/' + id;
        }

        ocsTable = new DataTable('#example', {
            responsive: true,
            ajax: '/api/compras/ordenescompra',
            search: {
                return: true
            },
            language: {
                url: '/assets/js/datatables/es-ES.json',
            },
            order: [[0, 'desc']],
            columns: [
                {
                    data: 'folio',
                    responsivePriority: 1
                },
                {
                    data: 'proveedor.razon_social',
                    responsivePriority: 2
                },
                {
                    data: 'proveedor.rut',
                    responsivePriority: 3
                },
                {
                    data: 'fecha_emision',
                    responsivePriority: 3
                },
                {
                    data: 'monto_total',
                    responsivePriority: 3,
                    render: function(data,type,row){
                        return '$' + row.monto_total.toFixed().replace(/(\d)(?=(\d{3})+(,|$))/g, '$1.');
                    }
                },
                {
                    data: 'estado',
                    responsivePriority: 3,
                    render: function(data, type, row) {
                        var html = '';
                        if(row.estado == 0){
                            html = '<span class="badge bg-warning">EN PROCESO</span>';
                        }else if(row.estado == 1){
                            html = '<span class="badge bg-warning">ENVIADA</span>';
                        }else if(row.estado == 2){
                            html = '<span class="badge bg-warning">ACEPTADA</span>';
                        }
                        return html;
                    }
                },
                {
                    data: null,
                    orderable:false,
                    render: function(data, type, row) {
                        var html = '<button type="button" title="Ver Orden de Compra" onclick="vistaPreviaOC('+row.folio+')" class="btn btn-outline-primary btnxs px-1 py-0"><i class="mdi mdi-18 mdi-magnify"></i></button>';
                        return html;
                    }
                },
            ],
            processing: true,
            serverSide: true
        });
    </script>
@endpush
