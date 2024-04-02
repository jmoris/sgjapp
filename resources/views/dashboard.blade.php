@extends('layout.master')

@section('title', 'Tablero de Estadisticas')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Tablero de Estadisticas</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">NUEVOS CLIENTES</h6>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12 col-xl-5">
                                    <h3 class="mb-2">1</h3>
                                    <div class="d-flex align-items-baseline">
                                        <p class="text-success">
                                            <span>0%</span>
                                            <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-6 col-md-12 col-xl-7">
                                    <div id="customersChart" class="mt-md-3 mt-xl-0"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">NUEVOS PROVEEDORES</h6>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12 col-xl-5">
                                    <h3 class="mb-2">1</h3>
                                    <div class="d-flex align-items-baseline">
                                        <p class="text-success">
                                            <span>0%</span>
                                            <i data-feather="arrow-up" class="icon-sm mb-1"></i>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-6 col-md-12 col-xl-7">
                                    <div id="customersChart" class="mt-md-3 mt-xl-0"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">NUEVAS O/C</h6>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12 col-xl-5">
                                    <h3 class="mb-2">1</h3>
                                    <div class="d-flex align-items-baseline">
                                        <p class="text-danger">
                                            <span>0%</span>
                                            <i data-feather="arrow-down" class="icon-sm mb-1"></i>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-6 col-md-12 col-xl-7">
                                    <div id="ordersChart" class="mt-md-3 mt-xl-0"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- row -->
    <div class="row">
        <div class="col-lg-12 col-xl-12 stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-baseline mb-2">
                <h6 class="card-title mb-0">Ultimos Proyectos</h6>
                <div class="dropdown mb-2">
                  <button class="btn btn-link p-0" type="button" id="dropdownMenuButton7" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                    <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="eye" class="icon-sm me-2"></i> <span class="">View</span></a>
                    <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="edit-2" class="icon-sm me-2"></i> <span class="">Edit</span></a>
                    <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="trash" class="icon-sm me-2"></i> <span class="">Delete</span></a>
                    <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="printer" class="icon-sm me-2"></i> <span class="">Print</span></a>
                    <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="download" class="icon-sm me-2"></i> <span class="">Download</span></a>
                  </div>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead>
                    <tr>
                      <th class="pt-0">#</th>
                      <th class="pt-0">Nombre del Proyecto</th>
                      <th class="pt-0">Fecha de Inicio</th>
                      <th class="pt-0">Fecha de Termino</th>
                      <th class="pt-0">Estado</th>
                      <th class="pt-0">Asignado</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>1</td>
                      <td>Proyecto Cancha</td>
                      <td>01/01/2023</td>
                      <td>26/04/2023</td>
                      <td><span class="badge bg-success">ENTREGADO</span></td>
                      <td>Juan Perez</td>
                    </tr>
                    <tr>
                      <td>2</td>
                      <td>Proyecto Galpon</td>
                      <td>01/01/2023</td>
                      <td>26/04/2023</td>
                      <td><span class="badge bg-success">ENTREGADO</span></td>
                      <td>Fernando Perez</td>
                    </tr>
                    <tr>
                      <td>3</td>
                      <td>Proyecto Bodegas</td>
                      <td>01/05/2023</td>
                      <td>10/09/2023</td>
                      <td><span class="badge bg-info">PENDIENTE PAGO</span></td>
                      <td>Sofia Perez</td>
                    </tr>
                    <tr>
                      <td>4</td>
                      <td>Proyecto Estacionamientos</td>
                      <td>01/01/2023</td>
                      <td>31/11/2023</td>
                      <td><span class="badge bg-warning">EN CONSTRUCCION</span>
                      </td>
                      <td>Fernando Perez</td>
                    </tr>
                    <tr>
                      <td>5</td>
                      <td>Proyecto Galpon 2</td>
                      <td>01/01/2023</td>
                      <td>31/12/2023</td>
                      <td><span class="badge bg-warning">EN CONSTRUCCION</span></td>
                      <td>Juan Perez</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div> <!-- row -->

@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/apexcharts/apexcharts.min.js') }}"></script>
@endpush

@push('custom-scripts')
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
@endpush
