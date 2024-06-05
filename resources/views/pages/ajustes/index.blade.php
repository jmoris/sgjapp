@extends('layout.master')

@section('title', 'Ajustes gENERALES')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Ajustes Generales</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">

        </div>
    </div>
    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="col-md-12">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tributario">Información
                                    tributaria</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#unidades">Unidades de Medida</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#categorias">Categorias de Productos</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane container active" id="tributario">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="container">
                                            <div class="row g-2">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="d-flex justify-content-between align-items-baseline">
                                                        <h6 class="card-title mb-3">Información del contribuyente</h6>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="mx-2">
                                                                <div class="mb-2">
                                                                    <label class="form-label">R.U.T.</label>
                                                                    <input type="text" name="rut" id="rut"
                                                                        class="form-control" value="{{ $emisor['rut'] }}"
                                                                        disabled>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label class="form-label">Razón social</label>
                                                                    <input type="text" name="razon_social"
                                                                        id="razon_social" class="form-control"
                                                                        value="{{ $emisor['razon_social'] }}" disabled>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label class="form-label">Actividad Económica</label>
                                                                    <input type="text" name="giro" id="giro"
                                                                        class="form-control" value="{{ $emisor['giro'] }}"
                                                                        disabled>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label class="form-label">Dirección</label>
                                                                    <input type="text" name="direccion" id="direccion"
                                                                        class="form-control"
                                                                        value="{{ $emisor['direccion'] }}" disabled>
                                                                </div>
                                                                <div class="mb-4">
                                                                    <label class="form-label">Comuna</label>
                                                                    <input type="text" name="comuna" id="comuna"
                                                                        class="form-control"
                                                                        value="{{ App\Comuna::find($emisor['comuna'])->nombre }}"
                                                                        disabled>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="d-flex justify-content-between align-items-baseline">
                                                        <h6 class="card-title mb-3">Información del certificado</h6>
                                                    </div>
                                                    <div class="row mx-1">
                                                        @php
                                                            $estado = \App\Helpers\SII::statusCert();
                                                        @endphp
                                                        <div>
                                                            <span class="fw-bold">ESTADO DEL CERTIFICADO: </span>
                                                            {!! ($estado['valido'])? '<span class="badge bg-success">VIGENTE</span>':'<span class="badge bg-danger">INVALIDO</span>'  !!}
                                                        </div>
                                                        <div>
                                                            <span class="fw-bold">DESDE:</span>
                                                            {{ $estado['desde']}}
                                                        </div>
                                                        <div>
                                                            <span class="fw-bold">HASTA:</span>
                                                            {{ $estado['hasta']}}
                                                        </div>
                                                        @php
                                                            $date1 = new DateTime($estado['desde']);
                                                            $date2 = new DateTime($estado['hasta']);
                                                            $diferencia = $date1->diff($date2);
                                                        @endphp
                                                        <small class="text-muted mt-2">* Quedan {{$diferencia->days}} dias para el vencimiento del certificado.</small>
                                                        <div
                                                            style="margin-top:.5em; margin-bottom: 1em; width:100%; border-bottom: 1px solid gainsboro;">
                                                        </div>
                                                        <form method="POST" enctype="multipart/form-data" action="/api/config/certificado">
                                                            @csrf
                                                            <div class="mb-3">
                                                                <label for="formFileSm" class="form-label">CERTIFICADO
                                                                    DIGITAL</label>
                                                                <input class="form-control form-control-sm" id="cert"
                                                                    type="file" name="cert">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="formFileSm" class="form-label">CONTRASEÑA
                                                                    CERTIFICADO</label>
                                                                <input class="form-control form-control-sm" id="password" name="password"
                                                                    type="text">
                                                            </div>
                                                            <div class="w-100">
                                                                <button type="submit"
                                                                    class="float-end btn btn-sm btn-primary">CARGAR
                                                                    CERTIFICADO</button>
                                                                    @if($errors->has('certificado'))
                                                                        <small class="text-red">El certificado no es valido o contiene errores</small>
                                                                    @endif
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane container fade" id="unidades">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="container">
                                            <div class="d-flex justify-content-between align-items-baseline">
                                                <h6 class="card-title mb-3">Unidades</h6>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <table id="tablaUnidades" class="table">
                                                        <thead>
                                                            <th>#</th>
                                                            <th>Nombre</th>
                                                            <th>Abreviación</th>
                                                            <th></th>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($unidades as $unidad)
                                                                <tr>
                                                                    <td>{{ $unidad->id }}</td>
                                                                    <td>{{ $unidad->nombre }}</td>
                                                                    <td>{{ $unidad->abreviacion }}</td>
                                                                    <td><button
                                                                            class="btn btn-outline-danger btn-sm px-1 py-0"
                                                                            onclick="deleteUnidad({{ $unidad->id }})"><i
                                                                                class="mdi mdi-delete"></i></button></td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="border-start col-md-4">
                                                    <div class="row">
                                                        <form id="formUnidades" name="formUnidades" method="post">
                                                            <div class="mb-3">
                                                                <label class="form-label">Nombre</label>
                                                                <input type="text" name="nombre" id="nombre"
                                                                    class="form-control"
                                                                    placeholder="Ingrese el nombre de la unidad">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Abreviación</label>
                                                                <input type="text" name="abreviacion" id="abreviacion"
                                                                    class="form-control" maxlength="3"
                                                                    placeholder="Ingrese la abreviación de la unidad">
                                                            </div>
                                                            <div class="col-md-12 mx-1">
                                                                <button class="btn btn-warning btn-sm"
                                                                    id="btnUnidadLimpiar" type="button">Limpiar</button>
                                                                <button class="btn btn-primary btn-sm float-end"
                                                                    id="btnUnidadGuardar" type="submit">Guardar</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane container fade" id="categorias">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="container">
                                            <div class="d-flex justify-content-between align-items-baseline">
                                                <h6 class="card-title mb-3">Categorias</h6>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <table id="tablaCategorias" class="table">
                                                        <thead>
                                                            <th>#</th>
                                                            <th>Nombre</th>
                                                            <th></th>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($categorias as $categoria)
                                                                <tr>
                                                                    <td>{{ $categoria->id }}</td>
                                                                    <td>{{ $categoria->nombre }}</td>
                                                                    <td><button
                                                                            class="btn btn-outline-danger btn-sm px-1 py-0"><i
                                                                                class="mdi mdi-delete"></i></button></td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="border-start col-md-4">
                                                    <div class="row">
                                                        <form id="formCategorias" name="formCategorias" method="post">
                                                            <div class="mb-3">
                                                                <label class="form-label">Nombre</label>
                                                                <input type="text" name="nombreCategoria"
                                                                    id="nombreCategoria" class="form-control"
                                                                    placeholder="Ingrese el nombre de la unidad">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Descripción</label>
                                                                <input type="text" name="descripcionCategoria"
                                                                    id="descripcionCategoria" class="form-control"
                                                                    placeholder="Ingrese la descripcion de la categoria">
                                                            </div>
                                                            <div class="col-md-12 mx-1">
                                                                <button class="btn btn-warning btn-sm"
                                                                    id="btnCategoriaLimpiar"
                                                                    type="button">Limpiar</button>
                                                                <button class="btn btn-primary btn-sm float-end"
                                                                    id="btnCategoriaGuardar"
                                                                    type="submit">Guardar</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
