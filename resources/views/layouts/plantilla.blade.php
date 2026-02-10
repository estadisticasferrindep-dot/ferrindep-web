<!-- CACHE BUST 2.1 -->
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title')</title>

  <meta name="description" content="{{ $description }}">
  <meta name="keywords" content="{{ $keywords }}">
  <meta name="public-path" content="{{ asset('/') }}">

  <base href="{{ url('/') }}/">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

  <!-- jQuery (para Slick) -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Fuentes para el checkout -->
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@600;700;800&display=swap"
    rel="stylesheet">

  <!-- Google Maps (Global Safe Load) -->
  <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDssmltsmUd-dlEzYjO5VZG72km7ZKtbdU&libraries=places&loading=async"
    async defer></script>

  <!-- CSS propio -->
  <link href="{{ asset('css/style.css?v=3.4') }}" rel="stylesheet">
  <link href="{{ asset('css/grid.css?v=3.4') }}" rel="stylesheet">
  <link href="{{ file_exists(public_path('mix-manifest.json')) ? mix('css/app.css') : asset('css/app.css?v=3.4') }}"
    rel="stylesheet">

  <!-- Slick CSS -->
  <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />

  <!-- Jodit CSS -->
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jodit/3.4.25/jodit.min.css">

  <style>
    /* GPS hover effect (moved from body to head for Vue compatibility) */
    #gps-container-desktop:hover #gps-edit-btn {
      text-decoration: underline;
    }

    .page-home section.section-home-categorias .card-buscable.home-hidden {
      display: none !important;
    }

    .page-home section.section-home-categorias .card-buscable.home-dim {
      opacity: .35;
      filter: grayscale(100%);
    }

    .page-home section.section-home-categorias .card-buscable {
      transition: opacity .2s ease;
    }

    .oferta,
    .sin_stock,
    [class*="ribbon"] {
      display: none !important;
      visibility: hidden !important;
    }

    /* === OCULTAR “Mi cuenta” EN MOBILE === */
    /* ELIMINADO PARA MOSTRAR MIS PEDIDOS EN MOBILE
    @media (max-width: 767.98px) {
      .mobile-account {
        display: none !important;
      }
    }
    */
    /* === OCULTAR “Mi cuenta” EN MOBILE === */

    /* FIX GOOGLE AUTOCOMPLETE Z-INDEX */
    .pac-container {
      z-index: 10000 !important;
    }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var v = document.getElementById('logo-video');
      if (v) {
        // Función para reproducir
        function playLogo() {
          v.play().catch(function (e) { /* Silenced */ });
        }

        // Al terminar, esperar 10 seg y volver a reproducir
        v.addEventListener('ended', function () {
          v.currentTime = 0; // Volver al inicio (frame estático)
          // v.pause(); // Ya se pausa solo al terminar
          setTimeout(playLogo, 10000);
        });

        // Iniciar la primera vez (opcional: esperar un poco o inmediato)
        playLogo();
      }
   });
  </script>

  <script src="https://www.google.com/recaptcha/api.js" async defer></script>

  <script>
    function openProductModal(url, b) {
      setTimeout(() => {
        var el = document.getElementById('iframe-producto' + b);
        if (el) el.src = url;
      }, 300);
    }
  </script>

  <script async src="https://www.googletagmanager.com/gtag/js?id=G-BWVDVM9X48"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'G-BWVDVM9X48');
  </script>

  <style>
    @media (max-width: 520px) {
      .mobile-cart-fix {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        min-height: 58px !important;
        height: auto !important;
        padding: 0 !important;
        margin-top: 0 !important;
      }

      .mobile-cart-fix .carrito {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        height: auto !important;
        min-height: 58px !important;
        position: relative !important;
        padding: 4px !important;
        margin: 0 !important;
        gap: 2px !important;
      }

      .mobile-cart-fix i,
      .mobile-cart-fix svg,
      .mobile-cart-fix .carrito i {
        display: block !important;
        line-height: 1 !important;
        font-size: 20px !important;
        height: auto !important;
        margin-bottom: 2px !important;
      }

      .mobile-cart-fix span,
      .mobile-cart-fix small,
      .mobile-cart-fix .label,
      .mobile-cart-fix .txt,
      .mobile-cart-fix .carrito div.d-md-none {
        display: block !important;
        line-height: 1.1 !important;
        font-size: 10px !important;
        white-space: normal !important;
        color: white !important;
        text-align: center !important;
      }

      /* Badge Fix */
      .mobile-cart-fix .carrito span:first-of-type {
        position: absolute !important;
        top: 2px !important;
        right: 50% !important;
        margin-right: -12px !important;
        left: auto !important;
        z-index: 10 !important;
      }
    }
  </style>
</head>

