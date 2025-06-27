@extends('layout.master')

@section('title', 'Gestión de Cesión de Documentos')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Gestión de Cesión de Documentos</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            <button type="button" class="btn btn-sm btn-primary btn-icon-text mb-2 mb-md-0"
                onclick="openModalCesion()">
                <div>
                    <i class="mdi mdi-plus"></i> Nueva Cesión
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
                                <h6 class="card-title mb-3">LISTA DE CESIONES</h6>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <table id="example" class="compact hover order-column row-border" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Cliente</th>
                                                <th>Fecha</th>
                                                <th>Factoring</th>
                                                <th>Monto</th>
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
    <div class="modal fade" id="modalCesion" tabindex="-1" aria-labelledby="modalCesionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCesionLabel">Nueva Cesión de Documentos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info" role="alert"> 
                        <h5 class="alert-heading">Información</h5>
                        <p>
                            Seleccione el cliente y el factoring para crear una nueva cesión de documentos.
                        </p>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <form id="formCesion" method="post" action="/ventas/cesiones/selector">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="cliente">Cliente</label>
                                            <select class="form-control" id="cliente" name="cliente">
                                                <option value="">Seleccione un cliente</option>
                                                @foreach ($clientes as $cliente)
                                                    <option value="{{ $cliente->id }}">{{ $cliente->razon_social }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="factoring">Factoring</label>
                                            <select class="form-control" id="factoring" name="factoring">
                                                <option value="">Seleccione un factoring</option>
                                                @foreach ($factoring as $factoring)
                                                    <option value="{{ $factoring->id }}">{{ $factoring->razon_social }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="fecha">Fecha</label>
                                            <input type="date" class="form-control" value="{{ date('Y-m-d') }}" id="fecha" name="fecha">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                                <button type="button" class="btn btn-primary" onclick="seleccionarDocumentos()">Seleccionar Documentos</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment-with-locales.min.js" integrity="sha512-4F1cxYdMiAW98oomSLaygEwmCnIP38pb4Kx70yQYqRwLVCs3DbRumfBq82T08g/4LJ/smbFGFpmeFlQgoDccgg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush


@push('custom-scripts')
    <script>
        var cesionesTable = null;
        var currentUserId = {{auth()->user()->id}};

        $('#factoring').select2({
            dropdownParent: $('#modalCesion'),
            width: '100%',
            placeholder: 'Seleccione un factoring',
        });
        $('#cliente').select2({
            dropdownParent: $('#modalCesion'),
            width: '100%',
            placeholder: 'Seleccione un cliente',
        });

        cesionesTable = new DataTable('#example', {
            responsive: true,
            ajax: '/api/ventas/cesiones',
            search: {
                return: true
            },
            language: {
                url: '/assets/js/datatables/es-ES.json',
            },
            order: [[0, 'desc']],
            columns: [
                {
                    data: 'id',
                    responsivePriority: 1,
                    render: function(data, type, row) {
                        return row.id.toString().padStart(5, '0');    
                    }
                },
                {
                    data: 'cliente.razon_social',
                    responsivePriority: 1
                },
                {
                    data: 'fecha_cesion',
                    responsivePriority: 2
                },
                {
                    data: 'factoring.razon_social',
                    responsivePriority: 3
                },
                {
                    data: 'monto_cesion',
                    responsivePriority: 3,
                    render: function(data, type, row) {
                        return '$' + data.toFixed().replace(/(\d)(?=(\d{3})+(,|$))/g, '$1.');
                    }
                },
                {
                    data: 'estado',
                    responsivePriority: 3,
                    render: function(data, type, row) {
                        var estado = row.estado;
                        if(estado == 0){
                            return '<span class="badge bg-warning me-1" title="Cesión En Proceso"><span class="mdi mdi-24 mdi-clock-time-eight-outline"></span></span>';
                        }else if(estado == 1){
                            return '<span class="badge bg-success me-1" title="Cesión Aceptada"><span class="mdi mdi-24 mdi-check-circle-outline"></span></span>';
                        }else if(estado == 2){
                            return '<span class="badge bg-danger me-1" title="Cesión con Rechazos"><span class="mdi mdi-24 mdi-alert-circle-outline"></span></span>';
                        }
                        return estado;
                    }
                },
                {
                    data: null,
                    orderable:false,
                    render: function(data, type, row) {
                        var html = '';  
                            html = '<div>';
                            html += '<a href="/ventas/cesiones/detalle/'+row.id+'" class="btn btn-outline-primary btnxs px-1 py-0 ms-1"><i class="mdi mdi-18 mdi-magnify"></i></a>';
                            html += '</div>';
                        return html;
                    }
                },
            ],
            processing: true,
            serverSide: true
        });

        function openModalCesion(){
            $('#modalCesion').modal('show');
        }

        function seleccionarDocumentos(){
            var cliente = $('#cliente').val();
            var factoring = $('#factoring').val();
            var fecha = $('#fecha').val();

            if(cliente == '' || factoring == '' || fecha == ''){
                alert('Debe seleccionar un cliente, un factoring y una fecha');
                return;
            }

            $('#formCesion').submit();
        }
    </script>   
@endpush
