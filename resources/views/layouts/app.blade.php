<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    {{-- ======= Google Analytics 4 (propiedad limpia) ======= --}}
    @php
        // Toma el ID desde config/services.php o .env; si no hay, usa el ID nuevo limpio.
        // En .env podés definir GA_MEASUREMENT_ID=G-BWVDVM9X48
        $gaId = config('services.ga4.id') ?: env('GA_MEASUREMENT_ID') ?: 'G-BWVDVM9X48';
    @endphp
    @env('production')
        @if (!empty($gaId))
            <!-- Google tag (gtag.js) -->
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
            <script>
                (function () {
                    // Solo habilitar en tu dominio (evita contaminación si el código se copia a otro sitio)
                    var host = location.hostname.replace(/^www\./, '');
                    var allow = host === 'ferrindep.com.ar';
                    if (!allow) {
                        console.warn('GA4 desactivado para host:', location.hostname);
                        return;
                    }
                    window.dataLayer = window.dataLayer || [];
                    function gtag() { dataLayer.push(arguments); }
                    window.gtag = gtag;
                    gtag('js', new Date());
                    gtag('config', '{{ $gaId }}', {
                        send_page_view: true,
                        anonymize_ip: true
                    });
                })();
            </script>
        @endif
    @endenv
    {{-- ======= /Google Analytics 4 ======= --}}

    {{-- Kill DT UI (para que no tape el menú) --}}
    <style id="kill-dt-global">
        /* Oculta UI de DataTables v1 y v2 para que no tape el menú */
        .dataTables_wrapper,
        .dataTables_length,
        .dataTables_filter,
        .dataTables_info,
        .dataTables_paginate,
        .dt-container,
        .dt-length,
        .dt-search,
        .dt-info,
        .dt-paging {
            display: none !important;
        }

        /* Por si clonan la tabla con clases propias */
        table.dataTable,
        table.dt-table {
            /* no estilos visibles */
        }
    </style>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- Jodit (summernote reemplazo) -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jodit/3.4.25/jodit.min.css">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@300&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">

    @stack('head')
</head>

