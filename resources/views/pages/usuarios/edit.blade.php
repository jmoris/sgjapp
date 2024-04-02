@extends('layout.master')

@section('title', 'Gestión de Usuarios')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Gestión de Usuarios <small style="font-size:.75em;color: grey;">#{{ str_pad($user->id, 5, '0', STR_PAD_LEFT); }}</small></h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h4 class="card-title mb-0">FORMULARIO DE EDICIÓN DE USUARIO</h4>
                            </div>
                            <div class="row mx-2">
                                <div style="width:100%; margin-top:24px;"></div>
                                <div class="col-md-12">
                                    <form class="form" id="storeForm">
                                        @csrf
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Nombre(s)</label>
                                                    <input type="text" name="name" id="name" class="form-control"
                                                        placeholder="Ingrese el nombre o los nombres"
                                                        value="{{ $user->name }}">
                                                </div>
                                            </div><!-- Col -->
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Apellido(s)</label>
                                                    <input type="text" name="lastname" id="lastname"
                                                        class="form-control"
                                                        placeholder="Ingrese el apellido o los apellidos"
                                                        value="{{ $user->lastname }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" name="email" id="email" class="form-control"
                                                        placeholder="Ingrese su correo electrónico"
                                                        value="{{ $user->email }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Password</label>
                                                    <input type="password" name="password" id="password"
                                                        class="form-control" autocomplete="off"
                                                        placeholder="Ingrese su contraseña">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Cargo</label>
                                                    <input type="text" name="cargo" id="cargo" class="form-control"
                                                        placeholder="Ingrese el cargo del usuario"
                                                        value="{{ $user->cargo }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Rol</label>
                                                    <select class="form-control" name="role" id="role">
                                                        <option>Seleccione el Rol</option>
                                                        @foreach($roles as $rol)
                                                        <option value="{{$rol->id}}" @if($rol->id == $user->role_id) selected @endif>{{ $rol->nombre }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="float-end">
                                            <button type="submit" class="btn btn-primary submit"><i
                                                    class="mdi mdi-content-save"></i> Guardar</button>
                                        </div>
                                        <button type="button" class="btn btn-danger" onclick="location.href = '/usuarios'">
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
                    url: '/api/usuarios/editar/{{ $user->id }}',
                    data: $(form).serialize(), // serializes the form's elements.
                    success: function(data) {
                        Swal.fire({
                            title: "Usuario actualizado exitosamente",
                            text: "La información ingresada es correcta y fue procesada exitosamente.",
                            icon: "success"
                        }).then((result) => {
                            location.href = '/usuarios';
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
