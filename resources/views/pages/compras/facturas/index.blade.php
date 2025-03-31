@extends('layout.master')

@section('title', 'Gestión de Facturas')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Gestión de Facturas de Compra</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-3">LISTA DE FACTURAS DE COMPRA</h6>
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
                                            <label class="form-label">Estado Proyecto</label>
                                            <select class="form-control form-control-sm" id="estado">
                                                <option value="0">Seleccione proyecto</option>
                                                <option value="1">Asignado</option>
                                                <option value="2">Sin asignar</option>
                                            </select>
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
                                                <th>Emisor</th>
                                                <th>RUT</th>
                                                <th>Proyecto</th>
                                                <th>Fecha</th>
                                                <th>Monto Total</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>

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
        var fecha_em_inicial, fecha_em_final;
        var filtro = {
            razon_social: '',
            rut: '',
            folio: '',
            fecha_emision: {
                min: '',
                max: ''
            },
            tipo_traslado: '',
            tipo_despacho: '',
            proyecto: null
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

            $('#estado').on('change', function() {
                filtro.proyecto = this.value;
                /*if(this.value == 2){
                    facturasTable.column(3).search('^$',false,true).draw()
                }else if(this.value == 1){
                    facturasTable.column(3).search(this.value).draw();
                }else if(this.value == 0){
                    facturasTable.column(3).search("").draw();
                }*/
                $('#tabla').DataTable().destroy();
                cargarDocumentos('', '', '', '');
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

        function verDocumento(emisor, folio) {
            location.href = `/compras/facturas/detalle/${emisor}/${folio}`;
        }

        function vistaPreviaDocumento(emisor, folio) {
            window.open(`/api/compras/facturas/vistaprevia/${emisor}/33/${folio}/?descargar=1`);
        }

        function cargarDocumentos(feMin = '', feMax = '', fvMin = '', fvMax = '') {
            facturasTable = new DataTable('#tabla', {
                layout: {
                    topEnd: null
                },
                responsive: true,
                ajax: {
                    url: '/api/compras/facturas',
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
                        d.proyecto = this.filtro.proyecto;
                    }
                },
                search: {
                    return: true
                },
                language: {
                    url: '/assets/js/datatables/es-ES.json',
                },
                order: [
                    [4, 'desc']
                ],
                columns: [
                    {
                        data: 'folio',
                        responsivePriority: 1
                    },
                    {
                        data: 'razon_social_emisor',
                        responsivePriority: 2
                    },
                    {
                        data: 'rut_emisor',
                        responsivePriority: 3
                    },
                    {
                        data: 'proyecto_id',
                        render: function(data, type, row) {
                            if(row.proyecto_id == null)
                                return 'No asignado';
                            else
                                return row.proyecto.nombre;
                        }
                    },
                    {
                        data: 'fecha_emision',
                        responsivePriority: 3,
                        render: function(data, type, row) {
                            var fecha = moment(row.fecha_emision, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY');
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
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            var html = '';
                            html = '<div>';
                            if(row.tiene_xml){
                                html += '<button type="button" title="Ver Factura" onclick="verDocumento(\'' +
                                row.rut_emisor + '\',' + row.folio +
                                ')" class="btn btn-outline-primary btnxs px-1 py-0 ms-1"><i class="mdi mdi-18 mdi-text-box-search-outline"></i></button>';
                                html += '<button type="button" title="Descargar PDF Factura" onclick="vistaPreviaDocumento(\'' +
                                row.rut_emisor + '\',' + row.folio +
                                ')" class="btn btn-outline-primary btnxs px-1 py-0 ms-1"><i class="mdi mdi-18 mdi-download"></i></button>';
                            }else{
                                html += '<button type="button" title="Documento XML no disponible" class="btn btn-outline-secondary btnxs px-1 py-0 ms-1"><i class="mdi mdi-18 mdi-text-box-search-outline"></i></button>';
                            }
                            html += '</div>';
                            return html;
                        }
                    },
                ],
                processing: true,
                serverSide: true
            });
        }

        facturasTable = new DataTable('#example', {
            responsive: true,
            ajax: '/api/compras/facturas',
            search: {
                return: true
            },
            language: {
                url: '/assets/js/datatables/es-ES.json',
            },
            order: [
                [3, 'desc']
            ],
            columns: [{
                    data: 'folio',
                    responsivePriority: 1
                },
                {
                    data: 'razon_social_emisor',
                    responsivePriority: 2
                },
                {
                    data: 'rut_emisor',
                    responsivePriority: 3
                },
                {
                    data: 'proyecto_id',
                    render: function(data, type, row) {
                        if(row.proyecto_id == null)
                            return 'No asignado';
                        else
                            return row.proyecto.nombre;
                    }
                },
                {
                    data: 'fecha_emision',
                    responsivePriority: 3,
                    render: function(data, type, row) {
                        var fecha = moment(row.fecha_emision, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY');
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
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        var html = '';
                        html = '<div>';
                        if(row.tiene_xml){
                            html += '<button type="button" title="Ver Factura" onclick="verDocumento(\'' +
                            row.rut_emisor + '\',' + row.folio +
                            ')" class="btn btn-outline-primary btnxs px-1 py-0 ms-1"><i class="mdi mdi-18 mdi-text-box-search-outline"></i></button>';
                        }else{
                            html += '<button type="button" title="Documento XML no disponible" class="btn btn-outline-secondary btnxs px-1 py-0 ms-1"><i class="mdi mdi-18 mdi-text-box-search-outline"></i></button>';
                        }
                        html += '</div>';
                        return html;
                    }
                },
            ],
            processing: true,
            serverSide: true
        });


        // Custom filtering function which will search data in column four between two values
        DataTable.ext.search.push(function(settings, data, dataIndex) {
            let min = minDate.val();
            let max = maxDate.val();
            let date = new Date(data[4]);

            if (
                (min === null && max === null) ||
                (min === null && date <= max) ||
                (min <= date && max === null) ||
                (min <= date && date <= max)
            ) {
                return true;
            }
            return false;
        });

        // Create date inputs
        minDate = new DateTime('#min', {
            format: 'DD/MM/YYYY'
        });
        maxDate = new DateTime('#max', {
            format: 'DD/MM/YYYY'
        });

        $('#min').change(function(e) {
            facturasTable.draw();
            $('#max').focus();
            $('#max').click();
        });
        $('#max').change(function(e) {
            facturasTable.draw();
        });
    </script>
@endpush
