@extends('layout.master')

@section('title', 'Gestión de Facturas')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Gestión de Facturas Pendientes de Acuse</h4>
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
                                            <li><a class="dropdown-item" onclick="agregarEventoDTE('ERM')" href="#">Otorga Recibo de Mercaderías o
                                                    Servicios (ERM)</a></li>
                                            <li><a class="dropdown-item" onclick="agregarEventoDTE('RCD')" href="#">Reclamo al Contenido del Documento
                                                    (RCD)</a></li>
                                            <hr style="margin: 5px 0;">
                                            <li><a class="dropdown-item" onclick="agregarEventoDTE('ACD')" href="#">Acepta Contenido del Documento
                                                    (ACD)</a></li>
                                            <li><a class="dropdown-item" onclick="agregarEventoDTE('RFP')" href="#">Reclamo por Falta Parcial de
                                                    Mercaderías (RFP)</a></li>
                                            <li><a class="dropdown-item" onclick="agregarEventoDTE('RFT')" href="#">Reclamo por Falta Total de
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
                                                <th style="width:35%" class="text-start">Emisor</th>
                                                <th style="width:15%" class="text-start">RUT</th>
                                                <th style="width:15%" class="text-start">Fecha</th>
                                                <th style="width:15%" class="text-start">Monto Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($documentos as $documento)
                                                <tr>
                                                    <td><input class="ms-1 selectedId" name="selectedId" type="checkbox"
                                                            emisor="{{ $documento->detRutDoc . '-' . $documento->detDvDoc }}"
                                                            folio="{{ $documento->detNroDoc }}" /></td>
                                                    <td class="text-start">{{ $documento->detNroDoc }}</td>
                                                    <td class="text-start">{{ $documento->detRznSoc }}</td>
                                                    <td class="text-start">
                                                        {{ $documento->detRutDoc . '-' . $documento->detDvDoc }}</td>
                                                    <td class="text-start">
                                                        {{ date('d/m/Y', strtotime(str_replace('/', '-', $documento->detFchDoc))) }}
                                                    </td>
                                                    <td class="text-start">$
                                                        {{ number_format($documento->detMntTotal, 0, ',', '.') }}</td>
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

                var id_list = $.map(facturasTable.rows('tr').nodes(), function (item) {
                    console.log(item);
                    return {'rut':$(item).attr("emisor"), 'folio':$(item).attr("folio")};
                });
                console.log(id_list);
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
            });

        });

        function agregarEventoDTE(evento) {

            for(i = 0; i < selectedFacturas.length; i++){
                var item = selectedFacturas[i];

                var dataJson = {
                    contribuyente: '{{$emisor["rut"]}}',
                    rut_emisor: item.rut,
                    tipo: 33,
                    folio: item.folio,
                    evento : evento
                };

                $.ajax({
                    type: "POST",
                    url: '/api/rcv/agregarevento',
                    data: dataJson, // serializes the form's elements.
                    success: function(data) {
                        console.log(data);
                        if (data.success == true) {
                            console.log("precio guardado");
                        } else {
                            console.log("error al guardar el precio");
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
                pageLength: 5,
                processing: true,
                serverSide: false
            });
        }
    </script>
@endpush