<body class="{{ (isset($breadcrumb[0]['title']) && $breadcrumb[0]['title'] === 'home') ? 'page-home' : '' }}">

  @php
    $isCarrito = request()->is('carrito');
    $isFinal = request()->is('fin') || request()->is('finalizar-compra');
    $isCheckout = $isCarrito || $isFinal;
  @endphp

  @if($isCheckout)
    <style>
      /* Elevar CTA para evitar overlays que lo tapen */
      #btnSubmitPedido,
      button[form="formFinal"] {
        position: fixed !important;
        bottom: 24px !important;
        right: 24px !important;
        z-index: 2147483647 !important;
        cursor: pointer !important;
      }

      /* Tipografías del checkout */
      .section-carrito h1,
      .section-carrito h2,
      .section-carrito h3 {
        font-family: "Montserrat", "Open Sans", Arial, sans-serif !important;
        font-weight: 700 !important;
        letter-spacing: .2px;
      }

      .section-carrito,
      .section-carrito *:not(h1):not(h2):not(h3) {
        font-family: "Inter", "Open Sans", Arial, sans-serif !important;
      }

      .section-carrito input,
      .section-carrito select,
      .section-carrito textarea,
      .section-carrito button {
        font-family: inherit !important;
      }
    </style>
  @endif

  <style>
    @media (max-width: 768px) {

      /* Reduce video logo height on mobile */
      #logo-video {
        height: 60px !important;
      }

      /* Reduce header padding/height if needed */
      header nav .container {
        min-height: auto !important;
        padding-top: 5px !important;
        padding-bottom: 5px !important;
      }
    }
  </style>

  @if(request()->routeIs('web.carrito'))
    <style>
      @media (max-width: 768px) {

        /* Hide right-side items (User icon, Cart icon) */
        header nav .nav-item {
          display: none !important;
        }

        /* Center the container content */
        header nav .container {
          justify-content: center !important;
        }

        /* Adjust logo size and container */
        #logo-video {
          height: 65px !important;
        }

        /* Slightly larger than standard mobile but centered */
        header nav .container>a {
          width: auto !important;
          flex: none !important;
          max-width: 100% !important;
        }
      }
    </style>
  @endif

  <div id="app">

    <!-- HEADER -->
    <header>
      <nav>
        <div class="container nav-modal" style="display:flex;justify-content:space-between;">

          <div class="col-4 col-md-3 d-flex flex-column justify-content-center align-items-start">
            <a href="{{ route('web.home') }}" style="display:block; overflow:hidden;">
              <video id="logo-video" muted playsinline
                style="height:111px; width: auto; max-width: 100%; object-fit: contain; mix-blend-mode: screen; filter: contrast(120%) brightness(90%);">
                <source src="{{ asset('videos/Vidhome.mp4') }}" type="video/mp4">
              </video>
            </a>
            @if(isset($ubicacionCliente) && $ubicacionCliente)
              <!-- DEBUG SESSION: ID: {{ session('gps_location')['destino_id'] ?? 'NULL' }} | CITY: {{ session('gps_location')['cityName'] ?? 'NULL' }} -->


              {{-- DESKTOP: Inline Container --}}
              <div class="d-none d-md-flex align-items-center" id="gps-container-desktop"
                style="font-size: 11px; color: #ffc107; margin-top: -15px; margin-bottom: 5px; white-space: nowrap; padding-left: 10px; z-index: 10001; position: relative;">

                {{-- View Mode --}}
                <div id="gps-view-mode" style="display: flex; align-items: center;">
                  <i class="fas fa-map-marker-alt gps-point" style="margin-right:4px;"></i>
                  <span id="gps-city-display" style="font-weight: 600;">
                    {{ $ubicacionCliente->cityName ?? '📍 Seleccionar ubicación' }}
                  </span>
                  @if(!empty($ubicacionCliente->regionName))
                    , <span id="gps-region-display" style="margin-right: 5px;">{{ $ubicacionCliente->regionName }}</span>
                  @else
                    <span style="margin-right: 5px;"></span>
                  @endif
                  <span id="gps-edit-btn" style="cursor: pointer; opacity: 1; font-size: 11px;">(modificar)</span>
                </div>

                {{-- Edit Mode (SHARED DESKTOP & MOBILE) --}}
                {{-- MOVED OUTSIDE to be visible on mobile via JS toggle --}}
              </div>
              {{-- END DESKTOP CONTAINER --}}

              {{-- SHARED EDIT CONTAINER (Hidden by default, toggled via JS) --}}
              <div id="gps-edit-mode" style="display: none; align-items: center; margin-left: 2px;">
                <input type="text" id="gps-inline-input" placeholder="Ej: Cañuelas..."
                  style="background: #fff; color: #333; border: 1px solid #ccc; border-radius: 3px; padding: 2px 5px; font-size: 11px; width: 150px;">
                <i class="fas fa-times" id="gps-cancel-btn" style="cursor: pointer; margin-left: 5px; color: #fff;"
                  title="Cancelar"></i>
              </div>

              {{-- MOBILE: Re-use inline logic triggers but keep simple layout --}}
              <div class="d-flex d-md-none align-items-center" id="btn-gps-mobile"
                style="font-size: 9px; color: #ffc107; margin-top: -5px; line-height: 1.1; padding-left: 2px;">

                {{-- View Mobile --}}
                <div id="gps-view-mobile" style="display: flex; align-items: center;">
                  <i class="fas fa-map-marker-alt" style="margin-right:2px;"></i>
                  <span id="gps-text-mobile" style="cursor: pointer;" onclick="toggleMobileEdit()">
                    {{ $ubicacionCliente->cityName ?? '📍 Seleccionar' }}
                  </span>
                  <span style="margin-left:4px; font-weight:bold; cursor:pointer;"
                    onclick="toggleMobileEdit()">(modificar)</span>
                </div>

                {{-- Edit Mobile Placeholder (Moves input here via JS or simple visibility toggle) --}}
                {{-- STRATEGY: We will re-use the SAME input but move it in DOM or just toggle a shared absolute one?
                BETTER: Just duplicate the input structure for mobile? No, ID conflict.
                BETTER: Share the container but make it responsive.

                WAIT: The "gps-edit-mode" above is inside "d-none d-md-flex".
                We need to MOVE valid structure OUT of that div or make a separate one.

                Let's use a JS function to toggle visibility of logic containers.
                --}}
              </div>
            @endif
          </div>


          {{-- CSS REMOVED FROM HERE --}}



          {{-- ORPHAN CSS REMOVED HERE --}}


          @if (auth()->guard('usuario')->check())
            {{-- En mobile: Mis Pedidos + Logout --}}
            <li class="nav-item mobile-account d-flex d-md-none align-items-center" style="margin-right: 15px;">
              {{-- Link Mis Pedidos --}}
              <div class="d-flex flex-column align-items-center me-3" style="margin-top: 2px;">
                <a class="nav-link sin-borde p-0" href="{{ route('web.mis_compras') }}"
                  style="color: white; font-size: 22px; line-height: 1; display: block; height: 22px;">
                  <i class="fas fa-clipboard-list"></i>
                </a>
                <a href="{{ route('web.mis_compras') }}"
                  style="font-size: 9px; line-height: 1.1; color: white; text-decoration: none; margin-top: 4px; display: block;">
                   Mi<br>Cuenta
                </a>
              </div>
              {{-- Link Logout --}}
              <div class="d-flex flex-column align-items-center" style="margin-top: 2px;">
                <a class="nav-link sin-borde p-0" href="{{ route('web.clientes.logout') }}"
                  style="color:white; font-size: 18px; line-height: 1; display: block; height: 22px; padding-top: 2px !important;">
                  <i class="fas fa-sign-out-alt"></i>
                </a>
                <a href="{{ route('web.clientes.logout') }}"
                  style="font-size: 9px; line-height: 1.1; color: white; text-decoration: none; margin-top: 4px; display: block;">
                  Salir
                </a>
              </div>
            </li>
          @else
            {{-- En mobile: Solo Mis Pedidos (Guest) --}}
            <li class="nav-item mobile-account d-flex d-md-none"
              style="flex-direction:column; align-items:center; justify-content:flex-start; margin-right: 10px; padding-top: 5px;">
              <a class="nav-link sin-borde p-0" href="{{ route('web.mis_compras') }}"
                style="color: white; font-size: 22px; line-height: 1; display: block; height: 22px;">
                <i class="fas fa-clipboard-list"></i>
              </a>
              <a href="{{ route('web.mis_compras') }}"
                style="font-size: 9px; line-height: 1.1; color: white; text-decoration: none; text-align: center; margin-top: 4px; display: block;">
                Mi<br>Cuenta
              </a>
            </li>
          @endif

          <li class="nav-item d-flex d-md-none mobile-cart-fix"
            style="align-items:center; justify-content:flex-start !important; padding-top: 0px !important;">
            <cart ref="cart" href="{{ route('web.carrito') }}" icon="fas fa-file-invoice" style="display: block;" />
          </li>

          <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header"><button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button></div>
                <div class="modal-body">
                  <li class="nav-item {{ ($breadcrumb[0]['title'] ?? '') == 'home' ? 'nav-item-active' : '' }}"><a
                      class="nav-link" href="{{ route('web.home') }}">Inicio</a></li>
                  <li class="nav-item {{ ($breadcrumb[0]['title'] ?? '') == 'productos' ? 'nav-item-active' : '' }}"><a
                      class="nav-link" href="{{ route('web.productos.productos2.mobile', $familia_1) }}">Productos</a>
                  </li>
                  <li class="nav-item {{ ($breadcrumb[0]['title'] ?? '') == 'videos' ? 'nav-item-active' : '' }}"><a
                      class="nav-link" href="{{ route('web.videos') }}">Videos</a></li>
                  <li class="nav-item"><a class="nav-link" href="{{ route('web.mis_compras') }}">Mi Cuenta</a></li>
                </div>
                <div class="modal-footer"></div>
              </div>
            </div>
          </div>

          <div style="display:flex;">
            <ul class="nav d-none d-md-flex">
              <li class="nav-item {{ ($breadcrumb[0]['title'] ?? '') == 'home' ? 'nav-item-active' : '' }}"
                style="width:89px;">
                <a class="nav-link" href="{{ route('web.home') }}">Inicio</a>
              </li>
              <li class="nav-item {{ ($breadcrumb[0]['title'] ?? '') == 'productos' ? 'nav-item-active' : '' }}"
                style="width:115px;"><a class="nav-link"
                  href="{{ route('web.productos.productos2', $familia_1) }}">Productos</a></li>
              <li class="nav-item {{ ($breadcrumb[0]['title'] ?? '') == 'videos' ? 'nav-item-active' : '' }}"
                style="width:94px;"><a class="nav-link" href="{{ route('web.videos') }}">Videos</a></li>

              @if (auth()->guard('usuario')->check())
                <li class="nav-itemr" style="color:white;padding-left:15px;display:flex;align-items:center;">Hola,
                  {{ auth()->guard('usuario')->user()->email }}
                </li>
                <li class="nav-item" style="display:flex;align-items:center;"><a class="nav-link sin-borde"
                    href="{{ route('web.clientes.logout') }}"
                    style="width:32px;padding-top:6px;font-size:13px;padding-left:15px;"><span>(Salir)</span></a></li>
              @else
                <li class="nav-item"><a class="nav-link sin-borde no-hover" style="margin-left:20px;"
                    href="{{ route('web.mis_compras') }}">MI CUENTA</a></li>
              @endif

              <li class="nav-item"
                style="margin-left:20px; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                <cart class="no-hover" ref="cart" href="{{ route('web.carrito') }}"
                  img="{{ asset('img/home/carrito.jpeg') }}" />
                <a href="{{ route('web.carrito') }}"
                  style="font-size: 10px; line-height: 1; margin-top: -5px; color: white; text-decoration: none;">ver
                  Orden de Compra</a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>
    <!-- /HEADER -->

    <div id="location-nudge-bar"
      style="display:none; background-color: #e9ecef; color: #333; font-size: 13px; text-align: center; padding: 8px; border-bottom: 1px solid #ddd;">
      <div class="container d-flex justify-content-center align-items-center">
        <i class="fas fa-map-marker-alt" style="color: #FD914D; margin-right: 8px;"></i>
        <span>
          Parece que estás en <b>Buenos Aires</b>.
          <a href="#" onclick="toggleMobileEdit(); return false;"
            style="color: #FD914D; font-weight: bold; text-decoration: underline; margin-left: 3px;">
            Ingresá tu localidad exacta
          </a> para ver costos de envío reales.
        </span>
        <i class="fas fa-times" onclick="closeNudge()" style="margin-left: 15px; cursor: pointer; color: #999;"></i>
      </div>
    </div>

    {{-- Nudge script moved outside #app to avoid Vue compilation errors --}}

    <main>@yield('content')</main>

    <!-- FOOTER -->
    <footer>
      @if ((($breadcrumb[0]['title'] ?? '') == 'home' || ($breadcrumb[0]['title'] ?? '') == 'contacto') && $configuracion->wsp_show)
        <div class="footer-top">
          <a href="https://api.whatsapp.com/send?phone={{ $configuracion->wsp }}" {{ $configuracion->wsp ? 'target=”_blank”' : '' }}>
            <div class="border-wsp"><i class="fab fa-whatsapp"></i></div>
          </a>
        </div>
      @endif

      <div class="footer-info d-none d-md-flex">
        @if(!$isCarrito)
          <div class="container footer-box" style="padding-top:0;padding-bottom:0;">
            <div class="row">
              <div class="col-3 d-none d-sm-none d-md-block izquierda" style="padding-top:74px;">
                <img src="{{ asset(Storage::url($home->logo_footer)) }}" style="width:35%;margin-bottom:15px;">
                <p>{{ $home->frase_footer }}</p>
                <div class="footer-redes" style="display:flex;">
                  @foreach ($redes as $red)
                    <a href="{{ $red->url }}" {{ $red->url ? 'target=”_blank”' : '' }}>{!! $red->icono !!}</a>
                  @endforeach
                </div>
              </div>

              <div class="col-9" style="padding-top:74px;">
                <div class="container" style="padding-right:0;">
                  <div class="row">
                    <div class="col-4 d-none d-sm-none d-md-block">
                      <h5>Secciones</h5>
                      <div class="row secciones">
                        <div class="col-6 d-none d-sm-none d-md-block">
                          <p><a href="{{ route('web.home') }}">Inicio</a></p>
                          <p><a href="{{ route('web.productos.productos2', $familia_1) }}">Productos</a></p>
                          <p>
                            <a href="{{ route('web.mis_compras') }}"
                              class="nav-link {{ request()->routeIs('web.mis_compras') ? 'active' : '' }}"
                              style="color: #fff; font-weight: 500;">
                              MI CUENTA
                            </a>
                          </p>
                        </div>
                        <div class="col-6 d-none d-sm-none d-md-block">
                          <p><a href="{{ route('web.videos') }}">Video</a></p>
                        </div>
                      </div>
                    </div>

                    <div class="col">
                      <h5>Suscribite al Newsletter</h5>
                      <form method="POST" action="{{ route('web.email') }}">
                        @csrf
                        <div class="input-box newsletter"
                          style="position:relative;margin-right:15px;justify-content:flex-end;">
                          <input type="text" placeholder="Ingresa tu email" name="email" id="nombreParaVincular">
                          <button type="submit" class="orangeBg"><i class="far fa-paper-plane"></i></button>
                        </div>
                      </form>
                    </div>

                    <div class="col" style="padding-left:0;padding-right:0;">
                      <h5>Contacto</h5>
                      <div class="item-footer"><i class="fas fa-map-marker-alt"
                          style="margin-top:4px;margin-right:5px;"></i>
                        <p style="line-height:19px;">{{ $configuracion->direccion }}</p>
                      </div>
                      <div class="item-footer"><i class="fas fa-phone-alt"></i>
                        <p>{!! $configuracion->tel !!}</p>
                      </div>
                      <div class="item-footer"><i class="fas fa-envelope"></i>
                        <p><a href="mailto:{{ $configuracion->email }}" target=”_blank”>{{ $configuracion->email }}</a>
                        </p>
                      </div>
                    </div>

                  </div>
                </div>
              </div>

            </div>
          </div>
        @endif
      </div>
    </footer>
    <!-- /FOOTER -->

  </div> <!-- /#app -->

  <!-- Nudge Script (outside #app for Vue compatibility) -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var cityText = '';
      var el1 = document.getElementById('gps-city-display');
      var el2 = document.getElementById('gps-text-mobile');
      if (el1) cityText = (el1.innerText || '').trim();
      if (!cityText && el2) cityText = (el2.innerText || '').trim();

      var generics = ['Buenos Aires', 'Capital Federal', 'Argentina', 'Seleccionar ubicación', 'Seleccionar'];
      var wasClosed = sessionStorage.getItem('nudge_closed');
      var userChose = localStorage.getItem('user_chose_location');

      if (!wasClosed && !userChose && cityText && generics.some(function(g) { return cityText.indexOf(g) !== -1; })) {
        var bar = document.getElementById('location-nudge-bar');
        if (bar) bar.style.display = 'block';
      }
    });

    function closeNudge() {
      var bar = document.getElementById('location-nudge-bar');
      if (bar) bar.style.display = 'none';
      sessionStorage.setItem('nudge_closed', 'true');
    }
  </script>

  <!-- SCRIPTS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>
  <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/jodit/3.4.25/jodit.min.js"></script>



  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var base = document.querySelector('meta[name="public-path"]');
      var PUBLIC = base ? base.content.replace(/\/+$/, '') + '/' : '/';

      function fixUrl(u) {
        if (!u) return u;
        if (/^(https?:)?\/\//i.test(u) || u.startsWith('/')) return u;
        if (u.startsWith('public/')) return '/storage/' + u.replace(/^public\//, '');
        if (u.startsWith('storage/')) return '/' + u;
        return u;
      }

      document.querySelectorAll('[src], [data-src], [data-lazy], [href]').forEach(function (el) {
        ['src', 'data-src', 'data-lazy', 'href'].forEach(function (attr) {
          if (el.hasAttribute(attr)) {
            var v = el.getAttribute(attr);
            var nv = fixUrl(v);
            if (nv && nv !== v) el.setAttribute(attr, nv);
          }
        });
      });

      document.querySelectorAll('img[srcset], source[srcset]').forEach(function (el) {
        var ss = el.getAttribute('srcset');
        if (!ss) return;
        var parts = ss.split(',').map(function (p) {
          var seg = p.trim().split(/\s+/);
          seg[0] = fixUrl(seg[0]);
          return seg.join(' ');
        });
        el.setAttribute('srcset', parts.join(', '));
      });

      document.querySelectorAll('[style*="background"]').forEach(function (el) {
        var bg = el.style.backgroundImage;
        if (!bg) return;
        var m = bg.match(/url\(["']?(.*?)["']?\)/i);
        if (!m) return;
        var fixed = fixUrl(m[1]);
        if (fixed && fixed !== m[1]) el.style.backgroundImage = 'url("' + fixed + '")';
      });
    });
  </script>

  @if($isCheckout)
    <script>
      (function () {
        // ==== Mata-overlays con "pausa" cuando se hace click en REALIZAR PEDIDO ====
        var ffHoldKillMsUntil = 0;

        function killOverlays() {
          if (Date.now() < ffHoldKillMsUntil) return; // pausa activa
          try {
            document.querySelectorAll(
              '.modal-backdrop, .modal[aria-modal="true"], .sweet-overlay, ' +
              '.swal2-container, .swal2-shown, .vld-overlay, [data-overlay]'
            ).forEach(function (el) {
              el.remove();
            });
            document.body.classList.remove('modal-open', 'swal2-shown');
            document.body.style.removeProperty('padding-right');
            document.body.style.removeProperty('overflow');
          } catch (e) { }
        }

        // Click global: si es el CTA, pausamos la limpieza 4s; si no, limpiamos normal
        window.addEventListener('click', function (e) {
          // Si el click es ADENTRO de una alerta/modal, NO matamos nada (dejamos que la alerta funcione)
          if (e.target.closest('.swal2-container, .sweet-overlay, .modal-content, .modal-dialog')) {
            return;
          }

          var btn = e.target.closest('button, a, input[type="button"], input[type="submit"]');
          var label = (btn && ((btn.textContent || btn.value) || '').toLowerCase().trim()) || '';

          if (/realizar pedido/.test(label)) {
            ffHoldKillMsUntil = Date.now() + 4000; // 4 segundos de "no tocar overlays"
            setTimeout(killOverlays, 4200);
            return;
          }
          /* [FIX] Desactivamos la limpieza agresiva global porque rompe las alertas de validación */
          /* killOverlays(); */
        }, true);

        document.addEventListener('shown.bs.modal', killOverlays);
        document.addEventListener('show.bs.modal', killOverlays);
        document.addEventListener('hide.bs.modal', killOverlays);
        document.addEventListener('hidden.bs.modal', killOverlays);
      })();
    </script>
  @endif

  @if($isCheckout)
    <script>     /* ====== Parche de textos/labels sin recompilar Vue + heading ====== */     (function () {
        const desired = { dni: 'Ingrese CUIT o DNI', email: 'Correo electrónico *', direccion: 'Dirección de entrega *', cp: 'Código Postal *', nombreApe: 'Nombre y Apellido *', tel: 'Teléfono', entrecalles: 'Entre calles / piso / dpto', localidad: 'Localidad *', provincia: 'Provincia *', obs: 'Notas del pedido / referencias / aclaraciones respecto a la entrega', heading: 'Solicitamos los siguientes datos para dar curso al pedido' }; const norm = s => (s || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim();
        function patchPlaceholders(root) {
          root.querySelectorAll('input, textarea, label').forEach(function (el) {
            const isLabel = el.tagName === 'LABEL'; const ph = isLabel ? '' : (el.getAttribute('placeholder') || ''); const nm = (el.getAttribute('name') || ''); const tx = isLabel ? (el.textContent || '') : ph; const t = norm(tx); const nn = norm(nm);
            if (!isLabel) { if (/(dni|cuit)/.test(t) || /(dni|cuit)/.test(nn)) el.setAttribute('placeholder', desired.dni); else if (/(email|correo)/.test(t) || el.type === 'email') el.setAttribute('placeholder', desired.email); else if (/^direccion\b/.test(t) || /(domicilio|direccion)/.test(nn)) el.setAttribute('placeholder', desired.direccion); else if (/(codigo\s*postal|^cp\b)/.test(t) || /(cp|postal)/.test(nn)) el.setAttribute('placeholder', desired.cp); else if (/(telefono|celular)/.test(t) || el.type === 'tel') el.setAttribute('placeholder', desired.tel); else if (/\bentre\b/.test(t) && /calle/.test(t)) el.setAttribute('placeholder', desired.entrecalles); else if (/\blocalidad\b/.test(t) || /localidad/.test(nn)) el.setAttribute('placeholder', desired.localidad); else if (/\bprovincia\b/.test(t) || /provincia/.test(nn)) el.setAttribute('placeholder', desired.provincia); else if (/(nota|observa)/.test(t) || /(observacion|nota)/.test(nn)) el.setAttribute('placeholder', desired.obs); if (/nombre/.test(t) && /apellid/.test(t)) el.setAttribute('placeholder', desired.nombreApe); } else { if (/(dni|cuit)/.test(t)) el.textContent = 'Documento (DNI o CUIT)'; else if (/(email|correo)/.test(t)) el.textContent = 'Correo electrónico'; else if (/^direccion\b/.test(t)) el.textContent = 'Dirección de entrega'; else if (/(codigo\s*postal|^cp\b)/.test(t)) el.textContent = 'Código Postal'; }
          });
        }
        function patchHeading(root) { root.querySelectorAll('.section-carrito h1, .section-carrito h2, .section-carrito h3').forEach(function (h) { const txt = norm(h.textContent); if (txt.includes('orden de compra')) return; if (txt.includes('completar los datos') || txt.includes('datos para envio') || txt.includes('datos para envío') || txt.includes('finalizar compra')) { h.textContent = desired.heading; } }); }
        function patchAll() { const root = document; patchPlaceholders(root); patchHeading(root); } document.addEventListener('DOMContentLoaded', patchAll); let tries = 0, iv = setInterval(function () { patchAll(); if (++tries > 40) clearInterval(iv); }, 250);       // [FIX] Observer disabled to prevent infinite loop       // if ('MutationObserver' in window) new MutationObserver(patchAll).observe(document.body, { childList: true, subtree: true });     })();
    </script>

    <style>
      /* Normalizador tipografía + separadores “Productos” */
      .ff-prodarea,
      .ff-prodarea * {
        font-size: 14px !important;
        color: #222 !important;
        font-weight: 400 !important;
        text-decoration: none !important;
        font-family: "Inter", "Open Sans", Arial, sans-serif !important;
      }

      .ff-prodarea [style*="color"] {
        color: #222 !important;
      }

      .ff-item-start {
        border-top: 1px solid #e5e7eb !important;
        margin-top: 8px !important;
        padding-top: 8px !important;
      }

      .ff-item-start.ff-first {
        border-top: none !important;
        margin-top: 0 !important;
        padding-top: 0 !important;
      }
    </style>
    <script>     (function () {
                     function txt(el) { return (el && el.textContent || '').trim(); } function normalizeProductos() {
                       const root = document.querySelector('.section-carrito'); if (!root) return; const prodTitle = Array.from(root.querySelectorAll('*')).find(el => txt(el).toLowerCase() === 'productos:'); if (!prodTitle) return;
                       const stopRx = /^(env[ií]o|retiro|total)\b/i; let el = prodTitle.nextElementSibling, rows = [], guard = 0; while (el && guard++ < 200) { const t = txt(el); if (stopRx.test(t)) break; if (el.nodeType === 1) rows.push(el); el = el.nextElementSibling; } rows.forEach(n => n.classList.remove('ff-prodarea', 'ff-item-start', 'ff-first')); rows.forEach(n => n.classList.add('ff-prodarea'));
                       let first = false; rows.forEach(n => { const t = txt(n).toLowerCase(); const isHeader = /\b\d+\s*un\b.*-/.test(t) || /\$\s*[\d.]+(?:,\d+)?/.test(t); if (isHeader) { n.classList.add('ff-item-start'); if (!first) { n.classList.add('ff-first'); first = true; } } });
                     } document.addEventListener('DOMContentLoaded', normalizeProductos); let tries = 0, iv = setInterval(function () { normalizeProductos(); if (++tries > 80) clearInterval(iv); }, 250); const target = document.querySelector('.section-carrito') || document.body;       /* [FIX] Observer disabled to prevent infinite loop */       /* if (target && 'MutationObserver' in window) new MutationObserver(normalizeProductos).observe(target, { childList: true, subtree: true }); */
                   })();
    </script>

    <!-- === TACHITO (eliminar ítem) - Forzar visible o crear uno propio junto al precio === -->
    <style>
      .section-carrito .fa-trash,
      .section-carrito [class*="trash" i],
      .section-carrito [class*="remove" i],
      .section-carrito [class*="eliminar" i] {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
      }

      .ff-price-wrap {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        justify-content: flex-end;
        flex-wrap: nowrap
      }

      .ff-del {
        background: none;
        border: 0;
        cursor: pointer;
        opacity: .85;
        padding: 0 2px;
        font-size: 16px;
        line-height: 1
      }

      .ff-del:hover {
        opacity: 1;
        transform: scale(1.05)
      }
    </style>
    <script>     (function () {
                     function isHidden(el) { const cs = getComputedStyle(el); return el.offsetParent === null || cs.display === 'none' || cs.visibility === 'hidden' || +cs.opacity === 0; } const $all = (s, r = document) => Array.from(r.querySelectorAll(s));
                     function ensureTrash() {
                       const scope = document.querySelector('.section-carrito') || document; if (!scope) return;
                         /* Filas candidatas: las que tienen input de cantidad */         const qtyInputs = $all('input[type="number"], input[name*="cant"], input[name*="qty"], input[name*="cantidad"]', scope);
                       qtyInputs.forEach(inp => {
                         const row = inp.closest('.row, .producto, .product, .item, li, tr, .carrito, .cart, div') || inp.parentElement; if (!row || row.dataset.ffTrashOk) return;
                           /* 1) Localizamos el "eliminar" original (aunque esté oculto) */           let del = row.querySelector('.fa-trash, [class*="trash" i], [class*="remove" i], [class*="eliminar" i], a[href*="eliminar"], a[href*="remove"], button[onclick*="eliminar"], button[onclick*="remove"]');
                           /* 2) Localizamos el precio para anclar el botón */           let price = row.querySelector('[class*="precio" i], [class*="price" i]'); if (!price) { price = $all('*', row).find(n => /\$\s*\d[\d\.\,]*/.test((n.textContent || '').trim())); }
                           /* Si ya hay eliminar visible, marcamos y listo */           if (del && !isHidden(del)) { row.dataset.ffTrashOk = '1'; return; }
                           /* Si no hay ningún candidato, probamos otros botones del row con "eliminar/borrar" */           if (!del) { del = $all('button, a', row).find(b => /remove|eliminar|borrar/i.test(b.getAttribute('onclick') || '') || /eliminar|remove|borrar/i.test(b.textContent || '')); } if (!del) return; /* no tenemos a qué clickear */
                           /* 3) Creamos un botón visible que dispara el "del" original */           const btn = document.createElement('button'); btn.type = 'button'; btn.className = 'ff-del'; btn.innerHTML = (document.querySelector('.fa-trash') ? '<i class="fas fa-trash"></i>' : '🗑'); btn.addEventListener('click', e => { e.preventDefault(); e.stopPropagation(); del.click(); });
                           /* 4) Lo colocamos al lado del precio (o al final del row si no lo encontramos) */           if (price) { const holder = document.createElement('span'); holder.className = 'ff-price-wrap'; price.parentNode.insertBefore(holder, price); holder.appendChild(price); holder.appendChild(btn); } else { row.appendChild(btn); }
                         row.dataset.ffTrashOk = '1';
                       });
                     }
                     document.addEventListener('DOMContentLoaded', ensureTrash); let tries = 0, iv = setInterval(() => { ensureTrash(); if (++tries > 60) clearInterval(iv); }, 250);       /* [FIX] Observer disabled to prevent infinite loop */       /* if ('MutationObserver' in window) {          new MutationObserver(ensureTrash).observe(document.body, { childList: true, subtree: true });       } */
                   })();
    </script>
    <!-- === /TACHITO === -->

    <!-- ===== Checkout failsafe (v2): deja correr el submit original y usa plan B sólo si no pasa nada ===== -->
    <script>     (function () {
                     const N = s => (s || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim(); let sending = false;
                       /* Protegemos el carrito de borrados mientras enviamos */       try { const origRemove = localStorage.removeItem.bind(localStorage); localStorage.removeItem = function (k) { if (sending && k === 'cart') return; return origRemove(k); }; } catch (e) { }
                     function qAll(sel) { return Array.from(document.querySelectorAll(sel)); } function val(el) { return el ? (el.value || '').trim() : ''; }
                       /* Saca un resumen de texto del bloque "Productos:" para meter en Notas si usamos fallback */       function gatherProductSummary() {
                       const root = document.querySelector('.section-carrito'); if (!root) return ''; const title = Array.from(root.querySelectorAll('*')).find(el => (el.textContent || '').trim().toLowerCase() === 'productos:'); if (!title) return '';
                       const stop = /^(env[ií]o|retiro|total)\b/i; let el = title.nextElementSibling, lines = [], guard = 0; while (el && guard++ < 300) { const t = (el.textContent || '').replace(/\s+/g, ' ').trim(); if (t && stop.test(t.toLowerCase())) break; if (t) lines.push(t); el = el.nextElementSibling; } return lines.length ? 'Detalle (auto):\n' + lines.join('\n') : '';
                     }
                                                       /* Adjunta posibles claves de carrito del localStorage (además de stringCart) */       function appendCartParams(P) { try { const keys = Object.keys(localStorage); keys.forEach(k => { if (!/cart|carrito|items|productos|detalle/i.test(k)) return; const v = localStorage.getItem(k); if (v) P.append(k, v); }); const std = localStorage.getItem('cart'); if (std) P.append('stringCart', std); } catch (e) { } }
                     function collectPayload() {
                       const P = new URLSearchParams(); P.append('_token', '{{ csrf_token() }}');
                       const nombre = document.querySelector('[name*="usuario_nombre"], [placeholder*="ombre"][placeholder*="apell"]'); const dni = document.querySelector('[name*="usuario_dni"], [name*="cuit"], [placeholder*="cuit"], [placeholder*="dni"]'); const email = document.querySelector('[name*="usuario_email"], [type="email"], [placeholder*="correo"]'); const tel = document.querySelector('[name*="usuario_celular"], [type="tel"], [placeholder*="tel"]'); const dom = document.querySelector('[name*="usuario_domicilio"], [placeholder*="direc"]'); const loc = document.querySelector('[name*="usuario_localidad"], [placeholder*="localidad"]'); const prov = document.querySelector('[name*="usuario_provincia"], [placeholder*="provincia"]'); const cp = document.querySelector('[name*="usuario_cp"], [name*="postal"], [placeholder*="postal"]'); const obs = document.querySelector('[name*="observa"], textarea');
                       /* Campos base */         P.append('usuario_nombre', val(nombre)); P.append('usuario_dni', val(dni)); P.append('usuario_email', val(email)); P.append('usuario_celular', val(tel)); P.append('usuario_domicilio', val(dom)); P.append('usuario_localidad', val(loc)); P.append('usuario_provincia', val(prov)); P.append('usuario_cp', val(cp));
                       /* Notas / Mensaje (sumamos detalle auto si está vacío) */         let obsTxt = val(obs); const auto = gatherProductSummary(); if (auto) obsTxt = obsTxt ? (obsTxt + '\n\n' + auto) : auto; P.append('observacion', obsTxt);
                       const envio = document.querySelector('input[name="envio"]:checked'); const pago = document.querySelector('input[name="pago"]:checked'); if (envio) P.append('envio', envio.value || ''); if (pago) P.append('pago', pago.value || '');
                       const tHidden = document.querySelector('[name="total"]'); const eHidden = document.querySelector('[name="envio_calculado"]'); if (tHidden) P.append('total', tHidden.value || ''); if (eHidden) P.append('envio_calculado', eHidden.value || '');
                       appendCartParams(P); return P;
                     }
                     function submitAsForm(params) { if (sending) return; sending = true; const form = document.createElement('form'); form.method = 'POST'; form.action = '{{ route('web.envio_pedido') }}'; form.style.display = 'none'; for (const [k, v] of params.entries()) { const i = document.createElement('input'); i.type = 'hidden'; i.name = k; i.value = v; form.appendChild(i); } document.body.appendChild(form); form.submit(); }
                     function sendWithFetch() {
                       if (sending) return; sending = true; const params = collectPayload();
                       fetch('{{ route('web.envio_pedido') }}', { method: 'POST', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' }, body: params.toString() }).then(async r => { const ct = r.headers.get('content-type') || ''; if (ct.includes('application/json')) return r.json(); const t = await r.text(); try { return JSON.parse(t); } catch { return { status: 'raw', html: t }; } }).then(j => {
                         if (j && (j.redirect || j.url)) { window.location.href = j.redirect || j.url; } else {
                           submitAsForm(params); /* si la API devolvió HTML/JSON raro, caemos al form clásico */
                         }
                       }).catch(() => submitAsForm(params));
                     }
                     function isCTA(el) { if (!el) return false; const btn = el.closest('button, a, input[type="submit"], input[type="button"]'); if (!btn) return false; const t = N(btn.textContent || btn.value || ''); return t.includes('realizar') && t.includes('pedido'); }
                     /* >>> YA NO INTERCEPTAMOS submit del form: dejamos que corra el original */       /* Usamos fallback sólo si, tras hacer click, no ocurrió navegación */
                     function planBConTimeout(ms) {
                       const hrefBefore = location.href; setTimeout(function () {
                         if (location.href !== hrefBefore) return; /* el flujo original navegó OK */           sendWithFetch();                          /* si no pasó nada, usamos fallback */
                       }, ms);
                     }
                               /* Click en CTA */       document.addEventListener('click', function (ev) { if (!isCTA(ev.target)) return;         /* no prevenimos el default: damos tiempo al handler original */         planBConTimeout(1200); }, true);
                               /* Enter dentro del bloque checkout: intentamos submit nativo; si no navega, fallback */       document.addEventListener('keydown', function (ev) {
                       if (ev.key !== 'Enter') return; const scope = document.querySelector('.section-carrito'); if (!scope || !scope.contains(ev.target)) return;
                       const form = document.getElementById('formFinal') || scope.querySelector('form'); if (form && form.requestSubmit) form.requestSubmit(); else { const cta = qAll('button,a,input').find(isCTA); if (cta) cta.click(); } ev.preventDefault(); planBConTimeout(1200);
                     }, true);
                   })();
    </script>

  @endif

  @yield('scripts')
  <script src="{{ file_exists(public_path('mix-manifest.json')) ? mix('js/app.js') : asset('js/app.js') }}"></script>

  <script>
      document.addEventListener('DOMContentLoaded', function () {
        const editBtn = document.getElementById('gps-edit-btn');
        const cancelBtn = document.getElementById('gps-cancel-btn');
        const viewMode = document.getElementById('gps-view-mode');
        const editMode = document.getElementById('gps-edit-mode');
        const viewMobile = document.getElementById('gps-view-mobile'); // Mobile View
        const input = document.getElementById('gps-inline-input');

        // Global function for mobile trigger
        window.toggleMobileEdit = function () {
          if (editMode.style.display === 'none') {
            // Show Edit
            viewMode.style.display = 'none'; // Hide desktop view if visible
            if (viewMobile) viewMobile.style.display = 'none'; // Hide mobile view
            editMode.style.display = 'flex';
            setTimeout(() => input.focus(), 100);
          } else {
            // Hide Edit
            editMode.style.display = 'none';
            viewMode.style.display = 'flex'; // Restore desktop default (hidden via CSS on mobile)
            if (viewMobile) viewMobile.style.display = 'flex'; // Restore mobile
          }
        };

        if (editBtn) {
          editBtn.addEventListener('click', function () {
            viewMode.style.display = 'none';
            // Also hide mobile view just in case
            if (viewMobile) viewMobile.style.display = 'none';

            editMode.style.display = 'flex';
            setTimeout(() => input.focus(), 100);
          });
        }

        if (cancelBtn) {
          cancelBtn.addEventListener('click', function () {
            editMode.style.display = 'none';
            viewMode.style.display = 'flex';
            if (viewMobile) viewMobile.style.display = 'flex';
            input.value = '';
          });
        }

        if (cancelBtn) {
          cancelBtn.addEventListener('click', function () {
            editMode.style.display = 'none'; // Already handled above but kept for safety matching
            viewMode.style.display = 'flex';
            // if(viewMobile) viewMobile.style.display = 'flex'; // Handled in shared logic
            input.value = '';
          });
        }

        // Initialize Google Autocomplete
        function initAutocomplete() {
          if (!input || !window.google || !window.google.maps || !window.google.maps.places) return;

          const options = {
            types: ['geocode'],
            componentRestrictions: { country: 'ar' },
            fields: ['address_components', 'geometry']
          };

          const autocomplete = new google.maps.places.Autocomplete(input, options);
          autocomplete.addListener('place_changed', function () {
            const place = autocomplete.getPlace();
            if (!place.geometry) return;

            let city = '';
            let region = '';

            for (const component of place.address_components) {
              const types = component.types;
              if (types.includes('locality')) {
                city = component.long_name;
              } else if (types.includes('administrative_area_level_2') && !city) {
                city = component.long_name;
              }
              if (types.includes('administrative_area_level_1')) {
                region = component.long_name;
              }
            }
            if (!city && place.address_components.length > 0) {
              city = place.address_components[0].long_name;
            }

            // Capture Partido (Administrative Area Level 2)
            let partido = '';
            for (const component of place.address_components) {
              if (component.types.includes('administrative_area_level_2')) {
                partido = component.long_name;
                break;
              }
            }

            // Send to Backend (manual selection)
            updateLocation(city, region, partido, true);
          });
        }

        const checkGoogle = setInterval(() => {
          if (window.google && window.google.maps && window.google.maps.places) {
            initAutocomplete();
            initAutoGlobalGPS();
            clearInterval(checkGoogle);
          }
        }, 500);

        function initAutoGlobalGPS() {
          // Check if supported
          if (!navigator.geolocation) return;

          // Prevent infinite loops/reloads if already checked in this session
          if (sessionStorage.getItem('gps_auto_checked_v1')) return;

          console.log("📍 Requesting High Accuracy GPS...");

          navigator.geolocation.getCurrentPosition(
            (position) => {
              console.log("📍 GPS Access Granted");
              const lat = position.coords.latitude;
              const lng = position.coords.longitude;

              // Confirm we have a position, mark session as checked to avoid re-looping after reload
              sessionStorage.setItem('gps_auto_checked_v1', 'true');

              const geocoder = new google.maps.Geocoder();
              geocoder.geocode({ location: { lat, lng } }, (results, status) => {
                if (status === 'OK' && results[0]) {
                  let city = '';
                  let region = '';
                  let partido = '';

                  for (const component of results[0].address_components) {
                    const types = component.types;
                    if (types.includes('locality')) {
                      city = component.long_name;
                    } else if (types.includes('administrative_area_level_2') && !city) {
                      city = component.long_name;
                    }

                    if (types.includes('administrative_area_level_1')) region = component.long_name;
                    if (types.includes('administrative_area_level_2')) partido = component.long_name;
                  }

                  if (city) {
                    console.log("📍 GPS Resolved:", city, partido);
                    updateLocation(city, region, partido);
                  }
                } else {
                  console.warn("Geocoder failed:", status);
                }
              });
            },
            (error) => {
              console.warn("GPS Access Denied or Error:", error.message);
              // Mark as checked so we don't badger the user every page load
              sessionStorage.setItem('gps_auto_checked_v1', 'true');
            },
            {
              enableHighAccuracy: true, // <--- KEY REQUEST
              timeout: 10000,
              maximumAge: 0
            }
          );
        }

        function updateLocation(city, region, partido = '', isManual = false) {
          // Mark that user manually chose a location (hides the nudge bar)
          if (isManual) {
            localStorage.setItem('user_chose_location', 'true');
          }
          // Show loading state if desired, or just wait
          input.disabled = true;
          input.style.opacity = '0.5';

          fetch('{{ route("web.gps") }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ manual_city: city, manual_region: region, manual_partido: partido })
          })
            .then(r => r.json())
            .then(data => {
              // Force Reload to refresh PHP Controller logic (Safety First)
              console.log("📍 Location updated. Reloading to recalculate shipping...");
              window.location.reload();
            })
            .catch(err => {
              console.error(err);
              alert('Error al actualizar ubicación.');
              input.disabled = false;
              input.style.opacity = '1';
            });
        }
      });
  </script>
  <!-- Modal GPS Autocomplete -->
  <div id="location-picker-modal"
    style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:99999; justify-content:center; align-items:flex-start; padding-top:100px;">
    <div
      style="background:white; padding:20px; border-radius:8px; width:90%; max-width:500px; position:relative; box-shadow:0 4px 6px rgba(0,0,0,0.1);">
      <span id="gps-modal-close"
        style="position:absolute; top:10px; right:15px; cursor:pointer; font-size:20px; font-weight:bold; color:#666;">&times;</span>

      <h4 style="margin-top:0; color:#333; font-size:18px; margin-bottom:15px; font-family:'Open Sans', sans-serif;">
        <i class="fas fa-map-marker-alt" style="color:#FD914D; margin-right:8px;"></i>
        Seleccioná tu Ciudad
      </h4>

      <!-- Texto informativo eliminado por solicitud -->

      <div style="position:relative;">
        <input id="gps-autocomplete-input" type="text" placeholder="Ej: Cañuelas, San Isidro..."
          style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; font-size:16px;">
      </div>

      <div id="gps-modal-loading"
        style="display:none; margin-top:10px; text-align:center; color:#FD914D; font-weight:bold;">
        <i class="fas fa-spinner fa-spin"></i> Actualizando precios...
      </div>
    </div>
  </div>


</body>

</html>