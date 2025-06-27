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
                                <h6 class="card-title mb-3">LISTA DE DOCUMENTOS DISPONIBLES PARA CEDER</h6>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <div class="d-flex justify-content-end align-items-center">
                                            <button type="button" class="btn btn-primary" onclick="confirmarCesion()">Confirmar Cesión</button>
                                        </div>
                                        <br>
                                    <table id="example" class="compact hover order-column row-border" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" onclick="seleccionarTodos()"></th>
                                                <th>Folio</th>
                                                <th>Cliente</th>
                                                <th>RUT</th>
                                                <th>Fecha</th>
                                                <th>Monto Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($documentos as $doc)
                                            <tr>
                                                <td><input type="checkbox" name="documentos[]" value="{{ $doc->id }}"></td>
                                                <td>{{ $doc->folio }}</td>
                                                <td>{{ $doc->cliente->razon_social }}</td>
                                                <td>{{ $doc->cliente->rut }}</td>
                                                <td>{{ date('d/m/Y', strtotime($doc->fecha_emision)) }}</td>
                                                <td>$ {{ number_format($doc->monto_total, 0, ',', '.') }}</td>
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

        function confirmarCesion(folio) {
            Swal.fire({
                title: "¿Quieres confirmar la cesión de estos documentos?",
                text: "Una vez confirmada la cesión no se podra deshacer, se recomienda ser cuidadoso.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Confirmar",
                cancelButtonText: "Cancelar"
            }).then((modalResult) => {
                if (modalResult.isConfirmed) {
                    procesarCesion(folio);
                }
            });
        }   

        function seleccionarTodos() {
            if($('input[name="documentos[]"]').prop('checked')){    
                $('input[name="documentos[]"]').prop('checked', false);
            }else{
                $('input[name="documentos[]"]').prop('checked', true);
            }
        }

        function procesarCesion(folio) {

            $('#loadingModal').modal('show');
            $('#statusTxt').text('Enviando información del documento...');

            var documentos = $('input[name="documentos[]"]:checked').map(function() {
                return $(this).val();
            }).get();

            var data = {
                factoring_id:{{ $factoring->id }},
                cliente_id:{{ $cliente->id }},
                fecha: '{{ date('Y-m-d', strtotime(str_replace('/', '-', $fecha))) }}', //TODO: cambiar a la ruta correcta de la api
                documentos:documentos
            };  

            $.post("/api/ventas/cesiones", data) 
                .done(function(data) {
                    $('#statusTxt').text('Recibiendo información de respuesta...');
                    console.log(data);
                    $('#loadingModal').modal('hide');
                    Swal.fire({
                        title: 'Cesión realizada correctamente',
                        icon: 'success',    
                        timer: 1500,
                        showConfirmButton: true,
                        confirmButtonText: 'Aceptar',
                        showCancelButton: false,
                        showCloseButton: false,
                    }).then((modalResult) => {
                        if (modalResult.isConfirmed) {
                            location.href = '/ventas/cesiones';
                        }
                    });
                })
                .fail(function(data) {
                    $('#loadingModal').modal('hide');
                    Swal.fire({
                        title: 'Error al realizar la cesión',
                        icon: 'error',
                    }).then((modalResult) => {
                        if (modalResult.isConfirmed) {
                            location.href = '/ventas/cesiones';
                        }
                    });
                });
        }
        DataTable.datetime( 'DD/MM/YYYY' );

        facturasTable = new DataTable('#example', {
            responsive: true,
            search: {
                return: true
            },
            language: {
                url: '/assets/js/datatables/es-ES.json',
            },
            order: [[4, 'desc']],
            columnDefs: [{
                orderable: false,
                searchable: false,
                targets: [0]
            }],
            columns: [null, {className: 'dt-head-left dt-body-left' }, null, null, null, null],
        });
    </script>
@endpush
