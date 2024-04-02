@extends('layout.master')

@section('title', 'Gestión de Productos')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Gestión de Productos <small style="font-size:.75em;color: grey;">#{{ str_pad($producto->id, 5, '0', STR_PAD_LEFT); }}</small></h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h4 class="card-title mb-0">VISOR DE PRODUCTOS</h4>
                            </div>
                            <div class="row mx-2">
                                <div style="width:100%; margin-top:24px;"></div>
                                <form class="form" id="storeForm">
                                    @csrf
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="mb-3 border-bottom">
                                                <h5>Información general</h5>
                                            </div>
                                            <div class="mx-2">
                                                <div class="mb-2">
                                                    <label class="form-label">SKU</label>
                                                    <input type="text" name="sku" id="sku" class="form-control"
                                                        placeholder="Ingrese el SKU o código interno del producto"
                                                        value="{{$producto->sku}}">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Nombre</label>
                                                    <input type="text" name="nombre" id="nombre" class="form-control"
                                                        placeholder="Ingrese el nombre del producto"
                                                        value="{{$producto->nombre}}">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Descripción</label>
                                                    <textarea id="descripcion" name="descripcion" placeholder="Ingrese una descripción del producto" class="form-control" cols="40">{{$producto->descripcion}}</textarea>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Categoria</label>
                                                    <select class="form-control" id="categoria" name="categoria">
                                                        <option>Seleccionar categoria</option>
                                                        @foreach($categorias as $categoria)
                                                        <option value="{{$categoria->id}}" @if($producto->categoria_id == $categoria->id) selected @endif>{{$categoria->nombre}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-4">
                                                    <label class="form-label">Unidad</label>
                                                    <select class="form-control" id="unidad" name="unidad">
                                                        <option>Seleccionar unidad</option>
                                                        @foreach($unidades as $unidad)
                                                        <option value="{{$unidad->id}}" @if($producto->unidad_id == $unidad->id) selected @endif>{{$unidad->nombre}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-4 border-bottom">
                                                <h5>Información contable</h5>
                                            </div>
                                            <div class="mb-2">
                                                <input class="form-check-input" type="checkbox" value="1" id="es_afecto" name="es_afecto" @if($producto->es_afecto) checked @endif>
                                                <label class="form-check-label" for="es_afecto">
                                                    El producto es afecto
                                                </label>
                                            </div>
                                            <div class="mb-2">
                                                <input class="form-check-input" type="checkbox" value="1" id="se_vende" name="se_vende" @if($producto->se_vende) checked @endif>
                                                <label class="form-check-label" for="se_vende">
                                                    El producto se puede vender
                                                </label>
                                            </div>
                                            <div class="mb-2">
                                                <input class="form-check-input" type="checkbox" value="1" id="se_compra" name="se_compra" @if($producto->se_compra) checked @endif>
                                                <label class="form-check-label" for="se_compra">
                                                    El producto se puede comprar
                                                </label>
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label">Lista de precio</label>
                                                <select class="form-control" name="lista_precio" id="lista_precio">
                                                    <option>Seleccionar lista de precio</option>
                                                    <option value="1">General</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="float-end">
                                        <button type="submit" class="btn btn-primary submit"><i
                                                class="mdi mdi-content-save"></i> Guardar</button>
                                    </div>
                                    <button type="button" class="btn btn-danger" onclick="location.href = '/productos'">
                                        <i class="mdi mdi-cancel"></i>
                                        Cancelar
                                    </button>
                                </form>
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
        $("#storeForm").validate({
            rules: {
                name: {
                    required: true,
                },
                lastname: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                name: "El campo nombre(s) es obligatorio",
                lastname: "El campo apellidos(s) es obligatorio",
                email: {
                    required: "El campo correo es obligatorio",
                    email: "El campo correo no cumple con el formato solicitado a@b.cl"
                },
            },
            submitHandler: function(form) {
                $.ajax({
                    type: "POST",
                    url: '/api/productos/editar/{{ $producto->id }}',
                    data: $(form).serialize(), // serializes the form's elements.
                    success: function(data) {
                        Swal.fire({
                            title: "Producto actualizado exitosamente",
                            text: "La información ingresada es correcta y fue procesada exitosamente.",
                            icon: "success"
                        }).then((result) => {
                            location.reload();
                        });
                    }
                });
                return false;
            },
            errorPlacement: function(error, element) {
                error.addClass("invalid-feedback");

                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else if (element.prop('type') === 'radio' && element.parent('.radio-inline').length) {
                    error.insertAfter(element.parent().parent());
                } else if (element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
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
    </script>
@endpush
