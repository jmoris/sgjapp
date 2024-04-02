@extends('layout.master')

@section('title', 'Gestión de Productos')

@push('style')
    <style>
        tr.noBorder td {
            border: 0;
        }

        th,
        td {
            padding: 0px 0px;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Gestión de Productos <small
                    style="font-size:.75em;color: grey;">#{{ str_pad($producto->id, 5, '0', STR_PAD_LEFT) }}</small></h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h4 class="card-title mb-0">FORMULARIO DE EDICIÓN DE PRODUCTO</h4>
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
                                                        value="{{ $producto->sku }}">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Nombre</label>
                                                    <input type="text" name="nombre" id="nombre" class="form-control"
                                                        placeholder="Ingrese el nombre del producto"
                                                        value="{{ $producto->nombre }}">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Descripción</label>
                                                    <textarea id="descripcion" name="descripcion" placeholder="Ingrese una descripción del producto" class="form-control"
                                                        cols="40">{{ $producto->descripcion }}</textarea>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Categoria</label>
                                                    <select class="form-control" id="categoria" name="categoria">
                                                        <option>Seleccionar categoria</option>
                                                        @foreach ($categorias as $categoria)
                                                            <option value="{{ $categoria->id }}"
                                                                @if ($producto->categoria_id == $categoria->id) selected @endif>
                                                                {{ $categoria->nombre }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-4">
                                                    <label class="form-label">Unidad</label>
                                                    <select class="form-control" id="unidad" name="unidad">
                                                        <option>Seleccionar unidad</option>
                                                        @foreach ($unidades as $unidad)
                                                            <option value="{{ $unidad->id }}"
                                                                @if ($producto->unidad_id == $unidad->id) selected @endif>
                                                                {{ $unidad->nombre }}</option>
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
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    id="es_afecto" name="es_afecto"
                                                    @if ($producto->es_afecto) checked @endif>
                                                <label class="form-check-label" for="es_afecto">
                                                    El producto es afecto
                                                </label>
                                            </div>
                                            <div class="mb-2">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    id="se_vende" name="se_vende"
                                                    @if ($producto->se_vende) checked @endif>
                                                <label class="form-check-label" for="se_vende">
                                                    El producto se puede vender
                                                </label>
                                            </div>
                                            <div class="mb-2">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    id="se_compra" name="se_compra"
                                                    @if ($producto->se_compra) checked @endif>
                                                <label class="form-check-label" for="se_compra">
                                                    El producto se puede comprar
                                                </label>
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
                                <div style="width: 100%; margin: 16px 0px; border-bottom: 1px solid gainsboro;"></div>
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <button class="nav-link active" id="venta-tab" data-bs-toggle="tab"
                                            data-bs-target="#venta" type="button" role="tab"
                                            aria-selected="true">Precios de Venta</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" id="compra-tab" data-bs-toggle="tab"
                                            data-bs-target="#compra" type="button" role="tab"
                                            aria-selected="true">Precios de Compra</button>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="venta" role="tabpanel"
                                        aria-labelledby="venta-tab">
                                        <table class="table table-sm">
                                            <thead>
                                                <th>Nombre Lista</th>
                                                <th>Fecha Actualización</th>
                                                <th>Precio</th>
                                                <th></th>
                                            </thead>
                                            <tbody>
                                                @foreach ($producto->precios as $precio)
                                                        <tr>
                                                            <td>{{ App\ListaPrecio::find($precio->lista_precio_id)->nombre }}
                                                            </td>
                                                            <td>{{ $precio->updated_at }}</td>
                                                            <td>$ {{ number_format($precio->precio, 0, ',', '.') }}</td>
                                                            <td style="width: 10%;">
                                                                <button title="Editar precio"
                                                                    class="btn btn-outline-warning btn-sm px-1 py-0 float-end">
                                                                    <i class="mdi mdi-pencil"></i>
                                                                </button>
                                                                <button title="Eliminar precio"
                                                                    class="btn btn-outline-danger btn-sm px-1 py-0 float-end">
                                                                    <i class="mdi mdi-delete"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                @endforeach
                                                <tr id="rowDetalle">
                                                    <td>
                                                        <div class="input-group">
                                                            <select id="listaTxt" class="form-control form-control-sm">
                                                                <option>Seleccione Lista de Precios</option>
                                                                @foreach ($lista_precios as $lista)
                                                                    <option value="{{ $lista->id }}">
                                                                        {{ $lista->nombre }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ date('d/m/Y h:i') }}" disabled />
                                                    </td>
                                                    <td>
                                                        <input id="precioVentaTxt" type="text"
                                                            class="form-control form-control-sm" value="0"
                                                            placeholder="Precio de Venta" />
                                                    </td>
                                                    <td style="width:10%;">
                                                        <button title="Guardar precio"
                                                            class="btn btn-outline-primary btn-sm px-1 py-0 float-end"
                                                            onclick="agregarPrecioVenta()">
                                                            <i class="mdi mdi-content-save"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="compra" role="tabpanel"
                                        aria-labelledby="compra-tab">
                                        <table class="table table-sm">
                                            <thead>
                                                <th>Nombre Proveedor</th>
                                                <th>Fecha Actualización</th>
                                                <th>Precio</th>
                                                <th></th>
                                            </thead>
                                            <tbody>
                                                @foreach ($producto->proveedores as $precio)
                                                        <tr>
                                                            <td>{{ $precio->razon_social }}
                                                            </td>
                                                            <td>{{ $precio->updated_at }}</td>
                                                            <td>$ {{ number_format($precio->pivot->precio, 0, ',', '.') }}</td>
                                                            <td style="width: 10%;">
                                                                <button title="Editar precio"
                                                                    class="btn btn-outline-warning btn-sm px-1 py-0 float-end">
                                                                    <i class="mdi mdi-pencil"></i>
                                                                </button>
                                                                <button title="Eliminar precio"
                                                                    class="btn btn-outline-danger btn-sm px-1 py-0 float-end">
                                                                    <i class="mdi mdi-delete"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                @endforeach
                                                <tr id="rowDetalle">
                                                    <td>
                                                        <div class="input-group">
                                                            <select id="listaProveedorTxt" class="form-control form-control-sm">
                                                                <option>Seleccione Proveedor</option>
                                                                @foreach ($proveedores as $proveedor)
                                                                    <option value="{{ $proveedor->id }}">
                                                                        {{ $proveedor->razon_social }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm"
                                                            value="{{ date('d/m/Y h:i') }}" disabled />
                                                    </td>
                                                    <td>
                                                        <input id="precioCompraTxt" type="text"
                                                            class="form-control form-control-sm" value="0"
                                                            placeholder="Precio de Venta" />
                                                    </td>
                                                    <td style="width:10%;">
                                                        <button title="Guardar precio"
                                                            class="btn btn-outline-primary btn-sm px-1 py-0 float-end"
                                                            onclick="agregarPrecioCompra()">
                                                            <i class="mdi mdi-content-save"></i>
                                                        </button>
                                                    </td>
                                                </tr>
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

@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"
        integrity="sha512-efAcjYoYT0sXxQRtxGY37CKYmqsFVOIwMApaEbrxJr4RwqVVGw8o+Lfh/+59TU07+suZn1BWq4fDl5fdgyCNkw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush

@push('custom-scripts')
    <script>
        var index = 0;
        $(document).ready(function() {
            var hash = window.location.hash;
            if (hash != '') {
                var tabEl = document.querySelector(hash + '-tab');
                if (tabEl) {
                    (new bootstrap.Tab(tabEl)).show();
                }
            }
            $('#precioVentaTxt').inputmask('numeric', {
                min: 0,
                prefix: '$ ',
                radixPoint: ',',
                groupSeparator: '.',
                rightAlign: false
            });
            $('#precioCompraTxt').inputmask('numeric', {
                min: 0,
                prefix: '$ ',
                radixPoint: ',',
                groupSeparator: '.',
                rightAlign: false
            });


        });

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

        function agregarPrecioVenta() {
            // Verificamos que se haya seleccionado una lista de precios
            var listaId = $('#listaTxt :selected').val();
            if (listaId == '') {
                return;
            }

            // Se recopila toda la información del producto
            var producto = {
                'producto_id': {{ $producto->id }},
                'lista_precio_id': listaId,
                'lista_precio_nombre': $('#listaTxt :selected').text(),
                'precio': $('#precioVentaTxt').val()
            };
            // Verificamos que el producto contenga información valida
            if (producto.lista_precio_id == '' || producto.precio == '') {
                return;
            }

            var data = {
                lista_precio_id: producto.lista_precio_id,
                producto_id: producto.producto_id,
                precio: $('#precioVentaTxt').inputmask('unmaskedvalue'),
            };
            // Creamos peticion psot para agregar el precio de venta
            $.ajax({
                type: "POST",
                url: '/api/productos/listaprecio',
                data: data, // serializes the form's elements.
                success: function(data) {
                    if (data.success == true) {
                        Swal.fire({
                            title: "Precio guardado exitosamente",
                            text: "La información ingresada es correcta y fue procesada exitosamente.",
                            icon: "success"
                        }).then((result) => {
                            window.location.href = "/productos/{{ $producto->id }}#venta";
                        });
                    } else {
                        Swal.fire({
                            title: "Error al guardar el precio",
                            text: "La información ingresada no es correcta o ya existe en la base de datos.",
                            icon: "warning"
                        });
                    }

                }
            });
        }

        function agregarPrecioCompra() {
            // Verificamos que se haya seleccionado una lista de precios
            var listaId = $('#listaProveedorTxt :selected').val();
            if (listaId == '') {
                return;
            }

            // Se recopila toda la información del producto
            var producto = {
                'producto_id': {{ $producto->id }},
                'proveedor_id': listaId,
                'proveedor_nombre': $('#listaTxt :selected').text(),
                'precio': $('#precioCompraTxt').val()
            };
            // Verificamos que el producto contenga información valida
            if (producto.proveedor_id == '' || producto.precio == '') {
                return;
            }

            var data = {
                proveedor_id: producto.proveedor_id,
                producto_id: producto.producto_id,
                precio: $('#precioCompraTxt').inputmask('unmaskedvalue'),
            };
            // Creamos peticion psot para agregar el precio de venta
            $.ajax({
                type: "POST",
                url: '/api/productos/precioproveedor',
                data: data, // serializes the form's elements.
                success: function(data) {
                    if (data.success == true) {
                        Swal.fire({
                            title: "Precio guardado exitosamente",
                            text: "La información ingresada es correcta y fue procesada exitosamente.",
                            icon: "success"
                        }).then((result) => {
                            window.location.href = "/productos/{{ $producto->id }}#compra";
                        });
                    } else {
                        Swal.fire({
                            title: "Error al guardar el precio",
                            text: "La información ingresada no es correcta o ya existe en la base de datos.",
                            icon: "warning"
                        });
                    }

                }
            });
        }
    </script>
@endpush
