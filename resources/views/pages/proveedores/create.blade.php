@extends('layout.master')

@section('title', 'Gestión de Proveedores')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Gestión de Proveedores</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h4 class="card-title mb-0">FORMULARIO DE NUEVO PROVEEDOR</h4>
                            </div>
                            <div class="row mx-2">
                                <div style="width:100%; margin-top:24px;"></div>
                                <div class="col-md-12">
                                    <form class="form" id="storeForm">
                                        @csrf
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="mb-2 border-bottom">
                                                    <h5>Información comercial</h5>
                                                </div>
                                                <div class="mx-2">
                                                    <div class="mb-2">
                                                        <label class="form-label">R.U.T.</label>
                                                        <input type="text" name="rut" id="rut"
                                                            class="form-control"
                                                            maxlength="12"
                                                            placeholder="Ingrese el R.U.T del proveedor">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Razón social</label>
                                                        <input type="text" name="razon_social" id="razon_social"
                                                            class="form-control"
                                                            placeholder="Ingrese la razón social del proveedor">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Actividad Económica</label>
                                                        <input type="text" name="giro" id="giro"
                                                            class="form-control"
                                                            placeholder="Ingrese el giro del proveedor">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Dirección</label>
                                                        <input type="text" name="direccion" id="direccion"
                                                            class="form-control"
                                                            placeholder="Ingrese la dirección comercial del proveedor">
                                                    </div>
                                                    <div class="mb-4">
                                                        <label class="form-label">Comuna</label>
                                                        <select id="comuna" name="comuna"
                                                            class="js-example-basic-single form-select" data-width="100%">
                                                            <option></option>
                                                            @foreach ($comunas as $comuna)
                                                                <option value="{{ $comuna->id }}">{{ $comuna->nombre }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-2 border-bottom">
                                                    <h5>Información de contacto</h5>
                                                </div>
                                                <div class="mx-2">
                                                    <div class="mb-2">
                                                        <label class="form-label">Teléfono</label>
                                                        <input type="text" name="telefono" id="telefono"
                                                            class="form-control"
                                                            placeholder="Ingrese el telefono de contacto del proveedor">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Correo de contacto</label>
                                                        <input type="text" name="correo_contacto" id="correo_contacto"
                                                            class="form-control"
                                                            placeholder="Ingrese el correo de contacto del proveedor">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Página web</label>
                                                        <input type="text" name="web" id="web"
                                                            class="form-control"
                                                            placeholder="Ingrese la página web del proveedor">
                                                    </div>
                                                </div>
                                                <div class="mb-2 border-bottom">
                                                    <h5>Sincronización entre empresas</h5>
                                                </div>
                                                <div class="ms-3">
                                                    <div class="mb-2">
                                                        <input class="form-check-input" type="checkbox" value="1"
                                                            id="sincronizar" name="sincronizar" checked>
                                                        <label class="form-check-label" for="sincronizar">
                                                            Agregar información en todas las empresas
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="float-end">
                                            <button type="submit" class="btn btn-primary submit"><i
                                                    class="mdi mdi-content-save"></i> Guardar</button>
                                        </div>
                                        <button type="button" class="btn btn-danger"
                                            onclick="location.href = '/proveedores'">
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
    <div class="modal fade bd-example-modal-lg" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <img class="text-center mx-auto" src="/loading.gif" style="width: 48px; height: 48px;">
                <span id="statusTxt" class="text-center fw-bold">Obteniendo información desde el SII...</span>
            </div>
        </div>
    </div>
@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"
        integrity="sha512-efAcjYoYT0sXxQRtxGY37CKYmqsFVOIwMApaEbrxJr4RwqVVGw8o+Lfh/+59TU07+suZn1BWq4fDl5fdgyCNkw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

@endpush

@push('custom-scripts')
    <script>
        /*$('#rut').inputmask({
            mask: '99.999.999-[9|K]',
            definitions: {
                'K': {
                    validator: "(k|K)",
                    casing: "upper"
                }
            }
        });*/
        $("input#rut").rut({
            formatOn: 'keyup',
            minimumLength: 8, // validar largo mínimo; default: 2
            validateOn: 'change' // si no se quiere validar, pasar null
        });

        $('#rut').keypress(function(e){
            if(e.which == 13) {
                return false;
            }
        });

        $('#rut').change(function(e) {
            if($(this).val() == ''||$(this).val().length < 9)
                return false;
            $('.modal').modal('show');
            $('#razon_social').val("");
            $('#giro').val("");
            $('#direccion').val("");
            $("#comuna").val(null);
            $('#comuna').trigger('change');
            var rut = $(this).inputmask('unmaskedvalue');
            var dv = rut[rut.length - 1];
            //var rutCompleto = rut.slice(0, -1) + '-' + dv;

            var rutCompleto = $.formatRut(rut, false);
            console.log(rutCompleto);
            $.ajax({
                url: '/api/infodte/contribuyentes/' + rutCompleto,
                type: 'GET',
                tryCount: 0,
                retryLimit: 3,
                success: function(data) {
                    $('#razon_social').val(data.RAZONSOCIAL);
                    $('#giro').val(data.GIROS[0].DESCRIPCION);
                    $('#direccion').val(data.DIRECCION);
                    $("#comuna").val(data.COMUNA);
                    $('#comuna').trigger('change');
                    validator.resetForm();
                    $('.modal').modal('hide');
                },
                error: function(response, status, error) {
                    if (response.status == 500) {
                        this.tryCount++;
                        if (this.tryCount <= this.retryLimit) {
                            console.log('Peticion al SII fallida');
                            setTimeout(() => {
                                console.log('Reintentando peticion');
                                $.ajax(this);
                            }, 1000);
                            return;
                        } else {
                            console.log('No se pudo obtener la informacion');
                            $('.modal').modal('hide');
                        }
                    }
                }
            });
            return false;
        });

        $("#comuna").select2({
            placeholder: 'Seleccione una comuna'
        });
        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });
        var validator = $("#storeForm").validate({
            rules: {
                rut: {
                    required: true,
                },
                razon_social: {
                    required: true,
                },
                giro: {
                    required: true,
                },
                direccion: {
                    required: true,
                },
                comuna: {
                    required: true,
                }
            },
            messages: {
                rut: "El campo rut es obligatorio",
                razon_social: "El campo razón social es obligatorio",
                giro: "El campo actividad economica es obligatorio",
                direccion: "El campo direccion es obligatorio",
                comuna: "El campo comuna es obligatorio",
            },

            submitHandler: function(form) {
                var data = {
                    rut: $('#rut').inputmask('unmaskedvalue'),
                    razon_social: $('#razon_social').val(),
                    giro: $('#giro').val(),
                    direccion: $('#direccion').val(),
                    comuna: $('#comuna').val(),
                    telefono: $('#telefono').val(),
                    correcto_contacto: $('#correcto_contacto').val(),
                    web: $('#web').val(),
                    sincronizar: ($('#sincronizar').is(':checked') == true) ? 1 : 0,
                };
                $.ajax({
                    type: "POST",
                    url: '/api/proveedores',
                    data: data, // serializes the form's elements.
                    success: function(data) {
                        Swal.fire({
                            title: "Proveedor guardado exitosamente",
                            text: "La información ingresada es correcta y fue procesada exitosamente.",
                            icon: "success"
                        }).then((result) => {
                            location.href = '/proveedores';
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
