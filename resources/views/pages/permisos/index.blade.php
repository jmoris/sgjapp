@extends('layout.master')

@section('title', 'Gestión de Usuarios')

@push('style')
<style>
.table tbody tr.highlight td {
    -webkit-user-select: none; /* Safari */
    -ms-user-select: none; /* IE 10 and IE 11 */
    user-select: none; /* Standard syntax */
    background-color: gainsboro;
}
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Gestión de Roles y Permisos</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="container">
                                <div class="row g-2">
                                    <div class="col-md-7 col-sm-12 border-end">
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <h6 class="card-title mb-3">LISTADO DE ROLES</h6>
                                            <button class="btn btn-primary btn-xs float-end" onclick="nuevoRol()">Nuevo Rol</button>
                                        </div>
                                        <table id="rolesTable" class="table compact hover order-column row-border"
                                            style="width:100%">
                                            <thead>
                                                <th>#</th>
                                                <th>Nombre</th>
                                                <th></th>
                                            </thead>
                                            <tbody>
                                                @foreach($roles as $rol)
                                                <tr>
                                                    <td>{{$rol->id}}</td>
                                                    <td>{{$rol->nombre}}</td>
                                                    <td>
                                                        @if($rol->nombre != 'Administrador')
                                                        <button type="button" class="float-end py-0 px-1 btn btn-danger btn-xs">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-5 col-sm-12">
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <h6 class="card-title mb-3">ASIGNACIÓN DE PERMISOS {{ ($rol!=null)?$rol->name:'' }}</h6>
                                        </div>
                                        <table id="permisosTable" class="mx-2 compact hover order-column row-border"
                                            style="width:100%">
                                            <thead>
                                                <th>Módulo</th>
                                                <th style="width:15%;">Ver</th>
                                                <th style="width:15%;">Editar</th>
                                                <th style="width:15%;">Crear</th>
                                                <th style="width:15%;">Eliminar</th>
                                            </thead>
                                            <tbody>
                                                <tr modulename="administracion">
                                                    <td>Administracion</td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="verAdministracion" name="verAdministracion"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="editarAdministracion" name="editarAdministracion"></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr modulename="usuarios">
                                                    <td>Usuarios</td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="verUsuarios" name="verUsuarios"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="editarUsuarios" name="editarUsuarios"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="crearUsuarios" name="crearUsuarios"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="eliminarUsuarios" name="eliminarUsuarios"></td>
                                                </tr>
                                                <tr modulename="proveedores">
                                                    <td>Proveedores</td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="verProveedor" name="verProveedor"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="editarProveedor" name="editarProveedor"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="crearProveedor" name="crearProveedor"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="eliminarProveedor" name="eliminarProveedor"></td>
                                                </tr>
                                                <tr modulename="clientes">
                                                    <td>Clientes</td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="verCliente" name="verCliente"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="editarCliente" name="editarCliente"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="crearCliente" name="crearCliente"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="eliminarCliente" name="eliminarCliente"></td>
                                                </tr>
                                                <tr modulename="productos">
                                                    <td>Productos</td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="verProducto" name="verProducto"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="editarProducto" name="editarProducto"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="crearProducto" name="crearProducto"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="eliminarProducto" name="eliminarProducto"></td>
                                                </tr>
                                                <tr modulename="ordenes_compra">
                                                    <td>Ordenes de Compra</td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="verOrdenCompra" name="verOrdenCompra"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="editarOrdenCompra" name="editarOrdenCompra"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="crearOrdenCompra" name="crearOrdenCompra"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="eliminarOrdenCompra" name="eliminarOrdenCompra"></td>
                                                </tr>
                                                <tr modulename="proyectos">
                                                    <td>Proyectos</td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="verProyecto" name="verProyecto"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="editarProyecto" name="editarProyecto"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="crearProyecto" name="crearProyecto"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="eliminarProyecto" name="eliminarProyecto"></td>
                                                </tr>
                                                <tr modulename="facturas">
                                                    <td>Facturas</td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="verFactura" name="verFactura"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="editarFactura" name="editarFactura"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="crearFactura" name="crearFactura"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="eliminarFactura" name="eliminarFactura"></td>
                                                </tr>
                                                <tr modulename="guias_despacho">
                                                    <td>Guias de Despacho</td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="verGuiaDespacho" name="verGuiaDespacho"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="editarGuiaDespacho" name="editarGuiaDespacho"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="crearGuiaDespacho" name="crearGuiaDespacho"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="eliminarGuiaDespacho" name="eliminarGuiaDespacho"></td>
                                                </tr>
                                                <tr modulename="notas_credito">
                                                    <td>Notas de Credito</td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="verNotaCredito" name="verNotaCredito"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="editarNotaCredito" name="editarNotaCredito"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="crearNotaCredito" name="crearNotaCredito"></td>
                                                    <td><input class="form-check-input" type="checkbox"
                                                            id="eliminarNotaCredito" name="eliminarNotaCredito"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <button type="button" onclick="guardarPermisos()" class="mt-3 btn btn-primary btn-xs float-end">Guardar</button>
                                    </div>
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
<style>
    div.dt-buttons {
    float: right;
}
</style>
@php
    $roleId = null;
    if(request()->has('roleId')){
        $roleId = request()->get('roleId');
    }
