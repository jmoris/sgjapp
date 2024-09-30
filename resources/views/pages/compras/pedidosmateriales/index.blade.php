@extends('layout.master')

@section('title', 'Gestión de Ordenes de Compra')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Gestión de Pedidos de Materiales</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            <button type="button" class="btn btn-sm btn-primary btn-icon-text mb-2 mb-md-0"
                onclick="location.href = '/compras/pedidosmateriales/nuevo';"
                @if(!has_permission('crear-orden-compra')) disabled @endif>
                <div>
                    <i class="mdi mdi-plus"></i> Nuevo Pedido de Material
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
                                <h6 class="card-title mb-3">LISTA DE PEDIDOS DE MATERIALES</h6>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <table id="example" class="compact hover order-column row-border" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Folio</th>
                                                <th>Mandante</th>
                                                <th>Fecha</th>
                                                <th>Peso Total</th>
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
@push('plugin-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment-with-locales.min.js" integrity="sha512-4F1cxYdMiAW98oomSLaygEwmCnIP38pb4Kx70yQYqRwLVCs3DbRumfBq82T08g/4LJ/smbFGFpmeFlQgoDccgg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush
@push('custom-scripts')
    <script>
        var ocsTable = null;
        var currentUserId = {{auth()->user()->id}};

        function editarPedido(id){
            location.href = '/compras/pedidosmateriales/editar/' + id
        }

        function vistaPreviaPedido(id, rev){
            window.open('/api/reportes/excel/pedidomaterial/' + id);
        }

        ocsTable = new DataTable('#example', {
            responsive: true,
            ajax: '/api/compras/pedidosmateriales',
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
                    data: 'mandante.razon_social',
                    responsivePriority: 2
                },
                {
                    data: 'fecha_emision',
                    responsivePriority: 3,
                    render: function(data,type,row){
                        var fecha = moment(row.fecha_emision,'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY');
                        return fecha;
                    }
                },
                {
                    data: 'peso_total',
                    responsivePriority: 3,
                    render: function(data,type,row){
                        return row.peso_total.toFixed().replace(/(\d)(?=(\d{3})+(,|$))/g, '$1.') + ' Kgs';
                    }
                },
                {
                    data: 'estado',
                    responsivePriority: 3,
                    render: function(data, type, row) {
                        var html = '';
                        if(row.estado == -1){
                            html = '<span class="badge bg-danger">ANULADA</span>';
                        }else if(row.estado == 0){
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
                        var html = '';
                        if(row.estado != -1){
                            html = '<div>';
                            @if(has_permission('editar-orden-compra'))
                            html += '<button type="button" title="Editar Pedido Material" onclick="editarPedido('+row.id+')" class="btn btn-outline-primary btnxs px-1 py-0"><i class="mdi mdi-18 mdi-pencil"></i></button>';
                            @endif
                            html += '<button type="button" title="Ver Pedido Material" onclick="vistaPreviaPedido('+row.id+')" class="btn btn-outline-primary btnxs px-1 py-0 ms-1"><i class="mdi mdi-18 mdi-magnify"></i></button>';
                            html += '</div>';
                        }
                        //var html = '';
                        return html;
                    }
                },
            ],
            processing: true,
            serverSide: true
        });
    </script>
@endpush
