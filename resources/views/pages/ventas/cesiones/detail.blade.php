@extends('layout.master')

@section('title', 'Detalle de Cesión de Documentos')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Detalle de Cesión de Documentos</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                <div class="d-flex justify-content-between align-items-baseline mb-4">
                                    <h6 class="card-title">INFORMACIÓN DE LA CESIÓN</h6>
                                    <a href="/ventas/cesiones" class="btn btn-danger btn-sm">
                                        <i class="mdi mdi-arrow-left"></i> Volver
                                    </a>
                                </div>

                                <!-- Estado de la cesión -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="alert alert-info" role="alert">
                                            <h6 class="alert-heading">Estado de la Cesión</h6>
                                            @if($cesion->estado == 0)
                                                <p class="mb-0">
                                                    <span class="badge bg-warning me-2">
                                                        <i class="mdi mdi-clock-time-eight-outline"></i> En Proceso
                                                    </span>
                                                    La cesión está siendo procesada.
                                                </p>
                                            @elseif($cesion->estado == 1)
                                                <p class="mb-0">
                                                    <span class="badge bg-success me-2">
                                                        <i class="mdi mdi-check-circle-outline"></i> Aceptada
                                                    </span>
                                                    La cesión ha sido aceptada exitosamente.
                                                </p>
                                            @elseif($cesion->estado == 2)
                                                <p class="mb-0">
                                                    <span class="badge bg-danger me-2">
                                                        <i class="mdi mdi-alert-circle-outline"></i> Con Rechazos
                                                    </span>
                                                    La cesión presenta algunos rechazos.
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Información de la cesión -->
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="card-title text-muted fw-bold">Cliente</h6>
                                                <p class="card-text">{{ $cesion->cliente->razon_social }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="card-title text-muted fw-bold">Factoring</h6>
                                                <p class="card-text">{{ $cesion->factoring->razon_social }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="card-title text-muted fw-bold">Fecha de Cesión</h6>
                                                <p class="card-text">{{ date('d/m/Y', strtotime($cesion->fecha_cesion)) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="card-title text-muted fw-bold">Monto Total</h6>
                                                <p class="card-text">$ {{ number_format($cesion->monto_cesion, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                

                                <!-- Tabla de documentos -->
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="card-title mb-3">DOCUMENTOS ASOCIADOS</h6>
                                        <div class="table-responsive">
                                            <table id="documentosTable" class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Folio</th>
                                                        <th>Cliente</th>
                                                        <th>RUT</th>
                                                        <th>Fecha Emisión</th>
                                                        <th>Monto</th>
                                                        <th>Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($cesion->aecs as $aec)
                                                    <tr>
                                                        <td>{{ $aec->factura->folio }}</td>
                                                        <td>{{ $aec->factura->cliente->razon_social }}</td>
                                                        <td>{{ $aec->factura->cliente->rut }}</td>
                                                        <td>{{ date('d/m/Y', strtotime($aec->factura->fecha_emision)) }}</td>
                                                        <td>$ {{ number_format($aec->factura->monto_total, 0, ',', '.') }}</td>
                                                        <td>
                                                            @if($aec->estado == 0)
                                                                <span class="badge bg-warning">
                                                                    <i class="mdi mdi-clock-time-eight-outline"></i> Pendiente
                                                                </span>
                                                            @elseif($aec->estado == 1)
                                                                <span class="badge bg-success">
                                                                    <i class="mdi mdi-check-circle-outline"></i> Aceptado
                                                                </span>
                                                            @elseif($aec->estado == 2)
                                                                <span class="badge bg-danger">
                                                                    <i class="mdi mdi-alert-circle-outline"></i> Rechazado
                                                                </span>
                                                            @endif
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
            </div>
        </div>
    </div>
@endsection

@push('plugin-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment-with-locales.min.js" integrity="sha512-4F1cxYdMiAW98oomSLaygEwmCnIP38pb4Kx70yQYqRwLVCs3DbRumfBq82T08g/4LJ/smbFGFpmeFlQgoDccgg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush

@push('custom-scripts')
    <script>
        
    </script>
@endpush
