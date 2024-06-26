@extends('layout.master2')

@section('title', 'Iniciar Sesión')

@section('content')
    <div style="background-image: url({{ asset('assets/images/welding.webp') }}); background-size: cover;"
        class="page-content d-flex align-items-center justify-content-center">

        <div class="row mx-0 auth-page">
            <div style="width: 400px;">
                <div class="card">
                    <div class="col-md-12 ps-md-0">
                        <div class="auth-form-wrapper px-4 pt-4 pb-2">
                            <div class="d-flex justify-content-center">
                                <img style="width:50%; height:auto;" src="/logo_joremet.png" />
                            </div>
                            <hr>
                            <h5 class="text-muted fw-normal mb-4">Complete el formulario para iniciar sesión</h5>
                            <form class="forms-sample" action="/login" method="post">
                                @csrf

                                <div class="mb-3">
                                    <label for="userEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" id="userEmail"
                                        placeholder="Ingrese su correo electrónico">
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="userPassword" class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password" id="userPassword"
                                        autocomplete="current-password" placeholder="Ingrese su contraseña">
                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" name="remember_me" value="false"
                                        id="authCheck">
                                    <label class="form-check-label" for="authCheck">
                                        Recordarme
                                    </label>
                                </div>
                                <!-- if there are login errors, show them here -->
                                <div>


                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary me-2 mb-2 mb-md-0">Acceder</button>
                                </div>
                                <hr>
                                <div class="text-center mt-3">
                                    <small class="d-block text-muted">Si tiene algun problema para iniciar sesión,
                                        comuniquese con soporte clickeando <a
                                            href="mailto:contacto@soluciontotal.cl">aquí</small>.
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
@endsection