@endphp
    <script>
        var currentUserId = {{ auth()->user()->id }};
        var selectedRol = null;
        cargarPermisos(1);

        $(document).ready(function(){
            $('#rolesTable>tbody>tr').eq(0).addClass('highlight').siblings().removeClass('highlight');
            $('#rolesTable').on('dblclick', 'tbody tr', function(event) {
                $(this).addClass('highlight').siblings().removeClass('highlight');
                var id = $(this).find('td:eq(0)').text();
                var nombre = $(this).find('td:eq(1)').text();

                cargarPermisos(id);
            });



        });

        function nuevoRol(){
            Swal.fire({
                title: "Ingrese el nombre del Rol",
                input: "text",
                inputLabel: "Nombre del Rol",
                inputValue: "",
                showCancelButton: true,
                inputValidator: (value) => {
                    if (!value) {
                        return "El nombre del rol es obligatorio";
                    }
                }
            }).then((data) => {
                console.log(data.value);
                $.ajax({
                        type: "POST",
                        url: '/api/roles',
                        data: {nombre:data.value},
                        success: function(resp) {
                            location.reload();
                        }
                });
            });

        }

        function guardarPermisos(){
            var datos = {
                'usuarios': [tienePermiso('#verUsuarios'),tienePermiso('#editarUsuarios'),tienePermiso('#crearUsuarios'),tienePermiso('#eliminarUsuarios')],
                'proveedores': [tienePermiso('#verProveedor'),tienePermiso('#editarProveedor'),tienePermiso('#crearProveedor'),tienePermiso('#eliminarProveedor')],
                'productos': [tienePermiso('#verProducto'),tienePermiso('#editarProducto'),tienePermiso('#crearProducto'),tienePermiso('#eliminarProducto')],
                'ordenes_compra': [tienePermiso('#verOrdenCompra'),tienePermiso('#editarOrdenCompra'),tienePermiso('#crearOrdenCompra'),tienePermiso('#eliminarOrdenCompra')],
                'proyectos': [tienePermiso('#verProyecto'),tienePermiso('#editarProyecto'),tienePermiso('#crearProyecto'),tienePermiso('#eliminarProyecto')],
            };

            $.post('/api/roles/' + selectedRol + '/permisos', {modulos:datos}).done(function(e){
                cargarPermisos(selectedRol);
            });
        }

        function tienePermiso(id){
            var data = $(id).is(":checked");
            return (data)?1:0;
        }

        function bloquearAdmin(){
            var cells = $('#permisosTable tbody tr').children().find('input');
            if(selectedRol==1){
                cells.attr("disabled", true);
            }else{
                cells.removeAttr("disabled");
            }
        }

        function cargarPermisos(id){
            selectedRol = id;
            $.get('/api/roles/' + id + '/permisos').done(function(data){
             $('#permisosTable input').prop('checked', false);
             $.each(data.permisos, function(k,v) {
                    var cells = $('#permisosTable tr[modulename='+k+']').children();
                    if(v[0]){
                        cells.eq(1).find('input').prop('checked', true);
                    }
                    if(v[1]){
                        cells.eq(3).find('input').prop('checked', true);
                    }
                     if(v[2]){
                        cells.eq(2).find('input').prop('checked', true);
                    }
                     if(v[3]){
                        cells.eq(4).find('input').prop('checked', true);
                    }
                    return;
                });
            });
            bloquearAdmin();
        }

    </script>
@endpush
