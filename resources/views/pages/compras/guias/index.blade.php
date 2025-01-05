@extends('layout.master')

@section('title', 'Gestión de Facturas')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Gestión de Guias de Despacho de Compras</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-3">LISTA DE GUIAS DE DESPACHO DE COMPRAS</h6>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="col-12 mx-2">
                                        Desde: <input type="text" id="min" name="min">
                                        Hasta:  <input type="text" id="max" name="max">
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
                                            @foreach($documentos as $doc)
                                            <tr>
                                                <td>{{ $doc->folio }}</td>
                                                <td>{{ $doc->razonsocial_emisor }}</td>
                                                <td>{{ $doc->rut_emisor }}</td>
                                                <td>{{ $doc->fecha_emision }}</td>
                                                <td>$ {{ number_format($doc->monto_total, 0, ',', '.') }}</td>
                                                <td><div><button type="button" title="Ver Factura de Compra" onclick="verDocumento('{{$doc->rut_emisor}}', {{$doc->folio}})" class="btn btn-outline-primary btnxs px-1 py-0 ms-1"><i class="mdi mdi-18 mdi-text-box-search-outline"></i></button><button type="button" title="Ver Factura de Compra" onclick="vistaPreviaDocumento('{{$doc->rut_emisor}}', {{$doc->tipo_doc}}, {{$doc->folio}})" class="btn btn-outline-primary btnxs px-1 py-0 ms-1"><i class="mdi mdi-18 mdi-magnify"></i></button></div></td>
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
        var guiasTable = null;
        var currentUserId = {{ auth()->user()->id }};

        function verDocumento(emisor, folio) {
            location.href = `/compras/guiasdespacho/detalle/${emisor}/${folio}`;
        }

        function vistaPreviaDocumento(emisor, tipo, folio) {
            window.open(`/api/compras/guiasdespacho/vistaprevia/${emisor}/${tipo}/${folio}`);
        }

        guiasTable = new DataTable('#example', {
            responsive: true,
            search: {
                return: true
            },
            language: {
                url: '/assets/js/datatables/es-ES.json',
            },
            order: [
                [3, 'desc']
            ]
        });

 // Custom filtering function which will search data in column four between two values
 DataTable.ext.search.push(function (settings, data, dataIndex) {
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


 // Refilter the table
 document.querySelectorAll('#min, #max').forEach((el) => {
     el.addEventListener('change', () => guiasTable.draw());
 });
    </script>
@endpush
