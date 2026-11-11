@extends('layout.master')

@section('title', 'Gestión de Facturas Pendientes')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Gestión de Facturas Pendientes de Acuse de Recibo</h4>
        </div>
        <div class="align-end">
            <button type="button" class="btn btn-danger" onclick="location.href = '/compras/facturas'">
                <i class="mdi mdi-arrow-left"></i>
                Volver
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
                                <h6 class="card-title mb-3">LISTA DE FACTURAS PENDIENTES</h6>
                                <!--aqui boton-->
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
                            </div>
                            <div class="row">
                                <div class="col-12 d-flex justify-content-md-end">
                                    <div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle" type="button"
                                            id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                            Acciones masivas
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <li><a class="dropdown-item" onclick="botonAgregarEvento('ERM')" href="#">Otorga Recibo de Mercaderías o
                                                    Servicios (ERM)</a></li>
                                            <li><a class="dropdown-item" onclick="botonAgregarEvento('RCD')" href="#">Reclamo al Contenido del Documento
                                                    (RCD)</a></li>
                                            <hr style="margin: 5px 0;">
                                            <li><a class="dropdown-item" onclick="botonAgregarEvento('ACD')" href="#">Acepta Contenido del Documento
                                                    (ACD)</a></li>
                                            <li><a class="dropdown-item" onclick="botonAgregarEvento('RFP')" href="#">Reclamo por Falta Parcial de
                                                    Mercaderías (RFP)</a></li>
                                            <li><a class="dropdown-item" onclick="botonAgregarEvento('RFT')" href="#">Reclamo por Falta Total de
                                                    Mercaderías (RFT)</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <table id="tabla" class="compact hover order-column row-border" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th style="width:10%" class="text-start"><input type="checkbox"
                                                        id="selectall"> Todas </input></th>
                                                <th style="width:10%" class="text-start">Folio</th>
                                                <th style="width:45%" class="text-start">Emisor</th>
                                                <th style="width:10%" class="text-start">RUT</th>
                                                <th style="width:10%" class="text-start">Fecha</th>
                                                <th style="width:15%" class="text-start">Monto Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($documentos as $documento)
                                                <tr>
                                                    <td><input class="ms-1 selectedId" name="selectedId" type="checkbox"
                                                            emisor="{{ $documento->rut_emisor }}"
                                                            folio="{{ $documento->folio }}" /></td>
                                                    <td class="text-start">{{ $documento->folio }}</td>
                                                    <td class="text-start">{{ $documento->razon_social }}</td>
                                                    <td class="text-start">{{ $documento->rut_emisor }}</td>
                                                    <td class="text-start">
                                                        {{ optional($documento->fecha_emision)->format('d/m/Y') }}
                                                    </td>
                                                    <td class="text-start">$
                                                        {{ number_format($documento->monto_total, 0, ',', '.') }}</td>
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
    <div class="modal fade" id="loadingModal" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <img class="text-center mx-auto" src="/loading.gif" style="width: 48px; height: 48px;">
                <span id="statusTxt" class="text-center fw-bold">Enviando información al SII...</span>
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
        };
        var selectedFacturas = [];

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

            $('#selectall').click(function() {
                $('.selectedId').prop('checked', this.checked);
                var data = facturasTable.rows().data();

                if(this.checked){
                    selectedFacturas = [];
                    selectedFacturas = $.map(facturasTable.rows({page:'current'}).nodes(), function (item) {
                        var item = $(item).find('td').eq(0);

                        var emisor = $(item).find('input').attr('emisor');
                        var folio = parseInt($(item).find('input').attr('folio'));

                        return {'rut':emisor, 'folio':folio};
                    });
                }else{
                    selectedFacturas = [];
                }
                console.log(selectedFacturas);
            });

            $('.selectedId').change(function() {
                var check = ($('.selectedId').filter(":checked").length == $('.selectedId').length);

                $('#selectall').prop("checked", check);

                var factura = {
                    rut: $(this).attr('emisor'),
                    folio: $(this).attr('folio')
                };

                let index = checkFactura(factura);
                if (index != false) {
                    selectedFacturas.splice(index, 1);
                } else {
                    selectedFacturas.push(factura);
                }
                console.log(selectedFacturas);
            });

        });

        function botonAgregarEvento(evento){
            Swal.fire({
                title: "Confirmar registro de evento en DTE",
                text: "La acción que desea realizar es irreversible, ¿desea continuar con la operación?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#6571FF",
                cancelButtonColor: "#FF3366",
                confirmButtonText: "Confirmar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    agregarEventoDTE(evento);
                }
            });
        }

        function agregarEventoDTE(evento) {
            $('#loadingModal').modal('toggle');
            for(i = 0; i < selectedFacturas.length; i++){
                var item = selectedFacturas[i];

                var dataJson = {
                    rut: item.rut,
                    folio: item.folio,
                    evento : evento
                };

                $.ajax({
                    type: "POST",
                    url: '/api/compras/rcv/agregarevento',
                    data: dataJson, // serializes the form's elements.
                    dataType: 'json',
                    success: function(data) {
                        console.log(data);
                        $('#loadingModal').modal('hide');
                        if (data.success == true) {
                            location.reload();
                        } else {
                            Swal.fire({
                                title: "Error registrando evento en DTE",
                                text: data.msg,
                                icon: "error"
                            });
                        }
                    }
                });
            }
        }

        function checkFactura(factura) {
            for (var i = 0; i < selectedFacturas.length; i++) {
                selected = selectedFacturas[i];
                if (factura.rut == selected.rut && factura.folio == selected.folio) {
                    return i;
                }
            }
            return false;
        }

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
                search: {
                    return: true
                },
                columnDefs: [{
                    orderable: false,
                    sorting: false,
                    targets: 0
                }],
                language: {
                    url: '/assets/js/datatables/es-ES.json',
                },
                order: [
                    [4, 'desc']
                ],
                processing: true,
                serverSide: false
            });
        }
    </script>
@endpush
