@extends('layout.master')

@section('title', 'Gestión de Proyectos')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Gestión de Proyectos</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            <button type="button" class="btn btn-sm btn-primary btn-icon-text mb-2 mb-md-0"
                onclick="location.href = '/ventas/proyectos/nuevo';">
                <div>
                    <i class="mdi mdi-plus"></i> Nuevo Proyecto
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
                                <h6 class="card-title mb-3">LISTA DE PROYECTOS</h6>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <table id="example" class="compact hover order-column row-border" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
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
        var proyectosTable = null;
        var currentUserId = {{auth()->user()->id}};

        function deleteProyecto(id){
            Swal.fire({
                title: "Confirmar eliminación de proyecto",
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
                        url: '/api/ventas/proyectos/' + id,
                        success: function(data){
                            Swal.fire({
                                title: "Proyecto eliminado exitosamente",
                                text: "El proyecto seleccionado fue eliminado satisfactoriamente",
                                icon: "success"
                            });
                            proyectosTable.ajax.reload();
                        }
                    });
                }
            });
        }

        function editProyecto(id){
            location.href = '/ventas/proyectos/editar/' + id;
        }

        function verProyecto(id){
            location.href = '/ventas/proyectos/' + id;
        }

        proyectosTable = new DataTable('#example', {
            responsive: true,
            ajax: '/api/ventas/proyectos',
            search: {
                return: true
            },
            language: {
                url: '/assets/js/datatables/es-ES.json',
            },
            columns: [
                {
                    data: 'nombre',
                    responsivePriority: 1,
                    width: '80%',
                    render: function(data, type, row){
                        return '<a href="javascript:void(0);" class="text-decoration-none text-black" onclick="verProyecto('+row.id+')">'+row.nombre+'</a>'
                    }
                },
                {
                    data: null,
                    orderable:false,
                    responsivePriority: 1,
                    render: function(data, type, row) {
                        console.log(row);
                        var html = '<div class="float-end me-2">' +
                            '<button type="button" onclick="deleteProyecto('+row.id+')" title="Eliminar Proyecto" class="btn btnxs px-1 py-0"><i class="mdi mdi-trash-can"></i></button></div>';
                        return html;
                    }
                },
            ],
            processing: true,
            serverSide: true
        });
    </script>
@endpush
