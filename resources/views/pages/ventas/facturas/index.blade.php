@extends('layout.master')

@section('title', 'Gestión de Facturas')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Gestión de Facturas</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            <button type="button" class="btn btn-sm btn-primary btn-icon-text mb-2 mb-md-0"
                onclick="location.href = '/ventas/facturas/nuevo';">
                <div>
                    <i class="mdi mdi-plus"></i> Nueva Factura
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
                                <h6 class="card-title mb-3">LISTA DE FACTURAS</h6>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="row mx-5">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">RUT</label>
                                            <input type="text" id="rut" class="form-control form-control-sm"
                                                placeholder="Ingrese un RUT para filtrar">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Razón social</label>
                                            <input type="text" id="razonsocial" class="form-control form-control-sm"
                                                placeholder="Ingrese una Razón social para filtrar">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Folio</label>
                                            <input type="text" id="folio" class="form-control form-control-sm"
                                                placeholder="Ingrese el/los folio(s) a filtrar">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mx-5">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Fecha emisión</label>
                                            <input type="text" id="fecha_emision" class="form-control form-control-sm"
                                                placeholder="Enter first name">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Fecha vencimiento</label>
                                            <input type="text" id="fecha_vencimiento"
                                                class="form-control form-control-sm" placeholder="Enter first name">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <table id="tabla" class="compact hover order-column row-border" style="width:100%">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"
        integrity="sha512-efAcjYoYT0sXxQRtxGY37CKYmqsFVOIwMApaEbrxJr4RwqVVGw8o+Lfh/+59TU07+suZn1BWq4fDl5fdgyCNkw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment-with-locales.min.js"
        integrity="sha512-4F1cxYdMiAW98oomSLaygEwmCnIP38pb4Kx70yQYqRwLVCs3DbRumfBq82T08g/4LJ/smbFGFpmeFlQgoDccgg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('custom-scripts')
    <script>
        var facturasTable = null;
        var currentUserId = {{ auth()->user()->id }};
        var fecha_em_inicial, fecha_em_final, fecha_ve_inicial, fecha_ve_final;
        var filtro = {
            razon_social: '',
            rut: '',
            folio: '',
            fecha_emision: {
                min: '',
                max: ''
            },
            fecha_vencimiento: {
                min: '',
                max: ''
            }
        };

        $(document).ready(function() {

            moment.locale('es');
            cargarDocumentos();

            $('#rut').inputmask({
                mask: '99.999.999-[9|K]',
                definitions: {
                    'K': {
                        validator: "(k|K)",
                        casing: "upper"
                    }
                }
            });

            $('#fecha_emision').daterangepicker({
                locale: {
                    applyLabel: 'Aplicar',
                    cancelLabel: 'Cancelar'
                },
            }, function(start, end, label) {
                fecha_em_inicial = moment(start).format('YYYY-MM-DD');
                fecha_em_final = moment(end).format('YYYY-MM-DD');
                filtro.fecha_emision.min = moment(start).format('YYYY-MM-DD');
                filtro.fecha_emision.max = moment(end).format('YYYY-MM-DD');
            });
            $('#fecha_vencimiento').daterangepicker({
                locale: {
                    applyLabel: 'Aplicar',
                    cancelLabel: 'Cancelar'
                },
            }, function(start, end, label) {
                fecha_ve_inicial = moment(start).format('YYYY-MM-DD');
                fecha_ve_final = moment(end).format('YYYY-MM-DD');
                filtro.fecha_vencimiento.min = moment(start).format('YYYY-MM-DD');
                filtro.fecha_vencimiento.max = moment(end).format('YYYY-MM-DD');
            });

            $('#fecha_emision').on('cancel.daterangepicker', function(ev, picker) {
                fecha_em_inicial = undefined;
                fecha_em_final = undefined;
                $('#tabla').DataTable().destroy();
                cargarDocumentos();
            });

            $('#fecha_vencimiento').on('cancel.daterangepicker', function(ev, picker) {
                fecha_ve_inicial = undefined;
                fecha_ve_final = undefined;
                $('#tabla').DataTable().destroy();
                cargarDocumentos();
            });


            $('#folio').on('change', function() {
                filtro.folio = this.value;
                $('#tabla').DataTable().column(0).search(filtro.folio).draw();
            });

            $('#razonsocial').on('change', function() {
                filtro.razon_social = this.value;
                $('#tabla').DataTable().column(1).search(filtro.razon_social).draw();
            });

            $('#rut').on('change', function() {
                var rut = $(this).inputmask('unmaskedvalue');
                var dv = ''
                var rutCompleto = '';
                if (rut != '') {
                    var dv = rut[rut.length - 1];
                    var rutCompleto = rut.slice(0, -1) + '-' + dv;
                }
                filtro.rut = rutCompleto;
                $('#tabla').DataTable().column(2).search(filtro.rut).draw();
            });

            $('#fecha_emision').on('change', function() {
                $('#tabla').DataTable().destroy();
                cargarDocumentos(fecha_em_inicial, fecha_em_final, '', '');
            });

            $('#fecha_vencimiento').on('change', function() {
                $('#tabla').DataTable().destroy();
                cargarDocumentos('', '', fecha_ve_inicial, fecha_ve_final);
            });

        });

        function vistaPreviaFactura(folio) {
            window.open('/api/ventas/facturas/vistaprevia/' + folio + '/?descargar=1');
        }

        function verFactura(folio) {
            location.href = `/ventas/facturas/detalle/${folio}`;
        }

        function descargarXML(id){
            window.open('/api/ventas/facturas/descargar/' + id)
        }

        function cargarDocumentos(feMin = '', feMax = '', fvMin = '', fvMax = '') {
            facturasTable = new DataTable('#tabla', {
                layout: {
                    topEnd: null
                },
                responsive: true,
                ajax: {
                    url: '/api/ventas/facturas',
                    data: function(d) {
                        d.ffecha = (feMin == '' && feMax == '') ? false : true;
                        if (feMin != '' && feMax != '') {
                            d.feMinDate = feMin;
                            d.feMaxDate = feMax;
                        }
                        if (fvMin != '' && fvMax != '') {
                            d.fvMinDate = fvMin;
                            d.fvMaxDate = fvMax;
                        }
                    }
                },
                search: {
                    return: true
                },
                language: {
                    url: '/assets/js/datatables/es-ES.json',
                },
                order: [
                    [0, 'desc']
                ],
                columns: [{
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
                        render: function(data, type, row) {
                            var fecha = moment(row.fecha_emision, 'YYYY-MM-DD HH:mm:ss').format(
                                'DD/MM/YYYY');
                            return fecha;
                        }
                    },
                    {
                        data: 'monto_total',
                        responsivePriority: 3,
                        render: function(data, type, row) {
                            return '$' + row.monto_total.toFixed().replace(/(\d)(?=(\d{3})+(,|$))/g, '$1.');
                        }
                    },
                    {
                        data: 'estado',
                        responsivePriority: 3,
                        render: function(data, type, row) {
                            var estado = row.estado;
                            var estadoSii = estado.slice(0, 1);
                            var estadoXml = estado.slice(1, 2);
                            var estadoCesion = estado.slice(2, 3);
                            var html = '';
                            if (estadoSii == 0) {
                                // en proceso
                                html +=
                                    '<span class="badge bg-warning me-1" title="Documento En Proceso"><span class="mdi mdi-24 mdi-clock-time-eight-outline"></span></span>';
                            } else if (estadoSii == 1) {
                                // aceptado
                                html +=
                                    '<span class="badge bg-success me-1" title="Documento Aceptado"><span class="mdi mdi-24 mdi-check-circle-outline"></span></span>';
                            } else if (estadoSii == 2) {
                                // rechazado
                                html +=
                                    '<span class="badge bg-danger me-1" title="Documento Rechazado"><span class="mdi mdi-24 mdi-alert-circle-outline"></span></span>';
                            } else if (estadoSii == 3) {
                                // anulado
                                html +=
                                    '<span class="badge bg-danger me-1" title="Documento Anulado"><span class="mdi mdi-24 mdi-close-circle"></span></span>';
                            }

                            if (estadoXml == 0) {
                                html +=
                                    '<span class="badge bg-warning me-1" title="XML No Enviado"><span class="mdi mdi-24 mdi mdi-send-clock"></span></span>';
                            } else if (estadoXml == 1) {
                                html +=
                                    '<span class="badge bg-success me-1" title="XML Recibido"><span class="mdi mdi-24 mdi-send"></span></span>';
                            } else if (estadoXml == 2) {
                                html +=
                                    '<span class="badge bg-error me-1" title="Error de Envio"><span class="mdi mdi-24 mdi-alert-circle-outline"></span></span>';
                            }

                            if (estadoCesion == 1) {
                                html +=
                                    '<span class="badge bg-success" title="Cedida"><span class="mdi mdi-24 mdi-cash-multiple"></span></span>';
                            }

                            return html;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            var html = '';
                            html = '<div>';
                                html += '<button type="button" title="Ver Detalle" onclick="verFactura(\'' + row.folio +
                                '\')" class="btn btn-outline-primary btnxs px-1 py-0 ms-1"><i class="mdi mdi-18 mdi-text-box-search-outline"></i></button>';
                                html += '<button type="button" title="Ver PDF Factura" onclick="vistaPreviaFactura(\'' + row.folio +
                                '\')" class="btn btn-outline-primary btnxs px-1 py-0 ms-1"><i class="mdi mdi-18 mdi-download"></i></button>';
                                html += '</div>';
                            //var html = '';
                            return html;
                        }
                    },
                ],
                processing: true,
                serverSide: true
            });
        }
    </script>
@endpush
