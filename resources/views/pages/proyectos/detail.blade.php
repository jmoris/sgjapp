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
                                        </div>
                                            <div class="mb-3">
                                                <label class="form-label">Nombre</label>
                                                <input type="text" name="nombre" id="nombre" class="form-control"
                                                    placeholder="Ingrese el nombre del proyecto" value="{{ $proyecto->nombre }}" disabled>
                                            </div>
                                    </div>
                                    <div class="col-md-9 border-start">
                                        <div class="d-flex justify-content-between align-items-baseline mb-3">
                                            <h4 class="card-title mb-0">ORDENES DE COMPRA ASOCIADAS</h4>
                                        </div>
                                        <table id="tabla" class="ml-2 table">
                                            <thead>
                                                <th>Folio</th>
                                                <th>Fecha</th>
                                                <th>Razon Social</th>
                                                <th>Monto Total</th>
                                                <th></th>
                                            </thead>
                                            <tbody>
                                                @foreach($ocs as $oc)
                                                <tr>
                                                    <td>{{$oc->folio}}</td>
                                                    <td>{{ date('d/m/Y', strtotime($oc->fecha_emision)) }}</td>
                                                    <td>{{$oc->proveedor->razon_social}}</td>
                                                    <td>$ {{ number_format($oc->monto_total, 0, ',', '.') }}</td>
                                                    <td><button type="button" title="Ver Orden de Compra" onclick="vistaPreviaOC({{$oc->folio}})" class="btn btn-outline-primary btnxs px-1 py-0"><i class="mdi mdi-18 mdi-magnify"></i></button></td>
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

@endsection

@push('plugin-scripts')
@endpush

@push('custom-scripts')
    <script>
        $('#tabla').DataTable({
            lengthMenu: [5, 10, 20, 50],
        });
        function vistaPreviaOC(id){
            location.href = '/api/compras/ordenescompra/vistaprevia/' + id;
        }
    </script>
@endpush
