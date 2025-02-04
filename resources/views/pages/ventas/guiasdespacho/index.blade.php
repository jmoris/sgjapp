@extends('layout.master')

@section('title', 'Gestión de Guias de Despacho')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Gestión de Guias de Despacho</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            <button type="button" class="btn btn-sm btn-primary btn-icon-text mb-2 mb-md-0"
                onclick="location.href = '/ventas/guiasdespacho/nuevo';">
                <div>
                    <i class="mdi mdi-plus"></i> Nueva Guia de Despacho
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
                                <h6 class="card-title mb-3">LISTA DE GUIAS DE DESPACHO</h6>
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
                                            <label class="form-label">Tipo de Traslado</label>
                                            <select id="tipo_traslado" class="form-control">
                                                <option>Seleccione un tipo de traslado</option>
                                                <option value="1">Operación Constituye Venta
                                                </option>
                                                <option value="2">Venta Por efectuar</option>
                                                <option value="3">Consigación</option>
                                                <option value="4">Donación</option>
                                                <option value="5">Traslado Interno</option>
                                                <option value="6">No Constituye Venta</option>
                                                <option value="7">Devolución</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Tipo de Despacho</label>
                                            <select id="tipo_despacho" class="form-control">
                                                <option>Seleccione un tipo de despacho</option>
                                                <option value="1">Comprador</option>
                                                <option value="2">Emisor al Comprador</option>
                                                <option value="3">Emisor a Otro</option>
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
        var guiasTable = null;
        var currentUserId = {{auth()->user()->id}};
        var fecha_em_inicial, fecha_em_final, fecha_ve_inicial, fecha_ve_final;
        var filtro = {
            razon_social: '',
            rut: '',
            folio: '',
            fecha_emision: {
                min: '',
                max: ''
            },
            tipo_traslado: '',
            tipo_despacho: ''
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

            $('#tipo_traslado').on('change', function() {
                filtro.tipo_traslado = this.value;
                $('#tabla').DataTable().destroy();
                cargarDocumentos('', '', filtro.tipo_despacho, filtro.tipo_traslado);
            });

            $('#tipo_despacho').on('change', function() {
                filtro.tipo_despacho = this.value;
                $('#tabla').DataTable().destroy();
                cargarDocumentos('', '', filtro.tipo_despacho, filtro.tipo_traslado);
            });
        });

        function vistaPreviaFactura(id){
            window.open('/api/ventas/guiasdespacho/vistaprevia/' + id);
        }

        function anularGuiaDespacho(id){
            Swal.fire({
                title: "Confirmar anulación de documento",
                text: "La acción que desea realizar es irreversible, ¿desea continuar con la operación?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#6571FF",
                cancelButtonColor: "#FF3366",
                confirmButtonText: "Confirmar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: '/api/ventas/guiasdespacho/anular/' + id,
                        success: function(data){
                            Swal.fire({
                                title: "Documento anulado",
                                text: "El documento seleccionado fue anulado satisfactoriamente",
                                icon: "success"
                            });
                            guiasTable.ajax.reload();
                        }
                    });
                }
            });
        }

        function cargarDocumentos(feMin = '', feMax = '', tipo_despacho = '', tipo_traslado = '') {
            guiasTable = new DataTable('#tabla', {
                layout: {
                    topEnd: null
                },
                responsive: true,
                ajax: {
                    url: '/api/ventas/guiasdespacho',
                    data: function(d) {
                        d.ffecha = (feMin == '' && feMax == '') ? false : true;
                        if (feMin != '' && feMax != '') {
                            d.feMinDate = feMin;
                            d.feMaxDate = feMax;
                        }
                        if (tipo_despacho != ''&&tipo_despacho != 'Seleccione un tipo de despacho') {
                            d.tipo_despacho = tipo_despacho;
                        }
                        if (tipo_traslado != ''&&tipo_traslado != 'Seleccione un tipo de traslado') {
                            d.tipo_traslado = tipo_traslado;
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
                            var html = '';
                            var estado = row.estado;
                            if(estado == 0){
                                // en proceso
                                html += '<span class="badge bg-warning me-1" title="Documento En Proceso"><span class="mdi mdi-24 mdi-clock-time-eight-outline"></span></span>';
                            }else if(estado == 1){
                                // aceptado
                                html += '<span class="badge bg-success me-1" title="Documento Aceptado"><span class="mdi mdi-24 mdi-check-circle-outline"></span></span>';
                            }else if(estado == 2){
                                // rechazado
                                html += '<span class="badge bg-danger me-1" title="Documento Rechazado"><span class="mdi mdi-24 mdi-alert-circle-outline"></span></span>';
                            }else if(estado == 3){
                                // anulado
                                html += '<span class="badge bg-danger me-1" title="Documento Anulado"><span class="mdi mdi-24 mdi-close-circle"></span></span>';
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
                                if(row.estado != 3){
                                    html += '<button type="button" onclick="anularGuiaDespacho('+row.id+')" title="Anular Guia de Despacho" class="btn btn-outline-danger btnxs ms-1 px-1 py-0"><i class="mdi mdi-trash-can"></i></button>';
                                }
                                html += '<button type="button" title="Ver Guia de Despacho" onclick="vistaPreviaFactura('+row.folio+')" class="btn btn-outline-primary btnxs px-1 py-0 ms-1"><i class="mdi mdi-18 mdi-magnify"></i></button>';
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
        }
    </script>
@endpush
