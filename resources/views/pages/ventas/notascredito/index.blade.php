@extends('layout.master')

@section('title', 'Gestión de Notas de Credito')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Gestión de Notas de Credito</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            <div class="btn-group" role="group">
                <button id="btnGroupDrop1" type="button" class="btn btn-sm btn-primary btn-icon-text mb-2 mb-md-0 dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-plus"></i> Nueva Nota de Credito
                </button>
                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                  <a class="dropdown-item" href="/ventas/notascredito/selector">Anulación</a>
                </div>
              </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-3">LISTA DE NOTAS DE CREDITO</h6>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <table id="example" class="compact hover order-column row-border" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Folio</th>
                                                <th>Cliente</th>
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

@push('plugin-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment-with-locales.min.js" integrity="sha512-4F1cxYdMiAW98oomSLaygEwmCnIP38pb4Kx70yQYqRwLVCs3DbRumfBq82T08g/4LJ/smbFGFpmeFlQgoDccgg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush

@push('custom-scripts')
    <script>
        var notasCreditoTable = null;
        var currentUserId = {{auth()->user()->id}};

        function vistaPreviaFactura(id){
            location.href = '/api/ventas/notascredito/vistaprevia/' + id;
        }

        notasCreditoTable = new DataTable('#example', {
            responsive: true,
            ajax: '/api/ventas/notascredito',
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
                    data: 'cliente.razon_social',
                    responsivePriority: 2
                },
                {
                    data: 'cliente.rut',
                    responsivePriority: 3
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
                        var estado = row.estado;
                        console.log(estado);

                        var estadoSii = estado.slice(0, 1);
                        var estadoXml = estado.slice(1, 2);
                        var html = '';
                        if(estadoSii == 0){
                            // en proceso
                            html += '<span class="badge bg-warning me-1" title="Documento En Proceso"><span class="mdi mdi-24 mdi-clock-time-eight-outline"></span></span>';
                        }else if(estadoSii == 1){
                            // aceptado
                            html += '<span class="badge bg-success me-1" title="Documento Aceptado"><span class="mdi mdi-24 mdi-check-circle-outline"></span></span>';
                        }else if(estadoSii == 2){
                            // rechazado
                            html += '<span class="badge bg-danger me-1" title="Documento Rechazado"><span class="mdi mdi-24 mdi-alert-circle-outline"></span></span>';
                        }else if(estadoSii == 3){
                            // anulado
                            html += '<span class="badge bg-danger me-1" title="Documento Anulado"><span class="mdi mdi-24 mdi-close-circle"></span></span>';
                        }

                        if(estadoXml == 0){
                            html += '<span class="badge bg-warning" title="XML No Enviado"><span class="mdi mdi-24 mdi mdi-send-clock"></span></span>';
                        }else if(estadoXml == 1){
                            html += '<span class="badge bg-success" title="XML Recibido"><span class="mdi mdi-24 mdi-send"></span></span>';
                        }else if(estadoXml == 2){
                            html += '<span class="badge bg-error" title="Error de Envio"><span class="mdi mdi-24 mdi-alert-circle-outline"></span></span>';
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
                            html += '<button type="button" title="Ver Factura" onclick="vistaPreviaFactura('+row.folio+')" class="btn btn-outline-primary btnxs px-1 py-0 ms-1"><i class="mdi mdi-18 mdi-magnify"></i></button>';
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
