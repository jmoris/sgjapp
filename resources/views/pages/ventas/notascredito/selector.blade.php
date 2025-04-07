@extends('layout.master')

@section('title', 'Selección de Documentos')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Selección de Documentos</h4>
        </div>

    </div>

    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-3">LISTA DE DOCUMENTOS DISPONIBLES PARA ANULAR</h6>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <table id="example" class="compact hover order-column row-border" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Folio</th>
                                                <th>Cliente</th>
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
                                                <td>{{ $doc->cliente->razon_social }}</td>
                                                <td>{{ $doc->cliente->rut }}</td>
                                                <td>{{ date('d/m/Y', strtotime($doc->fecha_emision)) }}</td>
                                                <td>$ {{ number_format($doc->monto_total, 0, ',', '.') }}</td>
                                                <td>
                                                    <button type="button" title="Anular Documento" onclick="confirmarAnulacion({{$doc->folio}})" class="btn btn-outline-primary btnxs px-1 py-0 ms-1"><i class="mdi mdi-18 mdi-arrow-right-thick"></i></button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment-with-locales.min.js" integrity="sha512-4F1cxYdMiAW98oomSLaygEwmCnIP38pb4Kx70yQYqRwLVCs3DbRumfBq82T08g/4LJ/smbFGFpmeFlQgoDccgg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush

@push('custom-scripts')
    <script>
        var facturasTable = null;
        var currentUserId = {{auth()->user()->id}};

        function confirmarAnulacion(folio) {
            Swal.fire({
                title: "¿Quieres confirmar la anulación de este documento?",
                text: "Una vez confirmada la anulación no se podra deshacer, se recomienda ser cuidadoso.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Confirmar",
                cancelButtonText: "Cancelar"
            }).then((modalResult) => {
                if (modalResult.isConfirmed) {
                    procesarAnulacion(folio);
                }
            });
        }

        function procesarAnulacion(folio) {

            $('#loadingModal').modal('show');
            $('#statusTxt').text('Enviando información del documento...');

            var doc = {
                tipo_doc: 33,
                folio: folio
            };

            $.post("/api/ventas/notascredito/anulacion", doc)
                .done(function(data) {
                    $('#statusTxt').text('Recibiendo información de respuesta...');
                    console.log(data);
                    $('#loadingModal').modal('hide');
                    location.href = '/ventas/notascredito';
                });

        }

        facturasTable = new DataTable('#example', {
            responsive: true,
            search: {
                return: true
            },
            language: {
                url: '/assets/js/datatables/es-ES.json',
            },
            order: [[0, 'desc']],
            columns: [{ className: 'dt-head-left dt-body-left' }, null, null, null, null, null],
        });
    </script>
@endpush
