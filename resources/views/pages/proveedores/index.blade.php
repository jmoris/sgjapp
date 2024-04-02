@extends('layout.master')

@section('title', 'Gestión de Proveedores')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Gestión de Proveedores</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            <button type="button" class="btn btn-sm btn-primary btn-icon-text mb-2 mb-md-0"
                onclick="location.href = '/proveedores/nuevo';">
                <div>
                    <i class="mdi mdi-plus"></i> Nuevo Proveedor
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
                                <h6 class="card-title mb-3">LISTA DE PROVEEDORES</h6>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <table id="example" class="compact hover order-column row-border" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>RUT</th>
                                                <th>Razón Social</th>
                                                <th>Dirección</th>
                                                <th>Comuna</th>
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

@push('custom-scripts')
    <script>
        var proveedoresTable = null;
        var currentUserId = {{auth()->user()->id}};

        function deleteProveedor(id){
            Swal.fire({
                title: "Confirmar eliminación de proveedor",
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
                        url: '/api/proveedores/' + id,
                        success: function(data){
                            Swal.fire({
                                title: "Registro eliminado",
                                text: "El proveedor seleccionado fue eliminado satisfactoriamente",
                                icon: "success"
                            });
                            proveedoresTable.ajax.reload();
                        }
                    });
                }
            });
        }

        function editProveedor(id){
            location.href = '/proveedores/editar/' + id
        }

        proveedoresTable = new DataTable('#example', {
            responsive: true,
            ajax: '/api/proveedores',
            search: {
                return: true
            },
            language: {
                url: '/assets/js/datatables/es-ES.json',
            },
            columns: [
                {
                    data: 'rut',
                    responsivePriority: 1
                },
                {
                    data: 'razon_social',
                    responsivePriority: 2
                },
                {
                    data: 'direccion',
                    responsivePriority: 3
                },
                {
                    data: 'comuna.nombre',
                    responsivePriority: 3
                },
                {
                    data: null,
                    orderable:false,
                    render: function(data, type, row) {
                        console.log(row);
                        var html = '<div><button type="button" title="Editar Proveedor" onclick="editProveedor('+row.id+')" class="btn btnxs px-1 py-0"><i class="mdi mdi-pencil"></i></button>' +
                            '<button type="button" onclick="deleteProveedor('+row.id+')" title="Eliminar Proveedor" class="btn btnxs px-1 py-0"><i class="mdi mdi-trash-can"></i></button>' +
                            '</div>';
                        return html;
                    }
                },
            ],
            processing: true,
            serverSide: true
        });
    </script>
@endpush
