<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Responsive Laravel Admin Dashboard Template based on Bootstrap 5">
    <meta name="author" content="SolucionTotal">
    <meta name="keywords" content="web,gestion,soluciontotal,joremet">

    <title>SGJ App - @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!-- End fonts -->

    <!-- CSRF Token -->
    <meta name="_token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{ asset('/favicon.ico') }}">

    <!-- plugin css -->
    <link href="{{ asset('assets/plugins/@mdi/css/materialdesignicons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/fonts/feather-font/css/iconfont.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet" />
    <!-- end plugin css -->

    @stack('plugin-styles')
    <!--
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.6/css/dataTables.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css" />
    -->
    <link href="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-2.0.6/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/date-1.5.4/cr-2.0.1/datatables.min.css" rel="stylesheet">
    <!--<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.2.0/b-colvis-3.2.0/b-html5-3.2.0/b-print-3.2.0/cr-2.0.4/date-1.5.4/r-3.0.3/datatables.min.css" rel="stylesheet">-->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- common css -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
    <!-- end common css -->

    @stack('style')
</head>

<body data-base-url="{{ url('/') }}">

    <script src="{{ asset('assets/js/spinner.js') }}"></script>
    <div class="main-wrapper" id="app">
        @include('layout.sidebar')
        <div class="page-wrapper">
            <div id="toast-container" class="toast-container top-right show" aria-live="polite" aria-atomic="true">
            </div>
            @include('layout.header')
            <div class="page-content">
                @yield('content')
            </div>
            @include('layout.footer')
        </div>
    </div>

    <!-- base js -->
    <script src="{{ asset('js/app.js') }}"></script>
    <!--
    <script src="https://cdn.datatables.net/2.0.6/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-2.0.6/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/date-1.5.4/cr-2.0.1/datatables.min.js"></script>


   <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.2.0/b-colvis-3.2.0/b-html5-3.2.0/b-print-3.2.0/cr-2.0.4/date-1.5.4/r-3.0.3/datatables.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/jquery.rut@1.1.2/jquery.rut.min.js"></script>

    <script src="{{ asset('assets/plugins/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <!-- end base js -->

    <!-- plugin js -->
    @stack('plugin-scripts')
    <!-- end plugin js -->

    <!-- common js -->
    <script src="{{ asset('assets/js/template.js') }}"></script>
    <!-- end common js -->

    @stack('custom-scripts')

    <script>
        $(document).ready(function(){
            leerNotificaciones();
        });

        function leerNotificaciones(){
            $.ajax({
                type: "GET",
                url: "/api/notificaciones",
                success: function(data){
                    console.log(data);
                    $('#indicator').html(data.count);
                    renderNotificaciones(data.data);

                }
            });
        }

        function renderNotificaciones(notificaciones){
            $('#listaNotificaciones').html('');
            if(notificaciones.length > 0){
                var max = (notificaciones.length > 4) ? 4: notificaciones.length;
                for($i = 0; $i < max; $i++){
                    var noti = notificaciones[$i];
                    let notifTime = new Date(noti.created_at);
                    let now = new Date();
                    let dif = (now - notifTime);
                    dif = Math.round((dif / 1000) / 60);
                    let difstr = 'min';

                    console.log(dif);
                    calc = null;
                    if(dif >= 60){
                        calc = Math.round(dif/60);
                        difstr = (calc==1)?'hora':'horas';
                    }

                    if(dif >= 1440){
                        calc = Math.round((dif/1440));
                        difstr = (calc==1)?'dia':'dias';
                    }

                    var tipo_doc = noti.data['tipo_doc'];
                    var str_url = '';
                    if(tipo_doc == 33){
                        str_url = 'facturas';
                    }else if(tipo_doc == 61){
                        str_url = 'notascredito';
                    }else if(tipo_doc == 56){
                        str_url = 'notasdebito';
                    }
                    var url = `/compras/${str_url}/detalle/${noti.data['rut_emisor']}/${noti.data['folio']}`;

                    var html = `<a href="${url}" class="dropdown-item d-flex align-items-center py-2">
                        <div class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                        <i class="mdi mdi-file-document-arrow-right text-white mdi-18px"></i>
                        </div>
                        <div class="flex-grow-1 me-2">
                        <p>Se ha recibido un nuevo documento del<br>contribuyente ${noti.data['rut_emisor']} con folio ${noti.data['folio']}</p>
                        <p class="tx-12 text-muted">hace ${calc} ${difstr}</p>
                        </div>
                    </a>`;

                    $('#listaNotificaciones').append(html);
                }
            }else{
                var html = `<div class="dropdown-item d-flex align-items-center py-2">
                        <div class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                        </div>
                        <div class="flex-grow-1 me-2">
                        <p>No hay notificaciones que mostrar</p>
                        </div>
                    </div>`;

                    $('#listaNotificaciones').append(html);
            }
        }

        function marcarNotificacionesLeidas(){
            $.ajax({
                type: "POST",
                url: "/api/notificaciones/marcar",
                success: function(data){
                    leerNotificaciones();
                }
            });
        }
    </script>
</body>

</html>
