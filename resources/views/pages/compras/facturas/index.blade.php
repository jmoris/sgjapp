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
                            <div class="row">
                                <div class="col-12">
                                    <div class="col-12 mx-2 my-2">
                                        Fecha emisión: <input type="text" id="min" name="min"> - <input
                                            type="text" id="max" name="max">
                                    </div>
                                    <table id="example" class="compact hover order-column row-border" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Folio</th>
                                                <th>Emisor</th>
                                                <th>RUT</th>
                                                <th>Fecha</th>
                                                <th>Monto Total</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($documentos as $doc)
                                                <tr>
                                                    <td>{{ $doc->folio }}</td>
                                                    <td>{{ $doc->razonsocial_emisor }}</td>
                                                    <td>{{ $doc->rut_emisor }}</td>
                                                    <td>{{ $doc->fecha_emision }}</td>
                                                    <td>$ {{ number_format($doc->monto_total, 0, ',', '.') }}</td>
                                                    <td>
                                                        <div><button type="button" title="Ver Factura de Compra"
                                                                onclick="verDocumento('{{ $doc->rut_emisor }}', {{ $doc->folio }})"
                                                                class="btn btn-outline-primary btnxs px-1 py-0 ms-1"><i
                                                                    class="mdi mdi-18 mdi-text-box-search-outline"></i></button><button
                                                                type="button" title="Ver Vista Previa Factura de Compra"
                                                                onclick="vistaPreviaDocumento('{{ $doc->rut_emisor }}', {{ $doc->tipo_doc }}, {{ $doc->folio }})"
                                                                class="btn btn-outline-primary btnxs px-1 py-0 ms-1"><i
                                                                    class="mdi mdi-18 mdi-magnify"></i></button></div>
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
@endsection

@push('plugin-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment-with-locales.min.js"
        integrity="sha512-4F1cxYdMiAW98oomSLaygEwmCnIP38pb4Kx70yQYqRwLVCs3DbRumfBq82T08g/4LJ/smbFGFpmeFlQgoDccgg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush

@push('custom-scripts')
    <script>
        var facturasTable = null;
        var currentUserId = {{ auth()->user()->id }};
        let minDate, maxDate;

        function verDocumento(emisor, folio) {
            location.href = `/compras/facturas/detalle/${emisor}/${folio}`;
        }

        function vistaPreviaDocumento(emisor, tipo, folio) {
            window.open(`/api/compras/facturas/vistaprevia/${emisor}/${tipo}/${folio}`);
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
                            html += '<button type="button" title="Documento XML no disponible" disabled class="btn btn-outline-primary btnxs px-1 py-0 ms-1"><i class="mdi mdi-18 mdi-text-box-search-outline"></i></button>';
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
            let date = new Date(data[3]);

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
