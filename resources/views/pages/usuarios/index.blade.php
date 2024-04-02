@extends('layout.master')

@section('title', 'Gestión de Usuarios')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Gestión de Usuarios</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            <button type="button" class="btn btn-sm btn-primary btn-icon-text mb-2 mb-md-0"
                onclick="location.href = '/usuarios/nuevo';">
                <div>
                    <i class="mdi mdi-plus"></i> Nuevo Usuario
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
                                <h6 class="card-title mb-3">LISTA DE USUARIOS</h6>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <table id="example" class="compact hover order-column row-border" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Nombre(s)</th>
                                                <th>Apellido(s)</th>
                                                <th>Email</th>
                                                <th>Ult. Conexión</th>
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
        var usersTable = null;
        var currentUserId = {{auth()->user()->id}};

        function deleteUser(id){
            Swal.fire({
                title: "Confirmar eliminación de usuario",
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
                        url: '/api/usuarios/' + id,
                        success: function(data){
                            Swal.fire({
                                title: "Usuario eliminado exitosamente",
                                text: "El usuario seleccionado fue eliminado satisfactoriamente",
                                icon: "success"
                            });
                            usersTable.ajax.reload();
                        }
                    });
                }
            });
        }

        function editUser(id){
            location.href = '/usuarios/editar/' + id
        }

        usersTable = new DataTable('#example', {
            responsive: true,
            ajax: '/api/usuarios',
            search: {
                return: true
            },
            language: {
                url: '/assets/js/datatables/es-ES.json',
            },
            columns: [
                {
                    data: 'name',
                    responsivePriority: 1
                },
                {
                    data: 'lastname',
                    responsivePriority: 1
                },
                {
                    data: 'email',
                    responsivePriority: 3
                },
                {
                    data: 'last_login',
                    searchable:false,
                    responsivePriority: 10,
                    render: function(data, type, row) {
                        if(data === null){
                            return 'No registra'
                        }else{
                            return data;
                        }
                    }
                },
                {
                    data: null,
                    orderable:false,
                    responsivePriority: 1,
                    render: function(data, type, row) {
                        console.log(row);
                        var html = '<div>' +
                            ((currentUserId == row.id) ? '': '<button type="button" title="Editar Usuario" onclick="editUser('+row.id+')" class="btn btnxs px-1 py-0"><i class="mdi mdi-pencil"></i></button>' +
                            '<button type="button" onclick="deleteUser('+row.id+')" title="Eliminar Usuario" class="btn btnxs px-1 py-0"><i class="mdi mdi-trash-can"></i></button>') +
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
