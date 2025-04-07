<nav class="navbar">
  <a href="#" class="sidebar-toggler">
    <i data-feather="menu"></i>
  </a>
  <div class="navbar-content">
    <ul class="navbar-nav">
        <li class="nav-item">
            <b>{{ \App\Helpers\Ajustes::getEmisor()['razon_social'] }}</b>
        </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="appsDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i data-feather="grid"></i>
        </a>
        <div class="dropdown-menu p-2" aria-labelledby="appsDropdown">
          <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
            <p class="mb-0 fw-bold">ACCESO RÁPIDO</p>
          </div>
          <div class="row g-0 p-1">
            <div class="col-4 text-center">
              <a href="/clientes/nuevo" class="dropdown-item d-flex flex-column align-items-center justify-content-center wd-100 ht-85"><i class="mdi mdi-account-multiple-plus mdi-24px"></i><p class="tx-12">Nuevo Cliente</p></a>
            </div>
            <div class="col-4 text-center">
              <a href="/ventas/proyectos/nuevo" class="dropdown-item d-flex flex-column align-items-center justify-content-center wd-100 ht-85"><i class="mdi mdi-folder-multiple-plus mdi-24px"></i><p class="tx-12">Nuevo Proyecto</p></a>
            </div>
            <div class="col-4 text-center">
                <a href="/ventas/facturas/nuevo" class="dropdown-item d-flex flex-column align-items-center justify-content-center wd-100 ht-85"><i class="mdi mdi-truck-plus-outline mdi-24px"></i><p class="tx-12">Nueva Factura</p></a>
              </div>
          </div>

        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i data-feather="bell"></i>
          <div id="indicator" class="indicator">
            ...
          </div>
        </a>
        <div class="dropdown-menu p-0" aria-labelledby="notificationDropdown">
          <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
            <p>Notificaciones</p>
            <a href="javascript:;" onclick="marcarNotificacionesLeidas()" class="text-muted">Limpiar</a>
          </div>
          <div class="p-1" id="listaNotificaciones">

          </div>
          <div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
            <a href="javascript:;">Ver todas</a>
          </div>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <img class="wd-30 ht-30 rounded-circle" src="{{ url('https://freesvg.org/img/abstract-user-flat-4.png') }}" alt="profile">
        </a>
        <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
          <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
            <div class="mb-3">
              <img style="background-color: gainsboro" class="wd-80 ht-80 rounded-circle" src="{{ url('https://freesvg.org/img/abstract-user-flat-4.png') }}" alt="">
            </div>
            @php
            $currentUser = Auth::user();
            @endphp
            <div class="text-center">
              <p class="tx-16 fw-bolder">{{ $currentUser->name.' '.$currentUser->lastname }}</p>
              <p class="tx-12 text-muted">{{ $currentUser->email }}</p>
            </div>
          </div>
          <ul class="list-unstyled p-1">
            <li class="dropdown-item py-2">
              <a href="javascript:;" class="text-body ms-0">
                <i class="me-2 icon-md" data-feather="edit"></i>
                <span>Modificar Información</span>
              </a>
            </li>
            <li class="dropdown-item py-2" onclick="location.href='/auth/logout'">
              <div class="text-body ms-0">
                <i class="me-2 icon-md" data-feather="log-out"></i>
                <span>Cerrar Sesión</span>
              </div>
            </li>
          </ul>
        </div>
      </li>
    </ul>
  </div>
</nav>
