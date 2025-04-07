@extends('layout.master')

@section('title', 'Gestión de Productos')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Gestión de Productos</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h4 class="card-title mb-0">FORMULARIO DE NUEVO PRODUCTO</h4>
                            </div>
                            <div class="row mx-2">
                                <div style="width:100%; margin-top:24px;"></div>
                                <div class="col-md-12">
                                    <form class="form" method="post" id="storeForm">
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
                                                            placeholder="Ingrese el SKU o código interno del producto">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Nombre</label>
                                                        <input type="text" name="nombre" id="nombre" class="form-control"
                                                            placeholder="Ingrese el nombre del producto">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Descripción</label>
                                                        <textarea id="descripcion" name="descripcion" placeholder="Ingrese una descripción del producto" class="form-control" cols="40"></textarea>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Categoria</label>
                                                        <select class="form-control" id="categoria" name="categoria">
                                                            <option>Seleccionar categoria</option>
                                                            @foreach($categorias as $categoria)
                                                            <option value="{{$categoria->id}}">{{$categoria->nombre}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-4">
                                                        <label class="form-label">Unidad</label>
                                                        <select class="form-control" id="unidad" name="unidad">
                                                            <option>Seleccionar unidad</option>
                                                            @foreach($unidades as $unidad)
                                                            <option value="{{$unidad->id}}">{{$unidad->nombre}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-4 border-bottom">
                                                    <h5>Información del producto</h5>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Largo</label>
                                                    <input type="text" name="largo" id="largo" class="form-control"
                                                        placeholder="Ingrese el largo del producto">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Ancho</label>
                                                    <input type="text" name="ancho" id="ancho" class="form-control"
                                                        placeholder="Ingrese el ancho del producto">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Peso (Kg/Mt)</label>
                                                    <input type="text" name="peso" id="peso" class="form-control"
                                                        placeholder="Ingrese el peso del producto">
                                                </div>
                                                <div class="mb-4 border-bottom">
                                                    <h5>Información contable</h5>
                                                </div>
                                                <div class="mb-2">
                                                    <input class="form-check-input" type="checkbox" value="1" id="es_materiaprima" name="es_materiaprima">
                                                    <label class="form-check-label" for="es_materiaprima">
                                                        El producto es materia prima
                                                    </label>
                                                </div>
                                                <div class="mb-2">
                                                    <input class="form-check-input" type="checkbox" value="1" id="es_afecto" name="es_afecto" checked>
                                                    <label class="form-check-label" for="es_afecto">
                                                        El producto es afecto
                                                    </label>
                                                </div>
                                                <div class="mb-2">
                                                    <input class="form-check-input" type="checkbox" value="1" id="se_vende" name="se_vende" checked>
                                                    <label class="form-check-label" for="se_vende">
                                                        El producto se puede vender
                                                    </label>
                                                </div>
                                                <div class="mb-2">
                                                    <input class="form-check-input" type="checkbox" value="1" id="se_compra" name="se_compra" checked>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js" integrity="sha512-efAcjYoYT0sXxQRtxGY37CKYmqsFVOIwMApaEbrxJr4RwqVVGw8o+Lfh/+59TU07+suZn1BWq4fDl5fdgyCNkw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush

@push('custom-scripts')
    <script>

        $('#es_materiaprima').change(function(){
            var checked = $(this).is(':checked');
            if(checked){
                $('#se_compra').prop('checked', true);
                $('#es_afecto').prop('checked', false);
                $('#es_afecto').prop('disabled', true);
                $('#se_vende').prop('checked', false);
                $('#se_vende').prop('disabled', true);
            }else{
                $('#se_compra').prop('checked', true);
                $('#es_afecto').prop('checked',true);
                $('#es_afecto').prop('disabled', false);
                $('#se_vende').prop('checked', true);
                $('#se_vende').prop('disabled', false);
            }
        });

        $("#storeForm").validate({
            rules: {
                sku: {
                    required: true,
                },
                nombre: {
                    required: true,
                },
                descripcion: {
                    required: true,
                },
                categoria: {
                    required: true,
                },
                unidad: {
                    required: true,
                },
                es_materiaprima: {
                    required: true,
                },
                es_afecto: {
                    required: true,
                },
                se_vende: {
                    required: true,
                },
                se_compra: {
                    required: true,
                }
            },
            messages: {
                sku: "El campo SKU es obligatorio",
                nombre: "El campo nombre(s) es obligatorio",
                descripcion: "El campo apellidos(s) es obligatorio",
                categoria: "El campo contraseña es obligatorio",
                unidad: "El campo unidad es obligatorio",
                es_afecto: "El selector de exencion es obligatorio",
                se_vende: "El selector de venta es obligatorio",
                se_compra: "El selector de compra es obligatorio",
            },
            submitHandler: function(form) {
                var data = {
                    sku: $('#sku').val(),
                    nombre: $('#nombre').val(),
                    descripcion: $('#descripcion').val(),
                    categoria: $('#categoria').val(),
                    unidad: $('#unidad').val(),
                    largo: $('#largo').val(),
                    ancho: $('#ancho').val(),
                    peso: $('#peso').val(),
                    es_materiaprima: ($('#es_materiaprima').is(':checked'))?1:0,
                    es_afecto: ($('#es_afecto').is(':checked'))?1:0,
                    se_vende: ($('#se_vende').is(':checked'))?1:0,
                    se_compra: ($('#se_compra').is(':checked'))?1:0,
                };
                $.ajax({
                    type: "POST",
                    url: '/api/productos',
                    data: data, // serializes the form's elements.
                    success: function(data){
                        Swal.fire({
                            title: "Producto guardado exitosamente",
                            text: "La información ingresada es correcta y fue procesada exitosamente.",
                            icon: "success"
                        }).then((result) => {
                            location.href = '/productos';
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