@endpush

@push('custom-scripts')
    <script>
        function deleteUnidad(id) {
            Swal.fire({
                title: "Confirmar eliminación de unidad",
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
                        url: '/api/unidades/' + id,
                        success: function(data) {
                            Swal.fire({
                                title: "Unidad eliminada exitosamente",
                                text: "La unidad seleccionada fue eliminada satisfactoriamente",
                                icon: "success"
                            });
                            location.href = "/ajustes#unidades";
                            location.reload();
                        }
                    });
                }
            });
        }

        function deleteCategoria(id) {
            Swal.fire({
                title: "Confirmar eliminación de categoria",
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
                        url: '/api/unidades/' + id,
                        success: function(data) {
                            Swal.fire({
                                title: "Categoria eliminada exitosamente",
                                text: "La categoria seleccionada fue eliminada satisfactoriamente",
                                icon: "success"
                            });
                            location.href = "/ajustes#categorias";
                            location.reload();
                        }
                    });
                }
            });
        }

        $(document).ready(function() {
            console.log("Documento cargado");
            $("#formUnidades").validate({
                rules: {
                    nombre: {
                        required: true,
                    },
                    abreviacion: {
                        required: true,
                    },
                },
                messages: {
                    nombre: "El campo nombre es obligatorio",
                    abreviacion: "El campo abreviación es obligatorio",
                },
                submitHandler: function(form) {
                    $.ajax({
                        type: "POST",
                        url: '/api/unidades',
                        data: $(form).serialize(), // serializes the form's elements.
                        success: function(data) {
                            if (data.success == true) {
                                Swal.fire({
                                    title: "Unidad guardada exitosamente",
                                    text: "La información ingresada es correcta y fue procesada exitosamente.",
                                    icon: "success"
                                }).then((result) => {
                                    location.href = "/ajustes#unidades";
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: "Error al guardar la unidad",
                                    text: "La información ingresada no es correcta o ya existe en la base de datos.",
                                    icon: "warning"
                                });
                            }

                        }
                    });
                    return false;
                },
                errorPlacement: function(error, element) {
                    error.addClass("invalid-feedback");

                    if (element.parent('.input-group').length) {
                        error.insertAfter(element.parent());
                    } else if (element.prop('type') === 'radio' && element.parent('.radio-inline')
                        .length) {
                        error.insertAfter(element.parent().parent());
                    } else if (element.prop('type') === 'checkbox' || element.prop('type') ===
                        'radio') {
                        error.appendTo(element.parent().parent());
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function(element, errorClass) {
                    if ($(element).prop('type') != 'checkbox' && $(element).prop('type') != 'radio') {
                        $(element).addClass("is-invalid").removeClass("is-valid");
                    }
                },
                unhighlight: function(element, errorClass) {
                    if ($(element).prop('type') != 'checkbox' && $(element).prop('type') != 'radio') {
                        $(element).addClass("is-valid").removeClass("is-invalid");
                    }
                }
            });
            $("#formCategorias").validate({
                rules: {
                    nombreCategoria: {
                        required: true,
                    },
                    descripcionCategoria: {
                        required: true,
                    }
                },
                messages: {
                    nombreCategoria: "El campo nombre es obligatorio",
                    descripcionCategoria: "El campo descripcion es obligatorio"
                },
                submitHandler: function(form) {
                    var dataCategorias = {
                        nombre: $('#nombreCategoria').val(),
                        descripcion: $('#descripcionCategoria').val()
                    };
                    $.ajax({
                        type: "POST",
                        url: '/api/categorias',
                        data: dataCategorias, // serializes the form's elements.
                        success: function(data) {
                            if (data.success == true) {
                                Swal.fire({
                                    title: "Categoria guardada exitosamente",
                                    text: "La información ingresada es correcta y fue procesada exitosamente.",
                                    icon: "success"
                                }).then((result) => {
                                    location.href = "/ajustes#unidades";
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: "Error al guardar la categoria",
                                    text: "La información ingresada no es correcta o ya existe en la base de datos.",
                                    icon: "warning"
                                });
                            }

                        }
                    });
                    return false;
                },
                errorPlacement: function(error, element) {
                    error.addClass("invalid-feedback");

                    if (element.parent('.input-group').length) {
                        error.insertAfter(element.parent());
                    } else if (element.prop('type') === 'radio' && element.parent('.radio-inline')
                        .length) {
                        error.insertAfter(element.parent().parent());
                    } else if (element.prop('type') === 'checkbox' || element.prop('type') ===
                        'radio') {
                        error.appendTo(element.parent().parent());
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function(element, errorClass) {
                    if ($(element).prop('type') != 'checkbox' && $(element).prop('type') != 'radio') {
                        $(element).addClass("is-invalid").removeClass("is-valid");
                    }
                },
                unhighlight: function(element, errorClass) {
                    if ($(element).prop('type') != 'checkbox' && $(element).prop('type') != 'radio') {
                        $(element).addClass("is-valid").removeClass("is-invalid");
                    }
                }
            });
        });
    </script>
@endpush