<body>
    <div id="app">
        <header class="navbar navbar-expand-md shadow-sm">
            <div class="container">
                <div class="collapse navbar-collapse">
                    <h2>@yield('title')</h2>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown3" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown3">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </header>

        <nav>
            <div style="background-color:black;display:flex;">
                <img src="{{ asset('img/home/logo.png') }}" style="width:100%;">
            </div>

            @guest
            @else
                <a href="{{ route('homes.index') }}"><i class="fas fa-home"></i> {{ __('Home') }}</a>

                <li class="nav-item dropdown" style="height:49px; position:relative; list-style:none;">
                    <a id="navbarDropdown2" class="nav-link dropdown-toggle" style="position:absolute; top:0;" href="#"
                        role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        <i class="fas fa-box"></i> Productos
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown2">
                        <a class="dropdown-item" href="{{ route('show_productos.index') }}"><i class="fas fa-box"></i>
                            {{ __('Productos') }}</a>
                        <a class="dropdown-item" href="{{ route('familias.index') }}"><i class="far fa-bookmark"></i>
                            {{ __('Familia') }}</a>
                        <a class="dropdown-item" href="{{ route('categorias.index') }}"><i class="far fa-bookmark"></i>
                            {{ __('Anchos') }}</a>
                        <a class="dropdown-item" href="{{ route('medidas.index') }}"><i class="far fa-bookmark"></i>
                            {{ __('Medidas') }}</a>
                        <a class="dropdown-item" href="{{ route('espesores.index') }}"><i class="far fa-bookmark"></i>
                            {{ __('Espesores') }}</a>
                    </div>
                </li>

                <a href="{{ route('videos.index') }}"><i class="fas fa-images"></i> {{ __('Videos') }}</a>
                <a href="{{ route('imagenes.index') }}"><i class="fas fa-images"></i> {{ __('Imágenes Sliders') }}</a>
                <a href="{{ route('redes.index') }}"><i class="fas fa-share-alt"></i> {{ __('Redes Sociales') }}</a>
                <a href="{{ route('usuarios.index') }}"><i class="fas fa-images"></i> {{ __('Clientes') }}</a>

                <li class="nav-item dropdown" style="height:49px; position:relative; list-style:none;">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" style="position:absolute; top:0;" href="#"
                        role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        <i class="fas fa-box"></i> Pedidos
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('pedidos.index') }}"><i class="fas fa-images"></i>
                            {{ __('Pedidos') }}</a>
                        <a class="dropdown-item" href="{{ route('configpedidos.index') }}"><i class="fas fa-images"></i>
                            {{ __('Configuración') }}</a>
                    </div>
                </li>

                <a href="{{ route('excel.index') }}"><i class="fas fa-cog"></i> {{ __('Excel precios') }}</a>

                <li class="nav-item dropdown" style="height:49px; position:relative; list-style:none;">
                    <a id="navbarDropdownEnv" class="nav-link dropdown-toggle" style="position:absolute; top:0;" href="#"
                        role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        <i class="fas fa-box"></i> Sistema de envíos
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownEnv">
                        <a class="dropdown-item" href="{{ route('destinos.index') }}"><i class="fas fa-images"></i>
                            {{ __('Destinos') }}</a>
                        <a class="dropdown-item" href="{{ route('zonas.index') }}"><i class="fas fa-images"></i>
                            {{ __('Zonas') }}</a>
                        <a class="dropdown-item" href="{{ route('destinozonas.index') }}"><i class="fas fa-images"></i>
                            {{ __('Destinos por zona') }}</a>
                        <a class="dropdown-item" href="{{ route('pesozonas.index') }}"><i class="fas fa-images"></i>
                            {{ __('Precios') }}</a>
                        <a class="dropdown-item" href="{{ route('shipping-prices.index') }}"><i
                                class="fas fa-truck-loading"></i>
                            {{ __('Precios Flex') }}</a>
                        <a class="dropdown-item" href="{{ route('configuracion_ubicacion.index') }}"><i
                                class="fas fa-map-marker-alt"></i> {{ __('Configurar Ubicaciones') }}</a>
                    </div>
                </li>

                <a href="{{ route('emails.index') }}"><i class="fas fa-share-alt"></i> {{ __('Emails subscriptos') }}</a>
                <a href="{{ route('metadatos.index') }}"><i class="fas fa-cog"></i> {{ __('Metadatos') }}</a>
                <a href="{{ route('configuraciones.index') }}"><i class="fas fa-cog"></i> {{ __('Configuración') }}</a>
            @endguest
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <!-- Scripts: jQuery (único), Bootstrap 5 bundle, Jodit -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jodit/3.4.25/jodit.min.js"></script>

    <script>
        document.querySelectorAll('.summernote').forEach(input => {
            try { new Jodit(input); } catch (e) { }
        });
    </script>

    {{-- Antídoto: en pedidos.index, si alguna librería activó DataTables sobre .table, lo desmonto para no tapar el
    menú --}}
    @if (request()->routeIs('pedidos.index'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (window.jQuery && jQuery.fn && jQuery.fn.DataTable) {
                    jQuery('.table').each(function () {
                        var $t = jQuery(this);
                        var isDt = false;
                        try { isDt = jQuery.fn.DataTable.isDataTable($t); } catch (e) { }
                        if (isDt || $t.hasClass('dataTable')) {
                            try { $t.DataTable().destroy(); } catch (e) { }
                            var $wrap = $t.closest('.dataTables_wrapper');
                            if ($wrap.length) {
                                $t.insertBefore($wrap);
                                $wrap.remove();
                            }
                        }
                    });
                }
            });
        </script>
    @endif

    @stack('scripts')
</body>

</html>